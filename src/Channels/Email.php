<?php

namespace NextDeveloper\Communication\Channels;

use InvalidArgumentException;
use Exception;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\EmailTemplates\GenericEnvelope;
use NextDeveloper\Communication\Envelopes\NotificationEnvelope;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\IAM\Database\Models\Users;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Gmail Channel Implementation
 *
 * Handles sending emails via Gmail API
 */
class Email implements ChannelAbstract
{
    /**
     * Channel name identifier
     */
    public const NAME = 'Gmail';

    /**
     * Creates a new Email channel instance
     *
     * @param array<string, mixed> $config Configuration array containing access_token, refresh_token, max_emails_per_hour
     * @throws Exception
     */
    public function __construct(public Users $user)
    {

    }

    /**
     * Validates the configuration array
     *
     * @param array<string, mixed> $config
     * @return bool
     */
    public function validateConfig(array $config): bool
    {
        return true;
    }

    /**
     * Sends the message via Gmail
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function sendGenericEmail($subject, $message): void
    {
        try {
            // If $message is an object with properties, extract them
            $envelope = new NotificationEnvelope($this->user, $subject, $message);

            (new Communicate($this->user))->sendEnvelopeNow($envelope);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': Error sending email', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            throw  new Exception(__METHOD__ . ': Failed to send message: ' . $e->getMessage());
        }
    }

    /**
     * Sends the message through the channel
     *
     * @param mixed $message
     * @throws \Exception If the message cannot be sent
     */
    public function send(mixed $message): void
    {
        //  Converting URLs in the message to clickable links
        $message['message'] = preg_replace_callback(
            '~(https?://[^\s<]+)~i',
            fn($m) => '<a href="' . htmlspecialchars($m[1]) . '">' . htmlspecialchars($m[1]) . '</a>',
            $message['message']
        );

        //  Converting new lines to <br> tags for HTML formatting
        $message['message'] = nl2br($message['message'], false);

        $this->sendGenericEmail($message['subject'], $message['message']);
    }
}
