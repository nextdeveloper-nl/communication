<?php

namespace NextDeveloper\Communication\Http\Requests\Unsubscribes;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class UnsubscribesCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_contact_id' => 'required|exists:communication_contacts,uuid|uuid',
        'channel_type' => 'required|string',
        'identifier' => 'required|string',
        'reason' => 'nullable|string',
        'source' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}