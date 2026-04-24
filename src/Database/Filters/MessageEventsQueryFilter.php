<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class MessageEventsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function eventType($value)
    {
        return $this->builder->where('event_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of eventType
    public function event_type($value)
    {
        return $this->eventType($value);
    }
    
    public function occurredAtStart($date)
    {
        return $this->builder->where('occurred_at', '>=', $date);
    }

    public function occurredAtEnd($date)
    {
        return $this->builder->where('occurred_at', '<=', $date);
    }

    //  This is an alias function of occurredAt
    public function occurred_at_start($value)
    {
        return $this->occurredAtStart($value);
    }

    //  This is an alias function of occurredAt
    public function occurred_at_end($value)
    {
        return $this->occurredAtEnd($value);
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

    public function communicationMessageId($value)
    {
            $communicationMessage = \NextDeveloper\Communication\Database\Models\Messages::where('uuid', $value)->first();

        if($communicationMessage) {
            return $this->builder->where('communication_message_id', '=', $communicationMessage->id);
        }
    }

        //  This is an alias function of communicationMessage
    public function communication_message_id($value)
    {
        return $this->communicationMessage($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
