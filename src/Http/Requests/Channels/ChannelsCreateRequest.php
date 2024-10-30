<?php

namespace NextDeveloper\Communication\Http\Requests\Channels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ChannelsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'config' => 'required',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'communication_available_channel_id' => 'nullable|exists:communication_available_channels,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}