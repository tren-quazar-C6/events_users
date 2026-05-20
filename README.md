# Tickify — Portal de Usuarios

Portal PWA para descubrir, comprar y guardar entradas de teatro.
Parte del proyecto Quasar (sistema de boletería multi-monolito).

## Stack

- **Laravel 13** (backend + Blade SSR)
- **Tailwind CSS 4** (estilos, configurado vía `@theme` en CSS)
- **Livewire 3** (componentes reactivos en el catálogo)
- **Alpine.js** (interactividad ligera: navbar, dropdowns)
- **Jetstream + Fortify** (instalados; usamos solo la capa de backend de auth, no las vistas)
- **PWA** (manifest + service worker, instalable, offline básico)

## Branding

Paleta sage/cream/peach. Tokens en `resources/css/app.css` bajo `@theme`.

## Setup

Requisitos: PHP 8.5+, Composer, Node 18+, SQLite.

```bash
git clone <url>
cd events_users

# Backend
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate

# Frontend
npm install
npm run dev    # en un terminal aparte
```

Levantar el server:

```bash
php artisan serve
```

Abrir http://127.0.0.1:8000

## Decisiones arquitectónicas clave

1. **Auth manual sobre Fortify.** Jetstream se instaló por su backend (sesiones, hash, migraciones), pero las rutas de Fortify se desactivan con `Fortify::ignoreRoutes()` y el `AuthController` propio maneja login/register/logout. Más transparente y educativo que delegar todo a Fortify.

2. **Mocks en JSON.** Los datos de eventos (`database/mocks/events.json`) y tickets del usuario (`database/mocks/my-tickets.json`) viven como JSON local. Cuando el monolito Tickets (ASP.NET) esté listo, las llamadas `file_get_contents` se reemplazan por `Http::get(...)` a su API. Las vistas y Livewire no se enteran.

3. **Vistas de Jetstream eliminadas.** Para evitar ruido, se borraron todas las views/components publicados por Jetstream que no usaremos. Se mantienen `forgot-password`, `reset-password` y `verify-email` para reciclar lógica cuando se integren.

4. **Teatro único.** Todos los eventos comparten el mismo aforo y sede. No hay selector de venue ni se muestra el dato por evento — es global del sistema.

## Estructura de rutas

| Método | URL | Acceso | Descripción |
|---|---|---|---|
| GET | `/` | público | Landing |
| GET | `/catalog` | público | Catálogo con filtros (Livewire) |
| GET | `/events/{slug}` | público | Detalle de evento |
| GET/POST | `/login` | guest | Login |
| GET/POST | `/register` | guest | Registro |
| POST | `/logout` | auth | Logout |
| GET | `/dashboard` | auth | Resumen del usuario |
| GET | `/dashboard/tickets` | auth | Mis entradas próximas |
| GET | `/dashboard/history` | auth | Historial de compras |
| GET/PATCH | `/dashboard/profile` | auth | Perfil + cambio de password |

Listado completo con `php artisan route:list`.

## PWA

- `public/manifest.json` define la app instalable
- `public/sw.js` cachea con estrategia network-first
- Iconos en `public/icons/icon-{192,512}.png`
- Probar instalación: abrir en Chrome → ícono "Instalar" en la barra de direcciones

## Próximos pasos

- [ ] Botón Google Auth — UI lista, conectar Laravel Socialite
- [ ] Página de checkout (selección de butaca + pasarela de pago)
- [ ] Endpoint real para "Mis tickets" (consume monolito Tickets vía proxy en Users)
- [ ] Email verification y password reset (Fortify ya instalado, solo conectar vistas)
- [ ] QR codes reales firmados (cuando integremos monolito Access)
- [ ] Push notifications para recordatorios de funciones