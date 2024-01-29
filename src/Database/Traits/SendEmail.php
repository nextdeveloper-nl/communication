<?php

namespace NextDeveloper\Communication\Database\Traits;

/**
 * This traits handles the email sending process for the customer
 */
trait SendEmail
{
    public function sendEmail($subject, $body)
    {
        /**
         * This function will take subject and body, then save it to database with a is_sent = 0 flag. And then
         * trigger mail sending action.
         */
    }

    public function sendRawEmail($subject, $body)
    {
        /**
         * This function will take subject and body, then send it to the email address directly.
         */
    }
}
