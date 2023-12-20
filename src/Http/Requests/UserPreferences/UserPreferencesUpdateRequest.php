<?php

namespace NextDeveloper\Communication\Http\Requests\UserPreferences;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class UserPreferencesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'is_system_email_optout'    => 'boolean',
        'is_phone_optout'           => 'boolean',
        'is_marketing_email_optout' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}