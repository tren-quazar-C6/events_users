# PWA — Progressive Web App

Tickify es instalable como PWA. Esta página documenta los archivos involucrados, la estrategia de caché y los cambios de mantenimiento más comunes.

## Archivos

| Archivo | Propósito |
|---------|-----------|
| `public/manifest.json` | Metadata de la app instalable (nombre, colores, iconos, start URL) |
| `public/sw.js` | Service Worker — intercepta peticiones y gestiona el caché offline |
| `public/icons/icon-192.png` | Icono requerido por el manifest (192×192 px) |
| `public/icons/icon-512.png` | Icono para splash screen en Android (512×512 px) |
| `public/icons/icon.svg` | Fuente vectorial de los iconos |
| `resources/js/app.js` | Registra el SW en cada carga de página |
| `resources/views/layouts/app.blade.php` | Enlaza `manifest.json` y define `theme-color` vía `<meta>` y `<link>` |

## Estrategia de caché

El SW usa **network-first**:

1. Intenta la petición a la red
2. Si responde con éxito (`response.ok && response.type === 'basic'`) → almacena en caché
3. Si no hay red → sirve desde el caché si existe

El **app shell** (manifest + iconos) se pre-cachea en la instalación del SW para que la app abra instantáneamente aunque la red tarde.

## Versioning

```js
// public/sw.js
const CACHE_NAME = 'tickify-v2';
```

Cada vez que se despliega con cambios en assets, incrementar la versión:
- `tickify-v2` → `tickify-v3`

El evento `activate` borra automáticamente los cachés de versiones anteriores.

## Criterios de instalabilidad (cumplidos)

- [x] Servido sobre HTTPS (o `localhost`)
- [x] `manifest.json` con `name`, `short_name`, `start_url`, `display: standalone`
- [x] Icono ≥ 192×192 px en el manifest
- [x] Service Worker registrado con handler de `fetch`

## Limitaciones

- iOS (Safari) no muestra prompt automático de instalación — el usuario debe usar `Compartir → Agregar a pantalla de inicio`
- No implementa Push Notifications ni Background Sync
