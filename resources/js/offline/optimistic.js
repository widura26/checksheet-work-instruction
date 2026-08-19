const pendingDeltas = [];

export function registerStockDelta({ kodeMaterial, delta }) {
    pendingDeltas.push({ kodeMaterial, delta });
}

export function applyPendingStockDeltas(tableSelector, kodeColumnIndex, stockColumnIndex) {
    const table = document.querySelector(tableSelector);
    if (!table) {
        return;
    }
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach((row) => {
        const kodeCell = row.querySelector(`td:nth-child(${kodeColumnIndex + 1})`);
        const stockCell = row.querySelector(`td:nth-child(${stockColumnIndex + 1})`);
        if (!kodeCell || !stockCell) {
            return;
        }
        const kode = kodeCell.textContent.trim();
        const delta = pendingDeltas
            .filter((d) => d.kodeMaterial === kode)
            .reduce((acc, d) => acc + d.delta, 0);
        if (delta !== 0) {
            const current = parseInt(stockCell.textContent.replace(/[^\d-]/g, ''), 10);
            if (!isNaN(current)) {
                stockCell.textContent = (current + delta).toString();
            }
        }
    });
}
