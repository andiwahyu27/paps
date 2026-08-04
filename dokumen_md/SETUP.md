# SETUP.md

## Setup Lokal PAPS

Panduan ini menjelaskan cara menjalankan project PAPS di lingkungan lokal (macOS/Linux dengan Herd/PHP + Docker MySQL).

---

## Prasyarat

- PHP >= 7.3 atau >= 8.0 (rekomendasi PHP 8.2)
- Composer
- MySQL / MariaDB
- Node.js + npm
- (Opsional) Docker untuk menjalankan MySQL
- (Opsional) Laravel Herd

---

## 1. Clone & Install Dependency

```bash
# Pastikan berada di root project
composer install
npm install
```

> Catatan: Project ini sudah memiliki `vendor/`. Jika ada masalah autoload, jalankan:
> ```bash
> composer dump-autoload
> ```

---

## 2. Konfigurasi Environment

Salin atau modifikasi file `.env`:

```bash
cp .env.example .env
# atau langsung edit file .env yang sudah ada
php artisan key:generate
```

Konfigurasi minimal yang perlu diperhatikan:

```dotenv
APP_NAME="PAPS | Platform Akreditasi Pelatihan Prakom & Statistisi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paps
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

SESSION_DRIVER=cookie
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

> **Penting**: Untuk login via curl atau browser lokal, pastikan `APP_URL` dan `SESSION_DOMAIN` mengarah ke domain lokal. Jika menggunakan `127.0.0.1:8000`, sesuaikan `SESSION_DOMAIN` menjadi `127.0.0.1`.

---

## 3. Setup Database

### Opsi A: Menggunakan Docker MySQL

Jika belum ada MySQL, jalankan container:

```bash
docker run -d \
  --name mysql \
  -e MYSQL_ROOT_PASSWORD=your_mysql_password \
  -e MYSQL_DATABASE=paps \
  -p 3306:3306 \
  mysql:8.0
```

Tunggu beberapa detik sampai MySQL ready, lalu import SQL dump:

```bash
# Jika mysql client tersedia di host
mysql -h 127.0.0.1 -u root -p paps < database/pusdiklat_akreditasi.sql

# Jika tidak, gunakan docker exec
docker exec -i mysql mysql -uroot -p paps < database/pusdiklat_akreditasi.sql
```

### Opsi B: Menggunakan MySQL yang Sudah Terinstall

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS paps;"
mysql -u root -p paps < database/pusdiklat_akreditasi.sql
```

---

## 4. Verifikasi Database

```bash
php artisan migrate:status
```

Hasil yang diharapkan:

```
+------+------------------------------------------------+-------+
| Ran? | Migration                                      | Batch |
+------+------------------------------------------------+-------+
| Yes  | 2014_10_12_000000_create_users_table           | 1     |
| Yes  | 2014_10_12_100000_create_password_resets_table | 1     |
| Yes  | 2019_08_19_000000_create_failed_jobs_table     | 1     |
+------+------------------------------------------------+-------+
```

---

## 5. Build Asset Frontend

```bash
npm run dev      # development
# atau
npm run prod     # production
```

---

## 6. Jalankan Aplikasi

```bash
php artisan serve --port=8000
```

Buka browser di:

```
http://127.0.0.1:8000/login
```

---

## 7. Login Pertama Kali

Project ini sudah memiliki seed user dari SQL dump. Untuk login uji coba, gunakan salah satu user dari tabel `users`:

```sql
SELECT id, name, email, role FROM users;
```

Role:
- `2` = Sekretariat
- `3` = Asesor
- `4` = Lembaga

Jika password lupa, reset via Laravel Tinker:

```bash
php artisan tinker
```

```php
User::find(1)->update(['password' => bcrypt('password_baru')])
```

---

## 8. Struktur Penting yang Perlu Diketahui

| Path | Keterangan |
|------|------------|
| `app/Http/Controllers/` | Semua controller, dibagi per peran |
| `app/Models/` | Eloquent models |
| `app/Http/Middleware/` | Middleware RBAC |
| `routes/web.php` | Route utama aplikasi |
| `database/pusdiklat_akreditasi.sql` | Dump lengkap skema + data master |
| `resources/views/` | Blade templates |
| `resources/sass/` | Styling Sass |
| `public/` | Asset publik |

---

## 9. Catatan & Kendala Umum

- **Database tidak sepenuhnya dikelola via migration**. Skema lengkap dan data master berasal dari `database/pusdiklat_akreditasi.sql`. Jika ada perubahan skema, lakukan di SQL dump atau buat migration baru.
- **Session menggunakan cookie**. Pastikan `SESSION_DOMAIN` dan `APP_URL` sesuai dengan domain yang digunakan saat testing.
- **File upload** disimpan di folder sesuai kategori file, bukan di `storage/app/public` secara default.

---

## 10. Perintah Berguna

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Route list
php artisan route:list

# Tinker
php artisan tinker
```

---

*Lihat juga: `ARSITEKTUR.md`, `DATABASE.md`, `AUTHENTICATION.md`, `API.md`.*
