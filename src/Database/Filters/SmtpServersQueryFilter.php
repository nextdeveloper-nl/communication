<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class SmtpServersQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function name($value)
    {
        return $this->builder->where('name', 'ilike', '%' . $value . '%');
    }

        
    public function host($value)
    {
        return $this->builder->where('host', 'ilike', '%' . $value . '%');
    }

        
    public function encryption($value)
    {
        return $this->builder->where('encryption', 'ilike', '%' . $value . '%');
    }

        
    public function username($value)
    {
        return $this->builder->where('username', 'ilike', '%' . $value . '%');
    }

        
    public function password($value)
    {
        return $this->builder->where('password', 'ilike', '%' . $value . '%');
    }

        
    public function fromEmail($value)
    {
        return $this->builder->where('from_email', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of fromEmail
    public function from_email($value)
    {
        return $this->fromEmail($value);
    }
        
    public function fromName($value)
    {
        return $this->builder->where('from_name', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of fromName
    public function from_name($value)
    {
        return $this->fromName($value);
    }
        
    public function replyTo($value)
    {
        return $this->builder->where('reply_to', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of replyTo
    public function reply_to($value)
    {
        return $this->replyTo($value);
    }
        
    public function lastCheckStatus($value)
    {
        return $this->builder->where('last_check_status', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of lastCheckStatus
    public function last_check_status($value)
    {
        return $this->lastCheckStatus($value);
    }
        
    public function lastCheckMessage($value)
    {
        return $this->builder->where('last_check_message', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of lastCheckMessage
    public function last_check_message($value)
    {
        return $this->lastCheckMessage($value);
    }
    
    public function port($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('port', $operator, $value);
    }

    
    public function isVerified($value)
    {
        return $this->builder->where('is_verified', $value);
    }

        //  This is an alias function of isVerified
    public function is_verified($value)
    {
        return $this->isVerified($value);
    }
     
    public function verifiedAtStart($date)
    {
        return $this->builder->where('verified_at', '>=', $date);
    }

    public function verifiedAtEnd($date)
    {
        return $this->builder->where('verified_at', '<=', $date);
    }

    //  This is an alias function of verifiedAt
    public function verified_at_start($value)
    {
        return $this->verifiedAtStart($value);
    }

    //  This is an alias function of verifiedAt
    public function verified_at_end($value)
    {
        return $this->verifiedAtEnd($value);
    }

    public function lastCheckedAtStart($date)
    {
        return $this->builder->where('last_checked_at', '>=', $date);
    }

    public function lastCheckedAtEnd($date)
    {
        return $this->builder->where('last_checked_at', '<=', $date);
    }

    //  This is an alias function of lastCheckedAt
    public function last_checked_at_start($value)
    {
        return $this->lastCheckedAtStart($value);
    }

    //  This is an alias function of lastCheckedAt
    public function last_checked_at_end($value)
    {
        return $this->lastCheckedAtEnd($value);
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
