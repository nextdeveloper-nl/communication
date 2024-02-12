<?php

namespace NextDeveloper\Communication\Services\Delivery;

use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * Class Twillio
 *
 * This class provides methods to interact with the Twilio API for sending SMS messages.
 */
class Twillio
{

    /**
     * @var Client $client An instance of the Twilio Client used for API communication.
     */
    protected Client $client;

    /**
     * Twillio constructor.
     *
     * Initializes the Twilio client using the provided Twilio Account SID and Auth Token.
     *
     * @throws \DeliveryMethodNotFoundException Thrown when Twilio SID or token is not set.
     * @throws ConfigurationException Thrown when there is a configuration error.
     */
    public function __construct()
    {
        $sid    = config('communication.services.twilio.sid'); // Fetch Twilio Account SID from configuration.
        $token  = config('communication.services.twilio.token'); // Fetch Twilio Auth Token from configuration.

        // Check if SID or token is empty.
        if (empty($sid) || empty($token)) {
            throw new \DeliveryMethodNotFoundException('Twilio sid or token is not set.');
        }

        // Initialize Twilio client with SID and token.
        $this->client = new Client($sid, $token);
    }

    /**
     * Send an SMS via Twilio.
     *
     * @param SMS $sms An object representing the SMS message to be sent.
     *
     * @throws TwilioException Thrown when there is an error during Twilio API communication.
     */
    public function send(SMS $sms): void
    {
        // Send an SMS message using a Twilio client.
        $response = $this->client->messages->create(
            $sms->to,
            [
                'from' => config('communication.services.twilio.from'), // Fetch Twilio phone number from configuration.
                'body' => $sms->body // Set the body of the SMS message.
            ]
        );

        // Update the SID of the SMS object with the SID of the sent message.
        $sms->update([
            'sid' => $response->sid
        ]);
    }

    /**
     * Send an SMS directly via Twilio without using an SMS object.
     *
     * @param string $to The recipient's phone number.
     * @param string $body The body of the SMS message.
     *
     * @return bool True if the message was successfully sent, false otherwise.
     * @throws TwilioException
     */
    public function sendDirect(string $to, string $body): bool
    {
        // Send an SMS message directly using a Twilio client.
        $response = $this->client->messages->create(
            $to,
            [
                'from' => config('communication.services.twilio.from'), // Fetch Twilio phone number from configuration.
                'body' => $body // Set the body of the SMS message.
            ]
        );

        // Check if the message was successfully sent (HTTP status code 201).
        if ($this->client->lastResponse->getStatusCode() == 201) {
            return true; // Return true if a message was successfully sent.
        }

        return false; // Return false if message sending failed.
    }

}