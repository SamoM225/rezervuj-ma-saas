<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A customer-facing booking e-mail whose HTML was already rendered by
 * App\Support\BookingMailer through the admin-editable template.
 */
class BookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $bookingId,
        public string $type,
        public string $subjectLine,
        public string $body,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(htmlString: $this->body);
    }
}
