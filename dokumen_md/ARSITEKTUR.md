# Arsitektur PAPS — Platform Akreditasi Pelatihan Prakom & Statistisi

## 1. Ringkasan

PAPS adalah aplikasi berbasis Laravel 8 untuk mengelola proses akreditasi pelatihan Pranata Komputer (Prakom) dan Statistisi. Aplikasi ini memiliki tiga peran utama: **Lembaga**, **Asesor**, dan **Sekretariat**, dengan alur kerja mulai dari pengajuan, verifikasi, penilaian, hingga penerbitan sertifikat dan berita acara.

---

## 2. Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend Framework | Laravel 8 (PHP ^7.3 \|\| ^8.0) |
| Frontend | Blade Templates, Bootstrap 5, Sass, Laravel Mix (Webpack) |
| Database | MySQL / MariaDB |
| Auth | Laravel Auth, Laravel Socialite, SSO BPS (GOJAGS), Google OAuth |
| PDF/Dokumen | barryvdh/laravel-dompdf, phpoffice/phpword |
| Monitoring | itsgoingd/clockwork |
| HTTP Client | Guzzle |
| JWT | firebase/php-jwt |

---

## 3. Struktur Folder

```
/Users/pusdiklatbps3/Developer/paps
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Helpers/              # Helper custom: Upload, Notifikasi, JWT, Error
│   ├── Http/
│   │   ├── Controllers/      # Controller, dibagi per peran
│   │   │   ├── Asesor/
│   │   │   ├── Auth/
│   │   │   ├── Lembaga/
│   │   │   ├── HomeController.php
│   │   │   ├── PanduanController.php
│   │   │   ├── PengaturanController.php
│   │   │   ├── SekretariatController.php
│   │   │   └── TtdController.php
│   │   ├── Kernel.php
│   │   └── Middleware/       # Middleware RBAC sederhana
│   ├── Models/               # Eloquent Models
│   ├── Providers/            # Service Provider bawaan + custom
│   └── View/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/           # Hanya migrasi default Laravel
│   ├── seeders/
│   └── pusdiklat_akreditasi.sql   # Dump lengkap skema + data master
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   ├── lang/
│   ├── sass/
│   └── views/                # Blade templates, dibagi per peran
├── routes/
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
├── .env
├── composer.json
├── package.json
└── webpack.mix.js
```

---

## 4. Model Domain

### Tabel Master (`mt_*`)

| Model | Tabel | Keterangan |
|-------|-------|------------|
| `Unsur` | `mt_unsurs` | Unsur penilaian akreditasi |
| `Subunsur` | `mt_subunsurs` | Sub-unsur penilaian |
| `Item` | `mt_items` | Butir penilaian dengan bobot |
| `JenisPengajuan` | `mt_jenis_pengajuans` | Jenis: Pranata Komputer, Statistisi |
| `Pangkat` | `mt_pangkat` | Daftar pangkat ASN |
| `Wilayah` | `mt_wilayah` | Provinsi / Kabupaten-Kota |
| `DokumenProgram` | `mt_program_dokumens` | Daftar dokumen program pelatihan |

### Tabel Transaksi (`tb_*`)

| Model | Tabel | Keterangan |
|-------|-------|------------|
| `User` | `users` | Autentikasi (role: 2, 3, 4) |
| `Profile` | `tb_profile_lembagas` | Profil lembaga pelatihan |
| `Pengajuan` | `tb_pengajuans` | Permohonan akreditasi |
| `Pelatihan` | `tb_pelatihans` | Program pelatihan dalam pengajuan |
| `Penilaian` | `tb_penilaians` | Nilai asesor per item |
| `Tenaga` | `tb_tenaga` | Data tenaga pelatihan |
| `Fasilitas` | `tb_fasilitas` | Fasilitas lembaga |
| `DokumenPelatihan` | `tb_pelatihan_dokumens` | Dokumen per program |
| `TenagaPelatihan` | `tb_pelatihan_tenagas` | Tenaga pengajar per program |
| Riwayat | `tb_riwayat_*` | Riwayat jabatan, kerja, pelatihan, pendidikan |

---

## 5. Role-Based Access Control

Role disimpan di kolom `role` pada tabel `users`:

| Role | Nilai | Middleware | Keterangan |
|------|-------|------------|------------|
| Sekretariat | `2` | `is.sekretariat` | Manajemen user, verifikasi, assign asesor, monitoring |
| Asesor | `3` | `is.asesor` | Penilaian pra/visitasi/paska/final |
| Lembaga | `4` | `is.lembaga` | Mengisi profil dan pengajuan |
| Asesor/Sekretariat | `3`/`2` | `is.asesor.or.sekretariat` | Akses tertentu yang dibagi |

---

## 6. Alur Bisnis

### 6.1 Lembaga
1. Login (SSO / Google / Email).
2. Mengisi **Profil Lembaga**: kelembagaan, tenaga, fasilitas, penyelenggaraan.
3. Mengajukan permohonan akreditasi (`pengajuan`).
4. Mengunggah dokumen program pelatihan.
5. Mengunci profil (`profile.lock`) saat siap dinilai.

### 6.2 Sekretariat
1. Melihat permohonan yang masuk.
2. Memverifikasi pengajuan.
3. Menetapkan asesor (`assign-asesor`).
4. Monitoring evaluasi dan penyelenggaraan.
5. Generate berita acara / sertifikat.

### 6.3 Asesor
1. Melihat pengajuan yang ditugaskan.
2. Penilaian **Pra-Visit** (pra_visit_asesor1/2/3).
3. Penilaian **Visitasi**.
4. Penilaian **Paska-Visit**.
5. Penilaian **Final**.
6. Mengunggah berita acara, rekomendasi, dan sertifikat.

---

## 7. Controller & Endpoint Utama

### Autentikasi
- `Auth::routes()` (login, register, forgot password, dll).
- `/auth-redirect` → Google OAuth.
- `/redirect-gojags/{type}` → SSO BPS (GOJAGS).
- `/login-error`.

### Sekretariat (`is.sekretariat`)
- `/pengguna/{role?}` → manajemen user.
- `/pengajuan/view/{id}` → lihat permohonan.
- `/pengajuan/verifikasi` → verifikasi.
- `/assign-asesor` → tetapkan asesor.
- `/generate-ba/{id}` → generate berita acara.
- `/monitoring-evaluasi`, `/monitoring-penyelenggaraan/{id}`.
- `/pelatihan/*`, `/lembaga/*`.

### Asesor (`is.asesor`)
- `/pengajuan/pravisit/{id}`
- `/pengajuan/pravisit2/{id}`
- `/pengajuan/visitasi/{id}`
- `/pengajuan/paskavisit/{id}`
- `/pengajuan/final/{id}`
- `/pengajuan/nilai/*` → proses penilaian item.
- `/pengajuan/bukti-dukung/{pengajuan}/{kode}`.

### Asesor / Sekretariat (`is.asesor.or.sekretariat`)
- `/pengajuan/ekspor-penilaian`
- `/pengajuan/pravisit2/view/{id}`
- `/pengajuan/visitasi/ekspor-rekomendasi/{id}`
- `/pengajuan/ekspor-sertifikat/{id}`
- `/pengajuan/visitasi/store-ba`
- `/pengajuan/store-rekomendasi`
- `/pengajuan/store-sertifikat`

### Lembaga (`is.lembaga`)
- `/pengajuan/{type?}` → daftar/ajukan permohonan.
- `/pengajuan/{type?}/edit`
- `/pengajuan/store`, `/pengajuan/update`, `/pengajuan/batal`
- `/profile/kelembagaan/{step?}`
- `/profile/tenaga/{step?}`
- `/profile/fasilitas/{step?}`
- `/profile/penyelenggaraan/{step?}`
- `/profile/tenaga-dokumen/*`
- `/program/{id}/{step?}` → dokumen program pelatihan.

### E-TTD (Public)
- `/ttd`
- `/ettd/save-signature`
- `/ttd/{pengajuanId}`
- `/ttd/download`

---

## 8. Helper & Integrasi

| Helper | File | Fungsi |
|--------|------|--------|
| `Uploadfile` | `app/Helpers/Uploadfile.php` | Upload file dengan penamaan unik |
| `Notification` | `app/Helpers/Notification.php` | Kirim notifikasi WhatsApp via Fonnte |
| `JwtHelper` | `app/Helpers/JwtHelper.php` | Validasi token JWT |
| `ErrorHelper` | `app/Helpers/ErrorHelper.php` | Helper error handling |

### Integrasi Eksternal (dari `.env`)
- **Database**: MySQL local.
- **Google OAuth**: untuk login.
- **SSO BPS (GOJAGS)**: untuk login pegawai BPS.
- **JWT**: untuk token client.
- **Fonnte**: WhatsApp gateway.
- **Mailtrap**: untuk email testing.
- **Domain**: `akreditasi.etc-nso.id`.

---

## 9. Asset & Build

- SCSS: `resources/sass/`
- JavaScript: `resources/js/`
- Build: `webpack.mix.js` + Laravel Mix v5.
- Run development: `npm run dev` / `npm run watch`.
- Run production: `npm run prod`.

---

## 10. Catatan & Rekomendasi

- **Database tidak dikelola via migration**; skema lengkap berada di `database/pusdiklat_akreditasi.sql`.
- **Middleware RBAC sederhana** berbasis nilai integer di kolom `role`.
- **Business logic cukup banyak di controller**; bisa dipertimbangkan untuk dipindahkan ke Service/Repository layer agar lebih mudah di-maintain.
- **File `.env` berisi kredensial eksternal**; pastikan tidak di-push ke repositori publik.
- **Aplikasi menggunakan soft delete** pada model `Pengajuan` dan `Pelatihan`.

---

*Dokumen ini dibuat secara otomatis dari analisis arsitektur kode PAPS.*
