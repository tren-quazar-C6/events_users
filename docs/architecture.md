# Arquitectura del Sistema

## Stack tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| Framework backend | Laravel | 13.8 |
| Componentes reactivos | Livewire | 3.6.4 |
| Reactividad de UI | Alpine.js | 3.x (vía npm + importado en app.js) |
| Estilos | Tailwind CSS | 4.0 (Vite 8) |
| Motor de plantillas | Blade | — |
| Base de datos | MySQL | 8.x (puerto 3307) |
| Tipografías | DM Sans, Fraunces | Google Fonts |
| PWA | manifest.json + Service Worker | ver `docs/pwa.md` |

## Estructura de carpetas relevante

```
events_users/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php       # Registro, login y logout
│   │       ├── PurchaseController.php   # Flujo de compra completo (4 métodos)
│   │       └── TicketController.php     # Genera QR en SVG
│   ├── Models/
│   │   ├── User.php                     # hasMany → Purchase, Ticket
│   │   ├── Purchase.php                 # Auto-genera ORD-XXXXXX en booted()
│   │   └── Ticket.php                   # Auto-genera BTC-XXXXXX en booted()
│   └── Mail/
│       └── PurchaseConfirmation.php     # Mailable de confirmación de compra
├── database/
│   └── migrations/
│       ├── *_create_purchases_table.php
│       └── *_create_tickets_table.php
├── resources/
│   ├── css/
│   │   └── app.css                      # Tokens del design system (Tailwind @theme)
│   ├── js/
│   │   └── app.js
│   ├── mocks/                           # Datos estáticos JSON (catálogo y eventos)
│   │   ├── events.json
│   │   ├── tickets.json
│   │   ├── user.json
│   │   └── pqrs.json
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php            # Layout principal
│       ├── partials/
│       │   ├── navbar.blade.php
│       │   └── footer.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── events/
│       │   ├── show.blade.php
│       │   └── seats.blade.php
│       ├── checkout/
│       │   ├── index.blade.php          # Resumen de compra + form pago mock
│       │   └── confirmation.blade.php   # Tickets con QR codes
│       ├── emails/
│       │   └── purchase-confirmation.blade.php
│       ├── dashboard/
│       │   ├── index.blade.php
│       │   └── tickets.blade.php        # Lee de BD real (purchases + tickets)
│       ├── catalog.blade.php
│       └── home.blade.php
├── routes/
│   └── web.php
└── docs/                                # Esta carpeta
```

## Flujo de datos

```
Solicitud HTTP
     │
     ▼
routes/web.php  →  closure / controller
     │
     ├─ Catálogo / eventos: lee resources/mocks/events.json (mock)
     ├─ Compra / tickets:   Eloquent ORM → MySQL (BD real)
     ├─ Pasa variables al view con compact()
     └─ Retorna Blade view
            │
            ▼
      Blade template
            │
            ├─ @extends('layouts.app')     ← layout principal
            ├─ Datos PHP renderizados      ← {{ $var }}
            └─ Alpine.js (x-data)         ← interactividad en cliente
```

## Convenciones generales

- Las rutas que requieren datos de mock cargan el JSON en la clausura de la ruta, no en un controlador dedicado.
- Alpine.js es el único motor de interactividad del lado cliente. Está disponible globalmente porque Livewire 3 lo incluye en `@livewireScripts`.
- No se usan componentes Livewire propios en ninguna vista actualmente; todas las interacciones son Alpine puro.
- Las clases CSS dinámicas generadas por Alpine (`:class`) que no aparecen textualmente en los archivos Blade se declaran en un comentario Tailwind scanner dentro de la vista correspondiente para asegurar su inclusión en el bundle.

## Autenticación

Gestionada por `AuthController` con sesiones de Laravel. La tabla `users` existe vía migraciones estándar de Laravel. No hay OAuth real implementado; el botón de Google en las vistas de auth es UI-only y apunta a `#`.

## Base de datos

MySQL 8.x corriendo en el contenedor de `events_infrastructure` (puerto **3307** en el host).

Configuración en `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=events
DB_USERNAME=users_app
DB_PASSWORD=cambiame_users
```

Tablas relevantes:

| Tabla | Origen | Descripción |
|-------|--------|-------------|
| `users` | migración Laravel estándar | Usuarios registrados |
| `sessions` | migración Laravel estándar | Sesiones HTTP |
| `purchases` | `*_create_purchases_table.php` | Compras confirmadas (ORD-XXXXXX) |
| `tickets` | `*_create_tickets_table.php` | Tickets individuales por asiento (BTC-XXXXXX) |

El catálogo de eventos y las fichas de evento aún leen de `resources/mocks/events.json`; la migración a un modelo `Event` con tabla propia está pendiente.
