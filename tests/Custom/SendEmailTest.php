<?php

use Illuminate\Foundation\Testing\TestCase;
use NextDeveloper\IAM\Database\Models\Users;
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
}
