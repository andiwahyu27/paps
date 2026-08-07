# PAPS — Perbaikan Workflow E-TTD, Tabs Catatan, dan Integrasi Signature ke Berita Acara

Kamu bekerja sebagai senior Laravel engineer pada project **PAPS**.

Kerjakan perubahan ini secara langsung pada repository/project yang sedang aktif.

## TUJUAN

Perbaiki workflow E-TTD `/ttd/{token}` sehingga:

1. Tidak ada lagi status/record `pending` yang salah setelah empat tanda tangan sudah lengkap.
2. Setelah Berita Acara disubmit, dokumen masuk state `submitted` dan tidak bisa diedit sembarang orang.
3. Hanya role **sekretariat** yang dapat melakukan RESET Berita Acara.
4. Halaman `/ttd/{token}` memiliki tabs:
   - **Tanda Tangan**
   - **Catatan**
5. Saat Berita Acara digenerate dari halaman visitasi, tanda tangan digital yang sudah ada otomatis ditempelkan ke dokumen BA.

---

# WAJIB: AUDIT SEBELUM CODING

Sebelum mengubah kode, telusuri implementation aktual dan dependency-nya.

Minimal periksa:

```text
routes/web.php
app/Http/Controllers/TtdController.php
app/Http/Controllers/SekretariatController.php
app/Http/Controllers/Asesor/PenilaianController.php

app/Models/DigitalSignature.php
app/Models/Pengajuan.php

resources/views/ttd.blade.php
resources/views/asesor/*
resources/views/sekretariat/*

database/migrations/*
```

Cari juga semua penggunaan:

```text
berita_acara
DigitalSignature
status_ttd
pending
signed
generateBA
generate.ba
ttd_token
resetTtd
saveSignature
createPost
visitasi
catatan
rekomendasi
```

Jangan mengasumsikan nama tabel, field, relasi, atau role.

Gunakan implementasi aktual repository sebagai source of truth.

Sebelum coding, tuliskan ringkas di terminal:

1. flow E-TTD saat ini;
2. tabel/model yang terlibat;
3. cara BA dinyatakan submitted saat ini;
4. sumber data Catatan;
5. fungsi yang benar-benar menghasilkan file Berita Acara;
6. file yang akan diubah.

Setelah itu lanjut implementasi tanpa menunggu konfirmasi.

---

# BAGIAN 1 — FIX BUG PENDING E-TTD

Saat ini terdapat kasus:

- asesor1 sudah signed
- asesor2 sudah signed
- asesor3 sudah signed
- kepala sudah signed
- total = 4 tanda tangan
- halaman `/ttd` sudah SUBMIT BERITA ACARA

tetapi database/UI masih menunjukkan data `pending`.

Investigasi root cause, jangan hanya menyembunyikan status pending di frontend.

## Hipotesis yang HARUS diperiksa

Periksa:

```php
TtdController::saveFormDataToDigitalSignature()
```

Saat ini ada pola seperti:

```php
DigitalSignature::updateOrCreate(
    [
        'pengajuan_id' => $pengajuanId,
        'jenis_user' => $user['jenis_user'],
        'status_ttd' => 'pending',
    ],
    [...]
);
```

Ini berpotensi bermasalah.

Jika signer sudah memiliki:

```text
pengajuan_id = X
jenis_user   = asesor1
status_ttd   = signed
```

query dengan:

```text
status_ttd = pending
```

tidak akan menemukan row tersebut dan dapat membuat ROW BARU `pending`.

Akibatnya satu signer dapat mempunyai:

```text
asesor1 signed
asesor1 pending
```

Ini tidak boleh terjadi.

## TARGET DATA MODEL

Untuk satu:

```text
pengajuan_id + jenis_user
```

hanya boleh ada **satu logical signature record**.

Signer valid:

```text
asesor1
asesor2
asesor3
kepala
```

Lifecycle:

```text
pending
   ↓
signed
```

Bukan:

```text
pending row
+
signed row
```

Jika design database memungkinkan, gunakan lookup:

```text
pengajuan_id + jenis_user
```

sebagai identitas signature.

`status_ttd` adalah STATE, bukan bagian identity record.

Pertimbangkan unique constraint:

```text
UNIQUE(pengajuan_id, jenis_user)
```

TAPI:

- audit data existing dahulu;
- jangan menambahkan constraint jika akan merusak data production;
- jika diperlukan buat migration aman;
- bersihkan duplicate secara deterministik;
- prioritaskan record `signed` jika terdapat `signed + pending`.

Jangan kehilangan signature PNG existing.

---

# BAGIAN 2 — DEFINISIKAN STATE BERITA ACARA

Buat source of truth di backend.

Minimal state konseptual:

### A. Belum lengkap

```text
signed signatures < 4
```

UI:

```text
Menunggu tanda tangan (x/4 selesai)
```

Tidak ada tombol Submit.

---

### B. Lengkap tetapi belum submit

```text
signed signatures = 4
BA belum submitted
```

UI:

```text
Semua tanda tangan telah lengkap (4/4)
```

Tampilkan:

```text
SUBMIT BERITA ACARA
```

sesuai hak akses/business flow existing.

---

### C. Sudah submit

```text
signed signatures = 4
BA = submitted
```

Tidak boleh kembali menampilkan:

```text
pending
Menunggu tanda tangan
SUBMIT BERITA ACARA
```

State harus berasal dari database/server.

Jangan hanya menggunakan variable JavaScript.

---

# BAGIAN 3 — BEHAVIOR SETELAH SUBMIT

Setelah BA submitted:

## Jika authenticated user role = SEKRETARIAT

Tampilkan status:

```text
Berita Acara telah disubmit
4 dari 4 tanda tangan telah lengkap.
```

Tombol:

```text
RESET BERITA ACARA
```

Gunakan styling yang konsisten dengan halaman E-TTD.

Reset harus memiliki confirmation modal/dialog.

Contoh:

```text
Reset Berita Acara?

Berita acara akan dibuka kembali sehingga tanda tangan dapat diperbaiki.
Tanda tangan yang sudah tersimpan tidak akan dihapus.

[Batal] [Ya, Reset]
```

---

## Jika BUKAN sekretariat / akses public

JANGAN tampilkan tombol reset.

JANGAN tampilkan tombol submit.

Tampilkan informational message:

```text
Berita Acara telah disubmit.

Seluruh tanda tangan telah lengkap dan Berita Acara sudah dikunci.
```

---

# BAGIAN 4 — RESET BERITA ACARA ≠ RESET SIGNATURE

Perhatikan bahwa saat ini terdapat endpoint:

```text
POST /ettd/reset-signature
TtdController::resetTtd()
```

yang digunakan untuk menghapus/reset individual signature.

JANGAN memakai endpoint tersebut untuk reset submission BA kecuali setelah audit ternyata memang requirement existing begitu.

Buat separation of concern.

Konseptual:

```text
reset signature
= menghapus satu tanda tangan

reset berita acara
= mengubah BA submitted → editable kembali
```

Saat RESET BERITA ACARA:

```text
4 signature tetap ada
file PNG tetap ada
status signed tetap signed
BA submission state berubah menjadi belum submit
```

Setelah reset, signature dapat diganti bila diperlukan.

Endpoint reset BA HARUS:

```text
auth
+
is.sekretariat
```

Backend harus mengotorisasi role.

Jangan mengandalkan hiding tombol di frontend.

---

# BAGIAN 5 — LOCK SETELAH SUBMIT

Setelah BA submitted, lakukan server-side lock.

Endpoint:

```text
saveSignature()
```

harus menolak perubahan jika BA sudah submitted.

Contoh HTTP:

```text
409 Conflict
```

atau response Laravel yang sesuai existing convention.

Message:

```text
Berita Acara telah disubmit. Reset Berita Acara terlebih dahulu untuk mengubah tanda tangan.
```

Frontend juga harus:

- tidak membuka signature modal;
- tidak menampilkan "Klik untuk tanda tangan";
- menampilkan signature existing secara read-only.

TETAPI backend guard tetap wajib.

---

# BAGIAN 6 — TABS PADA `/ttd/{token}`

Ubah halaman:

```text
resources/views/ttd.blade.php
```

menjadi memiliki:

```text
Tanda Tangan | Catatan
```

Gunakan referensi official Sneat Bootstrap free:

https://github.com/themeselection/sneat-bootstrap-html-admin-template-free/blob/main/html/ui-tabs-pills.html

Pelajari bagian **Basic Tabs**.

Gunakan Bootstrap native tab:

```html
<ul class="nav nav-tabs" role="tablist">
...
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active">
    </div>

    <div class="tab-pane fade">
    </div>
</div>
```

Jangan menambahkan library tab baru.

---

# TAB 1 — TANDA TANGAN

Pindahkan/pertahankan konten BA dan area signature existing ke:

```text
Tanda Tangan
```

Jangan redesign total.

Pertahankan identitas desain E-TTD sekarang:

```text
orange
cream
brown
warm neutral
```

Tab aktif gunakan aksen orange yang sama dengan halaman.

Contoh visual:

```text
┌─────────────────────────────────────────────┐
│  Tanda Tangan     Catatan                   │
│  ━━━━━━━━━━━━━                              │
│                                             │
│              BERITA ACARA                   │
│                                             │
│  ...                                        │
│                                             │
│  Ketua Asesor              Kepala Lembaga   │
│  [signature]               [signature]      │
│                                             │
│  Anggota Asesor                            │
│  [signature]                                │
│                                             │
│  Anggota Asesor                            │
│  [signature]                                │
│                                             │
└─────────────────────────────────────────────┘
```

Pastikan mobile responsive.

---

# TAB 2 — CATATAN

Tab kedua:

```text
Catatan
```

harus menampilkan **catatan visitasi/rekomendasi yang memang sudah tersimpan di PAPS**.

Sebelum implementasi:

Cari sumber datanya dari:

```text
PenilaianController
visitasi
penilaian
catatan
rekomendasi
subunsur
item penilaian
```

JANGAN langsung membuat tabel baru.

Jika data existing sudah ada, gunakan data tersebut.

Tampilkan read-only.

Prioritaskan layout yang mudah dibaca.

Misalnya jika data berbentuk item:

```text
Catatan Hasil Visitasi

Unsur 1 — ...
──────────────────────────
Subunsur 1.1
Catatan:
Lorem ipsum...

Subunsur 1.2
Catatan:
Lorem ipsum...
```

Jika catatan berasal dari asesor berbeda, tampilkan identitas asesor jika relevan.

Jika memang hanya ada satu catatan/rekomendasi, tampilkan satu card sederhana.

Jika tidak ada:

```text
Belum ada catatan hasil visitasi.
```

Jangan tampilkan raw HTML tanpa escaping.

---

# BAGIAN 7 — GENERATE BERITA ACARA

Halaman:

```text
/pengajuan/visitasi/{id}
```

menggunakan route:

```php
Route::get('/visitasi/{id}', [PenilaianController::class, 'visitasi'])
    ->name('visitasi');
```

Di halaman tersebut terdapat tombol:

```text
Generate BA
```

Route yang terlihat saat ini:

```php
Route::get('/generate-ba/{id}', [SekretariatController::class, 'generateBA'])
    ->name('generate.ba');
```

AUDIT dahulu function aktual yang digunakan.

Pastikan apakah BA dibuat menggunakan:

```text
PHPWord
DOCX template
DomPDF
HTML → PDF
atau mekanisme lain
```

Jangan mengganti engine generator kalau tidak perlu.

---

# BAGIAN 8 — TEMPELKAN DIGITAL SIGNATURE KE GENERATED BA

Saat Generate BA dipanggil:

Ambil signature:

```php
DigitalSignature::where('pengajuan_id', $pengajuan->id)
    ->where('status_ttd', 'signed')
```

Mapping:

```text
asesor1 → Ketua Tim Asesor
asesor2 → Anggota Tim Asesor #1
asesor3 → Anggota Tim Asesor #2
kepala  → Kepala/Pimpinan Instansi
```

File signature berasal dari field existing seperti:

```text
ttd
```

dan sekarang disimpan di:

```text
public/tandatangandigital/
```

Verifikasi implementasi aktual sebelum memakai path.

---

# SIGNATURE DI DOKUMEN

Signature harus ditempel **di atas nama masing-masing signer**, bukan di tempat arbitrary.

Konsep:

```text
Ketua Tim Asesor

     [PNG TTD]

Nama Ketua Asesor
```

```text
Anggota Tim Asesor

     [PNG TTD]

Nama Anggota
```

```text
Kepala Instansi

     [PNG TTD]

Nama Kepala
```

PNG signature harus:

- menjaga aspect ratio;
- transparent background tetap transparan;
- tidak stretched;
- ukuran proporsional;
- tidak menutup nama/jabatan;
- tidak merusak pagination BA.

Gunakan size yang seragam.

Jika menggunakan PHPWord:

- gunakan image insertion API PHPWord;
- jangan convert PNG ke base64 jika tidak diperlukan;
- resolve menggunakan real local file path;
- periksa file exists sebelum insert.

---

# BEHAVIOR JIKA SIGNATURE BELUM LENGKAP

Jangan membuat generator crash.

Jika hanya ada 2 dari 4 signature:

- masukkan 2 signature yang tersedia;
- signer lain mengikuti behavior template existing;

ATAU jika business rule existing mewajibkan 4/4:

- hentikan generate;
- redirect kembali;
- tampilkan message yang jelas:

```text
Berita Acara belum dapat digenerate karena tanda tangan belum lengkap (2/4).
```

Pilih berdasarkan flow existing dan paling sedikit mengubah behavior existing.

Catat keputusanmu di final report.

---

# BAGIAN 9 — SUBMITTED BA DAN GENERATE BA

Jika BA sudah submitted dan 4/4 signed:

Generate BA harus otomatis menggunakan signature existing.

Tidak boleh meminta:

```text
upload signature lagi
tanda tangan ulang
drawing ulang
```

Flow yang diinginkan:

```text
Visitasi
    ↓
Link E-TTD dibagikan
    ↓
Asesor 1 signed
Asesor 2 signed
Asesor 3 signed
Kepala signed
    ↓
4/4 complete
    ↓
SUBMIT BERITA ACARA
    ↓
BA locked
    ↓
Sekretariat membuka Visitasi
    ↓
Generate BA
    ↓
signature otomatis ditempel
    ↓
BA siap download
```

---

# BAGIAN 10 — SERVER-SIDE AUTHORIZATION

Ini WAJIB.

Public `/ttd/{token}` memang dapat diakses tanpa login.

Tetapi action administratif tidak boleh hanya protected oleh JS.

Pastikan:

```text
RESET BERITA ACARA
RESET individual signature
rotate token
```

hanya sekretariat.

Gunakan existing middleware:

```text
auth
is.sekretariat
```

atau authorization mechanism project yang sudah digunakan.

Jangan membuat role check hard-coded baru jika project sudah punya middleware/helper.

---

# BAGIAN 11 — JANGAN MERUSAK TOKEN E-TTD

Tetap gunakan token existing:

```text
/ttd/{40-char-token}
```

Jangan kembali memakai:

```text
/ttd/{pengajuan_id}
```

Jangan expose:

```text
pengajuan_id
database primary key
```

ke URL public.

---

# BAGIAN 12 — DATABASE MIGRATION

Hanya buat migration jika benar-benar diperlukan.

Jika perlu field status BA yang lebih eksplisit, audit dahulu field:

```text
berita_acara
```

dan existing usage-nya.

Jangan membuat:

```text
ba_status
submitted
is_submitted
```

secara redundant jika `berita_acara` yang sekarang sudah bisa menjadi source of truth dengan jelas.

Jika struktur existing buruk dan perlu field baru, gunakan migration Laravel yang backward-compatible.

Jangan edit migration lama yang sudah pernah dijalankan production.

---

# BAGIAN 13 — DATA CLEANUP

Audit database untuk kasus duplicate:

```text
pengajuan_id
jenis_user
signed
pending
```

Jika terdapat:

```text
asesor1 signed
asesor1 pending
```

prioritaskan signed.

Jangan menghapus file signature dari signed record.

Jika perlu cleanup command/migration:

- buat proses aman;
- idempotent;
- log jumlah data diperbaiki;
- jangan hapus data signed secara massal tanpa guard.

---

# BAGIAN 14 — TESTING WAJIB

Tambahkan/update test yang relevan.

Minimal test:

### Signature

```text
0/4 → incomplete
1/4 → incomplete
2/4 → incomplete
3/4 → incomplete
4/4 → complete
```

### Duplicate prevention

Pastikan:

```text
pengajuan_id X + asesor1
```

tidak menghasilkan:

```text
signed + pending
```

setelah submit.

### Submit BA

```text
4/4 + belum submit
→ submit berhasil
```

```text
<4/4
→ submit ditolak
```

### Submitted

```text
submitted
→ public tidak bisa modify signature
```

```text
submitted
→ public tidak melihat reset
```

```text
submitted
→ sekretariat melihat RESET BERITA ACARA
```

### Reset BA

```text
submitted + sekretariat
→ reset berhasil
→ signature tetap 4
→ signed tetap 4
→ BA editable kembali
```

```text
submitted + non-sekretariat
→ 403
```

### Generate BA

```text
4 signature tersedia
→ BA generated
→ empat image signature ditempel
```

Pastikan mapping tidak tertukar:

```text
asesor1
asesor2
asesor3
kepala
```

---

# BAGIAN 15 — MANUAL REGRESSION CHECK

Periksa manual:

```text
/ttd/{valid-token}
```

desktop dan mobile.

Pastikan:

- tab berfungsi;
- Tanda Tangan aktif secara default;
- Catatan dapat dibuka;
- 4 signature tampil;
- status benar;
- tidak ada `pending` palsu;
- modal signature bekerja sebelum submit;
- modal tidak bisa digunakan setelah submit;
- RESET hanya muncul untuk sekretariat;
- reset membuka kembali BA;
- Generate BA menghasilkan dokumen dengan signature.

---

# CLEAN CODE

Jangan menambah patch JavaScript besar untuk menutupi masalah backend.

Prioritas:

```text
backend source of truth
→ controller/service/model
→ Blade
→ JavaScript hanya untuk interaction
```

Jika logic mulai besar, ekstrak helper/service secara wajar.

Hindari:

```text
duplicate query
duplicate business logic
hard-coded role
hard-coded pengajuan ID
hard-coded signature path
```

Gunakan route helper Laravel jika memungkinkan daripada hard-coded URL seperti:

```js
fetch('/ettd/save-signature')
```

---

# UI

Jangan redesign keseluruhan halaman `/ttd`.

Pertahankan desain existing.

Gunakan tema:

```text
primary orange
warm cream
brown text
light orange border
```

Tabs harus terasa native dengan E-TTD sekarang.

Gunakan Sneat Basic Tabs hanya sebagai referensi struktur/behavior.

Jangan membuat UI terlihat seperti template AI generik.

---

# ACCEPTANCE CRITERIA

Task dianggap selesai hanya jika seluruh kondisi berikut terpenuhi:

- [ ] Tidak ada duplicate pending ketika signer sudah signed.
- [ ] 4 signature = status 4/4.
- [ ] Setelah submit, status menjadi “Berita Acara telah disubmit”.
- [ ] Tombol Submit hilang setelah submitted.
- [ ] Sekretariat melihat tombol Reset BA.
- [ ] Non-sekretariat tidak melihat tombol Reset BA.
- [ ] Reset BA tidak menghapus signature.
- [ ] Setelah submitted signature tidak dapat diubah.
- [ ] `/ttd` mempunyai tab Tanda Tangan.
- [ ] `/ttd` mempunyai tab Catatan.
- [ ] Catatan berasal dari data existing PAPS.
- [ ] Generate BA membaca DigitalSignature.
- [ ] Signature asesor1 ditempel di Ketua Asesor.
- [ ] Signature asesor2 ditempel di Anggota Asesor.
- [ ] Signature asesor3 ditempel di Anggota Asesor.
- [ ] Signature kepala ditempel di Kepala Instansi.
- [ ] PNG tidak stretched.
- [ ] Existing E-TTD token tetap bekerja.
- [ ] Authorization reset dilakukan backend.
- [ ] Test suite existing tetap pass.
- [ ] Test baru untuk workflow ini pass.

---

# FINAL OUTPUT

Setelah implementation selesai, laporkan:

## Root Cause

Jelaskan penyebab pending signature.

## Architecture

Jelaskan state transition final:

```text
pending → signed → BA submitted → reset(optional)
```

## Files Changed

List semua file.

## Database Changes

Jelaskan migration/data cleanup jika ada.

## E-TTD Changes

Jelaskan behavior baru.

## Catatan Tab

Jelaskan sumber data yang digunakan.

## Generate BA

Jelaskan cara signature dimasukkan ke dokumen.

## Authorization

Jelaskan protection endpoint.

## Tests

Laporkan command dan hasilnya.

Contoh:

```bash
php artisan test
```

## Commit

Jika seluruh test pass:

```bash
git status
git diff
```

Review diff terlebih dahulu.

Kemudian commit dengan message:

```text
feat: finalize e-signature workflow and integrate signatures into BA
```

JANGAN push ke remote kecuali memang environment/instruksi agent secara eksplisit mengizinkan push.

Jangan melakukan unrelated refactor.
Jangan mengganti framework.
Jangan upgrade dependency besar.
Jangan mengubah desain halaman lain.
