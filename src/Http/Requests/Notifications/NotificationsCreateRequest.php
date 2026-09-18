<?php

namespace NextDeveloper\Communication\Http\Requests\Notifications;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class NotificationsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'severity' => 'string|in:info,warning,error',
        'object_id' => 'required|uuid',
        'object_type' => 'required|string',
        'data' => 'required',
        'iam_user_id' => 'nullable|uuid',
        'read_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}