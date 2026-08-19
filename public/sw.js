const CACHE_VERSION = 'ct-offline-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

const STATIC_ASSETS = [
    '/offline.html',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => !key.startsWith(CACHE_VERSION))
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

const isStaticAsset = (url) => {
    return (
        url.pathname.startsWith('/template/') ||
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/manifest.json') ||
        url.pathname === '/favicon.ico'
    );
};

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET') {
        return; // non-GET requests go to the network untouched
    }

    // Same-origin only; ignore external requests.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Static assets: cache-first, then network.
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) {
                    return cached;
                }
                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Navigations + GET JSON (DataTables, api): network-first, cached fallback.
    event.respondWith(
        fetch(request)
            .then((response) => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => {
                return caches.match(request, { ignoreSearch: true }).then((cached) => {
                    if (cached) {
                        return serveCachedResponse(request, cached);
                    }
                    if (request.mode === 'navigate') {
                        return caches.match('/offline.html');
                    }
                    return Response.error();
                });
            })
    );
});

async function serveCachedResponse(request, cached) {
    const currentDraw = new URL(request.url).searchParams.get('draw');
    if (currentDraw === null) {
        return cached;
    }
    const text = await cached.text();
    let json;
    try {
        json = JSON.parse(text);
    } catch (e) {
        return new Response(text, { status: cached.status });
    }
    if (json && typeof json.draw !== 'undefined') {
        json.draw = parseInt(currentDraw, 10);
        return new Response(JSON.stringify(json), {
            status: cached.status,
            headers: { 'Content-Type': 'application/json' },
        });
    }
    return new Response(text, { status: cached.status });
}
