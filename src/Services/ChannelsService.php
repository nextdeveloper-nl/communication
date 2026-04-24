<?php

namespace NextDeveloper\Communication\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use NextDeveloper\Commons\Database\Models\Validatables;
use NextDeveloper\Commons\Services\ValidatablesService;
use NextDeveloper\Communication\Actions\Channels\Send;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\AbstractServices\AbstractChannelsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

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