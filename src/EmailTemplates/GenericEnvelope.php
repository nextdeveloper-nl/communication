<?php

namespace NextDeveloper\Communication\EmailTemplates;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\Communication\Database\Models\Emails;
use NextDeveloper\I18n\Helpers\i18n;
use NextDeveloper\IAM\Database\Models\Users;

class GenericEnvelope extends Mailable
{
    use Queueable, SerializesModels;

    public $language;

    private $email;

    /**
     * Create a new message instance.
     */
    public function __construct(Emails $email)
    {
        /**
         * Here we get all the information of the email from the database, and then we will use it to send the email.
         *
         * You don't need to make anything extra as a content to send this email.
         */
        $this->email = $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: i18n::t($this->subject, $this->language->code),
            from: new Address(
                //  ...
            ),
            to: [
                new Address(
                //  ...
                )
            ],
            cc: [
                new Address(
                //  ...
                )
            ],
            bcc: [
                new Address(
                //  ...
                )
            ],
            replyTo: [
                new Address(
                //  ...
                )
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Communication::emails.generic',
            with: [
                'data'  =>  '', //   Here will be body from the email.
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
