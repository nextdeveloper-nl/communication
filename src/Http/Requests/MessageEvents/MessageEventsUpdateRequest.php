<?php

namespace NextDeveloper\Communication\Http\Requests\MessageEvents;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class MessageEventsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_message_id' => 'nullable|exists:communication_messages,uuid|uuid',
        'event_type' => 'nullable|string',
        'metadata' => 'nullable',
        'occurred_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}