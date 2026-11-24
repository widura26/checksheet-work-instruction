# Sistem Monitoring Checksheet Work Instruction

Aplikasi internal berbasis Laravel 10 untuk monitoring **material planning / purchase requisition** dan pengelolaan **Bill of Material (BOM)** di lingkungan INKA. Antarmuka memakai template CoreUI, login menggunakan NIP, dan akses dibagi menjadi dua peran: `admin` dan `viewer`.

## Fitur utama

- **Material Planning**: dashboard dataset SAP, unggah/import CSV atau Excel, ekspor hasil filter, dan tampilan deviasi.
- **Bill of Material**: kelola BOM, impor dataset BOM lewat antrean, riwayat revisi, dan unduh dokumen PDF.
- **Kode Proyek, Users, Roles**: pengelolaan data pendukung (khusus admin).
- **Activity Log**: jejak aktivitas aplikasi dengan filter tanggal, pengguna, dan fitur.
- **Mode offline**: service worker, halaman offline, dan antrean permintaan saat koneksi terputus.

## Kebutuhan sistem

- PHP 8.1 atau lebih baru (dengan ekstensi `pdo_mysql`, `mbstring`, `xml`, `gd`, `zip`)
- Composer 2
- Node.js 18+ dan npm
- MySQL / MariaDB

## Instalasi

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Atur `DB_*` di `.env`, lalu:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

Buka `http://localhost:8000` dan masuk memakai NIP.

> `DatabaseSeeder` membuat akun admin/viewer dengan password default `123456789`. Ganti password tersebut sebelum aplikasi dipakai di lingkungan produksi.

### Worker dan scheduler

Import BOM dan SAP dijalankan sebagai job antrean, sehingga `QUEUE_CONNECTION=database` memerlukan worker yang berjalan terus:

```bash
php artisan queue:work
```

Pembersihan activity log dijadwalkan harian pukul 02:00 `Asia/Jakarta`. Aktifkan scheduler cron server:

```cron
* * * * * cd /path/ke/checksheet-work-instruction && php artisan schedule:run >> /dev/null 2>&1
```

## Pengujian

Konfigurasi PHPUnit masih memakai database MySQL (baris SQLite di `phpunit.xml` dinonaktifkan), jadi siapkan database testing terlebih dahulu.

```bash
php artisan test
# atau
vendor/bin/phpunit
```

## Dokumentasi teknis

| Topik | File |
| --- | --- |
| Indeks dokumentasi | [`docs/README.md`](docs/README.md) |
| Arsitektur dan alur request | [`docs/architecture.md`](docs/architecture.md) |
| Instalasi dan konfigurasi | [`docs/getting-started.md`](docs/getting-started.md) |
| Referensi route dan API internal | [`docs/reference/routes.md`](docs/reference/routes.md) |
| Model dan skema database | [`docs/reference/data-model.md`](docs/reference/data-model.md) |
| Alur import BOM dan SAP | [`docs/guides/imports.md`](docs/guides/imports.md) |
| Mode offline | [`docs/guides/offline-mode.md`](docs/guides/offline-mode.md) |
| Autentikasi, peran, dan activity log | [`docs/guides/auth-and-activity-log.md`](docs/guides/auth-and-activity-log.md) |
| Keputusan desain dan catatan operasional | [`docs/explanation/design-notes.md`](docs/explanation/design-notes.md) |

## Struktur direktori ringkas

```
app/
  Actions/Fortify/     aksi kustom Fortify (registrasi, reset password, profil)
  Console/             penjadwalan activity log
  Exports/ Imports/    ekspor Excel dan import dataset
  Http/Controllers/    controller web + endpoint JSON
  Jobs/                job antrean import
  Models/              model Eloquent
  Services/ Support/   logika pemrosesan data, mapper kolom, tracker import
config/                konfigurasi Laravel, permission, fortify, bom-sheets
database/              migrasi, seeder, dump SQL proyek
resources/js/offline/  penyimpanan IndexedDB dan sinkronisasi offline
routes/web.php         seluruh route aplikasi (Fortify ditambahkan paket)
tests/                 pengujian Unit dan Feature
```
