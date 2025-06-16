<?php

namespace NextDeveloper\Communication\Channels;

use InvalidArgumentException;
use Exception;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Gmail Channel Implementation
 *
 * Handles sending emails via Gmail API
 */
class Gmail implements ChannelAbstract
{
    /**
     * Channel name identifier
     */
    public const NAME = 'Gmail';

    /**
     * Required configuration fields
     *
     * @var array<string, string>
     */
    public const FIELDS = [
        'access_token' => 'required',
        'refresh_token' => 'required',
        'max_emails_per_hour' => 'nullable'
    ];

    /**
     * The Gmail client
     */
    protected Google_Client $client;

    /**
     * The Gmail service
     */
    protected Google_Service_Gmail $service;

    /**
     * Maximum emails per hour
     */
    protected int $maxEmailsPerHour;

    /**
     * Creates a new Gmail channel instance
     *
     * @param array<string, mixed> $config Configuration array containing access_token, refresh_token, max_emails_per_hour
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!$this->validateConfig($config)) {
            throw new InvalidArgumentException(__METHOD__ . ': Invalid configuration provided');
        }

        $this->maxEmailsPerHour = (int)$config['max_emails_per_hour'];

        try {
            $this->client = new Google_Client();
            $this->client->setAccessToken($config['access_token']);
            $this->client->refreshToken($config['refresh_token']);

            $this->service = new Google_Service_Gmail($this->client);
        } catch (Exception $e) {
            throw new Exception(__METHOD__ . ': Failed to initialize Gmail client: ' . $e->getMessage());
        }
    }

    /**
     * Validates the configuration array
     *
     * @param array<string, mixed> $config
     * @return bool
     */
    public function validateConfig(array $config): bool
    {
        // Get keys with 'required' validation
        $requiredFields = array_filter(self::FIELDS, function ($rule) {
            return $rule === 'required';
        });

        foreach ($requiredFields as $field => $rule) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sends the message via Gmail
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function send(mixed $message): void
    {
        try {
            // Check rate limit
            if (!$this->checkRateLimit()) {
                throw new Exception(__METHOD__ . ': Rate limit exceeded. Maximum emails per hour: ' . $this->maxEmailsPerHour);
            }

            // Create the message
            $email = new Google_Service_Gmail_Message();

            // If $message is an object with properties, extract them
            $rawMessage = $this->createEmail(
                $message->to[0],
                $message->subject,
                $message->body,
                $message->from_email_address
            );


            $email->setRaw(base64_encode($rawMessage));

            // Send the message
            $this->service->users_messages->send('me', $email);

            // Update rate limit counter
            $this->updateRateLimitCounter();

        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Error sending email', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            throw  new Exception(__METHOD__ . ': Failed to send message: ' . $e->getMessage());
        }
    }

    /**
     * Creates an email message
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param string|null $from Sender email (optional)
     * @return string Raw email message
     */
    protected function createEmail(string $to, string $subject, string $body, ?string $from = null): string
    {
        $from = $from ?? 'me';

        $message = "From: {$from}\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=utf-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= base64_encode($body);

        return $message;
    }

    /**
     * Checks if the rate limit has been exceeded
     *
     * @return bool True if underrate limit, false otherwise
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function checkRateLimit(): bool
    {
        $cacheKey = 'gmail_rate_limit_' . date('Y-m-d_H');
        $count = cache()->get($cacheKey, 0);

        return $count < $this->maxEmailsPerHour;
    }

    /**
     * Updates the rate limit counter
     */
    protected function updateRateLimitCounter(): void
    {
        $cacheKey = 'gmail_rate_limit_' . date('Y-m-d_H');
        $count = cache()->get($cacheKey, 0);

        // Increment counter and set expiry to the end of the current hour
        $expiryTime = strtotime(date('Y-m-d H:59:59'));
        $ttl = $expiryTime - time();

        cache()->put($cacheKey, $count + 1, $ttl);
    }
}
