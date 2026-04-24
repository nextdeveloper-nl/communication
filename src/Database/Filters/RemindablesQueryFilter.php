<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class RemindablesQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function objectType($value)
    {
        return $this->builder->where('object_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of objectType
    public function object_type($value)
    {
        return $this->objectType($value);
    }
        
    public function note($value)
    {
        return $this->builder->where('note', 'ilike', '%' . $value . '%');
    }

    
    public function isReminded($value)
    {
        return $this->builder->where('is_reminded', $value);
    }

        //  This is an alias function of isReminded
    public function is_reminded($value)
    {
        return $this->isReminded($value);
    }
     
    public function isAcknowledged($value)
    {
        return $this->builder->where('is_acknowledged', $value);
    }

        //  This is an alias function of isAcknowledged
    public function is_acknowledged($value)
    {
        return $this->isAcknowledged($value);
    }
     
    public function isCancelled($value)
    {
        return $this->builder->where('is_cancelled', $value);
    }

        //  This is an alias function of isCancelled
    public function is_cancelled($value)
    {
        return $this->isCancelled($value);
    }
     
    public function remindDatetimeStart($date)
    {
        return $this->builder->where('remind_datetime', '>=', $date);
    }

    public function remindDatetimeEnd($date)
    {
        return $this->builder->where('remind_datetime', '<=', $date);
    }

    //  This is an alias function of remindDatetime
    public function remind_datetime_start($value)
    {
        return $this->remindDatetimeStart($value);
    }

    //  This is an alias function of remindDatetime
    public function remind_datetime_end($value)
    {
        return $this->remindDatetimeEnd($value);
    }

    public function snoozeDatetimeStart($date)
    {
        return $this->builder->where('snooze_datetime', '>=', $date);
    }

    public function snoozeDatetimeEnd($date)
    {
        return $this->builder->where('snooze_datetime', '<=', $date);
    }

    //  This is an alias function of snoozeDatetime
    public function snooze_datetime_start($value)
    {
        return $this->snoozeDatetimeStart($value);
    }

    //  This is an alias function of snoozeDatetime
    public function snooze_datetime_end($value)
    {
        return $this->snoozeDatetimeEnd($value);
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

    public function iamUserId($value)
    {
            $iamUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($iamUser) {
            return $this->builder->where('iam_user_id', '=', $iamUser->id);
        }
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
