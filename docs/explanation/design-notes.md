# Catatan desain dan utang teknis

Dokumen ini menjelaskan keputusan yang terlihat dari kode beserta konsekuensinya. Ini bukan daftar persetujuan formal, melainkan ringkasan agar perubahan berikutnya tidak mengulang masalah yang sama.

## Orientasi desain

### Login berbasis NIP, bukan email

`config/fortify.php:48` menetapkan `username => nip`, dan `FortifyServiceProvider` memvalidasi NIP sebagai 9 digit angka. Email tetap disimpan dan dipakai untuk reset password serta tampilan activity log. Konsekuensinya, semua pembuatan user harus mengisi `nip` yang unik; aksi Fortify `CreateNewUser` saat ini belum melakukannya, sehingga registrasi mandiri tidak menghasilkan akun yang bisa dipakai.

### Otorisasi berbasis nama peran

Tidak ada Gate atau Policy. Pembatasan dilakukan dengan middleware `role:admin` pada grup route dan pengecekan `hasRole('admin')` di sidebar serta beberapa view. Dampaknya: aturan akses mudah dibaca di `routes/web.php`, tetapi tiap endpoint baru harus dimasukkan ke grup yang tepat secara manual. Endpoint `GET /api/user` memakai `auth:sanctum` tanpa `auth.active`, sehingga tidak mengikuti aturan sesi web.

### Audit log yang fail-open

`ActivityLogger::record()` menangkap semua exception dan hanya menulis `Log::warning`. Pilihan ini menjaga operasi bisnis tetap berjalan saat pencatatan gagal, tetapi kegagalan audit tidak terlihat di UI. Kolom `feature` di tabel `activity_log` wajib diisi, jadi pemanggilan langsung ke API spatie tanpa mengisi `feature` akan gagal.

Data sensitif diredaksi berdasarkan nama kunci. `redact()` hanya menelusuri array, bukan objek. Jika properti baru berupa objek, pastikan mengubahnya ke array sebelum dicatat.

### Zona waktu ganda

Default `APP_TIMEZONE` adalah `UTC` dan `.env.example` tidak mengisinya, sehingga timestamp di database mengikuti UTC. Dua tempat menggunakan `Asia/Jakarta` secara eksplisit:

- `ActivityLogController`: filter tanggal dikonversi dari Asia/Jakarta ke UTC, tampilan dikonversi kembali ke `d-m-Y H:i:s` Asia/Jakarta.
- `Console\Kernel`: cleanup log dijadwalkan pukul 02:00 Asia/Jakarta.

Perubahan on-device di branch aktif menambahkan konversi ini; versi ter-commit sebelumnya memakai waktu aplikasi apa adanya dan retensi 180 hari.

### Dua jalur import yang tumbuh terpisah

Import BOM rekap memakai job + pencarian payload di tabel `jobs`, sedangkan import rekap material memakai `ImportTracker` berbasis cache tetapi dijalankan `dispatchSync()` di request. Perbedaan ini bukan pilihan arsitektur yang disengaja di satu titik, melainkan hasil pertumbuhan fitur. Konsekuensinya: status tidak bisa dipakai lintas jalur. Jalur CSV SAP lama dan tabel `sap_datasets`-nya sudah dihapus. Detail ada di [panduan import](../guides/imports.md).

Dua keputusan import yang perlu diingat:

- `BomDatasetImport` melakukan **replace penuh** `bill_of_material_dataset` setiap sukses. Ini menyederhanakan sinkronisasi rekap, tetapi file yang tidak lengkap akan menghapus data lama.
- `ImportTracker::completed()` menaikkan `mp:version`, yang dipakai cache dashboard sebagai suffix key. Ini cara aplikasi memaksa data baru tampil tanpa menunggu TTL 300 detik, tetapi failed import tidak menaikkan versi.

### File BOM bukan dihasilkan aplikasi

Paket dompdf terpasang, tetapi tidak dipakai. Dokumen BOM adalah file PDF/XLS/XLSX yang diunggah admin, disimpan di disk `public` pada folder `bill-of-material-revisions/rev-{versi}/`, lalu ditayangkan inline atau diunduh apa adanya. Jadi "revisi" adalah versi file, bukan PDF yang dirender ulang dari data.

### Fondasi offline yang belum tersambung

Service worker, IndexedDB queue, dan middleware idempotensi sudah dibangun, tetapi route yang dituju antrean offline belum terdaftar dan middleware belum dipasang ke route produksi. Perlakukan mode offline sebagai fondasi, bukan fitur yang sudah aktif penuh. Detail ada di [mode offline](../guides/offline-mode.md).

### PHPUnit memakai MySQL

Baris konfigurasi SQLite di `phpunit.xml:24-25` sengaja dikomentari. Test feature memakai transaksi pada database MySQL sesuai `.env`. Ini membuat test lebih dekat ke produksi, tetapi setiap developer dan CI harus menyiapkan database testing. Beberapa test lokal (idempotensi) beralih ke SQLite in-memory sendiri.

## Utang teknis yang diketahui

| Area | Masalah | Dampak |
| --- | --- | --- |
| Registrasi | `CreateNewUser` tidak mengisi `nip` wajib dan tidak memberi peran | Akun baru gagal dibuat / tidak bisa login |
| Peran | `StaffController::update()` bisa mengubah peran sendiri; pemeriksaan "admin terakhir" hanya di `changeStatus()`/`destroy()` | Risiko kehilangan satu-satunya admin |
| Activity log | Observer dan controller bisa mencatat event ganda; `old` diambil setelah `save()` | Duplikat dan diff yang kurang tepat |
| Offline | `client.request.id` hanya dipakai test; URL antrean tidak ada di route | Header replay diabaikan, antrean tidak bisa diproses |
| Service worker | Ikon manifest tidak cocok dengan file aktual | Ikon PWA 404 |
| Keamanan | Seeder memakai password default `123456789` | Harus diganti sebelum produksi |
| File lama | Tabel `audit_logs` dan beberapa model/view legacy tidak terpakai | Menambah kebingungan saat membaca schema |
| Import | `/bom/status` mengembalikan `completed` untuk ID asing | Polling bisa salah lapor sukses |
| UI | Tombol import BOM menargetkan modal yang tidak ada; `bom-import.js` memakai DOM ID usang | Endpoint import BOM belum bisa dipakai dari UI |
| UI | `bill-of-material/index.js` memanggil `initBomAnalytics()` yang tidak terdefinisi dan membuat form delete tanpa CSRF | Error JS dan risiko hapus gagal atau ditolak |
| Dokumen | `BomDocumentRevisionController::show()` memakai `find()` bukan `findOrFail()` | ID tidak ditemukan menghasilkan 500 |
| Validasi | UI mengiklankan 25 MB dan format doc/docx/gambar, server membatasi 20 MB serta pdf/xls/xlsx | User mendapat error setelah memilih file |
| Test | Beberapa assertion lama tidak lagi cocok dengan implementasi (`groupLabels`, header CSV) | Test gagal bukan karena regresi nyata |

## Panduan mengubah

- Menambah endpoint: tentukan dulu apakah viewer boleh mengaksesnya. Jika tidak, taruh di dalam grup `role:admin` di `routes/web.php`.
- Menambah kolom sensitif: daftarkan nama kolom ke `ActivityLogger::SENSITIVE_KEYS` atau redaksi tidak akan terjadi.
- Menambah field pada form antrean offline: perbarui `serializeForm()` dan `registerStockDeltaFromForm()` sekaligus.
- Mengganti zona waktu: ubah di satu tempat (misalnya `config/app.php` dan `.env.example`) lalu sesuaikan konversi di `ActivityLogController` dan scheduler agar tidak bertolak belakang.
- Menambah jalur import: putuskan dulu apakah memakai `jobs`, `ImportTracker`, atau `dispatchSync()`, lalu dokumentasikan endpoint statusnya. Jangan langsung memakai pola `/bom/status` untuk data SAP.
