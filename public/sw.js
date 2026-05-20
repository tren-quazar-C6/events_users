// ============================================
// Service Worker de Tickify (v2 — robusto)
// ============================================

const CACHE_NAME = 'tickify-v2';  // ⬅ subimos versión para forzar refresco

// App shell mínimo. NO incluimos /catalog aquí — lo cacheamos en runtime
// cuando se visite con éxito, así nunca pre-cacheamos una ruta rota.
const APP_SHELL = [
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// 1. INSTALL
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[SW] Pre-cacheando app shell');
            return cache.addAll(APP_SHELL);
        })
    );
    self.skipWaiting();
});

// 2. ACTIVATE — limpiar cachés viejos (importante: borra el "tickify-v1" corrupto)
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((names) =>
            Promise.all(
                names
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => {
                        console.log('[SW] Borrando caché viejo:', name);
                        return caches.delete(name);
                    })
            )
        )
    );
    return self.clients.claim();
});

// 3. FETCH — network-first, pero SOLO cachea respuestas exitosas
self.addEventListener('fetch', (event) => {
    // Solo GET
    if (event.request.method !== 'GET') return;

    // Ignorar requests que no son HTTP(S) (chrome-extension://, etc.)
    if (!event.request.url.startsWith('http')) return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // ⬅ CLAVE: solo cachear si la respuesta es OK (200-299)
                // y no es una respuesta opaca (cross-origin sin CORS)
                if (response.ok && response.type === 'basic') {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, clone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Sin red: caer al caché si existe
                return caches.match(event.request);
            })
    );
});