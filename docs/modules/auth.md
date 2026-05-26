# Módulo: Autenticación

## Vistas

### Login

**Archivo:** `resources/views/auth/login.blade.php`
**Ruta:** `GET /login` → `route('login')`
**Acción del formulario:** `POST /login` → `route('auth.attempt')`
**Controlador:** `App\Http\Controllers\AuthController@auth`

Vista standalone (no extiende `layouts.app`). Incluye sus propias fuentes, CSS y scripts.

#### Secciones

| Sección | Descripción |
|---------|-------------|
| Brand header | Logo "Butaca" centrado (`font-display text-primary`), texto de bienvenida |
| Google OAuth | Botón "Continuar con Google" con SVG del logo de Google — UI-only, href `#` |
| Divider | Separador con texto "o continúa con tu email" |
| Formulario | Campos email (con `old('email')`) y contraseña, checkbox "Recordarme", botón submit |
| Errores | Bloque `@if ($errors->any())` con fondo `bg-error-container/40` |
| Link registro | Enlace a `route('register')` |

#### Campos del formulario

| Campo | Nombre HTML | Tipo | Autocomplete |
|-------|------------|------|-------------|
| Email | `email` | `email` | `username` |
| Contraseña | `password` | `password` | `current-password` |
| Recordar sesión | `remember` | `checkbox` | — |

---

### Registro

**Archivo:** `resources/views/auth/register.blade.php`
**Ruta:** `GET /register` → `route('register')`
**Acción del formulario:** `POST /register` → `route('register.store')`
**Controlador:** `App\Http\Controllers\AuthController@register`

Misma estructura standalone que el login.

#### Secciones

| Sección | Descripción |
|---------|-------------|
| Brand header | Logo "Butaca", texto "Crea tu cuenta y empieza" |
| Google OAuth | Botón "Registrarse con Google" — UI-only, href `#` |
| Divider | "o regístrate con tu email" |
| Formulario | 4 campos + botón submit |
| Errores | Mismo bloque de validación que login |
| Link login | Enlace a `route('login')` |

#### Campos del formulario

| Campo | Nombre HTML | Tipo | Autocomplete |
|-------|------------|------|-------------|
| Nombre completo | `name` | `text` | `name` |
| Email | `email` | `email` | `email` |
| Contraseña | `password` | `password` | `new-password` |
| Confirmar contraseña | `password_confirmation` | `password` | `new-password` |

---

## Controlador

**Archivo:** `app/Http/Controllers/AuthController.php`

| Método | Acción | Descripción |
|--------|--------|-------------|
| `login()` | `GET /login` | Retorna vista de login |
| `auth()` | `POST /login` | Valida credenciales con `Auth::attempt()`, redirige a dashboard |
| `showRegister()` | `GET /register` | Retorna vista de registro |
| `register()` | `POST /register` | Crea usuario, inicia sesión, redirige a dashboard |
| `logout()` | `POST /logout` | Invalida sesión, redirige a home |

---

## OAuth (estado UI)

Los botones de Google OAuth no tienen backend implementado. Están diseñados para conectarse a `GET /auth/google` en el futuro (Laravel Socialite).

Por ahora, el atributo `href="#"` es un placeholder sin funcionalidad.

---

## Estilo

Ambas vistas comparten el mismo sistema visual: fondo `bg-background`, textura `.grain-overlay`, efecto `.spotlight-glow`, card con `bg-surface-container-lowest rounded-[24px]`, inputs con `border-secondary-container/40` y focus `border-primary`.
