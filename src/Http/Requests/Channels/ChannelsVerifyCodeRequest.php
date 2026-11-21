<?php

namespace NextDeveloper\Communication\Http\Requests\Channels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ChannelsVerifyCodeRequest extends AbstractFormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The verification code is required.',
            'code.digits' => 'The verification code has 6 digits.',
        ];
    }
}
