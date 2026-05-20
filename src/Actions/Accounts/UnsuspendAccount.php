<?php

namespace NextDeveloper\Communication\Actions\Accounts;

use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Models\Accounts;

/**
 * This class handles lifting the suspension of a Communication account.
 */
class UnsuspendAccount extends AbstractAction
{
    public const EVENTS = [
        'unsuspended:NextDeveloper\Communication\Accounts',
    ];

    /**
     * @throws NotAllowedException
     */
    public function __construct(Accounts $accounts)
    {
        $this->model = $accounts;
        parent::__construct();
    }

    public function handle(): void
    {
        $this->setProgress(0, 'Starting to unsuspend account');

        $this->model->updateQuietly([
            'is_suspended' => false,
        ]);

        CacheHelper::deleteKeys(get_class($this->model), $this->model->uuid);

        $this->setProgress(100, 'Account unsuspended');
    }
}
