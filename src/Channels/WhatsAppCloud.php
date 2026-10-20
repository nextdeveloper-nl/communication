<?php

namespace NextDeveloper\Communication\Channels;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;

/**
 * Outbound sender for a connected WhatsApp Business number (Meta WhatsApp Cloud API).
 *
 * communication_channels.credentials:
 *   access_token (required) - permanent/system-user token of the tenant's WhatsApp Business Account
 *
 * communication_channels.configuration:
 *   phone_number_id (required) - the Cloud API id of the number (not the phone number itself)
 *   waba_id, display_phone_number - informational
 *
 * Free-form text can only be sent inside the 24 hour customer service window (24h after the customer's last
 * message); outside it Meta rejects the call and the error is surfaced as a failed message.
 */
class WhatsAppCloud implements ChannelAbstract
{
    public const NAME = 'whatsapp';

    public const GRAPH_API_VERSION = 'v21.0';

    public const FIELDS = [
        'access_token' => 'required',
    ];

    public function __construct(public readonly Channels $channel)
    {
        if (!$this->validateConfig($channel->credentials ?? [])) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing required access_token.');
        }

        if (empty(data_get($channel->configuration, 'phone_number_id'))) {
            throw new InvalidArgumentException(__METHOD__ . ': Missing phone_number_id in channel configuration.');
        }
    }

    public function validateConfig(array $config): bool
    {
        return !empty($config['access_token']);
    }

    /**
     * Sends a text message to the number in $message['to'] (international format, digits only or with "+").
     *
     * @param mixed $message Array with 'message' (body text) and 'to' (recipient phone number)
     */
    public function send(mixed $message): void
    {
        $to = is_array($message) ? ($message['to'] ?? null) : null;
        $text = is_array($message) ? ($message['message'] ?? '') : (string) $message;

        // Cloud API wants the number as digits only
        $to = $to ? preg_replace('/\D+/', '', (string) $to) : null;

        if (!$to) {
            throw new \RuntimeException(__METHOD__ . ': No recipient phone number available for WhatsApp message.');
        }

        $phoneNumberId = data_get($this->channel->configuration, 'phone_number_id');

        // Token goes in the Authorization header so it never appears in a URL (and so not in exception messages)
        $response = Http::withToken(data_get($this->channel->credentials, 'access_token'))
            ->post(sprintf('https://graph.facebook.com/%s/%s/messages', self::GRAPH_API_VERSION, $phoneNumberId), [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $text],
            ]);

        if (!$response->successful()) {
            Log::error(__METHOD__ . ': Cloud API send failed', [
                'channel_id'  => $this->channel->id,
                'status'      => $response->status(),
                'graph_error' => $response->json('error') ?? mb_substr($response->body(), 0, 500),
            ]);

            throw new \RuntimeException('Failed to send WhatsApp message: ' . ($response->json('error.message') ?? $response->status())
                . ' (code ' . $response->json('error.code') . ')');
        }
    }
}
