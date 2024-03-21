<?php

namespace NextDeveloper\Communication\Services;

use Illuminate\Mail\Mailable;
use NextDeveloper\Communication\Jobs\DeliverAllEmails;
use NextDeveloper\Communication\Services\AbstractServices\AbstractEmailsService;
use NextDeveloper\IAM\Database\Models\Users;

/**
 * This class is responsible from managing the data for Emails
 *
 * Class EmailsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class EmailsService extends AbstractEmailsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function sendEnvelope(Users $users, Mailable $mailable) {
        /**
         * Here we will record the email in the database and send it to the user by using DeliverAllEmail job.
         */

        //  Here save the envelope in the database
        dispatch(new DeliverAllEmails());
    }
}
