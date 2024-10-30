<?php

namespace NextDeveloper\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Actions\Channels\Send;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\IAM\Database\Models\Users;

class CrossPlatformNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Users $model;
    protected string $message;


    public function __construct(Users $model, string $message)
    {
        $this->model        = $model;
        $this->message      = $message;
    }


    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $channels = (new Communicate($this->model))->getNotificationPlatforms();

        foreach ($channels as $channel) {
            $send = new Send($channel, $this->message);
            $send->handle();
        }
    }
}