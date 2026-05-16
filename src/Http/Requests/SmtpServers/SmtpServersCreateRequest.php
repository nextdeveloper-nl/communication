<?php

namespace NextDeveloper\Communication\Http\Requests\SmtpServers;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class SmtpServersCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_channel_id' => 'required|exists:communication_channels,uuid|uuid',
        'name' => 'required|string',
        'host' => 'required|string',
        'port' => 'required|integer',
        'encryption' => 'required|string',
        'username' => 'required|string',
        'password' => 'required|string',
        'from_email' => 'required|string',
        'from_name' => 'nullable|string',
        'reply_to' => 'nullable|string',
        'is_verified' => 'boolean',
        'verified_at' => 'nullable|date',
        'last_checked_at' => 'nullable|date',
        'last_check_status' => 'nullable|string',
        'last_check_message' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}