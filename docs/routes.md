# Rutas registradas

Archivo: `routes/web.php`

## Rutas públicas

| Método | URI | Nombre | Controlador / Acción | Vista |
|--------|-----|---------|---------------------|-------|
| `GET` | `/` | `home` | Closure | `home` |
| `GET` | `/catalog` | `catalog` | Closure — carga `mocks/events.json` | `catalog` |
| `GET` | `/events/{id}` | `events.show` | Closure — busca evento por id en upcoming | `events.show` |
| `GET` | `/events/{id}/seats` | `events.seats` | Closure — busca evento por id en upcoming | `events.seats` |

## Rutas de autenticación

| Método | URI | Nombre | Controlador / Acción |
|--------|-----|---------|---------------------|
| `GET` | `/login` | `login` | `AuthController@login` |
| `POST` | `/login` | `auth.attempt` | `AuthController@auth` |
| `GET` | `/register` | `register` | `AuthController@showRegister` |
| `POST` | `/register` | `register.store` | `AuthController@register` |
| `POST` | `/logout` | `logout` | `AuthController@logout` — middleware `auth` |

## Rutas protegidas (middleware `auth`)

| Método | URI | Nombre | Controlador / Acción | Vista |
|--------|-----|---------|---------------------|-------|
| `GET` | `/dashboard` | `dashboard` | Closure | `dashboard.index` |
| `GET` | `/dashboard/tickets` | `dashboard.tickets` | Closure — carga `mocks/tickets.json` | `dashboard.tickets` |

## Notas

- Los cierres de rutas del catálogo y el detalle de evento cargan `mocks/events.json` completo en cada solicitud y filtran con `collect()->firstWhere()`. No hay caché.
- `abort_if(!$event, 404)` garantiza una respuesta 404 limpia si el `id` no existe en el mock.
- Las rutas de seats y show comparten el mismo mecanismo de resolución de evento.
- No existen rutas de API ni prefijo `api/`. Todo el tráfico pasa por rutas web con sesión.
