// Alpine is bundled and started automatically by Livewire 3.
// Do NOT import or start it manually here — it causes a double-init conflict.

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