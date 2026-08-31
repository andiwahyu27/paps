# DEPLOYMENT.md

## Panduan Deployment PAPS

Dokumen ini menjelaskan langkah-langkah deploy aplikasi PAPS ke server production.

Dokumen khusus VM lama Apache + `htdocs` tersedia di:

```text
dokumen_md/PANDUAN_DEPLOY_VM_LAMA_AKREDITASI_ETC_NSO.md
```

Dokumen tersebut mencakup backup, pemeriksaan path, preservasi `.env`/`APP_KEY`, migration aman, permission PHP-FPM, konfigurasi Apache, smoke test TTD/DOCX, dan rollback.

---

## 1. Persiapan Server

### 1.1 Requirement Server

- OS: Ubuntu 20.04/22.04 LTS (rekomendasi)
- Web Server: Nginx atau Apache
- PHP: 8.0 atau 8.1/8.2
- PHP Extensions: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`, `gd`
- Database: MySQL 8.0 / MariaDB 10.6+
- Composer
- Node.js + npm
- Redis (opsional, untuk queue/cache)

### 1.2 Contoh Instalasi PHP & Extension

```bash
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mbstring php8.2-xml php8.2-mysql php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd
```

---

## 2. Konfigurasi Aplikasi

### 2.1 Clone Project

```bash
cd /var/www
git clone <repository-url> paps
cd paps
```

### 2.2 Install Dependency

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run prod
```

### 2.3 Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai production:

```dotenv
APP_NAME="PAPS | Platform Akreditasi Pelatihan Prakom & Statistisi"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://akreditasi.etc-nso.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paps
DB_USERNAME=paps_user
DB_PASSWORD=strong_password

SESSION_DRIVER=cookie
SESSION_DOMAIN=akreditasi.etc-nso.id
SANCTUM_STATEFUL_DOMAINS=akreditasi.etc-nso.id

# Integrasi
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://akreditasi.etc-nso.id/auth-callback

SSO_URL=https://sso.bps.go.id
CLIENT_SECRET=...
PAPS_REDIRECT_URI=https://akreditasi.etc-nso.id/callback-gojags
PAPS_UUID=...

JWT_SECRET=...

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
```

---

## 3. Setup Database

### 3.1 Buat Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE paps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'paps_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON paps.* TO 'paps_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3.2 Import SQL Dump

```bash
mysql -u paps_user -p paps < database/pusdiklat_akreditasi.sql
```

### 3.3 Jalankan Migration Default

```bash
php artisan migrate
```

---

## 4. Permission & Storage

```bash
# Set permission
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/paps

# Symlink storage public
php artisan storage:link
```

---

## 5. Cache & Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Konfigurasi Web Server

### 6.1 Nginx

```nginx
server {
    listen 80;
    server_name akreditasi.etc-nso.id;
    root /var/www/paps/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6.2 SSL (Let's Encrypt)

```bash
sudo certbot --nginx -d akreditasi.etc-nso.id
```

---

## 7. Setup Scheduler (Opsional)

Jika menggunakan fitur scheduled command seperti `profile:update-lock-status`:

```bash
crontab -e
```

Tambahkan:

```cron
* * * * * cd /var/www/paps && php artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Setup Queue (Jika Diperlukan)

Jika menggunakan queue driver selain `sync`:

```bash
php artisan queue:work
```

Atau gunakan Supervisor untuk menjaga agar queue worker tetap berjalan.

---

## 9. Checklist Sebelum Go-Live

- [ ] `.env` sudah diisi dengan kredensial production.
- [ ] `APP_DEBUG=false`.
- [ ] Database sudah di-import dan migration dijalankan.
- [ ] Storage folder writable.
- [ ] SSL aktif.
- [ ] Redirect URI Google/SSO sudah terdaftar dengan domain production.
- [ ] Mail server sudah dikonfigurasi.
- [ ] Backup schedule aktif.
- [ ] Log monitoring aktif.

---

## 10. Rollback Plan

Sebelum deploy:
1. Backup database.
2. Backup folder `storage/` dan `.env`.

Jika terjadi masalah:
1. Restore database dari backup.
2. Restore `.env` lama.
3. Clear cache: `php artisan config:clear && php artisan cache:clear && php artisan view:clear`.

---

## 11. Maintenance Mode

```bash
php artisan down
# lakukan maintenance
php artisan up
```

---

*Lihat juga: `SETUP.md`, `INTEGRATIONS.md`, `TROUBLESHOOTING.md`.*
