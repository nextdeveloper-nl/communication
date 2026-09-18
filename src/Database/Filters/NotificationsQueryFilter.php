<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
use NextDeveloper\Commons\Database\Filters\FilterClauses;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class NotificationsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function severity($value)
    {
        return $this->builder->where('severity', 'ilike', '%' . $value . '%');
    }


    public function objectType($value)
    {
        return FilterClauses::objectType($this->builder, $value);
    }

    /**
     * Notifications about one or more records (comma separated uuids of the object_type sent along).
     */
    public function objectId($value)
    {
        return FilterClauses::objectId(
            $this->builder,
            $this->request->get('object_type', $this->request->get('objectType')),
            $value
        );
    }

    //  This is an alias function of objectId
    public function object_id($value)
    {
        return $this->objectId($value);
    }

    /**
     * unread=true: not read yet; unread=false: read.
     */
    public function unread($value)
    {
        $column = $this->builder->getModel()->qualifyColumn('read_at');

        return filter_var($value, FILTER_VALIDATE_BOOLEAN)
            ? $this->builder->whereNull($column)
            : $this->builder->whereNotNull($column);
    }

        //  This is an alias function of objectType
    public function object_type($value)
    {
        return $this->objectType($value);
    }

    public function data($value)
    {
        return $this->builder->where('data', 'ilike', '%' . $value . '%');
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
        return FilterClauses::linkedId($this->builder, 'iam_user_id', \NextDeveloper\IAM\Database\Models\Users::class, $value);
    }

    //  This is an alias function of iamUserId
    public function iam_user_id($value)
    {
        return $this->iamUserId($value);
    }


    public function iamAccountId($value)
    {
        return FilterClauses::linkedId($this->builder, 'iam_account_id', \NextDeveloper\IAM\Database\Models\Accounts::class, $value);
    }

    //  This is an alias function of iamAccountId
    public function iam_account_id($value)
    {
        return $this->iamAccountId($value);
    }


    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE




}
