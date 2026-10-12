<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\CRM\Database\Models\Importers;
use NextDeveloper\CRM\Jobs\Importers\ImportLeadJob;
use NextDeveloper\CRM\Services\ImportersService;

/**
 * Facebook Lead Ads adapter for the generic CRM lead importer.
 *
 * Only fetches and normalizes: a Facebook lead becomes ['name','email','phone','fields','source','form'] and is
 * handed to ImportLeadJob. Everything about users/accounts/opportunities/flow lives in LeadImporterService.
 *
 * The importer for a Facebook page is a crm_importers row with source_type 'facebook' whose
 * object_type/object_id point at the Communication Channel. Forms are mapped in importer config:
 *   config.forms.<facebook form id>.{flow_pipeline_id, flow_stage_id, opportunity_type}
 * A form that is not listed (and no default flow_pipeline_id) is not imported.
 */
class FacebookLeadImportService
{
    public const SOURCE_TYPE = 'facebook';

    private const GRAPH_API_VERSION = 'v19.0';

    /**
     * Active importer connected to this channel, if any.
     */
    public static function importerForChannel(Channels $channel): ?Importers
    {
        return Importers::withoutGlobalScopes()
            ->where('source_type', self::SOURCE_TYPE)
            ->where('status', 'active')
            ->where('object_type', Channels::class)
            ->where('object_id', $channel->id)
            ->first();
    }

    /**
     * Realtime path: the webhook already fetched the form answers, queue the import.
     */
    public static function dispatchFromWebhook(Channels $channel, ?string $formId, array $fieldData): void
    {
        $importer = self::importerForChannel($channel);

        if (!$importer || !self::isFormMapped($importer, $formId)) {
            return;
        }

        ImportLeadJob::dispatch($importer->id, self::normalize($fieldData, $formId));
    }

    /**
     * Backfill path: pulls leads created since the importer cursor for every mapped form and queues them.
     * Safe to overlap with the webhook, LeadImporterService de-duplicates on lead identity.
     *
     * @return int number of leads queued
     */
    public static function sync(Importers $importer): int
    {
        $channel = Channels::withoutGlobalScopes()->find($importer->object_id);
        $token = $channel ? data_get($channel->credentials, 'page_access_token') : null;

        if (!$channel || !$token) {
            ImportersService::update($importer->uuid, ['last_error' => 'Channel or page access token missing.']);
            return 0;
        }

        $since = (int) ($importer->cursor ?: 0);
        $highWatermark = $since;
        $queued = 0;

        foreach (array_keys($importer->config['forms'] ?? []) as $formId) {
            $url = sprintf('https://graph.facebook.com/%s/%s/leads', self::GRAPH_API_VERSION, $formId);
            $params = ['access_token' => $token, 'limit' => 100];

            if ($since) {
                // Graph filtering: only leads newer than the cursor
                $params['filtering'] = json_encode([['field' => 'time_created', 'operator' => 'GREATER_THAN', 'value' => $since]]);
            }

            while ($url) {
                $response = Http::get($url, $params);

                if (!$response->successful()) {
                    Log::error('[FacebookLeadImportService] Failed to pull leads', [
                        'importer_id' => $importer->id,
                        'form_id'     => $formId,
                        'status'      => $response->status(),
                    ]);
                    ImportersService::update($importer->uuid, ['last_error' => 'Graph API ' . $response->status() . ' on form ' . $formId]);
                    // Do not move the cursor past a form we could not read
                    return $queued;
                }

                foreach ($response->json('data', []) as $row) {
                    $fields = [];
                    foreach ($row['field_data'] ?? [] as $field) {
                        $fields[$field['name']] = $field['values'][0] ?? null;
                    }

                    ImportLeadJob::dispatch($importer->id, self::normalize($fields, $formId));
                    $queued++;

                    if (!empty($row['created_time'])) {
                        $highWatermark = max($highWatermark, strtotime($row['created_time']));
                    }
                }

                // Follow Graph pagination; the next URL already carries all params
                $url = $response->json('paging.next');
                $params = [];
            }
        }

        ImportersService::update($importer->uuid, [
            'cursor'         => (string) $highWatermark,
            'last_synced_at' => now(),
            'last_error'     => null,
        ]);

        return $queued;
    }

    /**
     * Facebook field_data -> normalized lead understood by LeadImporterService.
     */
    public static function normalize(array $fieldData, ?string $formId): array
    {
        $name = $fieldData['full_name'] ?? trim(($fieldData['first_name'] ?? '') . ' ' . ($fieldData['last_name'] ?? ''));

        // Anything that is not a standard contact field is kept as the lead's extra answers
        $standard = ['full_name', 'first_name', 'last_name', 'email', 'phone_number'];

        return [
            'name'   => $name ?: null,
            'email'  => $fieldData['email'] ?? null,
            'phone'  => $fieldData['phone_number'] ?? null,
            'fields' => array_diff_key($fieldData, array_flip($standard)),
            'source' => self::SOURCE_TYPE,
            'form'   => $formId,
        ];
    }

    private static function isFormMapped(Importers $importer, ?string $formId): bool
    {
        $config = $importer->config ?? [];

        return ($formId && isset($config['forms'][$formId])) || !empty($config['flow_pipeline_id']);
    }
}
