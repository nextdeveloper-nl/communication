<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * Mattermost Channel Implementation
 *
 * Handles sending messages to Mattermost via webhooks
 */
class Mattermost implements ChannelAbstract
{
    /**
     * Channel name identifier
     */
    public const NAME = 'Mattermost';

    /**
     * Required configuration fields
     *
     * @var array<string, string>
     */
    public const FIELDS = ['webhook_url' => 'required'];

    /**
     * The message to be sent
     */
    protected string $message;

    /**
     * The webhook URL for Mattermost
     */
    protected string $webhookUrl;

    /**
     * Creates a new Mattermost channel instance
     *
     * @param array<string, mixed> $config Configuration array containing webhook_url
     * @throws InvalidArgumentException If the configuration is invalid
     */
    public function __construct(array $config)
    {
        if (!$this->validateConfig($config)) {
            throw new InvalidArgumentException('Invalid configuration provided');
        }

        $this->webhookUrl = $config['webhook_url'];
    }

    /**
     * Validates the configuration array
     *
     * @param array<string, mixed> $config
     * @return bool
     */
    public function validateConfig(array $config): bool
    {
        if (!isset($config['webhook_url']) || !is_string($config['webhook_url'])) {
            return false;
        }

        // Validate webhook URL format
        if (!filter_var($config['webhook_url'], FILTER_VALIDATE_URL)) {
            return false;
        }

        return true;
    }

    /**
     * Sends the message to Mattermost
     *
     * @throws Exception If the message fails to send
     */
    public function send($message): void
    {
        try {
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->post($this->webhookUrl, [
                    'text' => $message,
                ]);

            $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception(
                "Failed to send message to Mattermost: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Handles the HTTP response from Mattermost
     *
     * @param Response $response
     * @throws Exception If the response indicates an error
     */
    protected function handleResponse(Response $response): void
    {
        if (!$response->successful()) {
            throw new Exception(
                "Mattermost API error: {$response->status()} - {$response->body()}",
                $response->status()
            );
        }
    }

    /**
     * Gets the message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Gets the webhook URL
     *
     * @return string
     */
    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }
}
