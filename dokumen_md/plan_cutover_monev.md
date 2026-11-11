# Cutover Monev Implementation Plan

> Jalankan task secara berurutan dengan superpowers:executing-plans. Perbarui checklist dan catat bukti uji setiap kali satu task selesai.

**Tujuan:** Menjadikan `tr_monev` dan `tr_monev_jawaban` sebagai sumber laporan aktif, dengan histori dan URL yang tetap bisa ditelusuri.

**Arsitektur:** Verifikasi data lama dan relasi terlebih dahulu. Siapkan pembacaan dan penulisan baru per alur, lalu aktifkan bersama pada skema restore sebelum pengalihan database dev. Pertahankan versi historis di penyimpanan arsip sebelum menghapus tabel asal.

**Teknologi:** Laravel, Blade, Artisan, PHPUnit, MySQL.

**Spesifikasi:** [Plan Monev Kuesioner](plan_monev_kuesioner.md).

## Tracking

| Task | Hasil yang bisa diverifikasi | Status |
| --- | --- | --- |
| 1 | Rekonsiliasi laporan, jawaban, dan relasi pada skema restore | Selesai |
| 2 | Form, draf, submit, dan reset memakai transaksi Monev | Selesai, siap aktivasi bersama |
| 3 | Dashboard, detail, dan ekspor membaca sumber yang sama | Selesai, siap aktivasi bersama |
| 4 | Link dan impor berjalan melalui `monev_id` | Selesai, siap aktivasi bersama |
| 5 | Uji cutover di skema restore dan alihkan dev | Selesai, aktif di dev |
| 6 | Arsip dan cleanup tabel lama setelah verifikasi | Arsip selesai, cleanup dev menunggu konfirmasi |

## Batasan

- Pertahankan URL laporan yang telah dibagikan. ID lama dan ID Monev mungkin berbeda. Gunakan pemetaan `legacy_report_id` sebelum menerbitkan URL untuk laporan baru, dengan identitas rute yang tidak ambigu.
- Pertahankan pemeriksaan login, keanggotaan lembaga, dan role sekretariat. Link tetap mengikuti aturan kedaluwarsa dan penghapusan.
- Simpan versi kuesioner, angka nol, pilihan jamak, serta metadata pengiriman terakhir. Pengiriman lama dengan pengirim tak diketahui tetap bernilai null.
- Pertahankan versi jawaban historis dan provenance impor. Perubahan sumber data aplikasi, pelepasan relasi lama, dan penghapusan tabel adalah tindakan terpisah.

## Task 1 — Rekonsiliasi skema restore

- [x] Verifikasi backup `/private/tmp/paps-dev-backup.DrYSXT/paps.sql` dan skema `paps_restore_monev_dryst`. Dry run backfill menunjukkan 0 siap, 0 terblokir, dan 5 sudah dipetakan. Dry run pemulihan peserta menunjukkan 0 nilai tersisa.
- [x] Bandingkan 5 laporan lama dan 5 laporan Monev. UUID, lembaga, program, tahun, status, dan revisi cocok. Tidak ada pemetaan tanpa laporan asal. Pulihkan snapshot akreditasi yang kosong pada laporan lama ID 5 dari versi resmi, lalu verifikasi hasilnya di skema restore.
- [x] Cocokkan 4 link, 7 catatan audit, dan 3 sumber impor dengan laporan Monev. Tidak ditemukan relasi yang salah atau kosong.

**Keputusan rekonsiliasi:** Draf terbaru pada tabel lama untuk laporan ID 4 berisi nilai kosong di delapan pertanyaan pilihan. Nilai pada Monev cocok dengan 41 versi historis dan merupakan jawaban yang saat ini ditampilkan aplikasi. Pertahankan nilai yang ditampilkan tersebut, simpan versi lama dalam histori, dan verifikasi hasil setelah cutover. Task 5 juga harus memeriksa kembali dan mengisi snapshot ID 5 pada dev apabila masih kosong.

## Task 2 — Form dan penyimpanan lembaga

- [x] Gunakan UUID untuk URL baru dan petakan URL numerik lama lewat `legacy_report_id`. Uji mencakup ID lama 1 yang menunjuk ke Monev ID 42.
- [x] Siapkan pembacaan, draf, submit, dan reset dengan penguncian baris serta HTTP 409. Kiriman baru diarsipkan pada `monev_submissions` dalam transaksi yang sama. Migrasi arsip berhasil di skema MySQL restore.
- [x] Uji pembatasan akses, pemuatan ulang pilihan dan angka nol, penolakan draf duplikat, dan reset dengan histori tetap tersedia. Suite lengkap lulus 19 test dan 103 assertion.

**Aktivasi:** `MONEV_USE_NORMALIZED_REPORTS` tetap nonaktif secara bawaan hingga dashboard, link, dan impor siap diuji bersama pada Task 5. Uji PHPUnit mengaktifkan mode baru secara terisolasi. Uji alur lengkap pada MySQL tetap dijadwalkan untuk Task 5.

## Task 3 — Dashboard, detail, dan ekspor sekretariat

- [x] Daftar, filter, detail, dan ekspor memakai Monev saat mode baru aktif. Statistik nihil memakai arsip kiriman terakhir, sedangkan detail dan CSV memakai jawaban saat ini.
- [x] Uji dashboard, URL detail lama, dan CSV dengan tabel laporan lama dihapus dari fixture SQLite. Uji memeriksa angka nol, pilihan jamak, pemisahan 12a/12b, dan penolakan akses. Migrasi mengarsipkan 8 kiriman lama di MySQL restore.

## Task 4 — Link dan impor

- [x] Link dan audit memakai `monev_id` pada mode baru. Uji mencakup link lama, link baru tanpa ID lama, kedaluwarsa, pengaktifan ulang, penggantian, penghapusan, dan penolakan akun lembaga lain.
- [x] Impor memakai Monev, arsip kiriman, dan provenance dalam satu transaksi pada mode baru. Uji MySQL restore memverifikasi impor baru, impor ulang identik, penolakan konflik, serta jumlah laporan lama yang tetap. Transaksi uji dibatalkan.

Mode baru masih nonaktif pada database dev. Migrasi arsip kiriman dan perubahan relasi yang memungkinkan Monev tanpa ID lama sudah diuji pada skema restore. Aktivasi bersama dijadwalkan pada Task 5.

## Task 5 — Uji pengalihan dan aktivasi dev

- [x] Jalankan `php tests/Support/check-monev-cutover.php paps_restore_monev_dryst`. URL lama, dashboard, detail, CSV, link, draf, konflik revisi, submit, buka ulang, reset, dan impor lulus ketika kedua tabel lama dibayangi tabel temporer yang tidak memiliki kolom aplikasi. Uji ini menemukan perbandingan kode pertanyaan dengan angka pada reset MySQL. Perbandingan telah diubah menjadi teks dan uji ulang lulus.
- [x] Jeda aplikasi dev dan buat backup `/private/tmp/paps-dev-cutover.zSELLW/paps.sql`. Restore ke `paps_cutover_backup_zsellw` menunjukkan 5 laporan, 193 versi, 5 Monev, dan 90 jawaban. Verifikasi 5 pemetaan laporan, 8 kiriman yang diarsipkan, pemulihan snapshot ID 5, serta kesesuaian seluruh relasi link, audit, dan sumber impor.
- [x] Aktifkan `MONEV_USE_NORMALIZED_REPORTS=true` pada konfigurasi lokal, hidupkan kembali aplikasi dev, dan jalankan `php tests/Support/check-monev-cutover.php paps`. Seluruh alur lulus. Transaksi data sintetis pada kedua skema dibatalkan.

**Pemulihan:** Simpan backup sebelum cutover dan skema restore. Jika perlu kembali ke jalur lama, jeda penulisan dan rekonsiliasi perubahan Monev setelah aktivasi sebelum menonaktifkan flag atau menggunakan snapshot lama.

## Task 6 — Arsip dan cleanup

- [x] Arsipkan dan cocokkan seluruh kolom sumber untuk 5 laporan dan 193 versi di `paps` serta di skema restore. Arsip mencakup draf, kiriman, snapshot, dan atribusi historis.
- [x] Uji migrasi manual di `paps_restore_monev_dryst`. Relasi link, audit, dan sumber impor kini mewajibkan `monev_id` di skema uji. Kedua tabel lama sudah terhapus di sana dan smoke test rute serta impor tetap lulus.
- [x] Backup sebelum cleanup tersimpan di `/private/tmp/paps-dev-sebelum-cleanup.GxdAY9/paps.sql`. Restore ke `paps_cleanup_backup_gxday9` menghasilkan 5 laporan, 193 versi, 5 laporan arsip, 193 versi arsip, 5 Monev, dan 90 jawaban.
- [ ] Setelah konfirmasi penghapusan di dev, jeda penulisan, verifikasi bahwa koneksi mengarah ke MySQL lokal `paps`, lalu jalankan `php artisan migrate --path=database/migrations/manual/2026_11_11_000006_remove_legacy_monev_reports.php`. Buka kembali aplikasi dan ulangi `php tests/Support/check-monev-cutover.php paps`. Jika perlu pemulihan, gunakan backup terverifikasi setelah merekonsiliasi perubahan yang terjadi setelah backup.
