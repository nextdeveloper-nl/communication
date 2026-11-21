<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Commons\Database\Models\ExternalServices;
use NextDeveloper\Communication\Database\Models\Channels;

/**
 * Microsoft 365 / Outlook channel implementation (Microsoft Graph /me/sendMail, delegated Mail.Send).
 *
 * communication_channels.configuration:
 *   provider              (required) — 'microsoft_365'; this is what routes the channel here (see ChannelHelper)
 *   external_service_uuid (required) — UUID of the 'microsoft_365' common_external_services row. That row is the
 *                                      source of truth for the tokens: Microsoft rotates the refresh token on every
 *                                      refresh, so a copy in the channel credentials would go stale after one use.
 *   max_emails_per_hour   (optional)
 *
 * OAuth app credentials come from services.microsoft.* (client_id, client_secret, tenant).
 */
class Microsoft365 implements ChannelAbstract, VerifiableChannel
{
    public const NAME = 'microsoft_365';

    public const PROVIDER = 'microsoft_365';

    public const FIELDS = [
        'external_service_uuid' => 'required',
        'max_emails_per_hour'   => 'nullable',
    ];

    private const GRAPH_SEND_URL = 'https://graph.microsoft.com/v1.0/me/sendMail';

    protected ExternalServices $service;

    public function __construct(public readonly Channels $channel)
    {
        $config = $channel->configuration ?? [];
        if (is_string($config)) {
            $config = json_decode($config, true) ?? [];
        }

        if (!$this->validateConfig($config)) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required Microsoft 365 configuration (external_service_uuid).');
        }

        $service = ExternalServices::withoutGlobalScopes()
            ->where('uuid', $config['external_service_uuid'])
            ->whereNull('deleted_at')
            ->first();

        if (!$service) {
            throw new Exception(__METHOD__ . ': The Microsoft 365 connection of this channel no longer exists. Reconnect the account.');
        }

        if (!$service->is_alive) {
            throw new Exception(__METHOD__ . ': The Microsoft 365 connection is not active (the grant was revoked or expired). Reconnect the account.');
        }

        $this->service = $service;
    }

    public function send(mixed $message): void
    {
        $to      = $message['to'] ?? null;
        $subject = $message['subject'] ?? '(no subject)';
        $body    = $message['message'] ?? $message['body'] ?? '';
        $cc      = array_filter((array) ($message['cc'] ?? []));

        if (!$to) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing recipient.');
        }

        $payload = [
            'message' => [
                'subject'      => $subject,
                'body'         => ['contentType' => 'HTML', 'content' => $body],
                'toRecipients' => [['emailAddress' => ['address' => $to]]],
            ],
            'saveToSentItems' => true,
        ];

        if (!empty($cc)) {
            $payload['message']['ccRecipients'] = array_map(
                fn ($address) => ['emailAddress' => ['address' => $address]],
                array_values($cc)
            );
        }

        try {
            $response = Http::withToken($this->accessToken())->post(self::GRAPH_SEND_URL, $payload);

            //  The access token can be revoked before its expiry; refresh once and retry
            if ($response->status() === 401) {
                $response = Http::withToken($this->accessToken(true))->post(self::GRAPH_SEND_URL, $payload);
            }
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Microsoft 365 delivery failed', ['to' => $to, 'error' => $e->getMessage()]);

            throw new Exception(__METHOD__ . ': Failed to send via Microsoft 365: ' . $e->getMessage());
        }

        //  sendMail answers 202 Accepted with an empty body
        if (!$response->successful()) {
            $error = $response->json('error.message') ?: $response->body();

            Log::error(__METHOD__ . ': Microsoft 365 delivery failed', [
                'to'     => $to,
                'status' => $response->status(),
                'error'  => $error,
            ]);

            throw new Exception(__METHOD__ . ': Failed to send via Microsoft 365 (' . $response->status() . '): ' . $error);
        }
    }

    /**
     * Forces a token refresh: proves the grant and the app credentials still work, with no extra scope needed.
     */
    public function verify(): string
    {
        $this->accessToken(true);

        return 'Microsoft accepted the stored authorization.';
    }

    public function validateConfig(array $config): bool
    {
        return !empty($config['external_service_uuid']);
    }

    /**
     * Access token of the connection, refreshed (and persisted, including the rotated refresh token) when it is
     * about to expire or when $force is set.
     */
    private function accessToken(bool $force = false): string
    {
        $service   = $this->service->fresh() ?? $this->service;
        $expiresAt = data_get($service->configuration, 'expires_at');

        if (!$force && $expiresAt && now()->addMinutes(5)->lt($expiresAt)) {
            return self::decryptOrRaw($service->token);
        }

        if (empty($service->refresh_token)) {
            throw new Exception('Microsoft 365 connection has no refresh token. Reconnect the account.');
        }

        $tenant = config('services.microsoft.tenant', 'common');

        $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
            'client_id'     => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'refresh_token' => self::decryptOrRaw($service->refresh_token),
            'grant_type'    => 'refresh_token',
            //  Without offline_access Microsoft does not return the rotated refresh token
            'scope'         => implode(' ', array_unique(array_merge(data_get($service->configuration, 'allowed_scopes', []), ['offline_access']))),
        ]);

        if (!$response->successful() || empty($response->json('access_token'))) {
            $error = (string) $response->json('error', 'unknown_error');

            //  invalid_grant = revoked / expired refresh token; the user has to reconnect
            if ($error === 'invalid_grant') {
                $service->update(['is_alive' => false]);
            }

            throw new Exception('Microsoft token refresh failed: ' . $error . ' ' . $response->json('error_description', '') . ' Reconnect the account if this persists.');
        }

        $data = [
            'token'         => $response->json('access_token'),
            'configuration' => array_merge($service->configuration ?? [], [
                'expires_at' => now()->addSeconds($response->json('expires_in', 3600))->toISOString(),
            ]),
        ];

        //  Rotated refresh token; losing it would break the connection on the next refresh
        if ($response->json('refresh_token')) {
            $data['refresh_token'] = $response->json('refresh_token');
        }

        $service->update($data);
        $this->service = $service->fresh();

        return $response->json('access_token');
    }

    private static function decryptOrRaw(string $value): string
    {
        try {
            return decrypt($value);
        } catch (\Throwable) {
            return $value;
        }
    }
}
