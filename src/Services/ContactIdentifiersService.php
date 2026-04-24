<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Database\Eloquent\Collection;
use NextDeveloper\Communication\Database\Models\ContactIdentifiers;
use NextDeveloper\Communication\Services\AbstractServices\AbstractContactIdentifiersService;

/**
 * This class is responsible from managing the data for ContactIdentifiers
 *
 * Class ContactIdentifiersService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class ContactIdentifiersService extends AbstractContactIdentifiersService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Suppresses an identifier so it is never contacted again.
     * For opt-outs, always go through UnsubscribesService — not this method directly.
     */
    public static function suppress(string $ref, string $reason): ContactIdentifiers
    {
        return self::update($ref, [
            'is_suppressed'     => true,
            'suppressed_at'     => now(),
            'suppressed_reason' => $reason,
        ]);
    }

    /**
     * Suppresses by contact ID + identifier string.
     * Used internally by UnsubscribesService after recording the opt-out.
     */
    public static function suppressByIdentifier(int $contactId, string $identifier, string $reason): void
    {
        $record = ContactIdentifiers::where('communication_contact_id', $contactId)
            ->where('identifier', $identifier)
            ->first();

        if ($record) {
            self::suppress($record->uuid, $reason);
        }
    }

    /**
     * Lifts suppression on an identifier (admin use only).
     */
    public static function unsuppress(string $ref): ContactIdentifiers
    {
        return self::update($ref, [
            'is_suppressed'     => false,
            'suppressed_at'     => null,
            'suppressed_reason' => null,
        ]);
    }

    /**
     * Returns all non-suppressed identifiers for a contact.
     */
    public static function getActiveForContact(int $contactId): Collection
    {
        return ContactIdentifiers::where('communication_contact_id', $contactId)
            ->where('is_suppressed', false)
            ->get();
    }
}