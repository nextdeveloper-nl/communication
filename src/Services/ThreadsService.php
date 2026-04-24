<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Services\AbstractServices\AbstractThreadsService;
use NextDeveloper\Commons\Helpers\DatabaseHelper;

/**
 * This class is responsible from managing the data for Threads
 *
 * Class ThreadsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class ThreadsService extends AbstractThreadsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Marks a thread as resolved.
     */
    public static function resolve(string $ref): Threads
    {
        return self::update($ref, [
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Reopens a resolved thread.
     */
    public static function reopen(string $ref): Threads
    {
        return self::update($ref, [
            'status'      => 'open',
            'resolved_at' => null,
        ]);
    }

    /**
     * Assigns a thread to a team member, records the timestamp, and writes the assignment history.
     */
    public static function assign(string $ref, string $toUserUuid, string $byUserUuid): Threads
    {
        $userId = DatabaseHelper::uuidToId('\NextDeveloper\IAM\Database\Models\Users', $toUserUuid);

        $thread = self::update($ref, [
            'assigned_to_user_id' => $userId,
            'assigned_at'         => now(),
        ]);

        ThreadAssignmentsService::record($thread, $toUserUuid, $byUserUuid);

        return $thread;
    }

    /**
     * Returns open threads for an account ordered by most recent activity.
     */
    public static function getOpenThreads(): Collection
    {
        return Threads::where('status', 'open')
            ->orderByDesc('last_message_at')
            ->get();
    }

    /**
     * Bumps last_message_at to now. Called by MessagesService after each new message.
     */
    public static function touchLastMessageAt(Threads $thread): void
    {
        self::update($thread->uuid, ['last_message_at' => now()]);
    }
}