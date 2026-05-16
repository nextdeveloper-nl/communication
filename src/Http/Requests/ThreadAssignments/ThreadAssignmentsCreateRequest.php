<?php

namespace NextDeveloper\Communication\Http\Requests\ThreadAssignments;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ThreadAssignmentsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_thread_id' => 'required|exists:communication_threads,uuid|uuid',
        'assigned_to_user_id' => 'nullable|exists:iam_users,uuid|uuid',
        'assigned_by_user_id' => 'nullable|exists:iam_users,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}