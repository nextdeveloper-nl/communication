<?php

namespace NextDeveloper\Communication\Http\Requests\Emails;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class EmailsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'email_address'      => 'required|string|max:500',
        'recipient_name'     => 'required|string|max:500',
        'subject'            => 'required|string|max:500',
        'body'               => 'required|string',
        'delivery_results'   => 'nullable',
        'is_marketing_email' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}