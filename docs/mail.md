# Sistema de correos

## Decisión de diseño — dev vs producción

El sistema de correos tiene dos capas independientes:

1. **El transporte** — quién entrega el email (varía por entorno)
2. **La cola** — cuándo se entrega (siempre asíncrono)

Ambas se configuran exclusivamente en `.env`. El código PHP no cambia entre entornos.

---

## Transporte local — Mailpit

En desarrollo se usa [Mailpit](https://github.com/axllent/mailpit), un servidor SMTP trampa que corre en Docker. Intercepta todos los emails enviados por la app y los muestra en una bandeja web en `http://localhost:8025`. Ningún email sale a internet.

```ini
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="tickify@local.dev"
MAIL_FROM_NAME="Tickify"
```

### Por qué Mailpit y no otra opción

| Alternativa | Por qué no |
|---|---|
| `MAIL_MAILER=log` | Los emails solo se ven en `storage/logs/laravel.log`. No hay forma de ver el HTML renderizado ni probar el diseño. |
| Mailtrap.io | Requiere cuenta externa, credenciales de equipo compartidas y conexión a internet. Agrega fricción innecesaria para dev local. |
| Gmail SMTP | Requiere App Password, 2FA, y los emails quedan en una bandeja real. Arriesgado para testing automatizado. |
| MailHog | Alternativa válida, pero sin mantenimiento activo desde 2022. Mailpit es su sucesor directo. |

Mailpit corre como un servicio más en `events_infrastructure/docker-compose.yml`:

```yaml
mailpit:
  image: axllent/mailpit
  container_name: quasar_mailpit
  restart: unless-stopped
  ports:
    - "1025:1025"   # SMTP
    - "8025:8025"   # UI web
  networks:
    - quasar_network
```

Para levantarlo:
```bash
# desde events_infrastructure/
docker compose up -d mailpit
```

---

## Transporte producción

Para pasar a producción solo se cambian las variables de entorno en el servidor.

```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com        # o SendGrid, Mailgun, Gmail, etc.
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_TU_API_KEY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="Tickify"
```

futura implementacion con resend.

---

## Cola asíncrona

### Por qué la cola

reducir tiempos de cargas para renderizado de vistas y poder poner a funcionar el servicio con un worker
para el sistema de cola, futura implementacion con un worker

### Implementación

`PurchaseConfirmation` implementa `ShouldQueue`. Laravel detecta la interfaz automáticamente cuando se llama `send()`:

```php
// app/Mail/PurchaseConfirmation.php
class PurchaseConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    // ...
}

// app/Http/Controllers/PurchaseController.php
Mail::to(Auth::user())->send(new PurchaseConfirmation($venta));
// ↑ encola el job porque el Mailable implementa ShouldQueue
```

El driver de cola es `database` (`.env: QUEUE_CONNECTION=database`). Los jobs se almacenan en la tabla `jobs`.

### Worker en desarrollo

```bash
php artisan queue:work
```

Debe correr en una terminal separada mientras se desarrolla. Sin él, los jobs se acumulan en la tabla `jobs` pero ningún email se envía.

> **Reiniciar el worker es obligatorio** después de cualquier cambio de código o configuración.
> El worker carga la app en memoria al arrancar — los cambios posteriores no se aplican hasta reiniciarlo.

### APP_URL y links en el email

Los links dentro del email se generan en el worker (sin request HTTP activo). Laravel usa `APP_URL` como base para `route()` en ese contexto. Para `php artisan serve`, el puerto debe estar incluido:

```ini
APP_URL=http://localhost:8000
```

`AppServiceProvider::boot()` llama `URL::forceRootUrl(config('app.url'))` para que tanto el worker como los redirects de HTTP usen siempre la misma base URL.

Para procesar un único job puntual sin dejar el worker corriendo:

```bash
php artisan queue:work --once
```

Para ver los jobs pendientes:

```bash
php artisan queue:monitor
```

---

## Supervisor en producción

En producción el worker no puede correr manualmente. [Supervisor](http://supervisord.org/) es un daemon de Linux que arranca el worker al boot y lo reinicia si muere.

### Instalación

```bash
sudo apt install supervisor
```

### Configuración

Crear `/etc/supervisor/conf.d/tickify-worker.conf`:

```ini
[program:tickify-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/events_users/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/events_users/storage/logs/worker.log
stopwaitsecs=3600
```

Activar y arrancar:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tickify-worker:*
```

### Parámetros clave del comando

| Flag | Valor | Significado |
|---|---|---|
| `--sleep=3` | 3s | Tiempo de espera entre polls cuando la cola está vacía |
| `--tries=3` | 3 | Intentos antes de marcar un job como fallido |
| `--max-time=3600` | 1h | El worker se reinicia solo cada hora para liberar memoria |

### Jobs fallidos

Si un email falla los 3 intentos, el job pasa a la tabla `failed_jobs`. Para inspeccionarlos:

```bash
php artisan queue:failed
```

Para reintentar todos los fallidos:

```bash
php artisan queue:retry all
```

---

## Flujo completo

```
Usuario confirma pago
        │
        ▼
PurchaseController::confirmCheckout()
        │  crea Purchase + Tickets en BD
        │
        └─ Mail::to($user)->send(new PurchaseConfirmation($venta))
                │
                │ detecta ShouldQueue → no abre SMTP
                ▼
          INSERT en tabla jobs (~5ms)
                │
        HTTP response → redirige a /confirmation
                │
                │ (proceso separado)
                ▼
        php artisan queue:work
                │
                └─ deserializa Venta → renderiza Blade
                           │
                           ▼
                   conexión SMTP → Mailpit (dev) / Resend (prod)
```
