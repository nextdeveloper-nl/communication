<?php

namespace NextDeveloper\Communication\Http\Requests\Accounts;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'plan' => 'string',
        'max_contacts' => 'nullable|integer',
        'max_emails_per_month' => 'nullable|integer',
        'max_sms_per_month' => 'nullable|integer',
        'max_channels' => 'nullable|integer',
        'emails_sent_this_period' => 'integer',
        'sms_sent_this_period' => 'integer',
        'current_period_start' => 'nullable|date',
        'current_period_end' => 'nullable|date',
        'is_suspended' => 'boolean',
        'suspension_reason' => 'nullable|string',
        'reputation_score' => 'nullable',
        'enabled_channel_types' => 'nullable',
        'is_ai_bots_enabled' => 'boolean',
        'is_dedicated_ip_enabled' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}