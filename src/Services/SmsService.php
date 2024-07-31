<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Communication\Services\Delivery\Twillio;
use Twilio\Exceptions\TwilioException;

/**
 * Class SmsService
 *
 * This class is responsible for sending SMS messages.
 */
class SmsService
{
    /**
     * Send an SMS message.
     *
     * @param string $to The phone number to send the SMS to.
     * @param string $body The body of the SMS message.
     * @return bool True if the SMS was sent successfully, false otherwise.
     * @throws TwilioException
     */
    public static function send(string $to, string $body): bool
    {
        // Send the SMS message using the Twilio service.

    }

    /**
     * @throws TwilioException
     */
    public static function sendDirect(string $to, string $body): bool
    {
        // Create a new Twilio instance and send the SMS directly.
        return (new Twillio())->sendDirect($to, $body);
    }

}
