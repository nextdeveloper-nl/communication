<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Communication\Database\Models\ThreadAssignments;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Services\AbstractServices\AbstractThreadAssignmentsService;
use NextDeveloper\Commons\Helpers\DatabaseHelper;

/**
 * This class is responsible from managing the data for ThreadAssignments
 *
 * Class ThreadAssignmentsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class ThreadAssignmentsService extends AbstractThreadAssignmentsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Records an assignment history entry. Called by ThreadsService::assign() — do not call directly.
     */
    public static function record(Threads $thread, string $toUserUuid, string $byUserUuid): ThreadAssignments
    {
        $toUserId = DatabaseHelper::uuidToId('\NextDeveloper\IAM\Database\Models\Users', $toUserUuid);
        $byUserId = DatabaseHelper::uuidToId('\NextDeveloper\IAM\Database\Models\Users', $byUserUuid);

        return parent::create([
            'communication_thread_id' => $thread->uuid,
            'assigned_to_user_id'     => $toUserId,
            'assigned_by_user_id'     => $byUserId,
        ]);
    }
}