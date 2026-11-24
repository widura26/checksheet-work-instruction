# Referensi route

Sumber utama: `routes/web.php`, `routes/api.php`, dan konfigurasi Fortify. Jalankan `php artisan route:list` untuk daftar yang dihasilkan framework (vendor termasuk route Fortify).

## Konvensi

- Semua route web (kecuali `/`) berada di dalam `middleware(['auth', 'auth.active'])`.
- Route di dalam grup `role:admin` hanya bisa diakses user dengan peran `admin`.
- Endpoint DataTables JSON umumnya menolak request tanpa header `X-Requested-With: XMLHttpRequest` (atau `ajax: true` dari jQuery).
- `throttle:N,1` berarti N request per menit per user/IP sesuai Laravel throttle.

## Ringkasan route web

| Method | URI | Nama | Akses | Throttle | Controller@method |
| --- | --- | --- | --- | --- | --- |
| GET | `/` | - | publik, redirect ke login | - | closure |
| GET | `/material-planning` | `sap-dataset.index` | auth + auth.active | - | `SapDatasetController@index` |
| GET | `/material-planning/data` | `material-planning.data` | viewer/admin | 120,1 | `SapDatasetController@data` |
| GET | `/material-planning/export` | `material-planning.export` | viewer/admin | 30,1 | `SapDatasetController@export` |
| GET | `/material-planning/import-status/{importId}` | `sap-dataset.import-status` | admin | 120,1 | `SapDatasetController@importStatus` |
| POST | `/material-planning/import-excel` | `sap-dataset.import-excel` | admin | 10,1 | `SapDatasetController@import_excel` |
| GET | `/project-code` | `project-code.index` | admin | - | `ProjectCodeController@index` |
| GET | `/project-code/data` | `project-code.data` | admin | - | `ProjectCodeController@getData` |
| GET | `/project-code/{project}/edit` | `project-code.edit` | admin | - | `ProjectCodeController@edit` |
| PUT | `/project-code/{project}` | `project-code.update` | admin | - | `ProjectCodeController@update` |
| GET | `/users` | `staff.index` | admin | - | `StaffController@index` |
| GET | `/users/data` | `staff.data` | admin | - | `StaffController@data` |
| POST | `/users` | `staff.store` | admin | - | `StaffController@store` |
| GET | `/users/{id}/edit` | `staff.edit` | admin | - | `StaffController@edit` |
| PUT | `/users/{id}` | `staff.update` | admin | - | `StaffController@update` |
| POST | `/users/{nip}/status` | `staff.status` | admin | - | `StaffController@changeStatus` |
| DELETE | `/users/{id}` | `staff.destroy` | admin | - | `StaffController@destroy` |
| GET | `/roles/data` | `roles.data` | admin | - | `RoleController@data` |
| GET | `/roles` | `roles.index` | admin | - | `RoleController@index` |
| POST | `/roles` | `roles.store` | admin | - | `RoleController@store` |
| GET | `/roles/create` | `roles.create` | admin | - | `RoleController@create` |
| GET | `/roles/{role}` | `roles.show` | admin | - | `RoleController@show` |
| GET | `/roles/{role}/edit` | `roles.edit` | admin | - | `RoleController@edit` |
| PUT/PATCH | `/roles/{role}` | `roles.update` | admin | - | `RoleController@update` |
| DELETE | `/roles/{role}` | `roles.destroy` | admin | - | `RoleController@destroy` |
| GET | `/activity-log` | `activity-log.index` | admin | - | `ActivityLogController@index` |
| GET | `/activity-log/data` | `activity-log.data` | admin | - | `ActivityLogController@data` |
| GET | `/activity-log/{activity}` | `activity-log.show` | admin, angka | - | `ActivityLogController@show` |
| GET | `/bill-of-material` | `bill-of-material.index` | admin | - | `BomController@index` |
| GET | `/bill-of-material/data` | `bill-of-material.data` | admin | - | `BomController@getData` |
| GET | `/bill-of-material/create` | `bill-of-material.create` | admin | - | `BomController@create` |
| POST | `/bill-of-material` | `bill-of-material.store` | admin | - | `BomController@store` |
| GET | `/bill-of-material/{id}/edit` | `bill-of-material.edit` | admin | - | `BomController@edit` |
| PUT | `/bill-of-material/{id}` | `bill-of-material.update` | admin | - | `BomController@update` |
| DELETE | `/bill-of-material/{id}/delete` | `bill-of-material.delete` | admin | - | `BomController@delete` |
| GET | `/bill-of-material/document/{id}` | `bom-document-revision.index` | admin | - | `BomDocumentRevisionController@show` |
| DELETE | `/bill-of-material/document/{id}` | `bom-document-revision.delete` | admin | - | `BomDocumentRevisionController@delete` |
| GET | `/bill-of-material/document/{id}/download` | `bom-document-revision.download` | admin | - | `BomDocumentRevisionController@download` |
| POST | `/bom/import` | `bom.import` | admin | 10,1 | `BillOfMaterialController@import` |
| GET | `/bom/status/{importId}` | `bom.status` | admin | 120,1 | `BillOfMaterialController@status` |
| DELETE | `/reset-data` | `reset-data` | admin | - | `SapDatasetController@resetData` |

Fortify menambahkan route `/login`, `/logout`, `/register`, `/forgot-password`, `/reset-password/{token}`, `/user/profile-information`, `/user/password`, serta route 2FA. Detail fitur ada di `config/fortify.php:146-157`.

## Endpoint API

| Method | URI | Middleware | Response |
| --- | --- | --- | --- |
| GET | `/api/user` | `auth:sanctum` | Data user yang sedang login |

`config/sanctum.php` memakai guard `web`. Route ini tidak memakai `auth.active` dan bukan bagian dari alur web utama.

## Parameter yang sering dipakai

### Filter Activity Log

`GET /activity-log/data` (AJAX) menerima:

| Parameter | Keterangan |
| --- | --- |
| `search[value]` atau `search` | Pencarian teks bebas |
| `user_id` | ID pembuat aktivitas |
| `event` | Nama event persis, mis. `import_queued` |
| `log_name` | Nama log atau `feature` |
| `date_from`, `date_to` | Tanggal `Y-m-d`, diinterpretasikan di `Asia/Jakarta` |
| `per_page` | 1..100, default 10 |

### Aksi dataset

| Endpoint | Catatan penting |
| --- | --- |
| `DELETE /reset-data` | Reset data rekap kebutuhan material; hanya admin |

Detail format file dan alur import ada di [panduan import](../guides/imports.md).

## Penanganan error

- Request HTML yang belum login diarahkan ke `/login`; request JSON mendapat 401 (`app/Http/Middleware/Authenticate.php`).
- User dengan `is_active = false` logout paksa dan diarahkan ke `/login` saat mengakses route web.
- Role yang tidak diizinkan mendapat response 403 dari middleware `role:admin`.
- Endpoint DataTables tanpa header AJAX dihentikan dengan 404 oleh controller terkait.
