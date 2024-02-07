<?php

namespace NextDeveloper\Communication\Database\Traits;

use NextDeveloper\Communication\Actions\SMS\Deliver;
use NextDeveloper\Communication\Services\Delivery\Twillio;
use Twilio\Exceptions\TwilioException;

/**
 * Trait SendSMS
 *
 * This trait provides methods for sending SMS messages.
 */
trait SendSMS
{
    /**
     * Send an SMS message and schedule it for delivery.
     *
     * @param string $body The body of the SMS message.
     * @param \DateTimeInterface|null $schedule The scheduled time for delivery.
     */
    public function sendSMS(string $body, $schedule = null): true
    {
        // If no schedule is provided, default to current time.
        if (!$schedule) {
            $schedule = now();
        }

        // TODO: Create an SMS record in the database.
        // Create an SMS record in the database.
//        $sms = SMS::create([
//            'body' => $body,
//            'from' => config('sms.from'),
//            'deliver_at' => $schedule,
//            'to' => $this->phone_number,
//        ]);

        // Dispatch a job to send the SMS.
//        Deliver::dispatch($sms);

        // Return true to indicate that the SMS was scheduled for delivery.
        return true;
    }

    /**
     * Send an SMS without recording it in the database.
     *
     * @param string $body The body of the SMS message.
     * @return bool True if the SMS was sent successfully, false otherwise.
     * @throws TwilioException Thrown if there is an error while sending the SMS.
     */
    public function sendSmsWithoutRecord(string $body): bool
    {
        // Create a new Twilio instance and send the SMS directly.
        $sms = (new Twillio())->sendDirect($this->phone_number, $body);
        return $sms;
    }
}
