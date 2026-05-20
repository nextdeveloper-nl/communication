<?php

namespace NextDeveloper\Communication\Actions\Accounts;

use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Models\Accounts;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;

/**
 * Backfills a Communication account for an existing IAM account.
 *
 * The DB trigger creates child rows on iam_accounts INSERT. This action is
 * used to manually create a child row for historical IAM accounts and flags
 * it not suspended.
 */
class CreateAccount extends AbstractAction
{
    public const EVENTS = [
        'created:NextDeveloper\Communication\Accounts',
    ];

    /**
     * @throws NotAllowedException
     */
    public function __construct(IamAccounts $iamAccount)
    {
        $this->model = $iamAccount;
        parent::__construct();
    }

    public function handle(): void
    {
        $this->setProgress(0, 'Starting to create communication account');

        Accounts::withoutGlobalScopes()->firstOrCreate(
            ['iam_account_id' => $this->model->id],
            [
                'plan' => 'free',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'is_suspended' => false,
            ]
        );

        $this->setProgress(100, 'Communication account created');
    }
}
