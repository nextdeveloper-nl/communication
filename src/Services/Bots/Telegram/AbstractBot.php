<?php

namespace NextDeveloper\Communication\Services\Bots\Telegram;

use NextDeveloper\Communication\Database\Models\Conversations;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

class AbstractBot
{
    protected $bot;

    public function setBot($bot)
    {
        $this->bot = $bot;
    }

    public function sendMessage($chatId, $message): void
    {
        $this->bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }

    public function getUpdates(): array
    {
        $updates = $this->bot->getUpdates();

        return $updates;
    }

    public static function getChatId($update)
    {
        if (!isset($update['message']['from']['id'])) {
            return null;
        }

        return $update['message']['from']['id'];
    }

    public static function getMessage($update)
    {
        if (!isset($update['message']['text'])) {
            return null;
        }
        return $update['message']['text'];
    }

    public static function getChatIdAndMessage($update): array
    {
        if (!isset($update['message']['from']['id']) || !isset($update['message']['text'])) {
            return [];
        }

        return [
            'chat_id' => $update['message']['from']['id'],
            'message' => $update['message']['text']
        ];
    }

    public static function getUserName($update)
    {
        if (!isset($update['message']['from']['username'])) {
            return null;
        }

        return $update['message']['from']['username'];
    }

    public static function getFirstName($update)
    {
        if (!isset($update['message']['from']['first_name'])) {
            return null;
        }

        return $update['message']['from']['first_name'];
    }

    public static function getLanguage($update)
    {
        if (!isset($update['message']['from']['language_code'])) {
            return null;
        }

        return $update['message']['from']['language_code'];
    }


    public static function getUpdateId($update)
    {
        if (!isset($update['update_id'])) {
            return null;
        }

        return $update['update_id'];
    }


    public static function getUpdateDate($update)
    {
        if (!isset($update['message']['date'])) {
            return null;
        }

        return $update['message']['date'];
    }

    public static function getUpdateType($update)
    {
        if (!isset($update['updateType'])) {
            return null;
        }

        return $update['updateType'];
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
