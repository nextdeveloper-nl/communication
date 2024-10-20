<?php

namespace NextDeveloper\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CrossPlatformNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $message;
    protected array $platforms;

    public function __construct(string $message, array $platforms = [])
    {
        $this->message = $message;
        $this->platforms = $platforms ?: config('communication.defaults.platforms');
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        foreach ($this->platforms as $platformClass) {
            if (!class_exists($platformClass)) {
                Log::error("Notification class $platformClass does not exist.");
                continue;
            }

            $platform = new $platformClass($this->message);
            $platform->send();
        }
    }
}