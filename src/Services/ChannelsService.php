<?php

namespace NextDeveloper\Communication\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Commons\Database\Models\Validatables;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Commons\Services\ValidatablesService;
use NextDeveloper\Communication\Actions\Channels\Send;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\AbstractServices\AbstractChannelsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for Channels
 *
 * Class ChannelsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class ChannelsService extends AbstractChannelsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    private const VERIFICATION_CODE_MIN = 100000;
    private const VERIFICATION_CODE_MAX = 999999;
    private const CHANNEL_MODEL_TYPE = 'NextDeveloper\Communication\Database\Models\Channels';

    /**
     * Channel types whose inbound events are routed to a Channel by an external id in its configuration
     * (WhatsApp: phone_number_id, Facebook: page_id). Such a channel may only come from the verified connect flows;
     * if the generic create/update accepted it, a tenant could claim another tenant's number or Page without proving
     * ownership and receive that tenant's inbound messages.
     */
    private const WEBHOOK_ROUTED_TYPES = [Channels::TYPE_WHATSAPP, Channels::TYPE_FACEBOOK];

    // Configuration keys that decide where inbound events are routed
    private const ROUTING_KEYS = ['phone_number_id', 'waba_id', 'page_id'];

    // True only while a verified connect flow is creating its Channel
    private static bool $trustedConnect = false;

    /**
     * Runs the callback as a verified connect flow (the token was checked against the external account first).
     */
    private static function asVerifiedConnect(callable $callback)
    {
        $previous = self::$trustedConnect;
        self::$trustedConnect = true;

        try {
            return $callback();
        } finally {
            self::$trustedConnect = $previous;
        }
    }

    /**
     * Generic updates may not change a webhook-routed channel's type or its routing keys.
     */
    public static function update($id, array $data)
    {
        if (!self::$trustedConnect) {
            $channel = Channels::where('uuid', $id)->first();
            $type = $data['type'] ?? $channel?->type;

            if (isset($data['type']) && $channel && $data['type'] !== $channel->type
                && (in_array($data['type'], self::WEBHOOK_ROUTED_TYPES, true) || in_array($channel->type, self::WEBHOOK_ROUTED_TYPES, true))) {
                throw new NotAllowedException('The type of this channel cannot be changed.');
            }

            if ($channel && in_array($type, self::WEBHOOK_ROUTED_TYPES, true) && array_key_exists('configuration', $data)) {
                $new = is_string($data['configuration']) ? (json_decode($data['configuration'], true) ?? []) : (array) $data['configuration'];

                foreach (self::ROUTING_KEYS as $key) {
                    if (data_get($new, $key) !== data_get($channel->configuration, $key)) {
                        throw new NotAllowedException('The ' . $key . ' of a connected channel cannot be changed. Disconnect and connect again.');
                    }
                }
            }
        }

        return parent::update($id, $data);
    }

    public static function create(array $data): Channels
    {
        if (!self::$trustedConnect && in_array($data['type'] ?? null, self::WEBHOOK_ROUTED_TYPES, true)) {
            throw new NotAllowedException('This channel type can only be created through its connect endpoint.');
        }

        foreach (['configuration', 'credentials'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $decoded = json_decode($data[$field], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new InvalidArgumentException("The {$field} value must be valid JSON.");
                }

                $data[$field] = $decoded;
            }
        }

        return parent::create($data);
    }

    /**
     * Validates that all required fields defined by the AvailableChannels config
     * exist and are non-empty in the channel's configuration.
     */
    public static function validateChannelConfiguration(Channels $channel, AvailableChannels $availableChannel): bool
    {
        $channelConfig = $channel->configuration;
        $platformConfig = $availableChannel->config;

        if (empty($channelConfig)) {
            return true;
        }

        if (empty($platformConfig)) {
            return false;
        }

        try {
            $requiredFields = self::getRequiredFields($platformConfig);

            if (empty($requiredFields)) {
                return true;
            }

            return self::validateRequiredFields($channelConfig, $requiredFields);
        } catch (Exception $e) {
            Log::error('[ChannelsService::validateChannelConfiguration]', [
                'error'               => $e->getMessage(),
                'channel_id'          => $channel->id,
                'available_channel_id' => $availableChannel->id,
            ]);

            return false;
        }
    }

    /**
     * Generates and sends a 6-digit verification code to the channel.
     */
    public static function sendCode(string $ref): bool
    {
        try {
            $channel = self::getByRef($ref);
            $code = random_int(self::VERIFICATION_CODE_MIN, self::VERIFICATION_CODE_MAX);

            ValidatablesService::create([
                'validation_code' => $code,
                'object_id'       => $channel->id,
                'object_type'     => self::CHANNEL_MODEL_TYPE,
            ]);

            Send::dispatch($channel, 'Your verification code is: ' . $code);

            Log::info('[ChannelsService::sendCode] Verification code sent', ['channel_id' => $channel->id]);

            return true;
        } catch (Exception $e) {
            Log::error('[ChannelsService::sendCode]', ['error' => $e->getMessage(), 'ref' => $ref]);

            return false;
        }
    }

    /**
     * Verifies the given code and activates the channel on success.
     */
    public static function verifyCode(array $data, string $ref): bool
    {
        try {
            $channel = self::getByRef($ref);

            $validatable = Validatables::withoutGlobalScope(AuthorizationScope::class)
                ->where('is_used', false)
                ->where('object_id', $channel->id)
                ->where('object_type', self::CHANNEL_MODEL_TYPE)
                ->where('validation_code', $data['code'])
                ->first();

            if (!$validatable) {
                return false;
            }

            self::update($channel->uuid, ['is_active' => true]);

            $validatable->is_used = true;
            $validatable->save();

            Log::info('[ChannelsService::verifyCode] Channel verified', ['channel_id' => $channel->id]);

            return true;
        } catch (Exception $e) {
            Log::error('[ChannelsService::verifyCode]', ['error' => $e->getMessage(), 'ref' => $ref]);

            return false;
        }
    }

    /**
     * Self-service Facebook Page connect: the customer supplies a Page ID and a
     * Page Access Token they generated themselves in Meta Business Suite. We verify
     * the token actually grants access to that Page, subscribe the Page to our
     * app's webhook (messages + leadgen), and persist the Channel under the
     * current tenant. No Meta App Review / OAuth redirect is involved — the
     * customer's own token does the authorizing.
     */
    public static function connectFacebookPage(array $data): Channels
    {
        // Trim: tokens pasted from a terminal or JSON often carry a trailing space or newline that Meta rejects
        $pageId = trim($data['page_id']);
        $accessToken = trim($data['page_access_token']);

        // Reject a pipeline the caller cannot see before anything is created or subscribed
        FacebookLeadImportService::assertPipelineAccessible($data['flow_pipeline_id'] ?? null);

        // Verify the token FIRST: only a caller who proves control of the Page may learn whether it is already connected
        // (otherwise the "already connected" answer would let anyone probe which Pages other tenants use).
        $pageInfo = self::verifyFacebookPageToken($pageId, $accessToken);

        // A Page routes its inbound events to exactly one channel. Checked across all tenants (without the scope): a Page
        // already connected by another account cannot be connected again, and the same account reconnecting only
        // refreshes the token of its existing channel instead of creating a second one.
        $existing = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('type', Channels::TYPE_FACEBOOK)
            ->get()
            ->filter(fn (Channels $channel) => (string) data_get($channel->configuration, 'page_id') === $pageId)
            ->values();

        // Channels of OTHER accounts: a working one blocks the connect, one whose token no longer reaches the Page (former
        // admin, revoked app) is disconnected automatically, because the caller just proved control of the Page.
        $others = $existing->filter(fn (Channels $channel) => (int) $channel->iam_account_id !== (int) UserHelper::currentAccount()->id);
        $existing = $existing->reject(fn (Channels $channel) => $others->contains('id', $channel->id))->values();

        self::releaseStaleChannels(
            $others,
            fn (Channels $other) => self::tokenReaches(
                sprintf('https://graph.facebook.com/%s/%s', self::FACEBOOK_GRAPH_VERSION, $pageId),
                data_get($other->credentials, 'page_access_token'),
                $pageId
            ),
            'This Facebook Page is already connected to an account.'
        );

        self::subscribeFacebookPageToWebhook($pageId, $accessToken);

        if ($ownChannel = $existing->first()) {
            // Reconnect by the owner: new token on the existing channel (routing keys unchanged), no duplicate channel
            return self::asVerifiedConnect(fn () => self::update($ownChannel->uuid, [
                'credentials' => ['page_access_token' => $accessToken],
                'is_active'   => true,
            ]));
        }

        $channel = self::asVerifiedConnect(fn () => self::create([
            'name'           => $data['name'] ?? ('Facebook: ' . ($pageInfo['name'] ?? $pageId)),
            'type'           => Channels::TYPE_FACEBOOK,
            'configuration'  => ['page_id' => $pageId, 'page_name' => $pageInfo['name'] ?? null],
            'credentials'    => ['page_access_token' => $accessToken],
            'is_active'      => true,
            'iam_account_id' => UserHelper::currentAccount()->id,
        ]));

        // Create the lead importer for this Page so its leads are imported right away. A failure here must not undo the
        // connection (messages/webhook still work); it is logged and the importer can be created later.
        try {
            FacebookLeadImportService::createImporterForChannel($channel, $data);
        } catch (Exception $e) {
            Log::error('[ChannelsService::connectFacebookPage] Importer creation failed', [
                'channel_id' => $channel->id,
                // Class only: HTTP exception messages can contain the request URL with the access token
                'error'      => get_class($e),
            ]);
        }

        return $channel;
    }

    /**
     * Unsubscribes the Page from our webhook and removes the Channel. Best-effort
     * on the Graph API call — a token the customer already revoked will fail here,
     * but we still remove the Channel so it stops being resolved for new events.
     */
    public static function disconnectFacebookPage(string $ref): void
    {
        $channel = self::getByRef($ref);
        $pageId = data_get($channel->configuration, 'page_id');
        $accessToken = data_get($channel->credentials, 'page_access_token');

        if ($pageId && $accessToken) {
            try {
                Http::delete(
                    sprintf('https://graph.facebook.com/v19.0/%s/subscribed_apps', $pageId),
                    ['access_token' => $accessToken]
                );
            } catch (Exception $e) {
                Log::warning('[ChannelsService::disconnectFacebookPage] Graph API unsubscribe failed', [
                    'channel_id' => $channel->id,
                    // Class only: HTTP exception messages can contain the request URL with the access token
                    'error'      => get_class($e),
                ]);
            }
        }

        // Stop the Page's lead importers from pulling with the removed channel
        FacebookLeadImportService::pauseImportersForChannel($channel);

        self::delete($ref);
    }

    /**
     * Confirms the token actually grants access to the given Page (rather than
     * some other Page, or a User token that doesn't cover it), by asking the
     * Graph API for that Page's own id/name using the supplied token.
     *
     * @return array{id: string, name: string}
     */
    private static function verifyFacebookPageToken(string $pageId, string $accessToken): array
    {
        $response = Http::get(
            sprintf('https://graph.facebook.com/v19.0/%s', $pageId),
            ['fields' => 'id,name', 'access_token' => $accessToken]
        );

        if (!$response->successful() || $response->json('id') != $pageId) {
            // Meta's own error (code and message) is what tells the user why; surface it and log it
            $metaError = $response->json('error.message') ?? ('returned id ' . var_export($response->json('id'), true));

            Log::warning('[ChannelsService::verifyFacebookPageToken] Page token check failed', [
                'page_id' => $pageId,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);

            throw new NotAllowedException(
                'Could not verify this Page Access Token against Page ' . $pageId
                . '. Make sure the token was generated for this exact Page and has not expired.'
                . ' Meta said (HTTP ' . $response->status() . '): ' . $metaError
            );
        }

        return $response->json();
    }

    /**
     * Subscribes the Page to our Facebook App's webhook for the fields we ingest
     * (Messenger conversations + Lead Ads form submissions), so Meta starts
     * delivering events for it to our webhook URL.
     */
    private static function subscribeFacebookPageToWebhook(string $pageId, string $accessToken): void
    {
        $response = Http::post(
            sprintf('https://graph.facebook.com/v19.0/%s/subscribed_apps', $pageId),
            [
                // Only leadgen by default: subscribing to 'messages' needs the pages_messaging permission, which Meta
                // refuses (#200) for apps without it. Add messages via config communication.services.facebook.subscribed_fields.
                'subscribed_fields' => config('communication.services.facebook.subscribed_fields', 'leadgen'),
                'access_token'      => $accessToken,
            ]
        );

        if (!$response->successful()) {
            Log::error('[ChannelsService::subscribeFacebookPageToWebhook] Subscription failed', [
                'page_id' => $pageId,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);

            throw new NotAllowedException(
                'Facebook accepted the token but refused to subscribe this Page to our webhook: ' . $response->body()
            );
        }
    }

    /**
     * Self-service WhatsApp Business number connect (Meta Cloud API), multi-tenant: every tenant connects its own number
     * with its own token. We verify the token really reaches that number, make sure no other channel already owns the
     * number (inbound events are routed by phone_number_id, so a number must belong to exactly one channel), subscribe
     * our Meta App to the tenant's WhatsApp Business Account and create the Channel under the current account.
     */
    public static function connectWhatsAppNumber(array $data): Channels
    {
        // Trim: tokens pasted from a terminal or JSON often carry a trailing space or newline that Meta rejects
        $phoneNumberId = trim($data['phone_number_id']);
        $wabaId = trim($data['waba_id']);
        $accessToken = trim($data['access_token']);

        // Verify the token FIRST: only a caller who proves control of the number may learn whether it is already connected.
        $numberInfo = self::verifyWhatsAppNumber($phoneNumberId, $accessToken);

        // A number can be connected once; checked across all tenants (without the scope) so nobody can take over
        // inbound routing of a number that still works for another account. A channel whose token no longer reaches the
        // number (migrated away, token revoked) is stale and is disconnected automatically, since the caller just
        // proved control of the number.
        $holders = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('type', Channels::TYPE_WHATSAPP)
            ->get()
            ->filter(fn (Channels $channel) => (string) data_get($channel->configuration, 'phone_number_id') === $phoneNumberId);

        self::releaseStaleChannels(
            $holders,
            fn (Channels $holder) => self::tokenReaches(
                sprintf('https://graph.facebook.com/%s/%s', \NextDeveloper\Communication\Channels\WhatsAppCloud::GRAPH_API_VERSION, $phoneNumberId),
                data_get($holder->credentials, 'access_token'),
                $phoneNumberId
            ),
            'This WhatsApp number is already connected to an account.'
        );

        self::subscribeWhatsAppAccount($wabaId, $accessToken);

        return self::asVerifiedConnect(fn () => self::create([
            'name'           => $data['name'] ?? ('WhatsApp: ' . ($numberInfo['display_phone_number'] ?? $phoneNumberId)),
            'type'           => Channels::TYPE_WHATSAPP,
            'configuration'  => [
                'phone_number_id'      => $phoneNumberId,
                'waba_id'              => $wabaId,
                'display_phone_number' => $numberInfo['display_phone_number'] ?? null,
                'verified_name'        => $numberInfo['verified_name'] ?? null,
            ],
            'credentials'    => ['access_token' => $accessToken],
            'is_active'      => true,
            'iam_account_id' => UserHelper::currentAccount()->id,
        ]));
    }

    /**
     * Removes the channel. The app is unsubscribed from the WhatsApp Business Account only when no other WhatsApp
     * channel of that account is left, because the subscription is per account, not per number. Best-effort on the
     * Graph call: a token the customer already revoked fails, but the channel is removed anyway.
     */
    public static function disconnectWhatsAppNumber(string $ref): void
    {
        $channel = self::getByRef($ref);
        $wabaId = data_get($channel->configuration, 'waba_id');
        $accessToken = data_get($channel->credentials, 'access_token');

        $othersOnSameAccount = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('type', Channels::TYPE_WHATSAPP)
            ->where('id', '!=', $channel->id)
            ->get()
            ->contains(fn (Channels $other) => data_get($other->configuration, 'waba_id') === $wabaId);

        if ($wabaId && $accessToken && !$othersOnSameAccount) {
            try {
                Http::withToken($accessToken)
                    ->delete(sprintf('https://graph.facebook.com/%s/%s/subscribed_apps', \NextDeveloper\Communication\Channels\WhatsAppCloud::GRAPH_API_VERSION, $wabaId));
            } catch (Exception $e) {
                Log::warning('[ChannelsService::disconnectWhatsAppNumber] Graph API unsubscribe failed', [
                    'channel_id' => $channel->id,
                    // Class only: the exception message can contain request details
                    'error'      => get_class($e),
                ]);
            }
        }

        self::delete($ref);
    }

    /**
     * Confirms the token reaches the given number by asking the Graph API for it.
     *
     * @return array{id: string, display_phone_number?: string, verified_name?: string}
     */
    private static function verifyWhatsAppNumber(string $phoneNumberId, string $accessToken): array
    {
        $response = Http::withToken($accessToken)->get(
            sprintf('https://graph.facebook.com/%s/%s', \NextDeveloper\Communication\Channels\WhatsAppCloud::GRAPH_API_VERSION, $phoneNumberId),
            ['fields' => 'id,display_phone_number,verified_name']
        );

        if (!$response->successful() || $response->json('id') != $phoneNumberId) {
            $metaError = $response->json('error.message') ?? ('returned id ' . var_export($response->json('id'), true));

            Log::warning('[ChannelsService::verifyWhatsAppNumber] Number check failed', [
                'phone_number_id' => $phoneNumberId,
                'status'          => $response->status(),
                'graph_error'     => $response->json('error') ?? mb_substr($response->body(), 0, 500),
            ]);

            throw new NotAllowedException(
                'Could not verify this access token against WhatsApp number ' . $phoneNumberId
                . '. Make sure the token belongs to the account that owns the number and has not expired.'
                . ' Meta said (HTTP ' . $response->status() . '): ' . $metaError
            );
        }

        return $response->json();
    }

    /**
     * Subscribes our Meta App to the tenant's WhatsApp Business Account so Meta delivers its numbers' events to our
     * webhook.
     */
    private static function subscribeWhatsAppAccount(string $wabaId, string $accessToken): void
    {
        $response = Http::withToken($accessToken)->post(
            sprintf('https://graph.facebook.com/%s/%s/subscribed_apps', \NextDeveloper\Communication\Channels\WhatsAppCloud::GRAPH_API_VERSION, $wabaId)
        );

        if (!$response->successful()) {
            Log::error('[ChannelsService::subscribeWhatsAppAccount] Subscription failed', [
                'waba_id'     => $wabaId,
                'status'      => $response->status(),
                'graph_error' => $response->json('error') ?? mb_substr($response->body(), 0, 500),
            ]);

            throw new NotAllowedException(
                'WhatsApp accepted the token but refused to subscribe this account to our webhook: '
                . ($response->json('error.message') ?? $response->status())
            );
        }
    }

    private const FACEBOOK_GRAPH_VERSION = 'v19.0';

    /**
     * True when the given token can still read the external object (Page / number) at $url, i.e. the channel holding it
     * still works. Any failure counts as "no longer reaches".
     */
    private static function tokenReaches(string $url, ?string $token, string $expectedId): bool
    {
        if (!$token) {
            return false;
        }

        try {
            $response = Http::withToken($token)->get($url, ['fields' => 'id']);
        } catch (Exception $e) {
            return false;
        }

        return $response->successful() && (string) $response->json('id') === $expectedId;
    }

    /**
     * For channels that hold the external object the caller has just proven control of: a working one blocks the
     * connect (one message for every case, so nothing else leaks), a stale one is disconnected automatically.
     * Disconnecting = importers paused and the channel soft-deleted (its threads and messages stay), so routing no
     * longer sees it and the object can be connected again. Runs as admin because the stale channel belongs to another
     * account that the caller cannot see.
     */
    private static function releaseStaleChannels($channels, callable $isLive, string $blockedMessage): void
    {
        foreach ($channels as $stale) {
            if ($stale->is_active && $isLive($stale)) {
                throw new NotAllowedException($blockedMessage);
            }

            Log::warning('[ChannelsService] Disconnecting a stale channel taken over by a verified connect', [
                'stale_channel_id' => $stale->id,
                'type'             => $stale->type,
            ]);

            UserHelper::runAsAdmin(function () use ($stale) {
                FacebookLeadImportService::pauseImportersForChannel($stale);
                self::delete($stale->uuid);
            });
        }
    }

    private static function getRequiredFields(array $platformConfig): array
    {
        return array_filter($platformConfig, function ($field) {
            return is_array($field)
                ? isset($field['required']) && $field['required'] === true
                : $field === 'required';
        });
    }

    private static function validateRequiredFields(array $channelConfig, array $requiredFields): bool
    {
        foreach ($requiredFields as $fieldKey => $fieldDefinition) {
            if (!array_key_exists($fieldKey, $channelConfig)) {
                return false;
            }

            $value = $channelConfig[$fieldKey];

            if (empty($value) && $value !== 0 && $value !== '0') {
                return false;
            }
        }

        return true;
    }
}