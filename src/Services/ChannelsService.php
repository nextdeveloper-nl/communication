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

    public static function create(array $data): Channels
    {
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
        $pageId = $data['page_id'];
        $accessToken = $data['page_access_token'];

        $pageInfo = self::verifyFacebookPageToken($pageId, $accessToken);

        self::subscribeFacebookPageToWebhook($pageId, $accessToken);

        return self::create([
            'name'           => $data['name'] ?? ('Facebook: ' . ($pageInfo['name'] ?? $pageId)),
            'type'           => Channels::TYPE_FACEBOOK,
            'configuration'  => ['page_id' => $pageId, 'page_name' => $pageInfo['name'] ?? null],
            'credentials'    => ['page_access_token' => $accessToken],
            'is_active'      => true,
            'iam_account_id' => UserHelper::currentAccount()->id,
        ]);
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
                    'error'      => $e->getMessage(),
                ]);
            }
        }

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
            throw new NotAllowedException(
                'Could not verify this Page Access Token against Page ' . $pageId
                . '. Make sure the token was generated for this exact Page and has not expired.'
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
                'subscribed_fields' => 'messages,leadgen',
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