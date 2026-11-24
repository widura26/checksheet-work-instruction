# Panduan import BOM dan dataset material

Ada **empat** jalur import dengan mekanisme berbeda. Memilih jalur yang salah adalah sumber bug paling umum di aplikasi ini.

| Jalur | Endpoint | Eksekusi | Pelacakan status | Tujuan tabel |
| --- | --- | --- | --- | --- |
| Rekap BOM | `POST /bom/import` | Job antrean | `GET /bom/status/{importId}` (payload `jobs`) | `bill_of_material_dataset` (ditimpa penuh) |
| PR SAP | `POST /material-planning/import-queue` | `Excel::queueImport` | `GET /material-planning/import-status/{importId}` (cache) | `sap_datasets` (append) |
| Rekap kebutuhan material | `POST /material-planning/import-excel` | `dispatchSync` (langsung di request) | Endpoint status yang sama, tetapi tanpa antrean nyata | `daftar_kebutuhan_materials` (append) |
| CSV SAP legacy | `POST /material-planning/import` | Sinkron, parser teks | Tidak ada | `sap_datasets` (append) |

Semua endpoint import berada di dalam `role:admin` dan `throttle:10,1`. File diunggah maksimal 20 MB.

## Prasyarat bersama

```bash
QUEUE_CONNECTION=database   # agar job BOM dan PR SAP diproses
php artisan queue:work      # harus berjalan terus
```

Status PR SAP dan rekap material disimpan di cache (`mp:import:{id}`, TTL 24 jam). Web dan worker **harus memakai cache store yang sama**, misalnya `CACHE_DRIVER=file` pada satu host atau Redis bersama pada beberapa host. Jika tidak, polling status tidak pernah melihat perubahan dari worker.

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

## 2. PR SAP (`/material-planning/import-queue`)

### Alur

```
POST /material-planning/import-queue
  -> validasi xlsx,xls,csv,txt (20 MB)
  -> storage/app/imports/{uuid}.{ext}
  -> ImportTracker::start(uuid)
  -> Excel::queueImport(new SapDatasetImport(uuid), path)
  -> response { success, import_id, message }
```

`SapDatasetImport` (`app/Imports/SapDatasetImport.php`) memakai `ToCollection`, `WithBatchInserts`, `WithChunkReading`, `WithStartRow`, `SkipsEmptyRows`, `WithHeadingRow`, `WithEvents`, dan `ShouldQueue` (`timeout=600`, `tries=3`).

| Pengaturan | Nilai |
| --- | --- |
| Baris mulai | 2 (baris 1 dianggap header) |
| Ukuran chunk baca | 2000 |
| Ukuran batch insert | 1000 |
| Valid | `wbs_elem` atau `material_code` terisi |
| Invalid | keduanya kosong -> `status = invalid` |
| Baris dilewati | jumlah kolom terisi ≤ 1 |

Event `BeforeImport`, `AfterImport`, dan `ImportFailed` memperbarui `ImportTracker`. `completed()` juga menaikkan `mp:version` agar cache dashboard dan filter langsung diperbarui.

### Pelacakan

`GET /material-planning/import-status/{importId}`:

- `importId` bukan UUID -> 422;
- state tidak ada/kedaluwarsa -> 404 `{ status: 'not_found' }`;
- selain itu mengembalikan state cache: `pending`, `processing`, `completed`, atau `failed` beserta `imported`, `error`, dan `updated_at`.

`ImportTracker` tersimpan 24 jam. Job ini **tidak menghapus file yang diunggah**, jadi `storage/app/imports/` perlu dibersihkan berkala.

### Ketidaksesuaian header

Alias di `SapDatasetImport::mapRow()` lebih sempit daripada `SapDatasetController::buildHeaderMap()` yang dipakai jalur CSV sinkron. Contoh: `purch.req.`, `req.date`, dan `mat. description` dikenali jalur sinkron tetapi tidak oleh jalur antrean. Jika eksport SAP mengubah format header, perbarui kedua tempat.

### Catatan UI

Handler `btnImport` / `importForm` di `public/js/sap-dataset/index.js:695-782` menargetkan DOM ID yang tidak ada di view. Endpoint tetap tersedia untuk dipanggil langsung atau lewat test.

## 3. Rekap kebutuhan material (`/material-planning/import-excel`)

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

## 4. CSV SAP legacy (`/material-planning/import`)

Endpoint lama yang masih ada. Validasi menerima `xlsx|xls|csv|txt`, tetapi implementasinya membaca file sebagai teks dengan `file_get_contents`, deteksi encoding, dan `fgetcsv`. **XLS/XLSX biner akan terbaca sebagai sampah**. Gunakan jalur antrean untuk Excel.

Karakteristik:

- delimiter: TAB jika baris pertama mengandung tab, `;` jika lebih banyak semicolon daripada koma, jika tidak `,`;
- alias header 26 kolom dibangun `SapDatasetController::buildHeaderMap()` (`L384-425`);
- baris dengan jumlah kolom = jumlah header + 1 dan sel pertama kosong diperbaiki dengan `array_shift`;
- baris dengan ≤ 50% kolom terisi dilewati;
- baris invalid jika `wbs_elem` dan `material_code` kosong; nilai string pada baris invalid dipotong 255 karakter;
- insert langsung tanpa transaksi, sehingga kegagalan di tengah bisa menyisakan data sebagian;
- file sementara di `sys_get_temp_dir()` dihapus di akhir;
- tidak ada endpoint status untuk jalur ini.

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
| Tiga mekanisme status berbeda | `/bom/status` tidak bisa dipakai untuk import SAP dan sebaliknya |
| `/bom/status` menganggap ID asing sebagai `completed` | UI bisa melaporkan sukses padahal tidak ada job |
| File PR SAP tidak pernah dihapus | `storage/app/imports/` terus bertambah |
| Jalur sinkron SAP menerima XLS/XLSX | File Excel diparse sebagai CSV |
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
| `tests/Feature/SapDatasetImportStatusTest.php` | Validasi UUID, state cache, versi cache, ekspor, `/import-queue` |
| `tests/Feature/MaterialImportColumnsTest.php` | Pemetaan 17 kolom A-Q `ImportExcelJob` |
| `tests/Feature/MaterialExportFilterTest.php` | Filter status dan pencarian pada ekspor |
| `tests/Feature/ResetDataTest.php` | Pembersihan data dan inkrementasi versi cache |

Beberapa assertion di `SapDatasetImportStatusTest` tampak sudah tertinggal dari implementasi (misalnya mengharapkan `groupLabels`, sementara service mengembalikan `matrixGroups`, dan mengharapkan header `Status PR` pada CSV padahal header export berasal dari nama kolom database). Periksa lagi sebelum mengubah service hanya karena test gagal.
