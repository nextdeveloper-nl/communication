<?php

namespace NextDeveloper\Communication\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use NextDeveloper\Commons\Database\Models\ExternalServices;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Communication\Exceptions\TokenRefreshedException;
use NextDeveloper\Communication\Helpers\ChannelHelper;
use NextDeveloper\Communication\Services\AbstractServices\AbstractMessagesService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\CRM\Database\Models\EmailTemplates;
use NextDeveloper\IAM\Helpers\UserHelper;
use Illuminate\Support\Facades\Log;

/**
 * This class is responsible from managing the data for Messages
 *
 * Class MessagesService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class MessagesService extends AbstractMessagesService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Creates a message and enforces the thread XOR campaign constraint.
     * Also bumps last_message_at on the thread when the message belongs to one.
     */
    public static function create(array $data): Messages
    {
        $hasThread   = !empty($data['communication_thread_id']);
        $hasCampaign = !empty($data['crm_campaign_id']);
        $hasChannel  = !empty($data['communication_channel_id']);

        // Standalone transactional messages (no thread, no campaign) are allowed
        // when a specific channel is provided. All other combinations must have
        // exactly one of thread or campaign.
        if (!$hasChannel && $hasThread === $hasCampaign) {
            throw new InvalidArgumentException(
                'A message must belong to exactly one of: communication_thread_id or crm_campaign_id, '
                . 'or must specify a communication_channel_id for standalone delivery.'
            );
        }

        // The API accepts UUIDs for FK fields; resolve them to integer IDs before insert.
        if ($hasChannel && !is_int($data['communication_channel_id'])) {
            $data['communication_channel_id'] = Channels::withoutGlobalScope(AuthorizationScope::class)
                ->where('uuid', $data['communication_channel_id'])
                ->firstOrFail()
                ->id;
        }

        if (!empty($data['communication_thread_id']) && !is_int($data['communication_thread_id'])) {
            $data['communication_thread_id'] = Threads::where('uuid', $data['communication_thread_id'])
                ->firstOrFail()
                ->id;
        }

        // If subject is provided as a top-level field, store it in metadata.
        if (!empty($data['subject'])) {
            $data['metadata'] = array_merge($data['metadata'] ?? [], ['subject' => $data['subject']]);
            unset($data['subject']);
        }

        $message = parent::create($data);

        // AbstractMessagesService::create() treats every "*_id" field as a foreign
        // key and runs it through DatabaseHelper::uuidToId(), which returns null for
        // any plain (non-UUID) string. external_message_id is not a relation — it's
        // an opaque external id (Facebook mid, Chatwoot message id, ...) — so a
        // plain string value gets silently wiped. Restore it here from the original
        // $data, which parent::create() received by value and could not mutate.
        if (!empty($data['external_message_id']) && $message->external_message_id !== $data['external_message_id']) {
            $message->external_message_id = $data['external_message_id'];
            $message->save();
        }

        if ($message->communication_thread_id) {
            $thread = Threads::find($message->communication_thread_id);
            if ($thread) {
                ThreadsService::touchLastMessageAt($thread);
            }
        }

        return $message;
    }

    /**
     * Marks a message as read by the contact.
     */
    public static function markAsRead(string $ref): Messages
    {
        return self::update($ref, ['read_at' => now()]);
    }

    /**
     * Marks a message as successfully delivered.
     */
    public static function markAsDelivered(string $ref): Messages
    {
        return self::update($ref, [
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Marks a message as failed and records the failure reason.
     */
    public static function markAsFailed(string $ref, string $reason): Messages
    {
        return self::update($ref, [
            'status'         => 'failed',
            'failed_at'      => now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Creates and immediately delivers a transactional email message.
     * Resolves the channel from its UUID, records the message, and dispatches it.
     */
    public static function sendEmail(array $data): Messages
    {
        $channel = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $data['communication_channel_id'])
            ->firstOrFail();

        $message = self::create([
            'communication_channel_id' => $channel->id,
            'direction'                => 1,
            'content_type'             => 'text/html',
            'body'                     => $data['body'],
            'recipient'                => $data['recipient'],
            'status'                   => 'queued',
            'iam_account_id'           => UserHelper::currentAccount()->id,
            'metadata'                 => [
                'subject' => $data['subject'],
                'cc'      => $data['cc'] ?? [],
            ],
        ]);

        self::deliver($message);

        return $message->fresh();
    }

    /**
     * Returns all queued messages that are due for delivery.
     * A null deliver_at means deliver immediately; a set value is delivered
     * once that timestamp has passed.
     */
    public static function getDueForDelivery(): Collection
    {
        return Messages::where('status', 'queued')
            ->where(function ($query) {
                $query->whereNull('deliver_at')
                      ->orWhere('deliver_at', '<=', now());
            })
            ->get();
    }

    /**
     * Delivers a message through its preferred channel.
     *
     * Resolution order:
     *   1. message's own communication_channel_id  (explicit preference)
     *   2. thread's communication_channel_id       (inherited from conversation)
     *   3. account's highest-priority active email channel (fallback)
     *
     * Gmail/Google Workspace channels are capped at 50 deliveries per day.
     * When the cap is reached the message is queued and rescheduled to a
     * random non-consecutive time slot on the next available day.
     *
     * Updates message status to 'sent' on success or 'failed' on error.
     */
    public static function deliver(Messages $message): void
    {
        $channel = self::resolveChannel($message);

        if (!$channel) {
            Log::warning('[MessagesService::deliver] No channel resolved for message', [
                'message_id' => $message->id,
            ]);

            self::markAsFailed($message->uuid, 'No delivery channel available');
            return;
        }

        // Enforce the 50-email/day cap for Gmail and Google Workspace channels.
        if (self::isGoogleChannel($channel)) {
            $sentToday = self::countDeliveredToday($channel);

            if ($sentToday >= 50) {
                self::scheduleForNextAvailableSlot($message, $channel);

                Log::info('[MessagesService::deliver] Gmail daily cap reached — message rescheduled', [
                    'message_id'  => $message->id,
                    'channel_id'  => $channel->id,
                    'sent_today'  => $sentToday,
                    'deliver_at'  => $message->fresh()->deliver_at,
                ]);

                return;
            }
        }

        // Resolve handler class: prefer AvailableChannels DB record, fall back to built-in map.
        $class = null;

        $available = ChannelHelper::getAvailableChannelByType($channel->type);

        if ($available) {
            $class = ChannelHelper::getChannelClass($available);
        }

        if (!$class) {
            $class = self::builtInChannelClass($channel->type);
        }

        if (!$class) {
            self::markAsFailed($message->uuid, 'Channel handler class not found for type: ' . $channel->type);
            return;
        }

        try {
            // Prefer the stored recipient address; fall back to the sender's email.
            $recipient = $message->recipient
                ?? Users::find($message->sent_by_user_id)?->email;

            // Normalise metadata: guard against double-encoded JSON strings stored as text.
            $metadata = $message->metadata;
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true) ?? [];
            }
            $metadata = $metadata ?? [];

            $subject = data_get($metadata, 'subject') ?: '(no subject)';

            // Wrap the body in an email template when crm_email_template_id is set in metadata.
            $body = self::applyTemplate($message->body, $metadata, $subject);

            Log::debug('[MessagesService::deliver] Payload before send', [
                'message_id'          => $message->id,
                'metadata'            => $metadata,
                'subject'             => $subject,
                'recipient'           => $recipient,
                'template_applied'    => data_get($metadata, 'crm_email_template_id') !== null,
            ]);

            $processor = new $class(channel: $channel);
            $processor->send([
                'subject' => $subject,
                'message' => $body,
                'to'      => $recipient,
                'cc'      => data_get($metadata, 'cc', []),
            ]);

            self::markAsDelivered($message->uuid);
        } catch (\NextDeveloper\Communication\Exceptions\TokenRefreshedException $e) {
            // Token was refreshed and saved — re-queue so the next delivery cycle retries with the new token.
            self::update($message->uuid, [
                'status'     => 'queued',
                'deliver_at' => now(),
            ]);

            Log::info('[MessagesService::deliver] Token refreshed — message re-queued', [
                'message_id' => $message->id,
                'channel_id' => $channel->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('[MessagesService::deliver] Delivery failed', [
                'message_id' => $message->id,
                'channel_id' => $channel->id,
                'error'      => $e->getMessage(),
            ]);

            self::markAsFailed($message->uuid, $e->getMessage());
        }
    }

    /**
     * Returns true when the channel is backed by a Google Workspace external service.
     * The link is: communication_channels.configuration['external_service_uuid']
     * → common_external_services.uuid where code = 'google_workspace'.
     */
    private static function isGoogleChannel(Channels $channel): bool
    {
        $externalServiceUuid = data_get($channel->configuration, 'external_service_uuid');

        if (!$externalServiceUuid) {
            return false;
        }

        return ExternalServices::where('uuid', $externalServiceUuid)
            ->where('code', 'google_workspace')
            ->exists();
    }

    /**
     * Counts messages successfully delivered through a channel today.
     * Uses delivered_at (set by markAsDelivered) as the authoritative timestamp.
     */
    private static function countDeliveredToday(Channels $channel): int
    {
        return Messages::where('communication_channel_id', $channel->id)
            ->whereIn('status', ['delivered', 'sent'])
            ->whereDate('delivered_at', now()->toDateString())
            ->count();
    }

    /**
     * Finds the next calendar day with fewer than 50 sent/scheduled messages for
     * the given channel, then picks a random time slot that is not within 15 minutes
     * of any already-scheduled message on that day. Updates the message record in
     * place with the computed deliver_at; status remains 'queued'.
     */
    private static function scheduleForNextAvailableSlot(Messages $message, Channels $channel): void
    {
        $dailyCap = 50;
        $date     = now()->addDay()->startOfDay();

        // Walk forward until we find a day below the cap.
        while (true) {
            $delivered = Messages::where('communication_channel_id', $channel->id)
                ->whereIn('status', ['delivered', 'sent'])
                ->whereDate('delivered_at', $date->toDateString())
                ->count();

            $queued = Messages::where('communication_channel_id', $channel->id)
                ->where('status', 'queued')
                ->whereDate('deliver_at', $date->toDateString())
                ->count();

            if ($delivered + $queued < $dailyCap) {
                break;
            }

            $date->addDay();
        }

        // Collect timestamps of already-queued messages on that day to avoid clustering.
        $occupied = Messages::where('communication_channel_id', $channel->id)
            ->where('status', 'queued')
            ->whereDate('deliver_at', $date->toDateString())
            ->whereNotNull('deliver_at')
            ->pluck('deliver_at')
            ->map(fn ($t) => Carbon::parse($t)->timestamp)
            ->sort()
            ->values()
            ->toArray();

        // Delivery window: 08:00–22:00 on the target day.
        $windowStart = $date->copy()->setTime(8, 0)->timestamp;
        $windowEnd   = $date->copy()->setTime(22, 0)->timestamp;

        $deliverAt = Carbon::createFromTimestamp(
            self::findRandomSlot($windowStart, $windowEnd, $occupied, minGapSeconds: 900)
        );

        Messages::where('id', $message->id)->update([
            'deliver_at' => $deliverAt,
            'status'     => 'queued',
        ]);
    }

    /**
     * Picks a random timestamp inside [windowStart, windowEnd] that is at least
     * minGapSeconds away from every occupied timestamp. Falls back to appending
     * after the last occupied slot if no free slot is found within maxAttempts.
     */
    private static function findRandomSlot(
        int $windowStart,
        int $windowEnd,
        array $occupied,
        int $minGapSeconds
    ): int {
        $maxAttempts = 200;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $candidate = rand($windowStart, $windowEnd);

            $free = true;
            foreach ($occupied as $ts) {
                if (abs($candidate - $ts) < $minGapSeconds) {
                    $free = false;
                    break;
                }
            }

            if ($free) {
                return $candidate;
            }
        }

        // Fallback: append after the last queued slot with a random extra gap.
        if (!empty($occupied)) {
            $after = max($occupied) + $minGapSeconds + rand(0, $minGapSeconds);
            return min($after, $windowEnd);
        }

        return rand($windowStart, $windowEnd);
    }

    /**
     * Maps known channel types to their built-in handler classes.
     * Used as a fallback when no AvailableChannels DB record exists for the type.
     */
    private static function builtInChannelClass(string $type): ?string
    {
        $map = [
            'smtp'             => \NextDeveloper\Communication\Channels\Smtp::class,
            'gmail'            => \NextDeveloper\Communication\Channels\Gmail::class,
            'google_workspace' => \NextDeveloper\Communication\Channels\Gmail::class,
            'mattermost'       => \NextDeveloper\Communication\Channels\Mattermost::class,
            'sms'              => \NextDeveloper\Communication\Channels\Sms::class,
            Channels::TYPE_FACEBOOK => \NextDeveloper\Communication\Channels\FacebookMessenger::class,
        ];

        return $map[$type] ?? null;
    }

    /**
     * Resolves the delivery channel for a message using the three-level fallback.
     */
    private static function resolveChannel(Messages $message): ?Channels
    {
        // 1. Explicit channel preference on the message itself
        // withoutGlobalScope so background workers (no auth session) can still resolve the channel
        if ($message->communication_channel_id) {
            $channel = Channels::withoutGlobalScope(AuthorizationScope::class)
                ->find($message->communication_channel_id);
            if ($channel) {
                return $channel;
            }
        }

        // 2. Channel inherited from the thread
        if ($message->communication_thread_id) {
            $thread = Threads::withoutGlobalScope(AuthorizationScope::class)
                ->find($message->communication_thread_id);
            if ($thread?->communication_channel_id) {
                $channel = Channels::withoutGlobalScope(AuthorizationScope::class)
                    ->find($thread->communication_channel_id);
                if ($channel) {
                    return $channel;
                }
            }
        }

        // 3. Account's highest-priority active email channel
        return ChannelHelper::getPrimaryForAccount($message->iam_account_id, 'email');
    }

    /**
     * Wraps $body inside a CRM email template when 'crm_email_template_id' is present in metadata.
     *
     * The template's content field is expected to contain a {{content}} placeholder.
     * The template's subject field is used as the fallback subject when the message
     * metadata does not already specify one.
     *
     * If the template is not found or has no {{content}} placeholder, the original
     * body is returned unchanged so delivery still proceeds.
     */
    private static function applyTemplate(string $body, array &$metadata, string &$subject): string
    {
        $templateId = data_get($metadata, 'crm_email_template_id');

        if (!$templateId) {
            return $body;
        }

        // Templates belong to CRM — bypass the authorization scope so the job/command
        // context (which runs as admin) can always resolve the template.
        $template = EmailTemplates::withoutGlobalScopes()
            ->where('uuid', $templateId)
            ->first();

        if (!$template) {
            Log::warning('[MessagesService::applyTemplate] Template not found', [
                'crm_email_template_id' => $templateId,
            ]);
            return $body;
        }

        // Use the template's subject when the message has no explicit subject.
        if ($subject === '(no subject)' && !empty($template->subject)) {
            $subject = $template->subject;
        }

        $rendered = str_replace('{{content}}', $body, $template->content ?? '');

        // If the template had no {{content}} token, fall back to the raw body.
        if ($rendered === ($template->content ?? '')) {
            Log::warning('[MessagesService::applyTemplate] Template has no {{content}} placeholder — using raw body', [
                'crm_email_template_id' => $templateId,
            ]);
            return $body;
        }

        return $rendered;
    }
}