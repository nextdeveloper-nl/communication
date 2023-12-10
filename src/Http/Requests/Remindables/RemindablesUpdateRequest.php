<?php

namespace NextDeveloper\Communication\Http\Requests\Remindables;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class RemindablesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'remindable_id'          => 'nullable|exists:remindables,uuid|uuid',
        'remindable_object_type' => 'nullable|string|max:191',
        'remind_datetime'        => 'nullable|date',
        'snooze_datetime'        => 'nullable|date',
        'note'                   => 'nullable|string',
        'status'                 => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}