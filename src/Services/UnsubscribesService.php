<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Communication\Database\Models\Unsubscribes;
use NextDeveloper\Communication\Services\AbstractServices\AbstractUnsubscribesService;

/**
 * This class is responsible from managing the data for Unsubscribes
 *
 * Class UnsubscribesService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class UnsubscribesService extends AbstractUnsubscribesService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Records an opt-out and suppresses the matching contact identifier.
     * This table is append-only — records are never updated or deleted.
     */
    public static function create(array $data): Unsubscribes
    {
        $record = parent::create($data);

        ContactIdentifiersService::suppressByIdentifier(
            $record->communication_contact_id,
            $record->identifier,
            $record->reason ?? 'unsubscribed'
        );

        return $record;
    }
}