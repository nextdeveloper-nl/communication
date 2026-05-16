<?php

namespace NextDeveloper\Communication\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use NextDeveloper\Commons\Database\Models\ExternalServices;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Helpers\ChannelHelper;
use NextDeveloper\Communication\Services\AbstractServices\AbstractMessagesService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
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

        // The API accepts UUIDs for FK fields; resolve them to integer IDs before insert.
        if ($hasChannel && !is_int($data['communication_channel_id'])) {
            $data['communication_channel_id'] = Channels::withoutGlobalScope(AuthorizationScope::class)
                ->where('uuid', $data['communication_channel_id'])
                ->firstOrFail()
                ->id;
        }

        if (!empty($data['communication_thread_id']) && !is_int($data['communication_thread_id'])) {
            $data['communication_thread_id'] = Threads::where('uuid', $data['communication_thread_id'])
                ->firstOrFail()
                ->id;
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
     * Creates and immediately delivers a transactional email message.
     * Resolves the channel from its UUID, records the message, and dispatches it.
     */
    public static function sendEmail(array $data): Messages
    {
        $channel = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $data['communication_channel_id'])
            ->firstOrFail();

        $message = self::create([
            'communication_channel_id' => $channel->id,
            'direction'                => 1,
            'content_type'             => 'text/html',
            'body'                     => $data['body'],
            'recipient'                => $data['recipient'],
            'status'                   => 'queued',
            'iam_account_id'           => UserHelper::currentAccount()->id,
            'metadata'                 => [
                'subject' => $data['subject'],
                'cc'      => $data['cc'] ?? [],
            ],
        ]);

        self::deliver($message);

        return $message->fresh();
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
     * Gmail/Google Workspace channels are capped at 50 deliveries per day.
     * When the cap is reached the message is queued and rescheduled to a
     * random non-consecutive time slot on the next available day.
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

        // Enforce the 50-email/day cap for Gmail and Google Workspace channels.
        if (self::isGoogleChannel($channel)) {
            $sentToday = self::countDeliveredToday($channel);

            if ($sentToday >= 50) {
                self::scheduleForNextAvailableSlot($message, $channel);

                Log::info('[MessagesService::deliver] Gmail daily cap reached — message rescheduled', [
                    'message_id'  => $message->id,
                    'channel_id'  => $channel->id,
                    'sent_today'  => $sentToday,
                    'deliver_at'  => $message->fresh()->deliver_at,
                ]);

                return;
            }
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
                'cc'      => $message->metadata['cc'] ?? [],
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
     * Returns true when the channel is backed by a Google Workspace external service.
     * The link is: communication_channels.configuration['external_service_uuid']
     * → common_external_services.uuid where code = 'google_workspace'.
     */
    private static function isGoogleChannel(Channels $channel): bool
    {
        $externalServiceUuid = data_get($channel->configuration, 'external_service_uuid');

        if (!$externalServiceUuid) {
            return false;
        }

        return ExternalServices::where('uuid', $externalServiceUuid)
            ->where('code', 'google_workspace')
            ->exists();
    }

    /**
     * Counts messages successfully delivered through a channel today.
     * Uses delivered_at (set by markAsDelivered) as the authoritative timestamp.
     */
    private static function countDeliveredToday(Channels $channel): int
    {
        return Messages::where('communication_channel_id', $channel->id)
            ->whereIn('status', ['delivered', 'sent'])
            ->whereDate('delivered_at', now()->toDateString())
            ->count();
    }

    /**
     * Finds the next calendar day with fewer than 50 sent/scheduled messages for
     * the given channel, then picks a random time slot that is not within 15 minutes
     * of any already-scheduled message on that day. Updates the message record in
     * place with the computed deliver_at; status remains 'queued'.
     */
    private static function scheduleForNextAvailableSlot(Messages $message, Channels $channel): void
    {
        $dailyCap = 50;
        $date     = now()->addDay()->startOfDay();

        // Walk forward until we find a day below the cap.
        while (true) {
            $delivered = Messages::where('communication_channel_id', $channel->id)
                ->whereIn('status', ['delivered', 'sent'])
                ->whereDate('delivered_at', $date->toDateString())
                ->count();

            $queued = Messages::where('communication_channel_id', $channel->id)
                ->where('status', 'queued')
                ->whereDate('deliver_at', $date->toDateString())
                ->count();

            if ($delivered + $queued < $dailyCap) {
                break;
            }

            $date->addDay();
        }

        // Collect timestamps of already-queued messages on that day to avoid clustering.
        $occupied = Messages::where('communication_channel_id', $channel->id)
            ->where('status', 'queued')
            ->whereDate('deliver_at', $date->toDateString())
            ->whereNotNull('deliver_at')
            ->pluck('deliver_at')
            ->map(fn ($t) => Carbon::parse($t)->timestamp)
            ->sort()
            ->values()
            ->toArray();

        // Delivery window: 08:00–22:00 on the target day.
        $windowStart = $date->copy()->setTime(8, 0)->timestamp;
        $windowEnd   = $date->copy()->setTime(22, 0)->timestamp;

        $deliverAt = Carbon::createFromTimestamp(
            self::findRandomSlot($windowStart, $windowEnd, $occupied, minGapSeconds: 900)
        );

        Messages::where('id', $message->id)->update([
            'deliver_at' => $deliverAt,
            'status'     => 'queued',
        ]);
    }

    /**
     * Picks a random timestamp inside [windowStart, windowEnd] that is at least
     * minGapSeconds away from every occupied timestamp. Falls back to appending
     * after the last occupied slot if no free slot is found within maxAttempts.
     */
    private static function findRandomSlot(
        int $windowStart,
        int $windowEnd,
        array $occupied,
        int $minGapSeconds
    ): int {
        $maxAttempts = 200;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $candidate = rand($windowStart, $windowEnd);

            $free = true;
            foreach ($occupied as $ts) {
                if (abs($candidate - $ts) < $minGapSeconds) {
                    $free = false;
                    break;
                }
            }

            if ($free) {
                return $candidate;
            }
        }

        // Fallback: append after the last queued slot with a random extra gap.
        if (!empty($occupied)) {
            $after = max($occupied) + $minGapSeconds + rand(0, $minGapSeconds);
            return min($after, $windowEnd);
        }

        return rand($windowStart, $windowEnd);
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