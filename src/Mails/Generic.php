<?php

namespace NextDeveloper\Communication\Mails;

use Helpers\i18n;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\IAM\Database\Models\Users;

class Generic extends Mailable
{
    use Queueable, SerializesModels;

    public $language;

    /**
     * Create a new message instance.
     */
    public function __construct(public Users $users, public $subject, public $body)
    {
        $language = Languages::where('id', $users->common_language_id)->first();
        $this->language = $language;

        $this->subject = I18n::t($this->subject, $language->code);
        $this->body = I18n::t($this->body, $language->code);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: i18n::t($this->subject, $this->language->code),
            from: new Address(
                config('communication.from.email'),
                config('communication.from.name')
            ),
            replyTo: [
                new Address(
                    config('communication.from.reply_to'),
                    config('communication.from.reply_to_name')
                )
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Communication::emails.generic',
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
