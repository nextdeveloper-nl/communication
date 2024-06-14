<?php

namespace NextDeveloper\Communication\Http\Requests\Conversations;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ConversationsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_bot_id' => 'nullable|exists:communication_bots,uuid|uuid',
        'message' => 'nullable|string',
        'direction' => 'integer',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}