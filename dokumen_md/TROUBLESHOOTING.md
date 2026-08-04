# TROUBLESHOOTING.md

## Panduan Troubleshooting PAPS

Dokumen ini berisi masalah umum yang sering ditemukan saat development/testing dan solusinya.

---

## 1. Error 419 — CSRF Token Mismatch

### Penyebab
Session cookie tidak dikirim kembali karena domain cookie tidak sesuai dengan domain request.

### Solusi
Pastikan `.env` sesuai dengan domain yang digunakan:

```dotenv
# Untuk lokal via php artisan serve
APP_URL=http://127.0.0.1:8000
SESSION_DOMAIN=127.0.0.1
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000

# Untuk domain production
APP_URL=https://akreditasi.etc-nso.id
SESSION_DOMAIN=akreditasi.etc-nso.id
SANCTUM_STATEFUL_DOMAINS=akreditasi.etc-nso.id
```

Setelah mengubah `.env`, clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 2. Tidak Bisa Login — Password Salah

### Solusi
Reset password via Tinker:

```bash
php artisan tinker
```

```php
use App\Models\User;
User::where('email', 'email@domain.com')->update(['password' => bcrypt('password_baru')]);
```

---

## 3. Database Belum Lengkap / Tabel Tidak Ditemukan

### Penyebab
Hanya migration default Laravel yang dijalankan. Data master dan skema lengkap belum di-import.

### Solusi
Import ulang SQL dump:

```bash
mysql -h 127.0.0.1 -u root -p paps < database/pusdiklat_akreditasi.sql
```

Atau via Docker:

```bash
docker exec -i mysql mysql -uroot -p paps < database/pusdiklat_akreditasi.sql
```

---

## 4. Error 503 — Anda Tidak Memiliki Hak Akses

### Penyebab
User yang login memiliki role yang tidak sesuai dengan route yang diakses.

### Solusi
Cek role user di database:

```sql
SELECT id, name, email, role FROM users WHERE email = 'email@domain.com';
```

Role yang valid:
- `2` = Sekretariat
- `3` = Asesor
- `4` = Lembaga

---

## 5. File Upload Gagal atau Tidak Tercipta

### Penyebab
Folder tujuan upload tidak ada atau tidak writable.

### Solusi
Cek permission folder tujuan upload. Folder tujuan ditentukan di parameter `$fileCategory` saat memanggil `Uploadfile::upload()`.

```bash
chmod -R 775 folder_upload
```

Pastikan juga ukuran file tidak melebihi `upload_max_filesize` dan `post_max_size` di `php.ini`.

---

## 6. Asset CSS/JS Tidak Muncul / 404

### Penyebab
Belum build asset atau symlink storage belum dibuat.

### Solusi
Build asset:

```bash
npm run dev
```

Jika asset di storage publik:

```bash
php artisan storage:link
```

---

## 7. Login Google/SSO Gagal

### Penyebab
- `GOOGLE_CLIENT_ID` atau `CLIENT_SECRET` salah/expired.
- `REDIRECT_URI` tidak cocok dengan yang didaftarkan di provider.
- Domain tidak sama dengan yang terdaftar.

### Solusi
1. Verifikasi kredensial di `.env`.
2. Pastikan redirect URI di Google Console / Keycloak sama dengan `.env`.
3. Untuk lokal, daftarkan `http://localhost:8000/auth-callback` atau `http://localhost:8000/callback-gojags`.

---

## 8. Session Tidak Bertahan / Selalu Logout

### Penyebab
- `SESSION_DOMAIN` tidak cocok.
- Cookie tidak disetel dengan benar di browser.
- Session file corrupt (jika menggunakan driver `file`).

### Solusi
1. Sesuaikan `SESSION_DOMAIN` dengan domain aktif.
2. Clear cache session:

```bash
rm -rf storage/framework/sessions/*
php artisan cache:clear
```

3. Cek bahwa browser mengizinkan cookie untuk domain tersebut.

---

## 9. Error MySQL — Connection Refused

### Penyebab
MySQL tidak berjalan atau konfigurasi `.env` salah.

### Solusi
1. Pastikan MySQL berjalan:

```bash
docker ps | grep mysql
# atau
pgrep mysql
```

2. Cek konfigurasi `.env`:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paps
DB_USERNAME=root
DB_PASSWORD=your_password
```

3. Test koneksi:

```bash
php artisan tinker --execute="echo DB::connection()->getPdo() ? 'OK' : 'FAIL';"
```

---

## 10. Composer Autoload Error

### Solusi

```bash
composer dump-autoload
```

---

## 11. Halaman Blank / Error Tidak Muncul

### Penyebab
`APP_DEBUG=false` atau error disembunyikan.

### Solusi
Pastikan:

```dotenv
APP_ENV=local
APP_DEBUG=true
```

Lalu clear cache:

```bash
php artisan config:clear
```

---

## 12. Notifikasi WhatsApp Tidak Terkirim

### Penyebab
- Token Fonnte invalid/expired.
- Nomor tujuan salah format.
- Layanan Fonnte tidak tersedia.

### Solusi
1. Cek token di `app/Helpers/Notification.php` atau `.env`.
2. Pastikan nomor menggunakan format internasional dengan country code `62`.
3. Cek log response dari Fonnte.

---

## 13. Port 8000 Sudah Digunakan

### Solusi
Gunakan port lain:

```bash
php artisan serve --port=8001
```

Atau kill proses yang menggunakan port 8000:

```bash
lsof -ti:8000 | xargs kill -9
```

---

*Lihat juga: `SETUP.md`, `AUTHENTICATION.md`, `API.md`.*
