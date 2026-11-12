# Rencana Perbaikan Monitoring Akreditasi PSTK

## Tujuan

Rapikan ekspor dan tampilan sekretariat, terapkan kewajiban pelaporan mulai tahun setelah akreditasi, serta tampilkan pelapor dan versi kuesioner yang mudah dibaca. Perubahan historis tetap dapat dilihat.

## Task 1 — Ekspor CSV per laporan

- [x] Nama berkas detail mengikuti `monitoring-lapor-nama-lemdik-tahun.csv`, dengan nama lembaga disanitasi untuk nama berkas.
- [x] Kolom CSV memuat Nama Lembaga, Program, Tahun, Status Submit, No Pertanyaan, Pertanyaan, dan Jawaban. Nilai pertanyaan 10, 11, 12a, 12b diekspor sebagai bilangan bulat.
- [x] Pastikan ekspor terfilter tetap menggunakan format kolom yang sama dan nama berkas yang menandakan hasil filter.
- [x] Verifikasi dengan PHPUnit dan commit terpisah.

**Hasil.** CSV detail memakai nama lembaga dan tahun. Kolom pertanyaan dan jawaban numerik sudah sesuai. PHPUnit 28 tes, 246 asersi.

## Task 2 — Tampilan tabel sekretariat

- [x] Perbaiki keluaran `@include` yang muncul mentah pada status laporan.
- [x] Samakan tampilan baris laporan historis tahun 2025 dengan badge status, badge status link, dan tombol Detail.
- [x] Pastikan nama, program, tahun, dan tautan detail tetap benar untuk laporan historis yang tidak tercakup register aktif.
- [x] Verifikasi HTML dashboard dan commit terpisah.

**Hasil.** Status laporan dan status link berbentuk badge. Baris historis memakai tombol Detail. PHPUnit 28 tes, 247 asersi.

## Task 3 — Kewajiban pelaporan mulai T+1

- [x] Program dengan tahun awal akreditasi T mulai masuk daftar pelaporan tahun T+1. Tahun awal diambil dari `monitoring_accreditations.valid_from`.
- [x] Terapkan aturan pada daftar sekretariat, daftar program lembaga, validasi pembuatan link, dan submit. Laporan historis yang sudah tersimpan tetap terlihat pada daftar sekretariat.
- [x] Verifikasi kasus akreditasi 2024 untuk pelaporan 2025 dan akreditasi 2025 untuk pelaporan 2026, termasuk penolakan laporan tahun akreditasi.
- [x] Commit terpisah setelah tes lulus.

**Hasil.** Program baru muncul pada tahun T+1. Jalur daftar, link, dan submit memakai batas yang sama. PHPUnit 29 tes, 254 asersi.

## Task 4 — Detail pelapor dan versi kuesioner

- [x] Tampilkan nama akun dari `submitted_by` pada kiriman resmi terakhir. Jika belum ada kiriman atau akun tidak ditemukan, tampilkan keterangan yang sesuai.
- [x] Tampilkan versi kuesioner dalam format `tahun.nomor`. Versi instrumen yang sudah ada bernama `2025.1`. Pertahankan kaitan laporan dengan instrumen aslinya.
- [x] Verifikasi detail untuk draf, draf revisi, dan kiriman serta migrasi versi pada MySQL dev.
- [x] Commit terpisah setelah verifikasi.

**Hasil.** Detail menampilkan nama akun kiriman terakhir. Versi instrumen sekarang dilabeli 2025.1 di MySQL dev. Angka `versi` lama tetap digunakan untuk urutan internal. PHPUnit 29 tes, 258 asersi.

## Task 5 — Verifikasi akhir

- [x] Jalankan seluruh PHPUnit, periksa keluaran HTML dan CSV, serta verifikasi migrasi dan data aktual di MySQL dev.
- [x] Catat hasil dan keputusan implementasi pada dokumen ini lalu commit.

**Verifikasi akhir, 12 November 2026.** PHPUnit lulus 29 tes dan 258 asersi. MySQL dev menerima migrasi label versi dan menampilkan `2025.1`. Pemeriksaan register menemukan dua akreditasi yang baru dimulai tahun 2025 dan tiga akreditasi lebih lama yang memenuhi syarat pelaporan tahun 2025. Aturan T+1 dan baris historis diuji otomatis.
