# UI Starter Kit "NewSystem" Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membangun project Laravel 10 baru di `D:\project\NewSystem` dengan kulit UI identik dengan sistem CT (CoreUI 5, branding INKA, layout + partials, login NIP, dashboard, halaman contoh DataTables + Livewire) — siap diisi modul bisnis domain baru.

**Architecture:** Project Laravel 10 mandiri. Tema statis CoreUI disalin dari `D:\project\CT\public\template\`. Layout, autentikasi (Fortify login NIP), otorisasi (spatie), audit log otomatis (observer global), dan pola DataTables disalin/digenerikkan dari CT. Semua modul bisnis CT (tool, SPM, distribusi, dll.) TIDAK disalin.

**Tech Stack:** PHP 8.1+, Laravel 10, Livewire 3, Fortify, spatie/laravel-permission, spatie/laravel-activitylog, yajra/laravel-datatables-oracle, maatwebsite/excel, CoreUI 5, Vite, jQuery, DataTables, select2, SweetAlert2, ApexCharts.

## Global Constraints

- Versi framework: `laravel/framework: ^10.10`, PHP `^8.1`.
- Login via **NIP** (numeric, 9 digit), bukan email. Kolom `nip` dan `is_active` wajib di tabel `users`.
- Branding dipertahankan: logo & favicon INKA tetap dipakai.
- `resources/js/app.js` TIDAK mengimpor `./offline/index`; CSS offline dihapus dari `app.css`; partial `offline-banner` tidak disalin.
- Semua blok `request()->routeIs('...')` yang mengacu route CT dihapus dari partials.
- Lokasi project target: `D:\project\NewSystem`. Sumber salinan: `D:\project\CT`.
- Semua langkah dijalankan dari bash (git bash) pada Windows.

---

### Task 1: Scaffold Project Laravel 10

**Files:**
- Create: `D:\project\NewSystem\` (seluruh skeleton Laravel)

**Interfaces:**
- Produces: project Laravel 10 kosong, siap diisi. Task berikutnya menambah package, aset, dan kode.

- [ ] **Step 1: Buat project Laravel 10**

Run:
```bash
composer create-project laravel/laravel:^10.0 NewSystem
```
Jalankan dari `D:\project`. Expected: folder `D:\project\NewSystem` terisi skeleton Laravel.

- [ ] **Step 2: Inisialisasi git dan konfigurasi .env**

```bash
cd D:/project/NewSystem
git init
php artisan key:generate
```

- [ ] **Step 3: Atur .env**

Edit `D:\project\NewSystem\.env`:
```ini
APP_NAME=NewSystem
APP_ENV=local
APP_URL=http://localhost
DB_DATABASE=new_system
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=sync
```

- [ ] **Step 4: Atur config locale Indonesia**

Edit `D:\project\NewSystem\config\app.php`:
```php
'locale' => 'id',
'fallback_locale' => 'id',
```

- [ ] **Step 5: Commit**

```bash
cd D:/project/NewSystem
git add -A
git commit -m "chore: scaffold Laravel 10 project"
```

---

### Task 2: Install & Konfigurasi Package Backend

**Files:**
- Modify: `D:\project\NewSystem\composer.json` (oleh composer)
- Modify: `D:\project\NewSystem\app\Http\Kernel.php`
- Create: `D:\project\NewSystem\database\migrations\*_create_permission_tables.php`, `*_create_activity_log_table.php` (oleh vendor:publish)

**Interfaces:**
- Produces: package terpasang; middleware alias `auth.active`, `role`, `permission`, `role_or_permission` terdaftar di Kernel.

- [ ] **Step 1: Install package**

```bash
cd D:/project/NewSystem
composer require laravel/fortify
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require yajra/laravel-datatables-oracle:10.6
composer require maatwebsite/excel
composer require livewire/livewire
```
Expected: semua package terpasang tanpa error.

- [ ] **Step 2: Publish konfigurasi & migrasi**

```bash
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --tag=livewire-config
```
Expected: file `config/fortify.php`, `config/permission.php`, `config/activitylog.php`, `config/excel.php`, `config/livewire.php` + migrasi permission & activity log terbuat.

- [ ] **Step 3: Daftarkan middleware alias di Kernel**

Edit `D:\project\NewSystem\app\Http\Kernel.php` — tambahkan `auth.active`, `role`, `permission`, `role_or_permission` pada array `$middlewareAliases`:
```php
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.active' => \App\Http\Middleware\CheckUserActive::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    'signed' => \App\Http\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
];
```

- [ ] **Step 4: Migrasi**

```bash
php artisan migrate
```
Expected: semua tabel migrasi dibuat (users, permission_tables, activity_log, dll).

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "chore: install fortify, spatie, yajra, maatwebsite, livewire packages"
```

---

### Task 3: Salin Aset UI dari CT

**Files:**
- Create (copy): `D:\project\NewSystem\public\template\` (dari `D:\project\CT\public\template\`)
- Modify: `D:\project\NewSystem\package.json`
- Modify: `D:\project\NewSystem\vite.config.js`
- Create (copy): `D:\project\NewSystem\resources\css\app.css`
- Create (copy): `D:\project\NewSystem\resources\js\app.js`
- Create (copy): `D:\project\NewSystem\resources\js\bootstrap.js`

**Interfaces:**
- Produces: tema CoreUI + library frontend siap di-build; `app.js` mengekspos `window.$`, `window.Swal`, `window.ApexCharts`, DataTables bs5, select2.

- [ ] **Step 1: Salin tema statis**

```bash
cp -r D:/project/CT/public/template D:/project/NewSystem/public/template
```
Expected: folder `public/template` (±24MB) tersalin.

- [ ] **Step 2: Salin file build frontend**

```bash
cp D:/project/CT/vite.config.js D:/project/NewSystem/vite.config.js
cp D:/project/CT/resources/js/bootstrap.js D:/project/NewSystem/resources/js/bootstrap.js
cp D:/project/CT/resources/css/app.css D:/project/NewSystem/resources/css/app.css
cp D:/project/CT/resources/js/app.js D:/project/NewSystem/resources/js/app.js
```

- [ ] **Step 3: Strip kode offline dari file tersalin**

Edit `D:\project\NewSystem\resources\js\app.js` — HAPUS baris terakhir `import "./offline/index";`. Isi akhir:
```js
import "./bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

// DataTables
import "datatables.net-bs5";
import "datatables.net-responsive-bs5";

// Select2
import select2 from "select2/dist/js/select2.full";
select2(window, $);
// SweetAlert2
import Swal from "sweetalert2";
window.Swal = Swal;

// ApexCharts
import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;
```

Edit `D:\project\NewSystem\resources\css\app.css` — HAPUS seluruh bagian mulai dari `/* Offline PWA-Lite banner */` sampai akhir file. Isi akhir:
```css
@import 'select2/dist/css/select2.min.css';
@import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
@import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';

/* Custom Sidebar Primary Theme */
.sidebar-primary {
  --cui-body-color: #ffffff;
  --cui-body-bg: #0d6efd; /* Bootstrap primary color */
  --cui-emphasis-color: #fff;
  --cui-secondary-color: rgba(255, 255, 255, 0.8);
  --cui-secondary-bg: #0b5ed7;
  --cui-tertiary-color: rgba(255, 255, 255, 0.6);
  --cui-tertiary-bg: #0a58ca;
  --cui-border-color: #0b5ed7;
}

.sidebar-primary .sidebar-toggler {
  filter: var(--cui-sidebar-toggler-white-filter);
}

/* Optional: Custom hover effects for primary sidebar */
.sidebar-primary .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.sidebar-primary .nav-link.active {
  background-color: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}
```

- [ ] **Step 4: Atur package.json dependency**

Edit `D:\project\NewSystem\package.json` — ganti isi dengan:
```json
{
    "name": "laravel-app",
    "version": "1.0.0",
    "private": true,
    "type": "module",
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

- [ ] **Step 5: Install npm**

```bash
npm install
```
Expected: `node_modules` terisi tanpa error.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: copy CoreUI theme and frontend build assets from CT"
```

---

### Task 4: Autentikasi Fortify (Login NIP)

**Files:**
- Modify: `D:\project\NewSystem\config\fortify.php`
- Create: `D:\project\NewSystem\app\Providers\FortifyServiceProvider.php`
- Modify: `D:\project\NewSystem\app\Models\User.php`
- Create: `D:\project\NewSystem\database\migrations\*_add_nip_to_users_table.php`
- Create: `D:\project\NewSystem\database\migrations\*_add_is_active_to_users_table.php`
- Create: `D:\project\NewSystem\app\Http\Middleware\CheckUserActive.php`
- Modify: `D:\project\NewSystem\config\app.php` (providers)
- Create: `D:\project\NewSystem\resources\views\auth\login.blade.php`

**Interfaces:**
- Produces: login via NIP 9 digit + password; user nonaktif (`is_active=false`) ditolak login & dipaksa logout; alias middleware `auth.active`.

- [ ] **Step 1: Konfigurasi config/fortify.php**

Edit `D:\project\NewSystem\config\fortify.php`:
```php
'username' => 'nip',
'home' => '/dashboard',
```
Ganti blok `'features'` menjadi:
```php
'features' => [
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```
(jika `Features::registration()` masih ada, HAPUS — karena registrasi via email tidak kompatibel dengan login NIP.)

- [ ] **Step 2: Daftarkan FortifyServiceProvider**

Edit `D:\project\NewSystem\config\app.php` array `'providers'`, tambahkan:
```php
App\Providers\FortifyServiceProvider::class,
```

- [ ] **Step 3: Buat FortifyServiceProvider**

Create `D:\project\NewSystem\app\Providers\FortifyServiceProvider.php`:
```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate(
                [
                    'nip' => 'required|numeric|digits:9',
                    'password' => 'required',
                ],
                [
                    'nip.required'   => 'NIP wajib diisi.',
                    'nip.numeric'    => 'NIP harus berupa angka.',
                    'nip.digits'     => 'NIP harus terdiri dari 9 digit.',
                    'password.required' => 'Password wajib diisi.',
                ]
            );

            $user = \App\Models\User::where('nip', $request->nip)->first();

            if ($user && $user->is_active && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
```

- [ ] **Step 4: Perbarui model User**

Edit `D:\project\NewSystem\app\Models\User.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];
}
```

- [ ] **Step 5: Buat migrasi kolom nip & is_active**

Create `D:\project\NewSystem\database\migrations\2026_08_19_000000_add_nip_to_users_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 20)->unique()->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nip');
        });
    }
};
```

Create `D:\project\NewSystem\database\migrations\2026_08_19_000001_add_is_active_to_users_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
```

- [ ] **Step 6: Buat middleware CheckUserActive**

Create `D:\project\NewSystem\app\Http\Middleware\CheckUserActive.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

- [ ] **Step 7: Buat halaman login**

Create `D:\project\NewSystem\resources\views\auth\login.blade.php`:
```blade
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Login - NewSystem</title>
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" />
    <link rel="manifest" href="{{ asset('template/assets/favicon/manifest.json') }}" />
    <meta name="theme-color" content="#ffffff" />
    <!-- Vendors styles-->
    <link rel="stylesheet" href="{{ asset('template/vendors/simplebar/css/simplebar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/css/vendors/simplebar.css') }}" />
    <!-- Main styles for this application-->
    <link href="{{ asset('template/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('template/css/examples.css') }}" rel="stylesheet" />
    <script src="{{ asset('template/js/config.js') }}"></script>
    <script src="{{ asset('template/js/color-modes.js') }}"></script>
</head>

<body>
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-group d-block d-md-flex row">
                        <div class="card col-md-7 p-4 mb-0">
                            <div class="card-body">
                                <h4 class="mb-3">NewSystem</h4>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use
                                                    xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-user') }}">
                                                </use>
                                            </svg></span>
                                        <input class="form-control @error('nip') is-invalid @enderror" type="text"
                                            name="nip" placeholder="NIP" value="{{ old('nip') }}" required autofocus />
                                        @error('nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="input-group mb-4">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use
                                                    xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-lock-locked') }}">
                                                </use>
                                            </svg></span>
                                        <input class="form-control @error('password') is-invalid @enderror"
                                            type="password" name="password" placeholder="Password" required />
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-primary px-4" type="submit">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card col-md-5 text-white bg-white py-5">
                            <div class="card-body text-center">
                                <div>
                                    <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}"
                                        alt="Dashboard" style="width: 80%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('template/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('template/vendors/simplebar/js/simplebar.min.js') }}"></script>
</body>

</html>
```

- [ ] **Step 8: Migrasi**

```bash
php artisan migrate
```
Expected: kolom `nip` dan `is_active` ada di tabel `users`.

- [ ] **Step 9: Commit**

```bash
git add -A
git commit -m "feat: fortify auth with NIP login and is_active middleware"
```

---

### Task 5: Roles & Seeder

**Files:**
- Create: `D:\project\NewSystem\database\seeders\RolesAndUsersSeeder.php`
- Modify: `D:\project\NewSystem\database\seeders\DatabaseSeeder.php`

**Interfaces:**
- Produces: role `admin`, `staff`, `viewer`; user demo admin (NIP `123456789`, password `password`, role `admin`).

- [ ] **Step 1: Buat seeder**

Create `D:\project\NewSystem\database\seeders\RolesAndUsersSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'staff', 'viewer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $admin = User::firstOrCreate(
            ['nip' => '123456789'],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@example.com',
                'password' => 'password',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
```

- [ ] **Step 2: Panggil dari DatabaseSeeder**

Edit `D:\project\NewSystem\database\seeders\DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call([
        RolesAndUsersSeeder::class,
    ]);
}
```

- [ ] **Step 3: Jalankan seeder**

```bash
php artisan db:seed
```
Expected: role terbuat, user `Admin Utama` (NIP `123456789`, password `password`) dengan role `admin`.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: seed roles and demo admin user"
```

---

### Task 6: Audit Log Global (Observer)

**Files:**
- Create: `D:\project\NewSystem\app\Observers\GlobalObserverActivityLogs.php`
- Modify: `D:\project\NewSystem\app\Providers\AppServiceProvider.php`

**Interfaces:**
- Produces: semua model di `app/Models` otomatis tercatat created/updated/deleted ke tabel `activity_log`.

- [ ] **Step 1: Buat observer**

Create `D:\project\NewSystem\app\Observers\GlobalObserverActivityLogs.php`:
```php
<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class GlobalObserverActivityLogs
{
    protected function getFeature($model)
    {
        return property_exists($model, 'featureName')
            ? $model->featureName
            : class_basename($model);
    }

    protected function getCauser()
    {
        return Auth::check() ? Auth::user() : null;
    }

    protected function buildProperties($model, array $extra = [])
    {
        return array_merge([
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
            'user_agent' => Request::header('User-Agent'),
        ], $extra);
    }

    protected function log($model, string $event, array $properties = [])
    {
        $causer = $this->getCauser();
        $feature = $this->getFeature($model);
        $modelName = class_basename($model);

        $desc = "{$modelName} {$event}";
        if ($causer) {
            $desc .= " by {$causer->name}";
        }

        activity()
            ->useLog($feature)
            ->causedBy($causer)
            ->performedOn($model)
            ->tap(function ($activity) use ($feature) {
                $activity->feature = $feature;
            })
            ->withProperties($this->buildProperties($model, $properties))
            ->event($event)
            ->log($desc);
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
}
```

- [ ] **Step 2: Auto-register observer di AppServiceProvider**

Edit `D:\project\NewSystem\app\Providers\AppServiceProvider.php`:
```php
<?php

namespace App\Providers;

use App\Observers\GlobalObserverActivityLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');

        $modelFiles = File::allFiles(app_path('Models'));

        foreach ($modelFiles as $file) {
            $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();

            if (
                class_exists($class) &&
                is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class) &&
                ! (new \ReflectionClass($class))->isAbstract()
            ) {
                $class::observe(GlobalObserverActivityLogs::class);
            }
        }
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "feat: global activity log observer for all models"
```

---

### Task 7: Layout & Partials Generik

**Files:**
- Create: `D:\project\NewSystem\resources\views\layouts\app.blade.php`
- Create: `D:\project\NewSystem\resources\views\partials\head.blade.php`
- Create: `D:\project\NewSystem\resources\views\partials\sidebar.blade.php`
- Create: `D:\project\NewSystem\resources\views\partials\header.blade.php`
- Create: `D:\project\NewSystem\resources\views\partials\footer.blade.php`
- Create: `D:\project\NewSystem\resources\views\partials\scripts.blade.php`
- Create: `D:\project\NewSystem\resources\views\partials\breadcrumb.blade.php`

**Interfaces:**
- Produces: shell UI generik. Sidebar berisi: Dashboard, grup Data Master (Data Master, Form Master), grup Pengaturan (Pengguna, Activity Log — admin only). Partial `header` menampilkan nama user + role + logout.

- [ ] **Step 1: Buat layout**

Create `D:\project\NewSystem\resources\views\layouts\app.blade.php`:
```blade
<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
    @livewireStyles
</head>

<body>
    @include('partials.sidebar')
    <div class="wrapper d-flex flex-column min-vh-100">
        @include('partials.header')
        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                @yield('content')
            </div>
        </div>
        @include('partials.footer')
    </div>
    @include('partials.scripts')
    @livewireScripts
</body>

</html>
```

- [ ] **Step 2: Buat partial head**

Create `D:\project\NewSystem\resources\views\partials\head.blade.php`:
```blade
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
<title>@yield('title', 'NewSystem')</title>
<link rel="icon" sizes="57x57"
    href="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" />
<link rel="manifest" href="{{ asset('template/assets/favicon/manifest.json') }}" />
<meta name="theme-color" content="#ffffff" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<!-- Vendors styles-->
<link rel="stylesheet" href="{{ asset('template/vendors/simplebar/css/simplebar.css') }}" />
<link rel="stylesheet" href="{{ asset('template/css/vendors/simplebar.css') }}" />
<!-- Main styles for this application-->
<link href="{{ asset('template/css/style.css') }}" rel="stylesheet" />
<!-- Custom styles -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('css')
```

- [ ] **Step 3: Buat partial sidebar**

Create `D:\project\NewSystem\resources\views\partials\sidebar.blade.php`:
```blade
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header bg-white">
        <a class="sidebar-brand" href="{{ url('/dashboard') }}">
            <img src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" alt="Dashboard"
                style="width: 80%;">
        </a>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-title">NewSystem</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-speedometer') }}">
                    </use>
                </svg>
                Dashboard
            </a>
        </li>
        <li
            class="nav-group {{ request()->routeIs(['data-master.index', 'data-master.create-form']) ? 'show active' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-applications') }}">
                    </use>
                </svg>
                Data Master
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['data-master.index']) ? 'active' : '' }}"
                        href="{{ route('data-master.index') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Data Master
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(['data-master.create-form']) ? 'active' : '' }}"
                        href="{{ route('data-master.create-form') }}">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Form Master
                    </a>
                </li>
            </ul>
        </li>
        @if (Auth::user()->hasRole('admin'))
            <li
                class="nav-group {{ request()->routeIs(['pengguna.index', 'activity-log.index']) ? 'show active' : '' }}">
                <a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-settings') }}">
                        </use>
                    </svg>
                    Pengaturan
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['pengguna.index']) ? 'active' : '' }}"
                            href="{{ route('pengguna.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(['activity-log.index']) ? 'active' : '' }}"
                            href="{{ route('activity-log.index') }}">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span>Activity Log
                        </a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>
```

- [ ] **Step 4: Buat partial header**

Create `D:\project\NewSystem\resources\views\partials\header.blade.php`:
```blade
@auth
    <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
            <button class="header-toggler" type="button"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
                style="margin-inline-start: -14px">
                <svg class="icon icon-lg">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                </svg>
            </button>
            <ul class="header-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-md">
                            <img class="avatar-img"
                                src="{{ asset('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}" />
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pt-0">
                        <div class="dropdown-header bg-body-tertiary text-body-secondary py-3 text-center">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ Auth::user()->getRoleNames()->first() }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <svg class="icon me-2">
                                    <use
                                        xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}">
                                    </use>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </header>
@endauth
```

- [ ] **Step 5: Buat partial footer, scripts, breadcrumb**

Create `D:\project\NewSystem\resources\views\partials\footer.blade.php`:
```blade
<footer class="footer px-4">

</footer>
```

Create `D:\project\NewSystem\resources\views\partials\scripts.blade.php`:
```blade
<!-- CoreUI and necessary plugins-->
<script src="{{ asset('template/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendors/simplebar/js/simplebar.min.js') }}"></script>

<script type="module">
    const header = document.querySelector('header.header');

    document.addEventListener('scroll', () => {
        if (header) {
            header.classList.toggle(
                'shadow-sm',
                document.documentElement.scrollTop > 0
            );
        }
    });
</script>
<!-- Plugins and scripts required by this view-->
<script src="{{ asset('template/vendors/chart.js/js/chart.umd.js') }}"></script>
<script src="{{ asset('template/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>
<script src="{{ asset('template/vendors/@coreui/utils/js/index.js') }}"></script>
<script src="{{ asset('template/js/main.js') }}"></script>
@stack('js')
```

Create `D:\project\NewSystem\resources\views\partials\breadcrumb.blade.php`:
```blade
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light rounded p-3">
        @foreach($breadcrumbs as $breadcrumb)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
            @else
                <li class="breadcrumb-item">
                    @if(isset($breadcrumb['url']))
                        <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">{{ $breadcrumb['title'] }}</a>
                    @else
                        {{ $breadcrumb['title'] }}
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
```

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: generic app layout and partials"
```

---

### Task 8: Routes & Dashboard

**Files:**
- Modify: `D:\project\NewSystem\routes\web.php`
- Create: `D:\project\NewSystem\app\Http\Controllers\DashboardController.php`
- Create: `D:\project\NewSystem\resources\views\pages\dashboard.blade.php`

**Interfaces:**
- Produces: route root redirect ke login; `/dashboard` (auth + auth.active) menampilkan halaman dashboard dengan 4 kartu statistik + 1 chart ApexCharts.

- [ ] **Step 1: Tulis routes/web.php**

Edit `D:\project\NewSystem\routes\web.php` — ganti seluruh isi:
```php
<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'auth.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Master (contoh CRUD DataTables server-side)
    Route::prefix('data-master')->name('data-master.')->controller(DataMasterController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/import', 'import')->name('import')->middleware('role:admin');
        Route::delete('/{dataMaster}', 'destroy')->name('destroy');
    });

    // Form Master (contoh form Livewire)
    Route::get('/form-master/create', \App\Livewire\DataMaster\CreateDataMaster::class)->name('data-master.create-form');
    Route::get('/form-master/{dataMasterId}/edit', \App\Livewire\DataMaster\CreateDataMaster::class)->name('data-master.edit-form');

    // Pengaturan (khusus admin)
    Route::middleware('role:admin')->group(function () {
        Route::prefix('pengguna')->name('pengguna.')->controller(PenggunaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'data')->name('data');
            Route::post('/', 'store')->name('store');
            Route::put('/{user}', 'update')->name('update');
            Route::post('/{user}/toggle', 'toggleActive')->name('toggle');
            Route::delete('/{user}', 'destroy')->name('destroy');
        });

        Route::prefix('activity-log')->name('activity-log.')->controller(ActivityLogController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'data')->name('data');
        });
    });
});
```

- [ ] **Step 2: Buat DashboardController**

Create `D:\project\NewSystem\app\Http\Controllers\DashboardController.php`:
```php
<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }
}
```

- [ ] **Step 3: Buat halaman dashboard**

Create `D:\project\NewSystem\resources\views\pages\dashboard.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
        <small class="text-muted">{{ date('d F Y') }}</small>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-semibold text-uppercase mb-1">Total Data</div>
                            <div class="fs-3 fw-semibold" id="stat-total">0</div>
                        </div>
                        <svg class="icon icon-2xl text-primary">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-storage') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-semibold text-uppercase mb-1">Data Aktif</div>
                            <div class="fs-3 fw-semibold" id="stat-aktif">0</div>
                        </div>
                        <svg class="icon icon-2xl text-success">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-check-circle') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-semibold text-uppercase mb-1">Pengguna</div>
                            <div class="fs-3 fw-semibold" id="stat-user">0</div>
                        </div>
                        <svg class="icon icon-2xl text-info">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-people') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-semibold text-uppercase mb-1">User Aktif</div>
                            <div class="fs-3 fw-semibold" id="stat-user-aktif">0</div>
                        </div>
                        <svg class="icon icon-2xl text-warning">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-user') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Statistik Bulanan</h5>
            <small class="text-body-secondary">Contoh chart ApexCharts — ganti data dengan data bisnis Anda</small>
        </div>
        <div class="card-body">
            <div id="chart-bulanan"></div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            var chart = new ApexCharts(document.querySelector('#chart-bulanan'), {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Contoh Data',
                    data: [30, 40, 35, 50, 49, 60, 70, 91, 125, 110, 80, 95]
                }],
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
                },
                colors: ['#0d6efd']
            });
            chart.render();
        });
    </script>
@endpush
```

- [ ] **Step 4: Test manual**

```bash
php artisan serve
```
Buka `http://localhost:8000`, login dengan NIP `123456789` / `password`. Expected: redirect ke `/dashboard`, sidebar + header + 4 kartu + chart tampil.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: routes and dashboard page with ApexCharts"
```

---

### Task 9: Halaman Data Master (DataTables Server-Side)

**Files:**
- Create: `D:\project\NewSystem\database\migrations\2026_08_19_000002_create_data_masters_table.php`
- Create: `D:\project\NewSystem\app\Models\DataMaster.php`
- Create: `D:\project\NewSystem\app\Http\Controllers\DataMasterController.php`
- Create: `D:\project\NewSystem\app\Imports\DataMasterImport.php`
- Create: `D:\project\NewSystem\resources\views\pages\data-master\index.blade.php`

**Interfaces:**
- Produces: halaman `/data-master` (auth) dengan DataTables server-side (endpoint `/data-master/data`), filter kode/nama, hapus via SweetAlert2 (AJAX DELETE), modal import Excel (admin). Model `DataMaster` dengan `kode` unique, `nama`, `deskripsi`, SoftDeletes.

- [ ] **Step 1: Buat migrasi**

Create `D:\project\NewSystem\database\migrations\2026_08_19_000002_create_data_masters_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_masters', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_masters');
    }
};
```

- [ ] **Step 2: Buat model**

Create `D:\project\NewSystem\app\Models\DataMaster.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = ['kode', 'nama', 'deskripsi'];
}
```

- [ ] **Step 3: Buat controller**

Create `D:\project\NewSystem\app\Http\Controllers\DataMasterController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Imports\DataMasterImport;
use App\Models\DataMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DataMasterController extends Controller
{
    public function index()
    {
        return view('pages.data-master.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = DataMaster::query()->select(['id', 'kode', 'nama', 'deskripsi']);

        if ($request->filled('kode')) {
            $query->where('kode', 'LIKE', '%' . $request->kode . '%');
        }
        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $editUrl = route('data-master.edit-form', $row->id);
                return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a> ' .
                    '<button class="btn btn-sm btn-danger deleteButton" data-id="' . $row->id . '">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy($dataMaster)
    {
        DataMaster::findOrFail($dataMaster)->delete();
        return response()->json(['success' => true]);
    }

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
            Excel::import(new DataMasterImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
```

- [ ] **Step 4: Buat Import class**

Create `D:\project\NewSystem\app\Imports\DataMasterImport.php`:
```php
<?php

namespace App\Imports;

use App\Models\DataMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DataMasterImport implements
    ToCollection, WithBatchInserts, WithChunkReading, WithStartRow, SkipsEmptyRows, WithHeadingRow
{
    public function collection($rows)
    {
        $existing = DataMaster::onlyTrashed()->withTrashed()->pluck('kode')->flip();

        $insertData = [];
        foreach ($rows as $row) {
            $kode = trim((string) $row['kode']);
            if ($kode === '' || isset($existing[$kode])) {
                continue;
            }
            $insertData[] = [
                'kode' => $kode,
                'nama' => trim((string) $row['nama']),
                'deskripsi' => trim((string) ($row['deskripsi'] ?? '')),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $existing[$kode] = true;
        }

        if (! empty($insertData)) {
            DB::table('data_masters')->insert($insertData);
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
```

- [ ] **Step 5: Buat halaman index**

Create `D:\project\NewSystem\resources\views\pages\data-master\index.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Data Master')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Data Master</h5>
            <small class="text-body-secondary">Contoh halaman DataTables server-side</small>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="d-flex justify-content-end mb-3 gap-2">
                <a href="{{ route('data-master.create-form') }}" class="btn btn-success">
                    Tambah Data
                </a>
                @if (Auth::user()->hasRole('admin'))
                    <button type="button" class="btn btn-primary" data-coreui-toggle="modal"
                        data-coreui-target="#importModal">
                        Import Excel
                    </button>
                @endif
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <input type="text" class="form-control" id="kode_filter" placeholder="Filter Kode">
                </div>
                <div class="col-6">
                    <input type="text" class="form-control" id="nama_filter" placeholder="Filter Nama">
                </div>
            </div>
            <div class="mt-3 mb-3">
                <button id="filter-button" class="btn btn-primary">
                    <svg class="icon me-1">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-filter') }}"></use>
                    </svg>
                    Filter
                </button>
                <button id="reset-filter-button" class="btn btn-secondary">Reset</button>
            </div>
            <div class="table-responsive">
                <table id="table-data-master" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Master</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('data-master.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih file Excel</label>
                            <input class="form-control" type="file" id="file" name="file" required>
                            <small class="text-body-secondary">Kolom: kode, nama, deskripsi (baris pertama header).</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            var table = $('#table-data-master').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('data-master.data') }}",
                    data: function(d) {
                        d.kode = $('#kode_filter').val();
                        d.nama = $('#nama_filter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'kode', name: 'kode' },
                    { data: 'nama', name: 'nama' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
            });

            $('#filter-button').click(function() {
                table.ajax.reload();
            });
            $('#reset-filter-button').click(function() {
                $('#kode_filter').val('');
                $('#nama_filter').val('');
                table.ajax.reload();
            });

            $(document).on('click', '.deleteButton', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/data-master/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
```

- [ ] **Step 6: Migrasi & test manual**

```bash
php artisan migrate
```
Buka `/data-master` (login NIP `123456789`). Expected: tabel kosong dengan tombol filter; klik "Tambah Data" → halaman form.

---

### Task 10: Form Master (Livewire)

**Files:**
- Create: `D:\project\NewSystem\app\Livewire\DataMaster\CreateDataMaster.php`
- Create: `D:\project\NewSystem\resources\views\livewire\data-master\create-data-master.blade.php`

**Interfaces:**
- Produces: komponen Livewire untuk create & edit Data Master. Route `data-master.create-form` (create) dan `data-master.edit-form` (edit). Setelah simpan → redirect ke `data-master.index` + flash success.

- [ ] **Step 1: Buat komponen Livewire**

Create `D:\project\NewSystem\app\Livewire\DataMaster\CreateDataMaster.php`:
```php
<?php

namespace App\Livewire\DataMaster;

use App\Models\DataMaster;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateDataMaster extends Component
{
    public $dataMasterId;
    public $kode = '';
    public $nama = '';
    public $deskripsi = '';

    public function mount($dataMasterId = null)
    {
        if ($dataMasterId) {
            $this->dataMasterId = $dataMasterId;
            $dm = DataMaster::findOrFail($dataMasterId);
            $this->kode = $dm->kode;
            $this->nama = $dm->nama;
            $this->deskripsi = $dm->deskripsi;
        }
    }

    public function rules()
    {
        return [
            'kode' => ['required', 'string', 'max:50', Rule::unique('data_masters', 'kode')->ignore($this->dataMasterId)],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->dataMasterId) {
            $dm = DataMaster::findOrFail($this->dataMasterId);
            $dm->update([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'deskripsi' => $this->deskripsi,
            ]);
            session()->flash('success', 'Data berhasil diperbarui.');
        } else {
            DataMaster::create([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'deskripsi' => $this->deskripsi,
            ]);
            session()->flash('success', 'Data berhasil ditambahkan.');
        }

        return redirect()->route('data-master.index');
    }

    public function render()
    {
        return view('livewire.data-master.create-data-master');
    }
}
```

- [ ] **Step 2: Buat blade komponen**

Create `D:\project\NewSystem\resources\views\livewire\data-master\create-data-master.blade.php`:
```blade
@extends('layouts.app')

@section('title', $dataMasterId ? 'Edit Data Master' : 'Tambah Data Master')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $dataMasterId ? 'Edit Data Master' : 'Tambah Data Master' }}</h5>
            <small class="text-body-secondary">Contoh form Livewire dengan validasi</small>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                        wire:model="kode" placeholder="Masukkan kode">
                    @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                        wire:model="nama" placeholder="Masukkan nama">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                        wire:model="deskripsi" rows="3" placeholder="Masukkan deskripsi (opsional)"></textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('data-master.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
```

- [ ] **Step 3: Test manual**

Buka `/form-master/create` (login). Expected: form tampil; isi dan simpan → redirect ke `/data-master` dengan flash success. Buka edit dari tombol "Edit" di tabel → form terisi.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: Livewire form for Data Master create/edit"
```

---

### Task 11: Pengguna & Activity Log (Admin)

**Files:**
- Create: `D:\project\NewSystem\app\Http\Controllers\PenggunaController.php`
- Create: `D:\project\NewSystem\app\Http\Controllers\ActivityLogController.php`
- Create: `D:\project\NewSystem\resources\views\pages\pengguna\index.blade.php`
- Create: `D:\project\NewSystem\resources\views\pages\activity-log\index.blade.php`
- Create: `D:\project\NewSystem\database\migrations\2026_08_19_000003_add_activity_feature_column.php`

**Interfaces:**
- Produces: manajemen pengguna (DataTables + modal form + toggle aktif + hapus + assign role) dan halaman Activity Log (DataTables dari tabel `activity_log`), keduanya admin-only.

- [ ] **Step 1: Buat migrasi kolom feature**

Create `D:\project\NewSystem\database\migrations\2026_08_19_000003_add_activity_feature_column.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('feature')->nullable()->after('log_name');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('feature');
        });
    }
};
```

- [ ] **Step 2: Buat PenggunaController**

Create `D:\project\NewSystem\app\Http\Controllers\PenggunaController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PenggunaController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('pages.pengguna.index', compact('roles'));
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = User::query()->with('roles');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('role', fn ($user) => $user->roles->pluck('name')->implode(', '))
            ->addColumn('action', function ($user) {
                $btn = '<button class="btn btn-sm btn-primary editButton" data-id="' . $user->id
                    . '" data-name="' . e($user->name) . '" data-email="' . e($user->email)
                    . '" data-nip="' . e($user->nip) . '" data-role="' . e($user->roles->first()?->name ?? '')
                    . '" data-is-active="' . ($user->is_active ? '1' : '0') . '">Edit</button> ';
                $btn .= '<button class="btn btn-sm btn-warning toggleButton" data-id="' . $user->id . '">'
                    . ($user->is_active ? 'Nonaktifkan' : 'Aktifkan') . '</button> ';
                $btn .= '<button class="btn btn-sm btn-danger deleteButton" data-id="' . $user->id . '">Hapus</button>';
                return $btn;
            })
            ->addColumn('status', function ($user) {
                return $user->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Nonaktif</span>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'numeric', 'digits:9', 'unique:users,nip'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
            'is_active' => $request->boolean('is_active'),
        ]);
        $user->syncRoles([$request->role]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => ['required', 'numeric', 'digits:9', Rule::unique('users', 'nip')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'is_active' => $request->boolean('is_active'),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        $user->syncRoles([$request->role]);

        return response()->json(['success' => true]);
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => ! $user->is_active]);
        return response()->json(['success' => true]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true]);
    }
}
```

- [ ] **Step 3: Buat ActivityLogController**

Create `D:\project\NewSystem\app\Http\Controllers\ActivityLogController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('pages.activity-log.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = DB::table('activity_log')
            ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
            ->select([
                'activity_log.id',
                'activity_log.log_name',
                'activity_log.description',
                'activity_log.event',
                'activity_log.created_at',
                'users.name as causer_name',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
}
```

- [ ] **Step 4: Buat halaman Pengguna**

Create `D:\project\NewSystem\resources\views\pages\pengguna\index.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Pengguna</h5>
            <small class="text-body-secondary">Kelola akun pengguna sistem</small>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#userModal"
                    id="btn-tambah">
                    Tambah Pengguna
                </button>
            </div>
            <div class="table-responsive">
                <table id="table-pengguna" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm">
                    <input type="hidden" id="user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP (9 digit) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nip" maxlength="9" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password"
                                placeholder="{{ __('Minimal 8 karakter (kosongkan saat edit jika tidak diubah)') }}">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            var table = $('#table-pengguna').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('pengguna.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'name', name: 'name' },
                    { data: 'nip', name: 'nip' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                autoWidth: false,
                scrollX: true,
            });

            $('#btn-tambah').click(function() {
                $('#userForm')[0].reset();
                $('#user_id').val('');
                $('#userModalLabel').text('Tambah Pengguna');
                $('#password').prop('required', true);
                $('#is_active').prop('checked', true);
            });

            $(document).on('click', '.editButton', function() {
                const d = $(this).data();
                $('#userForm')[0].reset();
                $('#user_id').val(d.id);
                $('#name').val(d.name);
                $('#nip').val(d.nip);
                $('#email').val(d.email);
                $('#role').val(d.role);
                $('#password').prop('required', false);
                $('#userModalLabel').text('Edit Pengguna');
                $('#is_active').prop('checked', d.is_active === 1);
                $('#userModal').modal('show');
            });

            $('#userForm').submit(function(e) {
                e.preventDefault();
                const id = $('#user_id').val();
                const url = id ? `/pengguna/${id}` : '{{ route('pengguna.store') }}';
                const method = id ? 'PUT' : 'POST';
                const data = {
                    name: $('#name').val(),
                    nip: $('#nip').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    role: $('#role').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0,
                };
                $.ajax({
                    url,
                    type: method,
                    data,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        $('#userModal').modal('hide');
                        Swal.fire('Berhasil!', 'Data pengguna tersimpan.', 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.errors
                            ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                            : 'Terjadi kesalahan.';
                        Swal.fire('Gagal!', msg, 'error');
                    }
                });
            });

            $(document).on('click', '.toggleButton', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `/pengguna/${id}/toggle`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() { table.ajax.reload(); }
                });
            });

            $(document).on('click', '.deleteButton', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pengguna ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/pengguna/${id}`,
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function() {
                                Swal.fire('Terhapus!', 'Pengguna berhasil dihapus.', 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
```

- [ ] **Step 5: Buat halaman Activity Log**

Create `D:\project\NewSystem\resources\views\pages\activity-log\index.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Activity Log</h5>
            <small class="text-body-secondary">Log otomatis semua perubahan data</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-activity-log" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Log</th>
                            <th>Deskripsi</th>
                            <th>Event</th>
                            <th>User</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            $('#table-activity-log').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('activity-log.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'log_name', name: 'activity_log.log_name' },
                    { data: 'description', name: 'activity_log.description' },
                    { data: 'event', name: 'activity_log.event', className: 'text-center' },
                    { data: 'causer_name', name: 'users.name' },
                    { data: 'created_at', name: 'activity_log.created_at', className: 'text-center' },
                ],
                order: [[5, 'desc']],
                autoWidth: false,
                scrollX: true,
            });
        });
    </script>
@endpush
```

- [ ] **Step 6: Migrasi & test**

```bash
php artisan migrate
```
Test: login sebagai admin → buka `/pengguna` (tambah/edit/toggle/hapus) dan `/activity-log`. Expected: DataTables menampilkan data; setelah edit Data Master, muncul entry baru di Activity Log.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: admin pengguna management and activity log pages"
```

---

### Task 12: Factory, Tests, Build, Verifikasi Akhir

**Files:**
- Modify: `D:\project\NewSystem\database\factories\UserFactory.php`
- Modify: `D:\project\NewSystem\phpunit.xml`
- Create: `D:\project\NewSystem\tests\Feature\AuthenticationTest.php`
- Create: `D:\project\NewSystem\tests\Feature\DashboardAccessTest.php`
- Create: `D:\project\NewSystem\tests\Feature\RoleProtectionTest.php`
- Create: `D:\project\NewSystem\tests\Feature\DataMasterTest.php`

**Interfaces:**
- Produces: semua test hijau; aset frontend ter-build; code style rapi (pint).

- [ ] **Step 1: Perbarui UserFactory**

Edit `D:\project\NewSystem\database\factories\UserFactory.php` — ganti SELURUH isi file menjadi:
```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nip' => fake()->unique()->numerify('#########'),
            'email_verified_at' => now(),
            'password' => 'password',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```
Password di-hash otomatis oleh cast `password => 'hashed'` di model `User`; karenanya factory tidak perlu memanggil `Hash::make`.

- [ ] **Step 2: Pastikan phpunit.xml memakai sqlite**

Edit `D:\project\NewSystem\phpunit.xml` — pastikan blok `php` berisi:
```xml
<env name="APP_ENV" value="testing"/>
<env name="BCRYPT_ROUNDS" value="4"/>
<env name="CACHE_DRIVER" value="array"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="MAIL_MAILER" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
<env name="TELESCOPE_ENABLED" value="false"/>
```

- [ ] **Step 3: Buat test autentikasi**

Create `D:\project\NewSystem\tests\Feature\AuthenticationTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attributes = [], string $role = 'staff'): User
    {
        Role::firstOrCreate(['name' => $role]);
        $user = User::create(array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nip' => '123456789',
            'password' => 'password',
            'is_active' => true,
        ], $attributes));
        $user->assignRole($role);
        return $user;
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('NIP');
    }

    public function test_user_can_login_with_nip(): void
    {
        $this->makeUser();

        $this->post('/login', ['nip' => '123456789', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->makeUser();

        $this->post('/login', ['nip' => '123456789', 'password' => 'wrong-password'])
            ->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->makeUser(['is_active' => false]);

        $this->post('/login', ['nip' => '123456789', 'password' => 'password']);

        $this->assertGuest();
    }
}
```

- [ ] **Step 4: Buat test akses dashboard**

Create `D:\project\NewSystem\tests\Feature\DashboardAccessTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'staff']);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nip' => '123456789',
            'password' => 'password',
            'is_active' => true,
        ]);
        $user->assignRole('staff');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }
}
```

- [ ] **Step 5: Buat test proteksi role**

Create `D:\project\NewSystem\tests\Feature\RoleProtectionTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        Role::firstOrCreate(['name' => $role]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nip' => '123456789',
            'password' => 'password',
            'is_active' => true,
        ]);
        $user->assignRole($role);
        return $user;
    }

    public function test_admin_can_access_pengguna(): void
    {
        $this->actingAs($this->makeUser('admin'))->get('/pengguna')->assertOk();
    }

    public function test_staff_cannot_access_pengguna(): void
    {
        $this->actingAs($this->makeUser('staff'))->get('/pengguna')->assertForbidden();
    }
}
```

- [ ] **Step 6: Buat test Data Master**

Create `D:\project\NewSystem\tests\Feature\DataMasterTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Livewire\DataMaster\CreateDataMaster;
use App\Models\DataMaster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataMasterTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        Role::firstOrCreate(['name' => 'staff']);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nip' => '123456789',
            'password' => 'password',
            'is_active' => true,
        ]);
        $user->assignRole('staff');
        return $user;
    }

    public function test_data_endpoint_requires_auth(): void
    {
        $this->get('/data-master/data', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertRedirect('/login');
    }

    public function test_data_endpoint_returns_json(): void
    {
        $user = $this->actingUser();
        DataMaster::create(['kode' => 'DM-001', 'nama' => 'Data Satu', 'deskripsi' => 'deskripsi']);

        $this->actingAs($user)
            ->get('/data-master/data', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJson(['recordsTotal' => 1]);
    }

    public function test_data_endpoint_aborts_on_non_ajax(): void
    {
        $this->actingAs($this->actingUser())
            ->get('/data-master/data')
            ->assertNotFound();
    }

    public function test_livewire_component_creates_data(): void
    {
        $this->actingAs($this->actingUser());

        Livewire::test(CreateDataMaster::class)
            ->set('kode', 'DM-002')
            ->set('nama', 'Data Dua')
            ->set('deskripsi', 'opsional')
            ->call('save')
            ->assertRedirect(route('data-master.index'));

        $this->assertDatabaseHas('data_masters', ['kode' => 'DM-002']);
    }

    public function test_livewire_component_validates_unique_kode(): void
    {
        DataMaster::create(['kode' => 'DM-001', 'nama' => 'Data Satu']);
        $this->actingAs($this->actingUser());

        Livewire::test(CreateDataMaster::class)
            ->set('kode', 'DM-001')
            ->set('nama', 'Duplikat')
            ->call('save')
            ->assertHasErrors('kode');
    }
}
```

- [ ] **Step 7: Jalankan test**

```bash
php artisan test
```
Expected: semua test PASS. Jika ada yang gagal terkait role, pastikan seeder/role terdaftar di test (test membuat role sendiri via `Role::firstOrCreate`).

- [ ] **Step 8: Build frontend**

```bash
npm run build
```
Expected: `public/build` terisi, tidak ada error Vite.

- [ ] **Step 9: Pint code style**

```bash
./vendor/bin/pint
```
Expected: tidak ada error.

- [ ] **Step 10: Commit**

```bash
git add -A
git commit -m "test: authentication, role protection, data master tests; build assets"
```

---

## Catatan Verifikasi Manual (setelah semua task)

1. `php artisan serve` → `http://localhost:8000` → redirect ke `/login`.
2. Login NIP `123456789` / `password` → `/dashboard` (4 kartu + chart).
3. `/data-master` → tambah via "Tambah Data" (form Livewire), filter, hapus, import Excel (admin).
4. `/pengguna` & `/activity-log` hanya bisa diakses admin; user `staff` → 403.
5. Edit data → cek `/activity-log` muncul entri baru.
