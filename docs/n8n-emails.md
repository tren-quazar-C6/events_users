# Correos vía n8n

Toda la entrega de correo transaccional sale de Laravel y la hace n8n. Laravel ya no abre SMTP: solo dispara un job que hace `POST` a un webhook con el HTML ya renderizado, y n8n se encarga del resto.

## Por qué movimos el envío fuera de Laravel

- El worker de cola y la conexión SMTP dejan de ser dependencias del backend. Si mañana cambiamos de Resend a Mailgun se toca un nodo en n8n, no `config/mail.php`.
- Tenemos un panel donde mirar ejecuciones, reintentos y el body que llegó al webhook sin tocar `storage/logs`.
- Es el primer paso para sumar más cosas al pipeline (logging en una hoja, alertas en Slack si rebota un correo, etc.) sin meter más Jobs en Laravel.

Las plantillas Blade siguen siendo la fuente de verdad. **n8n no renderiza nada**, solo entrega el HTML que recibe.

## Arquitectura

```
PurchaseController::confirmCheckout()          ChangeEventDate (artisan)
              │                                          │
              │   view('emails.x')->render()             │
              ▼                                          ▼
   SendEmailViaN8n::dispatch(...)         SendEmailViaN8n::dispatch(...)   (1 por favorito)
              │                                          │
              └─────────────┬────────────────────────────┘
                            │  INSERT en tabla `jobs`
                            ▼
                  php artisan queue:work
                            │
                            │  HTTP POST (Http::retry(3,200), timeout 10s)
                            ▼
   ┌──────────────────────────────────────────────────────────────────┐
   │  n8n cloud — workflow `email_automation` (id RRg1cdBIkfLIPWV9)   │
   │                                                                  │
   │  Webhook (POST) ──▶ Switch ($json.body.type) ──┬─▶ Send Email ─▶ Respond 200
   │                                                ├─▶ Send Email ─▶ Respond 200
   │                                                └─▶ Respond 400 (tipo desconocido)
   └──────────────────────────────────────────────────────────────────┘
                            │
                            ▼
                  SMTP (Mailpit en dev / Resend en prod)
```

## Contrato del webhook

`POST {N8N_EMAIL_WEBHOOK_URL}` con `Content-Type: application/json`:

```json
{
  "type": "purchase_confirmation",   // o "event_date_changed"
  "to": "user@example.com",
  "subject": "Confirmación de compra · ORD-ABC123",
  "html": "<!doctype html>...",
  "meta": { "venta_id": 42, "user_id": 7 }
}
```

Respuestas:

| HTTP | Cuándo | Body |
|---|---|---|
| `200` | Correo entregado al SMTP | `{ "ok": true, "type": "..." }` |
| `400` | `type` no reconocido | `{ "ok": false, "error": "unknown type", "received": "..." }` |
| `5xx` o timeout | n8n caído / SMTP roto | El Job reintenta hasta 3 veces (backoff 10s, 30s, 60s). Después cae en `failed_jobs`. |

`meta` no se usa hoy en n8n, pero queda en el log de ejecuciones — útil para correlacionar con `ventas.id` cuando rastreemos un correo perdido.

## El workflow en n8n

**Estado:** activo en producción (publicado el 2026-05-26).

URLs:
- Editor: <https://metafusion.app.n8n.cloud/workflow/RRg1cdBIkfLIPWV9>
- Ejecuciones: <https://metafusion.app.n8n.cloud/workflow/RRg1cdBIkfLIPWV9/executions>
- Webhook producción: `https://metafusion.app.n8n.cloud/webhook/232070c2-edff-46e9-91b9-bbc8c214b142`
- Webhook test (editor abierto): `https://metafusion.app.n8n.cloud/webhook-test/232070c2-edff-46e9-91b9-bbc8c214b142`

Nodos (en orden):

1. **Webhook** — `POST /webhook/232070c2-edff-46e9-91b9-bbc8c214b142`, `responseMode: responseNode`. El path es un UUID a propósito para que no sea adivinable (es nuestra única "auth" hoy; ver sección de seguridad más abajo).
2. **Route by Type** — Switch sobre `={{ $json.body.type }}`. Tres salidas: `purchase_confirmation`, `event_date_changed`, fallback (`unknown`).
3. **Send Purchase Email / Send Date-Changed Email** — dos nodos `emailSend` (SMTP). Mismos parámetros, separados para que cada rama tenga su métrica propia y para poder cambiarles credencial / from por separado si hace falta.
4. **Respond OK (x2) / Respond 400** — cierran el ciclo con la respuesta al webhook.

Las dos credenciales SMTP apuntan a la misma credencial llamada **`Tickify SMTP`** en n8n (no en el repo). Configurarla manualmente desde el editor:

- **Dev** → Mailpit. Pero ojo: n8n cloud no puede llegar a `127.0.0.1` de tu máquina. Necesitas un túnel (ngrok, cloudflared) apuntando al puerto 1025, o correr n8n self-hosted en local. Ver más abajo.
- **Prod** → Resend / SendGrid / etc. Host, puerto, user, pass desde el panel del proveedor. Activar TLS.

## Dev local — cómo probar

El cuello de botella es que **n8n cloud vive en internet y Mailpit vive en tu localhost**. Tienes tres opciones, ordenadas de menos a más fricción:

### Opción A — Correr n8n local (recomendado para dev)

```bash
docker run -it --rm -p 5678:5678 \
  -v ~/.n8n:/home/node/.n8n \
  --add-host=host.docker.internal:host-gateway \
  n8nio/n8n
```

Importar el mismo workflow (lo exportas desde la UI de n8n cloud y lo cargas), apuntar la credencial SMTP a `host.docker.internal:1025`, sin auth, sin TLS. En `.env` del backend:

```ini
N8N_EMAIL_WEBHOOK_URL=http://localhost:5678/webhook/232070c2-edff-46e9-91b9-bbc8c214b142
```

### Opción B — Túnel a Mailpit + n8n cloud

```bash
ngrok tcp 1025
```

Tomar el host:puerto que da ngrok y meterlo en la credencial `Tickify SMTP` de n8n cloud. La URL del webhook sigue siendo la de n8n cloud. Funciona pero el endpoint de ngrok cambia cada vez que reinicias.

### Opción C — Saltarse Mailpit y usar Resend con un dominio sandbox

Más realista pero los correos salen de verdad. Útil para QA, no para iterar en el HTML.

### Webhook test vs producción

Mientras tengas el workflow **abierto en el editor de n8n**, la URL de prueba es:

```
https://metafusion.app.n8n.cloud/webhook-test/232070c2-edff-46e9-91b9-bbc8c214b142
```

Esa URL solo responde una vez por click en "Execute workflow". Para dev continuo: publicar el workflow (toggle "Active" arriba a la derecha) y usar `/webhook/...` (sin `-test`).

## Cómo se dispara desde Laravel

### Job — `app/Jobs/SendEmailViaN8n.php`

```php
SendEmailViaN8n::dispatch(
    type: 'purchase_confirmation',
    to: $venta->user->email,
    subject: "Confirmación de compra · {$venta->referencia_interna}",
    html: view('emails.purchase-confirmation', ['venta' => $venta])->render(),
    meta: ['venta_id' => $venta->id, 'user_id' => $venta->user_id],
);
```

- `ShouldQueue` + driver `database` → mismo modelo de cola que ya teníamos.
- `$tries = 3`, `$backoff = [10, 30, 60]`. Si los tres intentos fallan, el Job va a `failed_jobs` y se puede reintentar con `php artisan queue:retry all`.
- El render del Blade se hace **antes** del dispatch, en el contexto HTTP, así `route()` ya tiene el host correcto. No depende de `URL::forceRootUrl`, aunque seguimos forzándolo en `AppServiceProvider` por seguridad para los casos en que el job termine renderizando algo.

### Puntos de disparo actuales

| Trigger | Archivo | Tipo |
|---|---|---|
| Compra confirmada | `app/Http/Controllers/PurchaseController.php:confirmCheckout()` | `purchase_confirmation` |
| Cambio de fecha de evento | `app/Console/Commands/ChangeEventDate.php` | `event_date_changed` (1 dispatch por favorito) |

Las clases `App\Mail\PurchaseConfirmation` y `App\Mail\EventDateChanged` siguen existiendo pero **ya no se encolan**. Se quedan como referencia de la vista/subject y como red de seguridad por si toca volver a `Mail::send($mailable)`.

## Operación

### Verificar que llegan los correos

1. Levantar Mailpit (`docker compose up -d mailpit` desde `events_infrastructure/`) y abrir <http://localhost:8025>.
2. Levantar el worker: `php artisan queue:work`.
3. Hacer una compra mock o correr `php artisan events:change-date <slug> 2026-06-10 2026-06-25`.
4. En el worker debería aparecer `App\Jobs\SendEmailViaN8n ... DONE`.
5. En Mailpit debería entrar el correo.
6. En n8n: <https://metafusion.app.n8n.cloud/workflow/RRg1cdBIkfLIPWV9/executions> — la ejecución debe estar en verde.

### Smoke test sin Laravel

```bash
curl -X POST "$N8N_EMAIL_WEBHOOK_URL" \
  -H 'Content-Type: application/json' \
  -d '{
    "type": "purchase_confirmation",
    "to": "test@local.dev",
    "subject": "smoke",
    "html": "<b>ok</b>",
    "meta": {}
  }'
```

Esperado: `{"ok":true,"type":"purchase_confirmation"}` y el correo en Mailpit.

### Cuando algo no llega

| Síntoma | Dónde mirar |
|---|---|
| Job en `failed_jobs` con `RuntimeException: n8n webhook falló (...)` | n8n no contestó 2xx. Abrir la última ejecución del workflow en el editor y ver qué nodo falló (90% de las veces es la credencial SMTP). |
| Job en `failed_jobs` con `cURL error 28: Operation timed out` | n8n cloud no responde o el `N8N_EMAIL_WEBHOOK_URL` está mal. Confirmar con el smoke test de arriba. |
| Ejecución verde en n8n pero nada en Mailpit | El SMTP de la credencial apunta a otro lado (clásico: olvidaste cambiar a `host.docker.internal` cuando moviste n8n a docker). |
| `Respond 400 (Unknown Type)` | El backend mandó un `type` que no es `purchase_confirmation` ni `event_date_changed`. Si agregaste un tipo nuevo, agregar la rama en el Switch del workflow. |
| Correo llega pero los links son `http://localhost/...` sin puerto | `APP_URL` en `.env` no incluye `:8000`. Es el mismo problema de siempre cuando se renderiza en el worker. |

### Reprocesar fallidos

```bash
php artisan queue:failed         # listar
php artisan queue:retry all      # reintentar todo
php artisan queue:flush          # botar (peligroso)
```

## Seguridad

Hoy el webhook está abierto: cualquiera con la URL puede mandar correos arbitrarios desde la cuenta SMTP. El UUID en el path baja el riesgo (no es adivinable), pero **no es auth**.

Antes de prod hay que añadir uno de:

- **Header Auth** en el nodo Webhook (el más simple) — Laravel manda `X-Tickify-Secret: <random>` y n8n lo valida.
- **JWT Auth** si más servicios van a hablar con n8n.

Cuando se active, el Job tiene que pasar el header en `Http::withHeaders([...])->post(...)`.

## Cómo agregar un nuevo tipo de correo

1. Crear la vista Blade en `resources/views/emails/<nombre>.blade.php`.
2. En el código que dispara, llamar al job con un nuevo `type`:
   ```php
   SendEmailViaN8n::dispatch(
       type: 'mi_tipo_nuevo',
       to: $user->email,
       subject: '...',
       html: view('emails.mi-tipo-nuevo', [...])->render(),
   );
   ```
3. Abrir el workflow `email_automation` en n8n y agregar una rama nueva en **Route by Type** con el mismo string, conectada a un `Send Email` + `Respond OK`.

No hace falta tocar la cola, el `.env`, ni nada de SMTP.

## Archivos clave

| Archivo | Para qué |
|---|---|
| `app/Jobs/SendEmailViaN8n.php` | El único punto de salida hacia n8n |
| `app/Http/Controllers/PurchaseController.php` | Disparo de `purchase_confirmation` |
| `app/Console/Commands/ChangeEventDate.php` | Disparo de `event_date_changed` (loop) |
| `app/Mail/PurchaseConfirmation.php` | Solo referencia (no se encola) |
| `app/Mail/EventDateChanged.php` | Solo referencia (no se encola) |
| `resources/views/emails/purchase-confirmation.blade.php` | Plantilla — fuente de verdad |
| `resources/views/emails/event-date-changed.blade.php` | Plantilla — fuente de verdad |
| `config/services.php` → `n8n.email_webhook` | Lee `N8N_EMAIL_WEBHOOK_URL` |
| `.env` → `N8N_EMAIL_WEBHOOK_URL` | URL del webhook |
| n8n workflow `RRg1cdBIkfLIPWV9` | El pipeline de entrega |
