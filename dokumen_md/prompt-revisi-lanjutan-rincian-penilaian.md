# Prompt Perbaikan Lanjutan: Tab "Rincian Penilaian" — Alignment, Kalkulasi Agregasi, dan Shading Export DOCX

Anda adalah Senior Laravel 8 Developer untuk project PAPS. Tab **Rincian Penilaian** pada `ttd-sidang.blade.php` sudah berjalan (termasuk perbaikan logika breakdown baris item/subunsur sebelumnya), tetapi masih ada 4 revisi yang perlu dikerjakan: 1 revisi tampilan (alignment), 2 revisi kalkulasi/agregasi nilai, dan 1 revisi shading pada export DOCX.

**Sebelum mengubah kode, audit dulu implementasi aktual** (kode render tabel di Blade, service/query kalkulasi nilai, dan generator DOCX) dan laporkan temuan singkat — jangan asumsikan lokasi logika tanpa verifikasi, karena file/nama class hasil implementasi sebelumnya mungkin berbeda dari nama yang disebut di plan awal.

---

## 1. Alignment: Nilai Unsur dan Nilai Subunsur → Rata Tengah (Center)

**Masalah:** Kolom "Nilai Sub Unsur" (kolom 2) dan "Nilai Unsur" (kolom 3) saat ini kemungkinan rata kiri/default, seharusnya rata tengah (center alignment), sesuai format resmi pada contoh dokumen.

**Perbaikan:**

- Terapkan `text-align: center` (atau kelas Bootstrap `text-center`) pada seluruh sel angka di kolom (2) dan (3), untuk semua level baris (Unsur, Subunsur, Item) — baik yang menampilkan nilai maupun yang kosong/`-`.
- Kolom (1) "Indikator" tetap rata kiri seperti sekarang (tidak berubah).
- Terapkan alignment yang sama secara konsisten di:
  - Tampilan tabel web (`ttd-sidang.blade.php`).
  - Tabel hasil export DOCX (`PhpOffice\PhpWord\TemplateProcessor` / dynamic row cloning) — gunakan `PhpOffice\PhpWord\SimpleType\JcTable` atau pengaturan alignment paragraph (`Jc::CENTER`) pada cell yang relevan, agar hasil download konsisten dengan tampilan web.

---

## 2. Kalkulasi: Nilai Unsur (Kolom 2) Harus Merupakan Penjumlahan Nilai Subunsur di Bawahnya

**Masalah:** Saat ini kolom (2) "Nilai Sub Unsur" pada **baris Unsur** kemungkinan menampilkan `-` (kosong), padahal seharusnya berisi angka: hasil penjumlahan nilai kolom (2) dari seluruh subunsur di bawah unsur tersebut.

**Contoh yang benar (ilustrasi dari Anda):**

Jika sebuah unsur memiliki 5 subunsur dengan nilai kolom (2) masing-masing `1,2`, `0,8`, `0,6`, `0,6`, `0,2`, maka:

```
Nilai Unsur (kolom 2) = 1,2 + 0,8 + 0,6 + 0,6 + 0,2 = 3,4
```

Nilai `3,4` inilah yang harus tampil di baris Unsur pada kolom (2) — **bukan `-`**.

**Perbaikan:**

- Pastikan **tidak ada lagi nilai `-` pada kolom (2) di baris Unsur manapun** yang memiliki subunsur. Nilai kolom (2) untuk baris Unsur = SUM dari nilai kolom (2) seluruh subunsur langsung di bawahnya.
- Untuk Unsur yang **tidak memiliki subunsur** (nilai langsung di level unsur, seperti "Unsur Kelembagaan" pada contoh sebelumnya), kolom (2) tetap menampilkan nilai unsur itu sendiri seperti sekarang (tidak berubah — kasus ini sudah benar).
- Kolom (3) "Nilai Unsur" tetap dihitung seperti pola yang sudah ada saat ini (kolom 2 unsur dikali bobot unsur), **tidak diubah oleh revisi ini** — hanya kolom (2) baris unsur yang diperbaiki.
- Pastikan perhitungan ini **konsisten dengan/di-reuse dari** service kalkulasi resmi yang sudah dipakai untuk `nilai_final` (jangan membuat logika penjumlahan baru yang berpotensi menyimpang dari sumber kebenaran nilai akreditasi).

---

## 3. Kalkulasi: Nilai Subunsur dengan >1 Item Harus Merupakan Penjumlahan Nilai Item (Setelah Dikalikan Bobot Item)

**Masalah:** Nilai yang ditampilkan pada baris **Item Penilaian** saat ini kemungkinan belum konsisten dengan nilai agregat subunsur di atasnya — nilai item yang ditampilkan harus **sudah dikalikan bobotnya masing-masing**, sehingga jika dijumlahkan, hasilnya sinkron dengan nilai subunsur.

**Contoh yang benar (ilustrasi dari Anda, kasus Subunsur 4.2 dengan 2 item):**

```
Item 4.2.1 (bobot 50%) → nilai tampil = 1   (sudah dikalikan bobot 50%)
Item 4.2.2 (bobot 50%) → nilai tampil = 1   (sudah dikalikan bobot 50%)

Nilai Subunsur 4.2 (kolom 2) = 1 + 1 = 2
```

Jadi nilai yang tampil di baris item **bukan skor mentah asesor**, melainkan **skor asesor × bobot item** — agar ketika dijumlahkan ke atas (ke level subunsur), hasilnya otomatis sinkron tanpa perlu formula terpisah/berbeda antara baris item dan baris subunsur.

**Perbaikan:**

- Audit dulu: apakah nilai item yang ditampilkan saat ini adalah skor mentah (belum dikali bobot) atau sudah dikali bobot. Jika masih skor mentah, ubah agar menampilkan nilai **setelah dikalikan bobot item** (`skor_item × bobot_item / 100` atau formula bobot yang sama dipakai di seluruh sistem — cek konsistensi dengan cara bobot dihitung di level unsur/subunsur yang sudah benar).
- Nilai Subunsur (kolom 2) untuk subunsur yang memiliki **lebih dari 1 item** = SUM dari nilai item (yang sudah dikali bobot) di bawahnya — bukan diambil dari sumber terpisah yang berpotensi tidak sinkron.
- Untuk subunsur dengan **1 item saja** (kasus "dilebur", sudah benar dari perbaikan sebelumnya): nilai subunsur = nilai item tunggal tersebut (setelah dikali bobot), tidak berubah dari perilaku saat ini.
- Terapkan prinsip agregasi bottom-up yang sama dan konsisten di seluruh level: **Item → Subunsur → Unsur → Final**, semuanya menggunakan pendekatan "nilai level ini = SUM nilai level di bawahnya yang sudah dikali bobot masing-masing", bukan campuran antara nilai mentah di satu level dan nilai terkalkulasi di level lain.
- **Wajib**: setelah perbaikan ini, jumlahkan seluruh hasil dari bawah ke atas dan pastikan angka **Nilai Akreditasi (final)** tetap identik dengan nilai final yang sudah tersimpan resmi di sistem (`nilai_final`) — revisi ini adalah perbaikan cara tampilan/agregasi ditampilkan secara konsisten, bukan mengubah hasil akhir penilaian.

---

## 4. Export DOCX: Shading Hanya pada Kolom 1–2 di Baris Unsur (Kolom 3 Tidak Ikut Diwarnai)

**Masalah:** Pada export DOCX saat ini, kemungkinan shading (warna latar biru/abu pada baris Unsur, seperti terlihat di contoh tabel) diterapkan ke seluruh baris termasuk kolom (3) "Nilai Unsur". Seharusnya shading **hanya diterapkan pada kolom (1) dan (2)** di baris Unsur — kolom (3) tidak diberi shading.

Catatan konteks: kolom (3) "Nilai Unsur" biasanya di-*merge* secara vertikal mencakup seluruh baris subunsur di bawah unsur tersebut (satu nilai unsur berlaku untuk beberapa baris subunsur sekaligus) — shading pada kolom ini akan janggal secara visual jika ikut diwarnai gelap hanya di baris unsur karena akan terlihat terputus dari sel merge di bawahnya yang tidak diwarnai. Oleh sebab itu kolom (3) dikecualikan dari shading.

**Perbaikan:**

- Pada logika generator DOCX (`PhpOffice\PhpWord\TemplateProcessor` / manipulasi tabel dinamis), terapkan shading (`w:shd` / `TableCellStyle` dengan `bgColor`) hanya pada **cell kolom (1) dan kolom (2)** untuk setiap baris yang levelnya Unsur.
- Kolom (3) pada baris Unsur dibiarkan tanpa shading (transparent/putih), konsisten dengan sifatnya yang merged mencakup baris subunsur di bawahnya.
- Baris Subunsur dan Item tetap tanpa shading (tidak berubah dari perilaku saat ini — hanya baris Unsur yang relevan dengan revisi ini).
- Setelah perubahan, render ulang beberapa contoh pengajuan dan bandingkan visual hasil DOCX dengan contoh tabel referensi untuk memastikan shading terlihat rapi dan tidak "terputus" di batas kolom (2)/(3).

---

## Test yang Wajib Dilakukan Setelah Perbaikan

1. **Alignment**: kolom (2) dan (3) tampil rata tengah di web maupun hasil download DOCX, untuk semua level baris.
2. **Agregasi Unsur**: tidak ada lagi `-` pada kolom (2) di baris Unsur yang memiliki subunsur; nilai yang tampil = penjumlahan nilai kolom (2) subunsur di bawahnya (verifikasi manual dengan kalkulator untuk minimal 2 unsur berbeda).
3. **Agregasi Item → Subunsur**: untuk subunsur dengan >1 item, jumlahkan nilai seluruh item (yang sudah dikali bobot) dan pastikan hasilnya identik dengan nilai yang tampil di baris subunsur.
4. **Sinkronisasi total**: Nilai Akreditasi (final) hasil tampilan tab ini setelah semua perbaikan tetap sama dengan `nilai_final` yang tersimpan di database — tidak boleh berubah akibat revisi tampilan/agregasi ini.
5. **Shading DOCX**: export ulang untuk pengajuan yang sama, cek visual bahwa hanya kolom 1–2 di baris Unsur yang memiliki warna latar, kolom 3 tetap putih/transparan.
6. Uji dengan minimal 2 pengajuan berbeda (jumlah unsur/subunsur/item bervariasi) untuk memastikan perbaikan bersifat generik, bukan hanya cocok untuk satu contoh data.

---

## Batasan

- Jangan mengubah formula bobot resmi atau algoritma penilaian yang menghasilkan `nilai_final` — revisi ini hanya menyelaraskan **cara nilai per level ditampilkan/diagregasi** di tab Rincian Penilaian agar konsisten secara matematis dari bawah ke atas.
- Jangan mengubah tab Tanda Tangan atau tab Rekomendasi Hasil Akreditasi.
- Jangan mengubah struktur data master (`mt_unsurs`, `mt_subunsurs`, `mt_items`).
- Perbaikan logika breakdown baris (subunsur/item dengan 1 anak vs >1 anak) dari revisi sebelumnya **tidak diubah** oleh prompt ini — pastikan perbaikan agregasi nilai di sini tetap kompatibel dengan logika breakdown tersebut.

## Output yang Diharapkan

1. Temuan audit singkat (lokasi kode alignment, kalkulasi agregasi, dan shading DOCX saat ini).
2. Daftar file yang diubah.
3. Penjelasan formula/agregasi final yang dipakai per level (Item → Subunsur → Unsur), dan konfirmasi bahwa ini reuse dari service resmi, bukan logika baru yang terpisah.
4. Screenshot/hasil test tabel web dan hasil export DOCX untuk minimal 2 pengajuan berbeda.
5. Konfirmasi bahwa Nilai Akreditasi (final) tidak berubah akibat revisi ini.
