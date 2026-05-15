<?php

namespace NextDeveloper\Communication\Http\Controllers\Emails;

use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Http\Controllers\AbstractController;
use NextDeveloper\Communication\Http\Requests\Emails\SendEmailRequest;
use NextDeveloper\Communication\Services\MessagesService;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class SendEmailController extends AbstractController
{
    /**
     * Sends a transactional email through the specified channel.
     *
     * Creates a communication_messages record, delivers it immediately via the
     * chosen channel, and returns the message so the caller can track status.
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
        $data = $request->validated();

        $channel = Channels::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $data['communication_channel_id'])
            ->first();

        $message = MessagesService::create([
            'communication_channel_id' => $channel->id,
            'direction'                => 1,
            'content_type'             => 'text/html',
            'body'                     => $data['body'],
            'recipient'                => $data['recipient'],
            'status'                   => 'queued',
            'iam_account_id'           => UserHelper::currentAccount()->id,
            'metadata'                 => ['subject' => $data['subject']],
        ]);

        MessagesService::deliver($message);

        return ResponsableFactory::makeResponse($this, $message->fresh());
    }
}
