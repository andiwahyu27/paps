# Plan: Tombol dan Modal Berita Acara Sidang pada Halaman Final

> **Dependency:** Implementasikan `plan_berita_acara_sidang.md` terlebih dahulu. Plan ini hanya mengatur integrasi UI pada `final.blade.php` setelah backend, database, route, dan `ttd-sidang.blade.php` tersedia.

**Goal:** Menambahkan tombol Generate Berita Acara Sidang dan Tanda Tangan Sidang pada halaman penilaian final, lengkap dengan modal metadata sidang yang mengikuti pola halaman visitasi.

**Architecture:** UI diletakkan di `resources/views/asesor/final.blade.php`. Tombol menggunakan route khusus sidang dan tidak memakai route Berita Acara Visitasi. Modal mengirim metadata ke endpoint pembentukan sesi/token TTD Sidang.

**Tech Stack:** Laravel Blade, Bootstrap modal, Laravel route helper, Carbon, endpoint `TtdSidangController`.

---

## 1. Konteks Existing

### 1.1 Blok tombol pada `visitasi.blade.php`

Referensi:

```text
resources/views/asesor/visitasi.blade.php:55-101
```

Blok tersebut memiliki:

- Tombol `Generate BA` melalui route `ekspor.ba`.
- Tombol `Generate BA Hasil TTD` melalui route `ekspor.ba.ttd` jika `ba_submitted_at` terisi.
- Tombol upload/update Berita Acara Visitasi.
- Tombol `Lihat Tanda Tangan` jika signature sudah tersedia.
- Tombol `Generate Tanda Tangan` atau `Generate Ulang Tanda Tangan`.

### 1.2 Modal metadata existing

Referensi:

```text
resources/views/asesor/visitasi.blade.php:283-391
```

Modal existing memiliki pola:

- Modal Bootstrap `confirmSignatureModal`.
- Form POST dengan CSRF.
- Nama aktor.
- Tempat surat.
- Tanggal surat.
- Zona waktu.
- Waktu surat.
- Preview tanggal dan waktu terformat.
- Submit menuju halaman tanda tangan.

## 2. Scope Plan

### Termasuk

- Menambahkan blok tombol Berita Acara Sidang pada `final.blade.php`.
- Menambahkan tombol Generate BA Sidang.
- Menambahkan tombol Generate BA Sidang Hasil TTD.
- Menambahkan tombol Generate/Lihat/Generate Ulang Tanda Tangan Sidang.
- Menambahkan modal metadata Sidang.
- Menghubungkan modal ke route khusus Sidang.
- Menambahkan JavaScript preview hari, tanggal, waktu, dan zona waktu.
- Menampilkan nilai metadata existing ketika sesi Sidang sudah pernah dibuat.

### Tidak termasuk

- Pembuatan migration.
- Pembuatan model `SidangSignature`.
- Pembuatan controller utama TTD Sidang.
- Pembuatan view `ttd-sidang.blade.php`.
- Pembuatan generator DOCX.
- Perubahan alur TTD Visitasi.

Semua item backend tersebut adalah dependency dari:

```text
dokumen_md/plan_berita_acara_sidang.md
```

## 3. Rancangan UI pada `final.blade.php`

File target:

```text
resources/views/asesor/final.blade.php
```

Lokasi yang disarankan: setelah blok `Dok. Pendukung` dan sebelum `Rekomendasi Hasil Akreditasi`, atau setelah blok `Export Data`, dengan urutan tampilan berikut:

```text
Dok. Pendukung
Generate Berita Acara Sidang
Tanda Tangan Berita Acara Sidang
Rekomendasi Hasil Akreditasi
Export Data
Sertifikat Akreditasi
```

### 3.1 Blok Generate Berita Acara Sidang

Label tampilan:

```text
Generate
Berita Acara Sidang
```

Tombol yang direncanakan:

```blade
<a href="{{ route('ekspor.ba.sidang', $pengajuan->id) }}"
    class="btn btn-sm rounded-pill btn-primary">
    <i class="bx bxs-notepad"></i> Generate BA Sidang
</a>
```

Jika status Sidang sudah disubmit:

```blade
@if ($pengajuan->ba_sidang_submitted_at)
    <a href="{{ route('ekspor.ba.sidang.ttd', $pengajuan->id) }}"
        class="btn btn-sm rounded-pill btn-success">
        <i class="bx bxs-pen"></i> Generate BA Sidang Hasil TTD
    </a>
@endif
```

Route di atas hanya boleh digunakan setelah backend dari `plan_berita_acara_sidang.md` tersedia.

### 3.2 Blok Tanda Tangan Berita Acara Sidang

Label tampilan:

```text
Tanda Tangan
BA Sidang
```

Jika token Sidang sudah tersedia:

```blade
@if ($pengajuan->ttd_sidang_token)
    <a href="{{ route('ttd.sidang.show', ['token' => $pengajuan->ttd_sidang_token]) }}"
        class="btn btn-sm rounded-pill btn-info">
        <i class="bx bx-show"></i> Lihat TTD Sidang
    </a>
    <button type="button" class="btn btn-sm rounded-pill btn-primary"
        data-bs-toggle="modal" data-bs-target="#confirmSidangSignatureModal">
        <i class="bx bx-pen"></i> Generate Ulang TTD Sidang
    </button>
@else
    <button type="button" class="btn btn-sm rounded-pill btn-primary"
        data-bs-toggle="modal" data-bs-target="#confirmSidangSignatureModal">
        <i class="bx bx-pen"></i> Generate TTD Sidang
    </button>
@endif
```

Jika sudah submit, label sebaiknya berubah menjadi:

```text
Lihat TTD Sidang
```

Tombol generate ulang hanya boleh tampil sesuai hak akses sekretariat dan aturan reset dari backend.

## 4. Rancangan Modal Metadata Sidang

File target:

```text
resources/views/asesor/final.blade.php
```

Modal baru:

```text
#confirmSidangSignatureModal
```

Judul:

```text
Konfirmasi Data Tanda Tangan Berita Acara Sidang
```

Form yang direncanakan:

```blade
<form id="confirmSidangSignatureForm"
    action="{{ route('ttd.sidang.create.post') }}"
    method="POST">
    @csrf
    <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
    <input type="hidden" name="token" value="{{ $pengajuan->ttd_sidang_token }}">
</form>
```

### 4.1 Field aktor sidang

#### Ketua Majelis

```text
name: ketua_majelis_name
id: ketua_majelis_name
required: yes
```

Label:

```text
Ketua Majelis
```

Contoh value:

```text
Dr. Budi Santoso, M.Stat.
```

#### Jabatan Ketua Majelis

```text
name: ketua_majelis_title
id: ketua_majelis_title
required: yes
```

Default:

```text
Ketua Majelis Akreditasi
```

#### Sekretaris Majelis

```text
name: sekretaris_majelis_name
id: sekretaris_majelis_name
required: yes
```

Contoh value:

```text
Siti Rahmawati, S.ST., M.Si.
```

#### Jabatan Sekretaris Majelis

```text
name: sekretaris_majelis_title
id: sekretaris_majelis_title
required: yes
```

Default:

```text
Sekretaris Majelis Akreditasi
```

#### Anggota Majelis

```text
name: anggota_majelis_name
id: anggota_majelis_name
required: yes
```

Contoh value:

```text
Andi Pratama, S.E., M.M.
```

#### Jabatan Anggota Majelis

```text
name: anggota_majelis_title
id: anggota_majelis_title
required: yes
```

Default:

```text
Anggota Majelis Akreditasi
```

### 4.2 Field metadata sidang

#### Tempat Surat

```text
name: signature_place
id: sidang_signature_place
maxlength: 100
required: yes
```

Contoh:

```text
Jakarta
```

#### Tanggal Surat

```text
name: letter_date
id: sidang_letter_date
input type: date
required: yes
```

Default: tanggal saat modal dibuka.

#### Hari dan Tanggal Surat Terbilang

Nilai `${hari_tanggal_surat}` tidak boleh menggunakan format singkat seperti:

```text
Kamis, 20 Agustus 2026
```

Format yang wajib dikirim ke backend dan digunakan pada template DOCX adalah:

```text
Hari Kamis Tanggal Dua Puluh Bulan Agustus Tahun Dua Ribu Dua Puluh Enam
```

Field preview:

```text
id: sidang_hari_tanggal_surat_preview
readonly: yes
```

Field tersembunyi yang dikirim:

```text
id: sidang_hari_tanggal_surat
name: hari_tanggal_surat
```

Field ini harus dibuat otomatis dari `sidang_letter_date`, bukan diketik manual oleh pengguna.

#### Zona Waktu

```text
name: timezone
id: sidang_timezone
required: yes
```

Pilihan:

```text
Asia/Jakarta   = WIB (UTC+7)
Asia/Makassar  = WITA (UTC+8)
Asia/Jayapura  = WIT (UTC+9)
```

Default: `Asia/Jakarta`.

#### Waktu Sidang/Surat

```text
name: signature_time
id: sidang_signature_time
input type: time
required: yes
```

Default: waktu saat modal dibuka.

#### Preview waktu terformat

```text
id: sidang_signature_datetime
name: datetime
readonly: yes
```

Contoh hasil:

```text
Kamis Tanggal 20 Agustus 2026, Pukul 09.00 Waktu Indonesia Barat
```

#### Preview tanggal dokumen

```text
id: sidang_signature_date_preview
readonly: yes
```

Field tersembunyi yang dikirim:

```text
id: sidang_signature_date
name: signature_date
```

`signature_date` adalah nilai tanggal untuk kebutuhan metadata dokumen. Nilai `${hari_tanggal_surat}` tetap menggunakan format terbilang lengkap dan tidak boleh digantikan oleh `signature_date`.

## 5. Alur Interaksi

### 5.1 Generate pertama kali

```text
Klik Generate TTD Sidang
        |
        v
Modal metadata tampil
        |
        v
Sekretariat mengisi nama tiga aktor dan metadata waktu
        |
        v
JavaScript memperbarui preview tanggal/waktu
        |
        v
Submit form metadata
        |
        v
Backend membuat atau mengisi ttd_sidang_token
        |
        v
Redirect ke /ttd-sidang/{token}
```

### 5.2 Generate ulang

```text
Klik Generate Ulang TTD Sidang
        |
        v
Modal tampil dengan data sebelumnya
        |
        v
Sekretariat mengubah data jika diperlukan
        |
        v
Backend meng-update metadata sidang
        |
        v
Signature lama mengikuti aturan reset backend
        |
        v
Redirect ke halaman TTD Sidang
```

### 5.3 Setelah Berita Acara Sidang disubmit

- Data modal hanya boleh dibuka untuk melihat atau melalui mekanisme reset sekretariat.
- Tanda tangan tidak boleh ditimpa tanpa reset.
- Tombol `Generate BA Sidang Hasil TTD` aktif jika `ba_sidang_submitted_at` tidak null.
- Tombol generate ulang mengikuti status dan policy reset dari `TtdSidangController`.

## 6. JavaScript Modal

Gunakan pola existing pada `visitasi.blade.php:393-438`, tetapi prefix ID dibuat unik agar tidak bentrok dengan modal visitasi.

Fungsi yang direncanakan:

```javascript
function updateSidangFormattedDateTime() {}
function updateSidangHariTanggalTerbilang() {}
```

Event listener:

```javascript
document.getElementById('sidang_signature_place')
document.getElementById('sidang_letter_date')
document.getElementById('sidang_signature_time')
document.getElementById('sidang_timezone')
document.getElementById('sidang_hari_tanggal_surat_preview')
```

Aturan:

- Jangan memakai ID generic seperti `letter_date` atau `timezone` jika view dapat memuat komponen lain.
- Gunakan nama function dengan prefix `Sidang`.
- Preview harus menggunakan label zona waktu Indonesia.
- Preview `${hari_tanggal_surat}` wajib memakai format `Hari [Nama Hari] Tanggal [Terbilang Hari] Bulan [Nama Bulan] Tahun [Terbilang Tahun]`.
- Contoh wajib: `Hari Kamis Tanggal Dua Puluh Bulan Agustus Tahun Dua Ribu Dua Puluh Enam`.
- Konversi angka ke terbilang harus dilakukan secara deterministik untuk tanggal 1–31 dan tahun 4 digit.
- Nilai hidden `hari_tanggal_surat` harus sama dengan preview terbilang yang dikirim ke backend.
- Jangan mengandalkan timezone browser untuk menentukan label zona.
- Backend tetap menjadi sumber kebenaran waktu dan tanggal.

## 7. Route yang Dibutuhkan dari Plan Sebelumnya

Route yang harus sudah tersedia sebelum UI ini diaktifkan:

```text
GET  /ttd-sidang/{token}
POST /ttd-sidang
GET  /api/ttd-sidang/{token}/signatures
POST /ettd-sidang/save-signature
POST /ettd-sidang/submit-ba
GET  /pengajuan/{id}/ekspor-ba-sidang
GET  /pengajuan/{id}/ekspor-ba-sidang-ttd
```

Nama route yang digunakan view:

```text
ttd.sidang.show
ttd.sidang.create.post
ttd.sidang.save
ttd.sidang.submit.ba
ekspor.ba.sidang
ekspor.ba.sidang.ttd
```

Jika nama route pada implementasi backend berbeda, view harus mengikuti nama route aktual—jangan membuat alias palsu hanya untuk menghindari error.

## 8. Data yang Diharapkan dari Controller Final

Agar view dapat dirender tanpa query tambahan, controller final atau view model perlu menyediakan:

```php
[
    'pengajuan' => $pengajuan,
    'sidangSignature' => $sidangSignature,
    'sidangSignatures' => $sidangSignatures,
    'sidangSubmitted' => (bool) $pengajuan->ba_sidang_submitted_at,
]
```

Minimal field yang harus dapat diakses:

```text
$pengajuan->ttd_sidang_token
$pengajuan->ba_sidang_submitted_at
$sidangSignatures['ketua_majelis']
$sidangSignatures['sekretaris_majelis']
$sidangSignatures['anggota_majelis']
```

Jangan menganggap `$pengajuan->ttd_sidang_token` tersedia sebelum migration dari plan utama dijalankan.

## 9. Validasi UI

- Semua nama aktor wajib diisi.
- Semua jabatan aktor wajib diisi.
- Tempat surat wajib diisi dan maksimal 100 karakter.
- Tanggal surat wajib diisi.
- `hari_tanggal_surat` wajib terbentuk otomatis dalam format terbilang lengkap.
- Format singkat seperti `Kamis, 20 Agustus 2026` tidak boleh dikirim sebagai `hari_tanggal_surat`.
- Waktu surat wajib diisi.
- Zona waktu wajib dipilih.
- Tombol submit modal disabled atau ditolak oleh validasi HTML jika field belum lengkap.
- Pesan error backend harus ditampilkan di halaman jika metadata tidak valid.

## 10. Kriteria Penerimaan

### Tombol

- [ ] `final.blade.php` menampilkan blok Generate Berita Acara Sidang.
- [ ] Tombol memakai route khusus Sidang.
- [ ] Tidak ada tombol yang memakai `ekspor.ba`, `ekspor.ba.ttd`, atau `ttd.show` untuk alur Sidang.
- [ ] Tombol TTD berubah antara Generate, Lihat, dan Generate Ulang sesuai status token.
- [ ] Tombol hasil TTD hanya aktif setelah `ba_sidang_submitted_at` terisi.

### Modal

- [ ] Modal memiliki field Ketua Majelis.
- [ ] Modal memiliki field Sekretaris Majelis.
- [ ] Modal memiliki field Anggota Majelis.
- [ ] Modal memiliki jabatan untuk masing-masing aktor.
- [ ] Modal memiliki tempat, tanggal, waktu, dan zona waktu.
- [ ] Preview hari/tanggal/waktu berubah saat input berubah.
- [ ] Form mengirim CSRF dan `pengajuan_id`.
- [ ] Form mengarah ke route `ttd.sidang.create.post`.
- [ ] Data lama muncul kembali ketika generate ulang.

### Regression

- [ ] Modal `confirmSignatureModal` Visitasi tetap berfungsi.
- [ ] ID dan function JavaScript Sidang tidak bentrok dengan Visitasi.
- [ ] `php artisan view:cache` berhasil.
- [ ] `php artisan route:list` menampilkan semua route Sidang.
- [ ] Browser test memastikan tombol tidak menghasilkan route error.
- [ ] Browser test memastikan metadata modal dapat dikirim.

## 11. Risiko dan Keputusan

1. **Status generate BA tanpa TTD:** tentukan apakah tombol Generate BA Sidang boleh digunakan sebelum tanda tangan lengkap atau hanya sebagai preview.
2. **Generate ulang:** perlu dipastikan apakah generate ulang otomatis mereset tiga signature atau hanya meng-update metadata.
3. **Hak akses:** halaman final dapat dilihat asesor dan sekretariat, tetapi pembuatan token dan reset sebaiknya dibatasi sekretariat.
4. **Sumber nama aktor:** default dapat diambil dari konfigurasi/roster Majelis, tetapi modal harus mengizinkan koreksi manual sesuai kebutuhan operasional.
5. **Satu anggota atau banyak anggota:** template saat ini hanya menyediakan satu `${anggota_majelis}`.
6. **Field modal dan schema:** nama field form harus disamakan dengan field controller dan migration agar tidak terjadi mismatch.

## 12. Urutan Implementasi Setelah Dependency Selesai

1. Pastikan migration, model, controller, route, dan `ttd-sidang.blade.php` dari plan utama sudah selesai.
2. Tambahkan data Sidang ke controller halaman final.
3. Tambahkan blok tombol Sidang ke `final.blade.php`.
4. Tambahkan modal metadata Sidang ke `final.blade.php`.
5. Tambahkan JavaScript preview dengan ID/function prefix Sidang.
6. Tambahkan route generate/download bila belum tersedia dari plan utama.
7. Jalankan `php artisan view:cache`.
8. Jalankan route verification.
9. Uji halaman final dengan pengajuan tanpa token, token aktif, dan status sudah submit.
10. Uji regresi tombol dan modal Visitasi.
11. Review diff, test, dan baru commit/push.
