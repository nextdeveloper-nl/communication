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
        'to' => 'required',
        'cc' => 'nullable',
        'bcc' => 'nullable',
        'subject' => 'required|string',
        'body' => 'required|string',
        'attachments' => 'nullable',
        'headers' => 'nullable',
        'delivery_results' => 'nullable',
        'is_marketing_email' => 'boolean',
        'deliver_at' => 'nullable|date',
        'delivered_at' => 'nullable|date',
        'communication_channel_id' => 'nullable|exists:communication_channels,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}