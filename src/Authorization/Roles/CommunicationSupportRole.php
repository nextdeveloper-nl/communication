<?php

namespace NextDeveloper\Communication\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class CommunicationSupportRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'communication-support';

    public const LEVEL = 120;

    public const DESCRIPTION = 'Communication Support';

    public const DB_PREFIX = 'communication';

    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        // System definitions and join tables without account ownership — no filter
        if (in_array($table, [
            'communication_available_channels',
            'communication_contact_identifiers',
            'communication_message_events',
            'communication_thread_assignments',
        ])) {
            return;
        }

        $builder->where('iam_account_id', UserHelper::currentAccount()->id);
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
            // System definitions — read only (admin manages these)
            'communication_accounts:read',
            'communication_available_channels:read',

            // Account config — full CRUD
            'communication_bots:read',
            'communication_bots:create',
            'communication_bots:update',
            'communication_bots:delete',
            'communication_channels:read',
            'communication_channels:create',
            'communication_channels:update',
            'communication_channels:delete',
            'communication_smtp_servers:read',
            'communication_smtp_servers:create',
            'communication_smtp_servers:update',
            'communication_smtp_servers:delete',

            // Operational objects — full CRUD
            'communication_contact_identifiers:read',
            'communication_contact_identifiers:create',
            'communication_contact_identifiers:update',
            'communication_contact_identifiers:delete',
            'communication_contacts:read',
            'communication_contacts:create',
            'communication_contacts:update',
            'communication_contacts:delete',
            'communication_messages:read',
            'communication_messages:create',
            'communication_messages:update',
            'communication_messages:delete',
            'communication_thread_assignments:read',
            'communication_thread_assignments:create',
            'communication_thread_assignments:update',
            'communication_thread_assignments:delete',
            'communication_threads:read',
            'communication_threads:create',
            'communication_threads:update',
            'communication_threads:delete',

            // Audit/events — read only (system-generated)
            'communication_message_events:read',

            // Notifications — support can create and read (e.g. sending alerts)
            'communication_notifications:read',
            'communication_notifications:create',

            // Personal objects — read only (member manages their own)
            'communication_remindables:read',
            'communication_unsubscribes:read',
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
}
