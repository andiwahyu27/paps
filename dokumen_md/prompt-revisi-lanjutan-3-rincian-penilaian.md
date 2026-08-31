# Prompt Perbaikan Lanjutan #3: Tab "Rincian Penilaian" — Kapitalisasi, Shading, Rowspan, Urutan Header, dan Spacing

Anda adalah Senior Laravel 8 Developer untuk project PAPS. Tab **Rincian Penilaian** pada `ttd-sidang.blade.php` sudah melewati dua putaran revisi sebelumnya (logika breakdown baris, alignment, agregasi nilai, shading DOCX). Sekarang ada 5 revisi tambahan yang mencakup tampilan web dan export DOCX.

**Sebelum mengubah kode, audit dulu implementasi aktual** (Blade view, service kalkulasi, dan generator DOCX) dan laporkan lokasi kode yang relevan untuk masing-masing poin sebelum melakukan perubahan — jangan asumsikan lokasi tanpa verifikasi, karena revisi-revisi sebelumnya mungkin sudah mengubah struktur file dari rencana awal.

---

## 1. Kapitalisasi (UPPERCASE) untuk Nama Program, Nama Lembaga, Nama Unsur, dan Predikat Final

**Perbaikan:** Pastikan teks berikut selalu ditampilkan dalam **huruf kapital semua**, baik di tampilan web (`ttd-sidang.blade.php`) maupun di hasil export DOCX:

- Nama program pelatihan (`jenis_pengajuan`)
- Nama lembaga (`nama_lembaga`)
- Nama unsur (label pada baris Unsur, kolom Indikator — mis. "UNSUR TENAGA PELATIHAN")
- Predikat final (`predikat_final`)

**Catatan implementasi:**

- Terapkan transformasi uppercase **di layer tampilan/output** (`strtoupper()` di PHP saat menyiapkan data untuk Blade dan untuk `TemplateProcessor`, atau `text-transform: uppercase` di CSS untuk web saja) — **jangan mengubah data master** (`mt_unsurs.nama`, dsb.) di database menjadi uppercase secara permanen, supaya data asli tetap fleksibel untuk kebutuhan lain (mis. laporan lain yang mungkin butuh format Title Case).
- Untuk web: jika memakai CSS `text-transform: uppercase`, pastikan tetap konsisten — tapi untuk export DOCX, CSS tidak berlaku, jadi teks yang dikirim ke `TemplateProcessor` **harus benar-benar diubah jadi uppercase di PHP** (`strtoupper()` / `Str::upper()`), bukan mengandalkan styling.
- Nama unsur pada baris Subunsur dan Item **tidak diminta ikut uppercase** (hanya nama Unsur yang eksplisit diminta) — konfirmasi ulang ke Blade existing apakah subunsur/item sudah punya casing tersendiri (biasanya Title Case, seperti "Subunsur Fasilitator") dan pastikan tidak ikut berubah oleh revisi ini kecuali memang diminta.

---

## 2. Shading DOCX Hanya untuk Baris UNSUR (Tegaskan Ulang — Audit Ulang Implementasi Sebelumnya)

**Konteks:** Pada revisi sebelumnya sudah diminta shading hanya di kolom (1)–(2) pada baris Unsur (kolom (3) dikecualikan). Revisi ini menegaskan ulang bahwa **shading secara keseluruhan hanya boleh berlaku untuk baris Unsur** — pastikan tidak ada baris Subunsur atau Item yang ikut ter-shading, baik karena bug pada implementasi sebelumnya maupun efek samping dari perubahan rowspan di poin 3 (lihat di bawah — rowspan dan shading harus tidak saling mengganggu).

**Perbaikan:**

- Audit ulang kode shading DOCX saat ini: pastikan kondisi pengecekan "baris ini levelnya Unsur" benar-benar akurat dan tidak keliru menandai baris Subunsur/Item sebagai Unsur (terutama jika ada unsur yang tidak punya subunsur — baris tunggal seperti itu harus tetap dikenali sebagai baris Unsur dan tetap di-shading).
- Setelah perubahan rowspan pada poin 3, pastikan shading tetap hanya menempel pada baris/cell Unsur yang benar, tidak "ikut" ke baris Subunsur di bawahnya akibat merge.

---

## 3. Rowspan pada Kolom (3) "Nilai Unsur" — Satu Unsur = Satu Cell (Berlaku di Web dan DOCX)

**Masalah:** Saat ini kemungkinan kolom (3) "Nilai Unsur" menampilkan nilai yang sama berulang di setiap baris Subunsur/Item di bawah unsur yang sama (redundan), padahal seharusnya nilai tersebut cukup ditampilkan **satu kali saja** dalam satu cell yang digabung (rowspan) mencakup seluruh baris subunsur/item milik unsur tersebut.

**Perbaikan:**

- **Web (Blade):** Untuk setiap Unsur, hitung berapa banyak baris (subunsur + item breakdown, jika ada) yang termasuk dalam unsur tersebut, lalu render cell kolom (3) hanya sekali di baris pertama unsur tersebut dengan atribut `rowspan="<jumlah_baris_unsur_ini>"`, dan **jangan render cell kolom (3) sama sekali** pada baris-baris Subunsur/Item lain yang termasuk unsur yang sama (karena sudah di-cover oleh rowspan dari baris di atasnya).
- Untuk Unsur yang tidak memiliki subunsur (baris tunggal), `rowspan="1"` (setara tanpa rowspan, perilaku normal seperti sekarang).
- **Export DOCX:** Gunakan kemampuan merge cell vertikal PhpWord (`vMerge` — cell pertama diberi `vMerge => 'restart'`, cell-cell di bawahnya pada kolom yang sama diberi `vMerge => 'continue'` dengan cell dikosongkan) untuk mencapai efek visual yang sama seperti rowspan HTML. Pastikan jumlah baris yang di-merge sesuai jumlah baris riil unsur tersebut (dinamis, bukan hardcode).
- **Penting:** perubahan rowspan ini murni soal presentasi (menghindari pengulangan nilai yang sama), **tidak mengubah nilai** yang ditampilkan maupun kalkulasi agregasi yang sudah diperbaiki pada revisi sebelumnya.
- Setelah perubahan, pastikan **alignment center** (dari revisi sebelumnya) tetap berlaku pada cell yang di-rowspan, dan **shading** (poin 2 di atas) tidak ikut menempel ke cell rowspan kolom (3) — karena rowspan ini di kolom (3), sementara shading yang diminta hanya untuk kolom (1)–(2), jadi seharusnya tidak bertabrakan, tapi tetap cek visual untuk memastikan.

---

## 4. Urutan Header: Nama Lembaga Sebelum Nama Program Pelatihan

**Masalah:** Urutan `<p>` pada header tab saat ini kemungkinan:

```html
<p>PROGRAM PELATIHAN TEKNIS DI BIDANG ${jenis_pengajuan}</p>
<p>${nama_lembaga}</p>
```

**Perbaikan:** Balik urutannya menjadi nama lembaga terlebih dahulu, baru nama program pelatihan:

```html
<p>${nama_lembaga}</p>
<p>PROGRAM PELATIHAN TEKNIS DI BIDANG ${jenis_pengajuan}</p>
```

Terapkan perubahan urutan ini di **kedua tempat**: tampilan web (`ttd-sidang.blade.php`) dan urutan paragraf pada template/hasil export DOCX (audit apakah urutan ini diatur lewat template asli `template_rincian_unsur_penilaian.docx` yang perlu disesuaikan urutannya, atau diatur lewat kode PHP saat proses generate — sesuaikan di tempat yang benar sesuai temuan audit).

---

## 5. Penyesuaian Spacing/Margin pada Web

**Perbaikan (khusus tampilan web, tidak berlaku untuk DOCX kecuali disebutkan):**

- Pada elemen `<h3>` dengan teks `RINCIAN UNSUR PENILAIAN AKREDITASI`, ubah style `margin-bottom` menjadi `5px`:
  ```html
  <h3 style="margin-bottom: 5px;">RINCIAN UNSUR PENILAIAN AKREDITASI</h3>
  ```
  (jika sudah ada style lain di elemen ini, tambahkan/timpa hanya properti `margin-bottom`, jangan menghapus style lain yang sudah ada tanpa alasan.)

- Pada `<p>` nama program pelatihan (hasil dari poin 4 di atas — yaitu `<p>` kedua setelah nama lembaga), tambahkan `style="margin-bottom:16px"`:
  ```html
  <p style="margin-bottom:16px">PROGRAM PELATIHAN TEKNIS DI BIDANG ${jenis_pengajuan}</p>
  ```

---

## Test yang Wajib Dilakukan Setelah Perbaikan

1. **Kapitalisasi:** cek tampilan web dan hasil download DOCX — nama program, nama lembaga, nama unsur (baris Unsur saja), dan predikat final semuanya tampil UPPERCASE. Pastikan nama Subunsur/Item tidak ikut berubah casing-nya kalau memang tidak diminta.
2. **Shading:** cek hasil export DOCX — hanya baris Unsur (kolom 1–2) yang memiliki warna latar; tidak ada baris Subunsur/Item yang ter-shading, termasuk untuk unsur yang tidak punya subunsur (baris tunggal) maupun unsur dengan banyak subunsur/item.
3. **Rowspan:**
   - Web: kolom (3) tampil sebagai satu cell tergabung per unsur, nilainya tampil sekali saja secara visual vertikal center di tengah baris-baris unsur tersebut.
   - DOCX: cek visual bahwa merge vertikal berhasil, tidak ada sel kosong "pecah" atau nilai yang terduplikasi.
   - Uji pada unsur yang tidak punya subunsur (rowspan=1) dan unsur dengan banyak subunsur/item (rowspan>1) untuk memastikan logika dinamis, bukan hardcode.
4. **Urutan header:** nama lembaga muncul sebelum nama program pelatihan, baik di web maupun DOCX.
5. **Spacing:** cek visual `margin-bottom` pada `<h3>` dan `<p>` sesuai nilai yang diminta, tidak merusak layout lain di sekitarnya.
6. Uji dengan minimal 2 pengajuan berbeda untuk memastikan seluruh revisi ini bersifat generik (terutama rowspan, karena jumlah baris per unsur bervariasi antar pengajuan).
7. Pastikan seluruh revisi sebelumnya (alignment kolom 2/3, agregasi nilai Item→Subunsur→Unsur, breakdown baris item) **masih berfungsi dengan benar** setelah perubahan ini — lakukan regression check singkat, terutama karena rowspan berpotensi bentrok dengan logika breakdown baris yang sudah ada.

---

## Batasan

- Jangan mengubah formula/algoritma perhitungan nilai unsur/subunsur/item/final.
- Jangan mengubah data master (`mt_unsurs`, dll.) menjadi uppercase secara permanen di database — kapitalisasi hanya di layer tampilan/output.
- Jangan mengubah tab Tanda Tangan atau tab Rekomendasi Hasil Akreditasi.
- Jangan mengubah template DOCX sumber (`template_rincian_unsur_penilaian.docx`) tanpa persetujuan eksplisit jika perubahan urutan header (poin 4) ternyata membutuhkan edit pada template asli, bukan cukup lewat kode PHP — jika demikian, laporkan dulu sebagai open question sebelum mengedit file template.

## Output yang Diharapkan

1. Temuan audit singkat (lokasi kode untuk masing-masing dari 5 poin di atas).
2. Daftar file yang diubah.
3. Konfirmasi bahwa rowspan/vMerge tidak mengganggu logika breakdown baris dan agregasi nilai dari revisi-revisi sebelumnya.
4. Screenshot/hasil test tabel web dan hasil export DOCX untuk minimal 2 pengajuan berbeda, menunjukkan kelima poin revisi sudah diterapkan.
5. Konfirmasi regression: fitur-fitur hasil revisi sebelumnya (alignment, agregasi, breakdown baris, shading kolom 1-2) tetap berjalan benar.
