<?php

namespace NextDeveloper\Communication\Http\Requests\BotCodes;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class BotCodesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'nullable|integer',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}