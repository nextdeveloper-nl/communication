<?php

namespace NextDeveloper\Communication\Actions\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\CRM\Database\Models\Users;

class SendEmail
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Sample action;
     * https://.../iam/users/{user-id}/action/reset-password
     */

    /**
     * This action takes a user object and assigns an Account Manager
     *
     * @param Users $users
     */
    public function __construct(Users $users)
    {

    }

    public function handle()
    {
        $mailer = config('communication.defaults.mailer');

        if(class_exists()) {

        } else {
            throw new \DeliveryMethodNotFoundException('Cannot find the delivery method you required.');
        }
    }
}
