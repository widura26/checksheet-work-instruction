# Panduan import BOM dan dataset material

Ada **dua** jalur import dengan mekanisme berbeda. Memilih jalur yang salah adalah sumber bug paling umum di aplikasi ini.

| Jalur | Endpoint | Eksekusi | Pelacakan status | Tujuan tabel |
| --- | --- | --- | --- | --- |
| Rekap BOM | `POST /bom/import` | Job antrean | `GET /bom/status/{importId}` (payload `jobs`) | `bill_of_material_dataset` (ditimpa penuh) |
| Rekap kebutuhan material | `POST /material-planning/import-excel` | `dispatchSync` (langsung di request) | `GET /material-planning/import-status/{importId}` (cache) | `daftar_kebutuhan_materials` (append) |

Tabel `sap_datasets` yang dipakai halaman deviasi **tidak memiliki jalur import aktif**. Jalur CSV legacy (`POST /material-planning/import`) sudah dihapus, dan `import_excel` hanya mengisi `daftar_kebutuhan_materials`.

Semua endpoint import berada di dalam `role:admin` dan `throttle:10,1`. File diunggah maksimal 20 MB.

## Prasyarat bersama

```bash
QUEUE_CONNECTION=database   # agar job BOM diproses
php artisan queue:work      # harus berjalan terus
```

Status rekap material disimpan di cache (`mp:import:{id}`, TTL 24 jam) dan bisa dibaca lewat `GET /material-planning/import-status/{importId}`. Karena import berjalan di dalam request HTTP, polling terutama berguna untuk memantau progres sebelum response selesai. Pastikan cache store yang dipakai konsisten antar proses yang mengaksesnya.

## 1. Rekap BOM (`/bom/import`)

### Format file

- Ekstensi yang diterima: `csv`, `txt`, `xlsx`, `xls` (`mimes`, maksimal 20480 KB).
- Baris pertama adalah header. Mapping memakai `app/Support/BomColumnMapper.php` dengan 10 kolom kanonik:

| Kolom database | Alias contoh |
| --- | --- |
| `sheet_name` | Sheet Name, sheet name |
| `kode_material` | Kode Material |
| `kode_material_delete` | Kode Material Delete |
| `kode_material_stock` | Kode Material Stock |
| `material_komponen` | Material Komponen |
| `spesifikasi` | Spesifikasi |
| `qty_ts_series` | Qty TS Series |
| `uom_car` | UoM/Car, UoM Car |
| `qty_total_series_allowance` | Qty Total Series Allowance |
| `qty_pr_total` | Qty PR Total |

Normalisasi header: `Str::slug(trim($header), '_')`. Jika `kode_material` tidak ditemukan di hasil mapping, import melempar `RuntimeException` **sebelum** tabel dihapus.

### Alur

```
POST /bom/import (multipart: file)
  -> UUID import_id
  -> storage/app/bom-imports/{uuid}.{ext}
  -> ImportBomDatasetJob::dispatch(importId, path absolut)
  -> response { success, import_id, message }
```

`ImportBomDatasetJob` (`app/Jobs/ImportBomDatasetJob.php`) menjalankan `Excel::import(new BomDatasetImport($importId), $path)` dengan `timeout=600`, `tries=1`, lalu menghapus file sumber.

`BomDatasetImport::collection()` (`app/Imports/BomDatasetImport.php:20-82`):

1. Mengambil baris pertama sebagai header dan memetakannya.
2. Melewati baris dengan jumlah nilai terpetakan ≤ 1.
3. Menandai `status = invalid` jika `kode_material` kosong.
4. Di dalam transaksi: `DELETE FROM bill_of_material_dataset`, lalu insert dalam potongan 500 baris.

Artinya **setiap import menggantikan seluruh rekap lama**. File hanya berisi header akan mengosongkan tabel. File kosong tidak melakukan apa-apa.

### Status

`GET /bom/status/{importId}` menghitung:

- job di tabel `jobs` yang `payload`-nya mengandung ID;
- job gagal di `failed_jobs` dengan cara yang sama;
- baris `bill_of_material_dataset` dengan `import_id` tersebut.

Aturan status: ada failed -> `failed`; masih ada di `jobs` -> `pending`; jika tidak -> `completed`. Tidak ada status `processing`, ID tidak divalidasi, dan ID yang tidak dikenal dilaporkan `completed` dengan `imported: 0`.

### Catatan UI

`public/js/bom-import.js` menargetkan DOM ID `btnBomImport`, `bomFile`, `bomImportResult`, dan `bomImportProgress`, yang **tidak ada** di view saat ini. Tombol "Import Excel" di halaman BOM juga menargetkan modal `#importExcelModal` yang tidak tersedia. Endpoint `/bom/import` saat ini hanya diuji lewat test feature, bukan lewat UI yang berfungsi.

## 2. Rekap kebutuhan material (`/material-planning/import-excel`)

Ini jalur yang dipakai tombol unggah di dashboard.

### Alur

```
POST /material-planning/import-excel (xlsx|xls, 20 MB)
  -> storage/app/imports/{uuid}.{ext}
  -> ImportTracker::start(uuid)
  -> set_time_limit(0), memory_limit=512M
  -> ImportExcelJob::dispatchSync(uuid, path)   // sinkron, bukan antrean
  -> response "Import selesai diproses..."
```

Karena memakai `dispatchSync()`, request HTTP menunggu sampai file selesai diproses. File besar bisa memakan waktu lama dan bergantung pada `max_execution_time` web server meskipun `set_time_limit(0)` dipanggil di dalam job.

### Format file

`ImportExcelJob` tidak membaca header. Posisi kolom tetap A sampai Q:

| Kolom | Field | Kolom | Field |
| --- | --- | --- | --- |
| A | `wbs` | J | `group` |
| B | `sheet_asal` | K | `lot` |
| C | `no` | L | `kebutuhan` |
| D | `rev` | M | `tindak_lanjut` |
| E | `kode_material` | N | `no_pr` |
| F | `kode_material_konversi` | O | `no_po` |
| G | `kode_material_delete` | P | `qty_pr` |
| H | `deskripsi_material` | Q | `deliv_date_sap` |
| I | `satuan` | | |

Baris 1 dilewati sebagai header. Jika kolom B kosong, `sheet_asal` diisi dari nama worksheet. Baris yang semua kolomnya kosong dilewati. Insert dilakukan per 500 baris ke `daftar_kebutuhan_materials`.

`ExcelReader` (`app/Support/ExcelReader.php`) membaca XLSX langsung sebagai ZIP/XML: `workbook.xml` + relasinya untuk daftar sheet, lalu `XMLReader` pada `xl/worksheets/{sheet}.xml`. Shared string, inline string, dan nilai `<v>` didukung. Tidak ada konversi serial tanggal dan tidak ada evaluasi formula selain nilai cache.

File sumber dihapus di blok `finally`. Setelah selesai, `ImportTracker::completed()` menaikkan `mp:version`, sehingga dashboard mengambil data baru.

## Polling status di frontend

`public/js/sap-dataset/index.js` memakai jeda `[0, 1000, 2000, 4000, 7000, 10000, 10000, 10000]` ms, membatalkan polling sebelumnya, dan berhenti sementara saat tab tidak aktif. Status 404/422 dianggap gagal segera. Setelah `completed`, cache data dan cache service worker dibersihkan, lalu halaman dimuat ulang.

Untuk integrasi manual, pola minimalnya:

```bash
# 1. unggah
curl -X POST http://localhost:8000/material-planning/import-excel \
  -H "X-CSRF-TOKEN: <token>" \
  -F "file=@rekap.xlsx"

# 2. respon mengandung import_id, lalu cek berkala
curl http://localhost:8000/material-planning/import-status/<import_id>
```

## Masalah yang diketahui

| Masalah | Dampak |
| --- | --- |
| Dua mekanisme status berbeda | `/bom/status` tidak bisa dipakai untuk import rekap material dan sebaliknya |
| `/bom/status` menganggap ID asing sebagai `completed` | UI bisa melaporkan sukses padahal tidak ada job |
| UI menyebut batas 25 MB, server 20 MB | User bisa mendapat 422 setelah memilih file |
| Input file dokumen BOM menerima doc/docx/gambar, server hanya pdf/xls/xlsx | Validasi client-server tidak cocok |
| Import BOM menghapus seluruh tabel | Pastikan file baru lengkap sebelum mengunggah |
| `config/bom-sheets.php` tidak dipakai kode | Perubahan konfigurasi di file itu tidak berpengaruh; mapping ada di `BomColumnMapper` |

## Pengujian

| File | Yang diuji |
| --- | --- |
| `tests/Unit/BomColumnMapperTest.php` | Normalisasi dan pemetaan 10 header BOM |
| `tests/Feature/BomDatasetImportParserTest.php` | Status baris, replace penuh, header salah, file kosong |
| `tests/Feature/BomDatasetImportEndpointTest.php` | Unggah `/bom/import`, status sukses/gagal |
| `tests/Feature/SapDatasetImportStatusTest.php` | Validasi UUID, state cache, versi cache, ekspor |
| `tests/Feature/MaterialImportColumnsTest.php` | Pemetaan 17 kolom A-Q `ImportExcelJob` |
| `tests/Feature/MaterialExportFilterTest.php` | Filter status dan pencarian pada ekspor |
| `tests/Feature/ResetDataTest.php` | Pembersihan data dan inkrementasi versi cache |

Beberapa assertion di `SapDatasetImportStatusTest` tampak sudah tertinggal dari implementasi (misalnya mengharapkan `groupLabels`, sementara service mengembalikan `matrixGroups`, dan mengharapkan header `Status PR` pada CSV padahal header export berasal dari nama kolom database). Periksa lagi sebelum mengubah service hanya karena test gagal.
