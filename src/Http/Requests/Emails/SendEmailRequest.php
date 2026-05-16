<?php

namespace NextDeveloper\Communication\Http\Requests\Emails;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class SendEmailRequest extends AbstractFormRequest
{
    public function rules(): array
    {
        return [
            'subject'                  => 'required|string',
            'body'                     => 'required|string',
            'communication_channel_id' => 'required|exists:communication_channels,uuid|uuid',
            'recipient'                => 'required|email',
            'cc'                       => 'nullable|array',
            'cc.*'                     => 'email',
        ];
    }
}
