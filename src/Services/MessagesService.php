<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Helpers\ChannelHelper;
use NextDeveloper\Communication\Services\AbstractServices\AbstractMessagesService;
use NextDeveloper\IAM\Database\Models\Users;
use Illuminate\Support\Facades\Log;

/**
 * This class is responsible from managing the data for Messages
 *
 * Class MessagesService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class MessagesService extends AbstractMessagesService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Creates a message and enforces the thread XOR campaign constraint.
     * Also bumps last_message_at on the thread when the message belongs to one.
     */
    public static function create(array $data): Messages
    {
        $hasThread   = !empty($data['communication_thread_id']);
        $hasCampaign = !empty($data['crm_campaign_id']);
        $hasChannel  = !empty($data['communication_channel_id']);

        // Standalone transactional messages (no thread, no campaign) are allowed
        // when a specific channel is provided. All other combinations must have
        // exactly one of thread or campaign.
        if (!$hasChannel && $hasThread === $hasCampaign) {
            throw new InvalidArgumentException(
                'A message must belong to exactly one of: communication_thread_id or crm_campaign_id, '
                . 'or must specify a communication_channel_id for standalone delivery.'
            );
        }

        $message = parent::create($data);

        if ($message->communication_thread_id) {
            $thread = Threads::find($message->communication_thread_id);
            if ($thread) {
                ThreadsService::touchLastMessageAt($thread);
            }
        }

        return $message;
    }

    /**
     * Marks a message as read by the contact.
     */
    public static function markAsRead(string $ref): Messages
    {
        return self::update($ref, ['read_at' => now()]);
    }

    /**
     * Marks a message as successfully delivered.
     */
    public static function markAsDelivered(string $ref): Messages
    {
        return self::update($ref, [
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Marks a message as failed and records the failure reason.
     */
    public static function markAsFailed(string $ref, string $reason): Messages
    {
        return self::update($ref, [
            'status'         => 'failed',
            'failed_at'      => now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Returns all queued messages whose scheduled delivery time has passed.
     */
    public static function getDueForDelivery(): Collection
    {
        return Messages::where('status', 'queued')
            ->where('deliver_at', '<=', now())
            ->get();
    }

    /**
     * Delivers a message through its preferred channel.
     *
     * Resolution order:
     *   1. message's own communication_channel_id  (explicit preference)
     *   2. thread's communication_channel_id       (inherited from conversation)
     *   3. account's highest-priority active email channel (fallback)
     *
     * Updates message status to 'sent' on success or 'failed' on error.
     */
    public static function deliver(Messages $message): void
    {
        $channel = self::resolveChannel($message);

        if (!$channel) {
            Log::warning('[MessagesService::deliver] No channel resolved for message', [
                'message_id' => $message->id,
            ]);

            self::markAsFailed($message->uuid, 'No delivery channel available');
            return;
        }

        $available = ChannelHelper::getAvailableChannelByType($channel->type);

        if (!$available) {
            self::markAsFailed($message->uuid, 'No handler registered for channel type: ' . $channel->type);
            return;
        }

        $class = ChannelHelper::getChannelClass($available);

        if (!$class) {
            self::markAsFailed($message->uuid, 'Channel handler class not found for type: ' . $channel->type);
            return;
        }

        try {
            // Prefer the stored recipient address; fall back to the sender's email.
            $recipient = $message->recipient
                ?? Users::find($message->sent_by_user_id)?->email;

            $processor = new $class(channel: $channel);
            $processor->send([
                'subject' => $message->metadata['subject'] ?? '(no subject)',
                'message' => $message->body,
                'to'      => $recipient,
            ]);

            self::markAsDelivered($message->uuid);
        } catch (\Throwable $e) {
            Log::error('[MessagesService::deliver] Delivery failed', [
                'message_id' => $message->id,
                'channel_id' => $channel->id,
                'error'      => $e->getMessage(),
            ]);

            self::markAsFailed($message->uuid, $e->getMessage());
        }
    }

    /**
     * Resolves the delivery channel for a message using the three-level fallback.
     */
    private static function resolveChannel(Messages $message): ?Channels
    {
        // 1. Explicit channel preference on the message itself
        if ($message->communication_channel_id) {
            $channel = Channels::find($message->communication_channel_id);
            if ($channel) {
                return $channel;
            }
        }

        // 2. Channel inherited from the thread
        if ($message->communication_thread_id) {
            $thread = Threads::find($message->communication_thread_id);
            if ($thread?->communication_channel_id) {
                $channel = Channels::find($thread->communication_channel_id);
                if ($channel) {
                    return $channel;
                }
            }
        }

        // 3. Account's highest-priority active email channel
        return ChannelHelper::getPrimaryForAccount($message->iam_account_id, 'email');
    }
}