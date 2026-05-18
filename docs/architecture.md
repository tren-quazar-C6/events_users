# Arquitectura del Sistema

## Stack tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| Framework backend | Laravel | 13.8 |
| Componentes reactivos | Livewire | 3.6.4 |
| Reactividad de UI | Alpine.js | 3.x (incluido vía Livewire) |
| Estilos | Tailwind CSS | 4.0 (Vite 8) |
| Motor de plantillas | Blade | — |
| Base de datos | SQLite | local |
| Iconografía | Material Symbols Outlined | Google CDN |
| Fuentes | Bricolage Grotesque, Plus Jakarta Sans | Google Fonts |

## Estructura de carpetas relevante

```
events_users/
├── app/
│   └── Http/
│       └── Controllers/
│           └── AuthController.php       # Registro, login y logout
├── resources/
│   ├── css/
│   │   └── app.css                      # Tokens del design system (Tailwind @theme)
│   ├── js/
│   │   └── app.js
│   ├── mocks/                           # Datos estáticos JSON
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
│       ├── dashboard/
│       │   ├── index.blade.php
│       │   └── tickets.blade.php
│       ├── catalog.blade.php
│       └── home.blade.php
├── routes/
│   └── web.php
└── docs/                                # Esta carpeta
```

## Flujo de datos (mock-first)

```
Solicitud HTTP
     │
     ▼
routes/web.php  →  closure / controller
     │
     ├─ Lee resources/mocks/*.json con json_decode()
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

SQLite local (`.env` → `DB_CONNECTION=sqlite`). Sesiones, caché y cola también se almacenan en la base de datos (`SESSION_DRIVER=database`, etc.). Solo la tabla `users` es relevante en el estado actual.
