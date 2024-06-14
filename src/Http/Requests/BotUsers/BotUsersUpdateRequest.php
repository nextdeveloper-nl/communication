<?php

namespace NextDeveloper\Communication\Http\Requests\BotUsers;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class BotUsersUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'chat_id' => 'nullable|exists:chats,uuid|uuid',
        'bot' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}