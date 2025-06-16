<?php

use Illuminate\Foundation\Testing\TestCase;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Envelopes\AccountCreatedEnvelope;
use Tests\CreatesApplication;

class SendEmailTest extends TestCase
{
    use CreatesApplication;

    public function testSendEmailSimpleUnitTest()
    {
        $user = Users::withoutGlobalScopes()->where('email', 'baris.bulut@plusclouds.com')->first();

        $this->assertTrue($user->sendEmail(
            subject: 'Test Email',
            body: 'This is a test email'
        ));
    }

    public function testGetEmailsUnitTest()
    {
        $emails = \NextDeveloper\Communication\Database\Models\Emails::withoutGlobalScopes()->where('id', 124)->get();

        $email = $emails->first();

        $email->update([
            'to'    =>  [
                'a@b.com',
                'a@b.com',
                'a@b.com',
                'a@b.com',
                'a@b.com'
            ]
        ]);

        $this->assertTrue($emails->count() > 0);
    }

    public function testSendWithEnvelopeUnitTest()
    {
        $user = Users::withoutGlobalScopes()->where('email', 'baris.bulut@plusclouds.com')->first();

        $this->assertTrue($user->sendWithEnvelope(
            new AccountCreatedEnvelope($user)
        ));

    }
}
