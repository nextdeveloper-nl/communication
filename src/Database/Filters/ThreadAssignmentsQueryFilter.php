<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
            

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ThreadAssignmentsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

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
    
    public function assignedByUserId($value)
    {
            $assignedByUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($assignedByUser) {
            return $this->builder->where('assigned_by_user_id', '=', $assignedByUser->id);
        }
    }

        //  This is an alias function of assignedByUser
    public function assigned_by_user_id($value)
    {
        return $this->assignedByUser($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
