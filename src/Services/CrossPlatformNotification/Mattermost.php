<?php

namespace NextDeveloper\Communication\Services\CrossPlatformNotification;

use Illuminate\Support\Facades\Http;

class Mattermost implements CrossPlatformNotificationInterface
{
    protected string $webhookUrl;
    protected string $message;

    /**
     * @throws \Exception
     */
    public function __construct($message)
    {
        $this->webhookUrl = config('communication.services.mattermost.webhook_url');

        if (!$this->webhookUrl)
        {
            throw new \Exception('');
        }

        $this->message = $message;
    }

    public function send(): void
    {
        Http::post($this->webhookUrl, [
            'text' => $this->message,
        ]);
    }

}