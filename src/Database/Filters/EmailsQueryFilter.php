<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class EmailsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function fromEmailAddress($value)
    {
        return $this->builder->where('from_email_address', 'ilike', '%' . $value . '%');
    }

    public function subject($value)
    {
        return $this->builder->where('subject', 'ilike', '%' . $value . '%');
    }

    public function body($value)
    {
        return $this->builder->where('body', 'ilike', '%' . $value . '%');
    }

    public function isMarketingEmail($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_marketing_email', $value);
    }

    public function deliverAtStart($date)
    {
        return $this->builder->where('deliver_at', '>=', $date);
    }

    public function deliverAtEnd($date)
    {
        return $this->builder->where('deliver_at', '<=', $date);
    }

    public function deliveredAtStart($date)
    {
        return $this->builder->where('delivered_at', '>=', $date);
    }

    public function deliveredAtEnd($date)
    {
        return $this->builder->where('delivered_at', '<=', $date);
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

    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    public function communicationChannelId($value)
    {
            $communicationChannel = \NextDeveloper\Communication\Database\Models\Channels::where('uuid', $value)->first();

        if($communicationChannel) {
            return $this->builder->where('communication_channel_id', '=', $communicationChannel->id);
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
