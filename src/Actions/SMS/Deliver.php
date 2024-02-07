<?php

namespace NextDeveloper\Communication\Actions\SMS;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Commons\Actions\AbstractAction;

class Deliver extends AbstractAction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * This action takes an SMS and sends it to the user.
     *
     * @param $model
     */
    public function __construct(public $model)
    {
        return parent::__construct();
    }

    public function handle()
    {
        $sms = config('communication.defaults.sms');

        if(class_exists($sms)) {
            /**
             * This is a pseude code that will be used to send sms. You need to change this code according to the
             * requirements.
             */
            $sms = new $sms();
            $sms->send($this->model);
        } else {
            throw new \DeliveryMethodNotFoundException('Cannot find the delivery method you required.');
        }
    }
}