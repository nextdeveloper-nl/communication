<?php

namespace NextDeveloper\Communication\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Actions\Emails\Deliver;
use NextDeveloper\Communication\Database\Models\Emails;

class EmailsDeliveryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nextdeveloper:emails-deliver
                            {--limit=50 : Maximum number of emails to process in a single run}
                            {--dry-run : Show what would be done without actually sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and deliver pending emails from the communication_emails table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting email delivery process...');

        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in dry-run mode. No emails will be sent.');
        }

        try {
            // Get pending emails that are due for delivery
            $pendingEmails = $this->getPendingEmails($limit);

            $count = $pendingEmails->count();
            $this->info("Found {$count} emails to process");

            if ($count === 0) {
                $this->info('No pending emails to deliver.');
                return 0;
            }

            $bar = $this->output->createProgressBar($count);
            $bar->start();

            $success = 0;
            $failed = 0;

            foreach ($pendingEmails as $email) {
                try {
                    if (!$dryRun) {
                        (new Deliver($email))->handle();
                    } else {
                        $this->line("\nWould deliver email ID: {$email->id}, Subject: {$email->subject}, To: " . implode(', ', $email->to));
                    }
                    $success++;
                } catch (\Exception $e) {
                    $this->error("\nError processing email ID {$email->id}: " . $e->getMessage());
                    Log::error("Error processing email ID {$email->id}: " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    $failed++;
                }

                $bar->advance();
            }

            $bar->finish();

            $this->newLine();
            $this->info("Email delivery completed: {$success} succeeded, {$failed} failed");

            return 0;
        } catch (\Exception $e) {
            $this->error('Email delivery process failed: ' . $e->getMessage());
            Log::error('Email delivery process failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Get pending emails that are due for delivery.
     *
     * @param int $limit Maximum number of emails to retrieve
     * @return Collection
     */
    protected function getPendingEmails(int $limit): Collection
    {
        return Emails::whereNull('delivered_at')
            ->where(function ($query) {
                $query->whereNull('deliver_at')
                    ->orWhere('deliver_at', '<=', Carbon::now());
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

}
