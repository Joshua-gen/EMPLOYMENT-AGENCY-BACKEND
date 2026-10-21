<?php

namespace App\Mail;

use App\Models\Management\ManpowerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManpowerRequestReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ManpowerRequest $manpowerRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Manpower Request Received: {$this->manpowerRequest->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.manpower-request-received');
    }
}
