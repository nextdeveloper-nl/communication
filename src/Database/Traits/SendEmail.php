<?php

namespace NextDeveloper\Communication\Database\Traits;

use NextDeveloper\Communication\Database\Models\Emails;
use NextDeveloper\Communication\Jobs\DeliverAllEmails;

/**
 * This traits handles the email sending process for the customer
 */
trait SendEmail
{
    /**
     *
     * This function will take the default view and the default content and then send the email.
     *
     * This function and the default template should only be limited with 1 content only. If you need to send multiple
     * content, then you need to use the sendWithView function.
     *
     * @param $subject
     * @param $body
     * @param null $schedule
     */
    public function sendEmail($subject, $body, $schedule = null)
    {
        if(!$schedule) {
            $schedule = now();
        }

        /**
         * This function will take subject and body, then save it to database with a is_sent = 0 flag.
         */
        $email  = Emails::create([
            'subject'               => $subject,
            'body'                  => $body,
            'from_email_address'    => config('mail.from.address'),
            'deliver_at'            => $schedule,
            'delivered_at'          => null,
        ]);

        /**
         * This function will trigger the job to send the email.
         */

        $sendEmailJob = new DeliverAllEmails($email);
        dispatch($sendEmailJob);

        $check = Emails::where('id', $email->id)->first();

        //  If the email is delivered, then return true.
        if ($check && $check->delivered_at) {
            return true;
        }

        return false;

    }

    public function sendWithView($subject, $view, $data, $schedule = null) {
        if(!$schedule) {
            $schedule = now();
        }

        /**
         * This function will take subject, view and data, then build the html and then save it to the database.
         */

        //  Here we will be creating the html from the view and data.
        $html = view($view, $data)->render();
        return self::sendHtmlEmail($subject, $html, $schedule);
    }

    /**
     * Receives the subject and the html and then saves it to the database. After that it triggers the job to
     * start the transcation
     *
     * @param $subject
     * @param $html
     * @return void
     */
    public function sendHtmlEmail($subject, $html, $schedule = null)
    {
        if(!$schedule) {
            $schedule = now();
        }

        Emails::create([
            //  ...
        ]);

        /**
         * This function will take subject and body, then save it to the database.
         */
    }
}
