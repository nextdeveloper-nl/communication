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
            // Optional lead import settings used when the importer for this Page is created
            'flow_pipeline_id'     => 'nullable|uuid',
            'flow_stage_name'      => 'nullable|string',
            'opportunity_type'     => 'nullable|string',
            'default_country_code' => 'nullable|string',
        ];
    }
}
