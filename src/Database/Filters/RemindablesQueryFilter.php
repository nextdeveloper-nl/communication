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

    public function note($value)
    {
        return $this->builder->where('note', 'ilike', '%' . $value . '%');
    }

    public function isReminded($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_reminded', $value);
    }

    public function isAcknowledged($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_acknowledged', $value);
    }

    public function isCancelled($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_cancelled', $value);
    }

    public function remindDatetimeStart($date)
    {
        return $this->builder->where('remind_datetime', '>=', $date);
    }

    public function remindDatetimeEnd($date)
    {
        return $this->builder->where('remind_datetime', '<=', $date);
    }

    public function snoozeDatetimeStart($date)
    {
        return $this->builder->where('snooze_datetime', '>=', $date);
    }

    public function snoozeDatetimeEnd($date)
    {
        return $this->builder->where('snooze_datetime', '<=', $date);
    }

    public function createdAtStart($date)
    {
        return $this->builder->where('created_at', '>=', $date);
    }

    public function createdAtEnd($date)
    {
        return $this->builder->where('created_at', '<=', $date);
    }

    public function updatedAtStart($date)
    {
        return $this->builder->where('updated_at', '>=', $date);
    }

    public function updatedAtEnd($date)
    {
        return $this->builder->where('updated_at', '<=', $date);
    }

    public function deletedAtStart($date)
    {
        return $this->builder->where('deleted_at', '>=', $date);
    }

    public function deletedAtEnd($date)
    {
        return $this->builder->where('deleted_at', '<=', $date);
    }

    public function iamUserId($value)
    {
            $iamUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($iamUser) {
            return $this->builder->where('iam_user_id', '=', $iamUser->id);
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
















}
