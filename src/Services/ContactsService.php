<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Communication\Database\Models\Contacts;
use NextDeveloper\Communication\Database\Models\ContactIdentifiers;
use NextDeveloper\Communication\Services\AbstractServices\AbstractContactsService;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for Contacts
 *
 * Class ContactsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class ContactsService extends AbstractContactsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Finds an existing contact by identifier string, or creates one together with its identifier.
     * Use this to avoid duplicate contacts when ingesting inbound messages.
     */
    public static function findOrCreateByIdentifier(
        string $identifier,
        string $channelType,
        string $fullName,
        int $accountId
    ): Contacts {
        $existing = ContactIdentifiers::where('identifier', $identifier)
            ->where('channel_type', $channelType)
            ->first();

        if ($existing) {
            return Contacts::find($existing->communication_contact_id);
        }

        $contact = self::create([
            'full_name'      => $fullName,
            'iam_account_id' => $accountId,
        ]);

        ContactIdentifiersService::create([
            'communication_contact_id' => $contact->uuid,
            'channel_type'             => $channelType,
            'identifier'               => $identifier,
            'is_primary'               => true,
        ]);

        return $contact;
    }
}