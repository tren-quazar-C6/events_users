<?php

namespace App\Mail;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmation extends Mailable implements ShouldQueue
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
