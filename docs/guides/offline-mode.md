# Mode offline

## Ringkasan

Aplikasi memiliki fondasi PWA: service worker untuk aset dan data dashboard, halaman fallback offline, antrean form di IndexedDB, indikator koneksi, dan middleware idempotensi berbasis `X-Client-Request-Id`. Sebagian jalur masih berupa fondasi: route yang dituju antrean offline belum ada di `routes/web.php`, jadi perilaku offline untuk form tertentu belum aktif secara end-to-end.

## Komponen

| Komponen | File | Peran |
| --- | --- | --- |
| Service worker | `public/sw.js` | Cache aset, cache data dashboard 45 detik, fallback halaman offline |
| Halaman fallback | `public/offline.html` | Pesan "Anda sedang offline" + tombol retry |
| Registrasi SW | `resources/js/offline/index.js:9-21` | `navigator.serviceWorker.register('/sw.js', { updateViaCache: 'none' })` |
| Entry point JS | `resources/js/app.js:58` | Mengimpor modul offline setelah jQuery dipasang ke `window.$` |
| IndexedDB | `resources/js/offline/db.js` | Database `ct-offline-db` v1, object store `queue` |
| Antrean & replay | `resources/js/offline/sync.js` | Menangkap submit, menyimpan request, memutar ulang saat online |
| Optimistic UI | `resources/js/offline/optimistic.js` | Menyesuaikan angka stok di tabel sebelum sinkronisasi |
| Banner offline | `resources/views/partials/offline-banner.blade.php` | Ditampilkan di `layouts/app.blade.php:9` |
| Idempotensi server | `app/Http/Middleware/HandleClientRequestId.php` | Menyimpan dan mengembalikan ulang response untuk ID yang sama |
| Model pendukung | `app/Models/OfflineRequest.php` + migrasi `2026_08_14_000000_create_offline_requests_table.php` | Menyimpan `client_request_id`, method, URL, status, body |

Halaman login tidak memuat `@vite`, sehingga service worker tidak terdaftar di halaman tersebut.

## Strategi cache service worker

Nama cache: `ct-offline-v3-static` dan `ct-offline-v3-runtime` (`public/sw.js:1-3`). Saat activate, cache lama yang tidak diawali `ct-offline-v3` dihapus (`L20-31`).

| Permintaan | Strategi | Catatan |
| --- | --- | --- |
| `/build/*` | Cache-first | Aman karena aset Vite punya hash konten; hanya response `ok` yang disimpan (`L69-86`) |
| `/template/*`, `/manifest.json`, `/favicon.ico` | Network-first, fallback cache | `cache: 'no-cache'` (`L88-105`) |
| `/material-planning/data`, `/sap-dataset/deviation` | Cache-first dengan TTL 45 detik | TTL memakai header buatan `x-sw-cached-at` (`L107-136`) |
| Lainnya | Network-first, fallback cache lalu `/offline.html` untuk navigasi | `ignoreSearch: true` saat mencari fallback (`L138-159`) |

Non-GET dan request cross-origin tidak disentuh (`L60-67`). `serveCachedResponse()` (`L162-182`) menulis ulang parameter `draw` pada JSON DataTables yang diambil dari cache agar halaman berikutnya tidak mengulang nomor halaman yang sama.

Path deviasi yang sebenarnya adalah `/material-planning/deviation` (`routes/web.php:24`), bukan `/sap-dataset/deviation`. Jadi cabang cache 45 detik untuk deviasi saat ini tidak pernah terpakai; request tersebut masuk strategi generik.

Selain SW, `public/js/sap-dataset/index.js:12-69` punya cache 60 detik di memori per URL plus deduplikasi request paralel. Setelah import selesai (`L665`, `L743`), cache SW runtime untuk `/material-planning/data` dihapus agar data langsung segar.

## Antrean form

`sync.js:185-194` hanya mengantre form POST yang `action`-nya mengandung `/pengurangan-tool` atau `/update-stok-tool`. Form yang memuat `File` dengan nilai tidak kosong ditolak (`serializeForm()`, `L52-62`).

Data yang disimpan per entri (`queueForm()`, `L64-84`):

```js
{
  clientRequestId: uuid(),
  url: form.action,
  method: form.method,
  fields: [[name, value], ...],
  csrfToken,
  createdAt
}
```

`uuid()` di `db.js:48-57` memakai `crypto.randomUUID` dengan fallback `Math.random()`.

Saat `online` atau saat halaman dimuat, `replayQueue()` (`L106-183`) memutar antrean secara berurutan dengan header:

- `X-Client-Request-Id`
- `X-CSRF-TOKEN`
- `X-Requested-With: XMLHttpRequest`
- `Accept: application/json`

Penanganan response:

| Hasil | Tindakan |
| --- | --- |
| Gagal koneksi atau ada redirect | Antrean tetap disimpan, status offline dipertahankan |
| 2xx | Entri dihapus dari IndexedDB |
| 400 / 409 / 422 | Antrean tetap, ditawarkan tautan penyelesaian manual ke `/dashboard` |
| 419 | Antrean tetap, pengguna diminta reload untuk CSRF baru |
| Status lain | Notifikasi gagal umum, antrean tetap |

`registerStockDeltaFromForm()` (`L86-104`) hanya mengenali action di atas dan mengambil kode material serta delta bertanda dari beberapa nama field. `optimistic.js` menyimpan delta di memori (`pendingDeltas`) dan menerapkannya ke kolom 1 dan 7 pada tabel `#table-tool` setiap event `draw.dt` (`offline/index.js:26-35`). Delta tidak dihapus setelah sinkronisasi berhasil dan tidak di-rollback saat gagal.

## Idempotensi di server

`HandleClientRequestId` (`app/Http/Middleware/HandleClientRequestId.php:12-44`):

1. Tanpa header `X-Client-Request-Id`, request diteruskan biasa.
2. ID sudah ada di `offline_requests` dan punya `redirect_url` -> redirect dengan pesan "Operasi ini sudah diproses sebelumnya."
3. ID sudah ada tanpa `redirect_url` -> kembalikan `response_body` dengan `status_code` tersimpan (header asli tidak dikembalikan).
4. ID baru -> jalankan request, lalu simpan ID, method, URL, status, Location redirect, body, dan `auth()->id()`.

Tabel `offline_requests` memiliki index unik pada `client_request_id(64)` dan tidak punya mekanisme TTL/cleanup. Baris tabel ini juga tercatat di activity log oleh observer global.

## Status implemented dan keterbatasan yang diketahui

- Alias `client.request.id` terdaftar di `app/Http/Kernel.php:71`, tetapi **tidak ada route produksi yang memakainya**. Pemasangan hanya ada di `tests/Feature/HandleClientRequestIdTest.php`. Jadi header yang dikirim replay saat ini diabaikan endpoint.
- URL antrean `/pengurangan-tool`, `/update-stok-tool`, dan tautan perbaikan `/dashboard` tidak ada di `routes/web.php`. Jalur submit offline ini belum berfungsi penuh sampai route-route tersebut tersedia.
- Manifest yang direferensikan `partials/head.blade.php:7` adalah `/template/assets/favicon/manifest.json`; ikon di dalamnya memakai path root `/android-icon-*.png`, sementara file aslinya ada di `/template/assets/favicon/`, sehingga ikon PWA bisa 404.
- Cache service worker menyimpan halaman dan data yang diautentikasi. Belum ada purge saat logout atau perubahan peran, dan response cached tidak dibedakan per user.
- `public/build/` harus dihasilkan dengan `npm run build` agar modul offline ikut terbundle.

## Pengujian

`tests/Feature/HandleClientRequestIdTest.php` menguji perilaku middleware dengan beralih ke SQLite in-memory dan membuat tabel `offline_requests` secara manual. Test tersebut belum menguji isi body/status yang dikembalikan, dan belum ada test otomatis untuk service worker, IndexedDB, atau alur replay form.
