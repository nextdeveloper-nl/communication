<?php

namespace NextDeveloper\Communication\Services\Bots\Telegram;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use NextDeveloper\Commons\Database\Models\Validatables;
use NextDeveloper\Communication\Database\Models\Conversations;
use NextDeveloper\Communication\Database\Models\Users;
use NextDeveloper\Communication\Services\AIService;
use NextDeveloper\Communication\Services\ConversationsService;
use NextDeveloper\I18n\Helpers\i18n;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class WelcomeBot
{

    /**
     * Create a conversation and handle incoming updates from Telegram bot.
     *
     */
    public static function getInstance($token): void
    {
        try {
            $bot = new InitBot(new Api($token));
            $updates = $bot->getUpdates();

            foreach ($updates as $update) {

                if (array_key_exists('my_chat_member', $update->toArray())) {
                    continue;
                }

                $updateId = $bot->getUpdateId($update);
                if (self::checkUpdateId($updateId)) {
                    continue;
                }


                self::handleUpdate($bot, $update);
            }

        } catch (TelegramSDKException | GuzzleException | Exception $e) {
            self::handleException($e);
        }
    }

    /**
     * Find a user by chat ID.
     *
     * @param mixed $chatId
     * @return Users|null
     */
    private static function findUser(mixed $chatId): ?Users
    {
        return Users::withoutGlobalScopes()
            ->where('telegram_id', $chatId)
            ->first();
    }

    /**
     * Handle new user by validating the message and sending a response.
     *
     * @param InitBot $bot
     * @param mixed $chatId
     * @param string $message
     * @param string $language
     * @param $updateId
     * @throws GuzzleException
     * @throws TelegramSDKException
     * @throws Exception
     */
    private static function handleNewUser(InitBot $bot, mixed $chatId, string $message, string $language, $updateId): void
    {
        $aiService = new AIService();
        $newUserSession = $aiService->createSession();
        $response = $aiService->sendMessage($newUserSession['uuid'], self::generateAIRequestMessage($message));
        $aiResponse = $response['message'];

        if (self::isValidCode($aiResponse)) {
            $user = self::findValidatableUser($aiResponse);

            if ($user) {
                Users::create([
                    'telegram_id' => $chatId,
                    'iam_user_id' => $user->iam_user_id,
                ]);

                self::sendWelcomeMessage($bot, $chatId, $language);
                self::saveMessage("Welcome to PlusClouds, you can now start using our services", $user->iam_user_id, 1, $updateId);
            } else {
                self::promptForCode($bot, $chatId, "Invalid code, please try again", $language);
                self::saveMessage("Invalid code, please try again", null, 1, $updateId);
            }
            return;
        }

        self::promptForCode($bot, $chatId, $aiResponse, $language);
        self::saveMessage($aiResponse, null, 1, $updateId);
    }

    /**
     * Generate a request message for AI validation.
     *
     * @param string $message
     * @return string
     */
    private static function generateAIRequestMessage(string $message): string
    {
        return "If this message contains a 6-digit validation code, return only that code. If this message "
            . "does not contain a 6-digit validation code, generate the following message and return it: "
            . "'Welcome to PlusCloudsBot! To use PlusCloudsBot, please enter the 6-digit code. If you don't "
            . "have it, you can generate it from your PlusClouds panel.'\n\n" . $message;
    }

    /**
     * Handle existing user by sending their message to AI service and responding.
     *
     * @param InitBot $bot
     * @param mixed $chatId
     * @param string $message
     * @param mixed $iamUserId
     * @param string $language
     * @param mixed $user
     * @param $updateId
     * @throws GuzzleException
     * @throws TelegramSDKException
     * @throws Exception
     */
    private static function handleExistingUser(InitBot $bot, mixed $chatId, string $message, mixed $iamUserId, string $language, mixed $user, $updateId): void
    {
        $aiService = new AIService();
        $userSession = $user->communication_session_id;

        if (!$userSession) {
            $session = $aiService->createSession($iamUserId);
            $user->update(['communication_session_id' => $session['uuid']]);
            $userSession = $session['uuid'];
        }

        $response = $aiService->sendMessage($userSession, $message);
        $botMessage = $response['message'];
        $bot->sendMessage($chatId, $botMessage);
        self::saveMessage($botMessage, $iamUserId, 1, $updateId);
    }

    /**
     * Validate if a message contains a 6-digit code.
     *
     * @param string $message
     * @return bool
     */
    private static function isValidCode(string $message): bool
    {
        return strlen($message) == 6 && is_numeric($message);
    }

    /**
     * Find a validatable user by code.
     *
     * @param string $code
     * @return Validatables|null
     */
    private static function findValidatableUser(string $code): ?Validatables
    {
        return Validatables::withoutGlobalScope(AuthorizationScope::class)
            ->latest()
            ->where('validation_code', $code)
            ->first();
    }

    /**
     * Send a welcome message to the user.
     *
     * @param InitBot $bot
     * @param mixed $chatId
     * @param string $language
     * @throws TelegramSDKException
     */
    private static function sendWelcomeMessage(InitBot $bot, mixed $chatId, string $language): void
    {
        $bot->sendMessage($chatId, i18n::t("Welcome to PlusClouds, you can now start using our services", $language));
    }

    /**
     * Prompt the user to enter a valid code.
     *
     * @param InitBot $bot
     * @param mixed $chatId
     * @param string $message
     * @param string $language
     * @throws TelegramSDKException
     */
    private static function promptForCode(InitBot $bot, mixed $chatId, string $message, string $language): void
    {
        $bot->sendMessage($chatId, i18n::t($message, $language));
    }

    /**
     * Save a message to the database.
     *
     * @param string $message
     * @param mixed $iamUserId
     * @param int $direction
     * @param mixed $updateId
     * @throws Exception
     */
    private static function saveMessage(string $message, mixed $iamUserId, int $direction, mixed $updateId): void
    {
        $data = [
            'message' => $message,
            'iam_user_id' => $iamUserId,
            'direction' => $direction,
            'update_id' => $updateId,
        ];
        ConversationsService::create($data);
    }

    /**
     * Handle exceptions by logging them.
     *
     * @param Exception $e
     */
    private static function handleException(Exception $e): void
    {
        logger()->error('[Communication::ConversationsService] ' . $e->getMessage());
    }

    /**
     * Handle updates from the Telegram bot.
     *
     * @param InitBot $bot
     * @param mixed $update
     * @throws GuzzleException
     * @throws TelegramSDKException
     */
    private static function handleUpdate(InitBot $bot, mixed $update): void
    {

        $updateId = $bot->getUpdateId($update);
        $chatId = $bot->getChatId($update);
        $message = $bot->getMessage($update);
        $language = $bot->getLanguage($update);

        if ($message === "/start") {
            $bot->sendMessage($chatId, "Welcome to PlusClouds, please enter 6 digit code to authenticate your account.");
            self::saveMessage("Welcome to PlusClouds, please enter 6 digit code to authenticate your account.", null, 0, $updateId);
            return;
        }

        $user = self::findUser($chatId);
        if (!$user) {
            self::handleNewUser($bot, $chatId, $message, $language, $updateId);
            self::saveMessage($message, null, 0, $updateId);
            return;
        }

        $iamUserId = $user->iam_user_id;
        self::handleExistingUser($bot, $chatId, $message, $iamUserId, $language, $user, $updateId);
        self::saveMessage($message, $iamUserId, 0, $updateId);
    }

    /**
     * Check if the update ID already exists in the database.
     *
     * @param mixed $updateId
     * @return Conversations|null
     */
    private static function checkUpdateId(mixed $updateId): ?Conversations
    {
        return Conversations::withoutGlobalScope(AuthorizationScope::class)
            ->where('update_id', $updateId)
            ->first();
    }

}
