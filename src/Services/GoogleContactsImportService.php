<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Database\Models\ExternalServices;
use NextDeveloper\Commons\Helpers\GoogleWorkspaceHelper;
use NextDeveloper\Communication\Database\Models\ContactIdentifiers;
use NextDeveloper\Communication\Database\Models\Contacts;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Read-only import of a connected Google account's contacts (People API people.connections.list)
 * into communication_contacts. Needs the contacts.readonly scope. Nothing is ever written back to Google.
 *
 * Duplicates are skipped by email within the account: a contact that already has the same email identifier
 * (from any source) is left untouched.
 */
class GoogleContactsImportService
{
    public const CHANNEL_TYPE = 'email';

    public const SOURCE_TAG = 'source:google';

    private const ENDPOINT = 'https://people.googleapis.com/v1/people/me/connections';

    private const PAGE_SIZE = 1000;

    /**
     * Imports contacts for one google_workspace ExternalServices row and records the result on its
     * configuration ('contacts_sync') so the panel can show last-synced status.
     *
     * @return array{status: string, imported: int, skipped: int, total: int, last_synced_at: string, error: ?string}
     */
    public static function import(ExternalServices $service): array
    {
        $result = ['status' => 'success', 'imported' => 0, 'skipped' => 0, 'total' => 0, 'error' => null];

        try {
            if (!GoogleWorkspaceHelper::hasScope($service, GoogleWorkspaceHelper::SCOPE_CONTACTS_READONLY)) {
                throw new \RuntimeException('contacts.readonly scope was not granted. Please reconnect your Google account.');
            }

            $token = GoogleWorkspaceHelper::freshAccessToken($service);

            if (!$token) {
                throw new \RuntimeException('Unable to refresh the Google token. Please reconnect your account.');
            }

            $pageToken = null;

            do {
                $response = Http::withToken($token)->get(self::ENDPOINT, array_filter([
                    'personFields' => 'names,emailAddresses',
                    'pageSize'     => self::PAGE_SIZE,
                    'pageToken'    => $pageToken,
                ]));

                if (!$response->successful()) {
                    // the status is the exception code so the catch below can tell an auth failure from a transient one
                    throw new \RuntimeException(
                        $response->json('error.message') ?? ('People API error ' . $response->status()),
                        $response->status()
                    );
                }

                foreach ($response->json('connections', []) as $person) {
                    self::importPerson($person, (int) $service->iam_account_id, $result);
                }

                $pageToken = $response->json('nextPageToken');
            } while ($pageToken);
        } catch (\Throwable $e) {
            $result['status'] = 'error';
            $result['error']  = $e->getMessage();

            // Google rejected the token (401) or access (403): flag the connection and email the owner once
            if (in_array($e->getCode(), [401, 403], true)) {
                GoogleWorkspaceHelper::flagUnauthorized($service, 'contacts');
            }

            Log::warning('[Communication::GoogleContactsImportService] Import failed', [
                'external_service_uuid' => $service->uuid,
                'error'                 => $e->getMessage(),
            ]);
        }

        $result['last_synced_at'] = now()->toISOString();

        GoogleWorkspaceHelper::recordSync($service, 'contacts_sync', $result);

        return $result;
    }

    /**
     * A person without an email address cannot be reached on any channel here, so it is not imported.
     */
    private static function importPerson(array $person, int $accountId, array &$result): void
    {
        foreach ($person['emailAddresses'] ?? [] as $emailAddress) {
            $email = strtolower(trim($emailAddress['value'] ?? ''));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $result['total']++;

            if (self::emailExists($email, $accountId)) {
                $result['skipped']++;
                continue;
            }

            $name = $person['names'][0]['displayName'] ?? $email;

            // Same pattern as the webhook ingestion: contacts are created as the system on behalf of the account
            // Source marker: 'source:google' tag on the contact, People API resourceName as external_id on the identifier
            UserHelper::runAsAdmin(function () use ($email, $name, $accountId, $person) {
                ContactsService::findOrCreateByIdentifier(
                    $email,
                    self::CHANNEL_TYPE,
                    $name,
                    $accountId,
                    [self::SOURCE_TAG],
                    ['source' => 'google', 'external_id' => $person['resourceName'] ?? null]
                );
            });

            $result['imported']++;
        }
    }

    private static function emailExists(string $email, int $accountId): bool
    {
        return ContactIdentifiers::withoutGlobalScopes()
            ->where('channel_type', self::CHANNEL_TYPE)
            ->whereRaw('lower(identifier) = ?', [$email])
            ->whereIn('communication_contact_id', Contacts::withoutGlobalScopes()
                ->where('iam_account_id', $accountId)
                ->select('id'))
            ->exists();
    }
}
