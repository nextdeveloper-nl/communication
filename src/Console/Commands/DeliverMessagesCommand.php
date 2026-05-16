<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Communication\Services\MessagesService;
use NextDeveloper\IAM\Helpers\UserHelper;

class DeliverMessagesCommand extends Command
{
    protected $signature = 'nextdeveloper:deliver-messages
                            {--dry-run : List due messages without actually delivering them}';

    protected $description = 'Deliver all queued messages that are due, through their own channels';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $messages = MessagesService::getDueForDelivery();
        $total    = $messages->count();

        if ($total === 0) {
            $this->info('No messages due for delivery.');
            return 0;
        }

        $this->info("Found {$total} message(s) due for delivery.");

        if ($this->option('dry-run')) {
            $this->warn('Dry-run mode — no messages will be sent.');
            $this->table(
                ['ID', 'Channel ID', 'Recipient', 'Status', 'Deliver At'],
                $messages->map(fn ($m) => [
                    $m->id,
                    $m->communication_channel_id,
                    $m->recipient,
                    $m->status,
                    $m->deliver_at,
                ])->toArray()
            );
            return 0;
        }

        $bar       = $this->output->createProgressBar($total);
        $delivered = 0;
        $failed    = 0;

        $bar->start();

        foreach ($messages as $message) {
            try {
                MessagesService::deliver($message);
                $delivered++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed message #{$message->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done — delivered: {$delivered}, failed: {$failed}, total: {$total}.");

        return $failed > 0 ? 1 : 0;
    }
}
