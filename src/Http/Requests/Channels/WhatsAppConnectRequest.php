<?php

namespace NextDeveloper\Communication\Http\Requests\Channels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WhatsAppConnectRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            // Cloud API id of the number (WhatsApp Manager > Phone numbers), not the phone number itself
            'phone_number_id' => 'required|string',
            // WhatsApp Business Account id the number belongs to
            'waba_id'         => 'required|string',
            // Permanent / system user token with whatsapp_business_management + whatsapp_business_messaging
            'access_token'    => 'required|string',
            'name'            => 'nullable|string',
            // Optional: with a Flow pipeline every inbound message becomes a CRM opportunity in it
            'flow_pipeline_id'     => 'nullable|uuid',
            'flow_stage_name'      => 'nullable|string',
            'opportunity_type'     => 'nullable|string',
            'default_country_code' => 'nullable|string',
            'tags'                 => 'nullable|array',
            'tags.*'               => 'string|max:100',
        ];
    }
}
