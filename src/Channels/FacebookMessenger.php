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
class FacebookMessenger implements ChannelAbstract, VerifiableChannel
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

    /**
     * Asks the Graph API for the Page with the stored token: the token is valid and still reaches the Page.
     */
    public function verify(): string
    {
        $pageId = data_get($this->channel->configuration, 'page_id');

        if (! $pageId) {
            throw new \RuntimeException('Missing page_id in channel configuration.');
        }

        // Token in the Authorization header so it never appears in a URL (and so not in exception messages)
        $response = Http::timeout(15)
            ->withToken(data_get($this->channel->credentials, 'page_access_token'))
            ->get(sprintf('https://graph.facebook.com/%s/%s', self::GRAPH_API_VERSION, $pageId), ['fields' => 'id,name']);

        if (! $response->successful() || (string) $response->json('id') !== (string) $pageId) {
            throw new \RuntimeException(sprintf(
                'Meta error (HTTP %d%s): %s',
                $response->status(),
                $response->json('error.code') ? ', code '.$response->json('error.code') : '',
                $response->json('error.message') ?? 'the token does not reach this Page'
            ));
        }

        return 'Meta accepted the token; Page '.($response->json('name') ?? $pageId).' is reachable.';
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
