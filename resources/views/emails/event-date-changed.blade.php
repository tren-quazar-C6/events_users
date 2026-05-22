<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de fecha · {{ $event['title'] }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f2ed; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fffdf9; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #6b8f71; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fffdf9; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: .5px; }
        .header p { color: rgba(255,253,249,.75); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px 40px; }
        .section-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .event-card { background: #fff4f0; border: 1px solid #f9c3af; border-radius: 12px; padding: 24px; }
        .event-title { font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 20px; }
        .date-change { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .date-box { text-align: center; padding: 12px 20px; border-radius: 10px; }
        .date-box.old { background: #f3f4f6; border: 1px solid #e5e7eb; }
        .date-box.old .label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .date-box.old .value { font-size: 16px; font-weight: 600; color: #6b7280; text-decoration: line-through; margin-top: 4px; }
        .arrow { font-size: 22px; color: #6b8f71; font-weight: 700; }
        .date-box.new { background: #d4e6d5; border: 1px solid #6b8f71; }
        .date-box.new .label { font-size: 11px; font-weight: 600; color: #2d5a31; text-transform: uppercase; letter-spacing: 1px; }
        .date-box.new .value { font-size: 16px; font-weight: 700; color: #2d5a31; margin-top: 4px; }
        .meta { margin-top: 16px; display: flex; gap: 6px; flex-wrap: wrap; font-size: 13px; color: #4b5563; }
        .meta span { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 20px; padding: 3px 10px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        .cta { text-align: center; margin: 28px 0; }
        .cta a { background: #6b8f71; color: #fffdf9; text-decoration: none; padding: 14px 32px; border-radius: 100px; font-size: 15px; font-weight: 600; display: inline-block; }
        .footer { background: #f5f2ed; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Actualización de fecha de evento</p>
    </div>
    <div class="body">
        <p style="font-size:15px;color:#374151;margin:0 0 24px;">
            Hola <strong>{{ $user->name }}</strong>, te informamos que se ha modificado la fecha de un evento que tienes guardado como favorito.
        </p>

        <p class="section-label">Evento afectado</p>

        <div class="event-card">
            <p class="event-title">{{ $event['title'] }}</p>

            <div class="date-change">
                <div class="date-box old">
                    <div class="label">Fecha anterior</div>
                    <div class="value">{{ \Carbon\Carbon::parse($oldDate)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</div>
                </div>
                <div class="arrow">→</div>
                <div class="date-box new">
                    <div class="label">Nueva fecha</div>
                    <div class="value">{{ \Carbon\Carbon::parse($newDate)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</div>
                </div>
            </div>

            <div class="meta">
                <span>📍 {{ $event['venue'] }}, {{ $event['city'] }}</span>
                <span>🎭 {{ $event['category'] }}</span>
            </div>
        </div>

        <hr class="divider">

        <p style="font-size:14px;color:#6b7280;margin:0 0 8px;">
            Si ya compraste entradas para este evento, verifica tu ticket y comunícate con nosotros si tienes alguna duda.
        </p>

        <div class="cta">
            <a href="{{ route('events.show', $event['slug']) }}">Ver evento</a>
        </div>
    </div>
    <div class="footer">
        {{ config('app.name') }} &mdash; ¿Algún problema? Escríbenos a {{ config('mail.from.address') }}
    </div>
</div>
</body>
</html>
