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

- [ ] Perbaiki keluaran `@include` yang muncul mentah pada status laporan.
- [ ] Samakan tampilan baris laporan historis tahun 2025 dengan badge status, badge status link, dan tombol Detail.
- [ ] Pastikan nama, program, tahun, dan tautan detail tetap benar untuk laporan historis yang tidak tercakup register aktif.
- [ ] Verifikasi HTML dashboard dan commit terpisah.

## Task 3 — Kewajiban pelaporan mulai T+1

- [ ] Program dengan tahun awal akreditasi T mulai masuk daftar pelaporan tahun T+1. Tahun awal diambil dari `monitoring_accreditations.valid_from`.
- [ ] Terapkan aturan pada daftar sekretariat, daftar program lembaga, validasi pembuatan link, dan submit. Laporan historis yang sudah tersimpan tetap terlihat pada daftar sekretariat.
- [ ] Verifikasi kasus akreditasi 2024 untuk pelaporan 2025 dan akreditasi 2025 untuk pelaporan 2026, termasuk penolakan laporan tahun akreditasi.
- [ ] Commit terpisah setelah tes lulus.

## Task 4 — Detail pelapor dan versi kuesioner

- [ ] Tampilkan nama akun dari `submitted_by` pada kiriman resmi terakhir. Jika belum ada kiriman atau akun tidak ditemukan, tampilkan keterangan yang sesuai.
- [ ] Tampilkan versi kuesioner dalam format `tahun.nomor`. Versi instrumen yang sudah ada bernama `2025.1`. Pertahankan kaitan laporan dengan instrumen aslinya.
- [ ] Verifikasi detail untuk draf, draf revisi, dan kiriman serta migrasi versi pada MySQL dev.
- [ ] Commit terpisah setelah verifikasi.

## Task 5 — Verifikasi akhir

- [ ] Jalankan seluruh PHPUnit, periksa keluaran HTML dan CSV, serta verifikasi migrasi dan data aktual di MySQL dev.
- [ ] Catat hasil dan keputusan implementasi pada dokumen ini lalu commit.
