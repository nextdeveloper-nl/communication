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
            'severity' => 'string',
        'object_id' => 'required',
        'object_type' => 'required|string',
        'data' => 'required|string',
        'read_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}