<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;


/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class MessagesQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function contentType($value)
    {
        return $this->builder->where('content_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of contentType
    public function content_type($value)
    {
        return $this->contentType($value);
    }

    public function body($value)
    {
        return $this->builder->where('body', 'ilike', '%' . $value . '%');
    }


    public function externalMessageId($value)
    {
        return $this->builder->where('external_message_id', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of externalMessageId
    public function external_message_id($value)
    {
        return $this->externalMessageId($value);
    }

    public function status($value)
    {
        return $this->builder->where('status', 'ilike', '%' . $value . '%');
    }


    public function failureReason($value)
    {
        return $this->builder->where('failure_reason', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of failureReason
    public function failure_reason($value)
    {
        return $this->failureReason($value);
    }

    public function direction($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('direction', $operator, $value);
    }


    public function isInternal($value)
    {
        return $this->builder->where('is_internal', $value);
    }

        //  This is an alias function of isInternal
    public function is_internal($value)
    {
        return $this->isInternal($value);
    }

    public function deliverAtStart($date)
    {
        return $this->builder->where('deliver_at', '>=', $date);
    }

    public function deliverAtEnd($date)
    {
        return $this->builder->where('deliver_at', '<=', $date);
    }

    //  This is an alias function of deliverAt
    public function deliver_at_start($value)
    {
        return $this->deliverAtStart($value);
    }

    //  This is an alias function of deliverAt
    public function deliver_at_end($value)
    {
        return $this->deliverAtEnd($value);
    }

    public function deliveredAtStart($date)
    {
        return $this->builder->where('delivered_at', '>=', $date);
    }

    public function deliveredAtEnd($date)
    {
        return $this->builder->where('delivered_at', '<=', $date);
    }

    //  This is an alias function of deliveredAt
    public function delivered_at_start($value)
    {
        return $this->deliveredAtStart($value);
    }

    //  This is an alias function of deliveredAt
    public function delivered_at_end($value)
    {
        return $this->deliveredAtEnd($value);
    }

    public function readAtStart($date)
    {
        return $this->builder->where('read_at', '>=', $date);
    }

    public function readAtEnd($date)
    {
        return $this->builder->where('read_at', '<=', $date);
    }

    //  This is an alias function of readAt
    public function read_at_start($value)
    {
        return $this->readAtStart($value);
    }

    //  This is an alias function of readAt
    public function read_at_end($value)
    {
        return $this->readAtEnd($value);
    }

    public function failedAtStart($date)
    {
        return $this->builder->where('failed_at', '>=', $date);
    }

    public function failedAtEnd($date)
    {
        return $this->builder->where('failed_at', '<=', $date);
    }

    //  This is an alias function of failedAt
    public function failed_at_start($value)
    {
        return $this->failedAtStart($value);
    }

    //  This is an alias function of failedAt
    public function failed_at_end($value)
    {
        return $this->failedAtEnd($value);
    }

    public function createdAtStart($date)
    {
        return $this->builder->where('created_at', '>=', $date);
    }

    public function createdAtEnd($date)
    {
        return $this->builder->where('created_at', '<=', $date);
    }

    //  This is an alias function of createdAt
    public function created_at_start($value)
    {
        return $this->createdAtStart($value);
    }

    //  This is an alias function of createdAt
    public function created_at_end($value)
    {
        return $this->createdAtEnd($value);
    }

    public function updatedAtStart($date)
    {
        return $this->builder->where('updated_at', '>=', $date);
    }

    public function updatedAtEnd($date)
    {
        return $this->builder->where('updated_at', '<=', $date);
    }

    //  This is an alias function of updatedAt
    public function updated_at_start($value)
    {
        return $this->updatedAtStart($value);
    }

    //  This is an alias function of updatedAt
    public function updated_at_end($value)
    {
        return $this->updatedAtEnd($value);
    }

    public function deletedAtStart($date)
    {
        return $this->builder->where('deleted_at', '>=', $date);
    }

    public function deletedAtEnd($date)
    {
        return $this->builder->where('deleted_at', '<=', $date);
    }

    //  This is an alias function of deletedAt
    public function deleted_at_start($value)
    {
        return $this->deletedAtStart($value);
    }

    //  This is an alias function of deletedAt
    public function deleted_at_end($value)
    {
        return $this->deletedAtEnd($value);
    }

    public function communicationThreadId($value)
    {
            $communicationThread = \NextDeveloper\Communication\Database\Models\Threads::where('uuid', $value)->first();

        if($communicationThread) {
            return $this->builder->where('communication_thread_id', '=', $communicationThread->id);
        }
    }

        //  This is an alias function of communicationThread
    public function communication_thread_id($value)
    {
        return $this->communicationThread($value);
    }

    public function crmCampaignId($value)
    {
            $crmCampaign = \NextDeveloper\CRM\Database\Models\Campaigns::where('uuid', $value)->first();

        if($crmCampaign) {
            return $this->builder->where('crm_campaign_id', '=', $crmCampaign->id);
        }
    }

        //  This is an alias function of crmCampaign
    public function crm_campaign_id($value)
    {
        return $this->crmCampaign($value);
    }

    public function sentByUserId($value)
    {
            $sentByUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($sentByUser) {
            return $this->builder->where('sent_by_user_id', '=', $sentByUser->id);
        }
    }

        //  This is an alias function of sentByUser
    public function sent_by_user_id($value)
    {
        return $this->sentByUser($value);
    }

    public function sentByBotId($value)
    {
            $sentByBot = \NextDeveloper\Communication\Database\Models\Bots::where('uuid', $value)->first();

        if($sentByBot) {
            return $this->builder->where('sent_by_bot_id', '=', $sentByBot->id);
        }
    }

        //  This is an alias function of sentByBot
    public function sent_by_bot_id($value)
    {
        return $this->sentByBot($value);
    }

    public function replyToId($value)
    {
            return $this->builder->where('reply_to_id', '=', $value);
    }

        //  This is an alias function of replyTo
    public function reply_to_id($value)
    {
        return $this->replyTo($value);
    }

    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }


    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
