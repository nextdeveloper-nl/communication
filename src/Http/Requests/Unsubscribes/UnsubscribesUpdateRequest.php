<?php

namespace NextDeveloper\Communication\Http\Requests\Unsubscribes;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class UnsubscribesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_contact_id' => 'nullable|exists:communication_contacts,uuid|uuid',
        'channel_type' => 'nullable|string',
        'identifier' => 'nullable|string',
        'reason' => 'nullable|string',
        'source' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}