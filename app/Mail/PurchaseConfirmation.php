<?php

namespace App\Mail;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Nota: el envío real lo hace n8n vía App\Jobs\SendEmailViaN8n.
// Esta clase se conserva solo como referencia de la vista/subject y para un
// posible fallback con Mail::send($mailable) si n8n cae.
class PurchaseConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Venta $venta) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmación de compra · {$this->venta->referencia_interna}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-confirmation',
        );
    }
}
