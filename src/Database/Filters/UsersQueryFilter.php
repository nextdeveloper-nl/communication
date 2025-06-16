<?php

namespace NextDeveloper\Communication\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;


/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class UsersQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function telegramId($value)
    {
        return $this->builder->where('telegram_id', 'ilike', '%' . $value . '%');
    }

    public function aiSessionId($value)
    {
        return $this->builder->where('ai_session_id', 'ilike', '%' . $value . '%');
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

    public function telegramId($value)
    {
            $telegram = \NextDeveloper\\Database\Models\Telegrams::where('uuid', $value)->first();

        if($telegram) {
            return $this->builder->where('telegram_id', '=', $telegram->id);
        }
    }

    public function aiSessionId($value)
    {
            $aiSession = \NextDeveloper\\Database\Models\AiSessions::where('uuid', $value)->first();

        if($aiSession) {
            return $this->builder->where('ai_session_id', '=', $aiSession->id);
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
