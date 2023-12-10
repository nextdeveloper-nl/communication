<?php

namespace NextDeveloper\Communication\Http\Requests\Emails;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class EmailsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'email_address'      => 'nullable|string|max:500',
        'recipient_name'     => 'nullable|string|max:500',
        'subject'            => 'nullable|string|max:500',
        'body'               => 'nullable|string',
        'delivery_results'   => 'nullable',
        'is_marketing_email' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}