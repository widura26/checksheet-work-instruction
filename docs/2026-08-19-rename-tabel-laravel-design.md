# Design: Rename Tabel ke Konvensi Laravel + Spatie RBAC

**Tanggal:** 2026-08-19
**Database:** `barcode` (MySQL, aplikasi legacy PHP di `renprod.1`)
**Scope:** Mapping dokumen saja — tanpa mengubah database atau kode.

## Tujuan

1. Membuat daftar mapping nama tabel database `barcode` agar sesuai dengan konvensi penamaan Laravel.
2. Menyiapkan landasan nama tabel untuk integrasi **Spatie `laravel-permission`** (RBAC).

## Konvensi Penamaan Laravel yang Dipakai

1. **Nama tabel plural, snake_case** — mis. `barcode` → `barcodes`.
2. **Multiword snake_case yang sudah sesuai** dipertahankan — mis. `md_header`, `bpm_ims`, `spm_ppcb`.
3. **Akronim domain** (BPM, SPM, STTP, CP, MD, WI, OKA, NCR, IMS) dipertahankan; bagian kata benda dijamakkan.
4. **Tabel pivot** (relasi many-to-many) dibiarkan tunggal & urutan alfabetis — mis. `bpm_cp`, `exp_cp` (sudah sesuai konvensi Laravel).
5. **Suffix `_tb` / `_1`** yang tidak bermakna dihapus atau dipetakan ulang.
6. **Kata Indonesia**: dijamakkan secara umum; istilah teknis yang memang sudah jamak dipertahankan (`proses`).

## Mapping Tabel (57 tabel)

| No | Tabel Saat Ini | Nama Laravel | Keterangan |
|---|---|---|---|
| 1 | audit_log | `audit_logs` | plural |
| 2 | barcode | `barcodes` | plural |
| 3 | barcode_detail | `barcode_details` | plural |
| 4 | barcode_start | `barcode_starts` | plural |
| 5 | bobot_part | `bobot_parts` | plural |
| 6 | bpm | `bpms` | plural akronim |
| 7 | bpm_cp | `bpm_cp` | pivot, sudah sesuai |
| 8 | bpm_ims | `bpm_ims` | sudah sesuai |
| 9 | con_fab_ims | `con_fab_ims` | sudah sesuai |
| 10 | con_fin_ims | `con_fin_ims` | sudah sesuai |
| 11 | cp | `cps` | plural |
| 12 | cp_1 | `cp_details` | butuh konfirmasi domain (cp_1 = tabel CP aktif) |
| 13 | ct_plant | `ct_plants` | plural |
| 14 | drawing | `drawings` | plural |
| 15 | exp | `exps` | plural (Ekspedisi) |
| 16 | exp_cp | `exp_cp` | pivot, sudah sesuai |
| 17 | exp_org | `exp_orgs` | plural |
| 18 | gas | `gases` | plural `gas` → `gases` |
| 19 | grafik_md | `grafik_md` | sudah sesuai |
| 20 | ims_bpm | `ims_bpm` | sudah sesuai |
| 21 | ims_spm | `ims_spm` | sudah sesuai |
| 22 | jadwal | `jadwals` | plural |
| 23 | jenis_kereta_tb | `jenis_keretas` | suffix `_tb` dihapus |
| 24 | lap_wi | `lap_wi` | sudah sesuai |
| 25 | md_gambar | `md_gambars` | plural |
| 26 | md_header | `md_headers` | plural |
| 27 | mdr | `mdrs` | plural |
| 28 | memo | `memos` | plural |
| 29 | memo_masuk | `memo_masuks` | plural |
| 30 | memo_proses_masuk | `memo_proses_masuks` | plural |
| 31 | mesin | `mesins` | plural |
| 32 | ncr | `ncrs` | plural |
| 33 | notifications | `notifications` | sudah sesuai |
| 34 | oka | `okas` | plural |
| 35 | piecepart | `pieceparts` | plural |
| 36 | piecepart_datang | `piecepart_datangs` | plural |
| 37 | pipt | `pipts` | plural |
| 38 | proses | `proses` | sudah jamak; dipertahankan (Laravel pluralizer memberi `proseses` — tidak dipakai) |
| 39 | proyek | `proyeks` | plural |
| 40 | self_check | `self_checks` | plural |
| 41 | spm | `spms` | plural |
| 42 | spm_ims | `spm_ims` | sudah sesuai |
| 43 | spm_ims_org | `spm_ims_orgs` | plural |
| 44 | spm_org | `spm_orgs` | plural |
| 45 | spm_ppcb | `spm_ppcb` | sudah sesuai |
| 46 | sttp_dalmat | `sttp_dalmats` | plural |
| 47 | sttp_dalmat_datang | `sttp_dalmat_datangs` | plural |
| 48 | sttp_dalmat_datang_org | `sttp_dalmat_datang_orgs` | plural |
| 49 | sttpp | `sttpps` | plural |
| 50 | sttpp_fab | `sttpp_fabs` | plural |
| 51 | tool_fab | `tool_fabs` | plural |
| 52 | tool_fab_ims | `tool_fab_ims` | sudah sesuai |
| 53 | tool_fin | `tool_fins` | plural |
| 54 | tool_fin_ims | `tool_fin_ims` | sudah sesuai |
| 55 | wi_finish | `wi_finishes` | plural |
| 56 | wi_trn | `wi_trns` | plural (`trn` = transaksi) |
| 57 | wi_trn_bpm | `wi_trn_bpms` | plural |

## Tabel Legacy (di luar mapping)

| Tabel | Tujuan Baru | Keterangan |
|---|---|---|
| user | `user_legacy` | 65 baris, tabel login aktif aplikasi lama; sistem baru memakai `users` milik Laravel — tidak dipetakan |
| userb | `userb_legacy` | 4 baris, backup `user` (struktur identik); tidak dipakai |
| users (lama) | `user_access_legacy` | 13 baris, tabel orphan (kolom flag md/wi/sttpp/barcode/dst.), tidak dipakai kode mana pun; tidak dipetakan |

## Integrasi Spatie `laravel-permission`

Tabel baru yang wajib ada (nama ditentukan Spatie, tidak diubah):

| Tabel | Tujuan |
|---|---|
| `roles` | level user |
| `permissions` | izin per menu/aksi |
| `role_has_permissions` | relasi role ↔ permission |
| `model_has_roles` | relasi user ↔ role (morph) |
| `model_has_permissions` | relasi user ↔ permission langsung (morph) |

### Seed roles

Tabel `users` (kanonik) sudah ada dari sistem Laravel baru — tidak diimpor dari aplikasi lama. Roles di-seed mengikuti level aplikasi lama (dokumentasi, bukan migrasi data):

```
admin, operator, operator1, operator2, operator3, operator4, operator5
```

Ketujuh nilai ini di-seed menjadi baris di tabel `roles`. Peran user ditangani Spatie via `model_has_roles`.

## Fase 2: Normalisasi Skema (PK / FK / Tipe / Timestamp)

> **Status saat ini:** verifikasi `SHOW CREATE TABLE` menunjukkan SEMUA tabel belum siap untuk Laravel —
> tidak ada primary key, tidak ada foreign key, ~95% kolom bertipe `text`, tidak ada index, tidak ada `created_at`/`updated_at`.
> Rename nama tabel (Fase 1) bukanlah konversi yang cukup; normalisasi kolom wajib dilakukan.

### 1. Primary Key

- Setiap tabel mendapat kolom `id` **BIGINT UNSIGNED AUTO_INCREMENT** sebagai PK (konvensi Laravel).
- Kolom identitas bisnis saat ini (`kode_user`, `id_md`, `id_cp`, `idbarang`, `no_konv`, `oka`, `part_no`, `cp_no`, `no_sttp`) tetap dipertahankan sebagai kolom biasa + **unique index** (bukan PK).
- Perhatian: beberapa tabel sudah punya kolom `id` tapi `DEFAULT NULL` dan tidak unik (mis. `wi_trn.id`, `barcode.id`) — perlu di-backfill nilai unik saat migrasi.
- `md_header` dan `oka` tidak punya kolom id sama sekali → ditambah baru.
- Karena tidak ada FK saat ini, penambahan PK aman (tidak ada constraint yang harus disesuaikan).

### 2. Foreign Key (relasi terdeteksi dari JOIN di kode)

Kolom relasi saat ini semuanya bertipe `text`; saat normalisasi dikonversi ke `unsignedBigInteger` + constraint FK yang menunjuk ke `id` tabel tujuan.

| Tabel Anak | Kolom Anak | Tabel Induk | Kolom Induk | Catatan |
|---|---|---|---|---|
| `wi_trns` | `oka_id` (dari `oka`) | `okas` | `id` | JOIN `wi_trn.oka=oka.oka` |
| `wi_trns` | `material_id` (dari `idbarang`) | `wi_trn_bpms` | `id` | JOIN `wi_trn.idbarang=wi_trn_bpm.idbarang` |
| `wi_trns` | `bpm_cp_id` (dari `no_ctplan`) | `bpm_cps` | `id` | JOIN `wi_trn.no_ctplan=bpm_cp.no_cp` |
| `wi_trns` | `md_header_id` (`part_no`+`proyek`) | `md_headers` | `id` | composite key → konversi ke FK tunggal |
| `wi_trns` | `jenis_kereta_id` | `jenis_keretas` | `id` | JOIN `wi_trn.jenis_kereta=jenis_kereta_tb.jenis_kereta` |
| `wi_trns` | `cp_id` (`part_no`+`no_ctplan`) | `cps` | `id` | JOIN `wi_trn.part_no=cp.part_no AND no_ctplan=cp.no_cp` |
| `wi_trns` | `memo_id` (`part_no`+`oka`) | `memos` | `id` | JOIN `wi_trn.part_no=memo.part_no AND oka=memo.oka` |
| `wi_trns` | `spm_ims_id` (dari `no_ctplan`) | `spm_ims` | `id` | JOIN `wi_trn.no_ctplan=spm_ims.no_cp` |
| `barcodes` | `oka_id` | `okas` | `id` | JOIN `barcode.oka=oka.oka` |
| `barcodes` | `wi_trn_id` (`part_no`+`PR`) | `wi_trns` | `id` | JOIN `wi_trn.no_ctplan=barcode.PR AND part_no` |
| `cps` | `bpm_cp_id` (dari `cp_no`) | `bpm_cps` | `id` | JOIN `cp_1.cp_no=bpm_cp.no_cp` |
| `cps` | `exp_cp_id` (dari `cp_no`) | `exp_cps` | `id` | JOIN `cp_1.cp_no=exp_cp.cp` |
| `bpm_cps` | `material_id` (dari `kode_mat`) | `wi_trn_bpms` | `id` | JOIN `bpm_cp.kode_mat=wi_trn_bpm.idbarang` |
| `bpms` | `material_id` (dari `kode_mat`) | `wi_trn_bpms` | `id` | JOIN `bpm.kode_mat=wi_trn_bpm.idbarang` |
| `bpms` | `spm_id` (dari `no_konv_bpm`) | `spms` | `id` | JOIN `bpm.no_konv_bpm=spm.no_konv_spm` |
| `bpm_ims` | `material_id` (dari `kode_mat`) | `wi_trn_bpms` | `id` | JOIN `bpm_ims.kode_mat=wi_trn_bpm.idbarang` |
| `bpm_ims` | `spm_ims_id` (dari `no_konv_bpm`) | `spm_ims` | `id` | JOIN `bpm_ims.no_konv_bpm=spm_ims.no_konv_spm` |
| `gases` | `material_id` (dari `kode_gas`) | `wi_trn_bpms` | `id` | JOIN `gas.kode_gas=wi_trn_bpm.idbarang` |
| `exps` | `oka_id` | `okas` | `id` | JOIN `exp.oka=oka.oka` |
| `exp_cps` | `oka_id` | `okas` | `id` | JOIN `exp_cp.oka=oka.oka` |
| `spms` | `spm_org_id` (dari `no_spm`) | `spm_orgs` | `id` | JOIN `spm.no_spm=spm_org.no_spm_org` |
| `spm_ims` | `oka_id` | `okas` | `id` | JOIN `spm_ims.oka=oka.oka` |
| `spm_ims` | `spm_ims_org_id` | `spm_ims_orgs` | `id` | JOIN `spm_ims.no_spm=spm_ims_org.no_spm_ims_org` |
| `md_laps` | `wi_trn_id` (`no_part1`+`oka1`+`lot1`) | `wi_trns` | `id` | composite |
| `md2s` | `md_header_id` (dari `md2_mdh`) | `md_headers` | `id` | JOIN `md2.md2_mdh=md_header.mdh_md2` |
| `md2s` | `jadwal_id` (dari `md2_jadwal`) | `jadwals` | `id` | JOIN `md2.md2_jadwal=jadwal.jadwal_md2` |
| `md2s` | `wi_trn_id` (dari `konv_md`) | `wi_trns` | `id` | JOIN `md2.konv_md=wi_trn.konv_wi` |
| `sttpp_details` | `sttpp_id` (dari `nosttpp`) | `sttpps` | `id` | JOIN `sttpp_detail.nosttpp=sttpp.nosttpp` |
| `sttp_dalmats` | `md_header_id` (`no_part`+`proyek`) | `md_headers` | `id` | JOIN `sttp_dalmat.no_part=md_header.part_no` |
| `pieceparts` | `md_header_id` (`no_part2`+`oka1`) | `md_headers` | `id` | JOIN `piecepart.no_part2=md_header.part_no` |
| `pieceparts` | `piecepart_datang_id` | `piecepart_datangs` | `id` | JOIN `piecepart.no_part1=piecepart_datang.no_part` |
| `piecepart_datangs` | `oka_id` | `okas` | `id` | JOIN `piecepart_datang.oka=oka.oka` |

> Catatan: banyak relasi bersifat *composite string key* (`part_no`+`proyek`, `part_no`+`oka`+`lot`). Saat normalisasi,
> kolom join `text` diganti FK numerik tunggal ke `id` tabel induk. Uniqueness bisnis tetap dijaga lewat
> **unique index** pada kombinasi kolom business-key.

### 3. Konversi Tipe Kolom

| Tipe Lama | Tipe Baru (Laravel) | Contoh |
|---|---|---|
| `text` (string) | `string` (varchar) | `part_no`, `no_konv`, `cp_no`, `status` |
| `text` (angka/kuantitas) | `decimal` / `integer` | `qty`, `qty_lot`, `total_car`, `qty_kirim` |
| `text` (tanggal string) | `date` / `dateTime` | `tgl_kirim`, `tgl_bpm` |
| `text` (acronym/kode) | `string` + index | `oka`, `idbarang`, `kode_mat` |
| `text` (flag: 0/1) | `boolean` | `status`, `aproved` |
| `text` (gambar/long text) | `longText` | `des`, `ket` |
| `bigint DEFAULT NULL` (id lama) | `unsignedBigInteger` auto-increment | `id` |
| `double` | `decimal` | `t1`, `h1`, `b1`, `l1`, `qa_tc1` |

- Migrasi tipe **harus** diawali validasi data (angka/`decimal` yang tersimpan sebagai `text`, string tanggal kosong `'0000-00-00'`, nilai kosong yang bukan NULL).
- Setiap kolom yang sering dipakai di `WHERE`/`JOIN` diberi index (`no_konv`, `part_no`, `oka`, `proyek`, `lot`, `no_ctplan`).

### 4. Timestamps & Soft Delete

- Tambah `created_at` dan `updated_at` (diisi Eloquent otomatis) di semua tabel.
- Kolom tanggal transaksi lama (`tgl_oka`, `tgl_cp`, `tgl_kirim`, dst.) TIDAK dihapus — tetap sebagai kolom bisnis, hanya dilengkapi timestamp Eloquent.
- Opsional: `deleted_at` (soft delete) untuk tabel master (`md_headers`, `okas`, `users`).

## Lampiran A: Deskripsi Field per Tabel

> Format: `field` (tipe saat ini) — arti. Semua field yang bertipe `text` akan dikonversi sesuai Fase 2.
> Istilah umum: **oka** = kode unit/order kereta, **part** = komponen, **lot** = batch produksi,
> **konv** = konversi (no_konv = nomor urut konversi barcode→WI), **tgl_*** = tanggal transaksi.
>
> **Legenda kolom "Peran & Relasi":**
> - **PK** = primary key (menjadi `id` Laravel) · **BK** = business key (unique index)
> - **FK** = foreign key → diikuti tabel tujuan + **kardinalitas** (relasi dari sisi field ini):
>   - *one-to-many* — satu baris di tabel tujuan dimiliki banyak baris tabel ini
>   - *many-to-one* — banyak baris tabel ini menunjuk satu baris tabel tujuan
>   - *one-to-one* — satu-ke-satu (biasanya tabel pelengkap/header)
>   - *many-to-many* — hanya pada tabel pivot
> - **Idxs** = diberi index untuk lookup/join · *(kosong)* = field data biasa

### `audit_logs` (audit_log) — log aktivitas pengguna
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| table_name | text | nama tabel yang diubah | data (polimorfik: referensi nama tabel target) |
| record_id | text | id record yang diubah | data (polimorfik: id record target) |
| action | text | aksi (create/update/delete) | data |
| old_value / new_value | text | nilai sebelum/sesudah | data |
| changed_fields | text | daftar field yang berubah | data |
| user_id | text | pelaku perubahan | **FK → users** (many-to-one) |
| user_name | text | nama pelaku (denormalisasi) | data (redundansi dari users) |
| ip_address | text | IP pelaku | data |
| created_at | datetime | waktu log | timestamp |

### `barcodes` (barcode) — data barcode per part/oka/lot
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id (nullable, akan jadi PK) | **PK** |
| oka | text | kode unit | **FK → okas** (many-to-one) + **Idxs** |
| wbs | text | kode WBS | data |
| lot | text | nomor lot | data (dipakai join ke wi_trns.lot) |
| part_no | text | nomor part | **BK** (unik komposit dgn `PR`) + **Idxs** |
| partno1 | text | nomor part alternatif | data |
| PR | text | nomor PR / CP asal | **FK → wi_trns.no_ctplan** (many-to-one) |
| no_konv | text | nomor konversi | **BK** (unik) + **FK → wi_trns.no_konv** (one-to-one) |
| tgl | date | tanggal input barcode | data |
| status | text | status record | data |
| bil | text | bill/keterangan bil | data |
| user_barcode | text | user penginput | **FK → users.kode_user** (many-to-one) |
| tgl_print | date | tanggal cetak barcode | data |

### `barcode_details` (barcode_detail) — riwayat scan/proses barcode
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| no_konv | varchar(4000) | nomor konversi | **FK → barcodes.no_konv** (one-to-many: 1 barcode banyak scan) + **Idxs** |
| proses | varchar(4000) | nama proses yang di-scan | **FK → proses.kode_proses** (many-to-one) |
| tgl | datetime | waktu scan | data (timestamp) |
| qty | bigint | kuantitas | data |
| jml_org | bigint | jumlah original | data |
| user_brcode | varchar(4000) | user scan | **FK → users.kode_user** (many-to-one) |
| kode_mesin | text | kode mesin | **FK → mesins.kode_mesin** (many-to-one) |

### `barcode_starts` (barcode_start) — awal proses barcode
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| no_konv | varchar(4000) | nomor konversi | **FK → barcodes.no_konv** (one-to-many) + **Idxs** |
| proses | varchar(4000) | proses awal | **FK → proses.kode_proses** (many-to-one) |
| tgl_start | datetime | waktu mulai | data (timestamp) |
| user_brcode | varchar(4000) | user | **FK → users.kode_user** (many-to-one) |
| kode_mesin | text | kode mesin | **FK → mesins.kode_mesin** (many-to-one) |

### `bobot_parts` (bobot_part) — bobot per part/oka/lot
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| proyek | text | kode proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| lot | text | nomor lot | data |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) + **Idxs** |
| bobot | double | nilai bobot | data |
| status | text | status | data |
| ket | text | keterangan | data |

### `bpms` (bpm) — Bahan Permintaan Material (BPM)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| no_bpm | text | nomor BPM | **BK** (unik) + **Idxs** (di-referensi spms.no_bpm, gases.no_bpm, memo_proses_masuks) |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| kode_mat | text | kode material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| spec | text | spesifikasi material | data |
| jumlah | bigint | jumlah diminta | data |
| satuan | text | satuan (pcs, kg, dll) | data |
| tgl_permintaan | date | tanggal permintaan | data |
| ket_bpm | text | keterangan BPM | data |
| no_konv_bpm | text | no konversi terkait | **FK → wi_trns.no_konv** (many-to-one) |
| no_bprm | text | nomor BPRM (realisasi) | data |
| tgl_bprm | date | tanggal BPRM | data |
| head_number | text | nomor kepala dokumen | data |
| jml_bprm | text | jumlah realisasi | data |
| stok | text | stok | data |
| user | text | user penginput | **FK → users.kode_user** (many-to-one) |

### `bpm_cps` (bpm_cp) — relasi BPM ↔ Cutting Plan (pivot)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| no_bpm | text | nomor BPM | **FK → bpms.no_bpm** — **many-to-many** (pivot BPM↔CP) |
| no_cp | text | nomor cutting plan | **FK → cp_details.cp_no** — **many-to-many** (pivot BPM↔CP) |
| kode_mat | text | kode material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| spec | text | spesifikasi | data |
| jumlah_bpm | text | jumlah dari BPM | data |
| ket_mat | text | keterangan material | data |
| oka_bpm | text | unit pada BPM | **FK → okas** (many-to-one) |
| tgl_bpm | date | tanggal BPM | data |
| ket_bpm | text | keterangan | data |
| user_bpm_cp | text | user | **FK → users.kode_user** (many-to-one) |
| mat_cp | text | material CP | data |
| spek_mat_cp | text | spesifikasi material CP | data |
| qty_cp | text | qty CP | data |
| mat_sisa | text | sisa material | data |
| status_mat | text | status material | data |
| no_pr / no_po | text | nomor PR / PO | data |
| eta | text | estimasi kedatangan | data |

### `bpm_ims` (bpm_ims) — BPM unit IMS (INKA)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| (sama dengan `bpms`) | | no_bpm, oka, kode_mat, spec, jumlah, satuan, tgl_permintaan, ket_bpm, no_konv_bpm, no_bprm, tgl_bprm, head_number, jml_bprm, stok | relasi identik dgn `bpms` (no_bpm BK, oka→okas, kode_mat→wi_trn_bpms, no_konv_bpm→wi_trns) |
| penerbit_bpm | text | unit penerbit BPM | data |
| user_bpm_ims | text | user | **FK → users.kode_user** (many-to-one) |

### `con_fab_ims` (con_fab_ims) — consumable fabrikasi IMS
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| nama | text | nama barang | data |
| spesifikasi | text | spesifikasi | data |
| kode_alat | text | kode alat | **BK** (unik) |
| kode_mesin | text | kode mesin | **FK → mesins.kode_mesin** (many-to-one) |
| jumlah | text | jumlah | data |
| satuan | text | satuan | data |
| kondisi | text | kondisi | data |
| lokasi | text | lokasi simpan | data |
| no_rak | text | nomor rak | data |
| foto | text | path foto | data |

### `con_fin_ims` (con_fin_ims) — consumable finishing IMS (struktur identik con_fab_ims)
> Relasi identik: `id` PK, `kode_alat` BK, `kode_mesin` → mesins (many-to-one), sisanya data.

### `cps` (cp) — Cutting Plan
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_cp | text | id CP (tekstual) | **BK** |
| no_cp | text | nomor cutting plan | **BK** (unik) + **Idxs** (di-referensi wi_trns.no_ctplan, bpm_cps.no_cp, exp_cps.cp) |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| mat / spec_mat | text | material & spesifikasi | data |
| qty | text | kuantitas | data |
| mat_sisa | text | sisa material | data |
| no_exp | text | nomor ekspedisi | **FK → exps.no_exp** (many-to-one) |
| shop | text | shop/workshop | data |
| pic_cp | text | penanggung jawab CP | **FK → users.kode_user** (many-to-one) |
| diterima | text | penerima | data |
| tgl_kirim | date | tanggal kirim | data |
| grup | text | grup | data |
| lot / qty_lot | text | lot & qty per lot | data |

### `cp_details` (cp_1) — Cutting Plan aktif
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_cp | text | id CP | **BK** |
| no_exp | text | nomor ekspedisi | **FK → exps.no_exp** (many-to-one) |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) + **Idxs** |
| rev_cp | text | revisi CP | data |
| cp_no | text | nomor CP | **BK** (unik) + **Idxs** — di-referensi bpm_cps.no_cp, exp_cps.cp, wi_trns.no_ctplan (one-to-many) |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| lot / qty_lot | text | lot & qty per lot | data |
| mat_cp | text | material CP | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| spek_mat_cp | text | spesifikasi material | data |
| group | text | grup (reserved word) | data |
| qty | text | kuantitas | data |
| shop_name | text | nama shop | data |
| mat_sisa | text | sisa material | data |
| tgl_cp | date | tanggal CP | data |
| planer | text | nama planner | **FK → users.kode_user** (many-to-one) |
| nama_komp | text | nama komponen | data |

### `ct_plants` (ct_plant) — jadwal cutting plan
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| kode_proyek | text | kode proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| lot | text | nomor lot | data |
| no_ctplan | text | nomor cutting plan | **FK → cp_details.cp_no** (many-to-one) |
| bpm_no | text | nomor BPM | **FK → bpms.no_bpm** (many-to-one) |
| idbarang | text | kode material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| tgl | date | tanggal | data |
| detail | bigint | jumlah detail | data |
| status | text | status | data |
| jenis_kereta | text | jenis kereta | **FK → jenis_keretas.jenis_kereta** (many-to-one) |

### `drawings` (drawing) — gambar drawing
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| proyek | text | kode proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| id | bigint | id | **PK** |
| nogbr | text | nomor gambar | **BK** (unik) + **Idxs** (di-referensi md_headers.nogbr) |
| nama | text | nama drawing | data |
| tgl | date | tanggal | data |
| rev | text | revisi | data |
| detail | bigint | jumlah detail | data |
| jml_md / jml_wi | bigint | jumlah MD / WI | data |

### `exps` (exp) — ekspedisi dokumen
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| no_exp | text | nomor ekspedisi | **BK** (unik) + **Idxs** (di-referensi cps.no_exp, cp_details.no_exp, pipts.no_exp) |
| no_cp | text | nomor CP | **FK → cp_details.cp_no** (many-to-one) |
| no_part | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| lot | text | nomor lot | data |
| tgl_kirim | date | tanggal kirim | data |
| tgl_kembali | date | tanggal kembali | data |
| pengirim | text | pengirim | **FK → users.kode_user** (many-to-one) |
| penerima | text | penerima | **FK → users.kode_user** (many-to-one) |
| tujuan | text | tujuan kirim | data |
| ket_exp | text | keterangan | data |

### `exp_cps` (exp_cp) — ekspedisi per CP
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_exp_cp | bigint | id | **PK** |
| exp_cp | text | nomor ekspedisi CP | **BK** (unik) |
| cp | text | nomor CP | **FK → cp_details.cp_no** — **many-to-many** (pivot CP↔ekspedisi) |
| nama_komp | text | nama komponen | data |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| destinasi | text | tujuan | data |
| tgl_kirim | date | tanggal kirim | data |
| pengirim / penerima | text | pengirim & penerima | **FK → users.kode_user** (many-to-one) |
| tgl_terima | date | tanggal terima | data |

### `exp_orgs` (exp_org) — ekspedisi organisasi/asal
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | varchar(4000) | id | **PK** |
| no_exp | varchar(4000) | nomor ekspedisi | **FK → exps.no_exp** (many-to-one) |
| nama_kirim | varchar(4000) | nama pengirim | data |
| tgl_kirim | date | tanggal kirim | data |

### `gases` (gas) — pengelolaan gas
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| no_botol | text | nomor botol gas | **BK** (unik) |
| vendor | text | vendor | data |
| no_spp | text | nomor SPP | data |
| kode_gas | text | kode material gas | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| tgl_masuk | date | tanggal masuk | data |
| no_bpm | text | nomor BPM | **FK → bpms.no_bpm** (many-to-one) |
| no_spm | text | nomor SPM | **FK → spms.no_spm** (many-to-one) |
| tgl_bpm / tgl_spm | date | tanggal BPM/SPM | data |
| bagian | text | bagian pemakai | data |
| tb_isi / tb_kosong | text | jumlah tabung isi/kosong | data |
| tek_awal / tek_akhir | text | tekanan awal/akhir | data |
| no_ambil / tgl_ambil | text/date | nomor & tgl pengambilan | data |
| no_keluar / tgl_keluar | text/date | nomor & tgl keluar | data |
| user_gas | text | user | **FK → users.kode_user** (many-to-one) |

### `grafik_md` (grafik_md) — data grafik monitoring MD
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| g_proyek | text | proyek untuk grafik | **FK → proyeks.kode_proyek** (many-to-one) + **Idxs** |
| g_nama | text | nama (title) | data |
| g_lot | text | lot | data (join ke wi_trns.lot / md_header.lot) |
| g_md / g_wi | text | jumlah MD / WI | data (agregat) |
| g_tampil | text | flag tampil di grafik | data |

### `ims_bpms` (ims_bpm) — BPM IMS (duplikat/arsip bpm_ims)
> Relasi identik dgn `bpm_ims`: `no_bpm` BK, `oka`→okas, `kode_mat`→wi_trn_bpms, `no_konv_bpm`→wi_trns, `user_bpm_ims`→users.

### `ims_spms` (ims_spm) — SPM IMS
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| no_bpm | text | nomor BPM | **FK → bpms.no_bpm** (many-to-one) |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| kode_mat / spec | text | kode & spesifikasi material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| jml_bpm | text | jumlah BPM | data |
| satuan | text | satuan | data |
| no_spm | text | nomor SPM | **BK** (unik) |
| jml_spm | text | jumlah SPM | data |
| tgl_spm | date | tanggal SPM | data |
| ket_spm | text | keterangan | data |
| no_konv_spm / no_konv_bpm | text | no konversi SPM/BPM | **FK → wi_trns.no_konv** (many-to-one) |
| user_spm_ims | text | user | **FK → users.kode_user** (many-to-one) |

### `jadwals` (jadwal) — jadwal MD
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_jadwal | text | id jadwal | **BK** |
| oka_jadwal | text | unit | **FK → okas** (many-to-one) |
| group_jadwal | text | grup | data |
| lot | text | lot | data |
| jadwal_wi | date | tanggal jadwal WI | data |
| jadwal_md2 | text | referensi md2 | **FK → md2s.md2_mdh** (one-to-many reverse: 1 md2 dipakai banyak jadwal) |

### `jenis_keretas` (jenis_kereta_tb) — master jenis kereta
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | varchar(4000) | id | **PK** |
| jenis_kereta | varchar(4000) | nama jenis kereta | **BK** (unik) + **Idxs** — di-referensi wi_trns.jenis_kereta, sttpp_fabs.jenis_kereta, ct_plants.jenis_kereta, wi_finishes.jenis_kereta (one-to-many) |
| ket_jenis | varchar(4000) | keterangan | data |

### `lap_wi` (lap_wi) — laporan WI (rencana)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| oka | text | kode unit | **FK → okas** (many-to-one) |
| lot | text | lot | data |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| renc_md_trn | date | rencana transfer MD | data |
| renc_wi_trn | date | rencana transfer WI | data |
| status_peng | text | status pengiriman | data |
| ket_status | text | keterangan status | data |

### `md_gambars` (md_gambar) — gambar MD
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| gambar | text | path/ref gambar | data |
| ada_gbr | text | flag ada gambar | data |

### `md_headers` (md_header) — header Manufacture Drawing (MD)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_md | text | id MD | **BK** (unik) |
| part_no | text | nomor part | **BK** (unik) + **Idxs** — di-referensi wi_trns, bobot_parts, memos, pieceparts, sttp_dalmats, lap_wi (one-to-many) |
| partno1 | text | nomor part alternatif | data |
| maspart | text | material part | data |
| nogbr | text | nomor gambar | **FK → drawings.nogbr** (many-to-one) |
| nopost | text | nomor post | data |
| title | text | judul MD | data |
| material | text | material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| type | text | tipe | data |
| t1/h1/b1/l1 | double | tebal/tinggi/lebar/panjang | data |
| nama_group | text | grup | data |
| qa_tc1 | double | nilai QA | data |
| qb_mc1 | text | nilai QB MC | data |
| sh1..sh3 | text | shop | data |
| ws1..ws8 | text | work station | data |
| des | text | deskripsi | data |
| tgl | date | tanggal | data |
| rev | text | revisi | data |
| mdh_md2 | text | relasi ke md2 | **BK** (unik) — di-referensi md2s.md2_mdh (one-to-many) |

### `mdrs` (mdr) — Manufacturing Defect Report
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_mdr | text | id MDR | **BK** |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| oka | text | unit | **FK → okas** (many-to-one) |
| tgl_mdr | date | tanggal | data |
| pic | text | PIC | **FK → users.kode_user** (many-to-one) |
| masalah | text | deskripsi masalah | data |
| lanjut | text | tindak lanjut | data |
| input | text | input | data |
| dampak | text | dampak | data |

### `memos` (memo) — memo part/produksi
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_memo | text | id memo | **BK** |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| title | text | judul | data |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| oka | text | unit | **FK → okas** (many-to-one) |
| status_p | text | status | data |
| ket | text | keterangan | data |
| status_mach | text | status machining | data |

### `memo_masuks` (memo_masuk) — memo masuk
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| nomer_memo | text | nomor memo | **BK** (unik) |
| bag_pengirim | text | bagian pengirim | data |
| perihal | text | perihal | data |
| penerima | text | penerima | data |
| pemroses | text | pemroses | data |
| tgl_terima | date | tanggal terima | data |
| keterangan | text | keterangan | data |
| user_memo_masuk | text | user | **FK → users.kode_user** (many-to-one) |
| nama_file | text | file lampiran | data |

### `memo_proses_masuks` (memo_proses_masuk) — memo proses masuk
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| no_proses_masuk | text | nomor proses masuk | **BK** (unik) |
| kode_mat_memo | text | kode material memo | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| spec_mat_memo | text | spesifikasi memo | data |
| qty / satuan | text | qty & satuan | data |
| no_memo_spp | text | nomor memo SPP | **FK → spms.no_spm** (many-to-one) |
| kode_mat_spp / spec_mat_spp / qty_spp | text | material SPP | data |
| no_memo_bpm | text | nomor memo BPM | **FK → bpms.no_bpm** (many-to-one) |
| kode_mat_bpm / spec_mat_bpm / qty_bpm | text | material BPM | data |
| tgl_memo_bpm | date | tanggal memo BPM | data |
| user_proses_masuk | text | user | **FK → users.kode_user** (many-to-one) |

### `mesins` (mesin) — master mesin
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_mesin | varchar(4000) | id mesin | **BK** |
| kode_mesin | varchar(4000) | kode mesin | **BK** (unik) + **Idxs** — di-referensi barcode_details.kode_mesin, barcode_starts.kode_mesin, tool_*/con_* (one-to-many) |
| nama | varchar(4000) | nama mesin | data |
| id_proses | bigint | relasi ke proses | **FK → proses.id_proses** (many-to-one) |

### `ncrs` (ncr) — Non-Conformance Report
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_ncr | text | id NCR | **BK** |
| sjn_ppart | text | nomor SJN piece part | **FK → piecepart_datangs.sjn_ppart** (many-to-one) |
| no_part | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| oka | text | unit | **FK → okas** (many-to-one) |
| qty_ncr | text | qty NCR | data |
| tgl_ncr | date | tanggal | data |
| diperbaiki | text | flag diperbaiki | data |
| buatbaru | text | flag dibuat baru | data |
| ket_ncr | text | keterangan | data |

### `notifications` (notifications) — notifikasi (struktur siap Laravel)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| user_id | text | penerima | **FK → users** (many-to-one) |
| message | text | pesan | data |
| is_read | bigint | flag dibaca | data (boolean) |
| created_at | datetime | waktu | timestamp |
| related_table / related_id | text | referensi record | data (polimorfik) |

### `okas` (oka) — master unit/order kereta
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| oka | text | kode unit | **PK/BK** (unik) + **Idxs** — di-referensi hampir semua tabel transaksi (wi_trns, barcodes, exps, bpms, spms, cp_details, dst.) (one-to-many) |
| kode_proyek | text | kode proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| wbs | text | WBS | data |
| ket | text | keterangan | data |
| total_car | text | jumlah gerbong | data |
| total_lot | bigint | total lot | data |
| jig / prototype / bogie / ktidur | text | jenis produk | data |
| total_lot1..total_lot4 | bigint | total lot per jenis | data |
| tgl_oka | date | tanggal oka | data |
| label_proyek | text | label proyek | data |
| ket_proyek | text | keterangan proyek | data |
| tampil_g | text | flag tampil grafik | data |

### `pieceparts` (piecepart) — stock piece part
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_piecepart | bigint | id | **BK** |
| no_piecepart | text | nomor piece part | **BK** (unik) |
| no_part1 | text | part pengirim | **FK → piecepart_datangs.no_part** (many-to-one) |
| no_part2 | text | part penerima | **FK → md_headers.part_no** (many-to-one) |
| proyek1 | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| oka1 | text | unit | **FK → okas** (many-to-one) |
| group1 | text | grup | data |
| qty_car | text | qty per car | data |
| qty_datang1 | text | qty datang | data |
| qty_ambil | text | qty diambil | data |
| qty_total | text | total qty | data |
| ket_piecepart | text | keterangan | data |

### `piecepart_datangs` (piecepart_datang) — penerimaan piece part
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_ppart_datang | text | id | **BK** |
| sjn_ppart | text | nomor SJN | **BK** (unik) + **Idxs** — di-referensi ncrs.sjn_ppart (one-to-many) |
| batc | text | batch | data |
| no_part | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| no_part2 | text | part alternatif | data |
| nama_part | text | nama part | data |
| oka | text | unit | **FK → okas** (many-to-one) |
| qty_datang | text | qty datang | data |
| tgl_datang | date | tanggal datang | data |
| ket_ppart_datang | text | keterangan | data |

### `pipts` (pipt) — dokumen PIPT
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | text | id | **PK** |
| no_doc | text | nomor dokumen | **BK** (unik) |
| nama_doc | text | nama dokumen | data |
| oka | text | unit | **FK → okas** (many-to-one) |
| wbs | text | WBS | data |
| jenis_doc | text | jenis dokumen | data |
| tgl_terima / tgl_kirim | date | tgl terima/kirim | data |
| revisi | text | revisi | data |
| bag_penerima | text | bagian penerima | data |
| no_exp | text | nomor ekspedisi | **FK → exps.no_exp** (many-to-one) |

### `proses` (proses) — master proses
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_proses | text | id proses | **BK** + **Idxs** — di-referensi mesins.id_proses (one-to-many) |
| kode_proses | text | kode proses | **BK** (unik) + **Idxs** — di-referensi barcode_details.proses, barcode_starts.proses |
| nama | text | nama proses | data |
| id_kel | text | id kelompok | data |

### `proyeks` (proyek) — master proyek
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| kode_proyek | varchar(4000) | kode proyek | **PK/BK** (unik) + **Idxs** — di-referensi okas, wi_trns, drawings, md_gambars, lap_wi, bobot_parts, grafik_md, memos (one-to-many) |
| nama | varchar(4000) | nama proyek | data |
| aktif | varchar(4000) | status aktif | data |

### `self_checks` (self_check) — self check part
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_self_check | text | id | **BK** |
| no_part | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| nama | text | nama | data |
| rev | text | revisi | data |
| oka | text | unit | **FK → okas** (many-to-one) |
| lot | text | lot | data |
| qty_lot / qty | text | qty | data |
| tgl_kirim | date | tanggal kirim | data |

### `spms` (spm) — Surat Permintaan Material
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| no_bpm | text | nomor BPM asal | **FK → bpms.no_bpm** (many-to-one: 1 BPM bisa punya banyak SPM) |
| oka | text | unit | **FK → okas** (many-to-one) |
| kode_mat | text | material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| spec | text | spesifikasi | data |
| jml_bpm | bigint | jumlah dari BPM | data |
| satuan | text | satuan | data |
| no_cp | text | nomor CP | **FK → cp_details.cp_no** (many-to-one) |
| no_spm | text | nomor SPM | **BK** (unik) + **Idxs** — di-referensi spm_orgs.no_spm_org, gases.no_spm (one-to-many) |
| jml_spm | bigint | jumlah SPM | data |
| tgl_spm | date | tanggal SPM | data |
| ket_spm | text | keterangan | data |
| no_konv_spm | text | no konversi | **FK → wi_trns.no_konv** (many-to-one) |
| user_spm | text | user | **FK → users.kode_user** (many-to-one) |

### `spm_ims` (spm_ims) — SPM unit IMS
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| (pola `spms`) | | no_bpm, oka, kode_mat, spec, jml_bpm, satuan, no_cp, no_spm, jml_spm, tgl_spm, ket_spm, no_konv_spm | relasi identik dgn `spms` (no_bpm→bpms, no_spm BK, no_cp→cp_details, no_konv_spm→wi_trns, oka→okas) |
| head_numbr | text | nomor kepala dokumen | data |
| penerbit_bpm | text | penerbit BPM | data |
| user_spm_ims | text | user | **FK → users.kode_user** (many-to-one) |

### `spm_orgs` (spm_org) — tanda tangan organisasi SPM
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_spm_org | text | id | **BK** |
| no_spm_org | text | nomor SPM | **FK → spms.no_spm** — **one-to-one** (1 SPM memiliki 1 set tanda tangan) |
| diminta | text | penandatangan "diminta oleh" | **FK → users.kode_user** (many-to-one) |
| mengetahui | text | penandatangan "mengetahui" | **FK → users.kode_user** (many-to-one) |
| diterima | text | penandatangan "diterima oleh" | **FK → users.kode_user** (many-to-one) |
| tgl_spm_org | date | tanggal | data |

### `spm_ims_orgs` (spm_ims_org) — tanda tangan SPM IMS
> Relasi identik dgn `spm_orgs`: `no_spm_ims_org` → spm_ims.no_spm (**one-to-one**), diminta/mengetahui/diterima → users (many-to-one).

### `spm_ppcbs` (spm_ppcb) — SPM PPCB
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_spm_ppcb | text | id | **BK** |
| no_spm_ppcb | text | nomor SPM | **BK** (unik) |
| no_cp_ppcb | text | nomor CP | **FK → cp_details.cp_no** (many-to-one) |
| no_bpm_ppcb | text | nomor BPM | **FK → bpms.no_bpm** (many-to-one) |
| komat_ppcb | text | kode material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| qty_bpm_ppcb | text | qty BPM | data |
| oka_ppcb | text | unit | **FK → okas** (many-to-one) |
| proyek_ppcb | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| lot_ppcb | text | lot | data |
| qty_spm_ppcb | text | qty SPM | data |
| pengirim_spm_ppcb / mengetahui_spm_ppcb / penerima_spm_ppcb | text | tanda tangan | **FK → users.kode_user** (many-to-one) |
| tgl_spm | date | tanggal | data |
| ket_spm_ppcb | text | keterangan | data |

### `sttp_dalmats` (sttp_dalmat) — STTP kirim dalmat
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_sttp_dalmat | bigint | id | **BK** |
| no_sjn | text | nomor SJN | **BK** (unik) |
| no_sttp | text | nomor STTP | **BK** (unik) |
| no_part | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| nama_part | text | nama part | data |
| oka | text | unit | **FK → okas** (many-to-one) |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| lot | text | lot | data |
| qty_kirim | text | qty kirim | data |
| tgl_kirim | date | tanggal kirim | data |
| satuan | text | satuan | data |
| keterangan | text | keterangan | data |

### `sttp_dalmat_datangs` (sttp_dalmat_datang) — STTP datang dalmat
> Relasi identik dgn `sttp_dalmats` (no_sjn/no_sttp BK, no_part→md_headers, oka→okas, proyek→proyeks) + field `qty_datang`, `tgl_datang` (data).

### `sttp_dalmat_datang_orgs` (sttp_dalmat_datang_org) — tanda tangan penerimaan STTP datang
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_sttp_org | text | id | **BK** |
| sttp_datang_org | text | nomor STTP | **FK → sttp_dalmat_datangs.no_sttp** — **one-to-one** (1 STTP datang memiliki 1 set tanda tangan) |
| diminta / mengetahui / diterima | text | penandatangan | **FK → users.kode_user** (many-to-one) |
| tgl_sttp_datang | date | tanggal | data |

### `sttpps` (sttpp) — header STTP
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| nosttpp | text | nomor STTP | **BK** (unik) + **Idxs** — di-referensi sttpp_details.nosttpp, sttpp_fabs.no_sttp (one-to-many) |
| oka | text | unit | **FK → okas** (many-to-one) |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| kontrak | text | nomor kontrak | data |
| lppb | text | nomor LPPB | data |
| tgl_lppb | date | tanggal LPPB | data |
| tgl_krm | date | tanggal kirim | data |
| dari / ke | text | asal & tujuan | data |
| pengirim / penerima | text | pengirim & penerima | **FK → users.kode_user** (many-to-one) |
| detail | bigint | jumlah detail | data |
| tahun | text | tahun | data |

### `sttpp_fabs` (sttpp_fab) — detail STTP fabrikasi
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_sttp_fab | text | id | **BK** |
| no_sttp | text | nomor STTP | **FK → sttpps.nosttpp** (one-to-many: 1 header STTP banyak detail) |
| no_part | text | nomor part | **FK → md_headers.part_no** (many-to-one) |
| name_part | text | nama part | data |
| oka | text | unit | **FK → okas** (many-to-one) |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| lot | text | lot | data |
| qty_lot / qty_kirim | text | qty per lot & qty kirim | data |
| satuan | text | satuan | data |
| tgl_kirim | date | tanggal kirim | data |
| dari / ke | text | asal & tujuan | data |
| pengirim / penerima | text | pengirim & penerima | **FK → users.kode_user** (many-to-one) |
| keterangan | text | keterangan | data |
| jenis_kereta | text | jenis kereta | **FK → jenis_keretas.jenis_kereta** (many-to-one) |
| user_sttp | text | user | **FK → users.kode_user** (many-to-one) |
| tgl_input | date | tanggal input | data |
| rev | text | revisi | data |

### `tool_fabs` (tool_fab) — tool fabrikasi (struktur identik con_fab_ims)
### `tool_fabs_ims` (tool_fab_ims) — tool fabrikasi IMS
### `tool_fins` (tool_fin) — tool finishing (struktur identik tool_fab)
### `tool_fins_ims` (tool_fin_ims) — tool finishing IMS
> Relasi identik dgn `con_fab_ims`: `id` PK, `kode_alat` BK (unik), `kode_mesin` → mesins (many-to-one), sisanya data. `tool_fabs_ims`/`tool_fins_ims` + field `user_tool_ims` → users (many-to-one).

### `wi_finishes` (wi_finish) — WI selesai
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id | **PK** |
| oka | text | unit | **FK → okas** (many-to-one) |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) |
| lot | text | lot | data |
| part_no | text | nomor part | **FK → wi_trns.part_no** (many-to-one) |
| qty_fin | double | qty finish | data |
| tgl_fin | date | tanggal finish | data |
| jenis_kereta | text | jenis kereta | **FK → jenis_keretas.jenis_kereta** (many-to-one) |
| cetak | text | flag cetak | data |
| no_sttp | text | nomor STTP | **FK → sttpps.nosttpp** (many-to-one) |
| pengirim / penerima | text | pengirim & penerima | **FK → users.kode_user** (many-to-one) |
| keterangan | text | keterangan | data |

### `wi_trns` (wi_trn) — tabel utama Work Instruction (transaksi WI)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id | bigint | id (nullable, akan jadi PK) | **PK** |
| oka | text | kode unit | **FK → okas** (many-to-one) + **Idxs** |
| proyek | text | proyek | **FK → proyeks.kode_proyek** (many-to-one) + **Idxs** |
| wbs | text | WBS | data |
| lot | text | nomor lot | data + **Idxs** |
| part_no | text | nomor part | **FK → md_headers.part_no** (many-to-one) + **Idxs** |
| partno1 | text | nomor part alternatif | data |
| qty_lot | text | qty per lot | data |
| tgl_trn | date | tanggal transfer WI (status turun) | data |
| no_ctplan | text | nomor cutting plan | **FK → cp_details.cp_no** (many-to-one) + **Idxs** |
| tgl_ctplan | date | tanggal CP | data |
| idbarang | text | kode material | **FK → wi_trn_bpms.idbarang** (many-to-one) |
| no_bpm | text | nomor BPM | **FK → bpms.no_bpm** (many-to-one) |
| tgl_bpm | date | tanggal BPM | data |
| barcode | text | kode barcode | **FK → barcodes.no_konv** (one-to-one) |
| status | text | status | data |
| ket | text | keterangan | data |
| jenis_kereta | text | jenis kereta | **FK → jenis_keretas.jenis_kereta** (many-to-one) |
| no_konv | text | nomor konversi | **BK** (unik) + **Idxs** — di-referensi barcodes.no_konv, bpms.no_konv_bpm, spms.no_konv_spm (one-to-many) |
| aproved / aproved1 / aproved2 | text | approval | data (boolean) |
| mat_cp / spek_mat_cp | date | tanggal material CP | data |
| planer | date | tanggal planning | data |
| no_exp | text | nomor ekspedisi | **FK → exps.no_exp** (many-to-one) |
| qty_cp | text | qty CP | data |
| mat_sisa | text | sisa material | data |
| ket_wi / keterangan | text | keterangan WI | data |
| rev | text | revisi | data |
| ket_mat / status_mat | text | keterangan & status material | data |
| no_pr / no_po | text | nomor PR / PO | data |
| eta | text | estimasi kedatangan | data |
| id_bpm_cp | text | relasi BPM CP | **FK → bpm_cps** (many-to-one) |
| qty_car | text | qty per car | data |
| tinggi / tebal / lebar / panjang | text | dimensi part | data |
| name | text | nama | data |
| user_cp | text | user CP | **FK → users.kode_user** (many-to-one) |
| user_print | text | user cetak | **FK → users.kode_user** (many-to-one) |
| type | text | tipe | data |
| group_wi | text | grup WI | data |
| konv_wi | text | no konversi WI | **FK → wi_trns.no_konv** (self-reference, one-to-one) |
| target_wi | date | target WI | data |
| note | text | catatan | data |

### `wi_trn_bpms` (wi_trn_bpm) — master material WI (kode material + spesifikasi)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| id_wi_trn_bpm | text | id | **BK** |
| idbarang | text | kode material | **PK/BK** (unik) + **Idxs** — di-referensi wi_trns.idbarang, bpms.kode_mat, bpm_cps.kode_mat, ct_plants.idbarang, gases.kode_gas, spms.kode_mat, cp_details.mat_cp (one-to-many) |
| spec | text | spesifikasi | data |
| ket | text | keterangan | data |
| user | text | user | **FK → users.kode_user** (many-to-one) |

## Tabel Legacy (field, hanya untuk arsip)

### `user_legacy` (user) — login aplikasi lama
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| kode_user | text | id user | **PK/BK** (unik) |
| nama_user | text | nama | data |
| username | text | username | **BK** (unik, dipakai login) |
| password | text | password (plaintext) | data (akan jadi hash bcrypt di Laravel) |
| level | text | level: admin, operator, operator1-5 | **BK** — nilai di-seed ke `roles` Spatie |

### `userb_legacy` (userb) — backup user (struktur identik user_legacy)
### `user_access_legacy` (users lama) — RBAC lama (orphan)
| Field | Tipe | Arti | Peran & Relasi |
|---|---|---|---|
| Id / UserName / Password | text | identitas (kapital) | data |
| nama / j_kel / alamat | text | biodata | data |
| kode_proyek / oka / md / wi / wi_finish / sttpp / st_work / fab / painting / barcode / utility | bigint | flag akses per menu | data — akan digantikan `permissions` Spatie |
| id_kel | text | id kelompok | data |
| klp_st / klp_weld / klp_mch / klp_int / klp_wir / klp_pmk / klp_pip / klp_pa / klp_final | text | kelompok kerja | data |
| status | text | status | data |

## Catatan / Keputusan Terbuka

- `cp` vs `cp_1`: `cp_1` yang dipakai aktif sebagai tabel Cutting Plan (dikonfirmasi dari `cp/data.php`, `cp_bpm/`, `cp_lap/`). `cp` masih dipakai di `grafik/proses.php` dan `lap_wi/data.php`. Perlu keputusan akhir apakah digabung sebelum rename.
- Pemetaan menu per level di `_header.php` (aplikasi lama) akan menjadi basis pembuatan `permissions` di sistem Laravel baru.
- Semua relasi di Lampiran A adalah relasi **logis** (dari kode JOIN). Saat migrasi (Fase 2) masing-masing diubah menjadi kolom FK numerik + constraint, dan relasi *many-to-many* dipindahkan ke tabel pivot (`bpm_cps`, `exp_cps`) sesuai konvensi Laravel.
