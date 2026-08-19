import './sync';
import { initOfflineSync } from './sync';
import { applyPendingStockDeltas } from './optimistic';

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
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