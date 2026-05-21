<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Google_Client;
use Google_Service_Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Exceptions\TokenRefreshedException;

/**
 * Gmail / Google Workspace channel implementation.
 *
 * communication_channels.credentials:
 *   access_token  (required) — OAuth2 access token
 *   refresh_token (required) — OAuth2 refresh token (long-lived)
 *   expires_in    (optional) — token lifetime in seconds (set by Google)
 *   created       (optional) — Unix timestamp when token was issued (set by Google)
 *
 * communication_channels.configuration:
 *   from_address          (optional) — sender address shown to recipients
 *   max_messages_per_hour (optional, default 500)
 *
 * Token lifecycle: the full credentials array (including expiry metadata) is passed
 * to Google_Client. When the access token is expired, it is refreshed automatically
 * and the new credentials are persisted back to the channel record.
 */
class Gmail implements ChannelAbstract
{
    public const NAME = 'gmail';

    public const FIELDS = [
        'client_id'             => 'required',
        'client_secret'         => 'required',
        'access_token'          => 'required',
        'refresh_token'         => 'required',
        'from_address'          => 'nullable',
        'max_messages_per_hour' => 'nullable',
    ];

    protected Google_Client $client;
    protected Google_Service_Gmail $service;
    protected string $fromAddress;

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

        if (!$this->validateConfig($credentials)) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required Gmail credentials (client_id, client_secret, access_token, refresh_token).');
        }

        $this->fromAddress = $config['from_address'] ?? 'me';

        try {
            $this->client = new Google_Client();
            $this->client->setClientId($credentials['client_id']);
            $this->client->setClientSecret($credentials['client_secret']);

            // Pass the full credentials array so Google_Client can track expiry metadata
            $this->client->setAccessToken($credentials);

            if ($this->client->isAccessTokenExpired()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($credentials['refresh_token']);

                if (isset($newToken['error'])) {
                    throw new Exception('Token refresh failed: ' . ($newToken['error_description'] ?? $newToken['error']));
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

    private function refreshAndPersistToken(): void
    {
        $credentials = $this->channel->credentials ?? [];
        if (is_string($credentials)) {
            $credentials = json_decode($credentials, true) ?? [];
        }

        $newToken = $this->client->fetchAccessTokenWithRefreshToken($credentials['refresh_token']);

        if (isset($newToken['error'])) {
            throw new Exception('Token refresh failed: ' . ($newToken['error_description'] ?? $newToken['error']));
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
        foreach (['client_id', 'client_secret', 'access_token', 'refresh_token'] as $field) {
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
