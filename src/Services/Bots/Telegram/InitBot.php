<?php

namespace NextDeveloper\Communication\Services\Bots\Telegram;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class InitBot
{

    public function __construct(private Api $bot){}

    /**
     * @throws TelegramSDKException
     */
    public function sendMessage($chatId, $message): void
    {
        $this->bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }

    /**
     * @throws TelegramSDKException
     */
    public function getUpdates(): array
    {
        $updates = $this->bot->getUpdates();

        return $updates;
    }


    public function getChatId($update)
    {

        if (!isset($update['message']['from']['id'])) {
            return null;
        }

        return $update['message']['from']['id'];
    }

    public function getMessage($update)
    {

        if (!isset($update['message']['text'])) {
            return null;
        }
        return $update['message']['text'];
    }

    public function getChatIdAndMessage($update): array
    {

        if (!isset($update['message']['from']['id']) || !isset($update['message']['text'])) {
            return [];
        }

        return [
            'chat_id' => $update['message']['from']['id'],
            'message' => $update['message']['text']
        ];
    }

    public function getUserName($update)
    {

        if (!isset($update['message']['from']['username'])) {
            return null;
        }

        return $update['message']['from']['username'];
    }

    public function getFirstName($update)
    {

        if (!isset($update['message']['from']['first_name'])) {
            return null;
        }

        return $update['message']['from']['first_name'];
    }


    public function getLanguage($update)
    {

        if (!isset($update['message']['from']['language_code'])) {
            return null;
        }

        return $update['message']['from']['language_code'];
    }


    public function getUpdateId($update)
    {

        if (!isset($update['update_id'])) {
            return null;
        }

        return $update['update_id'];
    }


    public function getUpdateDate($update)
    {
        if (!isset($update['message']['date'])) {
            return null;
        }

        return $update['message']['date'];
    }

    public function getUpdateType($update)
    {
        if (!isset($update['updateType'])) {
            return null;
        }

        return $update['updateType'];
    }

}

