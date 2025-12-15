<?php

namespace NextDeveloper\Communication\Channels;

use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Communication\Services\SmsDelivery\Twillio;
use NextDeveloper\IAM\Database\Models\Users;

/**
 * SMS Channel Implementation
 *
 * Handles sending SMS messages via configurable providers (default: Twilio)
 */
class Sms implements ChannelAbstract
{
    /**
     * Channel name identifier
     */
    public const NAME = 'Sms';

    /**
     * Default SMS provider
     */
    public const DEFAULT_PROVIDER = 'twilio';

    /**
     * Required configuration fields
     *
     * @var array<string, string>
     */
    public const FIELDS = ['phone_number' => 'required'];

    /**
     * The user to send SMS to
     */
    protected Users $user;

    /**
     * The phone number to send SMS to
     */
    protected ?string $phoneNumber;

    /**
     * Configuration array
     */
    protected array $config;

    /**
     * SMS provider to use
     */
    protected string $provider;

    /**
     * Creates a new SMS channel instance
     *
     * @param array<string, mixed> $config Configuration array containing phone_number and provider
     * @param Users|null $user The user to send SMS to
     */
    public function __construct(array $config = [], ?Users $user = null)
    {
        $this->config = $config;
        $this->user = $user;

        // Set provider from config or use default (twilio)
        $this->provider = $config['provider'] ?? config('communication.defaults.sms_provider', self::DEFAULT_PROVIDER);

        // Get a phone number from a user
        $this->phoneNumber = $user?->phone_number;

    }

    /**
     * Validates the configuration array
     *
     * @param array<string, mixed> $config
     * @return bool
     */
    public function validateConfig(array $config): bool
    {
        // Phone number can come from config or user
        return true;
    }

    /**
     * Validates a phone number format
     *
     * @param string $phoneNumber
     * @return bool
     */
    protected function validatePhoneNumber(string $phoneNumber): bool
    {
        // Basic validation: should start with + and contain only digits after
        return preg_match('/^\+?[1-9]\d{1,14}$/', $phoneNumber) === 1;
    }

    /**
     * Sends the message via SMS using the configured provider
     *
     * @param mixed $message Array containing 'subject' and 'message' keys
     * @throws Exception If the message cannot be sent
     */
    public function send(mixed $message): void
    {
        if (!$this->phoneNumber) {
            Log::warning(__METHOD__ . ': No phone number available for SMS delivery');
            throw new \RuntimeException('No phone number available for SMS delivery');
        }

        if (!$this->validatePhoneNumber($this->phoneNumber)) {
            Log::warning(__METHOD__ . ': Invalid phone number format', [
                'phone_number' => $this->phoneNumber
            ]);
            throw new \RuntimeException('Invalid phone number format');
        }

        try {
            // Compose SMS content from subject and message
            $smsContent = $this->composeSmsContent($message);

            // Send via configured provider (default: twilio)
            switch ($this->provider) {
                case 'twilio':
                default:
                    Twillio::send($smsContent, $this->phoneNumber);
                    break;
            }

            Log::info(__METHOD__ . ': SMS sent successfully', [
                'provider' => $this->provider,
                'to' => $this->phoneNumber,
                'content_length' => strlen($smsContent)
            ]);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Error sending SMS', [
                'provider' => $this->provider,
                'error' => $e->getMessage(),
                'to' => $this->phoneNumber
            ]);
            throw new \RuntimeException('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * Composes SMS content from message array
     *
     * @param mixed $message
     * @return string
     */
    protected function composeSmsContent(mixed $message): string
    {
        if (is_string($message)) {
            return $message;
        }

        if (is_array($message)) {
            $body = $message['message'] ?? '';
            // Strip HTML tags for SMS
            return strip_tags($body);
        }

        return (string) $message;
    }

    /**
     * Gets the phone number
     *
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * Sets the phone number
     *
     * @param string $phoneNumber
     * @return self
     */
    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Gets the current SMS provider
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Sets the SMS provider
     *
     * @param string $provider
     * @return self
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
}
