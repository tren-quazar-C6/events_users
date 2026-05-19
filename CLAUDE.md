# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Proyecto

**Tickify — Portal público de usuarios** (`events_users`)
Laravel 13 · PHP 8.4 · Tailwind CSS v4 · Alpine.js · Livewire · MySQL 8 (Docker)

Parte de un sistema de 4 monolitos (ver `events_infrastructure`). Este monolito sirve el portal público: catálogo, auth, selección de asientos y compra online de entradas.

---

## Comandos esenciales

```bash
composer run dev          # Levanta servidor + Vite + queue en paralelo
php artisan migrate       # Ejecuta migraciones pendientes
php artisan route:list    # Lista todas las rutas registradas
php artisan db:show       # Verifica conexión y estado de la BD
```

## Base de datos

MySQL 8 vía Docker (levantado desde `events_infrastructure`):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=events
DB_USERNAME=users_app
```

El usuario `users_app` tiene permisos `SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, INDEX`.
Si el Docker no está levantado, `composer run dev` falla en la queue con errores de conexión — es normal.

---

## Convenciones de commits

```
feat: <mensaje corto en minúsculas>
```

- Sin scope (`feat(algo):` — no)
- Sin cuerpo, sin firma, sin Co-Authored-By
- Rama activa: `feat/ticketing-purchase-flow`
- Push inmediato tras cada commit

---

## Estado actual del proyecto — rama `feat/ticketing-purchase-flow`

### ✅ Completado en esta rama

**Autenticación y layout**
- Registro, login, logout con branding Tickify
- Navbar con estados auth/guest
- Layout base (`layouts/app.blade.php`), landing, footer

**Catálogo y ficha de evento**
- `GET /catalog` — lista de eventos desde `resources/mocks/events.json`
- `GET /events/{id}` — ficha de evento con selector de fecha/hora (Alpine.js)
- Botón "Comprar Tickets" en catálogo → `events.seats` (requiere auth)
- Botón "Comprar entradas" en ficha → `events.seats` (requiere auth)

**Mapa de asientos**
- `GET /events/{id}/seats` — mapa interactivo con Alpine.js
- Selección de asientos, cálculo de precios reactivo
- **Ruta protegida con `auth`**: guests son redirigidos al login y vuelven automáticamente al mapa después

**Flujo de compra online (Dev 3)**
- `POST /events/{id}/checkout` → `PurchaseController@initCheckout`
  - Serializa asientos desde Alpine.js a JSON, guarda en sesión con token único
- `GET /checkout/{token}` → `PurchaseController@checkout`
  - Vista `checkout/index.blade.php`: resumen de asientos + form de pago mock
- `POST /checkout/{token}/confirm` → `PurchaseController@confirmCheckout`
  - Crea `Purchase` (auto-genera `ORD-XXXXXX` en `booted()`)
  - Crea un `Ticket` por asiento (auto-genera `BTC-XXXXXX` en `booted()`)
  - Invalida el token de sesión (anti-doble-submit)
  - Envía email con `PurchaseConfirmation` Mailable
- `GET /purchase/{reference}/confirmation` → `PurchaseController@confirmation`
  - Vista `checkout/confirmation.blade.php`: tarjetas con datos + QR codes
- `GET /tickets/{code}/qr` → `TicketController@qr`
  - Devuelve SVG generado con `bacon/bacon-qr-code` v3 (ya instalado vía Fortify)
  - Solo el dueño del ticket puede ver su QR

**Dashboard de tickets**
- `GET /dashboard/tickets` — lee de BD real (`Auth::user()->tickets()`)
- Variables: `$upcoming` (confirmed) y `$past` (used) — Eloquent Collections
- Modal Alpine.js con QR, badge Activo/Usado, datos del asiento
- Tabs Próximos / Pasados

**Modelos y BD**
- `Purchase`: `id`, `reference (ORD-XXXXXX)`, `user_id`, `event_id`, snapshot del evento, `subtotal`, `service_fee`, `total`, `status`
- `Ticket`: `id`, `unique_code (BTC-XXXXXX)`, `purchase_id`, `user_id`, `event_id`, snapshot del evento, `seat_row`, `seat_number`, `seat_section`, `price`, `status`
- Relaciones: `User → hasMany Purchase`, `User → hasMany Ticket`, `Purchase → hasMany Ticket`

---

### ❌ Pendiente Dev 3

1. **Pasarela de pagos real**
   El form de pago en `checkout/index.blade.php` es mock (solo UI). Integrar Wompi.
   Variables disponibles en `events_infrastructure/.env.example` (`WOMPI_*`).

2. **Marcar tickets como `used`**
   El portal `events_access` (ASP.NET Core) escanea el QR y necesita un endpoint en este monolito para cambiar `tickets.status` de `confirmed` a `used`. Falta crear esa ruta de API.

3. **Email con Mailpit en local**
   Configurar `.env` para usar Mailpit (incluido en Docker, UI en `localhost:8025`):
   ```ini
   MAIL_MAILER=smtp
   MAIL_HOST=127.0.0.1
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   ```
   Sin esto, `Mail::send()` en `confirmCheckout` puede lanzar error si no hay driver configurado.

---

### ❌ Pendiente general (fuera de Dev 3)

- **BD real para catálogo**: el catálogo y la ficha de evento leen de `resources/mocks/events.json`. Migrar a tablas reales requiere un `Event` model + migración + seeder.
- **Dashboard index**: `GET /dashboard` muestra una vista placeholder vacía.
- **PQRS**: mock en `resources/mocks/pqrs.json`, sin implementación.
- **Favoritos**: mencionado en la arquitectura, sin implementación.
- **Google OAuth**: variables `GOOGLE_*` en `.env.example`, sin implementar.

---

## Rutas del proyecto (resumen)

| Método | URI | Auth | Descripción |
|--------|-----|------|-------------|
| GET | `/` | ❌ | Landing |
| GET | `/catalog` | ❌ | Catálogo (mock) |
| GET | `/events/{id}` | ❌ | Ficha de evento (mock) |
| GET | `/events/{id}/seats` | ✅ | Mapa de asientos |
| POST | `/events/{id}/checkout` | ✅ | Inicia checkout |
| GET | `/checkout/{token}` | ✅ | Resumen de compra |
| POST | `/checkout/{token}/confirm` | ✅ | Confirma y crea tickets |
| GET | `/purchase/{ref}/confirmation` | ✅ | Vista de confirmación con QR |
| GET | `/tickets/{code}/qr` | ✅ | SVG del QR del ticket |
| GET | `/dashboard/tickets` | ✅ | Mis tickets (BD real) |
| GET/POST | `/login`, `/register`, `/logout` | — | Auth |

---

## Archivos clave

```
app/
  Http/Controllers/
    AuthController.php        ← login, register, logout
    PurchaseController.php    ← flujo de compra completo
    TicketController.php      ← QR endpoint
  Mail/
    PurchaseConfirmation.php  ← Mailable de confirmación
  Models/
    Purchase.php              ← auto-genera ORD-XXXXXX en booted()
    Ticket.php                ← auto-genera BTC-XXXXXX en booted()
    User.php                  ← hasMany purchases, hasMany tickets

resources/
  views/
    checkout/
      index.blade.php         ← resumen + form pago mock
      confirmation.blade.php  ← tickets con QR
    dashboard/
      tickets.blade.php       ← mis tickets con modal QR
    emails/
      purchase-confirmation.blade.php
    events/
      seats.blade.php         ← mapa Alpine.js + form POST
      show.blade.php          ← ficha de evento
  mocks/
    events.json               ← datos temporales hasta migrar a BD

routes/web.php                ← todas las rutas
```
