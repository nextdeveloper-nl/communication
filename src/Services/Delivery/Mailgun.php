<?php

namespace NextDeveloper\Communication\Services\Delivery;

use NextDeveloper\Communication\Database\Models\Emails;

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
     */
    public function __construct()
    {

        $apiKey         = config('communication.services.mailgun.api_key');
        $apiUrl         = config('communication.services.mailgun.api_url');
        $this->domain   = config('communication.services.mailgun.domain');

        //  If the API key, domain, or URL is not set, then throw an exception.
        if (empty($apiKey) || empty($this->url) || empty($this->domain)) {
            throw new \DeliveryMethodNotFoundException('Mailgun api key or domain is not set.');
        }

        //  Create the Mailgun client.
        $this->client = \Mailgun\Mailgun::create($apiKey, $apiUrl);
    }


    /**
     * Send an email via Mailgun.
     *
     * @param Emails $email The email to be sent.
     */
    public function send(Emails $email): void
    {

        //  Send the email via Mailgun.
        $response = $this->client->messages()->send($this->domain, [
            'from'          => $email->from_email_address,
            'to'            => 'ibrahim.yakoub@plusclouds.com',
            'cc'            => $email->cc ?? '',
            'bcc'           => $email->bcc ?? '',
            'subject'       => $email->subject,
            'text'          => $email->body,
            'attachment'    => $email->attachments ?? [],
        ]);


        //  If the email is delivered, then save the delivery results and the delivered_at timestamp.
        if ($response) {
            $email->delivery_results = $response;
            $email->delivered_at = now();
            $email->save();
        }
    }

}
