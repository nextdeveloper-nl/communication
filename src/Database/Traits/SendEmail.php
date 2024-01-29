<?php

namespace NextDeveloper\Communication\Database\Traits;

/**
 * This traits handles the email sending process for the customer
 */
trait SendEmail
{
    /**
     * This function will take the default view and the default content and then send the email.
     *
     * This function and the default template should only be limited with 1 content only. If you need to send multiple
     * content, then you need to use the sendWithView function.
     *
     * @param $subject
     * @param $body
     * @return void
     */
    public function sendEmail($subject, $body)
    {
        /**
         * This function will take subject and body, then save it to database with a is_sent = 0 flag. And then
         * trigger mail sending action.
         */
    }

    public function sendWithView($subject, $view, $data) {
        /**
         * This function will take subject, view and data, then build the html and then save it to the database.
         */

        //  Here we will be creating the html from the view and data.
        $html = view($view, $data)->render();
        return self::sendHtmlEmail($subject, $html);
    }

    /**
     * Receives the subject and the html and then saves it to the database. After that it triggers the job to
     * start the transcation
     *
     * @param $subject
     * @param $html
     * @return void
     */
    public function sendHtmlEmail($subject, $html)
    {
        /**
         * This function will take subject and body, then save it to the database.
         */
    }
}
