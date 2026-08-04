# CONTRIBUTING.md

## Panduan Kontribusi PAPS

Dokumen ini berisi aturan dan alur kontribusi bagi developer yang ingin mengembangkan project PAPS.

---

## 1. Persiapan

1. Pastikan sudah membaca `SETUP.md` dan bisa menjalankan aplikasi di lokal.
2. Pastikan sudah membaca `ARSITEKTUR.md` dan `PRD.md` untuk memahami konteks bisnis.
3. Gunakan branch yang jelas untuk setiap perubahan.

---

## 2. Branching

Gunakan model branching berikut:

```
main
  ├── feature/nama-fitur
  ├── bugfix/nama-bug
  └── hotfix/nama-hotfix
```

Contoh:

```bash
git checkout -b feature/penilaian-multi-asesor
git checkout -b bugfix/fix-upload-pdf
```

---

## 3. Commit Message

Gunakan format yang deskriptif:

```
<type>: <deskripsi singkat>

<body opsional>
```

Tipe yang digunakan:
- `feat:` fitur baru
- `fix:` perbaikan bug
- `docs:` perubahan dokumentasi
- `refactor:` refactor kode tanpa mengubah perilaku
- `test:` penambahan/ubahan test
- `chore:` maintenance, update dependency, dll

Contoh:

```bash
git commit -m "feat: tambah fitur reset tanda tangan digital"
git commit -m "fix: perbaiki error CSRF saat login lokal"
git commit -m "docs: update API.md untuk endpoint sekretariat"
```

---

## 4. Coding Standard

### 4.1 PHP
- Ikuti PSR-12.
- Gunakan type hinting jika memungkinkan.
- Jaga method/controller agar tidak terlalu panjang (fat controller). Pertimbangkan ekstraksi ke service atau helper.
- Hindari hardcode kredensial, URL, atau secret. Gunakan `.env`.

### 4.2 Blade
- Gunakan komponen jika ada repetisi UI.
- Pisahkan layout, partial, dan halaman.

### 4.3 Database
- Jika menambah tabel, buat migration Laravel.
- Jika mengubah data master, update juga `database/pusdiklat_akreditasi.sql` agar konsisten dengan production.
- Hindari menghapus kolom tanpa backup.

---

## 5. Pull Request

Sebelum membuat PR:

1. Pastikan kode berjalan di lokal.
2. Jalankan test: `php artisan test`.
3. Periksa tidak ada error PHP: `php artisan route:list`.
4. Update dokumentasi jika diperlukan.
5. Buat PR dengan deskripsi yang jelas:
   - Tujuan perubahan
   - Fitur yang diubah/ditambah
   - Cara test
   - Screenshot jika ada perubahan UI

---

## 6. Code Review

PR akan di-review sebelum merge. Hal yang diperhatikan:
- Fungsionalitas berjalan sesuai requirement.
- Tidak ada hardcode secret atau kredensial.
- Tidak ada query N+1.
- Validasi input sudah sesuai.
- Tidak ada breaking change tanpa alasan.

---

## 7. Penamaan File & Folder

- Controller: `PascalCaseController.php`
- Model: `PascalCase.php`
- Migration: `yyyy_mm_dd_nnnnnn_nama_migration.php`
- View: `snake_case.blade.php`
- Helper: `PascalCaseHelper.php` atau `snake_case.php`

---

## 8. Hal yang Dilarang

- Commit file `.env` yang berisi kredensial.
- Commit folder `vendor/`, `node_modules/`, atau file build yang seharusnya di-ignore.
- Menambahkan endpoint tanpa middleware otorisasi yang sesuai.
- Mengubah skema database production tanpa backup dan approval.

---

## 9. Kontak

Jika ada pertanyaan, diskusikan melalui issue atau Slack/Discord channel project.

---

*Lihat juga: `SETUP.md`, `TESTING.md`, `ADR.md`.*
