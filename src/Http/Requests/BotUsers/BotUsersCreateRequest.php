<?php

namespace NextDeveloper\Communication\Http\Requests\BotUsers;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class BotUsersCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'chat_id' => 'required|exists:chats,uuid|uuid',
        'bot' => 'required',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}