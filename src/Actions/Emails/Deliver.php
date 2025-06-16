<?php

namespace NextDeveloper\Communication\Actions\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Models\Emails;

/**
 * This action delivers all emails, if email is not specified, that are waiting in the queue.
 */
class Deliver extends AbstractAction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * This action takes an email and sends it to the user.
     *
     * @param Emails|null $email
     * @throws NotAllowedException
     */
    public function __construct(Emails $email = null)
    {
        $this->model = $email;
        return parent::__construct();
    }

    public function handle(): void
    {
        $defaultMailer = config('communication.defaults.mailer');
        $mailer = config('communication.services.' . $defaultMailer . '.class');


        if(class_exists($mailer)) {
            /**
             * This is a pseude code that will be used to send emails. You need to change this code according to the
             * requirements.
             */
            $mailer = new $mailer();
            $mailer->send($this->model);
        } else {
            throw new \BadMethodCallException('Cannot find the delivery method you required.');
        }
    }
}
