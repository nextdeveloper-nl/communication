<?php

namespace NextDeveloper\Communication\Http\Requests\MessageEvents;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class MessageEventsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_message_id' => 'required|exists:communication_messages,uuid|uuid',
        'event_type' => 'required|string',
        'metadata' => 'nullable',
        'occurred_at' => 'required|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}