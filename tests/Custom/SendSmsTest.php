<?php

use Illuminate\Foundation\Testing\TestCase;
use NextDeveloper\IAM\Database\Models\Users;
use Twilio\Exceptions\TwilioException;

class SendSmsTest extends TestCase
{

    use Tests\CreatesApplication;

    public function testSendUnitTest()
    {
        $user = Users::withoutGlobalScopes()->where('email', 'baris.bulut@plusclouds.com')->first();

        $this->assertTrue($user->sendSms(
            body: 'This is a test sms'
        ));
    }

    /**
     * @throws TwilioException
     */
    public function testSendWithoutRecordUnitTest()
    {
        $user = Users::withoutGlobalScopes()->where('email', 'baris.bulut@plusclouds.com')->first();

        $this->assertTrue($user->sendSmsWithoutRecord(
            body: 'This is a test sms'
        ));
    }

}
