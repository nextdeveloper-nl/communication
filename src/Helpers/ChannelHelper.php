<?php

namespace NextDeveloper\Communication\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Services\MessagesService;

class ChannelHelper
{
    /**
     * Returns the channel attached to a thread.
     */
    public static function getForThread(Threads $thread): ?Channels
    {
        $channel = Channels::find($thread->communication_channel_id);

        if (!$channel) {
            Log::error(__METHOD__ . ": Channel {$thread->communication_channel_id} not found for thread {$thread->id}");
        }

        return $channel;
    }

    /**
     * Returns the AvailableChannels definition matching a channel's type.
     * Used to resolve the delivery class for a channel.
     */
    public static function getAvailableChannelByType(string $type): ?AvailableChannels
    {
        $available = AvailableChannels::where('name', $type)->first();

        if (!$available) {
            Log::error(__METHOD__ . ": No AvailableChannels entry found for type '{$type}'");
        }

        return $available;
    }

    /**
     * Resolves and validates the delivery class registered on an AvailableChannels record.
     */
    public static function getChannelClass(AvailableChannels $availableChannel): ?string
    {
        $class = $availableChannel->class;

        if (!class_exists($class)) {
            Log::error(__METHOD__ . ": Delivery class {$class} not found");
            return null;
        }

        return $class;
    }

    /**
     * Handler classes for channel types that are not registered in the
     * AvailableChannels table.
     *
     * @var array<string, class-string>
     */
    private const BUILT_IN_CLASSES = [
        'smtp'             => \NextDeveloper\Communication\Channels\Smtp::class,
        'mailgun'          => \NextDeveloper\Communication\Channels\Mailgun::class,
        'gmail'            => \NextDeveloper\Communication\Channels\Gmail::class,
        'google_workspace' => \NextDeveloper\Communication\Channels\Gmail::class,
        'email'            => \NextDeveloper\Communication\Channels\Smtp::class,
        'mattermost'       => \NextDeveloper\Communication\Channels\Mattermost::class,
        'sms'              => \NextDeveloper\Communication\Channels\Sms::class,
    ];

    /**
     * Resolves the delivery handler for a channel type.
     *
     * A database-registered AvailableChannels row wins, so an operator can point
     * a type at a different class without a deploy; otherwise the built-in map
     * applies. This is the single place that answers "what sends this type", used
     * both when dispatching the queue and when sending a one-off test.
     */
    public static function getChannelClassForType(string $type): ?string
    {
        $available = AvailableChannels::where('name', $type)->first();

        if ($available && ($class = self::getChannelClass($available))) {
            return $class;
        }

        return self::BUILT_IN_CLASSES[$type] ?? null;
    }

    /**
     * Returns the highest-priority active channel for an account by type.
     * Use this to pick the sending transport when dispatching messages.
     */
    public static function getPrimaryForAccount(int $accountId, string $type): ?Channels
    {
        return Channels::where('iam_account_id', $accountId)
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('priority')
            ->first();
    }

    /**
     * Returns queued messages due for delivery, optionally capped to a limit.
     * Replaces V1 getPendingEmails — works on communication_messages.
     */
    public static function getDueMessages(int $limit = 100): Collection
    {
        return MessagesService::getDueForDelivery()->take($limit);
    }

    public static function logError(string $message, \Exception $exception): void
    {
        Log::error(__METHOD__ . ": {$message}: " . $exception->getMessage());
        Log::error($exception->getTraceAsString());
    }
}
