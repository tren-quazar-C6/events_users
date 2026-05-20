//
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()

// ============================================
// Registrar el Service Worker para PWA
// ============================================
if ('serviceWorker' in navigator) {
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