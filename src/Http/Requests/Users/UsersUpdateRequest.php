<?php

namespace NextDeveloper\Communication\Http\Requests\Users;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class UsersUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'telegram_id' => 'nullable|string|exists:telegrams,uuid|uuid',
        'ai_session_id' => 'nullable|string|exists:ai_sessions,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}