<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQRS recibida · {{ $pqrs->asunto }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f2ed; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fffdf9; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #6b8f71; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fffdf9; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: .5px; }
        .header p { color: rgba(255,253,249,.75); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px 40px; }
        .section-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .card { background: #fff4f0; border: 1px solid #f9c3af; border-radius: 12px; padding: 24px; }
        .title { font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 16px; }
        .meta { display: grid; gap: 10px; font-size: 14px; color: #374151; }
        .pill { display: inline-block; background: #d4e6d5; color: #2d5a31; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; }
        .message { background: #fffdf9; border-radius: 10px; border: 1px solid #e5e7eb; padding: 16px; line-height: 1.6; color: #374151; white-space: pre-line; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        .footer { background: #f5f2ed; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Hemos recibido tu PQRS</p>
    </div>
    <div class="body">
        <p style="font-size:15px;color:#374151;margin:0 0 24px;">
            Hola <strong>{{ $user->name }}</strong>, ya registramos tu solicitud y el equipo administrativo la revisará en el panel de soporte.
        </p>

        <p class="section-label">Detalle</p>
        <div class="card">
            <div class="pill">{{ $pqrs->tipo }}</div>
            <p class="title">{{ $pqrs->asunto }}</p>
            <div class="meta">
                <div><strong>Estado:</strong> {{ str_replace('_', ' ', $pqrs->estado) }}</div>
                <div><strong>Fecha:</strong> {{ $pqrs->fecha_creacion->locale('es')->translatedFormat('j M Y H:i') }}</div>
            </div>
            <hr class="divider">
            <div class="message">{{ $pqrs->mensajes->first()?->mensaje ?? 'Tu solicitud fue registrada correctamente.' }}</div>
        </div>

        <p style="font-size:14px;color:#6b7280;margin:24px 0 0;">
            Te notificaremos cuando el equipo responda tu caso.
        </p>
    </div>
    <div class="footer">
        {{ config('app.name') }} &mdash; ¿Necesitas ayuda? Responde a este correo.
    </div>
</div>
</body>
</html>
