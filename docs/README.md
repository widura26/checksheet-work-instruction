# Dokumentasi teknis

Dokumen ini menjelaskan aplikasi **Sistem Monitoring Checksheet Work Instruction** apa adanya sesuai kode di branch aktif. Ringkasan untuk pengguna baru ada di [`README.md`](../README.md) di akar repositori.

## Tur berdasarkan kebutuhan

| Kebutuhan | Mulai dari |
| --- | --- |
| Saya baru dan ingin menjalankan aplikasi | [Getting started](getting-started.md) |
| Saya ingin paham bentuk aplikasi dan alur request | [Arsitektur](architecture.md) |
| Saya mencari route, parameter, atau response JSON | [Referensi route](reference/routes.md) |
| Saya ingin tahu tabel, kolom, dan relasi | [Model dan data](reference/data-model.md) |
| Saya perlu mengimpor BOM atau dataset SAP | [Panduan import](guides/imports.md) |
| Saya menangani kendala koneksi / mode offline | [Mode offline](guides/offline-mode.md) |
| Saya menangani login, peran, atau log aktivitas | [Auth & activity log](guides/auth-and-activity-log.md) |
| Saya ingin tahu mengapa desainnya begini | [Catatan desain](explanation/design-notes.md) |

## Konvensi

- Semua path relatif terhadap akar repositori.
- `APP_TIMEZONE` di `.env` tidak diisi di lingkungan contoh, sehingga default aplikasi adalah `UTC`. Tampilan activity log dan penjadwalan cleanup memakai `Asia/Jakarta` secara eksplisit di kode.
- Sebagian dokumentasi merujuk ke nomor baris. Nomor dapat bergeser setelah perubahan kode; gunakan nama class/method sebagai jangkar utama.
