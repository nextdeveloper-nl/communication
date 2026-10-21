<?php

namespace NextDeveloper\Communication\Http\Requests\Channels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WhatsAppEmbeddedSignupRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            // One-time authorization code that FB.login returns (authResponse.code); exchanged for a token on the server
            'code'            => 'required|string',
            // From the WA_EMBEDDED_SIGNUP "FINISH" window message of the Embedded Signup popup
            'phone_number_id' => 'required|string',
            'waba_id'         => 'required|string',
            'name'            => 'nullable|string',
            // Optional: with a Flow pipeline every customer session becomes a CRM opportunity in it
            'flow_pipeline_id'     => 'nullable|uuid',
            'flow_stage_name'      => 'nullable|string',
            'opportunity_type'     => 'nullable|string',
            'default_country_code' => 'nullable|string',
            'tags'                 => 'nullable|array',
            'tags.*'               => 'string|max:100',
        ];
    }
}
