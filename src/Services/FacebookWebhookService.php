<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Contacts;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\CRM\Services\CampaignsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Ingests inbound Facebook Page webhook events (Click-to-Messenger conversations
 * and Lead Ads form submissions) into the standard Channels/Threads/Messages shape,
 * so CRM agents see both kinds of Facebook-ad-originated customer contact the same
 * way they see any other inbound conversation.
 *
 * There is no authenticated user on a webhook request, so all writes run under
 * UserHelper::runAsAdmin(), matching the Agreement\Services\WebhooksService precedent.
 */
class FacebookWebhookService
{
    private const GRAPH_API_VERSION = 'v19.0';

    /**
     * Handles one `entry[]` element whose `messaging[]` array contains
     * Messenger events (messages and/or ad click referrals).
     */
    public static function ingestMessagingEvent(array $entry): void
    {
        $pageId = $entry['id'] ?? null;
        $channel = self::resolveChannel($pageId);

        if (!$channel) {
            Log::warning('[FacebookWebhookService] No Channel found for Page', ['page_id' => $pageId]);
            return;
        }

        foreach ($entry['messaging'] ?? [] as $event) {
            $psid = $event['sender']['id'] ?? null;

            if (!$psid || empty($event['message'])) {
                // Skip non-message events (delivery receipts, read receipts, etc.)
                continue;
            }

            UserHelper::runAsAdmin(function () use ($channel, $psid, $event) {
                $contact = ContactsService::findOrCreateByIdentifier(
                    $psid,
                    'facebook_messenger',
                    'Facebook User',
                    $channel->iam_account_id
                );

                $thread = self::resolveOpenThread($channel, $contact);

                $adId = data_get($event, 'referral.ad_id') ?? data_get($event, 'message.referral.ad_id');
                $campaign = $adId ? CampaignsService::findByFacebookAdId($adId) : null;

                MessagesService::create([
                    'communication_thread_id'  => $thread->id,
                    'communication_channel_id' => $channel->id,
                    'crm_campaign_id'          => $campaign?->id,
                    'direction'                => Messages::DIRECTION_INBOUND,
                    'content_type'             => 'text/plain',
                    'body'                     => data_get($event, 'message.text', '[attachment]'),
                    'external_message_id'      => data_get($event, 'message.mid'),
                    'status'                   => 'delivered',
                    'iam_account_id'           => $channel->iam_account_id,
                    'metadata'                 => [
                        'psid'       => $psid,
                        'ad_id'      => $adId,
                        'referral'   => $event['referral'] ?? null,
                        'attachments' => data_get($event, 'message.attachments'),
                    ],
                ]);
            });
        }
    }

    /**
     * Handles one `entry[]` element whose `changes[]` array contains a
     * `field === 'leadgen'` Lead Ads form-submission notification.
     *
     * The webhook payload only carries the leadgen_id — the actual form answers
     * must be fetched separately from the Graph API using the Page access token.
     */
    public static function ingestLeadgenEvent(array $entry): void
    {
        foreach ($entry['changes'] ?? [] as $change) {
            if (($change['field'] ?? null) !== 'leadgen') {
                continue;
            }

            $value = $change['value'] ?? [];
            $leadgenId = $value['leadgen_id'] ?? null;
            $pageId = $value['page_id'] ?? $entry['id'] ?? null;
            $adId = $value['ad_id'] ?? null;
            $formId = $value['form_id'] ?? null;

            if (!$leadgenId) {
                continue;
            }

            $channel = self::resolveChannel($pageId);

            if (!$channel) {
                Log::warning('[FacebookWebhookService] No Channel found for Page', ['page_id' => $pageId]);
                continue;
            }

            UserHelper::runAsAdmin(function () use ($channel, $leadgenId, $adId, $formId) {
                $fieldData = self::fetchLeadFieldData($leadgenId, $channel);

                $email = self::extractLeadField($fieldData, ['email']);
                $fullName = self::extractLeadField($fieldData, ['full_name', 'first_name']) ?? 'Facebook Lead';
                $identifier = $email ?? $leadgenId;

                $contact = ContactsService::findOrCreateByIdentifier(
                    $identifier,
                    'facebook_lead_ads',
                    $fullName,
                    $channel->iam_account_id
                );

                $thread = self::resolveOpenThread($channel, $contact);

                $campaign = $adId ? CampaignsService::findByFacebookAdId($adId) : null;

                MessagesService::create([
                    'communication_thread_id'  => $thread->id,
                    'communication_channel_id' => $channel->id,
                    'crm_campaign_id'          => $campaign?->id,
                    'direction'                => Messages::DIRECTION_INBOUND,
                    'content_type'             => 'application/json',
                    'body'                     => 'New Facebook Lead Ad submission',
                    'external_message_id'      => $leadgenId,
                    'status'                   => 'delivered',
                    'iam_account_id'           => $channel->iam_account_id,
                    'metadata'                 => [
                        'form_id'    => $formId,
                        'ad_id'      => $adId,
                        'field_data' => $fieldData,
                    ],
                ]);
            });
        }
    }

    /**
     * Fetches the form answers for a lead from the Graph API. Meta does not
     * include them in the webhook payload itself, only the leadgen_id.
     *
     * @return array<string, string> field name => answer
     */
    private static function fetchLeadFieldData(string $leadgenId, Channels $channel): array
    {
        $accessToken = data_get($channel->credentials, 'page_access_token');

        if (!$accessToken) {
            Log::warning('[FacebookWebhookService] Channel has no page_access_token', ['channel_id' => $channel->id]);
            return [];
        }

        $response = Http::get(
            sprintf('https://graph.facebook.com/%s/%s', self::GRAPH_API_VERSION, $leadgenId),
            ['access_token' => $accessToken]
        );

        if (!$response->successful()) {
            Log::error('[FacebookWebhookService] Failed to fetch lead field_data', [
                'leadgen_id' => $leadgenId,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);
            return [];
        }

        $fields = [];
        foreach ($response->json('field_data', []) as $field) {
            $fields[$field['name']] = $field['values'][0] ?? null;
        }

        return $fields;
    }

    private static function extractLeadField(array $fieldData, array $candidateNames): ?string
    {
        foreach ($candidateNames as $name) {
            if (!empty($fieldData[$name])) {
                return $fieldData[$name];
            }
        }

        return null;
    }

    /**
     * Resolves the connected-Page Channel for an inbound event by its Facebook page_id.
     * Runs without the AuthorizationScope since there is no authenticated user/tenant
     * context on a webhook request — the Channel's own iam_account_id supplies that.
     */
    private static function resolveChannel(?string $pageId): ?Channels
    {
        if (!$pageId) {
            return null;
        }

        return Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('type', Channels::TYPE_FACEBOOK)
            ->where('is_active', true)
            ->get()
            ->first(fn (Channels $channel) => data_get($channel->configuration, 'page_id') === $pageId);
    }

    /**
     * Finds the contact's open thread on this channel, or opens a new one.
     */
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
