<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class AccountsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function plan($value)
    {
        return $this->builder->where('plan', 'ilike', '%' . $value . '%');
    }

        
    public function suspensionReason($value)
    {
        return $this->builder->where('suspension_reason', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of suspensionReason
    public function suspension_reason($value)
    {
        return $this->suspensionReason($value);
    }
    
    public function maxContacts($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('max_contacts', $operator, $value);
    }

        //  This is an alias function of maxContacts
    public function max_contacts($value)
    {
        return $this->maxContacts($value);
    }
    
    public function maxEmailsPerMonth($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('max_emails_per_month', $operator, $value);
    }

        //  This is an alias function of maxEmailsPerMonth
    public function max_emails_per_month($value)
    {
        return $this->maxEmailsPerMonth($value);
    }
    
    public function maxSmsPerMonth($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('max_sms_per_month', $operator, $value);
    }

        //  This is an alias function of maxSmsPerMonth
    public function max_sms_per_month($value)
    {
        return $this->maxSmsPerMonth($value);
    }
    
    public function maxChannels($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('max_channels', $operator, $value);
    }

        //  This is an alias function of maxChannels
    public function max_channels($value)
    {
        return $this->maxChannels($value);
    }
    
    public function emailsSentThisPeriod($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('emails_sent_this_period', $operator, $value);
    }

        //  This is an alias function of emailsSentThisPeriod
    public function emails_sent_this_period($value)
    {
        return $this->emailsSentThisPeriod($value);
    }
    
    public function smsSentThisPeriod($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('sms_sent_this_period', $operator, $value);
    }

        //  This is an alias function of smsSentThisPeriod
    public function sms_sent_this_period($value)
    {
        return $this->smsSentThisPeriod($value);
    }
    
    public function isSuspended($value)
    {
        return $this->builder->where('is_suspended', $value);
    }

        //  This is an alias function of isSuspended
    public function is_suspended($value)
    {
        return $this->isSuspended($value);
    }
     
    public function isAiBotsEnabled($value)
    {
        return $this->builder->where('is_ai_bots_enabled', $value);
    }

        //  This is an alias function of isAiBotsEnabled
    public function is_ai_bots_enabled($value)
    {
        return $this->isAiBotsEnabled($value);
    }
     
    public function isDedicatedIpEnabled($value)
    {
        return $this->builder->where('is_dedicated_ip_enabled', $value);
    }

        //  This is an alias function of isDedicatedIpEnabled
    public function is_dedicated_ip_enabled($value)
    {
        return $this->isDedicatedIpEnabled($value);
    }
     
    public function currentPeriodStartStart($date)
    {
        return $this->builder->where('current_period_start', '>=', $date);
    }

    public function currentPeriodStartEnd($date)
    {
        return $this->builder->where('current_period_start', '<=', $date);
    }

    //  This is an alias function of currentPeriodStart
    public function current_period_start_start($value)
    {
        return $this->currentPeriodStartStart($value);
    }

    //  This is an alias function of currentPeriodStart
    public function current_period_start_end($value)
    {
        return $this->currentPeriodStartEnd($value);
    }

    public function currentPeriodEndStart($date)
    {
        return $this->builder->where('current_period_end', '>=', $date);
    }

    public function currentPeriodEndEnd($date)
    {
        return $this->builder->where('current_period_end', '<=', $date);
    }

    //  This is an alias function of currentPeriodEnd
    public function current_period_end_start($value)
    {
        return $this->currentPeriodEndStart($value);
    }

    //  This is an alias function of currentPeriodEnd
    public function current_period_end_end($value)
    {
        return $this->currentPeriodEndEnd($value);
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

    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
