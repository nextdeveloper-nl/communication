<?php

namespace NextDeveloper\Communication\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Communication\Services\EmailDelivery\Mailgun;
use Illuminate\Support\Facades\Log;

class FetchMailgunEventCommand extends Command
{
    protected $signature = 'nextdeveloper:fetch-mailgun-event
                            {--limit=300 : Number of events to fetch per page}
                            {--events=delivered,failed,rejected,opened,clicked : Comma-separated list of event types to fetch}
                            {--all-pages : Process all pages of events (pagination)}';

    protected $description = 'Fetch and process Mailgun events for email tracking';

    public function handle()
    {
        $this->info('Fetching Mailgun events...');

        try {
            // Get command options
            $limit = (int) $this->option('limit');
            $eventTypes = $this->parseEventTypes($this->option('events'));
            $processAllPages = $this->option('all-pages');


            // Create Mailgun service
            $mailgun = new Mailgun();

            // Set custom options if provided
            $options = [
                'limit' => $limit,
                'event' => implode(' OR ', $eventTypes),
                'process_all_pages' => $processAllPages,
            ];

            // Start progress bar
            $this->output->write("\n");
            $progressBar = $this->output->createProgressBar();
            $progressBar->setFormat(' %current% events processed [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');
            $progressBar->start();

            // Process events with a callback for progress updates
            $totalProcessed = $mailgun->processEvents($options, function($count) use ($progressBar) {
                $progressBar->advance($count);
            });

            // Finish progress bar
            $progressBar->finish();
            $this->output->write("\n\n");

            // Show summary
            $this->info("Mailgun events processed successfully!");
            $this->info("Total events processed: {$totalProcessed}");

        } catch (\Exception $e) {
            $this->error("Error processing Mailgun events: " . $e->getMessage());
            Log::error("Error in FetchMailgunEventCommand: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Parse the event types from the command option
     *
     * @param string $eventsOption
     * @return array
     */
    protected function parseEventTypes(string $eventsOption): array
    {
        $eventTypes = explode(',', $eventsOption);
        $eventTypes = array_map('trim', $eventTypes);
        $eventTypes = array_filter($eventTypes);

        // If no valid event types, use defaults
        if (empty($eventTypes)) {
            return ['delivered', 'failed', 'rejected', 'opened', 'clicked'];
        }

        return $eventTypes;
    }
}
