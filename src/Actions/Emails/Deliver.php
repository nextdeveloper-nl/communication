<?php

namespace NextDeveloper\Communication\Actions\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Communication\Database\Models\Emails;
use NextDeveloper\CRM\Database\Models\Users;

class Deliver extends AbstractAction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Sample action;
     * https://.../communication/emails/{email-id}/actions/send
     */

    private $email;

    /**
     * This action takes an email and sends it to the user.
     *
     * @param Emails $emails
     */
    public function __construct(Emails $email = null)
    {
        $this->email = $email;

        return parent::__construct();
    }

    public function handle()
    {
        $mailer = config('communication.defaults.mailer');

        if(class_exists($mailer)) {
            /**
             * This is a pseude code that will be used to send emails. You need to change this code according to the
             * requirements.
             */
            $mailer = new $mailer();
            $mailer->send();
        } else {
            throw new \DeliveryMethodNotFoundException('Cannot find the delivery method you required.');
        }
    }
}
