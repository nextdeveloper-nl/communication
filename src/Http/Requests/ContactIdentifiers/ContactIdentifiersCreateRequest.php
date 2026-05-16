<?php

namespace NextDeveloper\Communication\Http\Requests\ContactIdentifiers;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContactIdentifiersCreateRequest extends AbstractFormRequest
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
        'is_primary' => 'boolean',
        'is_suppressed' => 'boolean',
        'suppressed_at' => 'nullable|date',
        'suppressed_reason' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}