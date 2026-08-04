# ADR.md

## Architecture Decision Records (ADR)

Dokumen ini mencatat keputusan arsitektur penting yang diambil dalam project PAPS.

---

## ADR-001: Penggunaan Laravel 8

### Konteks
Membangun aplikasi web dengan banyak fitur CRUD, autentikasi, role-based access, dan generate dokumen.

### Keputusan
Menggunakan Laravel Framework versi 8.

### Alasan
- Ekosistem Laravel mature untuk aplikasi web berbasis PHP.
- Blade, Eloquent, dan migration mempercepat development.
- Komunitas dan dokumentasi luas.
- Socialite untuk OAuth tersedia secara native.

### Konsekuensi
- Perlu upgrade ke Laravel 10/11 di masa depan untuk security patch jangka panjang.
- Versi 8 masih mendukung PHP 7.3 dan 8.0.

---

## ADR-002: Database Skema via SQL Dump

### Konteks
Project memiliki data master yang kompleks (unsur, subunsur, item, wilayah, pangkat, dll).

### Keputusan
Skema lengkap dan data master dikelola melalui `database/pusdiklat_akreditasi.sql`, bukan sepenuhnya via Laravel migration.

### Alasan
- Memudahkan import dari database yang sudah ada.
- Data master bersifat statis dan banyak.
- Memudahkan backup/restore dalam satu file.

### Konsekuensi
- Migration Laravel hanya berisi tabel default (`users`, `password_resets`, `failed_jobs`).
- Perubahan skema harus manual di SQL dump atau dibuatkan migration baru.
- Tidak ideal untuk kolaborasi tim besar tanpa disiplin version control SQL dump.

---

## ADR-003: Role-Based Access Control Sederhana

### Konteks
Aplikasi memiliki 3 peran utama: Sekretariat, Asesor, dan Lembaga.

### Keputusan
Menggunakan kolom integer `role` di tabel `users` dengan middleware sederhana untuk otorisasi.

### Alasan
- Mudah diimplementasikan dan dipahami.
- Cukup untuk 3 peran dengan hak akses yang relatif jelas.
- Tidak memerlukan package RBAC tambahan.

### Konsekuensi
- Tidak fleksibel untuk role dinamis atau permission granular.
- Jika peran bertambah kompleks, disarankan migrasi ke Spatie Permission atau Gate/Policy.

---

## ADR-004: Session Cookie Driver

### Konteks
Aplikasi berjalan di shared hosting dengan kemungkinan terbatas untuk Redis/database session.

### Keputusan
Menggunakan `SESSION_DRIVER=cookie`.

### Alasan
- Tidak memerlukan storage session di server.
- Mudah di-scale secara horizontal.

### Konsekuensi
- Cookie menjadi lebih besar karena menyimpan session terenkripsi.
- Perlu memastikan `SESSION_DOMAIN` sesuai dengan domain aktif agar cookie tidak hilang.

---

## ADR-005: Autentikasi Multi-Provider

### Konteks
User aplikasi adalah pegawai BPS dan lembaga eksternal. Diperlukan fleksibilitas login.

### Keputusan
Menggunakan 3 mekanisme login:
1. Email/password (default Laravel).
2. Google OAuth (Laravel Socialite).
3. SSO BPS (GOJAGS) via Keycloak.

### Alasan
- BPS user dapat login menggunakan SSO internal.
- Lembaga eksternal dapat login dengan Google atau email.

### Konsekuensi
- Manajemen kredensial provider lebih banyak.
- Redirect URI harus didaftarkan di setiap provider.
- Proses registrasi user dari SSO harus sinkron dengan struktur lokal.

---

## ADR-006: Generate Dokumen dengan dompdf dan phpword

### Konteks
Sistem perlu menghasilkan berita acara, rekomendasi, dan sertifikat dalam format PDF/Word.

### Keputusan
Menggunakan `barryvdh/laravel-dompdf` untuk PDF dan `phpoffice/phpword` untuk Word.

### Alasan
- Package ini sudah terintegrasi dengan Laravel.
- Cukup untuk kebutuhan generate dokumen sederhana.

### Konsekuensi
- Template dokumen perlu di-maintain dengan hati-hati.
- Generate dokumen kompleks bisa membebani memory/CPU.

---

## ADR-007: Notifikasi WhatsApp via Fonnte

### Konteks
Diperlukan notifikasi cepat ke user terkait status pengajuan/penilaian.

### Keputusan
Menggunakan API Fonnte untuk mengirim pesan WhatsApp.

### Alasan
- API Fonnte relatif mudah digunakan.
- WhatsApp adalah kanal komunikasi yang umum digunakan.

### Konsekuensi
- Bergantung pada ketersediaan layanan pihak ketiga.
- Token API harus dijaga kerahasiaannya.

---

## ADR-008: E-TTD Publik

### Konteks
Diperlukan fitur tanda tangan digital untuk dokumen akreditasi.

### Keputusan
Membuat halaman tanda tangan digital yang dapat diakses publik tanpa login penuh.

### Alasan
- Memudahkan penandatangan dokumen oleh pihak eksternal.
- Mengurangi beban login untuk fitur spesifik.

### Konsekuensi
- Perlu validasi token/signature agar tidak disalahgunakan.
- Perlu mekanisme keamanan untuk mencegah akses pengajuan yang tidak berhak.

---

## ADR-009: Helper Functions via Service Provider

### Konteks
Beberapa fungsi umum (upload, notifikasi) digunakan di banyak controller.

### Keputusan
Fungsi umum diletakkan di `app/Helpers/` dan di-load melalui Service Provider dengan class aliases.

### Alasan
- Mudah dipanggil dengan facade-like syntax.
- Tidak memerlukan global function yang sulit di-test.

### Konsekuensi
- Helper class menjadi sedikit terkait erat dengan Service Provider.
- Untuk skala besar, pertimbangkan pindah ke Service class atau Repository.

---

## ADR-010: Laravel Mix dengan Webpack

### Konteks
Frontend memerlukan kompilasi Sass dan JavaScript.

### Keputusan
Menggunakan Laravel Mix v5 dengan Webpack.

### Alasan
- Konfigurasi minimal dibandingkan Webpack murni.
- Integrasi native dengan Laravel.

### Konsekuensi
- Versi 5 sudah lebih tua; pertimbangkan upgrade ke Vite/Laravel Mix 6.
- Node.js version compatibility perlu diperhatikan.

---

*Lihat juga: `ARSITEKTUR.md`, `PRD.md`, `CONTRIBUTING.md`.*
