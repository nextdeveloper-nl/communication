<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Google_Client;
use Google_Service_Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Commons\Database\Models\ExternalServices;
use NextDeveloper\Commons\Helpers\GoogleWorkspaceHelper;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Exceptions\TokenRefreshedException;

/**
 * Gmail / Google Workspace channel implementation.
 *
 * communication_channels.credentials:
 *   access_token  (required) — OAuth2 access token (per-user)
 *   refresh_token (required) — OAuth2 refresh token, long-lived (per-user)
 *   expires_in    (optional) — token lifetime in seconds (set by Google)
 *   created       (optional) — Unix timestamp when token was issued (set by Google)
 *
 * communication_channels.configuration:
 *   external_service_uuid (required) — UUID of the common_external_services record
 *                                       that holds client_id and client_secret
 *   from_address          (optional) — sender address shown to recipients
 *   max_messages_per_hour (optional, default 500)
 *
 * common_external_services.configuration (referenced by external_service_uuid):
 *   client_id     (required) — OAuth2 app client ID
 *   client_secret (required) — OAuth2 app client secret
 *
 * Token lifecycle: the full credentials array (including expiry metadata) is passed
 * to Google_Client. When the access token is expired, it is refreshed automatically
 * and the new credentials are persisted back to the channel record.
 */
class Gmail implements ChannelAbstract, VerifiableChannel
{
    public const NAME = 'gmail';

    public const FIELDS = [
        'access_token'          => 'required',
        'refresh_token'         => 'required',
        'from_address'          => 'nullable',
        'max_messages_per_hour' => 'nullable',
    ];

    protected Google_Client $client;
    protected Google_Service_Gmail $service;
    protected string $fromAddress;

    /**
     * The google_workspace external service this channel is linked to (via
     * configuration.external_service_uuid), when there is a live one. null means the
     * channel sends with the token copy stored in its own credentials.
     */
    protected ?ExternalServices $linkedService = null;

    public function __construct(public readonly Channels $channel)
    {
        $credentials = $channel->credentials ?? [];
        if (is_string($credentials)) {
            $credentials = json_decode($credentials, true) ?? [];
        }

        $config = $channel->configuration ?? [];
        if (is_string($config)) {
            $config = json_decode($config, true) ?? [];
        }

        // The linked Google Workspace connection is the source of truth for the token. The copy
        // in the channel credentials is never kept up to date (leo:refresh-gmail-tokens only
        // refreshes the service), so once it died every send failed with invalid_grant while the
        // integration itself looked healthy. When the link exists, send with the service's token.
        $this->linkedService = self::resolveLinkedService($config);

        if ($this->linkedService) {
            $credentials = $this->credentialsFromLinkedService($this->linkedService, $credentials);
        }

        if (!$this->validateConfig($credentials)) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required Gmail credentials (access_token, refresh_token).');
        }

        // Resolve OAuth2 app credentials (client_id / client_secret).
        // Priority:
        //   1. Directly in channel.credentials
        //   2. Via channel.configuration['external_service_uuid'] → external service configuration
        $clientId     = $credentials['client_id'] ?? null;
        $clientSecret = $credentials['client_secret'] ?? null;

        if (!$clientId || !$clientSecret) {
            $externalServiceUuid = data_get($config, 'external_service_uuid');
            $externalService     = $externalServiceUuid
                ? ExternalServices::withoutGlobalScopes()->where('uuid', $externalServiceUuid)->first()
                : null;

            $clientId     = data_get($externalService?->configuration, 'client_id');
            $clientSecret = data_get($externalService?->configuration, 'client_secret');
        }

        if (!$clientId || !$clientSecret) {
            $clientId     = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
        }

        if (!$clientId || !$clientSecret) {
            throw new InvalidArgumentException(
                __METHOD__ . ': Cannot resolve client_id / client_secret. ' .
                'Store them in channel credentials, set external_service_uuid in channel configuration, ' .
                'or set services.google.client_id / client_secret in config.'
            );
        }

        $this->fromAddress = $config['from_address'] ?? 'me';

        try {
            $this->client = new Google_Client();
            $this->client->setClientId($clientId);
            $this->client->setClientSecret($clientSecret);

            // Pass the full credentials array so Google_Client can track expiry metadata
            $this->client->setAccessToken($credentials);

            // A linked service hands over a token that is valid for the next few minutes, so there
            // is nothing to refresh (and nothing to write back to the channel).
            if (!$this->linkedService && $this->client->isAccessTokenExpired()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($credentials['refresh_token']);

                if (isset($newToken['error'])) {
                    throw new Exception('Token refresh failed: ' . self::describeTokenError($newToken));
                }

                // Preserve refresh_token (Google omits it from refresh responses)
                $updatedCredentials = array_merge($credentials, $newToken);
                if (empty($updatedCredentials['refresh_token'])) {
                    $updatedCredentials['refresh_token'] = $credentials['refresh_token'];
                }

                $channel->update(['credentials' => $updatedCredentials]);
            }

            $this->service = new Google_Service_Gmail($this->client);
        } catch (Exception $e) {
            throw new Exception(__METHOD__ . ': Failed to initialize Gmail client: ' . $e->getMessage());
        }
    }

    public function send(mixed $message): void
    {
        try {
            $this->doSend($message);
        } catch (Google_Service_Exception $e) {
            // 401 means the access token was revoked or expired mid-session.
            // Refresh the token, persist it, then re-queue the message for the
            // next delivery cycle (the caller catches TokenRefreshedException).
            if ($e->getCode() === 401) {
                $this->refreshAndPersistToken();

                Log::warning(__METHOD__ . ': Access token expired during send — token refreshed, message will be re-queued', [
                    'to' => $message['to'] ?? null,
                ]);

                throw new TokenRefreshedException('Gmail access token was refreshed; message re-queued for retry.');
            }

            Log::error(__METHOD__ . ': Gmail delivery failed', [
                'to'    => $message['to'] ?? null,
                'error' => $e->getMessage(),
            ]);
            throw new Exception(__METHOD__ . ': Failed to send via Gmail: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Gmail delivery failed', [
                'to'    => $message['to'] ?? null,
                'error' => $e->getMessage(),
            ]);
            throw new Exception(__METHOD__ . ': Failed to send via Gmail: ' . $e->getMessage());
        }
    }

    /**
     * Mints a new access token from the stored refresh token. This is the real proof that the grant and the OAuth
     * client still work; it needs no extra scope (the channel may hold only the send scope).
     */
    public function verify(): string
    {
        $this->refreshAndPersistToken();

        return 'Google accepted the stored authorization.';
    }

    private function doSend(mixed $message): void
    {
        $to      = $message['to'] ?? null;
        $subject = $message['subject'] ?? '(no subject)';
        $body    = $message['message'] ?? $message['body'] ?? '';
        $cc      = array_filter((array) ($message['cc'] ?? []));

        $raw = $this->buildRaw($to, $subject, $body, $this->fromAddress, $cc);

        $gmailMessage = new Google_Service_Gmail_Message();
        $gmailMessage->setRaw(rtrim(strtr(base64_encode($raw), '+/', '-_'), '='));

        $this->service->users_messages->send('me', $gmailMessage);
    }

    /**
     * Turns Google's token-endpoint error into something actionable.
     *
     * Google puts the machine-readable cause in `error` (invalid_grant,
     * unauthorized_client, …) and a bare HTTP reason phrase in
     * `error_description` — literally "Bad Request" or "Unauthorized". Reporting
     * the description alone, as this used to, threw away the only part that says
     * what is actually wrong.
     *
     * @param  array<string, mixed>  $response
     */
    private static function describeTokenError(array $response): string
    {
        $code = (string) ($response['error'] ?? 'unknown_error');
        $description = trim((string) ($response['error_description'] ?? ''));

        $hint = match ($code) {
            'invalid_grant' => 'the refresh token is no longer valid: revoked, expired, the account '
                .'password changed, or it was issued by a different OAuth client. '
                .'Reconnect the account to mint a new one.',
            'unauthorized_client' => 'the refresh token was issued to a different OAuth client than the one '
                .'configured now (services.google.client_id). Reconnect the account, or restore the '
                .'client credentials that issued it.',
            'invalid_client' => 'the configured client_id / client_secret are wrong or belong to a deleted '
                .'OAuth client. Check services.google.',
            'invalid_scope' => 'the granted scopes no longer cover Gmail sending. Reconnect the account and '
                .'accept the mail scopes.',
            'invalid_request' => 'the refresh request was malformed — usually a missing or truncated refresh token.',
            default => null,
        };

        // The description only earns its place when it is not the HTTP reason phrase.
        $isReasonPhrase = in_array($description, ['Bad Request', 'Unauthorized', 'Forbidden', ''], true);

        return $code
            .($isReasonPhrase ? '' : ' ('.$description.')')
            .($hint ? ' — '.$hint : '');
    }

    /**
     * Returns the live google_workspace external service the channel points to, or null
     * when the channel has no link, the service is gone, is not a Google Workspace one,
     * or was flagged dead (is_alive = false) — then the channel's own credentials are used.
     */
    private static function resolveLinkedService(array $config): ?ExternalServices
    {
        $uuid = data_get($config, 'external_service_uuid');

        if (!$uuid) {
            return null;
        }

        return ExternalServices::withoutGlobalScopes()
            ->where('uuid', $uuid)
            ->where('code', 'google_workspace')
            ->where('is_alive', true)
            ->whereNotNull('refresh_token')
            ->first();
    }

    /**
     * Builds the Google_Client credentials array from the linked service: a freshly
     * refreshed access token plus the service's refresh token.
     */
    private function credentialsFromLinkedService(ExternalServices $service, array $channelCredentials): array
    {
        $accessToken = GoogleWorkspaceHelper::freshAccessToken($service);

        if (!$accessToken) {
            throw new Exception(
                __METHOD__ . ': Failed to initialize Gmail client: the linked Google Workspace connection '
                . 'could not be refreshed — reconnect the account (external service ' . $service->uuid . ').'
            );
        }

        try {
            $refreshToken = decrypt($service->refresh_token);
        } catch (\Throwable) {
            $refreshToken = $service->refresh_token;
        }

        return array_merge($channelCredentials, [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            // freshAccessToken() guarantees at least 5 minutes of validity
            'created'       => time(),
            'expires_in'    => 300,
        ]);
    }

    private function refreshAndPersistToken(): void
    {
        // Linked channel: refresh through the service so its stored token stays the single truth.
        if ($this->linkedService) {
            $token = GoogleWorkspaceHelper::refresh($this->linkedService->fresh())['token'] ?? null;

            if (!$token) {
                throw new Exception('Token refresh failed: the linked Google Workspace connection needs to be reconnected.');
            }

            $this->client->setAccessToken([
                'access_token' => GoogleWorkspaceHelper::freshAccessToken($this->linkedService->fresh()),
                'created'      => time(),
                'expires_in'   => 300,
            ]);
            $this->service = new Google_Service_Gmail($this->client);

            return;
        }

        $credentials = $this->channel->credentials ?? [];
        if (is_string($credentials)) {
            $credentials = json_decode($credentials, true) ?? [];
        }

        $newToken = $this->client->fetchAccessTokenWithRefreshToken($credentials['refresh_token']);

        if (isset($newToken['error'])) {
            throw new Exception('Token refresh failed: ' . self::describeTokenError($newToken));
        }

        $updatedCredentials = array_merge($credentials, $newToken);
        if (empty($updatedCredentials['refresh_token'])) {
            $updatedCredentials['refresh_token'] = $credentials['refresh_token'];
        }

        $this->channel->update(['credentials' => $updatedCredentials]);
        $this->client->setAccessToken($updatedCredentials);
        $this->service = new Google_Service_Gmail($this->client);
    }

    public function validateConfig(array $config): bool
    {
        foreach (['access_token', 'refresh_token'] as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return true;
    }

    private function buildRaw(string $to, string $subject, string $body, string $from, array $cc = []): string
    {
        $headers  = "From: {$from}\r\n";
        $headers .= "To: {$to}\r\n";

        if (!empty($cc)) {
            $headers .= 'Cc: ' . implode(', ', $cc) . "\r\n";
        }

        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $headers .= base64_encode($body);

        return $headers;
    }
}
