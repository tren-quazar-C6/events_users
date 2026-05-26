# Módulo: Layout, Navbar y Footer

## Layout principal

**Archivo:** `resources/views/layouts/app.blade.php`

Layout base que extienden todas las vistas del sitio excepto las de autenticación, que son standalone.

### Estructura

```
<html>
└── <head>
    ├── @vite (app.css + app.js)
    ├── Google Fonts (Bricolage Grotesque + Plus Jakarta Sans)
    └── Material Symbols Outlined (CDN)
└── <body class="bg-background text-on-surface">
    ├── <div class="fixed inset-0 grain-overlay z-0">   ← textura global
    ├── @include('partials.navbar')
    ├── <main class="relative flex-1 z-10">
    │   └── @yield('content')                           ← contenido de cada vista
    ├── @include('partials.footer')
    └── @livewireScripts                                 ← Alpine.js incluido aquí
```

### Slots disponibles

| Slot | Directiva | Descripción |
|------|-----------|-------------|
| Título de página | `@section('title', '...')` | Reemplaza el `<title>` del documento |
| Contenido principal | `@section('content') ... @endsection` | Zona principal de cada vista |
| Scripts adicionales | `@push('scripts') ... @endpush` | Scripts inline por vista (disponible, poco usado) |

---

## Navbar

**Archivo:** `resources/views/partials/navbar.blade.php`

Barra de navegación superior sticky. Renderiza dos estados según el estado de autenticación del usuario.

### Estado autenticado (`@auth`)

- Logo "Butaca" (`font-display text-primary`)
- Enlace activo determinado por `request()->routeIs('catalog')`, `request()->routeIs('dashboard.tickets')`, `request()->routeIs('dashboard')` — clase `text-primary font-bold` cuando coincide
- Icono de carrito (`shopping_cart`)
- Botón "Cerrar sesión" — POST a `route('logout')` con CSRF

### Estado invitado (`@guest`)

- Logo "Butaca"
- Navegación: Inicio, Cartelera
- Enlace "Iniciar sesión" → `route('login')`
- Botón "Crear cuenta" → `route('register')` — estilo `bg-primary text-on-primary`

### Menú móvil

Ambos estados incluyen un drawer lateral controlado con Alpine.js (`x-data="{ open: false }"`). Se activa con un botón hamburguesa visible en pantallas `< md`.

---

## Footer

**Archivo:** `resources/views/partials/footer.blade.php`

Footer de cuatro columnas presente en todas las vistas que usan el layout principal.

### Columnas

| Columna | Contenido |
|---------|-----------|
| Marca | Logo "Butaca", texto de copyright con año dinámico (`{{ date('Y') }}`), íconos sociales |
| Compañía | Links: Nosotros, Prensa, Trabaja con nosotros |
| Ayuda | Links: FAQ, Soporte, Accesibilidad |
| Contacto | Correo, teléfono, redes sociales |

### Estilo

Fondo `bg-surface-container-low`, borde superior `border-secondary-container/30`. Texto con `text-on-secondary-container` y hover `text-primary`.
