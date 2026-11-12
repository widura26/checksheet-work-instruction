import './sync';
import { initOfflineSync } from './sync';
import { applyPendingStockDeltas } from './optimistic';

if ('serviceWorker' in navigator) {
    const hadController = Boolean(navigator.serviceWorker.controller);
    let refreshing = false;

    navigator.serviceWorker
        .register('/sw.js', { updateViaCache: 'none' })
        .catch(() => {});

    // A new service worker took control (i.e. the app was updated). Reload
    // once so this page uses the freshly fetched assets, not the old cache.
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (!hadController || refreshing) {
            return;
        }
        refreshing = true;
        window.location.reload();
    });
}

document.addEventListener('DOMContentLoaded', initOfflineSync);

document.addEventListener('DOMContentLoaded', () => {
    if (!window.$) {
        return;
    }
    $(document).on('draw.dt', (event) => {
        if (event.target.id === 'table-tool') {
            applyPendingStockDeltas('#table-tool', 1, 7);
        }
    });
});