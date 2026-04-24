<?php

namespace NextDeveloper\Communication\Http\Requests\Threads;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ThreadsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => 'nullable|string',
        'status' => 'string',
        'communication_channel_id' => 'required|exists:communication_channels,uuid|uuid',
        'communication_contact_id' => 'required|exists:communication_contacts,uuid|uuid',
        'communication_bot_id' => 'nullable|exists:communication_bots,uuid|uuid',
        'assigned_to_user_id' => 'nullable|exists:assigned_to_users,uuid|uuid',
        'assigned_at' => 'nullable|date',
        'resolved_at' => 'nullable|date',
        'last_message_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}