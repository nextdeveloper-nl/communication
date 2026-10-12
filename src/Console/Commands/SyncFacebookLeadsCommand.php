<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Communication\Services\FacebookLeadImportService;
use NextDeveloper\CRM\Database\Models\Importers;

/**
 * Pulls Facebook Lead Ads leads for every active Facebook importer (backfill for missed webhooks).
 * Leads are queued as ImportLeadJob, so the command itself stays fast.
 */
class SyncFacebookLeadsCommand extends Command
{
    protected $signature = 'communication:sync-facebook-leads {--importer= : Only this importer (uuid)}';

    protected $description = 'Pulls new Facebook Lead Ads leads and queues them for the CRM lead importer.';

    public function handle(): int
    {
        // Code-defined importers get their rows before we look for importers to sync
        FacebookLeadImportService::ensureStaticImporters();

        $importers = Importers::withoutGlobalScopes()
            ->where('source_type', FacebookLeadImportService::SOURCE_TYPE)
            ->where('status', 'active')
            ->when($this->option('importer'), fn ($q, $uuid) => $q->where('uuid', $uuid))
            ->get();

        foreach ($importers as $importer) {
            $queued = FacebookLeadImportService::sync($importer);
            $this->line('Importer ' . $importer->uuid . ': ' . $queued . ' lead(s) queued');
        }

        return self::SUCCESS;
    }
}
