<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Channels\ChannelAbstract;
use NextDeveloper\Communication\Channels\FacebookMessenger;
use NextDeveloper\Communication\Channels\Gmail;
use NextDeveloper\Communication\Channels\Mailgun;
use NextDeveloper\Communication\Channels\Mattermost;
use NextDeveloper\Communication\Channels\Microsoft365;
use NextDeveloper\Communication\Channels\Slack;
use NextDeveloper\Communication\Channels\Sms;
use NextDeveloper\Communication\Channels\Smtp;
use NextDeveloper\Communication\Channels\Telegram;
use NextDeveloper\Communication\Channels\VerifiableChannel;
use NextDeveloper\Communication\Channels\WhatsAppCloud;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Exceptions\TokenRefreshedException;
use NextDeveloper\Communication\Helpers\ChannelHelper;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Tests a communication channel against its provider: the stored credentials are checked, then (where the channel
 * type allows it) a short test message is really delivered. The outcome is saved on the channel.
 */
class ChannelTestService
{
    public const STATUS_OK = 'ok';

    public const STATUS_ERROR = 'error';

    // The channel type has no delivery handler (inbound-only or proxied types); nothing is saved on the channel
    public const STATUS_UNSUPPORTED = 'unsupported';

    // The recipient is an email address
    public const RECIPIENT_EMAIL = 'email';

    // The recipient is a phone number
    public const RECIPIENT_PHONE = 'phone';

    // The recipient is a chat or channel id; the channel's own default is used when none is given
    public const RECIPIENT_CHAT = 'chat';

    // The channel posts to a fixed destination (incoming webhook)
    public const RECIPIENT_NONE = 'none';

    private const MAX_MESSAGE_LENGTH = 1000;

    // Configuration keys whose values must never show up in an error text
    private const SECRET_KEY_PATTERN = '/pass|token|secret|api_?key|auth|webhook|sid$/i';

    /**
     * What kind of recipient each handler delivers to.
     *
     * @var array<class-string, string>
     */
    private const RECIPIENT_KINDS = [
        Smtp::class => self::RECIPIENT_EMAIL,
        Mailgun::class => self::RECIPIENT_EMAIL,
        Gmail::class => self::RECIPIENT_EMAIL,
        Microsoft365::class => self::RECIPIENT_EMAIL,
        Sms::class => self::RECIPIENT_PHONE,
        WhatsAppCloud::class => self::RECIPIENT_PHONE,
        Telegram::class => self::RECIPIENT_CHAT,
        Slack::class => self::RECIPIENT_CHAT,
        Mattermost::class => self::RECIPIENT_NONE,
        FacebookMessenger::class => self::RECIPIENT_NONE,
    ];

    /**
     * Handlers that are only verified by default: a free-form message is refused by the provider unless the
     * recipient wrote first (WhatsApp 24 hour window, Messenger PSID).
     *
     * @var array<int, class-string>
     */
    private const VERIFY_ONLY_BY_DEFAULT = [WhatsAppCloud::class, FacebookMessenger::class];

    /**
     * Runs the test and saves its outcome on the channel.
     *
     * @return array{id: string, status: string, message: string, delivered: bool, recipient: ?string, last_checked_at: ?string}
     *
     * @throws NotAllowedException When the channel does not exist or is not visible to the caller
     * @throws ValidationException When the given recipient does not fit the channel type
     */
    public static function test(string $ref, ?string $recipient = null): array
    {
        $channel = ChannelsService::getByRef($ref);

        if (! $channel) {
            throw new NotAllowedException('We cannot find this channel. Maybe you dont have the permission to see it?');
        }

        if (! ChannelHelper::getChannelClassForChannel($channel)) {
            return [
                'id' => $channel->uuid,
                'status' => self::STATUS_UNSUPPORTED,
                'message' => 'Testing is not available for "'.$channel->type.'" channels: this type has nothing to send a test through.',
                'delivered' => false,
                'recipient' => null,
                'last_checked_at' => null,
            ];
        }

        $recipient = self::normalizeRecipient($channel, $recipient);
        $delivered = false;
        $usedRecipient = null;

        try {
            $handler = ChannelHelper::makeHandler($channel);
            $details = [];

            if ($handler instanceof VerifiableChannel) {
                $details[] = $handler->verify();
            }

            if (self::shouldDeliver($handler, $recipient)) {
                $usedRecipient = self::send(
                    $handler,
                    $channel,
                    $recipient,
                    'Test message from '.config('app.name'),
                    'This is a test message for the channel "'.$channel->name.'". If you can read it, the channel works.'
                );
                $delivered = true;
                $details[] = $usedRecipient ? 'Test message sent to '.$usedRecipient.'.' : 'Test message posted.';
            } else {
                $details[] = 'No test message was sent.';
            }

            $status = self::STATUS_OK;
            $message = implode(' ', array_filter($details));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $status = self::STATUS_ERROR;
            $message = self::sanitize($e->getMessage(), $channel);

            Log::warning('[ChannelTestService::test] Channel test failed', [
                'channel_id' => $channel->id,
                'type' => $channel->type,
                'error' => $message,
            ]);
        }

        $checkedAt = now();

        ChannelsService::update($channel->uuid, [
            'last_checked_at' => $checkedAt,
            'last_check_status' => $status,
            'last_check_message' => $message,
        ]);

        return [
            'id' => $channel->uuid,
            'status' => $status,
            'message' => $message,
            'delivered' => $delivered,
            'recipient' => $usedRecipient,
            'last_checked_at' => $checkedAt->toISOString(),
        ];
    }

    /**
     * Delivers a plain text message through the channel.
     *
     * @return ?string The recipient the message went to; null when the channel posts to a fixed destination
     *
     * @throws \Throwable With the provider's own error when the delivery fails
     */
    public static function deliver(Channels $channel, ?string $recipient, string $subject, string $text): ?string
    {
        return self::send(ChannelHelper::makeHandler($channel), $channel, $recipient, $subject, $text);
    }

    /**
     * The kind of recipient a channel delivers to (one of the RECIPIENT_* constants), null when it has no handler.
     */
    public static function recipientKind(Channels $channel): ?string
    {
        $class = ChannelHelper::getChannelClassForChannel($channel);

        return $class ? self::kindOfClass($class) : null;
    }

    /**
     * Where a message goes when the caller names nobody: the user's own address or number.
     */
    public static function defaultRecipient(Channels $channel, ?Users $user): ?string
    {
        return match (self::recipientKind($channel)) {
            self::RECIPIENT_EMAIL => $user?->email,
            self::RECIPIENT_PHONE => $user?->phone_number,
            default => null,
        };
    }

    private static function send(ChannelAbstract $handler, Channels $channel, ?string $recipient, string $subject, string $text): ?string
    {
        $kind = self::kindOfClass(get_class($handler));

        if (in_array($kind, [self::RECIPIENT_EMAIL, self::RECIPIENT_PHONE], true) && ! $recipient) {
            throw new \RuntimeException($kind === self::RECIPIENT_EMAIL
                ? 'No recipient email address is available for this message.'
                : 'No recipient phone number is available for this message.');
        }

        $payload = [
            'subject' => $subject,
            'message' => $kind === self::RECIPIENT_EMAIL ? '<p>'.e($text).'</p>' : $text,
            'to' => $kind === self::RECIPIENT_NONE ? null : $recipient,
        ];

        try {
            $handler->send($payload);
        } catch (TokenRefreshedException) {
            // The handler refreshed an expired OAuth token and asks for a retry
            ChannelHelper::makeHandler($channel->fresh() ?? $channel)->send($payload);
        }

        return $payload['to'];
    }

    private static function shouldDeliver(ChannelAbstract $handler, ?string $recipient): bool
    {
        foreach (self::VERIFY_ONLY_BY_DEFAULT as $class) {
            if ($handler instanceof $class) {
                // Messenger needs a PSID, which a test cannot ask for; WhatsApp is sent only on request
                return $handler instanceof WhatsAppCloud && ! empty($recipient);
            }
        }

        return true;
    }

    /**
     * Falls back to the current user's own address or number and rejects a recipient that cannot work for the type.
     */
    private static function normalizeRecipient(Channels $channel, ?string $recipient): ?string
    {
        $recipient = $recipient !== null ? trim($recipient) : null;
        $kind = self::recipientKind($channel);
        $class = ChannelHelper::getChannelClassForChannel($channel);

        if ($recipient === null || $recipient === '') {
            // WhatsApp is only verified unless a number is given explicitly
            return in_array($class, self::VERIFY_ONLY_BY_DEFAULT, true)
                ? null
                : self::defaultRecipient($channel, UserHelper::me());
        }

        if ($kind === self::RECIPIENT_EMAIL && ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['recipient' => 'The recipient must be a valid email address for this channel.']);
        }

        if ($kind === self::RECIPIENT_PHONE && ! preg_match('/^\+?[1-9][\d\s\-()]{5,20}$/', $recipient)) {
            throw ValidationException::withMessages(['recipient' => 'The recipient must be a phone number in international format for this channel.']);
        }

        return $recipient;
    }

    private static function kindOfClass(string $class): string
    {
        foreach (self::RECIPIENT_KINDS as $known => $kind) {
            if (is_a($class, $known, true)) {
                return $kind;
            }
        }

        return self::RECIPIENT_NONE;
    }

    /**
     * Makes a provider error safe to store and show: stored secrets are blanked out (a provider or an HTTP client
     * can echo the request back), internal class prefixes are dropped and the text is cut to a sane length.
     */
    private static function sanitize(string $message, Channels $channel): string
    {
        foreach ([(array) ($channel->credentials ?? []), (array) ($channel->configuration ?? [])] as $index => $bag) {
            array_walk_recursive($bag, function ($value, $key) use (&$message, $index) {
                $isSecret = $index === 0 || preg_match(self::SECRET_KEY_PATTERN, (string) $key);

                if ($isSecret && is_string($value) && strlen($value) >= 6) {
                    $message = str_replace([$value, urlencode($value)], '********', $message);
                }
            });
        }

        // "Vendor\Package\Class::method: text" → "text"
        $message = preg_replace('/(?:[A-Za-z_][\w]*\\\\)+[\w]+::[\w]+:\s*/', '', $message) ?? $message;
        $message = trim(preg_replace('/\s+/', ' ', $message) ?? $message);

        return mb_substr($message !== '' ? $message : 'The test failed without an error message.', 0, self::MAX_MESSAGE_LENGTH);
    }
}
