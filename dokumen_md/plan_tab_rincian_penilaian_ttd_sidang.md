# PRD: Mengubah Tab "Catatan Asesor" menjadi "Rincian Penilaian" pada TTD Sidang

**Status:** Implemented locally — belum commit/push/deploy
**Jenis dokumen:** Product Requirements Document / Implementation Specification
**Project:** PAPS
**Halaman target:** `resources/views/ttd-sidang.blade.php`
**Template referensi:** `template_rincian_unsur_penilaian.docx`
**Scope dokumen:** Perencanaan dan spesifikasi; belum melakukan perubahan kode, database, commit, push, atau deploy.

---

## 1. Ringkasan

Tab **Catatan Asesor** pada halaman `ttd-sidang.blade.php` diganti namanya menjadi **Rincian Penilaian**, dengan isi berupa tabel hierarkis Unsur → Subunsur → Item beserta bobot dan nilai masing-masing, mengikuti format `template_rincian_unsur_penilaian.docx`.

Tab ini bersifat **read-only** (murni menampilkan hasil penilaian yang sudah tersimpan di sistem), sama seperti sifat tab Catatan Asesor sebelumnya dan tab Rekomendasi Hasil Akreditasi (lihat `plan_rekomendasi_hasil_akreditasi_sidang.md` — pola: entri di halaman terpisah oleh asesor/sekretariat, hasilnya ditampilkan read-only di `ttd-sidang.blade.php`).

Tab menyediakan tombol:

- **Export to DOCX** dengan format berdasarkan `template_rincian_unsur_penilaian.docx` (konsisten dengan keputusan project: seluruh dokumen sidang di-generate sebagai DOCX via `PhpOffice\PhpWord\TemplateProcessor`, bukan PDF/Dompdf).

---

## 2. Tujuan

1. Mengganti isi tab "Catatan Asesor" menjadi rincian penilaian terstruktur sesuai format resmi template.
2. Menampilkan struktur Unsur → Subunsur → Item beserta bobot secara dinamis dari database, bukan hardcode.
3. Menampilkan nilai per subunsur dan per unsur (kolom (2) dan (3) pada template) secara akurat sesuai sumber nilai resmi aplikasi.
4. Menampilkan nilai akreditasi final dan predikat final dari sumber resmi yang sama dipakai fitur lain (konsisten dengan `plan_rekomendasi_hasil_akreditasi_sidang.md` §4).
5. Menyediakan export DOCX yang identik dengan template terlampir.
6. Tidak mengubah logika/formula perhitungan nilai unsur, subunsur, atau final.

---

## 3. Di Luar Cakupan

- Mengubah formula/algoritma penilaian, bobot, atau cara agregasi nilai unsur/subunsur/final.
- Mengubah data master `mt_unsurs`, `mt_subunsurs`, `mt_items`.
- Mengubah workflow penilaian asesor (Pra-Visit, Visitasi, Paska-Visit, Final).
- Mengubah tab Tanda Tangan atau tab Rekomendasi Hasil Akreditasi.
- Mengubah template DOCX sumber secara langsung.
- Membuat halaman entri baru — tab ini murni tampilan, tidak ada input manual.

---

## 4. WAJIB: Audit Sebelum Implementasi

Sebelum menulis kode, audit hal-hal berikut dan laporkan temuan aktual (jangan asumsikan):

1. **Isi tab Catatan Asesor saat ini** di `ttd-sidang.blade.php` — data apa yang ditampilkan, dari controller/query mana asalnya, apakah ada endpoint API terpisah yang perlu diubah/dipertahankan.
2. **Struktur data unsur/subunsur/item aktual di database** (`mt_unsurs`, `mt_subunsurs`, `mt_items`) — apakah jumlah unsur benar-benar tetap 6 seperti pada template contoh, atau bervariasi tergantung `jenis_pengajuan` (Pranata Komputer vs Statistisi). **Template ini kemungkinan besar hanya representasi untuk satu jenis pengajuan** — audit apakah ada template/struktur berbeda untuk jenis pengajuan lain, atau apakah satu template ini generik untuk semua jenis.
3. **Bagaimana nilai subunsur dan nilai unsur (kolom (2) dan (3) di template) dihitung/disimpan saat ini** — apakah:
   - tersimpan langsung sebagai kolom di database per subunsur/unsur (hasil agregasi yang sudah dihitung backend), atau
   - harus dihitung on-the-fly dari `tb_penilaians` (nilai per item × bobot item, dijumlahkan ke subunsur, lalu ke unsur) menggunakan formula yang **sama persis** dengan yang dipakai untuk menghitung `nilai_final`.

   Ini adalah titik paling rawan (lihat poin 6 di kebutuhan Anda: "perlu hati-hati dalam menampilkannya"). **Jangan membuat kalkulasi ulang yang terpisah/duplikat** dari logika resmi aplikasi — temukan dan gunakan kembali service/helper/method yang sudah menghitung nilai final, supaya tab ini dijamin konsisten dengan nilai final resmi dan tidak pernah menampilkan angka yang berbeda dari sumber kebenaran.
4. **Sumber `nilai_final` dan `predikat_final`** — pastikan sama dengan sumber yang sudah dipakai `plan_rekomendasi_hasil_akreditasi_sidang.md` (`tb_pengajuans.nilai_final` / `predikat_final` atau perhitungan resmi aplikasi), agar dua dokumen (Rincian Penilaian dan Rekomendasi Hasil Akreditasi) tidak pernah menampilkan nilai final yang berbeda satu sama lain.
5. **Apakah tahap penilaian yang dipakai sebagai sumber adalah tahap Final**, atau gabungan/versi tertentu (Pra-Visit, Visitasi, Paska-Visit, Final memiliki tabel/relasi skor yang mungkin berbeda per tahap — lihat `DATABASE.md` §7 `tb_penilaians`). Rincian Penilaian pada BA Sidang seharusnya merepresentasikan **hasil akhir (Final)**, bukan tahap antara — audit dan konfirmasi asumsi ini.
6. **Relasi `id_jenis` ke unsur/subunsur/item** — apakah struktur unsur/subunsur/item sama untuk semua jenis pengajuan atau ada mapping berbeda per `id_jenis` (lihat `DATABASE.md` §4.1: `mt_jenis_pengajuans` — 1 = Pranata Komputer, 2 = Statistisi). Jika berbeda, generator harus mengambil struktur unsur sesuai `jenis_pengajuan` pengajuan yang bersangkutan, bukan struktur tetap.

---

## 5. Analisis Template DOCX

### 5.1 Header

```text
RINCIAN UNSUR PENILAIAN AKREDITASI
PROGRAM PELATIHAN TEKNIS DI BIDANG ${jenis_pengajuan}
${nama_lembaga}
```

| Placeholder | Sumber sistem | Aturan |
|---|---|---|
| `${jenis_pengajuan}` | `tb_pengajuans.id_jenis` / relasi `mt_jenis_pengajuans` | Read-only, ikuti mapping resmi aplikasi (konsisten dengan penamaan yang dipakai `plan_rekomendasi_hasil_akreditasi_sidang.md`) |
| `${nama_lembaga}` | Relasi `$pengajuan->profile->nama_lembaga` | Read-only |

### 5.2 Tabel Rincian (struktur hasil inspeksi template)

Tabel memiliki 3 kolom: **INDIKATOR (1)**, **NILAI SUB UNSUR (2)**, **NILAI UNSUR (3)**.

Struktur baris pada template contoh (hanya representasi satu skenario — **jumlah unsur/subunsur/item aktual harus diambil dinamis dari database**, bukan hardcode 6 unsur seperti berikut):

```text
UNSUR ${UNSUR_1} (${BOBOT_UNSUR_1}%)                              -> unsur tanpa breakdown subunsur
UNSUR ${UNSUR_2} (${BOBOT_UNSUR_2}%)                               -> unsur dengan subunsur
  Subunsur ${subunsur_2_1} (${bobot_subunsur_2_1}%)
  Subunsur ${subunsur_2_2} (${bobot_subunsur_2_2}%)
  Subunsur ${subunsur_2_3} (${bobot_subunsur_2_3}%)
  Subunsur ${subunsur_2_4} (${bobot_subunsur_2_4}%)
  Subunsur ${subunsur_2_5} (${bobot_subunsur_2_5}%)
UNSUR ${UNSUR_3} (${BOBOT_UNSUR_3}%)                               -> unsur tanpa breakdown subunsur
UNSUR ${UNSUR_4} (${BOBOT_UNSUR_4}%)                               -> unsur dengan subunsur, dan salah satu subunsur pecah lagi ke item
  Subunsur ${subunsur_4_1} (${bobot_subunsur_4_1}%)
  Subunsur ${subunsur_4_2} (${bobot_subunsur_4_2}%)
    ${item_4_2_1} (${bobot_item_4_2_1}%)
    ${item_4_2_2} (${bobot_item_4_2_2}%)
UNSUR ${UNSUR_5} (${BOBOT_UNSUR_5}%)                               -> unsur tanpa breakdown subunsur
UNSUR ${UNSUR_6} (${BOBOT_UNSUR_6}%)                               -> unsur dengan subunsur
  Subunsur ${subunsur_6_1} (${bobot_subunsur_6_1}%)
  Subunsur ${subunsur_6_2} (${bobot_subunsur_6_2}%)

NILAI AKREDITASI     -> ${nilai_final}
PREDIKAT AKREDITASI  -> (${predikat_final})
```

**Temuan penting dari inspeksi file:** kolom (2) "NILAI SUB UNSUR" dan (3) "NILAI UNSUR" pada template **kosong tanpa placeholder bernama** di setiap baris unsur/subunsur/item. Ini berarti:

- Template tidak didesain untuk diisi lewat simple find-and-replace placeholder seperti `${nilai_unsur_1}`.
- Generator **wajib mengisi kolom tersebut secara terprogram per baris**, berdasarkan urutan/posisi baris (atau dengan menambahkan penanda unik di kolom tersebut saat proses generate, misalnya menyisipkan placeholder dinamis per baris sebelum text-replacement, atau membangun tabel dari nol dengan PhpWord jika jumlah baris bervariasi antar pengajuan).
- **Karena jumlah unsur/subunsur/item bisa berbeda antar `jenis_pengajuan`**, disarankan generator **membangun tabel secara dinamis** (clone baris template per jenis baris — baris unsur, baris subunsur, baris item) menggunakan `cloneRow()` PhpWord atau pendekatan setara, bukan mengandalkan jumlah baris tetap seperti pada file contoh. Ini adalah keputusan teknis penting yang wajib dikonfirmasi sebelum coding (lihat §9).

### 5.3 Footer nilai

```text
NILAI AKREDITASI    = ${nilai_final}
PREDIKAT AKREDITASI = (${predikat_final})
```

| Placeholder | Sumber sistem | Aturan |
|---|---|---|
| `${nilai_final}` | `tb_pengajuans.nilai_final` atau sumber resmi | Read-only |
| `${predikat_final}` | `tb_pengajuans.predikat_final` atau perhitungan resmi | Read-only, tidak boleh dientri manual |

**Catatan teknis (konsisten dengan `plan_rekomendasi_hasil_akreditasi_sidang.md` §4):** placeholder yang terpecah antar XML run harus dideteksi dengan menggabungkan seluruh node `<w:t>` sebelum analisis/penggantian. Template tidak boleh diedit/ditimpa selama implementasi tanpa persetujuan.

---

## 6. Business Rules

1. Tab **wajib read-only** — tidak ada input manual apa pun di tab ini (data 100% berasal dari hasil penilaian yang sudah tersimpan).
2. Struktur Unsur → Subunsur → Item, beserta nama dan bobot masing-masing, diambil dari tabel master (`mt_unsurs`, `mt_subunsurs`, `mt_items`) sesuai `jenis_pengajuan` pengajuan yang sedang dibuka — bukan hardcode.
3. Nilai per subunsur dan per unsur **harus dihitung/diambil menggunakan formula/service yang sama** dengan yang menghasilkan `nilai_final` — dilarang membuat logika kalkulasi baru yang terpisah.
4. `nama_lembaga`, `jenis_pengajuan`, `nilai_final`, dan `predikat_final` tidak boleh diubah dari tab ini.
5. Jika unsur tidak memiliki subunsur (nilai langsung di level unsur), baris subunsur tidak ditampilkan untuk unsur tersebut — ikuti pola template (Unsur 1, 3, 5 pada contoh tidak memiliki breakdown subunsur).
6. Jika subunsur pecah lagi menjadi item (seperti Subunsur 4.2 pada contoh), baris item ditampilkan sebagai sub-baris di bawah subunsur terkait.
7. Export DOCX tidak boleh mengubah data atau status penilaian/submit apa pun.
8. Tidak ada placeholder `${...}` tersisa pada dokumen hasil export.
9. Jika ada nilai yang tidak tersedia (mis. unsur belum dinilai), tampilkan `-` dan catat warning di log aplikasi — jangan mengosongkan baris secara diam-diam atau mengambil input manual sebagai pengganti.

---

## 7. UI/UX Tab

### 7.1 Perubahan nama tab

```text
Sebelum: [Tanda Tangan] [Catatan Asesor] [Rekomendasi Hasil Akreditasi]
Sesudah: [Tanda Tangan] [Rincian Penilaian] [Rekomendasi Hasil Akreditasi]
```

Nama tab wajib persis:

```text
Rincian Penilaian
```

Gunakan mekanisme tab yang sama (`ettd-tabs`, `ettd-panel`) yang sudah dipakai di `ttd-sidang.blade.php` saat ini — tidak menambah library tab baru, konsisten dengan pola project (lihat `paps-hermes-ettd-ba-plan.md` §6: "Jangan menambahkan library tab baru").

### 7.2 Isi tab

Header:

```text
RINCIAN UNSUR PENILAIAN AKREDITASI
```

Ringkasan otomatis read-only:

```text
Program Pelatihan: [otomatis, dari jenis_pengajuan]
Lembaga Pelatihan: [otomatis, dari nama_lembaga]
```

Tabel rincian ditampilkan sebagai tabel HTML biasa (Blade), bukan perlu meniru layout DOCX 1:1 — mengikuti struktur hierarkis:

```text
INDIKATOR                          | NILAI SUB UNSUR | NILAI UNSUR
UNSUR I ... (xx%)                  |                  | xx
UNSUR II ... (xx%)                 |                  | xx
  Subunsur II.1 ... (xx%)          | xx               |
  Subunsur II.2 ... (xx%)          | xx               |
...
NILAI AKREDITASI                                       xx
PREDIKAT AKREDITASI                                    (xx)
```

Gunakan indentasi visual (mis. padding-left bertingkat) untuk membedakan level Unsur / Subunsur / Item, agar tetap mudah dibaca meskipun jumlah baris dinamis.

Tombol aksi:

```text
EXPORT TO DOCX
```

### 7.3 Aturan interaksi

1. Tab dapat dibuka oleh siapa pun yang memegang link token `/ttd-sidang/{token}` (read-only, sama seperti tab Tanda Tangan) — **tidak ada aksi mutasi** di tab ini sehingga tidak menimbulkan risiko keamanan token publik.
2. Tombol `EXPORT TO DOCX` dapat digunakan kapan pun (before/after submit BA Sidang), karena bersifat non-destruktif.
3. Jangan menggunakan emoji dekoratif pada label tombol (konsisten dengan pola project).
4. Jika data penilaian final belum lengkap/tersedia, tampilkan pesan yang jelas (mis. "Data penilaian final belum tersedia") alih-alih tabel kosong tanpa keterangan.

---

## 8. Controller dan Service

### 8.1 Pendekatan

Karena tab ini murni read-only tanpa mutasi, **tidak perlu controller/model baru untuk CRUD**. Yang dibutuhkan:

1. Query/service untuk mengambil struktur Unsur → Subunsur → Item beserta bobot, terfilter sesuai `jenis_pengajuan` pengajuan.
2. Query/service untuk mengambil nilai per unsur/subunsur (idealnya **reuse** logika yang sudah dipakai untuk menghitung `nilai_final` — lihat §4 poin 3, ini wajib diaudit dulu sebelum membangun apa pun).
3. Endpoint/data-provider tambahan pada controller yang sudah menampilkan `ttd-sidang.blade.php` (kemungkinan `TtdSidangController::show()`), menambahkan data rincian penilaian ke payload view — **audit dulu controller aktual** (lihat §4 poin 1) sebelum menentukan apakah perlu method baru atau cukup menambah data ke method `show()` yang ada.

### 8.2 Export DOCX

Endpoint baru, konsisten dengan pola export DOCX lain di project (`TtdSidangController::eksporBeritaAcaraSidang()` pada `plan_berita_acara_sidang.md`):

```text
GET /pengajuan/{id}/ekspor-rincian-penilaian
```

atau, jika mengikuti pola akses token publik (karena tombol export ada di tab yang bisa diakses lewat token):

```text
GET /ttd-sidang/{token}/ekspor-rincian-penilaian
```

**Keputusan ini wajib dikonfirmasi** (lihat §9) — apakah export diakses via token publik (konsisten dengan sifat tab yang read-only untuk siapa pun pemegang link) atau dibatasi ke sesi login asesor/sekretariat. Karena tab ini tidak mengekspos data sensitif baru (data yang sama sudah tampil di halaman), dan tidak melakukan mutasi apa pun, risiko keamanan export via token relatif rendah — namun tetap perlu diputuskan secara eksplisit, bukan diasumsikan.

Generator menggunakan `PhpOffice\PhpWord\TemplateProcessor` dengan:

1. Text placeholder sederhana: `nama_lembaga`, `jenis_pengajuan`, `nilai_final`, `predikat_final`.
2. Baris tabel dinamis: gunakan `cloneRow()` atau pendekatan block-cloning PhpWord untuk membangun baris Unsur/Subunsur/Item sesuai jumlah aktual dari database (lihat §5.2 dan §9).
3. Validasi: pastikan tidak ada placeholder `${...}` tersisa pada hasil akhir.
4. Nama file yang direkomendasikan: `Rincian Penilaian Akreditasi - {nama_lembaga}.docx`, disanitasi dari karakter ilegal/path traversal (konsisten dengan pola penamaan file di `plan_rekomendasi_hasil_akreditasi_sidang.md` §11.3).

---

## 9. Keputusan yang Perlu Dikonfirmasi Sebelum Implementasi

1. Apakah nilai per unsur/subunsur untuk BA Sidang diambil dari tahap **Final** saja, atau perlu opsi menampilkan tahap lain?
2. Apakah struktur Unsur/Subunsur/Item benar-benar identik untuk semua `jenis_pengajuan`, atau template/struktur berbeda per jenis (Pranata Komputer vs Statistisi)? Jika berbeda, apakah satu template DOCX ini digunakan untuk semua jenis dengan struktur baris dinamis, atau perlu template terpisah per jenis?
3. Karena kolom (2)/(3) pada template tidak memiliki placeholder eksplisit per baris, apakah tim menyetujui pendekatan **dynamic row cloning** (PhpWord `cloneRow`) sebagai metode generate, mengingat ini mengubah cara baca template dari sekadar text-replace menjadi table-templating yang lebih kompleks?
4. Apakah nilai unsur/subunsur sudah tersimpan sebagai kolom teragregasi di database (tinggal dibaca), atau harus dihitung ulang saat generate/tampil (berisiko inkonsistensi jika logika kalkulasi tidak benar-benar direuse dari service resmi)?
5. Apakah export DOCX pada tab ini boleh diakses lewat token publik `/ttd-sidang/{token}`, atau dibatasi ke sesi login asesor/sekretariat saja?
6. Apakah nama tab lama "Catatan Asesor" benar-benar dihapus total, atau apakah ada konsumen lain (fitur/laporan lain) yang bergantung pada keberadaan tab/data tersebut dan perlu dipertahankan di tempat lain?

---

## 10. Target File Implementasi

### File yang mungkin dimodifikasi

```text
resources/views/ttd-sidang.blade.php
app/Http/Controllers/TtdSidangController.php
routes/web.php
```

### File baru (kandidat, tergantung hasil audit §4 dan keputusan §9)

```text
app/Services/RincianPenilaianService.php   # reuse logika kalkulasi nilai unsur/subunsur/final
resources/views/pdf-atau-docx-partial/...  # jika perlu partial Blade untuk tabel rincian (opsional)
```

### Dokumentasi/template

```text
dokumen_md/plan_tab_rincian_penilaian_ttd_sidang.md
public/template_rincian_unsur_penilaian.docx   # jangan ubah tanpa persetujuan
```

---

## 11. Acceptance Criteria

### Tab dan navigasi

- [ ] Tab **Rincian Penilaian** menggantikan posisi tab **Catatan Asesor** di `ttd-sidang.blade.php`.
- [ ] Tab dapat diakses oleh siapa pun pemegang token (read-only), tidak memerlukan login.
- [ ] Tab Tanda Tangan dan Rekomendasi Hasil Akreditasi tetap berfungsi normal.

### Data

- [ ] `nama_lembaga` dan `jenis_pengajuan` tampil otomatis dan sesuai data pengajuan yang dibuka.
- [ ] Struktur Unsur, Subunsur, dan Item beserta bobot masing-masing diambil dinamis dari tabel master, sesuai `jenis_pengajuan`.
- [ ] Nilai kolom (2) dan (3) sama persis dengan hasil kalkulasi resmi aplikasi (diverifikasi dengan membandingkan ke `nilai_final` dan sumber penilaian final yang sudah ada — tidak ada logika kalkulasi baru yang menyimpang).
- [ ] `nilai_final` dan `predikat_final` tampil sesuai sumber resmi, identik dengan yang tampil di tab Rekomendasi Hasil Akreditasi dan halaman final lembaga.
- [ ] Unsur tanpa subunsur ditampilkan langsung tanpa baris subunsur kosong.
- [ ] Subunsur yang memiliki breakdown item menampilkan baris item di bawahnya.
- [ ] Data yang belum tersedia menampilkan `-` dengan keterangan jelas, bukan tabel kosong tanpa penjelasan.

### Export DOCX

- [ ] Tombol `EXPORT TO DOCX` menghasilkan file dengan format sesuai `template_rincian_unsur_penilaian.docx`.
- [ ] Tidak ada placeholder `${...}` tersisa pada hasil export.
- [ ] Jumlah baris unsur/subunsur/item pada hasil export sesuai jumlah aktual di database (bukan jumlah tetap seperti contoh template).
- [ ] Nama file aman (tersanitasi).
- [ ] Export tidak mengubah data atau status apa pun di database.

### Regresi

- [ ] Data/fungsi yang sebelumnya berada di tab Catatan Asesor (jika masih dibutuhkan di tempat lain) sudah dipastikan tidak hilang — sesuai hasil audit §4 poin 1 dan keputusan §9 poin 6.
- [ ] Workflow tanda tangan, submit, dan reset BA Sidang tetap berjalan normal.
- [ ] `php artisan view:cache` dan `php artisan route:list` berhasil tanpa error.

---

## 12. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Nilai unsur/subunsur dihitung ulang dengan logika berbeda dari `nilai_final` resmi | Dokumen menampilkan angka yang tidak konsisten dengan hasil akreditasi resmi | Wajib reuse service/kalkulasi resmi yang sudah ada (audit dulu, lihat §4 poin 3) |
| Struktur unsur/subunsur/item diasumsikan tetap 6 unsur seperti contoh template | Data salah/rusak untuk jenis pengajuan lain | Audit struktur master data per `jenis_pengajuan` sebelum coding (§4 poin 2 & 6) |
| Kolom (2)/(3) template tanpa placeholder eksplisit disalahartikan sebagai fixed layout | Generator gagal untuk pengajuan dengan jumlah unsur/subunsur berbeda dari contoh | Gunakan pendekatan dynamic row cloning, bukan text-replace statis (§5.2, §9 poin 3) |
| Data/fitur yang bergantung pada tab Catatan Asesor lama hilang tanpa penggantian | Regresi fitur lain yang tidak terlihat langsung di tab ini | Audit konsumen data Catatan Asesor sebelum penghapusan (§4 poin 1, §9 poin 6) |
| Export via token publik membuka celah pengambilan data tanpa autentikasi (meski read-only) | Low-medium: bukan mutasi, tapi tetap pengungkapan data pengajuan ke siapa pun yang punya link | Putuskan eksplisit apakah export dibatasi role login (§9 poin 5) |

---

## 13. Rencana Implementasi Bertahap

### Tahap 1 — Audit dan keputusan

- Audit lima poin di §4.
- Konfirmasi enam keputusan di §9.
- Tentukan apakah perlu service kalkulasi baru atau reuse murni dari yang sudah ada.

### Tahap 2 — Data layer

- Bangun query/service pengambilan struktur Unsur → Subunsur → Item + bobot, terfilter `jenis_pengajuan`.
- Bangun/rewire pengambilan nilai per unsur/subunsur dari sumber resmi (reuse, bukan hitung ulang).
- Uji hasil kalkulasi terhadap beberapa pengajuan riil, bandingkan dengan `nilai_final` yang sudah ada.

### Tahap 3 — UI tab

- Ganti label tab "Catatan Asesor" menjadi "Rincian Penilaian".
- Render tabel hierarkis read-only sesuai struktur dinamis.
- Tambahkan tombol Export to DOCX.

### Tahap 4 — Export DOCX

- Implementasikan generator dengan `PhpOffice\PhpWord\TemplateProcessor` + dynamic row cloning.
- Uji dengan pengajuan yang memiliki jumlah unsur/subunsur/item bervariasi (bukan hanya kasus 6 unsur seperti contoh).
- Validasi hasil DOCX di Word/LibreOffice, pastikan tidak ada placeholder tersisa.

### Tahap 5 — Regresi dan deployment

- Uji akses tab lewat token publik.
- Uji nilai yang tampil identik dengan halaman final/rekap lembaga.
- Uji export dengan data lengkap dan data yang belum lengkap.
- Review diff dan test sebelum commit/push/deploy.

---

## 14. Catatan Implementasi Aktual

- Struktur live saat audit: 6 unsur, 12 subunsur, dan 13 item.
- Master jenis pengajuan di database aktif: `1 = Sistem Teknologi Berbasis Komputer`, `2 = Statistik`.
- Nilai diambil dari tahap `final` melalui `PenilaianController::calculateFinalData()` menggunakan bobot item → subunsur → unsur yang sama dengan halaman Final.
- Tab publik tetap read-only dan label `Catatan Asesor` diganti menjadi `Rincian Penilaian`.
- Export menggunakan route `GET /ttd-sidang/{token}/rincian-penilaian/export-docx`.
- Generator memakai `TemplateProcessor`, kemudian membangun ulang row tabel secara dinamis sambil mempertahankan style row template.
- Placeholder yang terpecah antar XML run ditangani dengan penggantian text node per cell.
- Jika nilai final belum lengkap, tampilan menggunakan `-` dan warning dicatat ke log Laravel.
- Template `public/template_rincian_unsur_penilaian.docx` tidak diubah.

## 15. Catatan Revisi Lanjutan

- Cell angka kolom (2) dan (3) pada web diberi alignment center; indikator tetap rata kiri.
- Pada DOCX, paragraph kolom angka dipaksa center.
- Kolom (2) pada row unsur sekarang berisi penjumlahan nilai subunsur resmi di bawahnya.
- Row item hanya menampilkan kontribusi item setelah bobot item dan bobot subunsur, sehingga jumlahnya sinkron dengan nilai subunsur resmi.
- Shading row unsur pada DOCX hanya dipertahankan pada cell kolom (1) dan (2); cell kolom (3) dihapus shading-nya.
- Hasil pengajuan uji 5: nilai final `3.59`, tidak berubah; hasil pengajuan uji 12: nilai final `3.56`, tidak berubah.
- Kedua pengajuan menghasilkan 6 row unsur, 9 row subunsur, dan 2 row item breakdown.

## 16. Catatan Revisi Lanjutan #3

- Nama lembaga, program, nama unsur, dan predikat final dikapitalisasi pada layer output; nama master tidak diubah.
- Urutan header output DOCX disusun menjadi nama lembaga lalu program pelatihan tanpa mengubah template sumber.
- Web menggunakan rowspan dinamis pada kolom (3), satu cell per unsur; DOCX menggunakan `vMerge` restart/continue dengan jumlah row dinamis.
- Shading data DOCX hanya berada pada kolom (1) dan (2) row unsur; row subunsur/item tidak diberi shading.
- Alignment angka web dan DOCX tetap center.
- Pengajuan 5 dan 12 sama-sama menghasilkan rowspan `1, 6, 1, 5, 1, 3`; nilai final tetap `3.59` dan `3.56` sesuai database.
