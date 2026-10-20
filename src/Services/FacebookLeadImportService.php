<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\CRM\Database\Models\Importers;
use NextDeveloper\CRM\Jobs\Importers\ImportLeadJob;
use NextDeveloper\CRM\Services\ImportersService;
use NextDeveloper\Flow\Database\Models\Pipelines;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;
use NextDeveloper\IAM\Database\Models\Users as IamUsers;
use NextDeveloper\IAM\Helpers\UserHelper;

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
        $importer = Importers::withoutGlobalScopes()
            ->where('source_type', self::SOURCE_TYPE)
            ->where('status', 'active')
            ->where('object_type', Channels::class)
            ->where('object_id', $channel->id)
            ->first();

        // No hand-made importer: fall back to a code-defined one for this Page (config crm.static_importers)
        return $importer ?? self::ensureStaticImporter($channel);
    }

    /**
     * Creates (once) the crm_importers row for a code-defined importer that matches this channel's Page.
     * The row belongs to the channel's account and that account's owner, so the import runs as the customer.
     * Later edits of the row (status, config) win over the code definition.
     */
    public static function ensureStaticImporter(Channels $channel): ?Importers
    {
        $pageId = data_get($channel->configuration, 'page_id');

        foreach (config('crm.static_importers', []) as $definition) {
            if (($definition['source_type'] ?? null) !== self::SOURCE_TYPE || ($definition['page_id'] ?? null) !== $pageId) {
                continue;
            }

            // Paused/disabled copies of the row count as "exists": they are not recreated
            $existing = Importers::withoutGlobalScopes()
                ->where('object_type', Channels::class)
                ->where('object_id', $channel->id)
                ->where('name', $definition['name'])
                ->first();

            if ($existing) {
                return $existing->status === 'active' ? $existing : null;
            }

            $account = IamAccounts::withoutGlobalScopes()->find($channel->iam_account_id);

            if (!$account) {
                return null;
            }

            $create = fn () => ImportersService::create([
                'name'           => $definition['name'],
                'source_type'    => self::SOURCE_TYPE,
                'status'         => 'active',
                'config'         => $definition['config'] ?? [],
                'object_type'    => Channels::class,
                'object_id'      => $channel->id,
                'iam_account_id' => $account->uuid,
                'iam_user_id'    => IamUsers::withoutGlobalScopes()->find($account->iam_user_id)?->uuid,
            ]);

            // The service resolves the account/user uuids through the authorization scope and the importers observer checks
            // the role, and the sync command (cron) has no user, so both come back empty there. Without a user: run as
            // admin. With one (the webhook already runs as admin): only bypass the role check, never nest runAsAdmin,
            // because that overwrites the saved identity it restores afterwards.
            return UserHelper::me()
                ? UserHelper::withRolesCheckBypassed($create)
                : UserHelper::runAsAdmin($create);
        }

        return null;
    }

    /**
     * Makes sure every code-defined importer has its row, for already connected Pages (used by the sync command).
     */
    public static function ensureStaticImporters(): void
    {
        Channels::withoutGlobalScopes()
            ->where('type', Channels::TYPE_FACEBOOK)
            ->where('is_active', true)
            ->get()
            ->each(fn (Channels $channel) => self::ensureStaticImporter($channel));
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

        // A disconnected (soft-deleted) or deactivated channel must not keep pulling leads with its old token
        if (!$channel || $channel->trashed() || !$channel->is_active || !$token) {
            self::updateImporter($importer, ['last_error' => 'Channel or page access token missing.']);
            return 0;
        }

        // Pick up lead forms created on the Page since the last run
        self::mapNewForms($importer, $token);

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
                        // Graph error details (code/subcode/message) so a 4xx can be diagnosed from the log
                        'graph_error' => $response->json('error') ?? mb_substr($response->body(), 0, 500),
                    ]);
                    self::updateImporter($importer, ['last_error' => 'Graph API ' . $response->status() . ' on form ' . $formId . ': ' . mb_substr((string) ($response->json('error.message') ?? $response->body()), 0, 300)
                        . ' (code ' . $response->json('error.code') . ', subcode ' . $response->json('error.error_subcode') . ')']);
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

        self::updateImporter($importer, [
            'cursor'         => (string) $highWatermark,
            'last_synced_at' => now(),
            'last_error'     => null,
        ]);

        return $queued;
    }

    /**
     * Creates the lead importer for a freshly connected Page (self-service connect), so leads start flowing without
     * anyone editing config. Maps every lead form the Page has, and keeps mapping new ones on each sync (auto_forms).
     * Flow settings (flow_pipeline_id, flow_stage_name, opportunity_type, default_country_code) are optional; without a
     * pipeline the lead is still imported, just not placed in a Flow pipeline.
     * Runs as the connecting user/account and only bypasses the role check, since connecting a Page is already authorized.
     */
    public static function createImporterForChannel(Channels $channel, array $options = []): ?Importers
    {
        $existing = Importers::withoutGlobalScopes()
            ->where('source_type', self::SOURCE_TYPE)
            ->where('object_type', Channels::class)
            ->where('object_id', $channel->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $token = data_get($channel->credentials, 'page_access_token');
        $pageId = data_get($channel->configuration, 'page_id');

        $forms = [];
        foreach (self::fetchPageFormIds($pageId, $token) as $formId) {
            $forms[$formId] = [];
        }

        $config = array_filter([
            'flow_pipeline_id'     => $options['flow_pipeline_id'] ?? null,
            'flow_stage_name'      => $options['flow_stage_name'] ?? null,
            'opportunity_type'     => $options['opportunity_type'] ?? 'sales',
            'default_country_code' => $options['default_country_code'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $config['dedupe_window_hours'] = 24;
        $config['auto_forms'] = true;
        $config['forms'] = $forms;

        return UserHelper::withRolesCheckBypassed(fn () => ImportersService::create([
            'name'           => 'Facebook: ' . (data_get($channel->configuration, 'page_name') ?: $pageId),
            'source_type'    => self::SOURCE_TYPE,
            'status'         => 'active',
            'config'         => $config,
            'object_type'    => Channels::class,
            'object_id'      => $channel->id,
            'iam_account_id' => UserHelper::currentAccount()->uuid,
            'iam_user_id'    => UserHelper::me()->uuid,
        ]));
    }

    /**
     * A customer-supplied flow_pipeline_id must be a pipeline the current user can see. LeadImporterService resolves
     * pipelines without scopes (code-defined importers point at our own pipelines), so the check has to happen here,
     * through the normal AuthorizationScope, before the id is stored in the importer config.
     */
    public static function assertPipelineAccessible(?string $pipelineUuid): void
    {
        if ($pipelineUuid && !Pipelines::where('uuid', $pipelineUuid)->exists()) {
            throw new NotAllowedException('The given flow_pipeline_id was not found or you do not have access to it.');
        }
    }

    /**
     * Pauses the importers linked to a channel (used when the Page is disconnected) so they stop pulling leads.
     */
    public static function pauseImportersForChannel(Channels $channel): void
    {
        Importers::where('source_type', self::SOURCE_TYPE)
            ->where('object_type', Channels::class)
            ->where('object_id', $channel->id)
            ->get()
            ->each(fn (Importers $importer) => ImportersService::update($importer->uuid, ['status' => 'paused']));
    }

    /**
     * Ids of the lead forms of a Page (all statuses except deleted ones, which Graph does not return).
     *
     * @return string[]
     */
    private static function fetchPageFormIds(?string $pageId, ?string $token): array
    {
        if (!$pageId || !$token) {
            return [];
        }

        $ids = [];
        $url = sprintf('https://graph.facebook.com/%s/%s/leadgen_forms', self::GRAPH_API_VERSION, $pageId);
        $params = ['access_token' => $token, 'fields' => 'id', 'limit' => 100];

        while ($url) {
            $response = Http::get($url, $params);

            if (!$response->successful()) {
                Log::warning('[FacebookLeadImportService] Could not list lead forms', [
                    'page_id'     => $pageId,
                    'status'      => $response->status(),
                    'graph_error' => $response->json('error') ?? mb_substr($response->body(), 0, 500),
                ]);
                break;
            }

            foreach ($response->json('data', []) as $row) {
                $ids[] = (string) $row['id'];
            }

            $url = $response->json('paging.next');
            $params = [];
        }

        return $ids;
    }

    /**
     * Adds forms created on the Page after the importer was set up (only for importers with config.auto_forms).
     * Existing form entries (and their per-form overrides) are never touched.
     */
    private static function mapNewForms(Importers $importer, string $token): void
    {
        $config = $importer->config ?? [];

        if (empty($config['auto_forms'])) {
            return;
        }

        $channel = Channels::withoutGlobalScopes()->find($importer->object_id);
        $found = self::fetchPageFormIds(data_get($channel?->configuration, 'page_id'), $token);
        $missing = array_diff($found, array_map('strval', array_keys($config['forms'] ?? [])));

        if (!$missing) {
            return;
        }

        foreach ($missing as $formId) {
            $config['forms'][$formId] = [];
        }

        self::updateImporter($importer, ['config' => $config]);
        $importer->config = $config;
    }

    /**
     * Updates an importer row from a context that may have no user (sync command / cron).
     * ImportersService::update() looks the row up through AuthorizationScope, which hides it when nobody is logged in
     * ("We cannot find the related object to update"), so without a user the update runs as admin.
     */
    private static function updateImporter(Importers $importer, array $data)
    {
        $update = fn () => ImportersService::update($importer->uuid, $data);

        return UserHelper::me() ? $update() : UserHelper::runAsAdmin($update);
    }

    /**
     * Field keys (after normalizeFieldKey) accepted for each contact attribute. Facebook sends standard fields as
     * email/full_name/phone_number, but forms in other languages or with custom questions arrive under their label
     * (e.g. Turkish "e-posta_adresi", "adı"), so the aliases cover those too.
     */
    private const FIELD_ALIASES = [
        'email'     => ['email', 'e_mail', 'email_address', 'e_posta', 'e_posta_adresi', 'eposta', 'eposta_adresi', 'mail', 'mail_adresi'],
        'phone'     => ['phone_number', 'phone', 'telefon', 'telefon_numarasi', 'telefon_no', 'cep_telefonu', 'cep_telefon_numarasi', 'gsm', 'mobile'],
        'full_name' => ['full_name', 'name', 'ad_soyad', 'adi_soyadi', 'ad_soyadi', 'isim_soyisim', 'isim', 'tam_ad'],
        'first_name' => ['first_name', 'ad', 'adi'],
        'last_name' => ['last_name', 'soyad', 'soyadi', 'soyisim'],
    ];

    /**
     * Lowercases, transliterates Turkish letters and turns every non-alphanumeric run into "_" so that
     * "E-posta Adresi", "e-posta_adresi" and "E_POSTA_ADRESI" compare equal.
     */
    private static function normalizeFieldKey(string $key): string
    {
        $key = strtr($key, ['İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ş' => 's', 'ş' => 's', 'Ç' => 'c', 'ç' => 'c',
            'Ğ' => 'g', 'ğ' => 'g', 'Ö' => 'o', 'ö' => 'o', 'Ü' => 'u', 'ü' => 'u']);

        return trim(preg_replace('/[^a-z0-9]+/', '_', mb_strtolower($key)), '_');
    }

    /**
     * Facebook field_data -> normalized lead understood by LeadImporterService.
     */
    public static function normalize(array $fieldData, ?string $formId): array
    {
        $picked = [];
        $used = [];

        // First alias hit per attribute wins; the keys that were used are not repeated in the extra answers
        foreach (self::FIELD_ALIASES as $attribute => $aliases) {
            foreach ($fieldData as $key => $value) {
                if (in_array(self::normalizeFieldKey((string) $key), $aliases, true) && filled($value)) {
                    $picked[$attribute] = is_array($value) ? implode(', ', $value) : trim((string) $value);
                    $used[] = $key;
                    break;
                }
            }
        }

        $name = $picked['full_name'] ?? trim(($picked['first_name'] ?? '') . ' ' . ($picked['last_name'] ?? ''));

        return [
            'name'   => $name ?: null,
            'email'  => $picked['email'] ?? null,
            'phone'  => $picked['phone'] ?? null,
            'fields' => array_diff_key($fieldData, array_flip($used)),
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
