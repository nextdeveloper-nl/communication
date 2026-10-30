<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Commons\Helpers\GoogleWorkspaceHelper;
use NextDeveloper\Communication\Services\GoogleContactsImportService;

/**
 * Imports Google contacts (read-only) for every Workspace connection that was granted contacts.readonly.
 * Scheduled, and run per user by the on-demand sync endpoint (--user).
 */
class ImportGoogleContactsCommand extends Command
{
    protected $signature = 'communication:import-google-contacts {--user= : Only this user ID}';

    protected $description = 'Imports contacts from connected Google Workspace accounts into Communication contacts.';

    public function handle(): int
    {
        $services = GoogleWorkspaceHelper::servicesWithScope(
            GoogleWorkspaceHelper::SCOPE_CONTACTS_READONLY,
            $this->option('user') ? (int) $this->option('user') : null
        );

        foreach ($services as $service) {
            $result = GoogleContactsImportService::import($service);

            $this->line($service->service_owner . ': ' . $result['status']
                . ' (imported ' . $result['imported'] . ', skipped ' . $result['skipped'] . ')');
        }

        return self::SUCCESS;
    }
}
