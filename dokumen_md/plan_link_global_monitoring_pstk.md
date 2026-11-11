# Rencana Implementasi Link Global Monitoring Akreditasi PSTK

> Jalankan per task dengan `superpowers:executing-plans`. Perbarui status dan catat hasil verifikasi di dokumen ini. Buat commit berbahasa Indonesia untuk setiap perubahan signifikan yang telah diverifikasi.

**Tujuan.** Sekretariat mencantumkan satu URL permanen pada surat pemberitahuan. Setelah login, lembaga masuk ke pelaporan tahun yang ditetapkan sekretariat dan hanya melihat program miliknya.

**Arsitektur.** PAPS menyimpan jadwal per tahun dan satu periode yang ditetapkan sebagai tujuan URL global. Aplikasi memeriksa jadwal dan histori kiriman di server. Ringkasan laporan memakai komponen Blade yang sama untuk lembaga dan sekretariat.

**Teknologi.** Laravel, Blade, MySQL, PHPUnit, dan alur login PAPS yang sudah ada.

**Dasar.** [Cutover Monev](plan_cutover_monev.md). Fitur baru memakai `tr_monev`, `tr_monev_jawaban`, `monev_submissions`, dan versi instrumen yang terikat pada laporan di `mt_kuesioner`.

## Tracking

| Task | Hasil yang bisa diperiksa | Status |
| --- | --- | --- |
| 1 | Sekretariat bisa mengatur periode dan menyalin URL permanen | Menunggu |
| 2 | Link mengarahkan akun lembaga ke program yang tepat setelah login Google atau GOJAGS | Menunggu |
| 3 | Aturan tenggat berlaku pada semua jalur perubahan laporan | Menunggu |
| 4 | Ringkasan laporan seragam dan menampilkan waktu kirim untuk sekretariat | Menunggu |
| 5 | Alur dari surat sampai ekspor diverifikasi di dev | Menunggu |

## Aturan yang disepakati

- URL permanen adalah `/monitoring-akreditasi-pstk/lapor`. **Salin Link Pelaporan** menyalin URL yang sama kapan pun. Sekretariat mengaktifkan periode, sehingga tidak perlu membuat token untuk surat atau lembaga.
- Sekretariat memilih tahun, tanggal dan jam buka, serta tanggal dan jam tutup dalam WIB. Hanya satu periode yang ditetapkan sebagai tujuan link global pada satu waktu. Periode yang ditetapkan bisa belum buka, sedang buka, atau sudah tutup. Jam buka inklusif dan jam tutup eksklusif. Jadwal disimpan dalam UTC.
- Lembaga harus login dan punya profil yang terhubung. Program diambil dari register akreditasi lembaga untuk tahun yang ditetapkan. Satu program langsung menuju tujuan sesuai statusnya. Beberapa program ditampilkan dalam daftar dengan status dan aksi masing-masing.
- Setelah tenggat, keputusan berlaku per lembaga, program, dan tahun. Laporan yang belum pernah disimpan tidak bisa dibuat. Draf yang sudah tersimpan sebelum tenggat dan belum pernah dikirim tetap bisa diedit dan dikirim, dengan peringatan. Laporan yang pernah dikirim hanya bisa dilihat dari kiriman terakhir, termasuk ketika masih ada draf revisi. Sesudah draf terlambat dikirim, laporan langsung menjadi hanya-baca.
- Di luar jam buka, URL global tetap menampilkan halaman pemberitahuan PAPS. Saat sudah tutup, pengguna dapat menuju laporan miliknya. Tampilan pemberitahuan memakai gaya PAPS biasa, dengan tanggal buka atau tutup dalam WIB sesuai keadaan.
- Sekretariat melihat jawaban terkini dan statusnya. Draf revisi tetap ditampilkan sebagai draf revisi. Detail dan CSV memakai sumber yang sama. Waktu Kirim Terakhir berasal dari kiriman resmi, tampil dalam WIB, dan tidak berubah ketika draf disimpan.
- Laporan yang dikirim setelah tenggat ditandai **Terlambat**. Catat penanda terhadap jadwal yang berlaku saat kiriman diterima, sehingga perubahan jadwal kemudian tidak mengubah penanda kiriman tersebut. Untuk histori yang belum mempunyai metadata periode, tampilkan waktu kirim yang diketahui dan biarkan penanda keterlambatan kosong.
- Jaga URL link per lembaga yang sudah beredar. Aturan periode untuk tahun yang telah dijadwalkan berlaku sama pada link global, link per lembaga, menu mandiri, dan request dari tab yang sudah terbuka. Tahun historis tanpa jadwal tetap dapat diakses melalui menu yang sudah tersedia.

## Task 1 — Periode pelaporan dan salin link

- [ ] Tambahkan migration MySQL dan model untuk periode pelaporan. Simpan tahun yang unik, waktu buka, waktu tutup, dan identitas periode yang ditetapkan untuk URL global. Perubahan tujuan harus atomik, termasuk saat dua sekretariat mengaktifkan periode bersamaan.
- [ ] Tambahkan kontrol **Periode Pelaporan** pada dashboard `resources/views/sekretariat/monitoring-evaluasi.blade.php`. Validasi tanggal tutup sesudah tanggal buka. Hanya sekretariat yang boleh menyimpan jadwal atau mengganti periode tujuan. Tampilkan tahun, status jadwal, dan jam WIB.
- [ ] Tambahkan GET `/monitoring-akreditasi-pstk/lapor` bernama `monitoring-akreditasi.global`, dengan halaman informasi periode sebagai tujuan awal. Tambahkan **Salin Link Pelaporan**. Jika clipboard tidak tersedia, tampilkan URL yang bisa disalin manual. Tombol tersedia sebelum, selama, dan sesudah periode.
- [ ] Verifikasi dengan test bahwa jadwal tidak valid ditolak, satu periode menjadi tujuan global, pergantian tahun tidak mengubah URL, dan role lembaga tidak dapat mengubah jadwal.

## Task 2 — Akses global dan kembali setelah login

- [ ] Hubungkan route `monitoring-akreditasi.global` ke alur masuk di controller Monitoring Akreditasi. GET hanya membaca data. Sistem menyimpan tujuan login melalui mekanisme Laravel dan memeriksa periode serta profil saat pengguna kembali.
- [ ] Di `app/Http/Controllers/Auth/LoginController.php`, ubah callback Google langsung agar memakai `redirect()->intended(...)` setelah autentikasi berhasil, dengan halaman utama sebagai tujuan bawaan. Pertahankan alur GOJAGS yang sudah memakai tujuan login. Tujuan setelah login tetap berasal dari rute internal yang dibuka pengguna.
- [ ] Gunakan program terakreditasi untuk tahun periode. Arahkan satu program ke form baru, draf, atau halaman lihat sesuai hasil pemeriksaan. Untuk beberapa program, tampilkan daftar dan statusnya. Untuk periode belum dijadwalkan, belum buka, sudah tutup, atau lembaga tanpa program, tampilkan pemberitahuan PAPS yang sesuai.
- [ ] Verifikasi login langsung dan login dari URL global, akun tanpa profil, satu atau beberapa program, periode yang berubah saat proses login, serta penolakan akses ke program lembaga lain.

## Task 3 — Penegakan tenggat dan kiriman terlambat

- [ ] Tambahkan pemeriksaan periode bersama di layanan laporan untuk membuat, menyimpan draf, mengirim, dan reset. Terapkan pemeriksaan dalam transaksi dengan penguncian yang konsisten pada saat menulis. Gunakan histori kiriman untuk mengenali laporan yang pernah dikirim meskipun status sekarang `revision_draft`.
- [ ] Setelah tutup, izinkan simpan dan kirim hanya untuk draf yang tersimpan sebelum tenggat dan belum memiliki kiriman resmi. Laporan kosong yang dibuat otomatis oleh generate link tidak memenuhi syarat draf tersimpan. Untuk laporan yang pernah dikirim, tolak edit, reset, dan submit, serta ambil kiriman terakhir untuk tampilan lembaga.
- [ ] Pada draf yang masih boleh dikirim, tampilkan banner kuning berisi waktu penutupan WIB dan ajakan segera mengirim. Tolak request yang sudah tidak memenuhi syarat dengan pesan yang bisa ditampilkan form. Pertahankan jawaban terakhir yang berhasil tersimpan.
- [ ] Simpan waktu submit dan metadata keterlambatan di arsip kiriman dalam transaksi submit. Periksa juga tautan sekretariat dan menu mandiri agar aturan tidak bisa dilewati dengan memakai rute lain.
- [ ] Uji sebelum buka, tepat saat tutup, dan sesudah tutup. Uji tab lama, laporan kosong dari generate link, draf terlambat yang menjadi hanya-baca setelah submit, draf revisi yang dikunci, serta dua program dengan status berbeda.

## Task 4 — Tampilan laporan bersama dan waktu kirim

- [ ] Ekstrak komponen ringkasan dari `resources/views/lembaga/monitoring-akreditasi/view.blade.php` dan pakai pada detail sekretariat. Sembunyikan pertanyaan kosong dan blok pelatihan yang tidak berlaku. Pertahankan angka nol, jawaban jamak, dan tahun empat digit.
- [ ] Tampilkan **Jenis Kuesioner** dan **Versi Kuesioner** dari instrumen yang terikat pada laporan. Status **Draf**, **Draf Revisi**, atau **Terkirim** tampil terpisah. Sekretariat tetap punya **Ekspor CSV**. Lembaga melihat **Edit** hanya jika masih berhak mengubah laporan.
- [ ] Sekretariat melihat **Waktu Kirim Terakhir** dalam WIB atau **Belum dikirim**, dengan penanda **Terlambat** jika kiriman tercatat terlambat. Saat ada draf revisi, isi detail dan CSV tetap berasal dari draf terkini, sementara waktu kirim berasal dari arsip resmi terakhir.
- [ ] Uji konsistensi detail dan CSV sekretariat untuk draf dan kiriman. Uji lembaga setelah tenggat saat ada draf revisi, termasuk bahwa jawaban di halaman lihat berasal dari kiriman terakhir dan tombol Edit tidak muncul.

## Task 5 — Verifikasi dev dan kesiapan link surat

- [ ] Jalankan suite PHPUnit. Uji di MySQL dev dengan dua akun lembaga dari profil berbeda dan akun sekretariat. Periksa link yang sama dari surat sebelum buka, selama buka, dan setelah tutup.
- [ ] Uji callback Google dan GOJAGS dengan akun yang sah. Jika penyedia login tidak tersedia di dev, catat bagian yang telah diuji otomatis dan validasi interaktif yang masih diperlukan.
- [ ] Lakukan satu simulasi surat dari salin link hingga draf, submit, peringatan keterlambatan, lihat laporan dan ekspor. Ulangi untuk program kedua yang belum menyimpan draf saat tenggat lewat.
- [ ] Perbarui tabel tracking dengan hasil uji dan commit per task. URL siap dicantumkan di surat setelah jalur login yang digunakan lembaga dan periode yang dipilih diverifikasi pada lingkungan tujuan.
