# Butaca — Documentación del Proyecto

## Descripción

**Butaca** es una plataforma web de venta de entradas para eventos de teatro. Permite a los usuarios explorar la cartelera, consultar el detalle de cada obra, seleccionar asientos de forma interactiva y gestionar sus tickets desde un panel personal.

## Estado actual

Implementación de interfaz frontend (mock-first). Todas las vistas consumen datos estáticos en JSON. No existe integración con base de datos de eventos ni pasarela de pago real.

## Índice de documentación

| Documento | Contenido |
|-----------|-----------|
| [`architecture.md`](./architecture.md) | Stack técnico, estructura de carpetas y flujo de datos |
| [`design-system.md`](./design-system.md) | Tokens de color, tipografía y convenciones de estilo |
| [`routes.md`](./routes.md) | Tabla de rutas registradas |
| [`modules/layout.md`](./modules/layout.md) | Layout principal, navbar y footer |
| [`modules/auth.md`](./modules/auth.md) | Vistas de autenticación (login, registro) |
| [`modules/catalog.md`](./modules/catalog.md) | Cartelera de eventos |
| [`modules/events.md`](./modules/events.md) | Detalle de evento y selección de asientos |
| [`modules/dashboard.md`](./modules/dashboard.md) | Panel de usuario y gestión de tickets |

## Inicio rápido

```bash
# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos local (SQLite)
touch database/database.sqlite
php artisan migrate --force

# Servidor de desarrollo
composer run dev
```

La aplicación queda disponible en `http://localhost:8000`.
