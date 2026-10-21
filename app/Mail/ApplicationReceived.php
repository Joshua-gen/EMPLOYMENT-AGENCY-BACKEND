<?php

namespace App\Mail;

use App\Models\Management\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Applicant $applicant)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Application Received: {$this->applicant->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.application-received');
    }
}
