# Autentikasi, peran, dan activity log

## Ringkasan

Aplikasi memakai Laravel Fortify untuk login, spatie/laravel-permission untuk peran, dan spatie/laravel-activitylog untuk jejak aktivitas. Login memakai **NIP 9 digit**, bukan email. Hanya ada dua peran yang dipakai aplikasi: `admin` dan `viewer`.

## Alur login

```
POST /login
  -> validasi nip: required|numeric|digits:9
  -> User::where('nip', $nip)->first()
  -> is_active && Hash::check(password)
  -> event Login -> LogAuthenticationActivity -> activity_log (log_name = Auth)
  -> redirect /material-planning
```

Implementasi ada di `app/Providers/FortifyServiceProvider.php:43-64` (closure `authenticateUsing`) dan `config/fortify.php:48` (`username => nip`).

| Aspek | Nilai / perilaku | Sumber |
| --- | --- | --- |
| Field login | `nip` + `password` | `resources/views/auth/login.blade.php:134-160` |
| Validasi NIP | `required|numeric|digits:9`, pesan bahasa Indonesia | `FortifyServiceProvider.php:44-55` |
| User tidak aktif | Ditolak sebagai kredensial salah (tidak dibedakan di response) | `FortifyServiceProvider.php:57-63` |
| Rate limit login | 5 request/menit per kombinasi NIP + IP | `FortifyServiceProvider.php:65-73` |
| Redirect setelah login | `/material-planning` | `config/fortify.php:76`, `RouteServiceProvider.php:20` |
| Tidak aktif saat session hidup | `auth.active` logout paksa + invalidasi session | `app/Http/Middleware/CheckUserActive.php:17-26` |
| Response JSON belum login | 401; request HTML diarahkan ke `route('login')` | `app/Http/Middleware/Authenticate.php:13-16` |

Fortify mengaktifkan registrasi, reset password, update profil/password, dan 2FA (`config/fortify.php:146-157`). Email verification tidak aktif. `CheckUserActive` hanya dipasang pada grup `routes/web.php:17`, bukan pada route Fortify atau `routes/api.php`, sehingga session user yang dinonaktifkan masih bisa mengakses route profil Fortify sampai menyentuh route web utama.

## Peran dan pembatasan akses

| Peran | Bisa | Tidak bisa |
| --- | --- | --- |
| `viewer` | Lihat dashboard `/material-planning`, `/data`, `/export`, `/deviation` | Semua route di dalam grup `role:admin` |
| `admin` | Semua route, termasuk import, BOM, project code, users, roles, activity log, reset data | Mengubah atau menghapus peran `admin` lewat halaman Roles |

Route dikelompokkan di `routes/web.php:17-87`:
- `middleware(['auth', 'auth.active'])` membungkus seluruh route aplikasi.
- `middleware('role:admin')` (`routes/web.php:28`) membungkus semua aksi tulis dan halaman administrasi.
- Alias `role`, `permission`, `role_or_permission` ada di `app/Http/Kernel.php:68-70`, tetapi hanya `role` yang dipakai. Tidak ada Gate atau Policy di `AuthServiceProvider`.

Beberapa controller mengulang pemeriksaan peran di constructor (`StaffController`, `RoleController`, `ActivityLogController`). `SapDatasetController::delete()` juga meminta konfirmasi eksplisit `confirm_delete=HAPUS` (`SapDatasetController.php:432-437`).

### Guardrail admin

`StaffController::changeStatus()` dan `destroy()` mencegah admin menonaktifkan atau menghapus dirinya sendiri dan mencegah penonaktifan/penghapusan admin aktif terakhir (`StaffController.php:168-232`). `RoleController` memblokir edit/update/delete peran bernama `admin` (`RoleController.php:79-114`).

Catatan: `StaffController::update()` tetap bisa mengganti peran sendiri lewat `syncRoles()`, dan pemeriksaan "admin terakhir" tidak mencakup jalur ini. `UserSeeder` juga hanya membuat peran `admin` dan `viewer`, sementara `StaffController::data()` ikut memfilter peran `staff`.

### Fortify registration

`CreateNewUser` (`app/Actions/Fortify/CreateNewUser.php:20-39`) hanya mengisi `name`, `email`, dan `password`, tidak mengisi `nip` yang bersifat unik dan wajib di tabel `users`, serta tidak memberi peran. Jadi fitur registrasi yang aktif di konfigurasi tidak menghasilkan akun yang bisa login di database sebenarnya.

## Activity log

### Penyimpanan

Tabel `activity_log` dibuat oleh `database/migrations/2025_12_16_073518_create_activity_log_table.php:9-31`. Perbedaan penting dari migrasi bawaan spatie: kolom `feature` **wajib diisi** (`NOT NULL`, baris 27). Semua penulisan log harus lewat `ActivityLogger::record()` atau observer karena keduanya mengisi `feature`.

Tabel lama `audit_logs` (`database/migrations/2026_08_19_101001_create_audit_logs_table.php`) tidak dipakai model/controller mana pun.

### `ActivityLogger` (`app/Support/ActivityLogger.php`)

- `record($event, $description, $properties, $subject, $logName, $causer)` mencatat `ip`, `url`, `user_agent` (dipotong 255 karakter), plus properti bisnis yang sudah diredaksi, dan mengisi `feature = $logName` (`L33-70`).
- `SENSITIVE_KEYS` (`L15-28`): `password`, `password_confirmation`, `current_password`, `remember_token`, `token`, `access_token`, `refresh_token`, `secret`, `api_key`, `authorization`, `two_factor_secret`, `two_factor_recovery_codes`. Nilai kunci tersebut diganti `***`.
- `redact()` (`L82-97`) bekerja rekursif untuk array, bukan objek.
- Kegagalan menulis log **tidak menggagalkan operasi bisnis**: exception ditangkap dan hanya ditulis ke `Log::warning` (`L71-76`).

### Sumber event

| Sumber | Event | Keterangan |
| --- | --- | --- |
| `GlobalObserverActivityLogs` | `created`, `updated`, `deleted` | Dipasang otomatis ke semua model di `app/Models` oleh `AppServiceProvider::boot()` (`L23-40`). Nama log = `class_basename` model (mis. `User`, `SapDataset`) karena tidak ada model yang mendefinisikan `$featureName`. |
| `LogAuthenticationActivity` | `login`, `logout`, `login_failed` | Didaftarkan di `EventServiceProvider.php:21-34`. `login_failed` memakai `email` atau `nip` dari kredensial, **tanpa password**. User nonaktif yang ditolak login juga masuk kategori ini. |
| Controller | `import`, `import_queued`, `export`, `delete_all`, `view`, `download`, `role_updated`, `created`, `updated`, `deleted` | Lihat `SapDatasetController`, `BillOfMaterialController`, `BomDocumentRevisionController`, `RoleController`, `StaffController`. |
| `DatabaseSeeder` | tidak ada | Seeding dibungkus `Activity::disableLogging()` / `enableLogging()` (`L10-17`). |

Observer dan controller bisa menghasilkan dua baris log untuk satu perubahan yang sama (contoh: `RoleController::store()` memanggil `create()` dan kemudian `ActivityLogger::record()`). Observer mengambil `old` dari `getOriginal()` setelah `save()`, jadi snapshot nilai lama perlu diverifikasi jika dipakai untuk audit diff.

### Halaman dan endpoint Activity Log

Route (`routes/web.php:57-63`, semua admin):

| Method | Path | Nama | Fungsi |
| --- | --- | --- | --- |
| GET | `/activity-log` | `activity-log.index` | Halaman DataTables + filter |
| GET | `/activity-log/data` | `activity-log.data` | JSON server-side; wajib header `X-Requested-With: XMLHttpRequest` |
| GET | `/activity-log/{activity}` | `activity-log.show` | Detail satu log; `{activity}` harus angka |

`ActivityLogController::data()` (`L78-165`) mendukung:

- filter teks bebas pada description, log name, feature, event, subjek, nama/email/NIP user;
- filter `user_id`, `event`, serta `log_name` **atau** `feature`;
- filter tanggal `date_from` / `date_to` yang diinterpretasikan di zona `Asia/Jakarta` lalu diubah ke UTC sebelum query (`L126-132`);
- `per_page` dibatasi 1..100, urutan `id DESC`.

`formatTime()` (`L197-207`) menampilkan waktu sebagai `d-m-Y H:i:s` dalam zona `Asia/Jakarta` (waktu di database disimpan UTC). `KNOWN_LOG_NAMES` dan `KNOWN_EVENTS` (`L13-42`) dipakai untuk menggabungkan nilai bawaan dengan nilai distinct dari database saat mengisi dropdown filter. Tampilan ada di `resources/views/pages/activity-log/index.blade.php` dengan modal detail dan render diff `old`/`new`.

### Retensi

`app/Console/Kernel.php:13-19` menjadwalkan:

```php
$schedule->command('activitylog:clean --days=7')
    ->timezone('Asia/Jakarta')
    ->dailyAt('02:00');
```

Artinya log lebih tua dari 7 hari dihapus tiap hari pukul 02:00 waktu Indonesia. Jalankan `php artisan schedule:run` lewat cron seperti di [getting started](../getting-started.md). Perubahan ini belum ter-commit di branch aktif; versi ter-commit memakai `--days=180` tanpa timezone.

## Pengujian terkait

- `tests/Feature/ActivityLogTest.php`: akses admin vs viewer, redaksi `***`, konversi timezone Asia/Jakarta, filter tanggal, serta pencatatan event login/login_failed tanpa password.
- `tests/Feature/ViewerAccessTest.php`: viewer hanya bisa membaca dashboard dan tidak bisa mengakses halaman/endpoint admin.
- Belum ada test untuk `CheckUserActive`, login `is_active`, 2FA, guardrail peran, dan jalur gagal `ActivityLogger`.
