<?php

namespace NextDeveloper\Communication\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\CRM\Database\Models\AccountManagers;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class CommunicationMemberRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'communication-member';

    public const LEVEL = 150;

    public const DESCRIPTION = 'This level of role lets user to see its own communication logs.';

    public const DB_PREFIX = 'communication';

    /**
     * Applies basic member role sql for Eloquent
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if($model->getTable() == 'communication_available_channels') {
            return;
        }

        /**
         * Here user will be able to list all models, because by default, sales manager can see everybody.
         */
        $builder->where([
            'iam_account_id'    =>  UserHelper::currentAccount()->id,
            'iam_user_id'       =>  UserHelper::me()->id
        ]);
    }

    public function checkPrivileges(Users $users = null)
    {
        //return UserHelper::hasRole(self::NAME, $users);
    }

    public function getModule()
    {
        return 'communication';
    }

    public function allowedOperations() :array
    {
        return [
            'communication_emails:read',
            'communication_notifications:read',
            'communication_remindables:read',
            'communication_user_preferences:read',
        ];
    }

    public function getLevel(): int
    {
        return self::LEVEL;
    }

    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function canBeApplied($column)
    {
        if(self::DB_PREFIX === '*') {
            return true;
        }

        if(Str::startsWith($column, self::DB_PREFIX)) {
            return true;
        }

        return false;
    }

    public function getDbPrefix()
    {
        return self::DB_PREFIX;
    }

    public function checkRules(Users $users): bool
    {
        // TODO: Implement checkRules() method.
    }
}
