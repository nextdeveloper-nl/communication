<?php

namespace NextDeveloper\Communication\Http\Requests\Messages;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class MessagesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'communication_thread_id' => 'nullable|exists:communication_threads,uuid|uuid',
        'crm_campaign_id' => 'nullable|exists:crm_campaigns,uuid|uuid',
        'direction' => 'nullable|integer',
        'content_type' => 'string',
        'body' => 'nullable|string',
        'attachments' => 'nullable',
        'sent_by_user_id' => 'nullable|exists:sent_by_users,uuid|uuid',
        'sent_by_bot_id' => 'nullable|exists:sent_by_bots,uuid|uuid',
        'reply_to_id' => 'nullable|exists:reply_tos,uuid|uuid',
        'external_message_id' => 'nullable|string|exists:external_messages,uuid|uuid',
        'status' => 'string',
        'deliver_at' => 'nullable|date',
        'delivered_at' => 'nullable|date',
        'read_at' => 'nullable|date',
        'failed_at' => 'nullable|date',
        'failure_reason' => 'nullable|string',
        'is_internal' => 'boolean',
        'metadata' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}