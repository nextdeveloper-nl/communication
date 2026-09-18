<?php

namespace NextDeveloper\Communication\Http\Requests\Notifications;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class NotificationsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'severity' => 'string',
        'object_id' => 'nullable|uuid|required_with:object_type',
        'object_type' => 'nullable|string|required_with:object_id',
        'data' => 'nullable',
        'read_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}