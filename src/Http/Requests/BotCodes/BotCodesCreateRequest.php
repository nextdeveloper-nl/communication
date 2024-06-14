<?php

namespace NextDeveloper\Communication\Http\Requests\BotCodes;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class BotCodesCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|integer',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}