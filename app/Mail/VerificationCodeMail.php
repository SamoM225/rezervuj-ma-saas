<?php

namespace App\Mail;

use App\Support\VerificationCodes;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One-time code for signup verification or login (second factor).
 */
class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $purpose,
        public string $mailLocale = 'sk',
        public ?string $businessName = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = __("auth.code_mail.subject_{$this->purpose}", ['code' => $this->code], $this->mailLocale);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verification-code', with: [
            'code' => $this->code,
            'purpose' => $this->purpose,
            'locale' => $this->mailLocale,
            'businessName' => $this->businessName ?? config('app.name'),
            'ttl' => VerificationCodes::TTL_MINUTES,
        ]);
    }
}
