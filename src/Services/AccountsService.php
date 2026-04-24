<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Communication\Database\Models\Accounts;
use NextDeveloper\Communication\Services\AbstractServices\AbstractAccountsService;

/**
 * This class is responsible from managing the data for Accounts
 *
 * Class AccountsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class AccountsService extends AbstractAccountsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Accounts are auto-provisioned by the DB trigger trg_create_communication_account.
     * Never call create() manually.
     */
    public static function getByIamAccount(int $iamAccountId): ?Accounts
    {
        return Accounts::where('iam_account_id', $iamAccountId)->first();
    }

    public static function suspend(string $ref, string $reason): Accounts
    {
        return self::update($ref, [
            'is_suspended'      => true,
            'suspension_reason' => $reason,
        ]);
    }

    public static function unsuspend(string $ref): Accounts
    {
        return self::update($ref, [
            'is_suspended'      => false,
            'suspension_reason' => null,
        ]);
    }

    /**
     * Atomically increments the email counter for the period.
     */
    public static function incrementEmailsSent(int $iamAccountId, int $count = 1): void
    {
        Accounts::where('iam_account_id', $iamAccountId)
            ->increment('emails_sent_this_period', $count);
    }

    /**
     * Atomically increments the SMS counter for the period.
     */
    public static function incrementSmsSent(int $iamAccountId, int $count = 1): void
    {
        Accounts::where('iam_account_id', $iamAccountId)
            ->increment('sms_sent_this_period', $count);
    }

    public static function isWithinEmailLimit(Accounts $account): bool
    {
        return $account->emails_sent_this_period < $account->max_emails_per_month;
    }

    public static function isWithinSmsLimit(Accounts $account): bool
    {
        return $account->sms_sent_this_period < $account->max_sms_per_month;
    }
}