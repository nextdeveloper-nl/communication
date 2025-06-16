<?php

namespace NextDeveloper\Communication\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use NextDeveloper\Communication\Actions\Emails\Deliver;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Database\Models\Emails;
use NextDeveloper\Communication\Helpers\ChannelHelper;
use NextDeveloper\IAM\Helpers\UserHelper;

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
        UserHelper::setAdminAsCurrentUser();

        // Log the start of the command
        $this->info('Starting email delivery process...');

        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in dry-run mode. No emails will be sent.');
        }

        try {
            // Get pending emails that are due for delivery
            $pendingEmails = ChannelHelper::getPendingEmails($limit);

            $count = $pendingEmails->count();
            $this->info("Found {$count} emails to process");

            if ($count === 0) {
                $this->info('No pending emails to deliver.');
                return 0;
            }

            $result = $this->processEmails($pendingEmails, $dryRun);

            $this->newLine();
            $this->info("Email delivery completed: {$result['success']} succeeded, {$result['failed']} failed");

            return 0;
        } catch (\Exception $e) {
            $this->error("\nEmail delivery process failed: " . $e->getMessage());
            ChannelHelper::logError('Email delivery process failed', $e);
            return 1;
        }
    }

    /**
     * Process a collection of emails for delivery.
     *
     * @param Collection $emails
     * @param bool $dryRun
     * @return array
     */
    protected function processEmails(Collection $emails, bool $dryRun): array
    {
        $bar = $this->output->createProgressBar($emails->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($emails as $email) {
            try {
                if (!$dryRun) {
                    $this->deliverEmail($email);
                } else {
                    $this->line("\nWould deliver email ID: {$email->id}, Subject: {$email->subject}, To: " . implode(', ', $email->to));
                }
                $success++;
            } catch (\Exception $e) {
                $this->error("\nError processing email ID {$email->id}: " . $e->getMessage());
                ChannelHelper::logError("Error processing email ID {$email->id}", $e);
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();

        return [
            'success' => $success,
            'failed' => $failed
        ];
    }

    /**
     * Deliver a single email using the appropriate channel.
     *
     * @param Emails $email
     * @throws \Exception
     */
    protected function deliverEmail(Emails $email): void
    {
        if ($email->communication_channel_id) {
            $this->deliverViaSpecificChannel($email);
        } else {
            ChannelHelper::deliverViaDefaultChannel($email);
        }

        // Mark the email as delivered
        ChannelHelper::markEmailAsDelivered($email);
    }


    /**
     * Deliver an email via a specific channel.
     *
     * @param Emails $email
     * @throws \Exception
     */
    protected function deliverViaSpecificChannel(Emails $email): void
    {
        $channel = ChannelHelper::getChannel($email);
        if (!$channel) {
            throw new \Exception(__METHOD__ . ": Channel with ID {$email->communication_channel_id} not found");
        }

        if (!$channel->is_active)
        {
            throw new \Exception(__METHOD__ . ": Channel with ID {$email->communication_channel_id} is not active");
        }

        $availableChannel = ChannelHelper::getAvailableChannel($channel, $email);
        if (!$availableChannel) {
            throw new \Exception(__METHOD__ . ": Available channel with ID {$channel->communication_available_channel_id} not found");
        }

        $channelClass = ChannelHelper::getChannelClass($availableChannel, $email);
        if (!$channelClass) {
            throw new \Exception(__METHOD__ . ": Class {$availableChannel->class} not found");
        }

        // Use Laravel's service container to resolve the channel class
        $channelInstance = App::makeWith($channelClass, ['config' => $channel->config]);

        if (!method_exists($channelInstance, 'send')) {
            throw new \Exception(__METHOD__ . ": Method send not found in class {$channelClass}");
        }

        $channelInstance->send($email);
    }
}
