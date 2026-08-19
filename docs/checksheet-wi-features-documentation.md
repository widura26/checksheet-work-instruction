# Aplikasi Checksheet Wi Renprod — Dokumentasi Fitur, Menu & Alur

> Divisi: **Perencanaan Produksi** — Support: 2115
> Lokasi kode: `D:\laragon\www\sourcecode\renprod.1`
> Database: `barcode` (60 tabel)

---

## 1. Ringkasan Sistem

Aplikasi "Checksheet Wi Renprod" adalah sistem produksi/perencanaan untuk mengelola alur kerja
**MD (Master Data) → WI (Work Instruction) → Barcode → CP (Check Point) → Expedisi → STTP/Dalmat → Proses Fabrikasi → Laporan**,
termasuk manajemen stock material (BPM/SPM), tool (fabrikasi/finishing), pengadaan, dan NCR.

Struktur aplikasi berbasis modul: setiap modul adalah folder berisi serangkaian halaman PHP dengan pola baku
(`data.php`, `add.php`, `edit.php`, `del.php`, `import.php`, `proses.php`, `export.php`, `pdf.php`, `grafik.php`, `pagination1.php`).

---

## 2. Teknologi & Arsitektur

| Aspek | Keterangan |
|---|---|
| Bahasa | PHP (sekarang kompatibel PHP 8.2; awalnya PHP 5) |
| Database | MySQL 8 (`barcode`), koneksi `mysqli_connect('127.0.0.1','root','','barcode')` per halaman atau `$con` dari `_config/config.php` |
| Library | Bootstrap 3, jQuery 1.11, DataTables, Select2, Chart.js, **PHPExcel 1.8.2** (import/export Excel), **FPDF** (print), **Ramsey UUID** |
| Entry | `index.php` → redirect ke `auth/login.php` (jika belum login) atau `dashboard/index.php` |
| Auth | `_header.php:5` memeriksa `$_SESSION['level']`; `$_SESSION['kode_user']` → nama user dari tabel `user` |
| Konfigurasi | `_config/conn.php` (koneksi DB), `_config/config.php` (session, helper `base_url`) |

> Catatan pengembangan: saat ini ada *bypass login* (`_config/config.php`) yang otomatis masuk sebagai
> `level=admin`, `kode_user=51` (Gilang) — ditandai komentar `===== BYPASS LOGIN =====`.

---

## 3. Login & Peran (Level)

Login (`auth/login.php`) membandingkan `username`+`password` (di-hash `sha1`) dengan tabel `user`.
Logout (`auth/logout.php`) menghapus session.

Level yang dikenal (dari `_header.php`):

| Level | Peran | Menu yang terlihat |
|---|---|---|
| `operator` | Operator MD/WI/CP | Md, Jadwal, Cp, Wi 1, Barcode, Barcode B |
| `admin` | Admin produksi | Md, Cp/Bpm/Exp Cp, Grafik, Wi 3, Print Wi 2, Kode Mat, Status Mat, Barcode 3, Print Wi UGL |
| `operator1` | Admin sistem | User, Edit Proyek, Grafik, Bpm Cp |
| `operator2` | (dipakai default menu) | Md, Wi, Cp, Bpm, Data Material, Dalmat, Tool |
| `operator3` | PPCB | Sttp2 (Input Sttp Part, Laporan), Spm, MDR, Proses Fab, Input Expedisi |
| `operator4` | Dalmat/INKA | Kode Mat, Md, Wi, Cp, Bpm, Sttp Dalmat, Dalmat, Dalmat INKA, Gas, Tool Inka, Tool Ims |
| `operator5` | Proses fabrikasi | Md, Wi, Proses Fab (Self Check, Proses Fab, Lap Proses Fab) |
| lainnya | Umum | Md, Wi, Cp, Bpm, Data Material, Dalmat, Tool, Sttp |

Menu umum untuk semua role: **Dashboard, Kode Proyek, Proses, Expedisi (Exp Wi ke WS, Exp Wi ke Dal), Laporan (Lap Wi, Lap Cp, Lap Wi-Spm, Lap Bpm), Logout**.

---

## 4. Fitur Standar Setiap Modul

Hampir semua modul data memiliki kemampuan berikut (pola baku):

| Fitur | File | Keterangan |
|---|---|---|
| Daftar + pencarian + paginasi | `data.php` | Tabel HTML, cari via `keyword`, paginasi 20–100 baris (`pagination.php` / `pagination1.php`) |
| Tambah | `add.php`, `add1.php`, `add2.php`, `add3.php`, `add4.php` | Form input; beberapa modul punya beberapa tahap input |
| Edit | `edit.php`, `edit1.php` | Update per baris atau batch (loop array `$_POST['id']`) |
| Hapus | `del.php` | Hapus per baris / massal (checkbox select-all) |
| Import Excel | `import.php` + `proses.php` | Upload `.xlsx` via PHPExcel → INSERT batch (cegah duplikat) |
| Export Excel | `export.php` | POST `sql` → PHPExcel (format BIFF `.xls`) |
| Print / PDF | `pdf.php` | Tampilan HTML siap-print (`window.print()`); sebagian punya fpdf |
| Grafik | `grafik.php` | Chart.js per modul |
| Pemrosesan | `proses.php` | Menerima semua aksi POST (add/add1/edit/import/move) |

Alur standar:
1. `data.php` menampilkan daftar; tombol aksi → `add.php` / `edit.php` / `del.php`.
2. Form mengirim ke `proses.php` dengan nama tombol (`add`, `add1`, `edit`, `import`, dst).
3. `proses.php` menjalankan query (INSERT/UPDATE/import) lalu redirect kembali ke `data.php`.

---

## 5. Peta Menu Lengkap per Peran

### 5.1 Menu `admin` (level default saat ini)
- Dashboard
- **Md** → Md (`md2`), Cp (`cp_bpm`), Bpm (`bpm_cp_1`), Exp Cp (`renprod_exp`)
- Grafik
- **Wi** → Wi 1 (`wi_3`), Print Wi 2 (`wi_51`)
- **Data Material** → Kode Mat (`kode`), Status Mat (`wi_mat`)
- **Barcode** → Barcode (`barcode_3`), Print Wi UGL (`wi_5`)

### 5.2 Menu `operator`
- Dashboard, Kode Proyek, Proses
- **Md** → Md (`md5`), Md Lap (`md_lap`)
- **Jadwal** → Jadwal (`md_jadwal`)
- Cp (`cp_bpm`)
- **Wi** → Wi 1 (`wi_1`)
- Barcode (`barcode`), Barcode B (`barcode_a`)

### 5.3 Menu `operator1` (admin sistem)
- User (`user`), Edit Proyek (`kode_proyek_1`), Grafik, Bpm Cp (`bpm_cp_admin`)

### 5.4 Menu `operator3` (PPCB)
- **Sttp2** → Input Sttp Part (`sttp5`), Laporan Sttp (`sttp4`)
- **Spm** → Spm (`spm_ppcb`)
- **MDR** → MDR (`mdr`)
- **Proses Fab** → Self Check (`self_check`), Proses Fab (`proses_ppcb`), Lap Proses Fab (`proses_fab_lab`)
- **Input Expedisi** → Input Wi ke Fab (`exp`), Terima Wi (`renprod_exp2`)

### 5.5 Menu `operator4` (Dalmat/INKA)
- Kode Mat (`kode`), Md (`md5`), Wi (`wi_2`)
- **Cp** → Cp (`cp_bpm1`), Bpm (`bpm_cp`)
- **Sttp Dalmat** → Sttp Kirim (`sttp_dalmat`), Sttp Datang (`piecepart`, `sttp_dalmat_datang`, `sttp_datang_org`), NCR (`ncr`)
- **Dalmat** → Bpm (`dalmat_bpm`), Spm (`dalmat_spm`, `spm_org`)
- **Dalmat INKA** → Bpm INKA (`dalmat_bpm_ims`), Spm INKA (`dalmat_spm_ims`, `spm_ims_org`), Laporan Bpm INKA (`dalmat_bpm_ims1`)
- **Gas** → Gas INKA (`dalmat_gas`), Report Gas INKA (`dalmat_gas_report`)
- **Tool Inka** → Tool Fabrikasi (`tool_fab`), Tool Finishing (`tool_fin`)
- **Tool Ims** → Tool Fabrikasi IMS (`tool_fab_ims`), Tool Finishing IMS (`tool_fin_ims`)

### 5.6 Menu `operator5` (proses fabrikasi)
- Md (`md5`), Wi (`wi_2`)
- **Proses Fab** → Self Check (`self_check`), Proses Fab (`proses_ppcb`), Lap Proses Fab (`proses_fab_lab`)

### 5.7 Menu default (role lain)
- Md (`md5`), Wi (`wi_2`), Cp (`cp_bpm1`), Bpm (`bpm_cp_1`)
- **Data Material** → Kode Mat (`kode1`), Status Mat (`wi_mat1`)
- **Dalmat** → Bpm (`dalmat_bpm1`), Spm (`dalmat_spm1`)
- **Tool** → Tool Fabrikasi (`tool_fab1`), Tool Finishing (`tool_fin1`)

### 5.8 Menu umum (semua role)
- **Sttp** → Sttp (`sttp51`) [operator3: Sttp2; role lain: Sttp]
- **Expedisi** → Exp Wi ke WS (`exp1`), Exp Wi ke Dal (`renprod_exp1`)
- **Laporan** → Lap Wi (`wi_lap`), Lap Cp (`checksheet`), Lap Wi-Spm (`wi_2a`), Lap Bpm (`lap_bpm`)

---

## 6. Dokumentasi Menu & Alur (Detail per Area Bisnis)

Legend aksi: `+` tambah, `~` edit, `-` hapus, `↥` import Excel, `⇩` export Excel, `🖨` print/pdf, `📈` grafik.

### 6.1 Master Data / Referensi

#### Kode Proyek — `kode_proyek` / `kode_proyek_1`
- **Fitur**: daftar proyek (tabel `oka`), edit label proyek & tampil grafik (`~`), import Excel (`↥`).
- **Tabel**: `oka`
- **Alur**: `data.php` (daftar `oka` dengan paginasi) → `edit.php` → `proses.php` (`edit` UPDATE `oka.label_proyek`, `oka.tampil_g`). `kode_proyek_1` = versi "Edit Proyek" khusus operator1.
- ⚠️ Anomali: `add.php` dan handler `add`/`import` di `proses.php` adalah *template copy-paste* — form menambah ke `wi_trn_bpm` (bukan `oka`), tidak konsisten dengan halaman data.

#### Proses (Kode Proses) — `proses`
- **Fitur**: daftar kode proses (`proses`), edit, import.
- **Tabel**: `proses`, `memo` (handler edit), `wi_trn_bpm` (handler add/import — anomali template)
- **Alur**: daftar `proses` → edit (`~` UPDATE `memo`).

#### Kode Material — `kode` / `kode1`
- **Fitur**: daftar & tambah kode material + spesifikasi, import.
- **Tabel**: `wi_trn_bpm`
- **Alur**: form `add.php` (idbarang + spec) → `proses.php` (`add` INSERT `wi_trn_bpm` dengan UUID).

#### User — `user`
- **Fitur**: manajemen akun (daftar, tambah, edit, hapus, reset password), import.
- **Tabel**: `user`
- **Alur**: daftar `user` → `add.php`/`edit.php`/`del.php` → `proses.php`.

### 6.2 MD (Master Data / Checksheet MD)

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `md`, `md2` | Daftar MD, add, edit, import | `md_header` | input/import part master MD |
| `md3` (Masterpart), `md4` (Md 2) | Daftar MD versi masterpart | `md` | seperti MD |
| `md5` (Md) | Daftar MD, add, edit batch, import | `md_header` | `proses.php` `add` gagal? (anomali: `$no_wi` tidak terdefinisi), `edit1` UPDATE `md_header` (field part, title, material, ukuran t/h/b/l, shop/ws, des, rev, maspart), `edit` set `mdh_md2 = part_no + proyek`, `import` INSERT batch `md_header` dari Excel (kolom A–AA) |
| `md_lap` (Checksheet Md / Md Lap) | Daftar MD lap, import | `md_lap` | join ke `wi_trn` lewat `no_part1/oka1/lot1` |
| `md_jadwal` (Jadwal / Checksheet Md) | Daftar jadwal MD | `jadwal`, `md_lap` | `data.php`: tanpa keyword tampil `jadwal` (ORDER BY group_jadwal); dengan keyword join `md_lap`↔`wi_trn` |
| `lap_md` (Laporan Md) | Laporan MD + export | `md_header` | filter part `'030F12%'` → paginasi → `export.php` |

**Alur MD**: master part (part_no, title, material, ukuran T/H/B/L, nama_group, shop1/2, ws1–ws8, des, rev, maspart) dimasukkan/diimport ke `md_header`. Data ini dipakai sebagai referensi WI, CP, dan checksheet.

### 6.3 WI (Work Instruction)

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `wi_1` (Wi 1) | Daftar WI, add (dari barcode), add1 (no_bpm), edit batch, import, export, grafik, generate | `wi_trn`, `barcode` | lihat alur di bawah |
| `wi`, `wi_2`, `wi_2a`, `wi_4`, `wi_6` | Daftar WI | `wi_trn` | seperti Wi umum |
| `wi_3` (Wi 1) | Daftar WI + pdf | `wi_trn` | |
| `wi_5` (Print Wi UGL) | **Print WI UGL** (pdf/print) | `wi_trn`, `oka` | `pdf.php` membaca `wi_trn` by `id`, join `oka`, menghitung total lot berdasarkan `jenis_kereta` (1=total_lot, 2=total_lot1, 3=total_lot2) |
| `wi_51` (Print Wi 2) | Print Wi | `wi_trn` | seperti Wi 5 |
| `wi_mat`, `wi_mat1` (Status Raw Material) | Status material WI | `wi_trn` | daftar WI sebagai status material |
| `wi_lap`, `wi_lap1`, `lap_wi`, `lap_wi_all` (Lap Wi) | Laporan WI + export | `wi_trn`, `lap_wi`, `md` | laporan/tracking WI |
| `rencana_wi` (Rencana WI) | Rencana WI turun | `lap_wi` | |

**Alur WI (modul `wi_1`)**:
1. `add.php`: user mencari **no konversi** di tabel `barcode` (join `oka`); form memakai data barcode (part_no, oka, lot, proyek, wbs) + input qty_lot, no_ctplan, jenis_kereta, status → submit `add` → `proses.php` INSERT ke `wi_trn` (cegah no_konv duplikat).
2. `add1.php`: input no_bpm, idbarang, tgl_bpm, ket → UPDATE `wi_trn` (no_bpm dll).
3. `edit1.php`: edit batch → `proses.php` `edit1` menghitung **target_wi = tgl_ctplan + 1 hari** dan set `konv_wi`.
4. `import.php` → `proses.php` `import`: upload Excel → INSERT batch ke `wi_trn` (kolom A–R, mulai baris 4; cek duplikat part_no+no_ctplan).
5. `export.php`: export `wi_trn` ke Excel.
6. `grafik.php`: Chart.js per proyek.

### 6.4 Barcode

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `barcode`, `barcode_1`, `barcode_2`, `barcode_3`, `barcode_a` (Barcode / Barcode B) | Daftar barcode, add (scan), edit, hapus massal, import, export, grafik | `barcode` (≈569rb baris) | |

**Alur barcode** (`barcode`): `data.php` daftar barcode per part/proyek → `add.php` (input part_no, no_konv, wbs, oka, lot, status, bil, tgl, no_ctplan) → `proses.php` `add` INSERT `barcode` (cegah duplikat no_konv); `edit1` batch update; `import` Excel (kolom A–I, `tgl` = hari ini) → INSERT batch. Data barcode menjadi sumber pembuatan WI (`wi_1/add.php`).

### 6.5 CP (Check Point) & BPM

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `cp` / `cp1` (Cp) | Daftar CP, add, import | `cp_1` / `cp` | |
| `cp_bpm`, `cp_bpm1` (Cp) | Daftar CP–BPM | `wi_trn` | daftar WI sebagai CP |
| `bpm_cp`, `bpm_cp_1`, `bpm_cp_admin` (Bpm) | Daftar CP/BPM + add (no_cp/no_bpm), import | `bpm_cp`, `wi_trn` | `proses.php` `add1` INSERT `bpm_cp`; `edit` UPDATE `wi_trn` (no_exp, mat_cp, spek_mat_cp, mat_sisa, qty_car, qty_cp, dst) **dan** `bpm_cp` (mat_cp, mat_sisa, qty_cp); `import` INSERT `cp_1` |
| `cp_lap`, `bpm_lap` (Laporan Cp / Bpm) | Laporan CP/BPM | `cp_1` | |
| `exp_cp` (Exp Cp), `renprod_exp` (Exp Wi ke Dal) | Expedisi CP → Dal | `exp_cp` | |
| `lap_bpm` (Lap Bpm Ims) | Laporan BPM | `bpm_ims` | |

**Alur CP/BPM**: data WI dijadikan dasar CP (`cp_bpm` menampilkan `wi_trn`), operator menambahkan no_cp/no_bpm ke `bpm_cp`, lalu melengkapi kuantitas material CP (qty_car, qty_lot, qty_cp, mat_cp/spek/mat_sisa) yang sinkron ke `wi_trn` dan `bpm_cp`.

### 6.6 Checksheet

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `checksheet` (Lap Cp) | Laporan checksheet CP | `wi_trn` | |
| `checksheet_1`, `checksheet_2`, `checksheet_21` (Checksheet Wi) | Checksheet WI | `md_header` / `wi_trn` | |

### 6.7 Proses Fabrikasi

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `fab`, `fab1` (Proses Fabrikasi) | Daftar proses fabrikasi | `barcode_start` / `barcode_detail` | |
| `proses_ppcb` (Proses Fabrikasi) | Proses fab PPCB | `barcode_start` | |
| `proses_fab_lab` (Laporan Proses Fabrikasi) | Laporan | `barcode_start` | |
| `self_check` (SELF CHECK) | Input self check produksi | `self_check`, `exp` (anomali import) | `proses.php` `add` INSERT `self_check` (no_part, nama, rev, oka, lot, qty_lot, qty, tgl_kirim); `edit` batch UPDATE `self_check` |
| `proses_start` (Laporan Proses Start) | Laporan | `wi_trn` | |
| `lap_proses_ws` (Laporan Proses Ws) | Laporan | `wi_trn` | |

### 6.8 Dalmat & STTP (Material Supply / Penerimaan)

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `sttp` / `sttp1` / `sttp1a` (Sttp) | Daftar & input STTP | `sttpp` / `sttpp_detail` | |
| `sttp2`, `sttp2a` (Part Sttp) | Input part STTP | `sttpp_detail` | |
| `sttp3` (Laporan Sttp) | Laporan | `sttpp_detail` | |
| `sttp4`, `sttp4a` (Laporan Sttp Dalprod) | Laporan STTP dalprod | `sttpp_fab` | |
| `sttp5`, `sttp51` (STTP PPCB) | **Input STTP part** | `sttpp_fab`, `sttpp_fab_1` | lihat alur di bawah |
| `sttp_fab` (Sttp Fabrikasi) | STTP fabrikasi | `wi_finish` | |
| `sttp_dalmat` (Sttp Piece Part Kirim) | STTP kirim | `sttp_dalmat` | + pdf |
| `sttp_dalmat_datang` (Sttp Dalmat ke Produksi) | STTP datang | `sttp_dalmat_datang` | + pdf |
| `sttp_datang_org` (Penyerahan Sttp Datang) | Penyerahan STTP | `sttp_dalmat_datang_org` | + pdf |
| `piecepart` (Piece Part Stock) | Stock part | `piecepart` | |
| `ncr` (NCR / Piece Part Stock) | NCR part | `piecepart` (join `md_header`) | query join besar — lambat tanpa index |
| `mdr` (Manufacturing Data Record) | MDR | `sttpp_fab` | |

**Alur STTP PPCB (`sttp5`)**:
1. `data.php` daftar STTP (`sttpp_fab`).
2. `add.php`: tambah kode material (`wi_trn_bpm`).
3. `add1.php`: set `penerima` & `pengirim` pada `sttpp_fab` (UPDATE).
4. `add3.php` / `add4.php`: input part STTP → INSERT ke **`sttpp_fab_1`** (tabel draft per user).
5. Tombol "submit/move" (`move`/`move1`) → **INSERT ke `sttpp_fab` lalu DELETE dari `sttpp_fab_1`** (submit draft ke tabel final).
6. `data1.php` → `add_data1`: input langsung batch ke `sttpp_fab`.
7. `pdf.php`: print surat STTP (HTML `window.print()`).

> ⚠️ Tabel `sttpp_fab_1` **tidak ada** di database hasil rekonstruksi (tidak ada baris di dump data) — modul sttp5 tahap draft (add3/add4 → move) akan error "table doesn't exist". Tabel perlu dibuat jika fitur ini dipakai.

### 6.9 Dalmat — BPM / SPM (Permintaan Material)

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `dalmat_bpm`, `dalmat_bpm1` (BPM) | Daftar & input BPM | `bpm` | |
| `dalmat_bpm_ims`, `dalmat_bpm_ims1`, `dalmat_bpm_ims_lap`, `lap_bpm` (Bpm INKA / Laporan) | BPM INKA & laporan | `bpm_ims` | |
| `dalmat_spm`, `dalmat_spm1` (SPM) | Daftar & input SPM + print | `spm`, `bpm` | lihat alur |
| `dalmat_spm_ims` (SPM INKA) | SPM INKA | `spm_ims` | |
| `spm_org`, `spm_ims_org` (Penyerahan SPM) | Penyerahan SPM | `spm_org`, `spm_ims_org` | + pdf |
| `spm_ppcb` (Spm Wi) | SPM Wi (PPCB) | `spm_ppcb` | + pdf |

**Alur SPM (`dalmat_spm`)**: `data.php` daftar BPM (`bpm`) → form `add` diisi no_bpm, kode_mat, spec, jumlah, no_cp, no_spm, jml_spm → `proses.php` `add` melakukan:
1. **INSERT ke `spm`** (record permintaan material).
2. **UPDATE `bpm` SET `stok = stok + jml_spm`** (penambahan stok dari penerimaan SPM).

`pdf.php` mencetak **Surat Permintaan Material (SPM Raw Material)** — HTML siap print dengan kop INKA.

### 6.10 Gas

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `dalmat_gas` (Gas INKA) | Input pemakaian gas | `gas` | |
| `dalmat_gas_report` (Report Gas Ims) | Laporan gas | `gas` | |

### 6.11 Tool & Consumable

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `tool_fab`, `tool_fab1` (Stock Tool Central Fabrikasi) | Stock tool fabrikasi (CRUD + foto + import) | `tool_fab` | `add` INSERT `tool_fab` (nama, spesifikasi, kode_alat, kode_mesin, jumlah, satuan, kondisi, lokasi, no_rak, foto) + upload foto ke `images2/`; cek duplikat kode_alat; `import` Excel |
| `tool_fin`, `tool_fin1` (Stock Tool Central Finishing) | Stock tool finishing | `tool_fin` | sama pola tool_fab |
| `tool_fab_ims` (Tool Fabrikasi IMS) | Stock tool IMS | `tool_fab_ims` | |
| `tool_fin_ims` (Stock Tool Finishing IMS) | Stock tool IMS | `tool_fin_ims` | |
| `con_fab_ims` (Consumable Fabrikasi IMS) | Stock consumable | `con_fab_ims` | |
| `con_fin_ims` (Stock Tool Finishing IMS) | Stock consumable finishing | `tool_fin_ims` | |

### 6.12 Expedisi (Pengiriman Dokumen / Part)

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `exp` (Expedisi Dokumen Wi) | Daftar expedisi hari ini, input exp Wi (`data1.php`), input penerima (`data2.php`), import (`import1.php`) | `exp` | daftar `exp` JOIN `oka`, filter tgl_kirim hari ini |
| `exp1` (Expedisi Dokumen Wi) | Daftar & edit exp | `exp` | `edit` UPDATE `exp` (no_exp, no_cp, oka, tgl_kirim, tgl_kembali, pengirim, penerima, ket_exp); `import` |
| `exp2` (Laporan Expedisi) | Laporan | `wi_trn` | |
| `renprod_exp`, `renprod_exp1`, `renprod_exp2` (Exp Wi ke Dal Fab/Fin) | Expedisi WI ke Dalmat | `exp_cp` | `renprod_exp2` = "Terima Wi" (penerimaan) |

**Alur expedisi**: dokumen WI dikirim (input `no_exp`, no_cp, oka, tgl_kirim, pengirim, tujuan → `exp`); saat diterima, `tgl_kembali` & `penerima` diisi (handler `edit_data2` di `sttp5` juga meng-update `exp`).

### 6.13 Pengadaan & Memo

| Menu | Fitur | Tabel | Alur |
|---|---|---|---|
| `pengadaan`, `pengadaan1` (Status Pengadaan) | Daftar status pengadaan + export | `memo` | filter part `'211F11%'`, search part_no/proyek/oka/title |
| `memo_masuk` (Memo Masuk) | Daftar memo masuk | `memo_masuk`, `memo_proses_masuk` | |
| `rencana_wi` (Status Pengadaan & Rencana Wi Turun) | Rencana WI | `lap_wi`, `memo` | |

### 6.14 MDR (Manufacturing Data Record)

- `mdr` — daftar & input MDR berbasis `sttpp_fab`.

### 6.15 Dashboard & Grafik

- **Dashboard** (`dashboard/index.php` dkk): pilih proyek (`grafik_md`) → tampil **KPI** (Jumlah MD per proyek, dan metrik lain per variasi index1–17) + grafik Chart.js.
- **Grafik** (`grafik/`): grafik MD per proyek dari `grafik_md`.

### 6.16 Modul lain / tidak termuat di menu

| Modul | Judul | Tabel | Catatan |
|---|---|---|---|
| `barcode_1`, `barcode_2` | Barcode | `barcode` | varian modul barcode |
| `bpm_lap` | Laporan Bpm | `cp_1` | varian laporan |
| `pi_pt`, `pi_pt1` | Pi Pt | `pipt` | tidak ada di menu saat ini |
| `pasien`, `pasienb` | laporan / obat | — / `wi_trn` | modul eksperimen (tidak dipakai) |
| `status` | — | — | kosong |
| `grafik` | Grafik | `grafik_md` | |

---

## 7. Alur Bisnis Utama (End-to-End)

```
1. Kode Proyek (oka)  ── master proyek
        │
2. Kode Proses (proses) ── master proses
        │
3. MD / Master Data (md_header)
   - import/add master part (part_no, material, ukuran, ws/shop, des, rev)
        │
4. WI (wi_trn)
   - barcode part masuk → buat WI (no_konv, no_ctplan, lot, qty_lot, jenis kereta, status)
   - set no_bpm, target_wi = tgl_ctplan + 1 hari
        │
5. Barcode (barcode) → Print Wi (wi_5/wi_51)
        │
6. CP / BPM (cp_1, bpm_cp, wi_trn)
   - WI dijadikan CP, lengkapi material CP (qty_cp, mat_cp, mat_sisa), hubungkan no_bpm
        │
7. Expedisi (exp / exp_cp)
   - kirim dokumen WI/CP ke workshop (no_exp, tgl_kirim, pengirim) → terima (tgl_kembali, penerima)
        │
8. Proses Fabrikasi (barcode_start, self_check, proses_ppcb)
   - part diproses fabrikasi, self check produksi
        │
9. Material: BPM → SPM (bpm, spm, spm_org) ; STTP (sttpp, sttpp_fab, sttp_dalmat)
   - permintaan & penerimaan material Dalmat (stock bertambah via SPM)
        │
10. Tool & Gas (tool_fab/fin, gas)  ── support produksi
        │
11. Laporan & Grafik (lap_wi, checksheet, lap_bpm, lap_md, grafik_md)
```

---

## 8. Daftar 60 Tabel Database `barcode`

| Tabel | Dipakai oleh modul | Keterangan |
|---|---|---|
| `audit_log` | — | log aktivitas (kosong) |
| `barcode` | barcode, barcode_1/2/3, barcode_a, wi_1 | data barcode part (≈569rb baris) |
| `barcode_detail` | fab1 | detail barcode fabrikasi |
| `barcode_start` | fab, proses_ppcb, proses_fab_lab | barcode awal proses |
| `bobot_part` | (referensi) | bobot part |
| `bpm` | dalmat_bpm, dalmat_spm | bahan permintaan material (stock) |
| `bpm_cp` | bpm_cp, bpm_cp_1, cp_bpm | hubungan BPM–CP |
| `bpm_ims` | dalmat_bpm_ims, lap_bpm | BPM INKA |
| `con_fab_ims` | con_fab_ims | consumable fabrikasi IMS |
| `con_fin_ims` | — | consumable finishing IMS |
| `cp` | cp1 | check point |
| `cp_1` | cp, cp_bpm, cp_lap, bpm_lap | check point |
| `ct_plant` | — | control plant |
| `drawing` | — | gambar/drawing |
| `exp` | exp, exp1, self_check, sttp5 | expedisi dokumen |
| `exp_cp` | exp_cp, renprod_exp, renprod_exp1/2 | expedisi CP |
| `exp_org` | — | expedisi (penerimaan) |
| `gas` | dalmat_gas, dalmat_gas_report | pemakaian gas |
| `grafik_md` | grafik, dashboard | data grafik MD per proyek |
| `ims_bpm`, `ims_spm` | — | BPM/SPM IMS (referensi) |
| `jadwal` | md_jadwal | jadwal MD |
| `jenis_kereta_tb` | — | referensi jenis kereta |
| `lap_wi` | lap_wi_all, rencana_wi | laporan WI |
| `md_gambar` | — | gambar MD |
| `md_header` | md, md2, md5, md_lap, lap_md, checksheet_1/2, ncr | master MD (≈139rb baris) |
| `mdr` | — | manufacturing data record |
| `memo` | pengadaan, pengadaan1, rencana_wi, proses | memo pengadaan |
| `memo_masuk`, `memo_proses_masuk` | memo_masuk | memo masuk |
| `mesin` | — | master mesin |
| `ncr` | — | non-conformance report (4 baris) |
| `notifications` | — | notifikasi |
| `oka` | kode_proyek, kode_proyek_1, dashboard, wi_5 | master proyek |
| `piecepart`, `piecepart_datang` | piecepart, ncr, sttp_dalmat | stock piecepart |
| `pipt` | pi_pt, pi_pt1 | PI/PT |
| `proses` | proses | master kode proses |
| `proyek` | — | master proyek (referensi) |
| `self_check` | self_check | self check produksi |
| `spm` | dalmat_spm, spm_org | surat permintaan material |
| `spm_ims`, `spm_ims_org`, `spm_org`, `spm_ppcb` | dalmat_spm_ims, spm_ims_org, spm_ppcb | SPM (varian) |
| `sttp_dalmat`, `sttp_dalmat_datang`, `sttp_dalmat_datang_org` | sttp_dalmat, sttp_dalmat_datang, sttp_datang_org | STTP Dalmat |
| `sttpp`, `sttpp_fab` | sttp, sttp1, sttp3, sttp4, sttp5, sttp51, mdr | STTP part (final) |
| `tool_fab`, `tool_fab_ims` | tool_fab, tool_fab1, tool_fab_ims | stock tool fabrikasi |
| `tool_fin`, `tool_fin_ims` | tool_fin, tool_fin1, con_fin_ims | stock tool finishing |
| `user`, `userb`, `users` | user | akun pengguna |
| `wi_finish` | sttp_fab | WI finish |
| `wi_trn` | wi*, cp*, checksheet*, exp2, wi_mat, wi_lap, lap_proses_ws | **transaksi WI (≈849rb baris)** |
| `wi_trn_bpm` | kode, kode1, sttp5, (template) | kode material BPM |

---

## 9. Catatan Penting & Anomali

1. **`sttpp_fab_1` tidak ada di DB** — dipakai `sttp5` untuk draft STTP; buat tabelnya jika fitur STTP PPCB dipakai.
2. **Template copy-paste** — beberapa modul (`kode_proyek`, `proses`, `kode`) punya `add.php`/handler `add` yang salah sasaran (INSERT ke `wi_trn_bpm`), tidak sesuai tabel utama modulnya. Ini bug bawaan aplikasi asli, bukan dari migrasi.
3. **`md5/proses.php` `add`** memakai variabel `$no_wi` yang tidak terdefinisi (INSERT gagal) — add MD via `add.php` perlu dicek.
4. **Tanpa index** — schema direkonstruksi dari dump data tanpa primary key/index sehingga halaman dengan JOIN/`LIKE` besar (`ncr/data1.php`, `lap_*`) lambat. Disarankan menambah index bila perlu.
5. **`pdf.php`** umumnya adalah tampilan HTML siap-`window.print()`, bukan file PDF biner; FPDF tersedia di beberapa folder `fpdf182/` untuk kasus tertentu.
6. **Migrasi lokal** — semua koneksi `mysql_*` sudah ditransformasi menjadi `mysqli_connect('127.0.0.1','root','','barcode')` agar jalan di PHP 8. Kredensial server produksi (10.10.2.15) tidak lagi dipakai di kode.

---

## 10. Restorasi Sistem ke Laravel (Migrasi dari PHP Native)

Bagian ini menjelaskan **cara** dan **apa saja yang dibutuhkan** untuk membangun ulang sistem Checksheet Wi Renprod sebagai aplikasi Laravel, dengan tetap mempertahankan database, fitur, dan alur bisnis yang ada.

### 10.1 Tujuan & Strategi

- Tujuan: mengganti arsitektur PHP native (per-modul folder) menjadi framework Laravel (MVC: Route → Controller → Model → View/Blade) agar terstruktur, aman (ORM, CSRF, prepared statement), mudah dipelihara, dan siap dikembangkan.
- Strategi: **migrasi bertahap (strangler pattern)** — aplikasi lama tetap berjalan, fitur dipindah satu per satu ke Laravel, tanpa mengubah struktur database agar data tidak hilang.

### 10.2 Kebutuhan Lingkungan (Prerequisites)

| Kebutuhan | Versi | Keterangan |
|---|---|---|
| PHP | ≥ 8.1 (disarankan 8.2) | sudah ada di `D:\laragon\bin\php\php-8.2.29-nts-Win32-vs16-x64` |
| Composer | ≥ 2.x | wajib; cek dengan `composer -V` |
| MySQL | 8.x | DB `barcode` (60 tabel) sudah ada; akses `127.0.0.1`, user `root`, password kosong |
| Ekstensi PHP | mysqli/pdo_mysql, mbstring, gd, zip, openssl, fileinfo | sudah aktif di PHP 8.2.29 |
| Node (opsional) | 18+ | untuk build asset frontend bila memakai Vite |

Instal Composer jika belum ada:
```bash
composer -V   # cek
# Jika belum: unduh composer-setup.exe dari https://getcomposer.org
```

### 10.3 Membuat Project Laravel

```bash
cd D:\laragon\www\sourcecode
composer create-project laravel/laravel renprod-laravel
cd renprod-laravel
```

Konfigurasi koneksi di file `.env` (project Laravel baru):
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barcode
DB_USERNAME=root
DB_PASSWORD=
```

> Penting: **jangan import ulang dump ke database baru**. Karena data sudah ada di DB `barcode`, migrasi Laravel cukup dibuat agar **sinkron dengan schema yang ada** (lihat 10.4), lalu Laravel langsung dipakai membaca/menulis tabel yang sama.

### 10.4 Migrasi Database (Schema → Laravel Migration)

Schema saat ini adalah hasil rekonstruksi tanpa primary key/index. Untuk Laravel, setiap tabel perlu dibuatkan **migration** (untuk kolom & tipe), lalu tambahkan **index/PK** yang saat ini hilang:

1. Buat satu migration per tabel (60 tabel) dengan kolom sesuai `barcode_schema.sql` / hasil `DESCRIBE` tiap tabel.
2. Setiap migration `up()` menambahkan: primary key (pilih kolom id atau kombinasi unik), `index()` untuk kolom yang sering dicari (`part_no`, `no_konv`, `no_ctplan`, `oka`, `proyek`, `lot`, `tgl_kirim`, `no_bpm`, `no_spm`, `no_sttp`, `kode_alat`, dll).
3. Gunakan migration yang **idempotent**: cukup dijalankan `php artisan migrate` sekali; jangan drop/recreate tabel yang sudah berisi data.
4. Buat tabel yang hilang: `sttpp_fab_1` (draft STTP, dipakai `sttp5`).
5. Verifikasi: jalankan `php artisan migrate` lalu `php artisan db:show` atau cek `SELECT COUNT(*)` per tabel tetap sama dengan data asli.

Alat bantu: `php artisan make:model` + `schema:dump` dapat dipakai, atau tool `laravel-ide-helper`/`mysql-to-laravel` untuk generate migration dari schema yang ada.

### 10.5 Model (Eloquent) & Relasi

Buat **Model** untuk tiap tabel dan definisikan relasi sesuai penggunaan di kode lama:

| Model | Tabel | Relasi penting (dari query lama) |
|---|---|---|
| `Oka` | `oka` | `hasMany(WiTrn, 'oka')`, dipakai join `barcode`, `exp`, `wi_5/pdf.php` |
| `MdHeader` | `md_header` | `hasMany(WiTrn, 'part_no')` |
| `WiTrn` | `wi_trn` | `belongsTo(Oka, 'oka')`, `belongsTo(MdHeader, 'part_no')`; join `md_lap`, `barcode`, `cp_bpm` |
| `Barcode` | `barcode` | `belongsTo(Oka, 'oka')`; sumber pembuatan WI (`wi_1/add.php`) |
| `Cp1` / `Cp` | `cp_1` / `cp` | dipakai `cp_bpm`, `cp_lap`, `bpm_lap` |
| `BpmCp` | `bpm_cp` | `hasMany` ke `wi_trn` lewat `no_cp` |
| `Bpm` / `BpmIms` | `bpm` / `bpm_ims` | stock BPM; `dalmat_spm` menambah `stok` |
| `Spm` | `spm` | record permintaan material |
| `Sttpp` / `SttppFab` | `sttpp` / `sttpp_fab` | STTP; `mdr` memakai `sttpp_fab` |
| `SttpDalmat*` | `sttp_dalmat*` | STTP Dalmat |
| `ToolFab` / `ToolFin` / `ToolFabIms` / `ToolFinIms` | `tool_*` | stock tool |
| `Memo` / `MemoMasuk` | `memo` / `memo_masuk` | pengadaan |
| `User` | `user` | auth (lihat 10.6) |
| `GrafikMd` | `grafik_md` | dashboard & grafik |

Gunakan `DB::table` / raw query hanya untuk laporan kompleks (join besar seperti `ncr`, `lap_*`); sisanya pakai Eloquent + `whereHas`/`with` agar aman dari SQL injection.

### 10.6 Autentikasi (Pengganti Login Native)

Sistem lama: session `$_SESSION['level']` + `$_SESSION['kode_user']`, password `sha1` di tabel `user`.

Di Laravel:
1. **Jangan** memakai default `users` (tabel baru) — pakai tabel `user` yang ada.
2. Buat guard custom di `config/auth.php` yang membaca tabel `user` (kolom `kode_user` sebagai id, `password` di-hash `sha1`).
   - Tambahkan `'password' => 'sha1'` (hash driver bawaan Laravel untuk `sha1`) pada guard, atau
   - Implementasikan `Hasher` kustom bila perlu kompatibilitas `md5/sha1` lama.
3. Gunakan **Laravel Breeze/Jetstream** untuk halaman login, sesuaikan kolom (username = `nama_user`/`kode_user`).
4. **Role/peran** (level): buat middleware `CheckLevel` (contoh `CheckLevel:admin`) dan terapkan per route group agar menu per peran (operator, admin, operator1, 3, 4, 5) ter-enforce di sisi server.
5. **Hapus bypass login** (`===== BYPASS LOGIN =====` di `_config/config.php`) — di Laravel ganti dengan login sesungguhnya + `auth` middleware.

### 10.7 Routing & Struktur Controller

Peta folder modul lama → Route+Controller:

| Modul lama | Route (contoh) | Controller | View Blade |
|---|---|---|---|
| `dashboard/*` | `GET /` | `DashboardController` | `dashboard/index.blade.php` |
| `md5`, `md2`, `md_lap`, `lap_md` | `resource /md` | `MdController` | `md/index.blade.php` |
| `wi_1` … `wi_6`, `wi_lap` | `resource /wi` | `WiController` | `wi/index.blade.php` |
| `barcode*` | `resource /barcode` | `BarcodeController` | `barcode/index.blade.php` |
| `cp*`, `bpm_cp*`, `cp_lap` | `resource /cp` | `CpController` | `cp/index.blade.php` |
| `dalmat_*`, `spm_*`, `bpm_*` | `resource /dalmat` | `DalmatController` | `dalmat/*.blade.php` |
| `sttp*`, `sttp_dalmat*`, `piecepart`, `ncr` | `resource /sttp` | `SttpController` | `sttp/*.blade.php` |
| `tool_*`, `con_*` | `resource /tool` | `ToolController` | `tool/*.blade.php` |
| `exp*`, `renprod_exp*`, `exp_cp` | `resource /expedisi` | `ExpedisiController` | `expedisi/*.blade.php` |
| `pengadaan*`, `memo_masuk`, `rencana_wi` | `resource /pengadaan` | `PengadaanController` | `pengadaan/*.blade.php` |
| `user` | `resource /user` | `UserController` | `user/index.blade.php` |
| `grafik` | `GET /grafik` | `GrafikController` | `grafik/index.blade.php` |
| `kode_proyek`, `proses`, `kode` | `resource /master` | `MasterDataController` | `master/*.blade.php` |

Setiap aksi `proses.php` (add/add1/edit/edit1/import/move/del) menjadi **method controller**:
`store()`, `update()`, `destroy()`, `import()`, `export()`, `move()`, dengan **Form Request** untuk validasi.

### 10.8 Frontend / Tampilan

| Aset lama | Pindah ke | Keterangan |
|---|---|---|
| `_assets/css/bootstrap.css`, `style.css`, `font-awesome.css` | `resources/css` / CDN | Bootstrap 3 bisa dipertahankan atau upgrade ke 5 |
| `_assets/js/jquery-1.11.1.js`, `bootstrap.js` | `resources/js` / `public/vendor` | |
| `_assets/libs/DataTables/datatables.*` | via npm/CDN | tetap dipakai untuk tabel besar (wi_trn 849rb baris) |
| `_select2/dist/*` | via npm | tetap dipakai untuk dropdown pencarian |
| `_assets/charts/Chart.js` | via npm | grafik dashboard |
| `_header.php` | `resources/views/layouts/app.blade.php` | pindahkan menu per role ke `@include('partials.sidebar')` dengan `@if(Auth::user()->level == 'admin')` dst. |
| `_footer.php` | `partials.footer` | |
| `pagination1.php` | Blade paginator bawaan Laravel | `{{ $rows->links() }}` |

### 10.9 Library Pengganti / Adaptasi

| Library lama | Masalah | Pengganti di Laravel |
|---|---|---|
| **PHPExcel 1.8.2** (import/export Excel) | deprecated, sudah di-patch manual | **PhpSpreadsheet** (`composer require phpoffice/phpspreadsheet`) — ganti `PHPExcel_IOFactory::load` → `IOFactory::load`, `getActiveSheet()->toArray()` tetap sama |
| **FPDF** (print) | manual | **Dompdf** / **Snappy** (`composer require barryvdh/laravel-dompdf`) untuk PDF; atau tetap print-view HTML |
| **Ramsey UUID** | sudah kompatibel | Laravel sudah mendukung `Str::uuid()`; package `ramsey/uuid` sudah include |
| mysqli mentah | raw query + rentan injection | **Eloquent / Query Builder** (`where`, `select`, `join`) |
| Session manual | `$_SESSION` | **Laravel Session** (`session()`, `auth()->user()`) |

### 10.10 Fitur Khusus yang Harus Dipertahankan

1. **Import Excel multi-kolom** — setiap modul punya mapping kolom Excel (mis. `wi_1`: kolom A–R; `md5`: A–AA; `barcode`: A–I). Buat **template import per modul** (`download format` yang ada di `_file1/sample1/`) dan mapping kolom yang sama.
2. **Export Excel** — export dari SQL aktif (form menyembunyikan `sql` lalu di-POST ke `export.php`). Di Laravel ganti dengan **query di controller** (jangan pernah menerima raw SQL dari form → risiko injeksi).
3. **Print surat** (SPM, STTP, WI UGL, Exp Wi) — template HTML print (`window.print()`) dengan kop INKA (`img/inka.png`); pindahkan ke view Blade + `@media print` CSS, atau render ke PDF via Dompdf.
4. **Upload foto tool** — `tool_fab` menyimpan foto ke `images2/`; pindahkan ke **Laravel Storage** (`storage/app/public`) + `php artisan storage:link`, simpan path di kolom `foto`.
5. **Batch update & checkbox select-all** — alur edit massal (`$_POST['id'][...]`) → `request()->input('id')` (array) + update dalam transaction.
6. **Flow draft → submit STTP** (`sttpp_fab_1` → `sttpp_fab`) — implementasikan dengan status/flag di model (mis. kolom `status=draft|final`) atau tabel draft terpisah + transaction `DB::transaction()`.
7. **Sinkronisasi `bpm.stok` saat input SPM** — gunakan transaction: `Spm::create()` + `Bpm::increment('stok', $jml)`.

### 10.11 Bug Bawaan yang Wajib Diperbaiki Saat Migrasi

Jangan bawa bug dari aplikasi lama ke Laravel:
1. **Template copy-paste** (`kode_proyek`, `proses`, `kode` → salah INSERT ke `wi_trn_bpm`) — tulis controller yang benar sesuai tabel tujuannya.
2. **`md5 add` error `$no_wi` undefined** — perbaiki insert MD.
3. **Tanpa index** — tambahkan index/PK di migration (10.4) agar JOIN besar (`ncr`, `lap_*`) cepat.
4. **Raw SQL dari form (`export.php`)** — ganti dengan query controller (10.10 poin 2).
5. **SQL injection di query lama** (concat variabel langsung) — semua dipindah ke Eloquent/query builder.

### 10.12 Langkah Migrasi Bertahap (Checklist)

```text
[ ] 1. Backup database & kode (mysqldump barcode > backup.sql)
[ ] 2. Install Composer + buat project Laravel + konfigurasi .env
[ ] 3. Generate migration 60 tabel + index + buat tabel sttpp_fab_1
[ ] 4. Buat Model + relasi untuk tiap tabel
[ ] 5. Auth custom (tabel user + level + middleware CheckLevel) — ganti bypass login
[ ] 6. Pindahkan layout (_header/_footer) → Blade layout + menu per role
[ ] 7. Migrasi modul per modul (mulai dari: Kode Proyek → MD → WI → Barcode → CP → Expedisi → STTP/Dalmat → Tool → Laporan)
[ ] 8. Ganti PHPExcel → PhpSpreadsheet (import/export) & FPDF → Dompdf
[ ] 9. Pindahkan foto tool ke Storage
[ ] 10. Uji banding: jumlah baris & nilai kunci sama antara aplikasi lama dan Laravel per modul
[ ] 11. Uji peran (level) & alur bisnis end-to-end (7)
[ ] 12. Hardening: CSRF, prepared statement, validasi, pagination untuk tabel besar
```

### 10.13 Hasil Akhir

Setelah migrasi selesai, struktur folder menjadi:

```
renprod-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/   (MdController, WiController, BarcodeController, ...)
│   │   └── Middleware/    (CheckLevel)
│   ├── Models/            (Oka, MdHeader, WiTrn, Barcode, Spm, ToolFab, ...)
├── database/
│   ├── migrations/        (60 tabel + index)
│   └── seeders/           (opsional: data referensi)
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── dashboard/ md/ wi/ barcode/ cp/ dalmat/ sttp/ tool/ expedisi/ ...
└── routes/web.php
```

Database **tidak berubah** — semua tabel & data tetap di `barcode`; Laravel hanya mengganti lapisan aplikasi.
