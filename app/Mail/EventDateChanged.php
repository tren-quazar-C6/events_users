<?php

namespace App\Mail;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventDateChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly Evento $evento,
        public readonly string $oldDate,
        public readonly string $newDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cambio de fecha · {$this->evento->nombre_evento}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.event-date-changed');
    }
}
