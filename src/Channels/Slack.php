<?php

namespace NextDeveloper\Communication\Channels;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;
use RuntimeException;

/**
 * Slack channel.
 *
 * Posts through the bot token (chat.postMessage, needs a channel) when one is stored, otherwise through the incoming
 * webhook. Values are read from the channel's credentials or configuration: bot_token, webhook_url, channel.
 */
class Slack implements ChannelAbstract, VerifiableChannel
{
    public const NAME = 'slack';

    public const FIELDS = [
        'webhook_url' => 'nullable',
        'bot_token' => 'nullable',
        'channel' => 'nullable',
    ];

    private const API_BASE_URL = 'https://slack.com/api';

    private ?string $botToken;

    private ?string $webhookUrl;

    private ?string $defaultChannel;

    public function __construct(public readonly Channels $channel)
    {
        $config = array_merge((array) ($channel->configuration ?? []), (array) ($channel->credentials ?? []));

        if (! $this->validateConfig($config)) {
            throw new InvalidArgumentException('Missing Slack credentials: store an incoming webhook URL or a bot token.');
        }

        $this->botToken = ! empty($config['bot_token']) ? trim((string) $config['bot_token']) : null;
        $this->webhookUrl = ! empty($config['webhook_url']) ? trim((string) $config['webhook_url']) : null;
        $this->defaultChannel = ! empty($config['channel']) ? (string) $config['channel'] : null;
    }

    public function validateConfig(array $config): bool
    {
        return ! empty($config['bot_token']) || ! empty($config['webhook_url']);
    }

    /**
     * Only a bot token can be checked without posting; an incoming webhook is proven by the post itself.
     */
    public function verify(): string
    {
        if (! $this->botToken) {
            return 'Slack incoming webhook stored (it is checked by posting to it).';
        }

        $response = $this->request(fn () => Http::timeout(15)->withToken($this->botToken)->post(self::API_BASE_URL.'/auth.test'));

        if ($response->json('ok') !== true) {
            throw new RuntimeException('Slack error: '.($response->json('error') ?? 'HTTP '.$response->status()));
        }

        return 'Slack accepted the bot token (workspace '.($response->json('team') ?? 'unknown').').';
    }

    /**
     * @param  mixed  $message  Array with 'message' (text) and optionally 'to' (channel name or id)
     */
    public function send(mixed $message): void
    {
        $target = is_array($message) ? ($message['to'] ?? null) : null;
        $target = $target ?: $this->defaultChannel;
        $text = strip_tags((string) (is_array($message) ? ($message['message'] ?? '') : $message));

        if ($this->botToken && $target) {
            $response = $this->request(fn () => Http::timeout(15)->withToken($this->botToken)
                ->post(self::API_BASE_URL.'/chat.postMessage', ['channel' => $target, 'text' => $text]));

            if ($response->json('ok') !== true) {
                throw new RuntimeException('Slack error: '.($response->json('error') ?? 'HTTP '.$response->status()));
            }

            return;
        }

        if (! $this->webhookUrl) {
            throw new RuntimeException('No Slack channel: set a default channel on the channel or pass a recipient.');
        }

        $response = $this->request(fn () => Http::timeout(15)->post($this->webhookUrl, ['text' => $text]));

        if (! $response->successful()) {
            throw new RuntimeException('Slack error (HTTP '.$response->status().'): '.mb_substr($response->body(), 0, 300));
        }
    }

    private function request(callable $call): Response
    {
        try {
            return $call();
        } catch (\Throwable $e) {
            // The webhook URL is itself a secret, so the original message is not passed on
            throw new RuntimeException('Could not reach Slack ('.class_basename($e).').');
        }
    }
}
