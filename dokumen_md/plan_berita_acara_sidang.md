# PRD: Berita Acara Sidang Majelis Akreditasi

## Status Dokumen

- **Status:** Plan / PRD
- **Project:** PAPS Laravel
- **Database:** `akreditasi2025`
- **Template sumber:** `public/template_berita_acara_sidang.docx`
- **Halaman baru:** `resources/views/ttd-sidang.blade.php`
- **Implementasi:** Belum dilakukan

## 1. Latar Belakang

Project PAPS sudah memiliki alur tanda tangan elektronik untuk Berita Acara Visitasi melalui `ttd.blade.php`, `TtdController`, dan tabel `tr_digital_signatures`.

Diperlukan alur terpisah untuk Berita Acara Sidang Majelis Akreditasi. Alur ini menggunakan template:

```text
public/template_berita_acara_sidang.docx
```

Berita Acara Sidang memiliki tiga aktor yang berwenang memberikan tanda tangan:

- Ketua Majelis
- Sekretaris Majelis
- Anggota Majelis

Data tanda tangan sidang harus dipisahkan dari tanda tangan Berita Acara Visitasi agar status, aktor, dan proses submit tidak saling memengaruhi.

## 2. Tujuan

1. Menyediakan halaman tanda tangan khusus Berita Acara Sidang.
2. Menyimpan nama, jabatan, file tanda tangan, dan status tanda tangan tiga aktor sidang.
3. Menyediakan status lengkap atau belum lengkapnya tanda tangan sidang.
4. Mengunci data setelah Berita Acara Sidang disubmit.
5. Mendukung reset oleh sekretariat sebelum proses tanda tangan diulang.
6. Menghasilkan dokumen Berita Acara Sidang dari template DOCX dengan data sidang dan tanda tangan yang tersimpan.

## 3. Di Luar Scope

Fase ini tidak mencakup:

- Perubahan algoritma penilaian akreditasi.
- Perubahan nilai pra, paska, atau final.
- Penghapusan tabel `tr_digital_signatures` yang digunakan Berita Acara Visitasi.
- Perubahan format template DOCX tanpa persetujuan baru.
- Penandatanganan oleh aktor selain tiga aktor sidang.

## 4. Analisis Template DOCX

Template dibaca dari:

```text
public/template_berita_acara_sidang.docx
```

Placeholder yang teridentifikasi:

| Placeholder | Sumber data | Keterangan |
|---|---|---|
| `${nama_lemdik}` | Profile lembaga | Nama lembaga penyelenggara |
| `${hari_tanggal_surat}` | Form metadata sidang | Hari dan tanggal surat |
| `${waktu_surat}` | Form metadata sidang | Waktu sidang |
| `${zona_surat}` | Form metadata sidang | Zona waktu |
| `${jenis_pengajuan}` | Pengajuan | Jenis program akreditasi |
| `${ketua_majelis}` | Data tanda tangan sidang | Nama Ketua Majelis |
| `${sekretaris_majelis}` | Data tanda tangan sidang | Nama Sekretaris Majelis |
| `${anggota_majelis}` | Data tanda tangan sidang | Nama Anggota Majelis |
| `${tempat_surat}` | Form metadata sidang | Tempat pembuatan Berita Acara |
| `${tanggal}` | Form metadata sidang | Tanggal tanda tangan |
| `${ttd_ketua_majelis}` | File tanda tangan sidang | Gambar tanda tangan Ketua |
| `${ttd_sekretaris_majelis}` | File tanda tangan sidang | Gambar tanda tangan Sekretaris |
| `${ttd_anggota_majelis}` | File tanda tangan sidang | Gambar tanda tangan Anggota |

### Contoh Dummy Pengisian Metadata

Contoh berikut hanya untuk memahami bentuk data yang akan diisi. Nama, token, dan path tanda tangan di bawah ini bukan data production.

#### Contoh data metadata sidang

```json
{
  "pengajuan_id": 12,
  "nama_lemdik": "Pusat Pendidikan dan Pelatihan Statistik",
  "hari_tanggal_surat": "Kamis, 20 Agustus 2026",
  "waktu_surat": "09.00",
  "zona_surat": "Waktu Indonesia Barat",
  "jenis_pengajuan": "Statistik",
  "tempat_surat": "Jakarta",
  "tanggal": "20 Agustus 2026",
  "ttd_sidang_token": "a1b2c3d4e5f60123456789abcdef0123456789abcd"
}
```

#### Contoh data tiga aktor

```json
[
  {
    "jenis_user": "ketua_majelis",
    "nama_user": "Dr. Budi Santoso, M.Stat.",
    "jabatan_user": "Ketua Majelis Akreditasi",
    "ttd": "uploads/ttd-sidang/12/ketua_majelis.png",
    "status_ttd": "signed"
  },
  {
    "jenis_user": "sekretaris_majelis",
    "nama_user": "Siti Rahmawati, S.ST., M.Si.",
    "jabatan_user": "Sekretaris Majelis Akreditasi",
    "ttd": "uploads/ttd-sidang/12/sekretaris_majelis.png",
    "status_ttd": "signed"
  },
  {
    "jenis_user": "anggota_majelis",
    "nama_user": "Andi Pratama, S.E., M.M.",
    "jabatan_user": "Anggota Majelis Akreditasi",
    "ttd": "uploads/ttd-sidang/12/anggota_majelis.png",
    "status_ttd": "signed"
  }
]
```

#### Contoh hasil mapping ke template DOCX

```text
${nama_lemdik}               = Pusat Pendidikan dan Pelatihan Statistik
${hari_tanggal_surat}        = Kamis, 20 Agustus 2026
${waktu_surat}               = 09.00
${zona_surat}                = Waktu Indonesia Barat
${jenis_pengajuan}           = Statistik
${ketua_majelis}             = Dr. Budi Santoso, M.Stat.
${sekretaris_majelis}        = Siti Rahmawati, S.ST., M.Si.
${anggota_majelis}           = Andi Pratama, S.E., M.M.
${tempat_surat}              = Jakarta
${tanggal}                   = 20 Agustus 2026
${ttd_ketua_majelis}         = uploads/ttd-sidang/12/ketua_majelis.png
${ttd_sekretaris_majelis}    = uploads/ttd-sidang/12/sekretaris_majelis.png
${ttd_anggota_majelis}       = uploads/ttd-sidang/12/anggota_majelis.png
```

Pada hasil akhir DOCX, tiga nilai `ttd_*` tidak ditulis sebagai teks path. Path tersebut digunakan oleh `TemplateProcessor` untuk menyisipkan gambar tanda tangan pada lokasi placeholder masing-masing.

Template memuat susunan:

```text
BERITA ACARA
SIDANG MAJELIS AKREDITASI

Identitas lembaga dan program
Susunan tim Majelis Akreditasi
Ketua
Sekretaris
Anggota

Penetapan status dan rekomendasi

Tempat dan tanggal
Ketua Majelis Akreditasi
Sekretaris/Anggota
```

Implementasi generator DOCX wajib memeriksa apakah placeholder di dalam template terpecah menjadi beberapa run Word. Penggantian placeholder tidak boleh mengandalkan pencarian string XML mentah yang berisiko merusak format DOCX.

## 5. Aktor dan Hak Akses

### 5.1 Sekretariat

Sekretariat berwenang untuk:

- Membuat atau mengisi metadata Berita Acara Sidang.
- Menetapkan nama dan jabatan tiga aktor sidang.
- Membuka halaman tanda tangan.
- Melihat status tanda tangan.
- Mengirim atau membagikan tautan token.
- Submit Berita Acara setelah semua tanda tangan lengkap.
- Reset satu tanda tangan atau seluruh tanda tangan.
- Reset status submit Berita Acara.
- Mengunduh hasil Berita Acara Sidang.

### 5.2 Ketua Majelis

- Membuka tautan tanda tangan.
- Mengisi atau mengonfirmasi nama dan jabatan.
- Membubuhkan tanda tangan.
- Mengganti tanda tangan sebelum Berita Acara disubmit.

### 5.3 Sekretaris Majelis

Hak akses sama dengan Ketua Majelis, tetapi hanya untuk aktor `sekretaris_majelis`.

### 5.4 Anggota Majelis

Hak akses sama dengan Ketua Majelis, tetapi hanya untuk aktor `anggota_majelis`.

## 6. Rancangan Database

### 6.1 Tabel Baru: `tr_sidang_signatures`

Disarankan menggunakan tabel baru, bukan mencampur data ke `tr_digital_signatures`.

Alasan:

- Aktor sidang berbeda dari aktor Berita Acara Visitasi.
- Status submit sidang harus independen.
- Query status dan reset lebih aman.
- Struktur dapat berkembang tanpa mengubah kontrak TTD visitasi.

Kolom yang direncanakan:

| Kolom | Tipe | Aturan | Keterangan |
|---|---|---|---|
| `id` | bigint | primary key | ID record |
| `pengajuan_id` | bigint | foreign key/index | Relasi ke `tb_pengajuans.id` |
| `jenis_user` | varchar(40) | required | `ketua_majelis`, `sekretaris_majelis`, `anggota_majelis` |
| `nama_user` | varchar(255) | required | Nama aktor yang ditampilkan di dokumen |
| `jabatan_user` | varchar(255) | nullable/required sesuai form | Jabatan aktor |
| `ttd` | varchar(500) | nullable | Path file PNG tanda tangan |
| `tgl_surat` | date | nullable | Tanggal surat |
| `waktu_surat` | time | nullable | Waktu sidang/surat |
| `tgl_waktu_surat` | varchar/datetime | nullable | Format waktu yang ditampilkan |
| `tempat_surat` | varchar(100) | nullable | Tempat pembuatan surat |
| `zona_surat` | varchar(100) | nullable | Zona waktu |
| `status_ttd` | varchar(20) | default `pending` | `pending` atau `signed` |
| `created_at` | timestamp | nullable | Timestamp Laravel |
| `updated_at` | timestamp | nullable | Timestamp Laravel |

Constraint yang direncanakan:

```text
UNIQUE (pengajuan_id, jenis_user)
```

Dengan constraint ini, satu pengajuan hanya memiliki satu record aktif untuk setiap aktor sidang.

### 6.2 Token Sidang

Disarankan menambahkan kolom terpisah pada `tb_pengajuans`:

```text
ttd_sidang_token varchar(64) nullable unique
```

Jangan menggunakan `ttd_token` visitasi untuk sidang karena token visitasi dan sidang memiliki siklus hidup yang berbeda.

### 6.3 Status Submit Sidang

Disarankan menambahkan kolom pada `tb_pengajuans`:

```text
ba_sidang_submitted_at timestamp nullable
```

Makna status:

- `NULL`: belum disubmit
- berisi timestamp: Berita Acara Sidang sudah disubmit dan terkunci

### 6.4 Migration

Migration yang direncanakan:

```text
database/migrations/YYYY_MM_DD_HHMMSS_create_tr_sidang_signatures_table.php
database/migrations/YYYY_MM_DD_HHMMSS_add_sidang_ttd_fields_to_tb_pengajuans.php
```

Migration wajib:

- Aman dijalankan pada database production.
- Memiliki method `down()`.
- Menambahkan foreign key/index secara konsisten dengan schema existing.
- Tidak mengubah data TTD visitasi.

## 7. Model Laravel

Model baru yang direncanakan:

```text
app/Models/SidangSignature.php
```

Konstanta aktor:

```php
private const SIGNER_TYPES = [
    'ketua_majelis',
    'sekretaris_majelis',
    'anggota_majelis',
];
```

Model harus memiliki:

- Relasi `belongsTo(Pengajuan::class)`.
- `$fillable` untuk field metadata dan tanda tangan.
- Method untuk mengambil tanda tangan satu pengajuan.
- Method untuk memeriksa apakah tiga aktor sudah signed.
- Method untuk reset satu aktor.
- Method untuk reset seluruh tanda tangan sidang.

## 8. Halaman `ttd-sidang.blade.php`

File baru:

```text
resources/views/ttd-sidang.blade.php
```

Halaman ini menggunakan ide dan pola dari:

```text
resources/views/ttd.blade.php
```

### 8.1 Komponen halaman

Halaman harus memiliki:

1. Header Berita Acara Sidang.
2. Identitas lembaga dan jenis pengajuan.
3. Ringkasan susunan Majelis Akreditasi.
4. Tiga blok tanda tangan:
   - Ketua Majelis
   - Sekretaris Majelis
   - Anggota Majelis
5. Modal canvas untuk menggambar tanda tangan.
6. Opsional upload file tanda tangan jika pola existing dipertahankan.
7. Status progress:

```text
Menunggu tanda tangan (0/3 selesai)
```

8. Tombol submit yang hanya aktif untuk sekretariat ketika seluruh tanda tangan lengkap.
9. Tombol reset satu tanda tangan.
10. Tombol reset seluruh tanda tangan.
11. Tombol reset Berita Acara Sidang setelah disubmit, khusus sekretariat.
12. Tombol unduh/generate Berita Acara Sidang.

### 8.2 Perbedaan dari `ttd.blade.php`

| Komponen | Visitasi | Sidang |
|---|---|---|
| View | `ttd.blade.php` | `ttd-sidang.blade.php` |
| Token | `ttd_token` | `ttd_sidang_token` |
| Tabel | `tr_digital_signatures` | `tr_sidang_signatures` |
| Aktor | `asesor1`, `asesor2`, `asesor3`, `kepala` | `ketua_majelis`, `sekretaris_majelis`, `anggota_majelis` |
| Status submit | `ba_submitted_at` | `ba_sidang_submitted_at` |
| Jumlah tanda tangan | 4 | 3 |
| Template | Berita Acara Visitasi | Berita Acara Sidang |

## 9. Rancangan Route

Route publik untuk membuka halaman:

```text
GET /ttd-sidang/{token}
```

Route untuk menyimpan metadata:

```text
POST /ttd-sidang
```

Route API status tanda tangan:

```text
GET /api/ttd-sidang/{token}/signatures
```

Route menyimpan tanda tangan:

```text
POST /ettd-sidang/save-signature
```

Route mengambil gambar tanda tangan:

```text
GET /api/ttd-sidang/{token}/signatures/{signerType}/image
```

Route submit:

```text
POST /ettd-sidang/submit-ba
```

Route reset satu aktor:

```text
POST /ettd-sidang/reset-signature
```

Route reset semua aktor:

```text
POST /ettd-sidang/reset-all-signatures
```

Route reset status submit:

```text
POST /ettd-sidang/reset-ba
```

Route generate/download DOCX:

```text
GET /pengajuan/{id}/ekspor-ba-sidang
```

Nama route harus menggunakan prefix yang membedakan sidang dari visitasi, misalnya:

```text
ttd.sidang.show
ttd.sidang.save
ttd.sidang.submit.ba
ttd.sidang.reset
ttd.sidang.reset.all
ttd.sidang.reset.ba
ekspor.ba.sidang
```

## 10. Controller

Disarankan membuat controller terpisah agar alur sidang tidak merusak TTD visitasi:

```text
app/Http/Controllers/TtdSidangController.php
```

Method minimal:

```text
show($token)
createPost(Request $request)
saveSignature(Request $request)
getSignatures($token)
signatureImage($token, $signerType)
submitBeritaAcara(Request $request)
resetSignature(Request $request)
resetAllSignatures(Request $request)
resetBeritaAcara(Request $request)
rotateToken($id)
eksporBeritaAcaraSidang($id)
```

Generator DOCX menggunakan:

```php
PhpOffice\PhpWord\TemplateProcessor
```

Path template:

```php
public_path('template_berita_acara_sidang.docx')
```

## 11. Aturan Bisnis

### 11.1 Penyimpanan tanda tangan

- `signer_type` harus salah satu dari tiga aktor sidang.
- Nama dan jabatan wajib tersedia.
- Data PNG harus divalidasi ukuran dan formatnya.
- File tanda tangan disimpan pada direktori upload yang terkontrol.
- Jika tanda tangan diganti, file lama dihapus setelah file baru berhasil disimpan.
- Record menggunakan `updateOrCreate` berdasarkan `pengajuan_id` dan `jenis_user`.

### 11.2 Submit Berita Acara Sidang

Submit hanya boleh berhasil jika:

```text
ketua_majelis = signed
sekretaris_majelis = signed
anggota_majelis = signed
```

Saat submit berhasil:

```text
ba_sidang_submitted_at = now()
```

Setelah submit:

- Form metadata dikunci.
- Penyimpanan tanda tangan ditolak.
- Reset hanya boleh dilakukan oleh sekretariat.
- API mengembalikan HTTP `409` untuk perubahan yang ditolak.

### 11.3 Reset

Reset satu tanda tangan:

- Mengubah status aktor menjadi `pending` atau menghapus path tanda tangan sesuai standar existing.
- Tidak mengubah tanda tangan aktor lain.
- Tidak boleh dilakukan jika Berita Acara sudah submit, kecuali status submit di-reset terlebih dahulu.

Reset semua tanda tangan:

- Berlaku untuk tiga aktor sidang.
- File gambar lama harus dibersihkan.
- Status seluruh aktor kembali `pending`.

Reset Berita Acara:

- Mengosongkan `ba_sidang_submitted_at`.
- Mencatat user sekretariat dan waktu reset pada log aplikasi.

## 12. Generator Dokumen Sidang

Generator wajib mengisi placeholder berikut:

```text
nama_lemdik
hari_tanggal_surat
waktu_surat
zona_surat
jenis_pengajuan
ketua_majelis
sekretaris_majelis
anggota_majelis
tempat_surat
tanggal
ttd_ketua_majelis
ttd_sekretaris_majelis
ttd_anggota_majelis
```

Tanda tangan pada DOCX harus disisipkan sebagai image, bukan hanya menulis path file. Generator harus:

1. Memvalidasi file template ada.
2. Memvalidasi semua aktor wajib tersedia.
3. Mengambil path signature yang berstatus `signed`.
4. Mengisi text placeholder.
5. Mengganti placeholder tanda tangan dengan gambar.
6. Menyimpan file sementara.
7. Mengirim file sebagai download.
8. Menghapus file sementara setelah response selesai.

Jika dokumen belum lengkap, generator harus mengembalikan pesan validasi yang jelas dan tidak menghasilkan dokumen parsial.

## 13. Keamanan

- Token sidang harus random, unik, dan minimal 40 karakter hexadecimal atau setara.
- Jangan menggunakan token visitasi untuk halaman sidang.
- Endpoint publik hanya boleh mengakses data berdasarkan token valid.
- Endpoint submit, reset, rotate token, dan generate dokumen harus memakai middleware yang sesuai.
- Reset dan rotate token wajib dibatasi untuk sekretariat.
- Jangan menampilkan path filesystem internal ke browser.
- Gunakan response `Cache-Control: no-store` untuk gambar tanda tangan.
- Terapkan rate limit pada endpoint penyimpanan tanda tangan.
- Jangan menyimpan data signature PNG mentah dalam database; database menyimpan path file.
- Validasi upload berdasarkan MIME type, ukuran, dan magic bytes.
- Hindari logging token atau signature data mentah.

## 14. Kriteria Penerimaan

### Database

- [ ] Migration `tr_sidang_signatures` berhasil dijalankan.
- [ ] Kolom `ttd_sidang_token` tersedia dan unique.
- [ ] Kolom `ba_sidang_submitted_at` tersedia.
- [ ] Unique constraint `(pengajuan_id, jenis_user)` aktif.
- [ ] Tabel TTD visitasi tidak berubah perilakunya.

### Halaman

- [ ] `/ttd-sidang/{token}` dapat dibuka dengan token valid.
- [ ] Tiga aktor sidang tampil dengan nama dan jabatan.
- [ ] Canvas tanda tangan dapat dibuka untuk setiap aktor.
- [ ] Status progress menampilkan `0/3` sampai `3/3`.
- [ ] Tanda tangan yang tersimpan dapat ditampilkan kembali.
- [ ] Halaman responsif pada desktop dan mobile.

### Submit dan reset

- [ ] Submit ditolak jika salah satu tanda tangan belum lengkap.
- [ ] Submit berhasil jika seluruh tiga tanda tangan sudah signed.
- [ ] Setelah submit, perubahan signature ditolak.
- [ ] Reset hanya dapat dilakukan oleh sekretariat.
- [ ] Reset mengizinkan proses tanda tangan ulang.

### Dokumen

- [ ] Template `template_berita_acara_sidang.docx` digunakan.
- [ ] Semua placeholder teks terisi.
- [ ] Tiga gambar tanda tangan masuk ke posisi template yang benar.
- [ ] Nama file download informatif.
- [ ] File DOCX hasil generate dapat dibuka di Microsoft Word/LibreOffice.
- [ ] Tidak ada placeholder `${...}` yang tersisa.

### Regression

- [ ] Alur TTD Visitasi tetap dapat digunakan.
- [ ] Submit/reset Visitasi tidak mengubah data Sidang.
- [ ] Submit/reset Sidang tidak mengubah data Visitasi.
- [ ] `php artisan route:list` menampilkan route sidang.
- [ ] `php artisan view:cache` berhasil.
- [ ] `php -l` untuk controller dan migration berhasil.
- [ ] Feature test untuk status, reset, akses role, dan token berhasil.

## 15. Rencana Implementasi Bertahap

### Fase 1: Discovery dan schema

- Finalisasi placeholder template.
- Finalisasi nama tabel dan field.
- Buat migration.
- Buat model `SidangSignature`.
- Jalankan migration pada environment test.

### Fase 2: Backend TTD

- Buat `TtdSidangController`.
- Tambahkan route publik, API, submit, reset, dan token.
- Implementasikan validasi aktor.
- Implementasikan penyimpanan file signature.
- Implementasikan lock setelah submit.

### Fase 3: Frontend

- Salin pola UI `ttd.blade.php` secara selektif.
- Ganti aktor dan label menjadi Majelis Akreditasi.
- Ganti endpoint JavaScript ke endpoint sidang.
- Pastikan status `0/3` sampai `3/3`.
- Uji canvas, upload, reload, dan reset.

### Fase 4: Generator DOCX

- Implementasikan mapping placeholder.
- Sisipkan gambar tanda tangan.
- Uji dengan template asli.
- Validasi hasil DOCX menggunakan Word/LibreOffice.

### Fase 5: Testing dan deployment

- Jalankan feature test.
- Jalankan lint dan cache compilation.
- Uji regresi TTD Visitasi.
- Review permission direktori signature.
- Deploy setelah hasil test disetujui.

## 16. Risiko dan Keputusan yang Perlu Dikonfirmasi

1. **Lokasi data metadata sidang:** apakah metadata waktu/tempat disimpan di `tr_sidang_signatures` atau tabel terpisah `tr_sidang_berita_acara`.
2. **Status aktor:** apakah `status_ttd = pending` dipertahankan sebagai record atau record dibuat saat tanda tangan pertama.
3. **Aktor anggota:** template hanya memiliki satu `${anggota_majelis}`. Jika anggota lebih dari satu, template dan schema perlu diperluas.
4. **Isi lampiran penetapan/rekomendasi:** template menyebut “terlampir”, tetapi belum mendefinisikan sumber lampiran.
5. **Siapa yang membuat metadata awal:** sekretariat atau sistem mengambil otomatis dari pengajuan.
6. **Apakah generate DOCX boleh dilakukan sebelum submit:** rekomendasi awal adalah hanya setelah tiga tanda tangan lengkap, tetapi belum submit juga dapat dipertimbangkan untuk preview.

## 17. File Target Implementasi

```text
app/Http/Controllers/TtdSidangController.php
app/Models/SidangSignature.php
database/migrations/YYYY_MM_DD_HHMMSS_create_tr_sidang_signatures_table.php
database/migrations/YYYY_MM_DD_HHMMSS_add_sidang_ttd_fields_to_tb_pengajuans.php
resources/views/ttd-sidang.blade.php
routes/web.php
routes/api.php
```

Dokumen template yang digunakan:

```text
public/template_berita_acara_sidang.docx
```
