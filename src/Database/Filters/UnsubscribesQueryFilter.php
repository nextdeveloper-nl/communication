<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class UnsubscribesQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function channelType($value)
    {
        return $this->builder->where('channel_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of channelType
    public function channel_type($value)
    {
        return $this->channelType($value);
    }
        
    public function identifier($value)
    {
        return $this->builder->where('identifier', 'ilike', '%' . $value . '%');
    }

        
    public function reason($value)
    {
        return $this->builder->where('reason', 'ilike', '%' . $value . '%');
    }

        
    public function source($value)
    {
        return $this->builder->where('source', 'ilike', '%' . $value . '%');
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
