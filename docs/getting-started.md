# Getting started

## 1. Prasyarat

| Komponen | Versi / catatan |
| --- | --- |
| PHP | `^8.1` (composer.json); lingkungan pengembangan yang teruji: PHP 8.2 |
| Composer | 2.x |
| Node.js + npm | untuk Vite; direkombinasi dengan `npm run build` |
| MySQL / MariaDB | `DB_CONNECTION=mysql` di `.env.example` |
| Ekstensi PHP | `pdo_mysql`, `mbstring`, `xml`, `gd`, `zip` (phpspreadsheet / maatwebsite/excel) |

Dependensi utama: Laravel 10, Laravel Fortify, Livewire 3, spatie/laravel-permission, spatie/laravel-activitylog, maatwebsite/excel, dompdf, yajra/datatables, dan picqer/php-barcode-generator. Daftar lengkap ada di [`composer.json`](../composer.json).

## 2. Instalasi

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Isi koneksi database di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=checksheet-work-instruction
DB_USERNAME=...
DB_PASSWORD=...
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

Lalu jalankan:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

`storage:link` membuat `public/storage` -> `storage/app/public`, dipakai untuk PDF revisi BOM. Jika link sudah ada, perintah ini aman dijalankan ulang.

## 3. Akun dari seeder

`DatabaseSeeder` memanggil `UserSeeder` dan `ProjectSeeder` dengan logging activity dimatikan sementara. `UserSeeder` membuat peran `admin` dan `viewer` serta tiga akun:

| Email | NIP | Peran |
| --- | --- | --- |
| `hadi.wuriyanto@inka.co.id` | `991200017` | admin |
| `dedy.kurniawan@inka.co.id` | `991700045` | viewer |
| `it@gmail.com` | `123456789` | admin |

Ketiganya memakai password `123456789`. Login memakai **NIP** (`config/fortify.php` mengatur `username => nip`), bukan email. Ganti password default sebelum dipakai di produksi.

Setelah login, Fortify mengarahkan ke `/material-planning` (`fortify.home`).

## 4. Proses yang harus berjalan terus

### Queue worker

Import dataset BOM masuk antrean database (`QUEUE_CONNECTION=database`, tabel `jobs`). Tanpa worker, status import tidak akan berubah dari `pending`. Import rekap material (`/material-planning/import-excel`) berjalan langsung di request HTTP dan tidak memerlukan worker.

```bash
php artisan queue:work
```

Untuk lingkungan produksi, jalankan lewat supervisor atau systemd. Job yang gagal tercatat di tabel `failed_jobs` (lihat `config/queue.php`).

### Scheduler

`app/Console/Kernel.php` menjadwalkan `activitylog:clean --days=7` setiap hari pukul 02:00 zona `Asia/Jakarta`. Tambahkan cron:

```cron
* * * * * cd /path/ke/checksheet-work-instruction && php artisan schedule:run >> /dev/null 2>&1
```

## 5. Konfigurasi penting

| File | Yang diatur |
| --- | --- |
| `.env` | koneksi DB, `QUEUE_CONNECTION`, `FILESYSTEM_DISK`, `APP_URL` |
| `config/fortify.php` | login memakai NIP, `home => /material-planning`, fitur Fortify aktif |
| `config/permission.php` | nama tabel spatie/laravel-permission |
| Activity log | paket spatie/laravel-activitylog memakai konfigurasi default (file `config/activitylog.php` tidak dipublish) |
| `config/bom-sheets.php` | daftar sheet/kolom yang dipakai import BOM |
| `phpunit.xml` | environment testing; koneksi SQLite sengaja dinonaktifkan |

## 6. Verifikasi instalasi

```bash
php artisan route:list
php artisan about
php artisan test
```

`php artisan test` membutuhkan database MySQL yang bisa diakses sesuai `.env` karena baris `DB_CONNECTION=sqlite` di `phpunit.xml` masih berupa komentar.

## 7. Masalah umum

| Gejala | Penyebab / penanganan |
| --- | --- |
| Halaman kosong atau aset Vite 404 | Jalankan `npm install && npm run build` lalu pastikan `public/build/manifest.json` ada |
| Status import tidak pernah selesai | Worker tidak berjalan; jalankan `php artisan queue:work` |
| PDF revisi tidak bisa diunduh | `php artisan storage:link` belum dijalankan atau izin `storage/app/public` salah |
| Login gagal walaupun email benar | Gunakan NIP sebagai username |
| Test gagal "database tidak ditemukan" | `phpunit.xml` memakai MySQL dari `.env`, bukan SQLite in-memory |
