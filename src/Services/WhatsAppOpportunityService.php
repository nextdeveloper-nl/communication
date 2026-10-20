<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Contacts;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\CRM\Database\Models\Importers;
use NextDeveloper\CRM\Jobs\Importers\ImportLeadJob;
use NextDeveloper\CRM\Services\ImportersService;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Turns every inbound WhatsApp message into a CRM opportunity through the generic CRM lead importer.
 *
 * The importer of a WhatsApp number is a crm_importers row with source_type 'whatsapp' whose object_type/object_id point at
 * the Communication Channel (same shape as the Facebook one), created when the number is connected and a Flow pipeline
 * is given. Without that row (or while it is not active) messages are stored but no opportunity is created.
 *
 * One opportunity per message: dedupe_window_hours is 0 and every lead carries the message's uuid as external_id, which makes
 * its idempotency hash unique. The opportunity description starts with a markdown link to the message,
 *   [CommunicationMessageId:<message uuid>](Communication message)
 * so a UI widget can later turn it into a link to the conversation.
 */
class WhatsAppOpportunityService
{
    public const SOURCE_TYPE = 'whatsapp';

    /**
     * Creates the lead importer for a freshly connected number. Needs a Flow pipeline (the importer cannot place the
     * opportunity without one), so it returns null when none is given.
     * Runs as the connecting user/account and only bypasses the role check, since connecting is already authorized.
     */
    public static function createImporterForChannel(Channels $channel, array $options = []): ?Importers
    {
        if (empty($options['flow_pipeline_id'])) {
            return null;
        }

        $existing = Importers::withoutGlobalScopes()
            ->where('source_type', self::SOURCE_TYPE)
            ->where('object_type', Channels::class)
            ->where('object_id', $channel->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $config = array_filter([
            'flow_pipeline_id'     => $options['flow_pipeline_id'],
            'flow_stage_name'      => $options['flow_stage_name'] ?? null,
            'opportunity_type'     => $options['opportunity_type'] ?? 'sales',
            'default_country_code' => $options['default_country_code'] ?? null,
            'tags'                 => array_values(array_filter((array) ($options['tags'] ?? []), 'is_string')) ?: null,
        ], fn ($v) => $v !== null && $v !== '');

        // Every message is its own opportunity, so no duplicate window
        $config['dedupe_window_hours'] = 0;

        return UserHelper::withRolesCheckBypassed(fn () => ImportersService::create([
            'name'           => 'WhatsApp: ' . (data_get($channel->configuration, 'display_phone_number') ?: data_get($channel->configuration, 'phone_number_id')),
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
     * Queues the opportunity for one stored inbound message. No-op when the channel has no active importer.
     */
    public static function dispatchForMessage(Channels $channel, Contacts $contact, Messages $message, string $waId, string $body): void
    {
        $importer = Importers::withoutGlobalScopes()
            ->where('source_type', self::SOURCE_TYPE)
            ->where('status', 'active')
            ->where('object_type', Channels::class)
            ->where('object_id', $channel->id)
            ->first();

        if (!$importer) {
            return;
        }

        // The contact name is the WhatsApp profile name, or "+<number>" when Meta sent none (then the importer names it by number)
        $name = str_starts_with((string) $contact->full_name, '+') ? null : $contact->full_name;

        ImportLeadJob::dispatch($importer->id, [
            'name'        => $name,
            'email'       => null,
            'phone'       => '+' . $waId,
            'fields'      => [],
            'source'      => self::SOURCE_TYPE,
            'form'        => null,
            'external_id' => $message->uuid,
            // The first line is the link a UI widget will resolve; the message text follows for context
            'description' => '[CommunicationMessageId:' . $message->uuid . '](Communication message)' . "\n\n" . $body,
        ]);
    }
}
