<?php

namespace NextDeveloper\Communication\Channels;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;
use RuntimeException;

/**
 * Telegram bot channel (Bot API).
 *
 * Reads bot_token (required) and chat_id (default recipient) from the channel's credentials or configuration.
 */
class Telegram implements ChannelAbstract, VerifiableChannel
{
    public const NAME = 'telegram';

    public const FIELDS = [
        'bot_token' => 'required',
        'chat_id' => 'nullable',
    ];

    private const API_BASE_URL = 'https://api.telegram.org';

    private string $botToken;

    private ?string $defaultChatId;

    public function __construct(public readonly Channels $channel)
    {
        $config = array_merge((array) ($channel->configuration ?? []), (array) ($channel->credentials ?? []));

        if (! $this->validateConfig($config)) {
            throw new InvalidArgumentException('Missing required Telegram bot token.');
        }

        $this->botToken = trim((string) $config['bot_token']);
        $this->defaultChatId = isset($config['chat_id']) && $config['chat_id'] !== '' ? (string) $config['chat_id'] : null;
    }

    public function validateConfig(array $config): bool
    {
        return ! empty($config['bot_token']);
    }

    public function verify(): string
    {
        $result = $this->call('getMe');

        return 'Telegram accepted the bot token (bot @'.($result['username'] ?? 'unknown').').';
    }

    /**
     * @param  mixed  $message  Array with 'message' (text) and optionally 'to' (chat id); the channel's chat_id is the default
     */
    public function send(mixed $message): void
    {
        $chatId = is_array($message) ? ($message['to'] ?? null) : null;
        $chatId = $chatId ?: $this->defaultChatId;
        $text = is_array($message) ? ($message['message'] ?? '') : (string) $message;

        if (! $chatId) {
            throw new RuntimeException('No Telegram chat id: set a default chat id on the channel or pass a recipient.');
        }

        $this->call('sendMessage', ['chat_id' => $chatId, 'text' => strip_tags((string) $text)]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function call(string $method, array $payload = []): array
    {
        try {
            $response = Http::timeout(15)->post(sprintf('%s/bot%s/%s', self::API_BASE_URL, $this->botToken, $method), $payload);
        } catch (\Throwable $e) {
            // The request URL carries the bot token, so the original message is not passed on
            throw new RuntimeException('Could not reach Telegram ('.class_basename($e).').');
        }

        if (! $response->successful() || $response->json('ok') !== true) {
            throw new RuntimeException(sprintf(
                'Telegram error %s: %s',
                $response->json('error_code') ?? $response->status(),
                $response->json('description') ?? 'no details'
            ));
        }

        return (array) $response->json('result', []);
    }
}
