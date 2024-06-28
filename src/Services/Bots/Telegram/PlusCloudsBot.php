<?php

namespace NextDeveloper\Communication\Services\Bots\Telegram;

class PlusCloudsBot extends AbstractBot
{
    public function __construct($token){
        $bot = new Api($token);
        $this->setBot($bot);
    }

    public function handle()
    {
        $updates = $this->bot->getUpdates();

        foreach ($updates as $update) {
            if (array_key_exists('my_chat_member', $update->toArray())) {
                continue;
            }

            $updateId = $this->bot->getUpdateId($update);
            if (self::checkUpdateId($updateId)) {
                continue;
            }

            //  Here first save all incoming messages to the database
            //  Here we will find the conversation and add 1 to unread_message_count
        }

        dispatch(new Welcoming($this->bot));
        dispatch(new Answer($this->bot));
    }
}
