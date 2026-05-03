<?php

namespace App\Mail;

use App\Models\Dashboard\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;
    public string $replySubject;
    public string $replyMessage;

    public function __construct(Contact $contact, string $replySubject, string $replyMessage)
    {
        $this->contact = $contact;
        $this->replySubject = $replySubject;
        $this->replyMessage = $replyMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->replySubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dashboard.contact-reply',
        );
    }
}