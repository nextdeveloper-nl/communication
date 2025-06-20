<?php

namespace NextDeveloper\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Communication\Actions\Emails\Deliver;
use NextDeveloper\Communication\Database\Models\Emails;

class DeliverAllEmails extends AbstractAction
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
        return parent::__construct();
    }

    public function handle()
    {
        $mailer = config('communication.defaults.mailer');

        /**
         * 1)   Gönderilmemiş tüm mailleri çek
         * 2)   Gönderilmemiş maillerin sayısını bul
         * 3)   Gönderilmemiş mailleri gönder ve %yi hesapla ve kaydet
         */

        if(class_exists($mailer)) {
            $emails = Emails::withoutGlobalScopes()
                ->where('delivered_at', null)
                ->get();

            $mailCount = $emails->count();
            $sentMail = 0;

            foreach ($emails as $email) {
                (new Deliver($email))->handle();
                $sentMail++;

                $this->setProgress(ceil($sentMail / $mailCount) * 100, 'Sending emails');
            }
        } else {
            Log::error('Cannot find the delivery method you required.'
                . ' Please check the configuration and the delivery method is in their place.');
        }
    }
}
