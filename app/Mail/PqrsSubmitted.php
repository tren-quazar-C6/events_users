<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Pqrs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PqrsSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Pqrs $pqrs,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "PQRS recibida · {$this->pqrs->asunto}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pqrs-submitted',
        );
    }
}
