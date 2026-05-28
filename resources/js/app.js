// Alpine is bundled and started automatically by Livewire 3.
// Do NOT import or start it manually here — it causes a double-init conflict.

const isLocalHost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);

if (isLocalHost && 'serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then((registrations) => {
        registrations.forEach((registration) => registration.unregister());
    });

    if ('caches' in window) {
        caches.keys().then((keys) => keys.forEach((key) => caches.delete(key)));
    }
}

// Register the PWA service worker only in production. In local dev it can serve
// stale pages after branch merges and make buttons look broken.
if (! isLocalHost && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker
            .register('/sw.js')
            .then((registration) => {
                console.log('[Tickify] Service Worker registrado:', registration.scope);
            })
            .catch((error) => {
                console.error('[Tickify] Falló el registro del SW:', error);
            });
    });
}
