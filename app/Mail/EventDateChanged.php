<?php

namespace App\Mail;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Nota: el envío real lo hace n8n vía App\Jobs\SendEmailViaN8n.
// Esta clase se conserva solo como referencia de la vista/subject y para un
// posible fallback con Mail::send($mailable) si n8n cae.
class EventDateChanged extends Mailable
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
