<?php

namespace NextDeveloper\Communication\Http\Requests\Channels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class FacebookConnectRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'page_id'            => 'required|string',
            'page_access_token'  => 'required|string',
            'name'               => 'nullable|string',
        ];
    }
}
