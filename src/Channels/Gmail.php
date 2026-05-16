<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;

/**
 * Gmail / Google Workspace channel implementation.
 *
 * Configuration keys expected in communication_channels.configuration:
 *   access_token  (required) — OAuth2 access token
 *   refresh_token (required) — OAuth2 refresh token
 *   from_address  (optional) — sender address shown to recipients
 *   max_messages_per_hour (optional, default 500)
 */
class Gmail implements ChannelAbstract
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

    public function __construct(public readonly Channels $channel)
    {
        $config = $channel->configuration ?? [];

        if (!$this->validateConfig($config)) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required Gmail configuration (access_token, refresh_token).');
        }

        $this->fromAddress = $config['from_address'] ?? 'me';

        try {
            $this->client = new Google_Client();
            $this->client->setAccessToken($config['access_token']);
            $this->client->refreshToken($config['refresh_token']);

            $this->service = new Google_Service_Gmail($this->client);
        } catch (Exception $e) {
            throw new Exception(__METHOD__ . ': Failed to initialize Gmail client: ' . $e->getMessage());
        }
    }

    public function send(mixed $message): void
    {
        try {
            $to      = $message['to'] ?? null;
            $subject = $message['subject'] ?? '(no subject)';
            $body    = $message['message'] ?? $message['body'] ?? '';
            $cc      = $message['cc'] ?? [];

            $raw = $this->buildRaw($to, $subject, $body, $this->fromAddress, $cc);

            $gmailMessage = new Google_Service_Gmail_Message();
            $gmailMessage->setRaw(rtrim(strtr(base64_encode($raw), '+/', '-_'), '='));

            $this->service->users_messages->send('me', $gmailMessage);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Gmail delivery failed', [
                'to'    => $message['to'] ?? null,
                'error' => $e->getMessage(),
            ]);
            throw new Exception(__METHOD__ . ': Failed to send via Gmail: ' . $e->getMessage());
        }
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

        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $headers .= base64_encode($body);

        return $headers;
    }
}
