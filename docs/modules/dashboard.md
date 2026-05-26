# Módulo: Dashboard de Usuario

## Vista principal del dashboard

**Archivo:** `resources/views/dashboard/index.blade.php`
**Ruta:** `GET /dashboard` → `route('dashboard')`
**Middleware:** `auth` — requiere sesión activa

Vista de bienvenida post-login. Actualmente es una vista de índice de estado placeholder que puede expandirse con métricas del usuario (próximas funciones, historial, recomendaciones).

---

## Vista de tickets

**Archivo:** `resources/views/dashboard/tickets.blade.php`
**Ruta:** `GET /dashboard/tickets` → `route('dashboard.tickets')`
**Middleware:** `auth`
**Mock de datos:** `resources/mocks/tickets.json`
**Variable de vista:** `$tickets`

### Estructura del mock

```json
{
  "upcoming": [
    {
      "id": 1,
      "event_title": "...",
      "category": "...",
      "date_label": "...",
      "venue": "...",
      "city": "...",
      "seat": "...",
      "image": null
    }
  ],
  "past": []
}
```

### Secciones

#### Tabs de navegación (Alpine.js)

```js
x-data="{ tab: 'upcoming' }"
```

Dos botones: "Próximos" y "Pasados". El tab activo controla con `:class` cuál panel es visible.

#### Panel "Próximos" (`tab === 'upcoming'`)

Lista de tickets iterada con `@forelse ($tickets['upcoming'] as $ticket)`.

**Tarjeta de ticket:**

| Campo | Descripción |
|-------|-------------|
| Imagen / placeholder | `$ticket['image']` o icono `theater_comedy` si es null |
| Categoría | `$ticket['category']` en badge |
| Título | `$ticket['event_title']` |
| Metadata | `$ticket['date_label']`, `$ticket['venue']`, `$ticket['city']`, `$ticket['seat']` |
| CTA | Botón "Ver QR" (sin funcionalidad implementada) |

**Estado vacío (`@empty`):** Tarjeta con borde punteado y CTA "Explorar cartelera" → `route('catalog')`.

#### Panel "Pasados" (`tab === 'past'`)

`@forelse ($tickets['past'] as $ticket)` — iteración idéntica al panel de próximos.

**Estado vacío:** Panel con `.spotlight-glow` y mensaje "No tienes eventos pasados".

---

## Interacciones

| Acción | Destino |
|--------|---------|
| Tab "Próximos" | Muestra `$tickets['upcoming']` |
| Tab "Pasados" | Muestra `$tickets['past']` |
| "Ver QR" | Sin funcionalidad (botón estático) |
| "Explorar cartelera" en empty state | `GET /catalog` |

---

## Acceso y seguridad

Ambas rutas del dashboard están agrupadas bajo `Route::middleware('auth')`. Cualquier solicitud sin sesión activa es redirigida automáticamente a `/login` por el middleware de Laravel.

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/dashboard/tickets', ...)->name('dashboard.tickets');
});
```
