<?php

namespace NextDeveloper\Communication\Helpers;

use App\Mail\CRM\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use NextDeveloper\IAM\Database\Models\Users;

class EmailHelper
{
    private $to;

    public function __construct(Users $to)
    {
        $this->to = $to;

        return $this;
    }

    public function send($subject, $body, $from = null) {
        Mail::to($this->to)
            ->send(new WelcomeEmail(
                $this->to
            ));

        var_dump($result);
    }
}
