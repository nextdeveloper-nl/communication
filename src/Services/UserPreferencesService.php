<?php

namespace NextDeveloper\Communication\Services;

use InvalidArgumentException;
use NextDeveloper\Communication\Database\Models\UserPreferences;
use NextDeveloper\Communication\Services\AbstractServices\AbstractUserPreferencesService;

/**
 * This class is responsible from managing the data for UserPreferences
 *
 * Class UserPreferencesService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class UserPreferencesService extends AbstractUserPreferencesService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    private const OPT_OUT_FIELDS = [
        'system_email'    => 'is_system_email_optout',
        'phone'           => 'is_phone_optout',
        'marketing_email' => 'is_marketing_email_optout',
    ];

    /**
     * Returns preferences for a user, creating a default record if none exists.
     */
    public static function getForUser(int $userId): UserPreferences
    {
        return UserPreferences::firstOrCreate(
            ['iam_user_id' => $userId],
            [
                'is_system_email_optout'    => false,
                'is_phone_optout'           => false,
                'is_marketing_email_optout' => false,
            ]
        );
    }

    public static function optOut(string $ref, string $type): UserPreferences
    {
        return self::update($ref, [self::resolveField($type) => true]);
    }

    public static function optIn(string $ref, string $type): UserPreferences
    {
        return self::update($ref, [self::resolveField($type) => false]);
    }

    private static function resolveField(string $type): string
    {
        if (!array_key_exists($type, self::OPT_OUT_FIELDS)) {
            throw new InvalidArgumentException(
                'Invalid opt-out type. Valid types: ' . implode(', ', array_keys(self::OPT_OUT_FIELDS))
            );
        }

        return self::OPT_OUT_FIELDS[$type];
    }
}