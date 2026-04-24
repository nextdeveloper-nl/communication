<?php

namespace NextDeveloper\Communication\Http\Requests\AvailableChannels;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AvailableChannelsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string',
        'class' => 'nullable|string',
        'parameters' => 'nullable',
        'config' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}