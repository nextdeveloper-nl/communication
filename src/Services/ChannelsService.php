<?php

namespace NextDeveloper\Communication\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use JsonException;
use NextDeveloper\Commons\Database\Models\Validatables;
use NextDeveloper\Commons\Services\ValidatablesService;
use NextDeveloper\Communication\Actions\Channels\Send;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\AbstractServices\AbstractChannelsService;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible for managing communication channels and their verification.
 *
 * @package NextDeveloper\Communication\Services
 */
class ChannelsService extends AbstractChannelsService
{
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    private const VERIFICATION_CODE_MIN = 100000;
    private const VERIFICATION_CODE_MAX = 999999;
    private const CHANNEL_MODEL_TYPE = 'NextDeveloper\Communication\Database\Models\Channels';

    public static function create($data)
    {
        try {
            $data['config'] = json_decode($data['config'], true);
        } catch (Exception $e) {
            throw new InvalidArgumentException('The config value should be json.');
        }

        return parent::create($data);
    }

    /**
     * Validates that all required channel fields exist in the platform fields.
     *
     * @param Channels $channel The channel object containing fields to validate
     * @param AvailableChannels $platform The platform object to validate against
     * @return bool Returns true if all required fields are present and valid
     * @throws InvalidArgumentException If input parameters are invalid
     */
    public static function validateChannelFields(Channels $channel, AvailableChannels $platform): bool
    {
        self::validateInputParameters($channel, $platform);

        $channelFields = $channel->config;
        $platformFields = $platform->config;

        if (self::areFieldsEmpty($channelFields)) {
            return true;
        }

        if (self::areFieldsEmpty($platformFields)) {
            return false;
        }

        try {

            if (empty($channelFields)) {
                return true;
            }

            $requiredFields = self::getRequiredFields($platformFields);

            if (empty($requiredFields)) {
                return true;
            }

            return self::validateRequiredFields($channelFields, $requiredFields);
        } catch (Exception $e) {
            Log::error('[ChannelsService::validate]', [
                'error' => $e->getMessage(),
                'channel_id' => $channel->id,
                'platform_id' => $platform->id
            ]);
            return false;
        }
    }

    /**
     * Sends a verification code to the user.
     *
     * @param string $ref Channel reference
     * @return bool True if the code was sent successfully
     */
    public static function sendCode(string $ref): bool
    {
        try {
            $channel = self::getByRef($ref);
            $code = self::generateVerificationCode();

            self::createValidatable($channel, $code);
            self::sendVerificationCode($channel, $code);

            return true;
        } catch (Exception $e) {
            Log::error('[ChannelsService::send]', [
                'error' => $e->getMessage(),
                'ref' => $ref
            ]);
            return false;
        }
    }

    /**
     * Verifies a user-provided verification code.
     *
     * @param array $data Data array containing the verification code
     * @param string $ref Channel reference
     * @return bool True if the code is valid and verified
     */
    public static function verifyCode(array $data, string $ref): bool
    {
        try {
            $channel = self::getByRef($ref);
            $validatable = self::findValidValidationCode($channel, $data['code']);

            if (!$validatable) {
                return false;
            }

            return self::markAsVerified($channel, $validatable);
        } catch (Exception $e) {
            Log::error('[ChannelsService::verify]', [
                'error' => $e->getMessage(),
                'refs' => $ref
            ]);
            return false;
        }
    }

    /**
     * Validates input parameters for channel fields validation.
     *
     * @throws InvalidArgumentException
     */
    private static function validateInputParameters(Channels $channel, AvailableChannels $platform): void
    {
        if (is_null($channel->config) || is_null($platform->config)) {
            throw new InvalidArgumentException('Channel and platform fields cannot be null');
        }
    }

    /**
     * Checks if fields are empty.
     */
    private static function areFieldsEmpty(mixed $fields): bool
    {
        return empty($fields) || $fields === '[]' || $fields === '{}';
    }

    /**
     * Decodes JSON fields with error handling.
     *
     * @throws JsonException
     */
    private static function decodeJsonFields(string $fields): array
    {
        $decoded = json_decode($fields, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Fields must decode to arrays');
        }

        return $decoded;
    }

    /**
     * Extracts required fields from platform fields.
     */
    private static function getRequiredFields(array $platformFields): array
    {
        return array_filter($platformFields, function ($field) {
            if (is_array($field)) {
                return isset($field['required']) && $field['required'] === true;
            }
            return $field === 'required';
        });
    }

    /**
     * Validates that all required fields exist and are not empty.
     */
    private static function validateRequiredFields(array $channelFields, array $requiredFields): bool
    {
        foreach ($requiredFields as $field => $value) {
            if (!array_key_exists($field, $channelFields)) {
                return false;
            }

            if (empty($channelFields[$field]) && $channelFields[$field] !== 0 && $channelFields[$field] !== '0') {
                return false;
            }
        }


        return true;
    }

    /**
     * Generates a random verification code.
     */
    private static function generateVerificationCode(): int
    {
        return rand(self::VERIFICATION_CODE_MIN, self::VERIFICATION_CODE_MAX);
    }

    /**
     * Creates a validatable record for the verification code.
     */
    private static function createValidatable(Channels $channel, int $code): void
    {
        ValidatablesService::create([
            'validation_code' => $code,
            'object_id' => $channel->id,
            'object_type' => self::CHANNEL_MODEL_TYPE,
        ]);

        Log::info('[ChannelsService::send] Verification code generated', [
            'channel_id' => $channel->id,
            'code' => $code
        ]);
    }

    /**
     * Sends the verification code through the channel.
     */
    private static function sendVerificationCode(Channels $channel, int $code): void
    {
        Send::dispatch($channel, 'Your verification code is: ' . $code);

        Log::info('[ChannelsService::send] Verification code sent', [
            'channel_id' => $channel->id
        ]);
    }

    /**
     * Finds a valid validation code for the channel.
     */
    private static function findValidValidationCode(Channels $channel, string $code): ?Validatables
    {
        return Validatables::withoutGlobalScope(AuthorizationScope::class)
            ->where('is_used', false)
            ->where('object_id', $channel->id)
            ->where('object_type', self::CHANNEL_MODEL_TYPE)
            ->where('validation_code', $code)
            ->first();
    }

    /**
     * Marks the channel and validation code as verified.
     */
    private static function markAsVerified(Channels $channel, Validatables $validatable): bool
    {
        $channel->is_verified = true;
        $channel->save();

        $validatable->is_used = true;
        $validatable->save();

        Log::info('[ChannelsService::verify] Verification code verified', [
            'channel_id' => $channel->id,
            'code' => $validatable->validation_code
        ]);

        return true;
    }

    public static function createMailChannelForUser(Users $user): Channels
    {
        $communicationAvailableChannel = AvailableChannels::withoutGlobalScope(AuthorizationScope::class)
            ->where('name', 'Email')
            ->first();

        $userChannels = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_user_id', $user->id)
            ->where('communication_available_channel_id', $communicationAvailableChannel->id)
            ->first();

        if(!$userChannels) {
            $userChannels = Channels::create([
                'communication_available_channel_id' => $communicationAvailableChannel->id,
                'iam_user_id' =>  $user->id,
                'iam_account_id' => UserHelper::currentAccount($user)->id,
                'config' => []
            ]);
        }

        return $userChannels;
    }
}
