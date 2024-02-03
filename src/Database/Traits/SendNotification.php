<?php

namespace NextDeveloper\Communication\Database\Traits;

/**
 * This traits handles the email sending process for the customer
 */
trait SendNotification
{
    /**
     * This function takes the body and then sends the notification to the user. Notification is sent through the
     * channels that is defined in the user communication_user_preferences table.
     *
     * You can never know which channels is this function will use to communicate with the user. It can be email, sms,
     * push notification, etc. However this notification message will create a communication record in the database and
     * will trigger the response function. So that you can be aware of the response of the user.
     *
     * @param $body
     * @return void
     */
    public function notify($body)
    {

    }
}
