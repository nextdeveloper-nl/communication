<?php

namespace NextDeveloper\Communication\Http\Requests\Channels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ChannelsTestRequest extends AbstractFormRequest
{
    public function rules(): array
    {
        return [
            // Email address, phone number or chat id depending on the channel type; the current user's own when empty
            'recipient' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'recipient.string' => 'The recipient must be an email address, a phone number or a chat id.',
            'recipient.max' => 'The recipient may not be longer than 255 characters.',
        ];
    }
}
