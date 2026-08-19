# Referensi Blueprint Sistem Web Baru

Dokumen ini adalah **referensi/blueprint** untuk membangun sistem web baru (terpisah dari sistem CT) dengan mencontek pola-pola yang sudah terbukti di project **Central Tool (CT)** — sebuah aplikasi Laravel 10 yang memakai Livewire, DataTables, CoreUI, Fortify, dan spatie packages.

Tujuannya: Anda tidak perlu riset ulang. Cukup salin bagian yang relevan, sesuaikan nama, dan jalankan. Setiap bagian diberi keterangan **dari mana asalnya** di project CT agar mudah ditelusuri.

---

## Daftar Isi

1. [Stack Inti yang Disarankan](#1-stack-inti-yang-disarankan)
2. [Package Composer (Backend)](#2-package-composer-backend)
3. [Package NPM (Frontend)](#3-package-npm-frontend)
4. [CSS & Tema](#4-css--tema)
5. [Middleware](#5-middleware)
6. [Autentikasi (Fortify + login custom)](#6-autentikasi-fortify--login-custom)
7. [Roles & Permissions (spatie)](#7-roles--permissions-spatie)
8. [Activity Log Global (Observer)](#8-activity-log-global-observer)
9. [DataTables Server-Side](#9-datatables-server-side)
10. [Import / Ekspor Excel](#10-import--ekspor-excel)
11. [PDF & Barcode](#11-pdf--barcode)
12. [Struktur Direktori & Konvensi](#12-struktur-direktori--konvensi)
13. [Mode Offline (Opsional)](#13-mode-offline-opsional)
14. [Konfigurasi .env](#14-konfigurasi-env)
15. [Checklist Membangun Sistem Baru](#15-checklist-membangun-sistem-baru)

---

## 1. Stack Inti yang Disarankan

| Layer | Pilihan | Alasan dipakai di CT |
|---|---|---|
| Framework | **Laravel 10** (`^10.10`) | Stabil, ekosistem packages lengkap |
| Bahasa | PHP `^8.1` | — |
| Frontend interaktif | **Livewire 3** | Form kompleks tanpa JS berat (SPM, Transfer Posting) |
| Tabel data | **yajra DataTables + datatables.net** | Tabel server-side dengan filter, pagination, export |
| Tema | **CoreUI 5** (statis di `public/template/`) | Dashboard admin siap pakai, brandable |
| Build | **Vite** | Standar Laravel 10 |
| Auth | **Laravel Fortify** + custom `authenticateUsing` | Login via field selain email (NIP) |
| Otorisasi | **spatie/laravel-permission** | Role-based access di route level |
| Audit | **spatie/laravel-activitylog** + observer global | Auto-log semua model |

> **Langkah pertama:** `composer create-project laravel/laravel` lalu `npm install`.

---

## 2. Package Composer (Backend)

Dari `composer.json` project CT. Salin sesuai kebutuhan — tidak semua wajib, tergantung fitur sistem baru.

### Wajib untuk kebanyakan sistem admin

```json
"require": {
    "php": "^8.1",
    "laravel/framework": "^10.10",
    "livewire/livewire": "^3.7",
    "laravel/fortify": "^1.32",
    "laravel/sanctum": "^3.3",
    "spatie/laravel-permission": "^6.4",
    "spatie/laravel-activitylog": "^4.8",
    "yajra/laravel-datatables-oracle": "10.6",
    "guzzlehttp/guzzle": "^7.2"
}
```

### Opsional sesuai fitur

```json
"require": {
    "maatwebsite/excel": "^3.1",              // import & ekspor Excel
    "phpoffice/phpspreadsheet": "^1.30",      // dependensi Excel
    "barryvdh/laravel-dompdf": "^3.1",        // cetak PDF (berita acara, SPM)
    "picqer/php-barcode-generator": "^3.2"    // generate barcode
}
```

### Dev

```json
"require-dev": {
    "enlightn/enlightn": "^2.10",     // analisis keamanan/performansi
    "laravel/pint": "^1.0",           // code style
    "laravel/sail": "^1.18",          // docker dev
    "phpunit/phpunit": "^10.1",
    "laravel/tinker": "^2.8"
}
```

### Peta kegunaan per package

| Package | Fungsi di CT | Contoh file |
|---|---|---|
| `livewire/livewire` | Form & halaman interaktif tanpa reload | `app/Livewire/Spm/CreateSpm.php` |
| `laravel/fortify` | Auth scaffolding + login NIP | `app/Actions/Fortify/`, `app/Providers/FortifyServiceProvider.php` |
| `spatie/laravel-permission` | Roles admin/staff/viewer | `routes/web.php` (`role:admin\|staff\|viewer`) |
| `spatie/laravel-activitylog` | Audit log semua perubahan data | `app/Observers/GlobalObserverActivityLogs.php` |
| `yajra/laravel-datatables-oracle` | DataTables server-side (JSON) | `app/Http/Controllers/DaftarKodeMaterialController.php` |
| `maatwebsite/excel` | Import/export .xlsx | `app/Imports/`, `app/Exports/` |
| `barryvdh/laravel-dompdf` | PDF berita acara & SPM | `app/Http/Service/CreateSpmFileService.php` |
| `picqer/php-barcode-generator` | Barcode CODE_128 material | `app/Http/Controllers/BarcodeController.php` |

---

## 3. Package NPM (Frontend)

Dari `package.json` project CT:

```json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    },
    "devDependencies": {
        "axios": "^1.6.4",
        "laravel-vite-plugin": "^1.0.0",
        "vite": "^5.0.0"
    },
    "dependencies": {
        "apexcharts": "^5.10.4",
        "datatables.net": "^2.3.7",
        "datatables.net-dt": "^2.3.7",
        "datatables.net-responsive": "^3.0.8",
        "datatables.net-responsive-bs5": "^3.0.8",
        "jquery": "^4.0.0",
        "select2": "^4.1.0-rc.0",
        "sweetalert2": "^11.26.22"
    }
}
```

### Peta kegunaan

| Package | Fungsi |
|---|---|
| `jquery` | Dasar DataTables & AJAX calls |
| `datatables.net` + `-bs5` + `-responsive` | Tabel server-side responsif gaya Bootstrap 5 |
| `select2` | Dropdown pencarian (contoh: cascade departemen/bagian/unit) |
| `sweetalert2` | Konfirmasi hapus, notifikasi sukses/error |
| `apexcharts` | Dashboard chart (KPI, trend, distribusi) |
| `axios` | AJAX (via `bootstrap.js`) |

### File entry `vite.config.js`

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            // penting: expose jQuery global untuk DataTables
            'jquery': path.resolve(__dirname, 'node_modules/jquery/dist/jquery.js'),
        },
    },
});
```

### Bootstrap global (`resources/js/app.js`) — pola yang dipakai CT

```js
import "./bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

import "datatables.net-bs5";
import "datatables.net-responsive-bs5";

import select2 from "select2/dist/js/select2.full";
select2(window, $);

import Swal from "sweetalert2";
window.Swal = Swal;

import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;
```

> Semua library di-expose ke `window` agar bisa dipakai langsung di `<script>` Blade per halaman (pola CT: `@stack('js')` di layout).

---

## 4. CSS & Tema

CT memakai **CoreUI 5** sebagai tema dashboard. Strukturnya: tema statis disimpan di `public/template/`, dan custom CSS + import library di `resources/css/app.css` yang di-build Vite.

### Layout & partials (`resources/views/layouts/app.blade.php`)

```
layouts/app.blade.php
├── @include('partials.head')        # asset tema + @vite + csrf-token
├── @livewireStyles
├── @include('partials.offline-banner')
├── @include('partials.sidebar')     # menu per role
├── partials/header
├── @yield('content')
├── partials/footer
├── @include('partials.scripts')     # CoreUI bundle + @stack('js')
└── @livewireScripts
```

### `partials/head.blade.php` — cara memuat tema + Vite

```blade
<link rel="stylesheet" href="{{ asset('template/vendors/simplebar/css/simplebar.css') }}" />
<link rel="stylesheet" href="{{ asset('template/css/vendors/simplebar.css') }}" />
<link href="{{ asset('template/css/style.css') }}" rel="stylesheet" />
<!-- Custom styles + library CSS -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('css')
```

### `partials/scripts.blade.php` — cara memuat JS tema

```blade
<script src="{{ asset('template/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendors/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('template/js/main.js') }}"></script>
@stack('js')
```

### `resources/css/app.css` — import library CSS + custom tema

```css
@import 'select2/dist/css/select2.min.css';
@import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
@import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';

/* Tema sidebar (Bootstrap primary) */
.sidebar-primary {
  --cui-body-color: #ffffff;
  --cui-body-bg: #0d6efd;
  --cui-secondary-bg: #0b5ed7;
  --cui-border-color: #0b5ed7;
}
.sidebar-primary .nav-link.active {
  background-color: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}
```

> **Kunci:** template CoreUI bersifat statis (ditaruh di `public/template/`), sedangkan CSS/JS custom dilewatkan Vite. `@vite` ditempatkan di `head` dan `@stack('js')` untuk skrip per-halaman.

---

## 5. Middleware

Tiga middleware custom di CT yang sangat berguna untuk sistem baru.

### 5.1 `auth.active` — blokir user nonaktif

**Asal:** `app/Http/Middleware/CheckUserActive.php`, didaftarkan di `app/Http/Kernel.php`:

```php
'auth.active' => \App\Http\Middleware\CheckUserActive::class,
```

**Implementasi:**

```php
class CheckUserActive
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
        }
        return $next($request);
    }
}
```

**Cara pakai** (semua route bisnis CT dibungkus begini):

```php
Route::middleware(['auth', 'auth.active'])->group(function () {
    // ...
});
```

> Syarat: kolom `is_active` (boolean) di tabel `users`. Berguna untuk menonaktifkan user tanpa menghapus.

### 5.2 `role` / `permission` — otorisasi spatie

**Asal:** `app/Http/Kernel.php`:

```php
'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
```

**Cara pakai** di `routes/web.php`:

```php
Route::middleware('role:admin|staff|viewer')->group(function () {
    Route::resource('tool', ToolsController::class);
});

Route::middleware('role:admin')->group(function () {
    Route::get('/activity-log', [ActivityLogController::class, 'index']);
});
```

**Cara cek di Blade:**

```blade
@if (Auth::user()->hasRole('admin'))
    <button>Import</button>
@endif
```

### 5.3 `client.request.id` — idempotensi replay offline

**Asal:** `app/Http/Middleware/HandleClientRequestId.php` + tabel `offline_requests`. Ini pola unik CT untuk mode offline (lihat [Bagian 13](#13-mode-offline-opsional)). Berguna jika sistem baru harus berjalan di LAN yang tidak stabil.

```php
'client.request.id' => \App\Http\Middleware\HandleClientRequestId::class,
```

Logikanya: jika request punya header `X-Client-Request-Id` yang sudah pernah diproses, kembalikan respons tersimpan (tidak dieksekusi ulang). Dipakai di route **write** saja:

```php
Route::post('/', [PenguranganToolController::class, 'store'])
    ->name('store')
    ->middleware('client.request.id');
```

---

## 6. Autentikasi (Fortify + login custom)

CT memakai **Fortify** dengan login via **NIP (nomor induk pegawai)**, bukan email. Pola ini bisa ditiru untuk sistem internal yang login pakai nomor pegawai/nik.

### Install Fortify

```bash
composer require laravel/fortify
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan migrate
```

### Konfigurasi `config/fortify.php`

```php
'username' => 'nip',          // kolom login selain email
'home' => '/dashboard',       // redirect setelah login
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

### Login custom via `FortifyServiceProvider`

**Asal:** `app/Providers/FortifyServiceProvider.php`

```php
public function boot(): void
{
    Fortify::loginView(fn () => view('auth.login'));

    Fortify::createUsersUsing(CreateNewUser::class);
    Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

    Fortify::authenticateUsing(function (Request $request) {
        $request->validate([
            'nip' => 'required|numeric|digits:9',
            'password' => 'required',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits'   => 'NIP harus terdiri dari 9 digit.',
        ]);

        $user = User::where('nip', $request->nip)->first();

        if ($user && $user->is_active && Hash::check($request->password, $user->password)) {
            return $user;
        }
        return null;
    });

    RateLimiter::for('login', function (Request $request) {
        $key = Str::lower($request->input(Fortify::username())) . '|' . $request->ip();
        return Limit::perMinute(5)->by($key);
    });
}
```

> Kalau login tetap pakai email: cukup hapus `Fortify::authenticateUsing`, ubah `'username' => 'email'`.

---

## 7. Roles & Permissions (spatie)

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Model User

```php
class User extends Authenticatable
{
    use HasRoles;   // spatie/laravel-permission

    protected $fillable = ['name', 'email', 'password', 'nip', 'is_active'];
}
```

### Roles yang dipakai CT

| Role | Akses |
|---|---|
| `admin` | Semua modul + konfigurasi |
| `staff` | Operasional harian |
| `staff_ct` | Modul spesifik saja |
| `viewer` | Read-only |

### Assign role (tinker / seeder)

```php
$user->assignRole('admin');
// atau saat create
User::create([...])->assignRole('staff');
```

### Route protection

```php
Route::middleware('role:admin|staff|viewer')->group(...);
```

---

## 8. Activity Log Global (Observer)

CT mencatat **semua perubahan semua model** ke tabel `activity_log` secara otomatis — tanpa menambah kode di tiap model. Polanya: satu observer + auto-register di `AppServiceProvider`.

### Observer (`app/Observers/GlobalObserverActivityLogs.php`)

```php
class GlobalObserverActivityLogs
{
    protected function getFeature($model)
    {
        return property_exists($model, 'featureName')
            ? $model->featureName
            : class_basename($model);
    }

    protected function buildProperties($model, array $extra = [])
    {
        return array_merge([
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
            'user_agent' => Request::header('User-Agent'),
        ], $extra);
    }

    public function created($model)
    {
        $this->log($model, 'created', ['new' => $model->getAttributes()]);
    }
    public function updated($model)
    {
        $this->log($model, 'updated', [
            'old' => $model->getOriginal(),
            'new' => $model->getAttributes(),
        ]);
    }
    public function deleted($model)
    {
        $this->log($model, 'deleted', ['old' => $model->getOriginal()]);
    }

    protected function log($model, $event, array $properties = [])
    {
        activity()
            ->useLog($this->getFeature($model))
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties($this->buildProperties($model, $properties))
            ->event($event)
            ->log(class_basename($model) . ' ' . $event);
    }
}
```

### Auto-register di `AppServiceProvider.php`

```php
use Illuminate\Support\Facades\File;

public function boot(): void
{
    Carbon::setLocale('id');

    $modelFiles = File::allFiles(app_path('Models'));

    foreach ($modelFiles as $file) {
        $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();

        if (
            class_exists($class) &&
            is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class) &&
            !(new \ReflectionClass($class))->isAbstract()
        ) {
            $class::observe(GlobalObserverActivityLogs::class);
        }
    }
}
```

> Ini pola terbaik yang bisa ditiru: **satu file observer, semua model ter-audit otomatis.** Halaman admin untuk melihat log ada di `ActivityLogController`.

---

## 9. DataTables Server-Side

Pola standar CT: controller menyediakan endpoint JSON, Blade merender tabel kosong, JS menginisialisasi DataTables.

### 1. Route

```php
Route::get('/daftar-kode-material/data', [DaftarKodeMaterialController::class, 'data'])
    ->name('daftar-kode-material.data');
```

### 2. Controller (`data()` method) — pakai yajra

**Pola asli CT** (`DaftarKodeMaterialController::data`): pakai `DataTables::of()` dengan query builder + filter manual + proteksi ajax.

```php
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

public function data(Request $request)
{
    if (! $request->ajax()) {
        abort(404);   // endpoint data hanya untuk AJAX
    }

    $query = DB::table('daftar_kode_materials')
        ->select(['id', 'kode_material', 'satuan', 'deskripsi', 'spesifikasi']);

    if ($request->filled('kode_material')) {
        $query->where('kode_material', '=', $request->kode_material);
    }
    if ($request->filled('deskripsi')) {
        $query->where('deskripsi', 'LIKE', '%' . $request->deskripsi . '%');
    }

    return DataTables::of($query)
        ->addIndexColumn()
        ->make(true);
}
```

> CT memakai `DB::table`/eloquent **tanpa fitur pencarian bawaan DataTables**; filter ditambahkan manual via `$request` lalu `table.ajax.reload()`. `addIndexColumn()` menghasilkan `DT_RowIndex` untuk kolom nomor urut.

### 3. Blade — inisialisasi

```blade
<table id="table-kode-material" class="table table-striped table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Material</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
</table>

@push('js')
<script type="module">
    $(document).ready(function() {
        var table = $('#table-kode-material').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('daftar-kode-material.data') }}",
                data: function(d) {
                    d.kode_material = $('#kode_material_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode_material', name: 'kode_material' },
                { data: 'deskripsi', name: 'deskripsi' },
            ],
            autoWidth: false,
            scrollX: true,
        });
        // tombol filter
        $('#filter-button').click(function() { table.ajax.reload(); });
    });
</script>
@endpush
```

### Tombol hapus dengan SweetAlert2

```js
$(document).on('click', '.deleteButton', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/resource/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: () => { Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success'); table.ajax.reload(); }
            });
        }
    });
});
```

---

## 10. Import / Ekspor Excel

### Install

```bash
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### Import (Imports class)

**Pola asli CT** (`app/Imports/KodeMaterialImport.php`) — kuat untuk file besar: chunk reading (2k baris), batch insert (1k), skip baris kosong & duplikat, jalan di queue.

```php
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\{
    SkipsEmptyRows, ToCollection, WithBatchInserts,
    WithChunkReading, WithHeadingRow, WithStartRow
};
use Illuminate\Contracts\Queue\ShouldQueue;

class KodeMaterialImport implements
    ToCollection, WithBatchInserts, WithChunkReading,
    WithStartRow, SkipsEmptyRows, WithHeadingRow, ShouldQueue
{
    public function collection($rows)
    {
        $existing = DB::table('daftar_kode_materials')->pluck('kode_material')->flip();

        $insertData = [];
        foreach ($rows as $row) {
            $kode = $row['kode_material'];
            if (isset($existing[$kode])) {
                continue;   // skip duplikat
            }
            $insertData[] = [
                'kode_material' => $kode,
                'satuan'        => $row['bun'],
                'deskripsi'     => $row['deskripsi'],
                'spesifikasi'   => $row['spesifikasi'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
            $existing[$kode] = true;
        }

        if (! empty($insertData)) {
            DB::table('daftar_kode_materials')->insert($insertData); // bulk insert
        }
    }

    public function startRow(): int  { return 2; }     // skip header
    public function chunkSize(): int { return 2000; }  // baca per 2k baris
    public function batchSize(): int { return 1000; }  // insert per 1k baris
}
```

> `ShouldQueue` membuat import berjalan di background — perlu mengatur `QUEUE_CONNECTION` (mis. `database`/`redis`) dan menjalankan `php artisan queue:work`.

**Controller:**

```php
use Maatwebsite\Excel\Facades\Excel;

public function import(Request $request)
{
    if (! Auth::user()->hasRole(['admin'])) {
        abort(403);
    }

    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:xls,xlsx,csv',
    ]);
    if ($validator->fails()) {
        return redirect()->back()->with('error', 'File harus berformat XLS atau XLSX.');
    }

    try {
        Excel::import(new KodeMaterialImport, $request->file('file'));
        return redirect()->back()->with('success', 'Data akan diinput secara bertahap di latar belakang.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
    }
}
```

> Pola **validasi dua-pass** juga dipakai CT (`ImportDistribusiController`): jalankan `ValidationImport` dulu untuk kumpulkan error (`$validator->errors`), kalau bersih baru `Import` yang sebenarnya. Cocok untuk import yang harus presisi.

### Ekspor (Exports class)

**Pola asli CT** (`app/Exports/DistribusiToolExport.php`) — kaya fitur: `FromQuery` + filter via constructor, headings, mapping kolom, auto size, dan styling.

```php
use Maatwebsite\Excel\Concerns\{
    FromQuery, ShouldAutoSize, WithColumnFormatting,
    WithHeadings, WithMapping, WithStyles
};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DistribusiToolExport implements
    FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;   // terima filter dari halaman
    }

    public function query()
    {
        $query = DaftarDistribusiTool::with(['tool', 'distribusiTool.user']);

        if (! empty($this->filters['kode_material'])) {
            $query->whereHas('tool', function ($q) {
                $q->where('kode_material', $this->filters['kode_material']);
            });
        }
        // ... filter lain (departemen, tanggal, dst.)

        return $query;
    }

    public function headings(): array
    {
        return ['Kode Material', 'Deskripsi', 'Jumlah', 'Nama Penerima', 'NIP', ...];
    }

    public function map($row): array
    {
        return [
            $row->tool->kode_material ?? '-',
            optional($row->distribusiTool->tanggal_pinjam_diserahkan)->format('d-m-Y H:i'),
            // ...
        ];
    }

    public function columnFormats(): array
    {
        return ['I' => NumberFormat::FORMAT_TEXT]; // NIP sebagai teks agar tidak berubah jadi angka
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]]; // header tebal
    }
}
```

**Controller:**

```php
public function exportExcel(Request $request)
{
    $filters = $request->only(['type', 'kode_material', 'departemen_id', 'start_date', 'end_date']);
    return Excel::download(new DistribusiToolExport($filters), 'distribusi.xlsx');
}
```

> Gunakan `FromQuery` (bukan `FromCollection`) agar file besar tidak dimuat seluruhnya ke memory. `WithMapping` mengatur isi tiap sel; `WithColumnFormatting` mencegah NIP/nomor panjang berubah jadi scientific notation.

---

## 11. PDF & Barcode

### PDF (dompdf) — pola asli `CreateSpmFileService`

```bash
composer require barryvdh/laravel-dompdf
```

```php
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

public function createSpmFile(Spm $spm)
{
    // render view blade -> HTML -> PDF, lalu SIMPAN ke storage
    $html = view('pages.spm.export-pdf', ['spm' => $spm])->render();

    $pdf = Pdf::loadHTML($html)
        ->setPaper([0, 0, 792, 684]);          // ukuran kertas custom (poin)

    $fileName = str_replace('/', '-', $spm->no_spm) . '.pdf';
    $directory = storage_path('app/public/spm_files');
    if (! file_exists($directory)) {
        Storage::disk('public')->makeDirectory('spm_files');
    }
    $pdf->save($directory . '/' . $fileName);   // simpan, bukan download
    return 'spm_files/' . $fileName;
}
```

> File PDF disimpan ke `storage/app/public/spm_files/` lalu diunduh lewat route (gaya CT: `SpmController@download`). Jangan lupa `php artisan storage:link` agar file bisa diakses via URL `public/...`.

### Barcode (picqer) — pola `BarcodeController`

```bash
composer require picqer/php-barcode-generator
```

```php
use Picqer\Barcode\BarcodeGeneratorPNG;

$generator = new BarcodeGeneratorPNG();
$barcode = base64_encode($generator->getBarcode($kodeMaterial, $generator::TYPE_CODE_128));
```

---

## 12. Struktur Direktori & Konvensi

Pola folder yang dipakai CT — disarankan ditiru untuk sistem baru:

```
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/          # CheckUserActive, HandleClientRequestId
│   ├── Requests/            # Form Request (validasi)
│   └── Service/             # logika bisnis berat (SpmReceiveService)
├── Livewire/                # komponen Livewire per modul
├── Models/
├── Observers/               # GlobalObserverActivityLogs
├── Imports/                 # import Excel
├── Exports/                 # ekspor Excel
└── Actions/                 # aksi Fortify (CreateNewUser, dll)
```

### Konvensi yang dipakai CT (layak ditiru)

| Konvensi | Contoh |
|---|---|
| **UUID `public_id`** untuk identitas publik + route key | `Tools`, `DistribusiTool`, `PurchaseRequisition` |
| **Auto-fill UUID di `booted()`** | `static::creating(fn($m) => $m->public_id = Str::uuid())` |
| **Route key custom** | `getRouteKeyName()` return `public_id` |
| **Primary key string** | `Project` (`project_id`), `Karyawan` (`nip`) |
| **SoftDeletes** untuk data master | `Tools`, `Posisi_CT`, `Departemen` |
| **Global scope** untuk query default | `Project::onlyWithStock` |
| **Aksesor format angka** | `getQtyFormattedAttribute()` |
| **Enum status sebagai string** | `Spm` (WAITING/COMPLETED/CANCELED) |
| **Resource route + endpoint `/data`** | `Route::resource('tool')` + `tool-get-data` |

### Contoh model dengan UUID

```php
class Tools extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'public_id'];

    protected static function booted()
    {
        static::creating(fn ($model) => $model->public_id = (string) Str::uuid());
    }

    public function getRouteKeyName()
    {
        return 'public_id';
    }
}
```

---

## 13. Mode Offline (Opsional)

Jika sistem baru berjalan di **LAN pabrik/kantor yang tidak stabil**, tiru pola offline CT. Komponennya:

| Komponen | File CT | Fungsi |
|---|---|---|
| Service worker | `public/sw.js` | Cache aset statis + snapshot DataTables; fallback `offline.html` |
| Antrian IndexedDB | `resources/js/offline/db.js` | Simpan request saat offline (`queueAdd`, `queueAll`, `queueDelete`) |
| Replay engine | `resources/js/offline/sync.js` | Intercept submit form saat offline, replay saat `online` dengan `X-Client-Request-Id` |
| Delta stok optimistik | `resources/js/offline/optimistic.js` | Update angka stok di tabel tanpa menunggu server |
| Idempotensi backend | `HandleClientRequestId` middleware + tabel `offline_requests` | Request duplikat tidak dieksekusi ulang |

### Alur

1. Saat offline, submit form POST ke route tertentu (`/pengurangan-tool`, `/update-stok-tool`, `/distribusi`) ditangkap `sync.js` → disimpan ke IndexedDB dengan `clientRequestId` (UUID).
2. Tabel DataTables langsung diperbarui secara optimistik.
3. Saat `online`, antrian direplay dengan header `X-Client-Request-Id` + CSRF.
4. Middleware cek `offline_requests`: jika ID sudah ada → kembalikan respons tersimpan; jika baru → proses & simpan.

> Ini fitur paling "mahal" untuk ditiru. Aktifkan **hanya jika benar-benar dibutuhkan**.

---

## 14. Konfigurasi .env

Template dari CT (`.env.example`):

```ini
APP_NAME=CentralTool
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Catatan konfigurasi khusus CT yang bisa disesuaikan:
- `config/app.php`: `'locale' => 'id'`, `'fallback_locale' => 'id'` (bahasa Indonesia default).
- `config/fortify.php`: username NIP, home `/dashboard`.
- `config/permission.php`: default spatie (teams off).
- PDF tersimpan di `storage/app/public/` → perlu `php artisan storage:link`.

---

## 15. Checklist Membangun Sistem Baru

### Setup dasar
- [ ] `composer create-project laravel/laravel` (atau kloning template)
- [ ] Install package backend (Bagian 2)
- [ ] Install package frontend (Bagian 3)
- [ ] Copy `vite.config.js`, `resources/js/app.js`, `resources/css/app.css` (Bagian 3 & 4)
- [ ] Konfigurasi `.env` (Bagian 14)
- [ ] `php artisan migrate` + `php artisan storage:link`

### Auth & otorisasi
- [ ] Install Fortify + config `username` + `Fortify::authenticateUsing` (Bagian 6)
- [ ] Kolom `is_active` di tabel `users`
- [ ] Install spatie + `HasRoles` di User + buat role (Bagian 7)
- [ ] Copy middleware `CheckUserActive` + daftarkan `auth.active` (Bagian 5.1)

### Audit
- [ ] Install activitylog + copy `GlobalObserverActivityLogs` + auto-register di `AppServiceProvider` (Bagian 8)

### Tabel data
- [ ] Install yajra DataTables + pola controller `data()` + Blade (Bagian 9)

### Fitur tambahan (jika perlu)
- [ ] Excel: maatwebsite (Bagian 10)
- [ ] PDF: dompdf + Barcode: picqer (Bagian 11)
- [ ] Offline: service worker + IndexedDB + middleware `client.request.id` (Bagian 13)

### Penutup
- [ ] `npm run build`
- [ ] Test login, role protection, dan 1 halaman DataTables berjalan
- [ ] Jalankan `./vendor/bin/pint` untuk code style