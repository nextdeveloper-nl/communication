<?php

namespace NextDeveloper\Communication\Services\EmailDelivery;

use NextDeveloper\Communication\Database\Models\Emails;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Communication\Exceptions\DeliveryMethodNotFoundException;

/**
 * Class Mailgun
 *
 * This class is responsible for sending emails via Mailgun.
 */

class Mailgun
{

    /**
     * @var \Mailgun\Mailgun The Mailgun client for making requests.
     */
    protected $client;

    /**
     * @var string The domain for sending emails.
     */
    protected $domain;


    /**
     * Mailgun constructor.
     *
     * Initializes the Mailgun service with the required API credentials.
     * @throws \Exception
     */
    public function __construct()
    {

        $apiKey = config('communication.services.mailgun.api_key');
        $this->domain = config('communication.services.mailgun.domain');

        // If the API key, domain, or URL is not set, then throw an exception.
        if (empty($apiKey) || empty($this->domain)) {
            throw new DeliveryMethodNotFoundException('Mailgun API key, or domain is not set.');
        }

        try {
            // Create the Mailgun client.
            $this->client = \Mailgun\Mailgun::create($apiKey);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Mailgun: ' . $e->getMessage());
            throw new DeliveryMethodNotFoundException('Failed to initialize Mailgun: ' . $e->getMessage());
        }
    }


    /**
     * Send an email via Mailgun.
     *
     * @param Emails $email The email to be sent.
     * @return void
     */
    public function send(Emails $email): void
    {
        try {


            // Ensure 'to' is properly formatted
            $to = $this->formatRecipients($email->to);

            if (empty($to)) {
                throw new \Exception('No recipients specified for the email.');
            }

            // Prepare the email parameters
            $parameters = [
                'from'              => $email->from_email_address,
                'to'                => $to,
                'subject'           => $email->subject,
                'text'              => $email->body,
                'html'              => $email->body,
                'o:tracking'        => true,
                'o:tracking-clicks' => true,
                'o:tracking-opens'  => true,
                'v:origin'          => $email->uuid,
            ];

            // Add CC if it exists
            if (!empty($email->cc)) {
                $parameters['cc'] = $this->formatRecipients($email->cc);
            }

            // Add BCC if it exists
            if (!empty($email->bcc)) {
                $parameters['bcc'] = $this->formatRecipients($email->bcc);
            }

            // Add attachments if they exist
            if (!empty($email->attachments) && is_array($email->attachments)) {
                $parameters['attachment'] = [];

                foreach ($email->attachments as $attachment) {
                    if (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $parameters['attachment'][] = [
                            'filePath' => $attachment['path'],
                            'filename' => $attachment['name'] ?? basename($attachment['path']),
                        ];
                    }
                }
            }

            // Add custom headers if they exist
            if (!empty($email->headers) && is_array($email->headers)) {
                foreach ($email->headers as $key => $value) {
                    $parameters['h:' . $key] = $value;
                }
            }

            // Send the email via Mailgun
            $response = $this->client->messages()->send($this->domain, $parameters);

            // If the email is delivered, then save the delivery results and the delivered_at timestamp
            $email->delivery_results = [
                'message'   => 'Email sent successfully via Mailgun',
            ];
            $email->delivered_at = now();
            $email->saveQuietly();

            Log::info('Email sent successfully via Mailgun', ['email_id' => $email->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send email via Mailgun: ' . $e->getMessage(), ['email_id' => $email->id]);

            // Update the email record with the error
            $email->delivery_results = [
                'message'   => $e->getMessage(),
            ];
            $email->saveQuietly();
        }
    }

    /**
     * Format recipients from array to comma-separated string if needed
     *
     * @param mixed $recipients
     * @return string
     */
    protected function formatRecipients($recipients): string
    {
        if (is_array($recipients)) {
            return implode(',', $recipients);
        }

        return (string) $recipients;
    }

    /**
     * Process events from Mailgun and update email records
     *
     * This method retrieves events from Mailgun and updates the corresponding email records
     * with the event data.
     *
     * @param array $options Optional parameters to customize the event processing
     * @param callable|null $progressCallback Optional callback for progress reporting
     * @return int Total number of events processed
     */
    public function processEvents(array $options = [], ?callable $progressCallback = null): int
    {
        $totalProcessed = 0;
        $currentPage = 1;
        $processAllPages = $options['process_all_pages'] ?? false;

        try {
            // Get events from Mailgun for the domain
            $queryParams = [
                'limit' => $options['limit'] ?? 300,
                'event' => $options['event'] ?? 'delivered OR failed OR rejected OR opened OR clicked',
            ];


            // Process pages until no more events or limit reached
            do {
                // Get events from the Mailgun API
                $result = $this->client->events()->get($this->domain, $queryParams);

                // Check if we have items in the response
                if (!$result || !method_exists($result, 'getItems')) {
                    Log::warning('No events or invalid response from Mailgun');
                    break;
                }

                $items = $result->getItems();
                $currentPageCount = count($items);

                if (empty($items)) {
                    Log::info('No events found on page ' . $currentPage);
                    break;
                }

                Log::info('Processing ' . $currentPageCount . ' events from page ' . $currentPage);

                // Process each event
                $processedInThisPage = 0;
                foreach ($items as $item) {
                    // Extract event data
                    $userVariables = $item->getUserVariables();

                    if (!isset($userVariables['origin']) || empty($userVariables['origin'])) {
                        continue; // Skip if no origin/uuid to match with
                    }

                    // Find the corresponding email by UUID
                    $email = Emails::withoutGlobalScopes()
                                ->where('uuid', $userVariables['origin'])
                                ->first();

                    if ($email) {
                        // Get existing delivery results or initialize empty array
                        $oldDeliveryResults = $email->delivery_results ?? [];

                        // If delivery_results is not an array, convert it to one
                        if (!is_array($oldDeliveryResults)) {
                            $oldDeliveryResults = [];
                        }

                        // Update the delivery results with the new event data
                        $email->delivery_results = array_merge($oldDeliveryResults, [
                            'status'                => $item->getEvent(),
                            'timestamp'             => $item->getTimestamp(),
                            'delivery_status'       => $item->getDeliveryStatus(),
                            'geo'                   => $item->getGeolocation(),
                            'message'               => $item->getMessage(),
                            'recipient'             => $item->getRecipient(),
                            'severity'              => $item->getSeverity(),
                            'original'              => $item, // Include the original object
                        ]);

                        // Save the updated email record
                        $email->saveQuietly();

                        Log::info('Email event processed', [
                            'email_id' => $email->id,
                            'event' => $item->getEvent(),
                        ]);

                        $processedInThisPage++;
                        $totalProcessed++;
                    }
                }

                // Report progress if callback provided
                if ($progressCallback && $processedInThisPage > 0) {
                    call_user_func($progressCallback, $processedInThisPage);
                }

                // Check if there are more pages to process
                $hasNextPage = false;
                $nextPageUrl = null;

                // For simplicity, we'll only use the getNextUrl method if available
                // Otherwise, we'll assume there's no next page
                try {
                    if (method_exists($result, 'getNextUrl')) {
                        $nextPageUrl = $result->getNextUrl();
                        $hasNextPage = !empty($nextPageUrl);
                    }

                    if ($hasNextPage && $processAllPages) {
                        // Extract page token for next request
                        $parsedUrl = parse_url($nextPageUrl);
                        if (isset($parsedUrl['query'])) {
                            parse_str($parsedUrl['query'], $queryParams);
                            $currentPage++;
                        } else {
                            $hasNextPage = false;
                        }
                    } else {
                        // If not processing all pages, stop after the first page
                        $hasNextPage = false;
                    }
                } catch (\Exception $e) {
                    Log::warning('Error checking for more pages: ' . $e->getMessage());
                    $hasNextPage = false;
                }

            } while ($hasNextPage);

            Log::info('Completed processing Mailgun events. Total processed: ' . $totalProcessed);

        } catch (\Exception $e) {
            Log::error('Failed to process Mailgun events: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        return $totalProcessed;
    }
}
