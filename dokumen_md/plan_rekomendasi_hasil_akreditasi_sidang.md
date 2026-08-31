# PRD / Implementation Specification: Rekomendasi Hasil Akreditasi

**Project:** PAPS  
**Status:** Implemented locally, pending review/deploy
**Tanggal:** 31 Agustus 2026

## 1. Ringkasan

Workflow rekomendasi hasil akreditasi menggunakan halaman internal teraut untuk entri oleh asesor/sekretariat. Halaman token publik `ttd-sidang/{token}` hanya menampilkan hasil tersimpan secara read-only.

Alur:

```text
final.blade.php
  -> Generate Template
rekomendasi-hasil-sidang.blade.php
  -> simpan / submit / export DOCX
tr_rekomendasi_hasil_akreditasi + status tb_pengajuans
  -> tab read-only
 ttd-sidang/{token}
```

## 2. Temuan Audit Aktual

### 2.1 Alur lama

Audit repository menemukan:

- `PenilaianController` masih memiliki method export route `ekspor.rekomendasi` menggunakan template DOCX.
- Route upload `upload.rekomendasi` masih menyimpan file manual pada kolom `tb_pengajuans.rekomendasi_visitasi`.
- Tombol lama dan modal upload berada di `resources/views/asesor/final.blade.php`.
- Kolom/data lama tidak dihapus agar file lama dan fitur kompatibilitas tidak rusak. Tombol UI lama digantikan workflow terstruktur baru. Penghapusan route/kolom lama memerlukan keputusan migrasi data terpisah.

### 2.2 Struktur UI aktual

- `final.blade.php` memakai layout `layouts.app-asesor` dan memiliki blok Dokumen Pendukung, BA Sidang, TTD BA Sidang, Rekomendasi, Export Data, serta Sertifikat.
- `ttd-sidang.blade.php` memakai mekanisme tab native berbasis `.ettd-tab` dan `data-tab`, dengan panel Tanda Tangan dan Catatan Asesor.
- Controller publik aktual adalah `TtdSidangController`; model signature aktual adalah `SidangSignature`.

### 2.3 Database dan role

- `tb_pengajuans.id` bertipe integer.
- `users.id` bertipe BIGINT UNSIGNED; kolom audit user memakai BIGINT UNSIGNED nullable.
- Tahun pengajuan bersumber dari `tb_pengajuans.created_at` karena tidak ada field tahun pengajuan eksplisit yang dipakai untuk kebutuhan ini.
- Middleware gabungan aktual: `is.asesor.or.sekretariat`.
- Role aplikasi: sekretariat `2`, asesor `3`.

### 2.4 Template DOCX

Template: `public/template_hasil_visitasi.docx`.

Placeholder otomatis yang dipakai:

- `${tahun_pengajuan}`
- `${nama_lemdik}`
- `${jenis_pengajuan}`
- `${nilai_final}`
- `${predikat_final}`

Label area rekomendasi:

- `Hal-hal yang harus dipertahankan`
- `Hal-hal yang harus diperbaiki`

Export menggunakan `PhpOffice\\PhpWord\\TemplateProcessor`; tidak memakai PDF/Dompdf.

## 3. Ruang Lingkup

### 3.1 Termasuk

1. Tabel detail rekomendasi dengan kategori dan urutan.
2. Status submit pada `tb_pengajuans`.
3. Halaman internal untuk entri asesor/sekretariat.
4. Baris dinamis tambah/hapus untuk dua kategori.
5. Tombol simpan, submit, dan Export to DOCX.
6. Tombol akses dari `final.blade.php`.
7. Tab read-only pada `ttd-sidang.blade.php`.
8. Nilai otomatis/read-only dari data resmi aplikasi.

### 3.2 Tidak termasuk

- Export PDF/Dompdf.
- Form mutasi pada halaman token publik.
- Reopen setelah submit.
- Penghapusan historis file upload manual lama.

## 4. Data Model

### 4.1 Tabel `tr_rekomendasi_hasil_akreditasi`

| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | primary key |
| pengajuan_id | INT | FK ke `tb_pengajuans.id`, cascade delete |
| kategori | VARCHAR(30) | `dipertahankan` atau `diperbaiki` |
| isi | TEXT | wajib setelah trim, max 5000 karakter |
| urutan | UNSIGNED INT | default 1 |
| created_by | BIGINT UNSIGNED nullable | user pembuat |
| updated_by | BIGINT UNSIGNED nullable | user terakhir mengubah |
| created_at/updated_at | timestamp | timestamps Laravel |

Index gabungan: `(pengajuan_id, kategori, urutan)`.

### 4.2 Tambahan `tb_pengajuans`

- `rekomendasi_akreditasi_submitted_at` timestamp nullable.
- `rekomendasi_akreditasi_submitted_by` BIGINT UNSIGNED nullable.

Status rekomendasi independen dari `ba_sidang_submitted_at`.

### 4.3 Relasi

`Pengajuan::rekomendasiHasilAkreditasi()` adalah `hasMany`. Model detail memiliki `belongsTo(Pengajuan::class)`.

## 5. Routes

Routes internal berada di group auth dan middleware role gabungan aktual:

```php
GET  /pengajuan/rekomendasi-hasil-sidang/{id}
POST /pengajuan/rekomendasi-hasil-sidang/{id}
POST /pengajuan/rekomendasi-hasil-sidang/{id}/submit
POST /pengajuan/rekomendasi-hasil-sidang/{id}/reopen
GET  /pengajuan/rekomendasi-hasil-sidang/{id}/export-docx
GET  /ttd-sidang/{token}/rekomendasi/export-docx
```

Nama route:

- `rekomendasi.hasil.sidang.show`
- `rekomendasi.hasil.sidang.store`
- `rekomendasi.hasil.sidang.submit`
- `rekomendasi.hasil.sidang.export.docx`

Tidak ada route mutasi rekomendasi di bawah `/ttd-sidang/{token}`.

## 6. Otorisasi dan Business Rules

1. Semua route internal memakai auth dan `is.asesor.or.sekretariat`.
2. Controller memvalidasi role 2/3 dan memastikan asesor terkait dengan pengajuan melalui `id_asesor1`, `id_asesor2`, atau `id_asesor3`.
3. Semua mutasi memakai CSRF dan validasi request.
4. Simpan hanya menerima dua kategori yang ditentukan dan mengabaikan baris kosong setelah trim.
5. Simpan mengganti detail yang belum disubmit dalam satu transaction.
6. Submit ditolak jika sudah pernah submit atau belum ada minimal satu detail.
7. Setelah submit, halaman entri berubah read-only dan simpan/submit berikutnya ditolak dengan HTTP 409.
8. Submit rekomendasi tidak mengubah lifecycle BA Sidang.
9. Reopen/reset hanya mengosongkan dua field status submit pada `tb_pengajuans`; detail rekomendasi tetap dipertahankan agar dapat diperbaiki.
10. Endpoint export token bersifat GET/read-only, resolve pengajuan dari token, dan tidak menerima `pengajuan_id` dari client.
11. Nilai otomatis tidak pernah berasal dari input user:
   - tahun: `created_at` format `Y`;
   - jenis: nama dari relasi master `mt_jenis_pengajuans` (`JenisPengajuan::nama`);
   - nilai/predikat: field final pengajuan.
12. Isi rekomendasi di-escape saat ditampilkan.

## 7. UI/UX

### 7.1 `final.blade.php`

Blok lama Generate/Upload Rekomendasi diganti link:

- Jika belum submit: `Generate Template`.
- Jika sudah submit: `Lihat Rekomendasi`.

Link mengarah ke halaman internal baru.

### 7.2 Halaman entri

File: `resources/views/asesor/rekomendasi-hasil-sidang.blade.php`.

- Ringkasan empat field otomatis dalam keadaan read-only.
- Dua section rekomendasi.
- Setiap section mendukung tambah/hapus baris.
- Tombol `SIMPAN REKOMENDASI`.
- Tombol `SUBMIT REKOMENDASI` dengan konfirmasi.
- Tombol `Export to DOCX`.
- Setelah submit, seluruh input dan tombol mutasi hilang; hanya data read-only dan export yang tersedia.

### 7.3 Tab publik

File: `resources/views/ttd-sidang.blade.php`.

- Tab baru: `Rekomendasi Hasil Akreditasi` di samping `Catatan Asesor`.
- Hanya ringkasan dan daftar teks per kategori.
- Tidak ada textarea, tambah, hapus, simpan, atau submit.
- Jika kosong: `Belum ada rekomendasi hasil akreditasi.`
- Data diambil controller berdasarkan pengajuan hasil resolve token.

## 8. Generator DOCX

Controller memakai `TemplateProcessor` dan template public yang sudah tersedia. Placeholder otomatis diganti sebelum file disajikan sebagai download.

Karena template menggunakan label area rekomendasi tanpa placeholder dinamis, XML `word/document.xml` hasil TemplateProcessor diisi daftar bernomor pada row label terkait. Teks di-escape sebagai XML. Kategori kosong menggunakan `-`. Export bersifat read-only dan tidak mengubah status/data.

## 9. File Implementasi

### Baru

- `app/Models/RekomendasiHasilAkreditasi.php`
- `app/Http/Controllers/RekomendasiHasilAkreditasiController.php`
- `resources/views/asesor/rekomendasi-hasil-sidang.blade.php`
- `resources/views/asesor/partials/rekomendasi-hasil-list.blade.php`
- `database/migrations/2026_08_31_000001_create_tr_rekomendasi_hasil_akreditasi_table.php`
- `database/migrations/2026_08_31_000002_add_rekomendasi_akreditasi_status_to_tb_pengajuans.php`

### Diubah

- `app/Models/Pengajuan.php`
- `app/Http/Controllers/TtdSidangController.php`
- `resources/views/asesor/final.blade.php`
- `resources/views/ttd-sidang.blade.php`
- `routes/web.php`

### Tidak dihapus

Route/controller/kolom upload manual lama masih dipertahankan sementara karena audit menemukan kemungkinan ketergantungan dan data historis. UI entri baru tidak lagi menggunakannya.

## 10. Verifikasi

Hasil verifikasi lokal:

- PHP lint model, controller, controller TTD, model Pengajuan, dan dua migration: lulus.
- `php artisan migrate --force`: kedua migration berhasil.
- `php artisan view:cache`: berhasil.
- `php artisan route:cache`: berhasil.
- Route list menampilkan keempat route rekomendasi dan route TTD Sidang.
- `git diff --check`: lulus.
- Render halaman entri dengan role sekretariat: berhasil; field otomatis dan tombol simpan terdeteksi.
- Render halaman token: tab rekomendasi terdeteksi; tombol simpan tidak ada.
- Export DOCX sementara: ZIP valid, 18.200 byte, tidak ada placeholder `${...}` tersisa.

## 11. Open Questions

1. Apakah route dan kolom upload manual lama boleh dinonaktifkan setelah data historis dimigrasikan/diarsipkan?
2. Apakah perlu tombol reopen resmi setelah submit? Saat ini sengaja belum dibuat.
3. Apakah mapping `id_jenis` selain nilai yang sudah dikenal perlu diperluas berdasarkan master jenis pengajuan?
4. Apakah export DOCX juga perlu tersedia dari tab token publik? Saat ini hanya tersedia dari halaman internal untuk menjaga endpoint publik tetap read-only.

## 12. Risiko Residual

- Format area dinamis bergantung pada label XML template; perubahan template perlu mengulang uji export.
- Data legacy `rekomendasi_visitasi` dan data terstruktur dapat berjalan paralel sampai keputusan migrasi dibuat.
- Tidak ada test browser end-to-end otomatis; verifikasi saat ini mencakup lint, migration, cache, route, render, dan ZIP DOCX.
- Perubahan belum dipush/deploy pada tahap PRD implementasi ini.
