<?php

namespace NextDeveloper\Communication\Http\Requests\Contacts;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContactsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => 'nullable|string',
        'notes' => 'nullable|string',
        'tags' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}