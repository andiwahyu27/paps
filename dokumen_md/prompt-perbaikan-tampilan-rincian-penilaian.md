# Prompt Perbaikan: Logika Tampilan Baris Item Penilaian pada Tab "Rincian Penilaian"

Anda adalah Senior Laravel 8 Developer untuk project PAPS. Tab **Rincian Penilaian** pada `ttd-sidang.blade.php` (hasil implementasi dari `plan_tab_rincian_penilaian_ttd_sidang.md`) sudah berjalan, tetapi ada bug logika tampilan pada level **Subunsur → Item** yang perlu diperbaiki. Ini adalah perbaikan tampilan (dan/atau data yang dikirim ke view), bukan perubahan formula perhitungan nilai.

---

## Masalah Saat Ini

Saat ini kemungkinan besar seluruh item penilaian di bawah subunsur selalu ditampilkan sebagai baris terpisah, tidak peduli berapa jumlah itemnya. Ini tidak sesuai dengan format resmi pada contoh dokumen (`template_rincian_unsur_penilaian.docx` / contoh tabel terlampir).

## Aturan yang Benar (berdasarkan contoh tabel terlampir)

**Baris item penilaian hanya ditampilkan sebagai baris breakdown terpisah JIKA subunsur tersebut memiliki LEBIH DARI 1 item penilaian.**

Jika subunsur hanya memiliki **1 item penilaian**, baris item tersebut **tidak ditampilkan terpisah** — nilainya "dilebur" langsung menjadi nilai subunsur itu sendiri (karena nilai 1 item = nilai subunsur, tidak ada gunanya breakdown untuk 1 baris yang sama persis).

### Contoh dari tabel terlampir (rujukan pasti, hasil visual yang benar)

**Kasus A — Subunsur dengan 1 item → item tidak ditampilkan, dilebur ke subunsur:**
```
Subunsur Program Pelatihan (50%)                                    | 1
```
Tidak ada baris "Komponen ..." di bawahnya karena subunsur ini hanya memiliki 1 item penilaian. Nilai `1` yang tampil di kolom Nilai Sub Unsur **adalah langsung nilai item satu-satunya itu**.

**Kasus B — Subunsur dengan lebih dari 1 item → item ditampilkan sebagai breakdown:**
```
Subunsur Perencanaan Penyelenggaraan Pelatihan (50%)                | 1,5   (nilai agregat subunsur)
    Komponen Perencanaan dan Realisasi Penyelenggaraan Pelatihan (50%)  | 0,5
    Komponen Evaluasi Penyelenggaraan Pelatihan (50%)                  | 1
```
Karena subunsur ini punya 2 item penilaian, kedua item ditampilkan sebagai baris breakdown terindentasi di bawah subunsur, masing-masing dengan nilainya sendiri, sementara baris subunsur menampilkan **nilai agregat** (bold) hasil kalkulasi dari kedua item tersebut (bukan nilai salah satu item).

### Pola umum (berlaku di semua level, bukan cuma unsur/subunsur)

Prinsip yang sama berlaku secara konsisten di seluruh hierarki tabel, bukan hanya di level Subunsur → Item:

- **Unsur tanpa subunsur** (mis. "Unsur Kelembagaan (5%)" pada contoh): nilai unsur langsung ditampilkan di baris unsur itu sendiri, tanpa baris breakdown di bawahnya — karena unsur tersebut memang tidak dipecah jadi subunsur (setara dengan "punya 1 komponen saja").
- **Unsur dengan subunsur** (mis. "Unsur Tenaga Pelatihan (45%)"): baris unsur menampilkan nilai agregat (bold, di kolom Nilai Unsur), dan seluruh subunsur-nya ditampilkan sebagai breakdown di bawahnya.
- **Subunsur dengan >1 item**: tampilkan breakdown item seperti Kasus B.
- **Subunsur dengan =1 item (atau tidak dipecah ke item sama sekali)**: jangan tampilkan baris item, nilai subunsur = nilai item satu-satunya, seperti Kasus A.

Jadi aturan intinya: **breakdown ke level di bawahnya hanya ditampilkan jika level tersebut punya LEBIH DARI 1 anak. Jika hanya 1 anak (atau tidak dipecah sama sekali), nilai anak tunggal itu langsung ditampilkan di baris induknya, tanpa baris terpisah.**

---

## Yang Perlu Diperbaiki

### 1. Audit dulu kode aktual

Sebelum mengubah apa pun, baca ulang implementasi yang sudah jalan saat ini:

```text
resources/views/ttd-sidang.blade.php          (bagian render tabel Rincian Penilaian)
app/Http/Controllers/TtdSidangController.php  (atau controller/service yang menyiapkan data rincian penilaian)
```

Serta service/query yang dibuat pada implementasi sebelumnya untuk mengambil struktur Unsur → Subunsur → Item dan nilai masing-masing (nama file persis tergantung hasil implementasi sebelumnya — telusuri, jangan asumsikan nama class).

Laporkan singkat: di mana logika "apakah item ditampilkan sebagai baris terpisah" saat ini diputuskan (jika memang belum ada logika ini sama sekali dan semua item selalu ditampilkan flat, sebutkan itu sebagai temuan).

### 2. Perbaiki logika penentuan baris yang ditampilkan

Terapkan aturan berikut pada level Subunsur → Item (dan pastikan level Unsur → Subunsur yang sudah ada saat ini sudah konsisten dengan prinsip yang sama — audit juga, jangan hanya percaya asumsi bahwa level itu sudah benar):

```text
untuk setiap subunsur:
    hitung jumlah item penilaian yang dimiliki subunsur tersebut

    jika jumlah item > 1:
        tampilkan baris subunsur dengan nilai agregat (hasil kalkulasi resmi, BUKAN dihitung ulang manual di Blade/view)
        tampilkan setiap item sebagai baris breakdown terindentasi di bawah subunsur, dengan nilai masing-masing item

    jika jumlah item == 1 (atau subunsur tidak dipecah ke item):
        tampilkan HANYA baris subunsur
        nilai yang ditampilkan pada baris subunsur = nilai item tunggal tersebut
        JANGAN render baris item terpisah untuk kasus ini
```

**Penting soal sumber nilai (konsisten dengan plan sebelumnya):** nilai agregat pada baris subunsur/unsur harus tetap diambil/dihitung dari service/logika resmi yang sama dipakai untuk `nilai_final` (bukan dijumlah ulang di Blade). Perbaikan ini murni soal **kapan baris breakdown ditampilkan**, bukan soal cara menghitung nilainya.

### 3. Terapkan prinsip yang sama secara konsisten di semua level

Cek ulang level **Unsur → Subunsur** juga: jika ada unsur yang hanya memiliki 1 subunsur, terapkan prinsip yang sama — nilai subunsur tunggal itu langsung ditampilkan di baris unsur, tanpa baris subunsur terpisah (meskipun pada data project saat ini kemungkinan tidak ada kasus unsur dengan tepat 1 subunsur, tetap buat logikanya generik agar tidak rapuh jika struktur master data berubah di kemudian hari).

### 4. Indentasi visual

Pastikan level indentasi tetap konsisten dan jelas dibedakan secara visual (Unsur → Subunsur → Item), termasuk saat sebagian baris "dilebur" dan sebagian lain menampilkan breakdown — jangan sampai tampilan jadi tidak rata/rapi karena campuran subunsur yang punya breakdown dan yang tidak.

### 5. Update export DOCX

Jika generator DOCX (`PhpOffice\PhpWord\TemplateProcessor` dengan dynamic row cloning, hasil implementasi sebelumnya) membangun baris tabel dengan pola yang sama seperti tampilan web, terapkan aturan yang sama di sana — pastikan hasil export DOCX **konsisten** dengan tampilan web (baris item yang di-skip di web juga harus di-skip di DOCX, bukan cuma di salah satu tempat).

---

## Test yang Wajib Dilakukan Setelah Perbaikan

Gunakan data pada tabel contoh terlampir sebagai acuan hasil yang benar. Verifikasi minimal untuk pengajuan uji:

1. Unsur Kelembagaan (tanpa subunsur) → tampil 1 baris saja, nilai unsur = nilai unsur itu sendiri.
2. Unsur Tenaga Pelatihan (5 subunsur) → seluruh 5 subunsur tampil sebagai breakdown, tidak ada satu pun subunsur yang "hilang" tersembunyi karena logika baru.
3. Subunsur Program Pelatihan (1 item) → **tidak ada baris item terpisah**, nilai subunsur langsung `1`.
4. Subunsur Perencanaan Penyelenggaraan Pelatihan (2 item) → **kedua item tampil sebagai baris breakdown**, nilai subunsur adalah agregat `1,5` (bukan nilai salah satu item).
5. Bandingkan total nilai akreditasi (`3,59` pada contoh) tetap sama sebelum dan sesudah perbaikan tampilan ini — pastikan perbaikan ini **tidak menyentuh angka**, hanya menyentuh baris mana yang dirender.
6. Export DOCX menghasilkan struktur baris yang sama persis dengan tampilan web untuk pengajuan yang sama.
7. Uji dengan pengajuan lain (jika ada) yang kemungkinan memiliki jumlah item berbeda per subunsur, untuk memastikan logika generik, bukan hardcode sesuai satu contoh data ini saja.

---

## Batasan

- Jangan mengubah formula perhitungan nilai unsur/subunsur/item atau nilai final.
- Jangan mengubah struktur data master (`mt_unsurs`, `mt_subunsurs`, `mt_items`).
- Jangan mengubah tab Tanda Tangan atau tab Rekomendasi Hasil Akreditasi.
- Ini murni perbaikan **logika tampilan** (kapan baris breakdown dirender), baik di web maupun di export DOCX.

## Output yang Diharapkan

1. Temuan audit singkat (lokasi logika render saat ini).
2. Daftar file yang diubah.
3. Penjelasan implementasi logika baru (level mana yang disentuh: Unsur→Subunsur, Subunsur→Item, atau keduanya).
4. Hasil test manual/otomatis dibandingkan dengan tabel contoh terlampir.
5. Konfirmasi bahwa total nilai akreditasi tidak berubah akibat perbaikan ini.
