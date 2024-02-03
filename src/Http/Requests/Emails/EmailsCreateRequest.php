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
            'from_email_address' => 'required|string',
        'subject' => 'required|string',
        'body' => 'required|string',
        'delivery_results' => 'nullable',
        'is_marketing_email' => 'boolean',
        'deliver_at' => 'nullable|date',
        'delivered_at' => 'nullable|date',
        'to' => 'nullable',
        'cc' => 'nullable',
        'bcc' => 'nullable',
        'attachments' => 'nullable',
        'headers' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}