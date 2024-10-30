<?php

namespace NextDeveloper\Communication\Helpers;

use Illuminate\Support\Facades\Mail;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\EmailsService;
use NextDeveloper\IAM\Database\Models\Users;
use PharIo\Manifest\Email;

/**
 * This class is used to email the user by using the communications module.
 */
class Communicate
{
    private Users $user;

    /**
     *
     *
     * @param Users $receiver
     */
    public function __construct(Users $receiver)
    {
        $this->user = $receiver;

        return $this;
    }

    /**
     * This function is used to get the notification platforms that the user has set.
     *
     * @return mixed
     */
    public function getNotificationPlatforms(): mixed
    {
        /**
         * Here we will get the notification platforms that the user has set.
         */
        return Channels::withoutGlobalScopes()
            ->where('iam_user_id', $this->user->id)
            ->whereIsActive(true)
            ->whereIsVerified(true)
            ->get();
    }

    /**
     * This function is an alias of Emails service to make it easy to use, since we cannot use traits.
     *
     * @param $envelope
     * @return void
     */
    public function sendEnvelope($envelope)
    {
        self::sendEnvelopeNow($envelope);
    }

    public function sendNotification($subject, $message) {
        /**
         * Here we will send the customer a notification by using customers prefered notification channels.
         */
    }

    public function sendEnvelopeNow($envelope) {
        Mail::driver('smtp')
            ->to($this->user->email)
            ->send($envelope);
    }
}
