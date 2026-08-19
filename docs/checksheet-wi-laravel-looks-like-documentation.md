# Dokumentasi Implementasi Laravel — Aplikasi Checksheet Wi Renprod

> Dokumen pendamping `DOKUMENTASI_FITUR.md`. Berisi rancangan implementasi sistem di atas **Laravel**:
> daftar tabel, menu, fitur, controller, route, dan view untuk setiap bagian sistem.
> Basis data yang dipakai tetap database `barcode` yang sudah ada (60 tabel).

---

## 1. Ringkasan Arsitektur

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 11/12 (PHP >= 8.2) |
| Database | MySQL 8 - DB `barcode` (60 tabel, data tetap) |
| ORM | Eloquent (model per tabel) + Query Builder untuk laporan kompleks |
| Auth | Guard custom tabel `user` (hash `sha1`), middleware `CheckLevel` per peran |
| Frontend | Blade + Bootstrap 3 (tetap) / DataTables / Select2 / Chart.js (via npm) |
| Import/Export | PhpSpreadsheet (pengganti PHPExcel) |
| PDF | Dompdf / print-view HTML (kop INKA) |
| Upload | Laravel Storage + `php artisan storage:link` |

Struktur folder target:

```
app/
  Models/                  (60 model)
  Http/Controllers/        (controller per area)
  Http/Middleware/         (CheckLevel, dll)
database/migrations/       (60 migration + index)
resources/views/
  layouts/app.blade.php
  partials/sidebar.blade.php
  <area>/<modul>/...blade.php
routes/web.php
```

---

## 2. Autentikasi & Peran (Level)

- **Guard**: `config/auth.php` -> provider memakai tabel `user` (kolom id = `kode_user`, password `sha1`).
- **Level** (dari `user.level`): `operator`, `admin`, `operator1`, `operator2`, `operator3`, `operator4`, `operator5`.
- **Middleware `CheckLevel`**:
  - `CheckLevel:admin` - hanya `admin`
  - `CheckLevel:operator` - operator MD/WI/CP/Barcode
  - `CheckLevel:operator1` - manajemen user/proyek
  - `CheckLevel:operator3` - PPCB (STTP, SPM, MDR, proses fab, expedisi)
  - `CheckLevel:operator4` - Dalmat/INKA (STTP dalmat, BPM/SPM, gas, tool)
  - `CheckLevel:operator5` - proses fabrikasi
  - `CheckLevel:any` - semua role login (untuk menu umum)
- **Bypass login** lama dihapus; semua route dilindungi `auth`.

---

## 3. Daftar Route Lengkap (`routes/web.php`)

Konvensi: `resource` untuk CRUD, route prefix per area, parameter int.

### 3.1 Auth & Dashboard

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| GET | `/` | `DashboardController@index` | auth | redirect ke dashboard |
| GET | `/dashboard` | `DashboardController@index` | auth | KPI + pilih proyek |
| GET | `/grafik` | `GrafikController@index` | auth | grafik MD per proyek |
| GET | `/login` | `Auth\LoginController@showLoginForm` | guest | form login |
| POST | `/login` | `Auth\LoginController@login` | guest | proses login |
| POST | `/logout` | `Auth\LoginController@logout` | auth | logout |

### 3.2 Master Data

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/master/oka` | `OkaController` | auth + CheckLevel:admin,operator1 | Kode Proyek (`oka`) |
| resource | `/master/proses` | `ProsesController` | auth | Kode Proses |
| resource | `/master/kode-material` | `KodeMaterialController` | auth | Kode Material (`wi_trn_bpm`) |
| resource | `/user` | `UserController` | auth + CheckLevel:operator1 | Manajemen akun |

### 3.3 MD (Master Data / Checksheet)

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/md` | `MdController` | auth | MD (`md`, `md2`, `md3`, `md4`, `md5`) |
| POST | `/md/edit1` | `MdController@editBatch` | auth | edit batch MD (`mdh_md2`) |
| resource | `/md-lap` | `MdLapController` | auth | Checksheet Md / Md Lap (`md_lap`) |
| resource | `/md-jadwal` | `MdJadwalController` | auth | Jadwal MD (`jadwal`, `md_lap`) |
| resource | `/lap-md` | `LapMdController` | auth | Laporan Md (`md_header`) |

### 3.4 WI (Work Instruction)

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/wi` | `WiController` | auth | WI (`wi`, `wi_1`...`wi_6`, `wi_2a`) |
| GET | `/wi/create/from-barcode` | `WiController@createFromBarcode` | auth | add WI dari barcode (no konversi) |
| POST | `/wi/add1` | `WiController@storeBpm` | auth | set no_bpm/idbarang/tgl_bpm |
| POST | `/wi/edit1` | `WiController@updateTarget` | auth | hitung `target_wi` + `konv_wi` |
| GET | `/wi/{id}/print` | `WiPrintController@show` | auth | Print Wi (`wi_5`, `wi_51`) |
| resource | `/wi-mat` | `WiMatController` | auth | Status Raw Material (`wi_mat`, `wi_mat1`) |
| resource | `/lap-wi` | `WiLapController` | auth | Lap Wi (`wi_lap`, `wi_lap1`, `lap_wi`, `lap_wi_all`) |
| resource | `/rencana-wi` | `RencanaWiController` | auth | Rencana WI turun (`lap_wi`, `memo`) |

### 3.5 Barcode

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/barcode` | `BarcodeController` | auth | Barcode (`barcode`, `barcode_1/2/3`, `barcode_a`) |

### 3.6 CP (Check Point) & BPM

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/cp` | `CpController` | auth | CP (`cp`, `cp1`, `cp_bpm`, `cp_bpm1`) |
| resource | `/bpm-cp` | `BpmCpController` | auth | BPM-CP (`bpm_cp`, `bpm_cp_1`, `bpm_cp_admin`) |
| POST | `/bpm-cp/edit1` | `BpmCpController@updateLink` | auth | sinkron `wi_trn` + `bpm_cp` |
| resource | `/cp-lap` | `CpLapController` | auth | Laporan Cp / Bpm (`cp_1`) |
| resource | `/exp-cp` | `ExpCpController` | auth | Expedisi CP (`exp_cp`, `renprod_exp`) |

### 3.7 Checksheet

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/checksheet` | `ChecksheetController` | auth | Checksheet (`checksheet`, `_1`, `_2`, `_21`) |

### 3.8 Proses Fabrikasi

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/fabrikasi` | `FabController` | auth | Proses Fabrikasi (`fab`, `fab1`) |
| resource | `/self-check` | `SelfCheckController` | auth | Self Check (`self_check`) |
| resource | `/proses-fab` | `ProsesFabController` | auth | Proses Fab PPCB (`proses_ppcb`, `proses_fab_lab`) |
| resource | `/proses-start` | `ProsesStartController` | auth | Lap Proses Start (`wi_trn`) |
| resource | `/lap-proses-ws` | `LapProsesWsController` | auth | Lap Proses Ws (`wi_trn`) |

### 3.9 STTP & Dalmat (Material)

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/sttp` | `SttpController` | auth | STTP (`sttp`, `sttp1/1a/2/2a/3/4/4a/5/51`, `sttp_fab`) |
| POST | `/sttp/move` | `SttpController@moveDraft` | auth | draft `sttpp_fab_1` -> `sttpp_fab` |
| resource | `/sttp-dalmat` | `SttpDalmatController` | auth | `sttp_dalmat`, `sttp_dalmat_datang`, `sttp_datang_org` |
| resource | `/piecepart` | `PiecepartController` | auth | Piece Part Stock (`piecepart`, `piecepart_datang`) |
| resource | `/ncr` | `NcrController` | auth | NCR (`ncr` + join `md_header`) |
| resource | `/mdr` | `MdrController` | auth | MDR (`sttpp_fab`) |

### 3.10 BPM / SPM

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/dalmat/bpm` | `BpmController` | auth + CheckLevel:operator4 | BPM (`bpm`) |
| resource | `/dalmat/bpm-ims` | `BpmImsController` | auth + CheckLevel:operator4 | BPM INKA (`bpm_ims`) |
| resource | `/dalmat/spm` | `SpmController` | auth + CheckLevel:operator4 | SPM (`spm`) |
| resource | `/dalmat/spm-ims` | `SpmImsController` | auth + CheckLevel:operator4 | SPM INKA (`spm_ims`) |
| resource | `/dalmat/spm-org` | `SpmOrgController` | auth + CheckLevel:operator4 | Penyerahan SPM (`spm_org`, `spm_ims_org`) |
| resource | `/dalmat/spm-ppcb` | `SpmPpcbController` | auth + CheckLevel:operator3 | SPM Wi (`spm_ppcb`) |

### 3.11 Gas

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/dalmat/gas` | `GasController` | auth + CheckLevel:operator4 | Gas INKA (`gas`) |
| GET | `/dalmat/gas/report` | `GasController@report` | auth + CheckLevel:operator4 | Report Gas |

### 3.12 Tool & Consumable

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/tool/fabrikasi` | `ToolFabController` | auth | `tool_fab`, `tool_fab1` (+ upload foto) |
| resource | `/tool/finishing` | `ToolFinController` | auth | `tool_fin`, `tool_fin1` |
| resource | `/tool/fabrikasi-ims` | `ToolFabImsController` | auth + CheckLevel:operator4 | `tool_fab_ims`, `con_fab_ims` |
| resource | `/tool/finishing-ims` | `ToolFinImsController` | auth + CheckLevel:operator4 | `tool_fin_ims`, `con_fin_ims` |

### 3.13 Expedisi

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/expedisi` | `ExpedisiController` | auth | Exp Wi (`exp`, `exp1`, `exp2`) |
| GET | `/expedisi/input` | `ExpedisiController@create` | auth | Input Exp Wi |
| GET | `/expedisi/penerima` | `ExpedisiController@penerima` | auth | Input Penerima |
| resource | `/expedisi/renprod` | `RenprodExpController` | auth | Exp Wi ke Dal (`renprod_exp`, `_1`, `_2`) |

### 3.14 Pengadaan & Memo

| Method | URI | Controller@Method | Middleware | Keterangan |
|---|---|---|---|---|
| resource | `/pengadaan` | `PengadaanController` | auth | Status Pengadaan (`pengadaan`, `pengadaan1`) |
| resource | `/memo-masuk` | `MemoMasukController` | auth | Memo Masuk (`memo_masuk`) |

### 3.15 Utility (dipakai semua resource)

| Method | URI | Controller@Method | Keterangan |
|---|---|---|---|
| POST | `/{area}/{id}/export` | `*Controller@export` | export Excel (query dari controller, bukan raw SQL form) |
| POST | `/{area}/import` | `*Controller@import` | import Excel (PhpSpreadsheet) |
| GET | `/{area}/{id}/print` | `*Controller@print` | print view / PDF |

---

## 4. Menu & Navigasi per Peran (Sidebar Blade)

`resources/views/partials/sidebar.blade.php` - menu dikelompokkan dengan `@if(Auth::user()->level == '...')`.

### Menu `admin`
Dashboard, Grafik
- **Md** -> `/md`, `/bpm-cp`, `/exp-cp`
- **Wi** -> `/wi`, `/wi/{id}/print`
- **Data Material** -> `/master/kode-material`, `/wi-mat`
- **Barcode** -> `/barcode`

### Menu `operator`
Dashboard, `/master/oka`, `/master/proses`
- **Md** -> `/md`, `/md-lap`
- **Jadwal** -> `/md-jadwal`
- Cp -> `/cp`
- **Wi** -> `/wi`
- Barcode -> `/barcode`, `/barcode?a=B` (Barcode B)

### Menu `operator1`
User `/user`, Edit Proyek `/master/oka`, Grafik `/grafik`, Bpm Cp `/bpm-cp`

### Menu `operator3`
- **Sttp2** -> `/sttp`, `/lap-sttp`
- **Spm** -> `/dalmat/spm-ppcb`
- **MDR** -> `/mdr`
- **Proses Fab** -> `/self-check`, `/proses-fab`
- **Input Expedisi** -> `/expedisi`, `/expedisi/renprod`

### Menu `operator4`
`/master/kode-material`, `/md`, `/wi`
- **Cp** -> `/cp`, `/bpm-cp`
- **Sttp Dalmat** -> `/sttp-dalmat`, `/piecepart`, `/ncr`
- **Dalmat** -> `/dalmat/bpm`, `/dalmat/spm`, `/dalmat/spm-org`
- **Dalmat INKA** -> `/dalmat/bpm-ims`, `/dalmat/spm-ims`, `/dalmat/spm-ims-org`
- **Gas** -> `/dalmat/gas`, `/dalmat/gas/report`
- **Tool Inka** -> `/tool/fabrikasi`, `/tool/finishing`
- **Tool Ims** -> `/tool/fabrikasi-ims`, `/tool/finishing-ims`

### Menu `operator5`
`/md`, `/wi`
- **Proses Fab** -> `/self-check`, `/proses-fab`

### Menu Umum (semua role)
`/sttp`, **Expedisi** -> `/expedisi`, `/expedisi/renprod`, **Laporan** -> `/lap-wi`, `/checksheet`, `/cp-lap`

---

## 5. Detail Modul -> Tabel, Menu, Fitur, Controller, Route, View

### 5.1 Kode Proyek - `OkaController`

| Aspek | Rincian |
|---|---|
| Tabel | `oka` |
| Menu | Kode Proyek / Edit Proyek (admin, operator1, umum) |
| Fitur | Daftar + search + paginasi; edit `label_proyek` & `tampil_g`; export Excel |
| Controller | `OkaController` |
| Route | `resource /master/oka`, `POST /master/oka/{id}/export` |
| View | `master/oka/index.blade.php`, `master/oka/edit.blade.php` |

### 5.2 Kode Proses - `ProsesController`

| Aspek | Rincian |
|---|---|
| Tabel | `proses` |
| Menu | Proses (umum) |
| Fitur | Daftar, add, edit, import |
| Controller | `ProsesController` |
| Route | `resource /master/proses` |
| View | `master/proses/index.blade.php`, `master/proses/form.blade.php` |

### 5.3 Kode Material - `KodeMaterialController`

| Aspek | Rincian |
|---|---|
| Tabel | `wi_trn_bpm` |
| Menu | Kode Mat (admin, operator4, default) |
| Fitur | CRUD idbarang + spec; import Excel |
| Controller | `KodeMaterialController` |
| Route | `resource /master/kode-material` |
| View | `master/kode-material/index.blade.php`, `form.blade.php` |

### 5.4 User - `UserController`

| Aspek | Rincian |
|---|---|
| Tabel | `user` |
| Menu | User (operator1) |
| Fitur | CRUD akun, reset password, import |
| Controller | `UserController` |
| Route | `resource /user` |
| View | `user/index.blade.php`, `user/form.blade.php` |

### 5.5 MD - `MdController`

| Aspek | Rincian |
|---|---|
| Tabel | `md_header` (+ varian `md`) |
| Menu | Md / Md 2 / Masterpart (semua role) |
| Fitur | Daftar MD, add, edit batch (`mdh_md2 = part_no + proyek`), import Excel (A-AA), export |
| Controller | `MdController` |
| Route | `resource /md`, `POST /md/edit1`, `POST /md/import` |
| View | `md/index.blade.php`, `md/form.blade.php`, `md/batch.blade.php`, `md/import.blade.php` |

### 5.6 MD Lap / Jadwal - `MdLapController`, `MdJadwalController`

| Aspek | Rincian |
|---|---|
| Tabel | `md_lap`, `jadwal` |
| Menu | Md Lap, Jadwal (operator) |
| Fitur | Daftar checksheet MD + join `wi_trn`; import |
| Controller | `MdLapController`, `MdJadwalController` |
| Route | `resource /md-lap`, `resource /md-jadwal` |
| View | `md_lap/index.blade.php`, `md_jadwal/index.blade.php` |

### 5.7 Laporan MD - `LapMdController`

| Aspek | Rincian |
|---|---|
| Tabel | `md_header` |
| Menu | Laporan Md |
| Fitur | Filter part, paginasi, export Excel |
| Controller | `LapMdController` |
| Route | `resource /lap-md` |
| View | `lap/md.blade.php` |

### 5.8 WI - `WiController`

| Aspek | Rincian |
|---|---|
| Tabel | `wi_trn` |
| Menu | Wi / Wi 1 (semua role) |
| Fitur | Daftar WI + search; buat WI dari barcode (no konversi); set no_bpm; edit batch + `target_wi = tgl_ctplan + 1`; import Excel (A-R); export; grafik |
| Controller | `WiController` |
| Route | `resource /wi`, `GET /wi/create/from-barcode`, `POST /wi/add1`, `POST /wi/edit1`, `POST /wi/import`, `POST /wi/{id}/export` |
| View | `wi/index.blade.php`, `wi/create.blade.php`, `wi/create-from-barcode.blade.php`, `wi/bpm.blade.php`, `wi/batch.blade.php`, `wi/import.blade.php`, `wi/grafik.blade.php` |

### 5.9 Print WI - `WiPrintController`

| Aspek | Rincian |
|---|---|
| Tabel | `wi_trn` (join `oka`) |
| Menu | Print Wi 2 (`wi_51`), Print Wi UGL (`wi_5`) |
| Fitur | Print surat WI per `id`; pilih total lot berdasar `jenis_kereta` (1=total_lot, 2=total_lot1, 3=total_lot2) |
| Controller | `WiPrintController` |
| Route | `GET /wi/{id}/print` |
| View | `wi/print.blade.php` |

### 5.10 Status Material & Lap Wi - `WiMatController`, `WiLapController`, `RencanaWiController`

| Aspek | Rincian |
|---|---|
| Tabel | `wi_trn`, `lap_wi`, `md`, `memo` |
| Menu | Status Raw Material, Lap Wi, Lap Wi-Spm, Lap Wi All, Rencana WI |
| Fitur | Daftar status/rencana, laporan + export |
| Controller | `WiMatController`, `WiLapController`, `RencanaWiController` |
| Route | `resource /wi-mat`, `resource /lap-wi`, `resource /rencana-wi` |
| View | `wi_mat/index.blade.php`, `lap/wi.blade.php`, `lap/wi-all.blade.php`, `rencana_wi/index.blade.php` |

### 5.11 Barcode - `BarcodeController`

| Aspek | Rincian |
|---|---|
| Tabel | `barcode` |
| Menu | Barcode / Barcode B / Barcode 3 (semua role) |
| Fitur | Daftar + search; add scan; edit batch; hapus massal (select-all); import Excel (A-I); export; grafik |
| Controller | `BarcodeController` |
| Route | `resource /barcode`, `POST /barcode/import`, `POST /barcode/destroy-many` |
| View | `barcode/index.blade.php`, `barcode/form.blade.php`, `barcode/batch.blade.php`, `barcode/import.blade.php`, `barcode/grafik.blade.php` |

### 5.12 CP & BPM-CP - `CpController`, `BpmCpController`, `CpLapController`

| Aspek | Rincian |
|---|---|
| Tabel | `cp_1`, `cp`, `bpm_cp`, `wi_trn` |
| Menu | Cp, Bpm, Bpm Cp, Laporan Cp/Bpm (admin, operator, operator4) |
| Fitur | Daftar CP dari `wi_trn`; add `cp_1`; add `bpm_cp` (no_cp/no_bpm); edit batch sinkron `wi_trn` + `bpm_cp` (mat_cp, spek_mat_cp, mat_sisa, qty_cp); import `cp_1`; export; laporan |
| Controller | `CpController`, `BpmCpController`, `CpLapController` |
| Route | `resource /cp`, `resource /bpm-cp`, `POST /bpm-cp/edit1`, `resource /cp-lap` |
| View | `cp/index.blade.php`, `cp/form.blade.php`, `bpm_cp/index.blade.php`, `bpm_cp/batch.blade.php`, `cp/lap.blade.php` |

### 5.13 Expedisi CP - `ExpCpController`

| Aspek | Rincian |
|---|---|
| Tabel | `exp_cp` |
| Menu | Exp Cp / Exp Wi ke Dal Fab-Fin (admin) |
| Fitur | Daftar, input, edit, export |
| Controller | `ExpCpController` |
| Route | `resource /exp-cp` |
| View | `expedisi/cp.blade.php`, `expedisi/cp-form.blade.php` |

### 5.14 Checksheet - `ChecksheetController`

| Aspek | Rincian |
|---|---|
| Tabel | `wi_trn`, `md_header` |
| Menu | Lap Cp, Checksheet Wi (umum) |
| Fitur | Daftar checksheet + export |
| Controller | `ChecksheetController` |
| Route | `resource /checksheet` |
| View | `checksheet/index.blade.php` |

### 5.15 Proses Fabrikasi & Self Check - `FabController`, `ProsesFabController`, `SelfCheckController`, `ProsesStartController`, `LapProsesWsController`

| Aspek | Rincian |
|---|---|
| Tabel | `barcode_start`, `barcode_detail`, `self_check`, `wi_trn` |
| Menu | Proses Fabrikasi, Self Check, Proses Fab, Lap Proses Fab, Lap Proses Start, Lap Proses Ws |
| Fitur | Daftar proses fab; input self check (part, qty, tgl_kirim); edit batch; laporan |
| Controller | `FabController`, `ProsesFabController`, `SelfCheckController`, `ProsesStartController`, `LapProsesWsController` |
| Route | `resource /fabrikasi`, `resource /proses-fab`, `resource /self-check`, `resource /proses-start`, `resource /lap-proses-ws` |
| View | `fabrikasi/index.blade.php`, `proses_fab/index.blade.php`, `self_check/index.blade.php`, `lap/proses*.blade.php` |

### 5.16 STTP - `SttpController`

| Aspek | Rincian |
|---|---|
| Tabel | `sttpp`, `sttpp_detail`, `sttpp_fab`, `sttpp_fab_1`, `wi_trn_bpm`, `wi_finish` |
| Menu | Sttp / Part Sttp / Laporan Sttp / Sttp Dalprod / STTP PPCB / Sttp Fabrikasi |
| Fitur | Daftar STTP; input header + part; set pengirim/penerima; **draft `sttpp_fab_1` -> submit `move` ke `sttpp_fab`**; input batch (`add_data1`); edit; print surat |
| Controller | `SttpController` |
| Route | `resource /sttp`, `POST /sttp/move`, `GET /sttp/{id}/print` |
| View | `sttp/index.blade.php`, `sttp/form.blade.php`, `sttp/draft.blade.php`, `sttp/batch.blade.php`, `sttp/print.blade.php` |

### 5.17 STTP Dalmat - `SttpDalmatController`, `PiecepartController`, `NcrController`, `MdrController`

| Aspek | Rincian |
|---|---|
| Tabel | `sttp_dalmat`, `sttp_dalmat_datang`, `sttp_dalmat_datang_org`, `piecepart`, `piecepart_datang`, `ncr`, `sttpp_fab` |
| Menu | Sttp Kirim, Sttp Datang, Penyerahan Sttp, Piecepart Stock, NCR, MDR |
| Fitur | STTP kirim/datang; penyerahan (org); stock piecepart; NCR (join `md_header`); MDR |
| Controller | `SttpDalmatController`, `PiecepartController`, `NcrController`, `MdrController` |
| Route | `resource /sttp-dalmat`, `resource /piecepart`, `resource /ncr`, `resource /mdr` |
| View | `sttp_dalmat/*.blade.php`, `piecepart/index.blade.php`, `ncr/index.blade.php`, `mdr/index.blade.php` |

### 5.18 BPM / SPM - `BpmController`, `BpmImsController`, `SpmController`, `SpmImsController`, `SpmOrgController`, `SpmPpcbController`

| Aspek | Rincian |
|---|---|
| Tabel | `bpm`, `bpm_ims`, `spm`, `spm_ims`, `spm_org`, `spm_ims_org`, `spm_ppcb` |
| Menu | Bpm / Bpm INKA / Laporan Bpm, Spm / Spm INKA / Penyerahan Spm / Spm Wi |
| Fitur | CRUD BPM (stock); **input SPM menambah `bpm.stok` (transaction)**; penyerahan (org); print SPM (kop INKA); import |
| Controller | `BpmController`, `BpmImsController`, `SpmController`, `SpmImsController`, `SpmOrgController`, `SpmPpcbController` |
| Route | `resource /dalmat/bpm`, `resource /dalmat/bpm-ims`, `resource /dalmat/spm`, `resource /dalmat/spm-ims`, `resource /dalmat/spm-org`, `resource /dalmat/spm-ppcb` |
| View | `dalmat/bpm/index.blade.php`, `dalmat/spm/index.blade.php`, `dalmat/spm/form.blade.php`, `dalmat/spm/print.blade.php`, `dalmat/spm_org/index.blade.php` |

### 5.19 Gas - `GasController`

| Aspek | Rincian |
|---|---|
| Tabel | `gas` |
| Menu | Gas INKA, Report Gas INKA (operator4) |
| Fitur | Input pemakaian gas; laporan |
| Controller | `GasController` |
| Route | `resource /dalmat/gas`, `GET /dalmat/gas/report` |
| View | `dalmat/gas/index.blade.php`, `dalmat/gas/report.blade.php` |

### 5.20 Tool & Consumable - `ToolFabController`, `ToolFinController`, `ToolFabImsController`, `ToolFinImsController`

| Aspek | Rincian |
|---|---|
| Tabel | `tool_fab`, `tool_fin`, `tool_fab_ims`, `tool_fin_ims`, `con_fab_ims`, `con_fin_ims` |
| Menu | Tool Fabrikasi/Finishing (Inka & IMS), Consumable |
| Fitur | CRUD stock tool; cek duplikat `kode_alat`; **upload foto ke Storage**; import Excel |
| Controller | `ToolFabController`, `ToolFinController`, `ToolFabImsController`, `ToolFinImsController` |
| Route | `resource /tool/fabrikasi`, `resource /tool/finishing`, `resource /tool/fabrikasi-ims`, `resource /tool/finishing-ims` |
| View | `tool/fabrikasi/index.blade.php`, `tool/fabrikasi/form.blade.php`, `tool/finishing/index.blade.php`, `tool/ims/index.blade.php` |

### 5.21 Expedisi - `ExpedisiController`, `RenprodExpController`

| Aspek | Rincian |
|---|---|
| Tabel | `exp`, `exp_org`, `exp_cp` |
| Menu | Expedisi Dokumen Wi, Exp Wi ke WS, Exp Wi ke Dal, Terima Wi, Laporan Exp |
| Fitur | Daftar exp (filter tgl_kirim hari ini); input exp; input penerima (set `tgl_kembali`); import; export; laporan |
| Controller | `ExpedisiController`, `RenprodExpController` |
| Route | `resource /expedisi`, `GET /expedisi/input`, `GET /expedisi/penerima`, `resource /expedisi/renprod` |
| View | `expedisi/index.blade.php`, `expedisi/form.blade.php`, `expedisi/penerima.blade.php`, `expedisi/lap.blade.php` |

### 5.22 Pengadaan & Memo - `PengadaanController`, `MemoMasukController`

| Aspek | Rincian |
|---|---|
| Tabel | `memo`, `memo_masuk`, `memo_proses_masuk` |
| Menu | Status Pengadaan, Memo Masuk (umum) |
| Fitur | Daftar status pengadaan (search part/proyek/oka/title); edit status; export; memo masuk |
| Controller | `PengadaanController`, `MemoMasukController` |
| Route | `resource /pengadaan`, `resource /memo-masuk` |
| View | `pengadaan/index.blade.php`, `pengadaan/edit.blade.php`, `memo_masuk/index.blade.php` |

### 5.23 Dashboard & Grafik - `DashboardController`, `GrafikController`

| Aspek | Rincian |
|---|---|
| Tabel | `grafik_md`, `md_header`, `wi_trn`, `barcode`, `cp_1`, `exp` |
| Menu | Dashboard, Grafik (semua role) |
| Fitur | Pilih proyek -> KPI (jumlah MD/WI/CP/Barcode/Exp per proyek) + Chart.js |
| Controller | `DashboardController`, `GrafikController` |
| Route | `GET /`, `GET /dashboard`, `GET /grafik` |
| View | `dashboard/index.blade.php`, `dashboard/partials/_kpi.blade.php`, `dashboard/partials/_chart.blade.php`, `grafik/index.blade.php` |

---

## 6. Daftar Lengkap Tabel -> Model & Controller

| # | Tabel | Model | Primary Key (usulan) | Controller pemakai | Keterangan |
|---|---|---|---|---|---|
| 1 | `audit_log` | `AuditLog` | `id` | - | log aktivitas |
| 2 | `barcode` | `Barcode` | `id` | `BarcodeController`, `WiController` | data barcode part |
| 3 | `barcode_detail` | `BarcodeDetail` | `id` | `FabController` | detail barcode fabrikasi |
| 4 | `barcode_start` | `BarcodeStart` | `id` | `FabController`, `ProsesFabController` | barcode awal proses |
| 5 | `bobot_part` | `BobotPart` | `id` | - | bobot part |
| 6 | `bpm` | `Bpm` | `id` | `BpmController`, `SpmController` | stock BPM |
| 7 | `bpm_cp` | `BpmCp` | `id` | `BpmCpController` | hubungan BPM-CP |
| 8 | `bpm_ims` | `BpmIms` | `id` | `BpmImsController`, `LapBpm` | BPM INKA |
| 9 | `con_fab_ims` | `ConFabIms` | `id` | `ToolFabImsController` | consumable fabrikasi IMS |
| 10 | `con_fin_ims` | `ConFinIms` | `id` | `ToolFinImsController` | consumable finishing IMS |
| 11 | `cp` | `Cp` | `id` | `CpController` | check point |
| 12 | `cp_1` | `Cp1` | `id_cp` | `CpController`, `CpLapController` | check point (utama) |
| 13 | `ct_plant` | `CtPlant` | `id` | - | control plant |
| 14 | `drawing` | `Drawing` | `id` | - | gambar |
| 15 | `exp` | `Exp` | `id` | `ExpedisiController`, `SttpController` | expedisi dokumen |
| 16 | `exp_cp` | `ExpCp` | `id` | `ExpCpController`, `RenprodExpController` | expedisi CP |
| 17 | `exp_org` | `ExpOrg` | `id` | `ExpedisiController` | expedisi (penerimaan) |
| 18 | `gas` | `Gas` | `id` | `GasController` | pemakaian gas |
| 19 | `grafik_md` | `GrafikMd` | `id` | `DashboardController`, `GrafikController` | data grafik MD |
| 20 | `ims_bpm` | `ImsBpm` | `id` | - | BPM IMS referensi |
| 21 | `ims_spm` | `ImsSpm` | `id` | - | SPM IMS referensi |
| 22 | `jadwal` | `Jadwal` | `id` | `MdJadwalController` | jadwal MD |
| 23 | `jenis_kereta_tb` | `JenisKeretaTb` | `id` | - | referensi jenis kereta |
| 24 | `lap_wi` | `LapWi` | `id` | `WiLapController`, `RencanaWiController` | laporan WI |
| 25 | `md_gambar` | `MdGambar` | `id` | - | gambar MD |
| 26 | `md_header` | `MdHeader` | `id_md` | `MdController`, `MdLapController`, `LapMdController`, `ChecksheetController`, `NcrController` | master MD |
| 27 | `mdr` | `Mdr` | `id` | `MdrController` | manufacturing data record |
| 28 | `memo` | `Memo` | `id` | `PengadaanController`, `RencanaWiController` | memo pengadaan |
| 29 | `memo_masuk` | `MemoMasuk` | `id` | `MemoMasukController` | memo masuk |
| 30 | `memo_proses_masuk` | `MemoProsesMasuk` | `id` | `MemoMasukController` | proses memo masuk |
| 31 | `mesin` | `Mesin` | `id` | - | master mesin |
| 32 | `ncr` | `Ncr` | `id` | `NcrController` | non-conformance report |
| 33 | `notifications` | `Notification` | `id` | - | notifikasi |
| 34 | `oka` | `Oka` | `oka` | `OkaController`, `DashboardController`, `WiPrintController`, `ExpedisiController` | master proyek |
| 35 | `piecepart` | `Piecepart` | `id` | `PiecepartController`, `NcrController`, `SttpDalmatController` | stock piecepart |
| 36 | `piecepart_datang` | `PiecepartDatang` | `id` | `PiecepartController` | piecepart datang |
| 37 | `pipt` | `Pipt` | `id` | `PiPtController` | PI/PT |
| 38 | `proses` | `Proses` | `id` | `ProsesController` | kode proses |
| 39 | `proyek` | `Proyek` | `id` | - | master proyek (referensi) |
| 40 | `self_check` | `SelfCheck` | `id_self_check` | `SelfCheckController` | self check produksi |
| 41 | `spm` | `Spm` | `id` | `SpmController`, `SpmOrgController` | surat permintaan material |
| 42 | `spm_ims` | `SpmIms` | `id` | `SpmImsController` | SPM INKA |
| 43 | `spm_ims_org` | `SpmImsOrg` | `id` | `SpmOrgController` | penyerahan SPM IMS |
| 44 | `spm_org` | `SpmOrg` | `id` | `SpmOrgController` | penyerahan SPM |
| 45 | `spm_ppcb` | `SpmPpcb` | `id` | `SpmPpcbController` | SPM Wi (PPCB) |
| 46 | `sttp_dalmat` | `SttpDalmat` | `id` | `SttpDalmatController` | STTP piecepart kirim |
| 47 | `sttp_dalmat_datang` | `SttpDalmatDatang` | `id` | `SttpDalmatController` | STTP dalmat datang |
| 48 | `sttp_dalmat_datang_org` | `SttpDalmatDatangOrg` | `id` | `SttpDalmatController` | penyerahan STTP datang |
| 49 | `sttpp` | `Sttpp` | `id` | `SttpController` | STTP header |
| 50 | `sttpp_fab` | `SttppFab` | `id_sttp_fab` | `SttpController`, `MdrController` | STTP part (final) |
| 51 | `sttpp_fab_1` | `SttppFabDraft` | `id_sttp_fab_1` | `SttpController` | **draft STTP (perlu dibuat)** |
| 52 | `tool_fab` | `ToolFab` | `id` | `ToolFabController` | stock tool fabrikasi |
| 53 | `tool_fab_ims` | `ToolFabIms` | `id` | `ToolFabImsController` | tool fabrikasi IMS |
| 54 | `tool_fin` | `ToolFin` | `id` | `ToolFinController` | stock tool finishing |
| 55 | `tool_fin_ims` | `ToolFinIms` | `id` | `ToolFinImsController`, `ConFinIms` | tool finishing IMS |
| 56 | `user` | `User` | `kode_user` | `UserController`, Auth | akun pengguna |
| 57 | `userb` | `Userb` | `id` | - | varian user |
| 58 | `users` | `Users` | `id` | - | varian user |
| 59 | `wi_finish` | `WiFinish` | `id` | `SttpController` | WI finish |
| 60 | `wi_trn` | `WiTrn` | `id` | `WiController`, `CpController`, `BpmCpController`, `ChecksheetController`, `WiMatController`, `WiLapController`, `ProsesStartController`, `LapProsesWsController` | **transaksi WI (utama)** |
| +1 | `wi_trn_bpm` | `WiTrnBpm` | `id_wi_trn_bpm` | `KodeMaterialController`, `SttpController` | kode material BPM |

### 6.1 Index yang Disarankan (migration)

Kolom yang sering dipakai `WHERE`/`JOIN`/`LIKE` di query lama -> wajib di-index:

- `barcode`: `no_konv`, `part_no`, `oka`, `PR(no_ctplan)`
- `wi_trn`: `no_konv`, `part_no`, `oka`, `lot`, `proyek`, `no_ctplan`, `no_bpm`, `idbarang`, `tgl_trn`
- `md_header`: `part_no`, `proyek`, `oka` (untuk JOIN besar `ncr`, `lap_md`)
- `cp_1`: `cp_no`, `part_no`
- `bpm_cp`: `no_cp`
- `exp`: `no_exp`, `no_cp`, `oka`, `tgl_kirim`
- `sttpp_fab`: `no_sttp`, `no_part`
- `spm`: `no_spm`, `no_cp`, `no_bpm`
- `bpm`: `no_bpm`, `oka`
- `tool_*`: `kode_alat`
- `user`: `nama_user` (login)

---

## 7. Struktur View (Blade) Target

```
resources/views/
├── layouts/
│   └── app.blade.php            # layout utama (header, sidebar, footer, @yield('content'))
├── partials/
│   ├── sidebar.blade.php        # menu per role (@if Auth::user()->level ...)
│   ├── table-actions.blade.php  # tombol edit/hapus/print/export
│   ├── import-form.blade.php    # form upload Excel reusable
│   └── export-form.blade.php    # tombol export (POST)
├── auth/
│   ├── login.blade.php
│   └── logout (route)
├── dashboard/
│   ├── index.blade.php
│   └── partials/_kpi.blade.php, _chart.blade.php
├── grafik/index.blade.php
├── master/
│   ├── oka/index.blade.php, form.blade.php
│   ├── proses/index.blade.php, form.blade.php
│   └── kode-material/index.blade.php, form.blade.php
├── user/index.blade.php, form.blade.php
├── md/ index.blade.php, form.blade.php, batch.blade.php, import.blade.php
├── md_lap/index.blade.php
├── md_jadwal/index.blade.php
├── wi/ index.blade.php, create.blade.php, create-from-barcode.blade.php, bpm.blade.php, batch.blade.php, import.blade.php, grafik.blade.php, print.blade.php
├── wi_mat/index.blade.php
├── barcode/ index.blade.php, form.blade.php, batch.blade.php, import.blade.php, grafik.blade.php
├── cp/ index.blade.php, form.blade.php, lap.blade.php
├── bpm_cp/ index.blade.php, batch.blade.php
├── checksheet/index.blade.php
├── fabrikasi/ index.blade.php
├── self_check/ index.blade.php, form.blade.php
├── proses_fab/ index.blade.php
├── sttp/ index.blade.php, form.blade.php, draft.blade.php, batch.blade.php, print.blade.php
├── sttp_dalmat/ kirim.blade.php, datang.blade.php, penyerahan.blade.php
├── piecepart/index.blade.php
├── ncr/index.blade.php
├── mdr/index.blade.php
├── dalmat/
│   ├── bpm/index.blade.php, form.blade.php
│   ├── spm/index.blade.php, form.blade.php, print.blade.php
│   ├── spm_org/index.blade.php
│   └── gas/index.blade.php, report.blade.php
├── tool/
│   ├── fabrikasi/index.blade.php, form.blade.php
│   ├── finishing/index.blade.php, form.blade.php
│   └── ims/index.blade.php
├── expedisi/ index.blade.php, form.blade.php, penerima.blade.php, lap.blade.php
├── pengadaan/ index.blade.php, edit.blade.php
├── memo_masuk/index.blade.php
└── lap/ md.blade.php, wi.blade.php, wi-all.blade.php, cp.blade.php, proses-start.blade.php, proses-ws.blade.php
```

---

## 8. Catatan Implementasi

1. **Tabel `sttpp_fab_1` (draft STTP)** harus dibuat terlebih dahulu (kolom sama dengan `sttpp_fab` + `user_sttp`) - fitur draft->submit di modul STTP.
2. **Jangan bawa bug lama**: handler `add` modul `kode_proyek`/`proses`/`kode` yang salah INSERT ke `wi_trn_bpm` diperbaiki di controller baru.
3. **Export Excel** tidak lagi menerima raw SQL dari form; setiap `export()` controller menjalankan query sendiri.
4. **Transaksi wajib**: `SpmController@store` (Spm::create + Bpm::increment) dan `SttpController@moveDraft` (insert + delete draft) dibungkus `DB::transaction()`.
5. **Peran & otorisasi** di-enforce di route middleware, bukan hanya di sidebar - menu yang tersembunyi tidak cukup.
6. **Data tetap** di database `barcode`; migration bersifat idempotent (hanya menambah index/PK, tidak drop tabel berisi data).

---

*Dokumen ini adalah rancangan implementasi; sesuaikan penamaan controller/route/view saat pengembangan berlangsung.*



