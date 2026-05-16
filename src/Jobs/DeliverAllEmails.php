<?php

namespace NextDeveloper\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Communication\Services\MessagesService;

class DeliverAllEmails extends AbstractAction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $messages = MessagesService::getDueForDelivery();
        $total    = $messages->count();

        if ($total === 0) {
            $this->setFinished('No queued messages due for delivery.');
            return;
        }

        Log::info('[DeliverAllEmails] Starting delivery run', ['total' => $total]);

        $delivered = 0;
        $failed    = 0;

        foreach ($messages as $message) {
            try {
                MessagesService::deliver($message);
                $delivered++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error('[DeliverAllEmails] Failed to deliver message', [
                    'message_id' => $message->id,
                    'error'      => $e->getMessage(),
                ]);
            }

            $this->setProgress(
                (int) ceil(($delivered + $failed) / $total * 100),
                "Delivered {$delivered} / {$total}"
            );
        }

        Log::info('[DeliverAllEmails] Delivery run complete', [
            'delivered' => $delivered,
            'failed'    => $failed,
        ]);

        $this->setFinished("Delivered {$delivered}, failed {$failed} out of {$total} messages.");
    }
}
