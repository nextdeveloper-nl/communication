<?php

namespace NextDeveloper\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Database\Models\Actions;
use NextDeveloper\Communication\Actions\Emails\Deliver;
use NextDeveloper\Communication\Database\Models\Emails;

class DeliverAllEmails extends AbstractAction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Sample action;
     * https://.../communication/emails/{email-id}/actions/send
     */


    /**
     * This action takes an email and sends it to the user.
     *
     * @param $mnodel
     */
    public function __construct(public $model)
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

            $this->setAction(Actions::create([
                'action'        =>  get_class($this),
                'progress'      =>  0,
                'runtime'       =>  0,
                'object_id'     =>  $this->model->id,
                'object_type'   =>  get_class($this->model)
            ]));

            foreach ($emails as $email) {
                (new Deliver($email))->handle();
                $email->delivered_at = now();
                $email->save();

                $sentMail++;

                $this->setProgress(ceil($sentMail / $mailCount) * 100, 'Sending emails');
            }

            $this->setFinished();
        } else {
            throw new \DeliveryMethodNotFoundException('Cannot find the delivery method you required.');
        }
    }
}
