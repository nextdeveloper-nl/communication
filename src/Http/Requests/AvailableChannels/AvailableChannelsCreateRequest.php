<?php

namespace NextDeveloper\Communication\Http\Requests\AvailableChannels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AvailableChannelsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
        'class' => 'required|string',
        'config' => 'required',
        'parameters' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}