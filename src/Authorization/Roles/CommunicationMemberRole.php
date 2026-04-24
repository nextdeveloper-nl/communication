<?php

namespace NextDeveloper\Communication\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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

    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        // System/lookup table and join tables without ownership — no filter
        if (in_array($table, [
            'communication_available_channels',
            'communication_contact_identifiers',
            'communication_message_events',
            'communication_thread_assignments',
        ])) {
            return;
        }

        // Only has iam_user_id
        if ($table === 'communication_user_preferences') {
            $builder->where('iam_user_id', UserHelper::me()->id);
            return;
        }

        // Only has iam_account_id
        if (in_array($table, [
            'communication_accounts',
            'communication_bots',
            'communication_channels',
            'communication_contacts',
            'communication_messages',
            'communication_smtp_servers',
            'communication_threads',
            'communication_unsubscribes',
        ])) {
            $builder->where('iam_account_id', UserHelper::currentAccount()->id);
            return;
        }

        // Has both iam_account_id and iam_user_id (notifications, remindables)
        $builder->where([
            'iam_account_id' => UserHelper::currentAccount()->id,
            'iam_user_id'    => UserHelper::me()->id,
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
            // System definitions — read only
            'communication_accounts:read',
            'communication_available_channels:read',

            // Account config — full CRUD (member manages their own)
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

            // Operational objects — full CRUD (member manages their own)
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

            // Audit/events — read only
            'communication_message_events:read',

            // Notifications — read only (system-generated for the member)
            'communication_notifications:read',

            // Personal objects — full CRUD (member owns these)
            'communication_remindables:read',
            'communication_remindables:create',
            'communication_remindables:update',
            'communication_remindables:delete',
            'communication_unsubscribes:read',
            'communication_unsubscribes:create',
            'communication_unsubscribes:update',
            'communication_unsubscribes:delete',
            'communication_user_preferences:read',
            'communication_user_preferences:create',
            'communication_user_preferences:update',
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

    public function checkRules(Users $users): bool
    {
        return true;
    }
}
