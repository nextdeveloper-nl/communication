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
                'from'     => $email->from_email_address,
                'to'       => $to,
                'subject'  => $email->subject,
                'text'     => $email->body,
                'html'     => $email->body,
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
            $email->delivery_results = json_encode([
                'status' => 'sent',
                'message' => 'Email sent successfully via Mailgun',
                'response' => $response
            ]);
            $email->delivered_at = now();
            $email->save();

            Log::info('Email sent successfully via Mailgun', ['email_id' => $email->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send email via Mailgun: ' . $e->getMessage(), ['email_id' => $email->id]);

            // Update the email record with the error
            $email->delivery_results = json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            $email->save();
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
}
