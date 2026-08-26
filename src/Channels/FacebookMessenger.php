<?php

namespace NextDeveloper\Communication\Channels;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;

/**
 * Outbound sender for a connected Facebook Page, used when a CRM/Communication
 * agent replies to an inbound Messenger conversation.
 *
 * communication_channels.credentials:
 *   page_access_token (required) — Page access token used to call the Graph API
 *
 * communication_channels.configuration:
 *   page_id (required) — the connected Facebook Page's id
 *
 * Follow-up to inbound ingestion (FacebookWebhookService); not required for
 * receiving messages, only for replying to them from within this app.
 */
class FacebookMessenger implements ChannelAbstract
{
    public const NAME = 'facebook';

    private const GRAPH_API_VERSION = 'v19.0';

    public const FIELDS = [
        'page_access_token' => 'required',
    ];

    public function __construct(public readonly Channels $channel)
    {
        if (!$this->validateConfig($channel->credentials ?? [])) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required page_access_token.');
        }
    }

    public function validateConfig(array $config): bool
    {
        return !empty($config['page_access_token']);
    }

    /**
     * Sends a text reply to the PSID stored in $message['to'] (the value that was
     * written into Messages.recipient when the outbound message was created).
     *
     * @param mixed $message Array with 'message' (body text) and 'to' (recipient PSID)
     */
    public function send(mixed $message): void
    {
        $psid = is_array($message) ? ($message['to'] ?? null) : null;
        $text = is_array($message) ? ($message['message'] ?? '') : (string) $message;

        if (!$psid) {
            throw new \RuntimeException(__METHOD__ . ': No recipient PSID available for Messenger reply.');
        }

        $accessToken = data_get($this->channel->credentials, 'page_access_token');

        $response = Http::post(
            sprintf('https://graph.facebook.com/%s/me/messages', self::GRAPH_API_VERSION),
            [
                'access_token'    => $accessToken,
                'recipient'       => ['id' => $psid],
                'messaging_type'  => 'RESPONSE',
                'message'         => ['text' => $text],
            ]
        );

        if (!$response->successful()) {
            Log::error(__METHOD__ . ': Graph API send failed', [
                'channel_id' => $this->channel->id,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);
            throw new \RuntimeException('Failed to send Messenger reply: ' . $response->body());
        }
    }
}
