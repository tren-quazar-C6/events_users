# Sistema de correos

> **El envío de correo ya no pasa por el mailer de Laravel.** Desde la migración a n8n, Laravel solo renderiza el Blade a HTML y lo manda a un webhook. El transporte SMTP lo gestiona n8n.
>
> Ver documentación completa del pipeline en [`docs/n8n-emails.md`](./n8n-emails.md).

## Resumen del flujo actual

```
Controller / Command
       │  view(...)->render()
       │  SendEmailViaN8n::dispatch(to, subject, html)
       ▼
  tabla `jobs` (queue database)
       │
       ▼
  php artisan queue:work
       │  HTTP POST → N8N_EMAIL_WEBHOOK_URL
       ▼
  n8n cloud (workflow email_automation)
       │
       ▼
  SMTP → Mailpit (dev) / proveedor real (prod)
```

La cola de Laravel sigue en uso — `ShouldQueue` vive en `App\Jobs\SendEmailViaN8n`, no en los Mailables. Los Mailables (`PurchaseConfirmation`, `EventDateChanged`) conservan solo la referencia a la vista y el `envelope()`, sin `implements ShouldQueue`.

---

## Transporte

### Dev — Mailpit

[Mailpit](https://github.com/axllent/mailpit) corre en Docker. Intercepta todos los correos. Bandeja en `http://localhost:8025`.

```bash
# desde events_infrastructure/
docker compose up -d mailpit
```

```yaml
# events_infrastructure/docker-compose.yml
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

**Nota importante:** n8n cloud no puede llegar al `localhost` de tu máquina. Para ver los correos en Mailpit necesitas o bien correr n8n en Docker local, o exponer el puerto 1025 con un túnel (ngrok). Ver [`docs/n8n-emails.md#dev-local`](./n8n-emails.md#dev-local----cómo-probar) para las tres opciones.

Las variables de `.env` para Mailpit aplican solo si alguna vez usas `Mail::send(new MiMailable())` directamente (fallback manual). El job de n8n no las usa.

```ini
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="tickify@local.dev"
MAIL_FROM_NAME="Tickify"
```

### Prod — proveedor SMTP

Se configura en la credencial **`Tickify SMTP`** dentro de n8n cloud. El `.env` del backend no tiene nada de SMTP para prod; todo está en el panel de n8n.

Opciones documentadas como candidatas: Resend, SendGrid, Mailgun.

---

## Cola

### Variables `.env` relevantes

```ini
QUEUE_CONNECTION=database
APP_URL=http://localhost:8000   # incluir el puerto siempre en dev
```

### Correr el worker

```bash
php artisan queue:work
```

**Reiniciar el worker** después de cualquier cambio de código. Carga la app en memoria al arrancar.

Para un único job puntual:

```bash
php artisan queue:work --once
```

### Jobs fallidos

```bash
php artisan queue:failed
php artisan queue:retry all
php artisan queue:flush        # descarta todo — peligroso
```

Si el webhook de n8n no responde 2xx después de 3 intentos (backoff: 10s, 30s, 60s), el job cae en `failed_jobs` con el mensaje de error. `queue:retry all` lo reprocesa cuando n8n vuelva.

### Supervisor en producción

El worker necesita mantenerse vivo con Supervisor:

```ini
# /etc/supervisor/conf.d/tickify-worker.conf
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

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tickify-worker:*
```

---

## APP_URL y links en los correos

El Blade se renderiza en el contexto HTTP del controller, no en el worker, así que `route()` ya tiene el host correcto. `AppServiceProvider::boot()` sigue llamando `URL::forceRootUrl(config('app.url'))` como capa extra de seguridad.

```ini
APP_URL=http://localhost:8000   # incluir :8000 para php artisan serve
```

Si los links del correo aparecen sin el puerto, este es el problema.
