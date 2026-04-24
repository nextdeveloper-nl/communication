<?php

namespace NextDeveloper\Communication\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;

class CommunicationAdminRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'communication-admin';

    public const LEVEL = 100;

    public const DESCRIPTION = 'Communication Admin';

    public const DB_PREFIX = 'communication';

    public function apply(Builder $builder, Model $model)
    {
        //  Returns everything about communications
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
            'communication_accounts:read',
            'communication_accounts:update',
            'communication_accounts:create',
            'communication_accounts:delete',
            'communication_available_channels:read',
            'communication_available_channels:update',
            'communication_available_channels:create',
            'communication_available_channels:delete',
            'communication_bots:read',
            'communication_bots:update',
            'communication_bots:create',
            'communication_bots:delete',
            'communication_channels:read',
            'communication_channels:update',
            'communication_channels:create',
            'communication_channels:delete',
            'communication_contact_identifiers:read',
            'communication_contact_identifiers:update',
            'communication_contact_identifiers:create',
            'communication_contact_identifiers:delete',
            'communication_contacts:read',
            'communication_contacts:update',
            'communication_contacts:create',
            'communication_contacts:delete',
            'communication_message_events:read',
            'communication_message_events:update',
            'communication_message_events:create',
            'communication_message_events:delete',
            'communication_messages:read',
            'communication_messages:update',
            'communication_messages:create',
            'communication_messages:delete',
            'communication_notifications:read',
            'communication_notifications:update',
            'communication_notifications:create',
            'communication_notifications:delete',
            'communication_remindables:read',
            'communication_remindables:update',
            'communication_remindables:create',
            'communication_remindables:delete',
            'communication_smtp_servers:read',
            'communication_smtp_servers:update',
            'communication_smtp_servers:create',
            'communication_smtp_servers:delete',
            'communication_thread_assignments:read',
            'communication_thread_assignments:update',
            'communication_thread_assignments:create',
            'communication_thread_assignments:delete',
            'communication_threads:read',
            'communication_threads:update',
            'communication_threads:create',
            'communication_threads:delete',
            'communication_unsubscribes:read',
            'communication_unsubscribes:update',
            'communication_unsubscribes:create',
            'communication_unsubscribes:delete',
            'communication_user_preferences:read',
            'communication_user_preferences:update',
            'communication_user_preferences:create',
            'communication_user_preferences:delete',
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
