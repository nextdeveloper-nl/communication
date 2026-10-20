<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Contacts;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Ingests inbound WhatsApp Cloud API webhook events into the standard Channels/Threads/Messages shape.
 *
 * Multi-tenant: one webhook URL serves every tenant. The tenant is found through the receiving number
 * (value.metadata.phone_number_id, unique across Meta) which maps to exactly one Channel, and the Channel's own
 * iam_account_id owns the contact, thread and message. There is no authenticated user on a webhook request, so the
 * writes run under UserHelper::runAsAdmin(), like FacebookWebhookService.
 */
class WhatsAppWebhookService
{
    public const CONTACT_CHANNEL_TYPE = 'whatsapp';

    /**
     * Handles one `entry[]` element (one WhatsApp Business Account) of a webhook payload.
     */
    public static function ingestEntry(array $entry): void
    {
        foreach ($entry['changes'] ?? [] as $change) {
            if (($change['field'] ?? null) !== 'messages') {
                continue;
            }

            $value = $change['value'] ?? [];
            $phoneNumberId = data_get($value, 'metadata.phone_number_id');
            $channel = self::resolveChannel($phoneNumberId, $entry['id'] ?? null);

            if (!$channel) {
                Log::warning('[WhatsAppWebhookService] No Channel found for phone number', ['phone_number_id' => $phoneNumberId]);
                continue;
            }

            // wa_id -> profile name, sent next to the messages
            $names = [];
            foreach ($value['contacts'] ?? [] as $contact) {
                $names[$contact['wa_id'] ?? ''] = data_get($contact, 'profile.name');
            }

            foreach ($value['messages'] ?? [] as $message) {
                self::ingestMessage($channel, $message, $names);
            }

            // Delivery/read receipts for outbound messages are not tracked yet
            foreach ($value['statuses'] ?? [] as $status) {
                if (($status['status'] ?? null) === 'failed') {
                    Log::warning('[WhatsAppWebhookService] Outbound message failed', [
                        'channel_id' => $channel->id,
                        'message_id' => $status['id'] ?? null,
                        'errors'     => $status['errors'] ?? null,
                    ]);
                }
            }
        }
    }

    private static function ingestMessage(Channels $channel, array $message, array $names): void
    {
        $from = $message['from'] ?? null;
        $wamid = $message['id'] ?? null;

        if (!$from || !$wamid) {
            return;
        }

        // Meta retries deliveries it did not get a 200 for: the same wamid must never be stored twice
        $alreadyStored = Messages::withoutGlobalScopes()
            ->where('communication_channel_id', $channel->id)
            ->where('external_message_id', $wamid)
            ->exists();

        if ($alreadyStored) {
            return;
        }

        UserHelper::runAsAdmin(function () use ($channel, $message, $names, $from, $wamid) {
            $contact = ContactsService::findOrCreateByIdentifier(
                $from,
                self::CONTACT_CHANNEL_TYPE,
                $names[$from] ?? ('+' . $from),
                $channel->iam_account_id
            );

            $thread = self::resolveOpenThread($channel, $contact);

            $type = $message['type'] ?? 'text';
            $body = match ($type) {
                'text'   => data_get($message, 'text.body', ''),
                'button' => data_get($message, 'button.text', '[button]'),
                'interactive' => data_get($message, 'interactive.button_reply.title')
                    ?? data_get($message, 'interactive.list_reply.title', '[interactive]'),
                default  => '[' . $type . ']',
            };

            MessagesService::create([
                'communication_thread_id'  => $thread->id,
                'communication_channel_id' => $channel->id,
                'direction'                => Messages::DIRECTION_INBOUND,
                'content_type'             => 'text',
                'body'                     => $body,
                'external_message_id'      => $wamid,
                'status'                   => 'delivered',
                'iam_account_id'           => $channel->iam_account_id,
                'metadata'                 => [
                    'wa_id'     => $from,
                    'type'      => $type,
                    'timestamp' => $message['timestamp'] ?? null,
                    // Media (image/audio/document/...) arrives as an id that needs the tenant's token to download
                    'media'     => $type !== 'text' ? ($message[$type] ?? null) : null,
                    'context'   => $message['context'] ?? null,
                    'referral'  => $message['referral'] ?? null,
                ],
            ]);

            ThreadsService::update($thread->uuid, ['last_message_at' => now()]);
        });
    }

    /**
     * The connected, active Channel that owns this WhatsApp number. Runs without AuthorizationScope since a webhook
     * has no user/tenant context - the Channel's own iam_account_id supplies it.
     */
    private static function resolveChannel(?string $phoneNumberId, ?string $wabaId): ?Channels
    {
        if (!$phoneNumberId) {
            return null;
        }

        $matches = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('type', Channels::TYPE_WHATSAPP)
            ->where('is_active', true)
            ->get()
            ->filter(fn (Channels $channel) => (string) data_get($channel->configuration, 'phone_number_id') === $phoneNumberId)
            ->values();

        // Fail closed: a number must belong to exactly one channel. More than one match means ambiguous ownership, and
        // routing to either could deliver one tenant's customer messages to another.
        if ($matches->count() !== 1) {
            if ($matches->count() > 1) {
                Log::error('[WhatsAppWebhookService] Several Channels claim the same phone number, event dropped', [
                    'phone_number_id' => $phoneNumberId,
                    'channel_ids'     => $matches->pluck('id')->all(),
                ]);
            }

            return null;
        }

        $channel = $matches->first();

        // The event must also come from the WhatsApp Business Account the channel was verified against
        if ($wabaId && data_get($channel->configuration, 'waba_id') && (string) data_get($channel->configuration, 'waba_id') !== $wabaId) {
            Log::warning('[WhatsAppWebhookService] WABA id does not match the Channel, event dropped', [
                'phone_number_id' => $phoneNumberId,
                'channel_id'      => $channel->id,
            ]);

            return null;
        }

        return $channel;
    }

    private static function resolveOpenThread(Channels $channel, Contacts $contact): Threads
    {
        $thread = Threads::withoutGlobalScope(AuthorizationScope::class)
            ->where('communication_channel_id', $channel->id)
            ->where('communication_contact_id', $contact->id)
            ->where('status', 'open')
            ->first();

        if ($thread) {
            return $thread;
        }

        return ThreadsService::create([
            'communication_channel_id' => $channel->id,
            'communication_contact_id' => $contact->id,
            'status'                   => 'open',
            'iam_account_id'           => $channel->iam_account_id,
            'last_message_at'          => now(),
        ]);
    }
}
