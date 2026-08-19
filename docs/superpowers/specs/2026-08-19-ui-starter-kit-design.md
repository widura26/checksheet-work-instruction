# Design: UI Starter Kit untuk Sistem Baru ("NewSystem")

Tanggal: 2026-08-19
Status: Disetujui

## Ringkasan

Membangun project Laravel 10 baru di `D:\project\NewSystem` yang kulit UInya identik
dengan sistem **Central Tool (CT)** di repo ini: tema CoreUI 5, branding INKA,
layout + partials, login, dashboard, serta contoh halaman tabel dan form — siap diisi
modul bisnis domain berbeda. Tidak ada logika bisnis CT yang disalin.

## Tujuan & Batasan

- **Domain baru**: sistem baru untuk kebutuhan berbeda; hanya tampilan/kulit UI yang sama.
- **Stack**: Laravel 10, Livewire 3, Fortify, spatie (permission + activitylog),
  yajra DataTables, CoreUI 5, Vite, jQuery/select2/SweetAlert2/ApexCharts.
- **Branding**: dipertahankan (logo & nama INKA).
- **Non-tujuan**: salinan modul bisnis CT, mode offline, konfigurasi produksi.

## Arsitektur

Project baru berupa app Laravel 10 mandiri dengan struktur:

```
D:\project\NewSystem\
├── public/template/          # CoreUI 5 statis + aset INKA (disalin utuh)
├── resources/css/app.css     # import library + tema custom
├── resources/js/app.js       # library global di window ($, DataTables, Swal, Apex, select2)
├── vite.config.js            # alias jquery
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── partials/             # head, sidebar, header, footer, scripts, breadcrumb
│   ├── auth/login.blade.php
│   └── pages/
│       ├── dashboard.blade.php
│       ├── data-master/index.blade.php
│       ├── data-master/create.blade.php
│       └── data-master/edit.blade.php
├── app/Http/Middleware/CheckUserActive.php
├── app/Providers/FortifyServiceProvider.php
├── app/Observers/GlobalObserverActivityLogs.php
└── app/Livewire/DataMaster/CreateDataMaster.php
```

## Komponen

### 1. Tema & aset (disalin utuh dari CT)

- `public/template/` — 24MB CoreUI 5 statis + logo/favicon INKA.
- Tidak ada perubahan isi file tema.

### 2. Build frontend (disalin + disesuaikan)

- `package.json` — dependency NPM yang sama dengan CT.
- `vite.config.js` — alias jquery global untuk DataTables.
- `resources/js/bootstrap.js` — axios + CSRF default Laravel.
- `resources/js/app.js` — expose global: `$`, DataTables (bs5 + responsive),
  select2, SweetAlert2, ApexCharts.
- `resources/css/app.css` — import CSS select2, DataTables bs5, responsive; tema
  sidebar primary.

### 3. Layout & partials (digenerikkan)

Sumber: `resources/views/layouts/app.blade.php` dan `partials/*` di CT, disalin lalu
**digenerikkan**:

- Hapus semua `request()->routeIs('...')` yang mengacu route CT.
- Sidebar diganti menu placeholder generik:
  - Dashboard
  - Data Master (grup: Data Master, Form Master)
  - Pengaturan (hanya admin): Pengguna, Activity Log
- Partial `head`, `header`, `footer`, `scripts`, `breadcrumb` disalin apa adanya,
  hanya teks aplikasi/grup menu yang digenerikkan.
- `offline-banner` **tidak disalin** (mode offline di luar scope).

### 4. Autentikasi (Fortify + login NIP)

- `config/fortify.php`: `username => 'nip'`, home `/dashboard`.
- `FortifyServiceProvider`: login view `auth.login`, `authenticateUsing` (NIP 9 digit
  + password + `is_active`), rate limit login 5/menit.
- Tabel `users`: kolom `name`, `email`, `password`, `nip`, `is_active`.
- Middleware `CheckUserActive` (`auth.active`) — blokir user nonaktif, didaftarkan
  di Kernel.
- Seeder role: `admin`, `staff`, `viewer`.

### 5. Otorisasi & audit (disalin)

- spatie/laravel-permission: `HasRoles` di model User, middleware `role`/`permission`
  didaftarkan di Kernel, sidebar filter per role (admin/staff/viewer).
- spatie/laravel-activitylog + `GlobalObserverActivityLogs` — satu observer,
  auto-register semua model di `AppServiceProvider`, event created/updated/deleted.

### 6. Halaman contoh (generik)

- **Dashboard**: kartu statistik statis + chart ApexCharts (ganti data nanti).
- **Data Master** (CRUD DataTables): controller `DataMasterController` dengan
  `index()` + `data()` (endpoint JSON server-side via yajra, proteksi `$request->ajax()`),
  `store/update/destroy`. Halaman `index` memuat filter + tabel + delete via
  SweetAlert2. Modal import Excel sebagai contoh (maatwebsite).
- **Form Master** (Livewire): komponen `CreateDataMaster` — form create/edit dengan
  validasi + flash message. Dipakai sebagai pola modul berikutnya.

## Data flow

1. `index` → Blade merender tabel kosong; JS inisialisasi DataTables `serverSide: true`
   memanggil `GET /data-master/data`.
2. `data()` di controller: query DB → `DataTables::of($query)->addIndexColumn()->make(true)`.
3. Filter: input di halaman dikirim via `table.ajax.reload()` + params.
4. Form Livewire: validasi di komponen, simpan ke DB, flash success, redirect index.

## Error handling

- Endpoint `/data` non-AJAX → `abort(404)`.
- Validasi form → pesan error per field (Blade / Livewire).
- Flash `success` / `error` session ditampilkan di halaman index.
- Delete gagal → SweetAlert2 error.

## Testing

- `php artisan test` — contoh test: login redirect, akses dashboard auth, role
  protection (staff tidak bisa buka halaman admin).
- Tidak menyalin test bisnis CT.

## Checklist implementasi

1. `composer create-project laravel/laravel` di `D:\project\NewSystem`
2. Install package backend (fortify, spatie, yajra, maatwebsite, livewire, dompdf,
   picqer) + publish + migrate
3. Salin `public/template/` + `resources/` CSS/JS + `vite.config.js` + `package.json`
4. Layout & partials digenerikkan
5. Fortify + middleware auth.active + seeder role
6. GlobalObserverActivityLogs + AppServiceProvider
7. Halaman dashboard + Data Master + Form Master
8. `npm install && npm run build`
9. `php artisan test` hijau
10. Commit di repo NewSystem