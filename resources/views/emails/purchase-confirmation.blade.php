<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de compra · {{ $purchase->reference }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f2ed; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fffdf9; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        /* Header — sage */
        .header { background: #6b8f71; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fffdf9; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: .5px; }
        .header p { color: rgba(255,253,249,.75); margin: 6px 0 0; font-size: 14px; }
        /* Body */
        .body { padding: 32px 40px; }
        .section-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        /* Ticket card — peach accent */
        .ticket { background: #fff4f0; border: 1px solid #f9c3af; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .ticket-title { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 12px; }
        .ticket-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px; color: #4b5563; }
        .ticket-meta span { display: block; }
        /* Code badge — sage */
        .ticket-code { display: inline-block; background: #d4e6d5; color: #2d5a31; font-family: monospace; font-size: 15px; font-weight: 700; letter-spacing: 2px; padding: 6px 14px; border-radius: 20px; margin-top: 14px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        /* Pricing */
        .price-row { display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; margin-bottom: 8px; }
        .price-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: #6b8f71; }
        /* CTA — sage */
        .cta { text-align: center; margin: 32px 0; }
        .cta a { background: #6b8f71; color: #fffdf9; text-decoration: none; padding: 14px 32px; border-radius: 100px; font-size: 15px; font-weight: 600; display: inline-block; }
        .footer { background: #f5f2ed; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Compra confirmada · {{ $purchase->reference }}</p>
    </div>
    <div class="body">
        <p style="font-size:15px;color:#374151;margin:0 0 24px;">
            Hola <strong>{{ $purchase->user->name }}</strong>, aquí están tus entradas. Preséntate con el código QR en la puerta del recinto.
        </p>

        <p class="section-label">Tus entradas ({{ $purchase->tickets->count() }})</p>

        @foreach ($purchase->tickets as $ticket)
        <div class="ticket">
            <p class="ticket-title">{{ $ticket->event_title }}</p>
            <div class="ticket-meta">
                <span>📅 {{ $ticket->event_date }}</span>
                <span>🕐 {{ $ticket->event_time }}h</span>
                <span>📍 {{ $ticket->venue }}</span>
                <span>🪑 Fila {{ $ticket->seat_row }}, Asiento {{ $ticket->seat_number }}</span>
            </div>
            <span class="ticket-code">{{ $ticket->unique_code }}</span>
        </div>
        @endforeach

        <hr class="divider">

        <p class="section-label">Resumen de pago</p>
        <div class="price-row"><span>Subtotal</span><span>$ {{ number_format($purchase->subtotal, 0, ',', '.') }}</span></div>
        <div class="price-row"><span>Cargo por servicio</span><span>$ {{ number_format($purchase->service_fee, 0, ',', '.') }}</span></div>
        <div class="price-total"><span>Total</span><span>$ {{ number_format($purchase->total, 0, ',', '.') }}</span></div>

        <div class="cta">
            <a href="{{ route('purchase.confirmation', $purchase->reference) }}">Ver mis entradas con QR</a>
        </div>
    </div>
    <div class="footer">
        {{ config('app.name') }} &mdash; ¿Algún problema? Escríbenos a {{ config('mail.from.address') }}
    </div>
</div>
</body>
</html>
