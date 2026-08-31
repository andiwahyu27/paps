# PRD: Tab Rekomendasi Hasil Akreditasi pada TTD Sidang

**Status:** Draft untuk persetujuan implementasi  
**Jenis dokumen:** Product Requirements Document / Implementation Specification  
**Project:** PAPS  
**Halaman target:** `resources/views/ttd-sidang.blade.php`  
**Template referensi:** `template_hasil_visitasi.docx`  
**Scope dokumen:** Perencanaan dan spesifikasi; belum melakukan perubahan kode, database, commit, push, atau deploy.

---

## 1. Ringkasan

Menambahkan tab baru di halaman `ttd-sidang.blade.php` di sebelah tab **Catatan Asesor** dengan label **Rekomendasi Hasil Akreditasi**.

Tab tersebut digunakan oleh role **asesor** dan **sekretariat** untuk memasukkan, meninjau, mengubah, menghapus, dan mengirim rekomendasi hasil akreditasi dalam dua kelompok:

1. **Hal-hal yang harus dipertahankan** — dapat memiliki banyak baris.
2. **Hal-hal yang harus diperbaiki** — dapat memiliki banyak baris.

Tab menyediakan tombol:

- **Simpan/Submit Rekomendasi**.
- **Export to PDF** dengan format berdasarkan template `template_hasil_visitasi.docx`.

Variabel berikut diisi otomatis oleh sistem dan tidak dientri manual:

- `tahun_pengajuan`
- `jenis_pengajuan`
- `nilai_final`
- `predikat_final`

---

## 2. Tujuan

1. Menyediakan tempat resmi untuk memasukkan rekomendasi hasil akreditasi pada alur BA/TTD Sidang.
2. Memisahkan rekomendasi hasil akreditasi dari catatan penilaian asesor.
3. Menjamin satu pengajuan dapat memiliki banyak poin rekomendasi untuk masing-masing kategori.
4. Menyediakan dokumen PDF yang konsisten dengan format template terlampir.
5. Menjamin nilai dan predikat final yang tampil pada dokumen selalu berasal dari data final pengajuan, bukan input manual.
6. Mempertahankan kompatibilitas alur TTD Sidang yang sudah berjalan.

---

## 3. Di luar cakupan

Hal berikut tidak termasuk dalam PRD ini kecuali disetujui kemudian:

- Mengubah formula nilai final.
- Mengubah formula predikat final.
- Mengubah workflow tanda tangan BA Sidang.
- Mengubah isi tab Catatan Asesor.
- Mengubah template DOCX sumber secara langsung.
- Menambah aktor baru selain asesor dan sekretariat.
- Mengirim PDF melalui email atau Telegram secara otomatis.
- Mengizinkan lembaga/pengguna umum mengubah rekomendasi.

---

## 4. Analisis template DOCX

Template terlampir berisi teks dan tabel dengan struktur berikut:

```text
HASIL PENILAIAN AKREDITASI PROGRAM PELATIHAN TEKNIS DI BIDANG
STATISTIK DAN SISTEM TEKNOLOGI BERBASIS KOMPUTER
TAHUN ${tahun_pengajuan}

Lembaga Pelatihan: ${nama_lemdik}
Program Pelatihan: ${jenis_pengajuan}
Nilai: ${nilai_final} (${predikat_final})

Rekomendasi Hasil Akreditasi
Hal-hal yang harus dipertahankan
Hal-hal yang harus diperbaiki
```

### Placeholder otomatis

| Placeholder | Sumber sistem | Aturan |
|---|---|---|
| `${tahun_pengajuan}` | `tb_pengajuans.created_at` atau sumber tahun pengajuan resmi yang disepakati | Format empat digit, contoh `2026` |
| `${nama_lemdik}` | Relasi `$pengajuan->profile->nama_lembaga` | Read-only, tidak berasal dari input rekomendasi |
| `${jenis_pengajuan}` | `tb_pengajuans.id_jenis` / relasi jenis | `1` = Sistem Teknologi Berbasis Komputer; selain itu mengikuti mapping resmi aplikasi |
| `${nilai_final}` | `tb_pengajuans.nilai_final` atau sumber nilai final resmi | Read-only, gunakan nilai final terbaru |
| `${predikat_final}` | `tb_pengajuans.predikat_final` atau perhitungan resmi aplikasi | Read-only, tidak boleh dientri manual |

### Data dinamis rekomendasi

Template menyediakan area untuk dua kategori rekomendasi, tetapi tidak menyediakan jumlah baris tetap. Generator PDF wajib mengisi area tersebut secara dinamis dari database:

- Setiap poin `hal_hal_dipertahankan` menjadi satu baris/item.
- Setiap poin `hal_hal_diperbaiki` menjadi satu baris/item.
- Jika kategori kosong, tampilkan `-` atau teks `Belum ada rekomendasi`, sesuai keputusan UI final.
- Urutan poin mengikuti kolom `urutan` ascending.

**Catatan teknis:** placeholder yang terpecah antar XML run harus dideteksi dengan menggabungkan seluruh node `<w:t>` sebelum analisis. Template tidak boleh diedit atau ditimpa selama implementasi tanpa persetujuan.

---

## 5. Aktor dan otorisasi

| Aktor | Lihat tab | Input/edit/delete | Submit | Export PDF |
|---|---:|---:|---:|---:|
| Asesor | Ya | Ya | Ya | Ya |
| Sekretariat | Ya | Ya | Ya | Ya |
| Lembaga/pengguna umum | Tidak melalui endpoint pengelolaan | Tidak | Tidak | Tidak, kecuali nanti dibuat endpoint publik khusus |

### Aturan authorization

1. Endpoint mutasi wajib memakai autentikasi dan middleware role asesor/sekretariat yang telah berlaku di PAPS.
2. Controller tetap melakukan pengecekan role internal jika pola controller yang ada memerlukannya.
3. Pengguna hanya boleh mengakses rekomendasi berdasarkan `pengajuan_id` yang sedang diproses.
4. Jangan mempercayai `pengajuan_id` dari request tanpa validasi ownership/akses.
5. Endpoint export PDF harus memvalidasi pengajuan dan hak akses sebelum menghasilkan file.
6. CSRF wajib aktif untuk request POST/PUT/PATCH/DELETE dari halaman.

---

## 6. Desain data

### 6.1 Rekomendasi tabel

**Tabel yang direkomendasikan:** `tr_rekomendasi_hasil_akreditasi`

Satu tabel detail dengan kolom kategori dipilih agar setiap pengajuan dapat memiliki banyak poin untuk kedua kelompok tanpa menambah dua tabel yang memiliki struktur sama.

| Kolom | Tipe | Null | Aturan |
|---|---|---:|---|
| `id` | bigint unsigned | Tidak | Primary key |
| `pengajuan_id` | integer/bigint sesuai `tb_pengajuans.id` | Tidak | Foreign key ke `tb_pengajuans.id` |
| `kategori` | varchar(40) / enum terkontrol | Tidak | Nilai: `dipertahankan` atau `diperbaiki` |
| `isi` | text | Tidak | Isi satu poin rekomendasi |
| `urutan` | unsigned integer | Tidak | Default `1`, digunakan untuk sorting |
| `created_by` | bigint unsigned/integer nullable | Ya | ID user pembuat, bila kompatibel dengan struktur users |
| `updated_by` | bigint unsigned/integer nullable | Ya | ID user terakhir yang mengubah |
| `created_at` | timestamp | Ya | Timestamp Laravel |
| `updated_at` | timestamp | Ya | Timestamp Laravel |

### Index dan constraint

- Foreign key `pengajuan_id` ke `tb_pengajuans.id` dengan tipe yang kompatibel.
- Index gabungan: `pengajuan_id`, `kategori`, `urutan`.
- Validasi kategori hanya menerima dua nilai yang ditentukan.
- `isi` wajib setelah trim, panjang maksimum mengikuti kebutuhan bisnis; rekomendasi awal `max:5000` karakter per poin.
- `urutan` harus bilangan bulat non-negatif.
- Hapus rekomendasi harus dibatasi pada `pengajuan_id` yang sedang diakses.

### 6.2 Kenapa tidak memakai dua kolom text pada tabel pengajuan?

Kolom seperti `hal_hal_dipertahankan` dan `hal_hal_diperbaiki` pada `tb_pengajuans` tidak direkomendasikan karena kebutuhan adalah `1:*`. Menyimpan banyak poin dalam satu kolom text akan menyulitkan:

- tambah/hapus satu poin;
- pengurutan poin;
- audit perubahan;
- validasi;
- pemakaian ulang untuk export;
- pemisahan kategori.

### 6.3 Status submit

Status submit rekomendasi sebaiknya disimpan terpisah dari status BA Sidang karena keduanya memiliki lifecycle berbeda.

**Opsi yang direkomendasikan:** menambah field pada `tb_pengajuans`:

| Kolom | Tipe | Aturan |
|---|---|---|
| `rekomendasi_akreditasi_submitted_at` | timestamp nullable | Null = belum submit; terisi = sudah submit |
| `rekomendasi_akreditasi_submitted_by` | integer nullable | User yang melakukan submit |

Status timestamp lebih informatif daripada boolean dan konsisten dengan pola `ba_sidang_submitted_at`.

Jika user belum menginginkan kolom status tambahan, status dapat dihitung dari keberadaan data, tetapi opsi tersebut tidak dapat membedakan **tersimpan** dan **sudah disubmit**. Keputusan final diperlukan sebelum implementasi.

---

## 7. Model

File yang direncanakan:

```text
app/Models/RekomendasiHasilAkreditasi.php
```

Relasi yang direncanakan:

```php
public function pengajuan()
{
    return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
}
```

`Pengajuan` dapat memiliki relasi:

```php
public function rekomendasiHasilAkreditasi()
{
    return $this->hasMany(RekomendasiHasilAkreditasi::class, 'pengajuan_id');
}
```

Model harus memiliki fillable yang eksplisit, minimal:

```text
pengajuan_id
kategori
isi
urutan
created_by
updated_by
```

Jangan menggunakan mass assignment tanpa `$fillable` yang jelas.

---

## 8. UI/UX tab baru

### 8.1 Posisi tab

Tab baru ditempatkan di sebelah tab **Catatan Asesor**:

```text
[Tanda Tangan] [Catatan Asesor] [Rekomendasi Hasil Akreditasi]
```

Nama tab wajib persis:

```text
Rekomendasi Hasil Akreditasi
```

Gunakan mekanisme tab yang sama dengan `ettd-tabs` dan `ettd-panel` yang sudah dipakai halaman `ttd-sidang.blade.php`.

### 8.2 Isi tab

Header tab:

```text
REKOMENDASI HASIL AKREDITASI
```

Ringkasan otomatis read-only:

```text
Tahun Pengajuan: [otomatis]
Jenis Pengajuan: [otomatis]
Nilai Final: [otomatis]
Predikat Final: [otomatis]
```

Form kategori pertama:

```text
Hal-hal yang harus dipertahankan
[textarea/input poin 1] [Hapus]
[textarea/input poin 2] [Hapus]
[+ Tambah Hal yang Dipertahankan]
```

Form kategori kedua:

```text
Hal-hal yang harus diperbaiki
[textarea/input poin 1] [Hapus]
[textarea/input poin 2] [Hapus]
[+ Tambah Hal yang Diperbaiki]
```

Tombol aksi:

```text
SIMPAN REKOMENDASI
SUBMIT REKOMENDASI
EXPORT TO PDF
```

### 8.3 Aturan interaksi

1. Asesor/sekretariat dapat menambah banyak baris pada masing-masing kategori.
2. Baris kosong tidak boleh disimpan.
3. Saat edit, data lama dimuat kembali berdasarkan `pengajuan_id` dan `kategori`.
4. Tombol hapus menghapus baris dari payload; penghapusan database harus eksplisit dan aman.
5. Setelah submit, rekomendasi menjadi read-only atau memerlukan alur buka kembali yang disepakati.
6. Tombol `EXPORT TO PDF` tetap dapat digunakan setelah submit.
7. Tampilkan toast sukses/gagal dengan komponen toast yang sudah ada, bukan membuat toast duplikat.
8. Jangan menggunakan emoji dekoratif pada label tombol.

### 8.4 Status UI

| Status | Input | Submit | Export PDF |
|---|---:|---:|---:|
| Belum ada data | Aktif | Aktif setelah valid | Aktif atau tampilkan peringatan data kosong |
| Tersimpan, belum submit | Aktif | Aktif | Aktif |
| Sudah submit | Read-only | Nonaktif/sembunyikan | Aktif |

Keputusan apakah rekomendasi boleh diedit kembali setelah submit harus mengikuti keputusan bisnis. Jika diperlukan, sediakan endpoint reset/reopen khusus sekretariat, bukan menghapus status secara diam-diam.

---

## 9. Controller dan service

### 9.1 Controller target

Direkomendasikan menambah controller khusus agar `TtdSidangController` tidak semakin besar:

```text
app/Http/Controllers/RekomendasiHasilAkreditasiController.php
```

Method minimal:

```text
index/show($pengajuanId)
store($pengajuanId)
update($pengajuanId)
destroy($pengajuanId, $itemId)
submit($pengajuanId)
exportPdf($pengajuanId)
```

Alternatif minimal adalah menempatkan method pada `TtdSidangController`, tetapi tidak direkomendasikan karena controller tersebut sudah menangani token, tanda tangan, submit BA, reset, dan DOCX.

### 9.2 Aturan penyimpanan

- Validasi semua item kategori.
- Gunakan database transaction saat menyimpan batch dua kategori.
- Jangan menghapus seluruh data lebih dulu tanpa transaction dan tanpa mempertahankan item yang tidak berubah.
- Rekomendasi: gunakan payload dengan ID untuk item lama dan operasi create/update/delete terkontrol.
- Simpan `created_by` dan `updated_by` jika kolom user tersedia.
- Setelah berhasil, redirect kembali atau return JSON sesuai pola halaman; tampilkan pesan sukses.

### 9.3 Aturan submit

Sebelum submit:

- pengajuan harus valid;
- minimal satu poin rekomendasi harus tersedia, kecuali keputusan bisnis membolehkan dua kategori kosong;
- semua poin harus valid dan tidak kosong;
- user harus asesor atau sekretariat;
- jika status sudah submitted, tolak request dengan HTTP `409` atau response bisnis yang konsisten.

Saat submit:

- isi `rekomendasi_akreditasi_submitted_at`;
- simpan user submit;
- jangan mengubah `final`, `ba_sidang_submitted_at`, atau status tanda tangan.

---

## 10. Route yang direncanakan

Nama route hanya usulan dan perlu disesuaikan dengan konvensi final project:

```php
Route::get('/ttd-sidang/{pengajuan}/rekomendasi', [RekomendasiHasilAkreditasiController::class, 'show'])
    ->middleware(['auth', 'is.asesor.or.sekretariat'])
    ->name('ttd.sidang.rekomendasi.show');

Route::post('/ttd-sidang/{pengajuan}/rekomendasi', [RekomendasiHasilAkreditasiController::class, 'store'])
    ->middleware(['auth', 'is.asesor.or.sekretariat'])
    ->name('ttd.sidang.rekomendasi.store');

Route::post('/ttd-sidang/{pengajuan}/rekomendasi/submit', [RekomendasiHasilAkreditasiController::class, 'submit'])
    ->middleware(['auth', 'is.asesor.or.sekretariat'])
    ->name('ttd.sidang.rekomendasi.submit');

Route::get('/ttd-sidang/{pengajuan}/rekomendasi/export-pdf', [RekomendasiHasilAkreditasiController::class, 'exportPdf'])
    ->middleware(['auth', 'is.asesor.or.sekretariat'])
    ->name('ttd.sidang.rekomendasi.export.pdf');
```

Jika tab menggunakan halaman token publik `/ttd-sidang/{token}`, endpoint sebaiknya menggunakan token yang sudah ada dan tetap melakukan authorization mutasi secara ketat. Jangan menaruh `pengajuan_id` terbuka di endpoint publik tanpa pengecekan tambahan.

---

## 11. Export PDF

### 11.1 Sumber data

Export PDF mengambil:

- metadata otomatis dari `Pengajuan` dan relasi profile/jenis;
- rekomendasi kategori `dipertahankan` dari tabel baru;
- rekomendasi kategori `diperbaiki` dari tabel baru;
- nilai dan predikat final dari sumber resmi aplikasi.

### 11.2 Library

`composer.json` saat ini sudah memiliki:

```text
barryvdh/laravel-dompdf
```

Implementasi direkomendasikan menggunakan Dompdf melalui view khusus:

```text
resources/views/pdf/rekomendasi-hasil-akreditasi.blade.php
```

Jangan mengandalkan DOCX sebagai input langsung Dompdf. Template DOCX digunakan sebagai acuan format; PDF perlu dibuat melalui Blade/CSS atau melalui pipeline konversi DOCX yang memang tersedia di production.

### 11.3 Format PDF

PDF minimal memuat:

1. Judul **HASIL PENILAIAN AKREDITASI**.
2. Deskripsi program pelatihan sesuai template.
3. Tahun pengajuan.
4. Lembaga pelatihan.
5. Program pelatihan.
6. Nilai final dan predikat final.
7. Bagian **Rekomendasi Hasil Akreditasi**.
8. Tabel/daftar **Hal-hal yang harus dipertahankan**.
9. Tabel/daftar **Hal-hal yang harus diperbaiki**.

Nama file yang direkomendasikan:

```text
Rekomendasi Hasil Akreditasi - {nama_lembaga}.pdf
```

Nama file harus disanitasi agar tidak mengandung path traversal atau karakter ilegal.

### 11.4 Aturan placeholder

Jangan menampilkan placeholder mentah seperti `${tahun_pengajuan}` pada PDF. Jika nilai otomatis tidak tersedia, tampilkan `-` dan catat warning di log aplikasi; jangan mengambil input manual untuk nilai final.

---

## 12. Business rules

1. Satu `pengajuan` memiliki nol atau banyak poin `dipertahankan`.
2. Satu `pengajuan` memiliki nol atau banyak poin `diperbaiki`.
3. Satu row hanya memiliki satu kategori.
4. Poin ditampilkan berdasarkan `urutan`, lalu `id` sebagai tie-breaker.
5. `tahun_pengajuan`, `jenis_pengajuan`, `nilai_final`, dan `predikat_final` tidak boleh diubah dari tab ini.
6. Rekomendasi tidak boleh memengaruhi nilai final atau predikat final.
7. Submit rekomendasi tidak otomatis submit BA Sidang.
8. Submit BA Sidang tidak otomatis membuat atau mengubah isi rekomendasi.
9. Export PDF tidak mengubah data atau status submit.
10. Jika rekomendasi sudah submit, perubahan harus ditolak sampai ada mekanisme reopen/reset yang disetujui.
11. Semua operasi mutasi harus memiliki CSRF dan authorization.
12. XSS harus dicegah melalui escaping Blade; isi rekomendasi ditampilkan sebagai teks, bukan HTML mentah.

---

## 13. Migration yang direncanakan

Nama migration usulan:

```text
database/migrations/YYYY_MM_DD_HHMMSS_create_rekomendasi_hasil_akreditasi_table.php
```

Jika status submit disimpan di `tb_pengajuans`, migration kedua:

```text
database/migrations/YYYY_MM_DD_HHMMSS_add_rekomendasi_akreditasi_status_to_tb_pengajuans.php
```

Migration wajib:

- tidak menggunakan `migrate:fresh` di production;
- memeriksa kompatibilitas tipe foreign key dengan `tb_pengajuans.id`;
- memakai `down()` yang aman;
- diuji pada database lokal/staging sebelum production;
- tidak menghapus data pengajuan yang sudah ada.

---

## 14. Target file implementasi

### File baru

```text
app/Models/RekomendasiHasilAkreditasi.php
app/Http/Controllers/RekomendasiHasilAkreditasiController.php
resources/views/pdf/rekomendasi-hasil-akreditasi.blade.php
database/migrations/*_create_rekomendasi_hasil_akreditasi_table.php
```

### File yang mungkin dimodifikasi

```text
resources/views/ttd-sidang.blade.php
app/Models/Pengajuan.php
routes/web.php
composer.json                         # hanya jika library PDF ternyata belum tersedia
composer.lock                         # hanya jika dependency berubah
resources/views/layouts/...            # hanya jika dibutuhkan oleh UI
```

### Dokumentasi/template

```text
dokumen_md/plan_rekomendasi_hasil_akreditasi_sidang.md
public/template_hasil_visitasi.docx   # jangan ubah tanpa persetujuan
```

---

## 15. Acceptance criteria

### Tab dan role

- [ ] Tab **Rekomendasi Hasil Akreditasi** tampil di sebelah tab **Catatan Asesor**.
- [ ] Asesor dapat membuka dan mengelola tab.
- [ ] Sekretariat dapat membuka dan mengelola tab.
- [ ] Role lain tidak dapat melakukan mutasi.
- [ ] Tab Catatan Asesor dan Tanda Tangan tetap berfungsi.

### Data dan form

- [ ] Satu pengajuan dapat menyimpan banyak poin `dipertahankan`.
- [ ] Satu pengajuan dapat menyimpan banyak poin `diperbaiki`.
- [ ] Poin kosong ditolak.
- [ ] Urutan poin tersimpan dan tampil konsisten.
- [ ] Data lama termuat kembali saat halaman dibuka.
- [ ] Nilai otomatis tampil read-only.
- [ ] Data rekomendasi tidak mengubah nilai/predikat final.

### Submit

- [ ] Tombol submit tersedia untuk asesor/sekretariat.
- [ ] Submit menyimpan timestamp dan user submit jika status tracking disetujui.
- [ ] Submit kedua kali ditolak atau ditangani idempotently sesuai keputusan final.
- [ ] Setelah submit, UI menjadi read-only sesuai business rule.
- [ ] Submit rekomendasi tidak mengubah status submit BA Sidang.

### PDF

- [ ] Tombol `EXPORT TO PDF` tersedia bagi asesor/sekretariat.
- [ ] PDF berhasil diunduh dengan nama file aman.
- [ ] PDF berisi tahun pengajuan, jenis pengajuan, nilai final, predikat final, dan nama lembaga.
- [ ] PDF berisi seluruh poin dipertahankan dalam urutan benar.
- [ ] PDF berisi seluruh poin diperbaiki dalam urutan benar.
- [ ] Tidak ada placeholder `${...}` yang tersisa.
- [ ] Data kosong memiliki fallback yang jelas.
- [ ] Export tidak mengubah database.

### Keamanan dan regresi

- [ ] Endpoint mutasi dilindungi auth, role middleware, CSRF, dan validasi akses pengajuan.
- [ ] Isi rekomendasi di-escape dari XSS.
- [ ] Request dengan pengajuan tidak valid ditolak.
- [ ] User tanpa role sesuai tidak dapat submit atau export melalui endpoint terproteksi.
- [ ] Workflow generate/sign/submit/reset TTD Sidang lama tetap berjalan.
- [ ] Route existing tidak tertimpa route baru.

---

## 16. Verifikasi teknis wajib saat implementasi

```bash
cd /home/ubuntu/paps

php -l app/Models/RekomendasiHasilAkreditasi.php
php -l app/Http/Controllers/RekomendasiHasilAkreditasiController.php
php -l database/migrations/<migration>.php

php artisan migrate --force
php artisan view:cache
php artisan route:cache
git diff --check

php artisan route:list | grep -E 'rekomendasi|ttd-sidang'
```

### Verifikasi database

```bash
php artisan tinker --execute='
$r=App\\Models\\RekomendasiHasilAkreditasi::query()->first();
echo "MODEL=".($r ? "ok" : "empty").PHP_EOL;
'
```

Test dengan fixture nyata wajib memeriksa:

- create dua kategori;
- update satu poin;
- delete satu poin;
- sorting `urutan`;
- submit;
- akses role asesor;
- akses role sekretariat;
- penolakan role lain;
- export PDF dan pemeriksaan file PDF.

### Verifikasi PDF

```bash
pdfinfo "hasil-rekomendasi.pdf"
pdftotext "hasil-rekomendasi.pdf" - | grep -E 'HASIL PENILAIAN|Rekomendasi Hasil Akreditasi|dipertahankan|diperbaiki'
```

Jika `pdfinfo` atau `pdftotext` tidak tersedia, gunakan parser PDF yang tersedia di environment dan nyatakan keterbatasannya; jangan mengklaim isi PDF tanpa membaca hasil file.

---

## 17. Risiko dan mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Template DOCX memiliki placeholder terpecah antar run | Mapping salah | Gabungkan node XML saat inspeksi; uji output |
| Nilai final diambil dari sumber yang salah | Dokumen resmi tidak konsisten | Tetapkan satu sumber resmi dan uji dengan fixture nyata |
| Dua kategori disimpan sebagai satu text panjang | Sulit edit/sort/audit | Gunakan tabel child berkategori |
| Submit rekomendasi tercampur dengan submit BA | Status bisnis salah | Pisahkan status timestamp rekomendasi dari BA Sidang |
| Export PDF tidak sama dengan template | Dokumen ditolak pengguna | Buat view PDF dengan struktur dan styling yang ditinjau |
| Endpoint memakai `pengajuan_id` tanpa authorization | Data bocor/diubah | Role middleware + validasi akses di controller |
| Edit setelah submit tidak jelas | Data resmi berubah tanpa audit | Lock setelah submit dan sediakan reopen terkontrol |
| Controller TTD Sidang membesar | Maintenance sulit | Controller/service rekomendasi terpisah |
| Migration foreign key tidak kompatibel | Migration production gagal | Cocokkan tipe kolom dengan schema aktual sebelum migration |

---

## 18. Keputusan yang perlu dikonfirmasi sebelum implementasi

1. Apakah status submit rekomendasi harus disimpan dengan:
   - `rekomendasi_akreditasi_submitted_at` dan `rekomendasi_akreditasi_submitted_by`; atau
   - cukup dihitung dari keberadaan data?
2. Setelah submit, apakah rekomendasi benar-benar dikunci?
3. Jika dikunci, siapa yang boleh membuka kembali: hanya sekretariat atau asesor dan sekretariat?
4. Apakah minimal satu poin rekomendasi wajib di setiap kategori, atau kategori boleh kosong?
5. Apakah export yang diminta benar-benar **PDF**, bukan DOCX berdasarkan template?
6. Apakah template terlampir hanya menjadi acuan format, atau harus dikonversi melalui DOCX-to-PDF agar layout identik?
7. Apakah `tahun_pengajuan` harus berasal dari `created_at`, atau ada field tahun pengajuan resmi lain?
8. Apakah PDF perlu memuat tanda tangan Majelis Sidang, atau hanya rekomendasi dan metadata?
9. Apakah tombol tab dan form harus tersedia setelah BA Sidang submitted, atau menjadi read-only bersama status BA?

---

## 19. Rencana implementasi bertahap

### Tahap 1 — Schema dan model

- Buat migration tabel rekomendasi.
- Jika disetujui, tambah status submit pada `tb_pengajuans`.
- Buat model dan relasi.
- Jalankan migration lokal dan verifikasi schema.

### Tahap 2 — Controller dan route

- Buat controller khusus.
- Tambahkan authorization dan validation.
- Implementasikan create/update/delete batch.
- Implementasikan submit dan lock.

### Tahap 3 — UI tab

- Tambahkan tab di `ttd-sidang.blade.php`.
- Tambahkan form dinamis dua kategori.
- Muat data existing.
- Tambahkan toast dan state read-only.

### Tahap 4 — Export PDF

- Buat Blade PDF.
- Implementasikan Dompdf.
- Mapping metadata otomatis.
- Mapping semua item rekomendasi.
- Uji teks dan layout PDF.

### Tahap 5 — Regression dan deployment

- Jalankan lint, migration, view cache, route cache, dan diff check.
- Uji role asesor/sekretariat.
- Uji alur TTD Sidang lama.
- Uji export PDF dengan data kosong dan data banyak.
- Commit/push/deploy hanya setelah persetujuan dan verifikasi.
