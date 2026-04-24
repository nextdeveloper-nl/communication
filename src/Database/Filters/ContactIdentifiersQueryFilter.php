<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ContactIdentifiersQueryFilter extends AbstractQueryFilter
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

        
    public function suppressedReason($value)
    {
        return $this->builder->where('suppressed_reason', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of suppressedReason
    public function suppressed_reason($value)
    {
        return $this->suppressedReason($value);
    }
    
    public function isPrimary($value)
    {
        return $this->builder->where('is_primary', $value);
    }

        //  This is an alias function of isPrimary
    public function is_primary($value)
    {
        return $this->isPrimary($value);
    }
     
    public function isSuppressed($value)
    {
        return $this->builder->where('is_suppressed', $value);
    }

        //  This is an alias function of isSuppressed
    public function is_suppressed($value)
    {
        return $this->isSuppressed($value);
    }
     
    public function suppressedAtStart($date)
    {
        return $this->builder->where('suppressed_at', '>=', $date);
    }

    public function suppressedAtEnd($date)
    {
        return $this->builder->where('suppressed_at', '<=', $date);
    }

    //  This is an alias function of suppressedAt
    public function suppressed_at_start($value)
    {
        return $this->suppressedAtStart($value);
    }

    //  This is an alias function of suppressedAt
    public function suppressed_at_end($value)
    {
        return $this->suppressedAtEnd($value);
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
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
