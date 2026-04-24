<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ThreadsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function subject($value)
    {
        return $this->builder->where('subject', 'ilike', '%' . $value . '%');
    }

        
    public function status($value)
    {
        return $this->builder->where('status', 'ilike', '%' . $value . '%');
    }

    
    public function assignedAtStart($date)
    {
        return $this->builder->where('assigned_at', '>=', $date);
    }

    public function assignedAtEnd($date)
    {
        return $this->builder->where('assigned_at', '<=', $date);
    }

    //  This is an alias function of assignedAt
    public function assigned_at_start($value)
    {
        return $this->assignedAtStart($value);
    }

    //  This is an alias function of assignedAt
    public function assigned_at_end($value)
    {
        return $this->assignedAtEnd($value);
    }

    public function resolvedAtStart($date)
    {
        return $this->builder->where('resolved_at', '>=', $date);
    }

    public function resolvedAtEnd($date)
    {
        return $this->builder->where('resolved_at', '<=', $date);
    }

    //  This is an alias function of resolvedAt
    public function resolved_at_start($value)
    {
        return $this->resolvedAtStart($value);
    }

    //  This is an alias function of resolvedAt
    public function resolved_at_end($value)
    {
        return $this->resolvedAtEnd($value);
    }

    public function lastMessageAtStart($date)
    {
        return $this->builder->where('last_message_at', '>=', $date);
    }

    public function lastMessageAtEnd($date)
    {
        return $this->builder->where('last_message_at', '<=', $date);
    }

    //  This is an alias function of lastMessageAt
    public function last_message_at_start($value)
    {
        return $this->lastMessageAtStart($value);
    }

    //  This is an alias function of lastMessageAt
    public function last_message_at_end($value)
    {
        return $this->lastMessageAtEnd($value);
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

    public function communicationChannelId($value)
    {
            $communicationChannel = \NextDeveloper\Communication\Database\Models\Channels::where('uuid', $value)->first();

        if($communicationChannel) {
            return $this->builder->where('communication_channel_id', '=', $communicationChannel->id);
        }
    }

        //  This is an alias function of communicationChannel
    public function communication_channel_id($value)
    {
        return $this->communicationChannel($value);
    }
    
    public function communicationContactId($value)
    {
            $communicationContact = \NextDeveloper\Communication\Database\Models\Contacts::where('uuid', $value)->first();

        if($communicationContact) {
            return $this->builder->where('communication_contact_id', '=', $communicationContact->id);
        }
    }

        //  This is an alias function of communicationContact
    public function communication_contact_id($value)
    {
        return $this->communicationContact($value);
    }
    
    public function communicationBotId($value)
    {
            $communicationBot = \NextDeveloper\Communication\Database\Models\Bots::where('uuid', $value)->first();

        if($communicationBot) {
            return $this->builder->where('communication_bot_id', '=', $communicationBot->id);
        }
    }

        //  This is an alias function of communicationBot
    public function communication_bot_id($value)
    {
        return $this->communicationBot($value);
    }
    
    public function assignedToUserId($value)
    {
            $assignedToUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($assignedToUser) {
            return $this->builder->where('assigned_to_user_id', '=', $assignedToUser->id);
        }
    }

        //  This is an alias function of assignedToUser
    public function assigned_to_user_id($value)
    {
        return $this->assignedToUser($value);
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
