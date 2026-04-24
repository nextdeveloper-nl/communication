<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Services\AbstractServices\AbstractMessagesService;

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

        if ($hasThread === $hasCampaign) {
            throw new InvalidArgumentException(
                'A message must belong to exactly one of: communication_thread_id or crm_campaign_id.'
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
}