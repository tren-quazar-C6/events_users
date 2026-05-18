# DEV3 — Transactions & Ticketing Plan
**Owner:** Developer 3  
**Fecha inicio:** 2026-05-18  
**Rama de trabajo:** `feat/dev3-ticketing`  
**Referencia para Claude:** leer este archivo al inicio de cada sesión para retomar contexto.

---

## Estado del proyecto al inicio del plan

### Lo que existe
- `GET /events/{id}/seats` → `events/seats.blade.php` — mapa de asientos Alpine.js completamente funcional. El botón "Continuar" es un `<button>` sin acción (no envía datos).
- `GET /dashboard/tickets` → lee `mocks/tickets.json` — datos estáticos, sin base de datos real.
- Tabla `users` en SQLite — autenticación funcional.
- Sin controladores propios más allá de `AuthController`.
- Sin migraciones de tickets ni compras.

### Lo que NO existe (a construir)
- Flujo de checkout conectado al mapa de asientos
- Tabla `purchases` y `tickets` en base de datos
- Generación de tickets con códigos únicos
- QR codes renderizados
- Correo de confirmación
- Dashboard de tickets leyendo de la base de datos real

---

## Decisiones técnicas

| Decisión | Elección | Razón |
|---------|---------|-------|
| QR generation | `simplesoftwareio/simple-qrcode` | Paquete oficial Laravel, genera SVG inline sin archivos en disco |
| Cart/seat state | Laravel Session (POST form) | Alpine.js maneja el estado visual; un form hidden serializa y envía los asientos al servidor |
| Unique ticket codes | `Str::upper(Str::random(6))` con prefijo `BTC-` | Corto, legible, único |
| Pago | Mock UI (no gateway real) | En scope solo el flujo UX, no integración de pagos |
| Email driver dev | `log` (`.env`) → Mailtrap para pruebas reales | Sin configuración extra para empezar |
| Notificación | `Laravel Mailable` + Blade template | Nativo de Laravel, consistente con el stack |

---

## Esquema de base de datos

### Tabla `purchases`
```
id                  bigint PK
user_id             FK → users.id
reference           string unique  (ej: ORD-A1B2C3)
total               decimal(12,2)
service_fee         decimal(10,2)
status              enum: pending | confirmed | failed   default: confirmed
created_at / updated_at
```

### Tabla `tickets`
```
id                  bigint PK
purchase_id         FK → purchases.id
event_id            integer  (ID del mock — no FK real)
event_title         string
event_date          string
event_time          string
venue               string
city                string
seat_row            string(1)    (A, B, C...)
seat_number         integer
seat_section        string       (Platea Central, Platea Alta)
unique_code         string unique  (BTC-XXXXXX)
price               decimal(12,2)
status              enum: confirmed | used | cancelled    default: confirmed
created_at / updated_at
```

---

## Mapa de rutas a agregar

Todas bajo `middleware('auth')`:

```
POST  /events/{id}/checkout            → PurchaseController@initCheckout
      Recibe: seats[] del form del mapa
      Almacena sesión, redirige a checkout

GET   /checkout/{token}                → PurchaseController@checkout
      Muestra resumen + form de pago mock

POST  /checkout/{token}/confirm        → PurchaseController@confirm
      Crea purchase + tickets en DB
      Dispara email
      Redirige a confirmación

GET   /purchase/{reference}/confirmation → PurchaseController@confirmation
      Muestra tickets generados con QR

GET   /tickets/{code}/qr               → TicketController@qr
      Retorna SVG del QR del código único

GET   /dashboard/tickets               → actualizar closure existente
      Lee de DB (tickets del usuario autenticado)
```

---

## Archivos a crear / modificar

### Nuevos archivos

```
app/Http/Controllers/PurchaseController.php
app/Http/Controllers/TicketController.php
app/Models/Purchase.php
app/Models/Ticket.php
app/Mail/PurchaseConfirmation.php
database/migrations/xxxx_create_purchases_table.php
database/migrations/xxxx_create_tickets_table.php
resources/views/checkout/index.blade.php
resources/views/checkout/confirmation.blade.php
resources/views/emails/purchase-confirmation.blade.php
docs/modules/ticketing.md                    ← Docs de team (va a GitHub)
docsStudio/04-proyecto-butaca/ticketing-flow.md  ← Docs personales (gitignored)
```

### Archivos a modificar

```
resources/views/events/seats.blade.php
   → Convertir botón "Continuar" en form con hidden inputs de asientos

routes/web.php
   → Agregar rutas de checkout, confirmación, QR

resources/views/dashboard/tickets.blade.php
   → Actualizar para leer de DB en lugar de mock
```

---

## Plan día a día

### Día 1 — Infraestructura y generación de tickets

- [ ] `composer require simplesoftwareio/simple-qrcode`
- [ ] Crear migración `purchases`
- [ ] Crear migración `tickets`
- [ ] `php artisan migrate`
- [ ] Crear modelo `Purchase` con relaciones
- [ ] Crear modelo `Ticket` con relaciones
- [ ] Crear `PurchaseController` (métodos: `initCheckout`, `checkout`, `confirm`, `confirmation`)
- [ ] Crear `TicketController` (método: `qr`)
- [ ] Agregar rutas en `web.php`
- [ ] Actualizar el botón "Continuar" en `seats.blade.php`:
  - Convertir en `<form>` que hace POST a `/events/{id}/checkout`
  - Usar `@foreach` Alpine-rendered con `<input type="hidden">` via `x-for`
  - O: usar un campo hidden con JSON serializado de los asientos seleccionados
- [ ] Implementar `PurchaseController@initCheckout`:
  - Deserializar asientos del POST
  - Verificar que hay asientos
  - Guardar en `session(['checkout_token' => ...])`
  - Redirigir a `GET /checkout/{token}`

**Commit:** `feat: add purchases/tickets migrations, models and initCheckout`

### Día 2 — Checkout, generación y QR

- [ ] Crear `resources/views/checkout/index.blade.php`:
  - Header con datos del evento
  - Lista de asientos del resumen
  - Desglose de precio (subtotal + cargo + total)
  - Form mock de pago (nombre en tarjeta, número enmascarado, fecha, CVV — campos UI)
  - Botón "Confirmar compra"
- [ ] Implementar `PurchaseController@confirm`:
  - Recuperar datos de sesión
  - Crear registro `Purchase` con referencia `ORD-XXXXXX`
  - Crear registro `Ticket` por cada asiento con código `BTC-XXXXXX`
  - Limpiar sesión del checkout
  - Redirigir a `GET /purchase/{reference}/confirmation`
- [ ] Implementar `PurchaseController@confirmation`:
  - Cargar purchase con sus tickets
  - Verificar que pertenece al usuario autenticado
  - Retornar vista de confirmación
- [ ] Crear `resources/views/checkout/confirmation.blade.php`:
  - Mensaje de éxito
  - Referencia de compra (ORD-XXXXXX)
  - Tarjetas de ticket con datos del asiento
  - QR code inline (`<img src="{{ route('tickets.qr', $ticket->unique_code) }}">`)
  - CTA "Ver mis tickets" → dashboard
- [ ] Implementar `TicketController@qr`:
  - Buscar ticket por `unique_code`
  - Verificar que pertenece al usuario
  - Retornar SVG del QR (contenido: unique_code o URL de validación)
- [ ] Actualizar `dashboard/tickets`:
  - Ruta ahora lee `Auth::user()->tickets()->with('purchase')->latest()->get()`
  - Separar upcoming (status: confirmed) y past (status: used)
  - El botón "Ver QR" linkea a `route('tickets.qr', $ticket->unique_code)`

**Commit:** `feat: implement checkout flow, ticket generation and QR rendering`

### Día 3 — Sistema de notificaciones (email)

- [ ] Configurar `.env` con driver `log` (prueba en storage/logs) o Mailtrap
- [ ] `php artisan make:mail PurchaseConfirmation --markdown`
  - O manual: `app/Mail/PurchaseConfirmation.php`
- [ ] Crear `resources/views/emails/purchase-confirmation.blade.php`:
  - Logo Butaca (texto)
  - Referencia de compra
  - Datos del evento (título, fecha, venue)
  - Lista de tickets con código único
  - Footer institucional
- [ ] Implementar `PurchaseConfirmation` Mailable:
  - Recibe `$purchase` con relación `tickets`
  - Asunto: "Tu entrada para [evento] — Butaca"
- [ ] En `PurchaseController@confirm`: disparar email después de crear tickets
  - `Mail::to(Auth::user()->email)->send(new PurchaseConfirmation($purchase))`
- [ ] Verificar en `storage/logs/laravel.log` que el email se genera correctamente

**Commit:** `feat: add purchase confirmation email with ticket details`

### Día 4 — Pulido, status management y documentación

- [ ] Status management en tickets:
  - Método `Ticket::markAsUsed()` — cambia status a `used`
  - Route `POST /tickets/{code}/use` (para validadores, protegida) — opcional si hay tiempo
- [ ] Revisar edge cases:
  - ¿Qué pasa si el token de checkout expiró o no existe?
  - ¿Qué pasa si el mismo usuario intenta recargar la confirmación?
  - Mostrar mensaje claro en lugar de 500
- [ ] Escribir `docs/modules/ticketing.md` (va a GitHub)
- [ ] Escribir `docsStudio/04-proyecto-butaca/ticketing-flow.md` (no sube)
- [ ] Commit y push de documentación

**Commit:** `feat: add ticket status management, edge case handling and documentation`

---

## Registro de progreso

Actualizar esta sección al finalizar cada tarea significativa:

```
[2026-05-18] Plan creado
[ ] Día 1 completado
[ ] Día 2 completado
[ ] Día 3 completado
[ ] Día 4 completado
```

---

## Notas de contexto para retomar

### Cómo pasar los asientos de Alpine al servidor

El mapa de asientos es Alpine.js puro (client-side). Para enviar los asientos seleccionados al servidor:

```html
<!-- En seats.blade.php, reemplazar el <button> por un <form> -->
<form
    method="POST"
    action="{{ route('events.checkout.init', $event['id']) }}"
    x-ref="checkoutForm">
    @csrf
    <input type="hidden" name="event_id"    value="{{ $event['id'] }}">
    <input type="hidden" name="event_title" value="{{ $event['title'] }}">
    <input type="hidden" name="event_date"  value="{{ $event['dates'][0]['day'] }} {{ $event['dates'][0]['month'] }}">
    <input type="hidden" name="event_time"  value="{{ $event['times'][0] }}">
    <input type="hidden" name="venue"       value="{{ $event['venue'] }}">
    <input type="hidden" name="city"        value="{{ $event['city'] }}">
    <input type="hidden" name="price"       value="{{ $event['price'] }}">
    <!-- Los asientos seleccionados: Alpine los inyecta aquí -->
    <input type="hidden" name="seats" :value="JSON.stringify(selected)">

    <button
        type="submit"
        :disabled="selected.length === 0"
        ...>
        Continuar
    </button>
</form>
```

`JSON.stringify(selected)` serializa el array Alpine a JSON. El servidor deserializa con `json_decode($request->input('seats'), true)`.

### Token de checkout (anti-repost)

Para evitar que F5 en la confirmación duplique la compra:

```php
// En initCheckout:
$token = Str::random(40);
session(["checkout:{$token}" => [
    'event_id' => ...,
    'seats'    => $seats,
    // ...
]]);
return redirect()->route('checkout', $token);

// En confirm: después de crear tickets, invalidar el token
session()->forget("checkout:{$token}");
```

### Generación del código único del ticket

```php
// En Ticket::generateCode()
do {
    $code = 'BTC-' . Str::upper(Str::random(6));
} while (Ticket::where('unique_code', $code)->exists());
return $code;
```

### QR con simplesoftwareio/simple-qrcode

```php
// En TicketController@qr
use SimpleSoftwareIO\QrCode\Facades\QrCode;

public function qr(string $code): Response
{
    $ticket = Ticket::where('unique_code', $code)
                    ->where('user_id', Auth::id()) // seguridad
                    ->firstOrFail();

    $svg = QrCode::format('svg')
                 ->size(300)
                 ->errorCorrection('H')
                 ->generate($ticket->unique_code);

    return response($svg, 200)->header('Content-Type', 'image/svg+xml');
}
```

### Cómo está la autenticación en el proyecto

`Auth::user()` retorna el usuario autenticado. Las rutas del área de checkout deben estar dentro del grupo `middleware('auth')`. Si el usuario no está autenticado y accede a `/checkout`, Laravel lo redirige a `/login`.

---

## Decisiones pendientes (resolver al implementar)

1. ¿El checkout requiere que el usuario esté logueado o puede comprar como invitado? → **Por ahora: requiere login.**
2. ¿El QR muestra solo el código o una URL de validación? → **Solo el unique_code.** URL futura: `butaca.app/validate/{code}`
3. ¿La referencia de compra es por compra (ORD-X) o por ticket (BTC-X)? → **Por compra.** Cada ticket tiene su propio código BTC-.
