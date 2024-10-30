<?php

namespace NextDeveloper\Communication\Http\Requests\Bots;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class BotsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string',
        'description' => 'nullable|string',
        'token' => 'nullable|string',
        'class' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}