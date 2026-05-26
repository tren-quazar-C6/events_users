# Probar envíos de email — Tickify + n8n

Aprenderás haciendo. Cada sección tiene un ejercicio primero y la explicación después.

---

## Antes de empezar

Necesitas:
- Terminal con `curl`
- El proyecto corriendo: `php artisan serve` en una terminal, `php artisan queue:work` en otra
- `.env` con `N8N_EMAIL_WEBHOOK_URL` configurado (ver `.env.example`)
- Mailpit corriendo (`docker compose up -d mailpit` desde `events_infrastructure/`) → bandeja en `http://localhost:8025`

> Si usas Resend en lugar de Mailpit: los correos llegan al email real que pongas en `to`.

---

## Ejercicio 1 — Manda un correo ahora (sin leer nada más)

Copia este comando, cambia `tu@email.com` por tu correo real y ejecútalo:

```bash
curl -X POST "https://metafusion.app.n8n.cloud/webhook/232070c2-edff-46e9-91b9-bbc8c214b142" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "purchase_confirmation",
    "to": "tu@email.com",
    "subject": "Prueba directa al webhook",
    "html": "<h1>Funciona</h1><p>Este correo saltó el queue de Laravel y fue directo a n8n.</p>",
    "meta": {}
  }'
```

**¿Qué esperas ver?**
Adivina antes de ejecutar: ¿llega el correo? ¿cuánto tarda? ¿qué responde el curl?

Luego ejecuta y compara.

---

### ¿Qué acaba de pasar?

```
tu terminal
    │  POST JSON con { type, to, subject, html }
    ▼
n8n cloud — workflow email_automation
    │  Switch lee body.type → rama "purchase_confirmation"
    │  nodo Send Email → SMTP
    ▼
Mailpit / Resend → tu bandeja
    │
    ▼
curl recibe: { "ok": true, "type": "purchase_confirmation" }
```

**Lo clave:** n8n recibe el HTML ya armado. No renderiza nada, no sabe nada de Laravel. Solo enruta y entrega.

El curl habló directo al webhook — sin queue, sin Laravel, sin worker. Así es como el smoke test valida que n8n está vivo independientemente de la app.

---

## Ejercicio 2 — Manda el otro tipo de correo

Mismo comando, distinto `type`. Adivina qué cambia en la respuesta y en n8n antes de ejecutar:

```bash
curl -X POST "https://metafusion.app.n8n.cloud/webhook/232070c2-edff-46e9-91b9-bbc8c214b142" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "event_date_changed",
    "to": "tu@email.com",
    "subject": "Tu evento cambió de fecha",
    "html": "<p>El evento <b>Rock en el Parque</b> cambió al <del>10 junio</del> → <strong>25 junio</strong>.</p>",
    "meta": { "user_id": 1, "evento_id": 3 }
  }'
```

Después de ejecutar: abre las ejecuciones del workflow en n8n → <https://metafusion.app.n8n.cloud/workflow/RRg1cdBIkfLIPWV9/executions> — ¿por qué rama pasó?

---

## Ejercicio 3 — Rómpelo a propósito

Estas cuatro roturas te van a mostrar exactamente cómo falla el sistema en cada capa.

### Rotura A — type inválido

```bash
curl -X POST "https://metafusion.app.n8n.cloud/webhook/232070c2-edff-46e9-91b9-bbc8c214b142" \
  -H "Content-Type: application/json" \
  -d '{"type": "algo_inventado", "to": "tu@email.com", "subject": "x", "html": "x", "meta": {}}'
```

¿Qué HTTP status devuelve? ¿Llega el correo? Busca la ejecución en n8n y mira qué nodo terminó respondiendo.

---

### Rotura B — worker apagado

1. Para el worker (`Ctrl+C` en la terminal donde corre `queue:work`).
2. Desde la app, haz una compra mock (checkout completo).
3. Abre `http://localhost:8025` — ¿llegó el correo?
4. Abre una consola y revisa la tabla de jobs:
   ```bash
   php artisan tinker --execute="echo App\Jobs\SendEmailViaN8n::class . ': ' . DB::table('jobs')->count() . ' pendiente(s)';"
   ```
5. Vuelve a levantar el worker. ¿Qué pasa?

**Lo que aprendes:** el worker es quien hace el POST a n8n. Sin worker, los jobs se acumulan en `jobs` pero nunca salen. La app responde rápido igual — el usuario llega a `/confirmation` sin esperar el email.

---

### Rotura C — URL del webhook incorrecta

1. En `.env`, cambia `N8N_EMAIL_WEBHOOK_URL` a cualquier URL inválida:
   ```ini
   N8N_EMAIL_WEBHOOK_URL=https://esto-no-existe.example.com/webhook/test
   ```
2. Reinicia el worker (`php artisan queue:work`).
3. Haz una compra mock.
4. Observa la terminal del worker — ¿qué error aparece? ¿cuántas veces lo reintenta?
5. Después:
   ```bash
   php artisan queue:failed
   ```
6. Restaura la URL correcta y prueba:
   ```bash
   php artisan queue:retry all
   ```

**Lo que aprendes:** el job tiene 3 intentos con backoff 10s / 30s / 60s. Después cae en `failed_jobs`. `queue:retry all` lo resucita cuando el webhook vuelve a estar disponible.

---

### Rotura D — variable no configurada

1. Comenta `N8N_EMAIL_WEBHOOK_URL` en `.env`:
   ```ini
   # N8N_EMAIL_WEBHOOK_URL=...
   ```
2. Corre `php artisan config:clear`.
3. Haz una compra. Busca el job fallido:
   ```bash
   php artisan queue:failed
   ```
4. ¿Qué mensaje de error ves?

Restaura la variable y haz `php artisan queue:retry all`.

---

## Ejercicio 4 — El flujo completo desde la app

### Correo de compra

1. `php artisan serve` + `php artisan queue:work` corriendo.
2. Login → catálogo → elige un evento → selecciona asientos → confirma pago.
3. En la terminal del worker deberías ver algo así:
   ```
   [2026-05-26 21:00:00] Processing: App\Jobs\SendEmailViaN8n
   [2026-05-26 21:00:01] Processed: App\Jobs\SendEmailViaN8n
   ```
4. Abre Mailpit (`http://localhost:8025`) — el correo de confirmación con los tickets debe estar ahí.
5. Verifica que el botón "Ver mis tickets" del correo lleva a `http://localhost:8000/dashboard/tickets` (no a `localhost` sin puerto).

Si el link no tiene el puerto → `.env` tiene `APP_URL=http://localhost` sin `:8000`.

---

### Correo de cambio de fecha

1. Necesitas un usuario que tenga un evento como favorito. Si no tienes, agrégalo desde la ficha del evento (ícono de corazón).
2. Corre el comando:
   ```bash
   php artisan events:change-date <slug-del-evento> 2026-06-10 2026-06-25
   ```
   Reemplaza `<slug-del-evento>` con el slug real (visible en la URL de la ficha: `/events/<slug>`).
3. El worker encola un job por cada usuario con ese favorito. Espera a que procese.
4. En Mailpit: un correo por usuario con la fecha anterior tachada y la nueva en verde.

---

## Ejercicio 5 — Ver las ejecuciones en n8n

Abre: <https://metafusion.app.n8n.cloud/workflow/RRg1cdBIkfLIPWV9/executions>

Por cada correo enviado hay una ejecución. Haz click en una y observa:
- Los datos de entrada que llegaron al webhook (el JSON que mandó Laravel)
- Por qué rama pasó el Switch
- El resultado del nodo Send Email

Esto es el equivalente a `storage/logs/laravel.log` pero para el pipeline de correos.

---

## Referencia rápida — qué revisar cuando algo no llega

| Síntoma | Dónde mirar |
|---|---|
| Nada en Mailpit, worker muestra `DONE` | n8n recibió el job pero SMTP falló. Ver ejecución en n8n → nodo Send Email → error. |
| Worker muestra error de cURL timeout | n8n cloud no responde. Hacer el smoke test del Ejercicio 1. |
| `queue:failed` con `RuntimeException: N8N_EMAIL_WEBHOOK_URL no está configurado` | Variable no está en `.env` o no se corrió `config:clear` tras el cambio. |
| `queue:failed` con status 400 de n8n | El `type` que mandó el job no existe en el Switch del workflow. |
| Correo llega pero links sin puerto | `APP_URL` en `.env` sin `:8000`. |
| Correo no llega y ejecución verde en n8n | SMTP entregó al servidor pero el correo está en spam, o Mailpit no está corriendo. |

---

## Cheatsheet

```bash
# smoke test directo al webhook
curl -X POST "$N8N_EMAIL_WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -d '{"type":"purchase_confirmation","to":"yo@ejemplo.com","subject":"test","html":"<b>ok</b>","meta":{}}'

# disparar correo de cambio de fecha
php artisan events:change-date mi-evento 2026-06-10 2026-06-25

# ver jobs pendientes
php artisan queue:monitor

# ver jobs fallidos
php artisan queue:failed

# reintentar fallidos
php artisan queue:retry all

# procesar un solo job (sin dejar worker corriendo)
php artisan queue:work --once

# limpiar config tras cambiar .env
php artisan config:clear
```
