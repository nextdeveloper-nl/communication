<?php
namespace NextDeveloper\Communication\Envelopes;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use NextDeveloper\IAM\Database\Models\Users;

class NotificationEnvelope extends Mailable
{
    public $user;
    public string $email;

    /**
     * Create a new message instance.
     *
     * @param Users|string $user The user to send the email to
     * @param array $params Contract parameters
     */
    public function __construct(Users|string $user, public $subject, public $body)
    {
        if ($user instanceof Users) {
            $this->user = $user;
            $this->email = $user->email;
        } else {
            $this->email = $user;
        }
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.generic',
            text: 'emails.generic',
            with: [
                'subject' => $this->subject,
                'text' => $this->body,
                'body' => $this->body
            ]
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                address: config('leo.mail.from'),
                name: config('leo.mail.from_name')
            ),
            to: [
                new Address(
                    $this->email
                )
            ],
            replyTo: [
                new Address(
                    config('leo.mail.reply_to')
                )
            ],
            subject: $this->subject,
        );
    }

}
