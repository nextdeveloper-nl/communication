<?php

namespace NextDeveloper\Communication\Services\Delivery;

use NextDeveloper\Communication\Database\Models\Emails;

class Mail
{

    public function send(Emails $email): void
    {

       \Illuminate\Support\Facades\Mail::mailer('smtp')
        ->raw($email->body, function ($message) use ($email) {
            $message->from($email->from_email_address);
            $message->to('ikay@app.com');
            $message->subject($email->subject);
        });

       $email->delivered_at = now();
       $email->save();

    }

}
