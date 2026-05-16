<?php

namespace NextDeveloper\Communication\Http\Controllers\Emails;

use NextDeveloper\Communication\Http\Controllers\AbstractController;
use NextDeveloper\Communication\Http\Requests\Emails\SendEmailRequest;
use NextDeveloper\Communication\Services\MessagesService;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;

class SendEmailController extends AbstractController
{
    /**
     * Sends a transactional email through the specified channel.
     *
     * POST /communication/send-email
     * {
     *   "subject":                  "Hello",
     *   "body":                     "<p>Hi there</p>",
     *   "communication_channel_id": "<uuid>",
     *   "recipient":                "user@example.com"
     * }
     */
    public function send(SendEmailRequest $request)
    {
        $message = MessagesService::sendEmail($request->validated());

        return ResponsableFactory::makeResponse($this, $message);
    }
}
