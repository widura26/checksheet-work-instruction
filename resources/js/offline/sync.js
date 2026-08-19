import { queueAdd, queueAll, queueDelete, uuid } from './db';
import { registerStockDelta, applyPendingStockDeltas } from './optimistic';

const csrfToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

function setBanner(state, message = '') {
    const banner = document.getElementById('offline-banner');
    if (!banner) {
        return;
    }
    const text = document.getElementById('offline-banner-text');
    if (state === 'online') {
        banner.classList.add('d-none');
        banner.classList.remove('offline-banner--offline', 'offline-banner--sync');
        return;
    }
    banner.classList.remove('d-none');
    banner.classList.remove('offline-banner--offline', 'offline-banner--sync');
    if (state === 'offline') {
        banner.classList.add('offline-banner--offline');
        text.textContent = message || 'Anda sedang offline. Data yang tampil adalah kondisi terakhir tersinkron.';
    } else if (state === 'sync') {
        banner.classList.add('offline-banner--sync');
        text.textContent = message || 'Menyinkronkan operasi...';
    }
}

function showToast(message, actionLabel = null, actionFn = null) {
    const container = document.getElementById('offline-toast-container');
    if (!container) {
        return;
    }
    const toast = document.createElement('div');
    toast.className = 'offline-toast';
    const msg = document.createElement('div');
    msg.textContent = message;
    toast.appendChild(msg);
    if (actionLabel && actionFn) {
        const btn = document.createElement('button');
        btn.className = 'btn btn-sm btn-outline-danger';
        btn.textContent = actionLabel;
        btn.addEventListener('click', () => actionFn());
        toast.appendChild(btn);
    }
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 15000);
}

function serializeForm(form) {
    const data = new FormData(form);
    const entries = [];
    for (const [key, value] of data.entries()) {
        if (value instanceof File && value.size > 0) {
            return null; // file uploads are not queued offline
        }
        entries.push([key, value]);
    }
    return entries;
}

async function queueForm(form) {
    const entries = serializeForm(form);
    if (entries === null) {
        showToast('Upload file tidak dapat dilakukan saat offline.');
        return;
    }
    const tokenInput = form.querySelector('input[name="_token"]');
    const entry = {
        clientRequestId: uuid(),
        url: form.action,
        method: (form.method || 'POST').toUpperCase(),
        fields: entries,
        csrfToken: tokenInput ? tokenInput.value : csrfToken(),
        createdAt: Date.now(),
    };
    await queueAdd(entry);
    registerStockDeltaFromForm(form, entries);
    setBanner('offline');
    showToast('Transaksi disimpan secara lokal. Akan tersinkron saat koneksi kembali.');
    applyPendingStockDeltas('#table-tool', 1, 7);
}

function registerStockDeltaFromForm(form, entries) {
    const action = form.action || '';
    const isPengurangan = action.includes('/pengurangan-tool');
    const isUpdateStock = action.includes('/update-stok-tool');
    if (!isPengurangan && !isUpdateStock) {
        return;
    }
    const fields = Object.fromEntries(entries);
    const kodeMaterial = fields.kode_material || fields['tools[0][kode_material]'];
    let delta = 0;
    if (isPengurangan) {
        delta = -(parseInt(fields.qty_dikurangi || fields['tools[0][qty_dikurangi]'], 10) || 0);
    } else {
        delta = parseInt(fields.qty_ditambah || fields.jumlah_baru || fields.qty, 10) || 0;
    }
    if (kodeMaterial && delta !== 0) {
        registerStockDelta({ kodeMaterial, delta });
    }
}

let replaying = false;

async function replayQueue() {
    if (replaying) return;
    replaying = true;
    try {
        await doReplayQueue();
    } finally {
        replaying = false;
    }
}

async function doReplayQueue() {
    const queue = await queueAll();
    if (queue.length === 0) {
        setBanner('online');
        return;
    }
    setBanner('sync', `Menyinkronkan ${queue.length} operasi...`);

    for (const entry of queue) {
        const body = new FormData();
        entry.fields.forEach(([key, value]) => body.append(key, value));

        let response;
        try {
            response = await fetch(entry.url, {
                method: entry.method,
                body,
                headers: {
                    'X-Client-Request-Id': entry.clientRequestId,
                    'X-CSRF-TOKEN': entry.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                redirect: 'follow',
            });
        } catch (e) {
            showToast('Koneksi belum stabil. Operasi tetap menunggu sinkronisasi.');
            setBanner('offline');
            return;
        }

        if (response.redirected) {
            showToast('Server mengalihkan tanpa konfirmasi. Operasi tetap menunggu sinkronisasi.');
            setBanner('offline');
            return;
        }

        if (response.ok) {
            await queueDelete(entry.id);
            continue;
        }

        if (response.status === 422 || response.status === 409 || response.status === 400) {
            showToast('Transaksi gagal tersinkron (data tidak valid). Periksa dan selesaikan secara manual.', 'Lihat & Selesaikan', () => {
                window.location.href = '/dashboard';
            });
            setBanner('offline');
            return;
        }

        if (response.status === 419) {
            showToast('Sesi berakhir. Muat ulang halaman lalu sinkronkan ulang.', 'Muat Ulang', () => {
                window.location.reload();
            });
            return;
        }

        showToast(`Transaksi gagal tersinkron (HTTP ${response.status}).`);
        setBanner('offline');
        return;
    }

    await queueAll().then((remaining) => {
        setBanner(remaining.length === 0 ? 'online' : 'offline');
    });
}

function isQueuableForm(form) {
    if (!form || form.method !== 'post') {
        return false;
    }
    const action = form.action || '';
    return (
        action.includes('/pengurangan-tool') ||
        action.includes('/update-stok-tool') ||
        action.includes('/distribusi')
    );
}

export function initOfflineSync() {
    if (!('indexedDB' in window)) {
        return;
    }

    const updateConnectionState = () => {
        if (navigator.onLine) {
            replayQueue();
        } else {
            setBanner('offline');
        }
    };

    window.addEventListener('online', updateConnectionState);
    window.addEventListener('offline', () => setBanner('offline'));

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!isQueuableForm(form)) {
            return;
        }
        if (!navigator.onLine) {
            event.preventDefault();
            event.stopPropagation();
            queueForm(form);
        }
    }, true);

    window.addEventListener('load', () => {
        if (navigator.onLine) {
            replayQueue();
        } else {
            setBanner('offline');
        }
    });
}