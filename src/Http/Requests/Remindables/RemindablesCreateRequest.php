<?php

namespace NextDeveloper\Communication\Http\Requests\Remindables;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class RemindablesCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_id' => 'required',
        'object_type' => 'required|string',
        'remind_datetime' => 'nullable|date',
        'snooze_datetime' => 'nullable|date',
        'note' => 'nullable|string',
        'is_reminded' => 'boolean',
        'is_acknowledged' => 'boolean',
        'is_cancelled' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}