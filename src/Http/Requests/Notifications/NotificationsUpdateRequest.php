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
            'type'            => 'nullable',
        'notifiable_id'   => 'nullable|exists:notifiables,uuid|uuid',
        'notifiable_type' => 'nullable|string|max:255',
        'data'            => 'nullable|string',
        'read_at'         => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}