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
