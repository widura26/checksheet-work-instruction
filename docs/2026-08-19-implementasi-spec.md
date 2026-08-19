# Implementasi Spec: Migrations + Models (uuid)

Konvensi baku yang HARUS diikuti SEMUA agent. Dokumen ini + `docs/2026-08-19-rename-tabel-laravel-design.md` (Lampiran A) adalah sumber kebenaran. Lampiran A memberi: nama tabel, daftar field, arti, Peran & Relasi (PK/BK/FK/Idxs).

## 1. Konvensi Migration

- Setiap tabel satu migration file: `database/migrations/2026_08_19_XXXXXX_create_<table>_table.php` (XXXXXX nomor urut unik global, jangan duplikat).
- Template WAJIB:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TABLE', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // ... kolom bisnis, FK, timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TABLE');
    }
};
```

- **PK**: `$table->uuid('id')->primary();` untuk SEMUA tabel.
- **Kolom FK**: `$table->foreignUuid('xxx_id')->nullable()->index();` TANPA `->constrained()` di create-migration. Constraint FK ditambahkan lewat migration terpisah (fase 2) oleh koordinator. Contoh: `$table->foreignUuid('oka_id')->nullable()->index();`
- **Pengecualian FK ke `users`**: `users.id` bertipe `bigint` (dari `$table->id()` di migration bawaan Laravel), bukan uuid. Maka SEMUA kolom FK yang menuju `users` memakai `$table->foreignId('xxx_id')->nullable()->index();` (bukan `foreignUuid`). Kolom yang terdampak: `user_id`, `pengirim_id`, `penerima_id`, `diminta_id`, `mengetahui_id`, `diterima_id`, `user_cp_id`, `user_print_id`.
- **FK ke tabel yang TIDAK dibuat** (target: `md2s`, `md_laps`, `sttpp_details`, `spms` legacy): `$table->uuid('xxx_id')->nullable()->index();` (uuid polos, tanpa foreignUuid).
- **Kolom business key**: `$table->string('no_bpm', ...)->unique();` untuk yang berlabel **BK (unik)**; `->index()` untuk yang berlabel **Idxs**. Composite unique (mis. `part_no`+`PR` di `barcodes`): dua kolom `string` + `$table->index(['part_no', 'PR']);`.
- **Timestamps**: `$table->timestamps();` di akhir.
- Jangan pakai komentar di kode.

## 2. Konvensi Model

Template WAJIB (pola `app/Models/Proses.php` yang sudah ada):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NamaModel extends Model
{
    use HasFactory;

    protected $table = 'TABLE';

    protected $guarded = ['id'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // relasi...
}
```

- Nama file model: `app/Models/<NamaModel>.php`.
- Nama model = `Str::studly(Str::singular(TABLE))`, mis. `wi_trns` → `WiTrn`, `md_headers` → `MdHeader`, `spm_ppcbs` → `SpmPpcb`, `jenis_keretas` → `JenisKereta`, `tool_fabs_ims` → `ToolFabsIm`? → TETAP pakai `ToolFabIm`. Aturan nama model pakai daftar eksplisit per tabel di bagian 4.
- **Relasi**: definisikan `belongsTo` untuk SETIAP kolom FK yang mengarah ke tabel yang ADA (target `users`, `okas`, `proyeks`, `processes`, dst.). Method relasi pakai `camelCase` nama target, mis. `oka()`, `proyek()`, `user()`, `material()`, `mesin()`, `bpm()`, `cpDetail()`, `exp()`, `mdHeader()`, `sttpp()`, `piecepartDatang()`, `jenisKereta()`, `process()`. Untuk FK ganda ke tabel yang sama, beri nama unik (mis. `pengirim()`, `penerima()`; `userCp()`, `userPrint()`).
- **FK yang targetnya TIDAK ada** (md2s, md_laps, sttpp_details, spms): jangan buat relasi; sisakan kolom saja.
- **belongsToMany** untuk pivot `bpm_cps` dan `exp_cps`: `bpm()` & `cpDetail()`.
- Inverse `hasMany` hanya jika jelas dari Lampiran A (one-to-many). Prioritas utama: `belongsTo` di sisi anak.

## 3. Mapping Tipe Kolom

| Tipe lama (Lampiran A) | Tipe Laravel |
|---|---|
| `text` (kode/nama/nomor pendek) | `string` nullable |
| `text` (deskripsi/keterangan panjang: des, ket, keterangan, masalah, alamat, note) | `text` nullable |
| `varchar(4000)` (kode/nama) | `string` nullable |
| `bigint` (qty/jumlah: qty, jumlah, jml_bpm, jml_spm, total_lot, detail, is_read, total_car) | `bigInteger` nullable |
| `double` (bobot, t1/h1/b1/l1, qa_tc1, qty_fin) | `decimal(12, 4)` nullable |
| `date` | `date` nullable |
| `datetime` | `dateTime` nullable |
| flag 0/1 (aproved, aproved1, aproved2, diperbaiki, buatbaru, tampil_g, ada_gbr, cetak, aktif) | `boolean` nullable default false |
| kolom `id` bigint lama (nullable) | TIDAK dibuat — diganti `uuid('id')->primary()` |
| `id_md`, `id_cp`, `id_mesin`, dst. (identitas tekstual) | `string` + unique (sesuai BK Lampiran A) |

## 4. Daftar Tabel: nama model, urutan nomor migration, dan FK

> Format per tabel: `TABLE | NamaModel | nomor | daftar FK (kolom → target)`. FK `xxx_id → users` berarti `foreignUuid('xxx_id')->nullable()->index()` (target ada). `(SKIP→tabel)` berarti uuid polos tanpa constraint.

1. `audit_logs` | AuditLog | 101001 | user_id → users
2. `okas` | Oka | 101002 | proyek_id → proyeks
3. `proyeks` | Proyek | 101003 | (tidak ada FK)
4. `jenis_keretas` | JenisKereta | 101004 | (tidak ada FK)
5. `drawings` | Drawing | 101005 | proyek_id → proyeks
6. `mesins` | Mesin | 101006 | process_id → processes
7. `wi_trn_bpms` | WiTrnBpm | 101007 | user_id → users
8. `md_headers` | MdHeader | 101008 | drawing_id → drawings; material_id → wi_trn_bpms
9. `processes` | (sudah ada — JANGAN buat)
10. `exp_orgs` | ExpOrg | 101010 | exp_id → exps
11. `cps` | Cp | 101011 | oka_id → okas; md_header_id → md_headers; exp_id → exps; bpm_cp_id → bpm_cps; exp_cp_id → exp_cps; user_id → users (dari pic_cp)
12. `cp_details` | CpDetail | 101012 | exp_id → exps; md_header_id → md_headers; oka_id → okas; material_id → wi_trn_bpms; user_id → users (dari planer)
13. `exps` | Exp | 101013 | oka_id → okas; cp_detail_id → cp_details; md_header_id → md_headers; pengirim_id → users; penerima_id → users
14. `exp_cps` | ExpCp | 101014 | oka_id → okas; cp_detail_id → cp_details; pengirim_id → users; penerima_id → users
15. `barcodes` | Barcode | 101015 | oka_id → okas; wi_trn_id → wi_trns; user_id → users (dari user_barcode)
16. `bpms` | Bpm | 101016 | oka_id → okas; material_id → wi_trn_bpms; wi_trn_id → wi_trns (dari no_konv_bpm); user_id → users (dari user)
17. `bpm_cps` | BpmCp | 101017 | bpm_id → bpms; cp_detail_id → cp_details; material_id → wi_trn_bpms; oka_id → okas (dari oka_bpm); user_id → users (dari user_bpm_cp)
18. `bpm_ims` | BpmIm | 101018 | oka_id → okas; material_id → wi_trn_bpms; wi_trn_id → wi_trns (dari no_konv_bpm); user_id → users (dari user_bpm_ims)
19. `con_fab_ims` | ConFabIm | 101019 | mesin_id → mesins
20. `con_fin_ims` | ConFinIm | 101020 | mesin_id → mesins
21. `gases` | Gas | 101021 | material_id → wi_trn_bpms; bpm_id → bpms; spm_id → spms (SKIP→uuid polos); user_id → users (dari user_gas)
22. `grafik_md` | GrafikMd | 101022 | proyek_id → proyeks (dari g_proyek)
23. `ims_bpms` | ImsBpm | 101023 | oka_id → okas; material_id → wi_trn_bpms; wi_trn_id → wi_trns (dari no_konv_bpm); user_id → users (dari user_bpm_ims)
24. `ims_spms` | ImsSpm | 101024 | bpm_id → bpms; oka_id → okas; material_id → wi_trn_bpms; wi_trn_id → wi_trns (dari no_konv_spm/bpm); user_id → users (dari user_spm_ims)
25. `jadwals` | Jadwal | 101025 | oka_id → okas (dari oka_jadwal); md2_id → md2s (SKIP→uuid polos)
26. `lap_wi` | LapWi | 101026 | md_header_id → md_headers; oka_id → okas; proyek_id → proyeks
27. `md_gambars` | MdGambar | 101027 | proyek_id → proyeks; md_header_id → md_headers
28. `mdrs` | Mdr | 101028 | proyek_id → proyeks; oka_id → okas; user_id → users (dari pic)
29. `memos` | Memo | 101029 | md_header_id → md_headers; proyek_id → proyeks; oka_id → okas
30. `memo_masuks` | MemoMasuk | 101030 | user_id → users (dari user_memo_masuk)
31. `memo_proses_masuks` | MemoProsesMasuk | 101031 | material_id → wi_trn_bpms (dari kode_mat_memo); spm_id → spms (SKIP→uuid polos); bpm_id → bpms; user_id → users (dari user_proses_masuk)
32. `ncrs` | Ncr | 101032 | piecepart_datang_id → piecepart_datangs; md_header_id → md_headers; oka_id → okas
33. `pieceparts` | Piecepart | 101033 | piecepart_datang_id → piecepart_datangs; md_header_id → md_headers; proyek_id → proyeks (dari proyek1); oka_id → okas (dari oka1)
34. `piecepart_datangs` | PiecepartDatang | 101034 | md_header_id → md_headers; oka_id → okas
35. `pipts` | Pipt | 101035 | oka_id → okas; exp_id → exps (dari no_exp)
36. `self_checks` | SelfCheck | 101036 | md_header_id → md_headers; oka_id → okas
37. `spm_ims` | SpmIm | 101037 | oka_id → okas; spm_ims_org_id → spm_ims_orgs; bpm_id → bpms; material_id → wi_trn_bpms; cp_detail_id → cp_details (dari no_cp); wi_trn_id → wi_trns (dari no_konv_spm); user_id → users (dari user_spm_ims)
38. `spm_ims_orgs` | SpmImOrg | 101038 | spm_ims_id → spm_ims (one-to-one); diminta_id → users; mengetahui_id → users; diterima_id → users
39. `spm_orgs` | SpmOrg | 101039 | spm_id → spms (SKIP→uuid polos); diminta_id → users; mengetahui_id → users; diterima_id → users
40. `spm_ppcbs` | SpmPpcb | 101040 | cp_detail_id → cp_details; bpm_id → bpms; material_id → wi_trn_bpms (dari komat_ppcb); oka_id → okas (dari oka_ppcb); proyek_id → proyeks (dari proyek_ppcb); pengirim_id → users; mengetahui_id → users; penerima_id → users
41. `sttp_dalmats` | SttpDalmat | 101041 | md_header_id → md_headers; oka_id → okas; proyek_id → proyeks
42. `sttp_dalmat_datangs` | SttpDalmatDatang | 101042 | md_header_id → md_headers; oka_id → okas; proyek_id → proyeks
43. `sttp_dalmat_datang_orgs` | SttpDalmatDatangOrg | 101043 | sttp_dalmat_datang_id → sttp_dalmat_datangs (one-to-one); diminta_id → users; mengetahui_id → users; diterima_id → users
44. `sttpps` | Sttpp | 101044 | oka_id → okas; proyek_id → proyeks; pengirim_id → users; penerima_id → users
45. `sttpp_fabs` | SttppFab | 101045 | sttpp_id → sttpps; md_header_id → md_headers; oka_id → okas; proyek_id → proyeks; jenis_kereta_id → jenis_keretas; pengirim_id → users; penerima_id → users; user_id → users (dari user_sttp)
46. `tool_fabs` | ToolFab | 101046 | mesin_id → mesins
47. `tool_fabs_ims` | ToolFabIm | 101047 | mesin_id → mesins; user_id → users (dari user_tool_ims)
48. `tool_fins` | ToolFin | 101048 | mesin_id → mesins
49. `tool_fins_ims` | ToolFinIm | 101049 | mesin_id → mesins; user_id → users (dari user_tool_ims)
50. `wi_finishes` | WiFinish | 101050 | oka_id → okas; proyek_id → proyeks; wi_trn_id → wi_trns (dari part_no); jenis_kereta_id → jenis_keretas; sttpp_id → sttpps (dari no_sttp); pengirim_id → users; penerima_id → users
51. `barcode_details` | BarcodeDetail | 101051 | barcode_id → barcodes; process_id → processes (dari proses); user_id → users (dari user_brcode); mesin_id → mesins (dari kode_mesin)
52. `barcode_starts` | BarcodeStart | 101052 | barcode_id → barcodes; process_id → processes (dari proses); user_id → users (dari user_brcode); mesin_id → mesins (dari kode_mesin)
53. `bobot_parts` | BobotPart | 101053 | oka_id → okas; proyek_id → proyeks; md_header_id → md_headers (dari part_no)
54. `ct_plants` | CtPlant | 101054 | oka_id → okas; proyek_id → proyeks (dari kode_proyek); cp_detail_id → cp_details (dari no_ctplan); bpm_id → bpms (dari bpm_no); material_id → wi_trn_bpms (dari idbarang); jenis_kereta_id → jenis_keretas (dari jenis_kereta)
55. `wi_trns` | WiTrn | 101055 | oka_id → okas; proyek_id → proyeks; material_id → wi_trn_bpms (dari idbarang); md_header_id → md_headers; jenis_kereta_id → jenis_keretas; bpm_cp_id → bpm_cps (dari no_ctplan); cp_id → cps (dari no_ctplan); memo_id → memos (dari part_no+oka); spm_ims_id → spm_ims (dari no_ctplan); bpm_id → bpms (dari no_bpm); barcode_id → barcodes (dari barcode, one-to-one); exp_id → exps (dari no_exp); user_cp_id → users; user_print_id → users; parent_id → wi_trns (self-reference, dari konv_wi)

**Total tabel dibuat: 54.** Yang TIDAK dibuat: `users`, `processes`, `notifications`, `spms`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`, `activity_log`.

## 4b. Constraint FK (fase 2 — selesai)

Dibuat: `database/migrations/2026_08_19_101100_add_foreign_keys_to_checksheet_tables.php`.

- Berisi `Schema::table(...)` + `$table->foreign('xxx_id')->references('id')->on('target')->cascadeOnUpdate()->restrictOnDelete();` untuk SEMUA 177 kolom FK dari tabel yang ADA.
- Kolom FK yang **TIDAK** dapat constraint (target tabel tidak dibuat): `gases.spm_id`, `jadwals.md2_id`, `spm_orgs.spm_id`, `memo_proses_masuks.spm_id` (uuid polos).
- JANGAN panggil `foreignUuid()`/`foreignId()` di migration ini (kolom sudah ada) — pakai `$table->foreign(...)`.

## 5. Kolom business key per tabel (string, unique/index)

Baca Lampiran A kolom "Peran & Relasi" untuk tiap tabel:
- **BK (unik)** → `string()->unique()`
- **Idxs** → `string()->index()`
- composite BK (mis. `barcodes.part_no`+`PR`) → `$table->index(['part_no','PR']);`

Catatan penting per tabel (contoh — baca Lampiran A untuk detail lengkap field):
- `barcodes`: `no_konv` unique; `oka` string (kolom FK `oka_id` terpisah); `part_no` + `PR` composite index; `lot`, `part_no` index.
- `wi_trns`: `no_konv` unique + index; `oka`, `proyek`, `lot`, `part_no`, `no_ctplan` index.
- `okas`: `oka` unique (PK/BK).
- `proyeks`: `kode_proyek` unique (PK/BK).
- `mesins`: `kode_mesin` unique; `id_mesin` index/unique.
- `jenis_keretas`: `jenis_kereta` unique.
- `md_headers`: `part_no` unique + index; `id_md` unique; `mdh_md2` unique.
- `drawings`: `nogbr` unique.
- `bpms`: `no_bpm` unique.
- `spm_*`: `no_spm` unique (kecuali spm_ppcbs `no_spm_ppcb` unique).
- `sttpps`: `nosttpp` unique.
- `gases`: `no_botol` unique.
- `con_fab_ims`/`con_fin_ims`/`tool_*`: `kode_alat` unique.

## 6. Verifikasi yang HARUS dilakukan setiap agent

Setelah menulis SEMUA file di scope-nya, jalankan `php -l` pada setiap file:
```bash
php -l database/migrations/<file>.php
php -l app/Models/<file>.php
```
Semua harus output "No syntax errors detected". Perbaiki sampai bersih.

## 7. Output yang harus dikembalikan

Kembalikan: daftar file yang dibuat (migration + model per tabel), dan hasil `php -l` (semua clean).
