# Panduan Deploy PAPS ke VM Lama `akreditasi.etc-nso.id`

**Status:** Panduan operasional
**Target:** VM lama PAPS berbasis Apache + `htdocs`
**Sumber release:** GitHub `andiwahyu27/paps`, branch `main`
**Commit referensi saat panduan dibuat:** `96de98d2a823c8833f4aa7537bfd80c199cc33bf`

> Panduan ini tidak mengubah VM secara otomatis. Jalankan langkahnya melalui SSH setelah memastikan path aplikasi, user Apache/PHP-FPM, dan database yang benar.

---

## 1. Prinsip penting

1. Jangan menimpa `.env` VM lama.
2. Jangan menjalankan `php artisan key:generate` pada aplikasi lama. `APP_KEY` harus tetap karena session dan data terenkripsi bergantung padanya.
3. Jangan menjalankan `migrate:fresh`, `db:wipe`, atau import dump ke database production lama.
4. Backup database, `.env`, dan folder upload sebelum mengganti kode.
5. `DocumentRoot` Apache harus menunjuk ke folder `public/`, bukan root repository.
6. Sesuaikan user web server. Umumnya Debian/Ubuntu memakai `www-data`; sebagian VM lama dapat memakai `apache`.

---

## 2. Variabel yang harus diisi di VM lama

Setelah SSH ke VM lama, tentukan nilai berikut. Jangan menebak path.

```bash
export APP_DIR=/path/ke/htdocs/paps
export BACKUP_DIR=/var/backups/paps-$(date +%Y%m%d-%H%M%S)
```

Cari path aplikasi:

```bash
pwd
find /var/www /var/www/html /home -maxdepth 4 -type f -name artisan 2>/dev/null
```

Cari user Apache dan PHP-FPM:

```bash
ps aux | grep -E '[a]pache2|[h]ttpd|php-fpm'
```

Cek versi runtime:

```bash
php -v
composer --version
mysql --version
```

Jika `APP_DIR` atau database target belum jelas, **berhenti dahulu**. Jangan menjalankan migration.

---

## 3. Pemeriksaan DNS dan HTTPS

Dari VM atau komputer admin:

```bash
getent hosts akreditasi.etc-nso.id
curl -kI https://akreditasi.etc-nso.id
```

Pastikan domain mengarah ke VM lama yang memang akan menerima deployment. Jika DNS mengarah ke server lain, jangan mengubah Apache/Caddy di VM ini.

---

## 4. Backup sebelum deployment

Buat direktori backup:

```bash
sudo mkdir -p "$BACKUP_DIR"
sudo chown "$(id -un):$(id -gn)" "$BACKUP_DIR"
```

Backup konfigurasi:

```bash
cp "$APP_DIR/.env" "$BACKUP_DIR/.env"
```

Backup folder runtime dan upload. Nama folder upload harus disesuaikan dengan hasil pemeriksaan aktual:

```bash
rsync -a "$APP_DIR/storage/" "$BACKUP_DIR/storage/"
rsync -a "$APP_DIR/public/" "$BACKUP_DIR/public/"
```

Identifikasi database tanpa menampilkan password:

```bash
grep -E '^(DB_CONNECTION|DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME)=' "$APP_DIR/.env"
```

Backup database menggunakan credential yang sudah ada di VM. Password jangan ditulis di shell history atau dikirim melalui chat:

```bash
mysqldump --single-transaction --routines --triggers -u DB_USERNAME -p DB_DATABASE > "$BACKUP_DIR/database.sql"
```

Verifikasi backup tidak kosong:

```bash
ls -lh "$BACKUP_DIR/database.sql" "$BACKUP_DIR/.env"
```

---

## 5. Cek apakah VM lama sudah terhubung Git

```bash
cd "$APP_DIR"
git rev-parse --is-inside-work-tree 2>/dev/null || true
git remote -v 2>/dev/null || true
git status --short 2>/dev/null || true
```

### Skenario A — repository Git sudah tersedia

Simpan informasi commit saat ini:

```bash
git rev-parse HEAD > "$BACKUP_DIR/previous-commit.txt"
```

Jika working tree memiliki perubahan lokal, jangan memaksa pull. Simpan patch untuk audit:

```bash
git diff > "$BACKUP_DIR/local-changes.patch"
git status --short
```

Tambahkan remote hanya jika belum ada dan URL sudah dikonfirmasi:

```bash
git remote add origin https://github.com/andiwahyu27/paps.git
```

Ambil release terbaru:

```bash
git fetch origin main
git log --oneline --decorate -5 origin/main
```

Update tanpa merge paksa:

```bash
git pull --ff-only origin main
```

### Skenario B — folder lama bukan repository Git

Jangan menjalankan `git init` di folder production lama tanpa backup dan review. Gunakan folder staging di luar DocumentRoot:

```bash
export RELEASE_DIR=/var/www/paps-release
sudo git clone --branch main https://github.com/andiwahyu27/paps.git "$RELEASE_DIR"
cd "$RELEASE_DIR"
git rev-parse HEAD
```

Salin `.env` lama ke release staging:

```bash
sudo cp "$APP_DIR/.env" "$RELEASE_DIR/.env"
```

Salin persistent storage dan upload ke staging tanpa menghapus data:

```bash
sudo rsync -a "$APP_DIR/storage/" "$RELEASE_DIR/storage/"
sudo rsync -a "$APP_DIR/public/" "$RELEASE_DIR/public/"
```

Sebelum mengganti DocumentRoot, pastikan Apache virtual host, path upload, dan PHP-FPM sudah mengarah ke release yang benar. Untuk perubahan DocumentRoot, lakukan pada maintenance window dan simpan konfigurasi lama.

---

## 6. Dependency

Dari root aplikasi release yang akan aktif:

```bash
cd "$APP_DIR"
```

Cek apakah dependency berubah dibanding commit lama:

```bash
git diff HEAD@{1}..HEAD -- composer.json composer.lock 2>/dev/null || true
```

Jika `composer.lock` berubah atau folder `vendor/` belum tersedia:

```bash
composer install --no-dev --optimize-autoloader
```

Jangan menjalankan `key:generate`.

`npm install`/`npm run prod` hanya diperlukan jika asset frontend memang berubah dan build tool tersedia di VM. Untuk perubahan Blade/PHP pada fitur ini, fokus utama adalah Composer/vendor yang sesuai dan cache Laravel.

---

## 7. Migration database

Pastikan `.env` sudah menunjuk ke database PAPS VM lama yang benar:

```bash
php artisan config:clear
php artisan migrate:status
```

Jalankan migration secara aman:

```bash
php artisan migrate --force
```

Migration fitur yang harus tersedia antara lain:

- workflow metadata TTD Sidang;
- workflow Rekomendasi Hasil Akreditasi;
- tabel `tr_rekomendasi_hasil_akreditasi`;
- kolom status submit rekomendasi pada `tb_pengajuans`;
- fitur Rincian Penilaian tidak menambah tabel baru.

Verifikasi:

```bash
php artisan migrate:status
```

Jangan menjalankan:

```bash
php artisan migrate:fresh
php artisan db:wipe
```

---

## 8. Cache Laravel dan permission wajib

Setelah kode dan migration benar:

```bash
cd "$APP_DIR"
php artisan optimize
```

**Wajib langsung setelah `php artisan optimize`:**

```bash
sudo chown -R ubuntu:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

Jika user PHP-FPM di VM lama bukan `www-data`, ganti group `www-data` dengan group web server aktual setelah diverifikasi.

Perintah `find` dapat mengubah mode file tracked seperti `.gitignore`. Jika repository berada di working tree production, cek dan pulihkan hanya perubahan mode yang tidak disengaja:

```bash
git status --short
git diff --summary
```

`.env` jangan dibuat world-readable:

```bash
sudo chown ubuntu:www-data .env
sudo chmod 640 .env
```

Pastikan PHP-FPM dapat membaca `.env`, sedangkan `storage` dan `bootstrap/cache` dapat ditulis:

```bash
sudo -u www-data test -r .env
sudo -u www-data test -w storage/framework/views
sudo -u www-data test -w bootstrap/cache
```

---

## 9. Apache dan PHP-FPM

Cek virtual host aktif:

```bash
sudo apachectl -S
```

Konfigurasi minimal harus memiliki karakteristik berikut:

```apache
<VirtualHost *:80>
    ServerName akreditasi.etc-nso.id
    DocumentRoot /path/ke/htdocs/paps/public

    <Directory /path/ke/htdocs/paps/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/paps-error.log
    CustomLog ${APACHE_LOG_DIR}/paps-access.log combined
</VirtualHost>
```

Pastikan `mod_rewrite` aktif:

```bash
sudo a2enmod rewrite
sudo apachectl configtest
```

Cari socket PHP-FPM aktual:

```bash
ls -l /run/php/php*-fpm.sock
```

Jangan mengasumsikan socket `php8.2`/`php8.3`; gunakan versi yang benar-benar aktif di VM lama.

Reload/restart hanya setelah `configtest` lulus:

```bash
sudo systemctl reload apache2
sudo systemctl is-active apache2
```

Jika PHP-FPM direstart setelah deployment:

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl is-active php8.3-fpm
```

Sesuaikan nama service jika versi PHP berbeda.

---

## 10. Smoke test setelah deploy

### Aplikasi dan login

```bash
curl -kI https://akreditasi.etc-nso.id/login
```

Target normal: HTTP `200`.

### Halaman token TTD

Ambil token secara lokal dari database tanpa mencetaknya ke chat. Contoh hanya menyimpan token ke variabel shell:

```bash
token=$(php artisan tinker --execute='echo App\\Models\\Pengajuan::whereNotNull("ttd_sidang_token")->value("ttd_sidang_token");' 2>/dev/null | tail -n 1)
```

Uji halaman:

```bash
curl -k -sS -o /tmp/ttd-old-vm.html -w '%{http_code}\n' "https://akreditasi.etc-nso.id/ttd-sidang/$token"
grep -q 'Rincian Penilaian' /tmp/ttd-old-vm.html
```

Jangan menampilkan nilai `$token`.

### Export Rincian Penilaian

```bash
curl -k -sS -o /tmp/rincian-old-vm.docx -w '%{http_code}\n' "https://akreditasi.etc-nso.id/ttd-sidang/$token/rincian-penilaian/export-docx"
unzip -t /tmp/rincian-old-vm.docx
```

Validasi marker tanpa membuka credential/token:

```bash
python3 - <<'PY'
from zipfile import ZipFile
from xml.etree import ElementTree as ET
p='/tmp/rincian-old-vm.docx'
ns={'w':'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
with ZipFile(p) as z:
    root=ET.fromstring(z.read('word/document.xml'))
    text=''.join(t.text or '' for t in root.findall('.//w:t', ns))
    rows=root.findall('.//w:tbl', ns)[0].findall('./w:tr', ns)
    print('DOCX_XML=valid')
    print('PLACEHOLDERS=' + ('none' if '${' not in text else 'present'))
    print('ROWS=' + str(len(rows)))
PY
```

### Log error

```bash
sudo tail -n 100 /var/log/apache2/error.log
cd "$APP_DIR"
tail -n 100 storage/logs/laravel.log
```

Jika muncul `Permission denied` pada `storage/framework/views`, ulangi langkah permission pada bagian 8. Jangan hanya menjalankan `php artisan optimize` tanpa normalisasi permission.

---

## 11. Verifikasi fitur baru

Checklist minimal:

- [ ] Login normal.
- [ ] Halaman Final dapat dibuka.
- [ ] Tab `Rincian Penilaian` tampil.
- [ ] Nama lembaga dan program tampil uppercase.
- [ ] Rowspan nilai unsur tampil sekali per unsur.
- [ ] Item tunggal tidak menjadi row terpisah.
- [ ] Subunsur dengan lebih dari satu item menampilkan breakdown.
- [ ] Export DOCX berhasil.
- [ ] Placeholder DOCX tidak tersisa.
- [ ] Tab `Rekomendasi Hasil Akreditasi` tetap tampil.
- [ ] Status submit rekomendasi memakai kolom baru, bukan `rekomendasi_visitasi`.
- [ ] File upload historis tetap ada.

---

## 12. Rollback aman

Jika release baru bermasalah:

1. Jangan hapus database atau menjalankan `migrate:fresh`.
2. Catat error dan commit aktif:

   ```bash
   git rev-parse HEAD
   git status --short
   ```

3. Jika deployment menggunakan Git dan fast-forward, kembali ke commit sebelumnya hanya setelah review dan persetujuan eksplisit.
4. Pulihkan `.env` dari backup jika file tersebut berubah.
5. Pulihkan file upload/storage dari backup hanya jika benar-benar tertimpa.
6. Migration yang sudah berjalan tidak otomatis aman untuk dihapus; evaluasi migration dan backup database sebelum rollback schema.
7. Bersihkan cache dan ulangi permission:

   ```bash
   php artisan optimize:clear
   php artisan optimize
   sudo chown -R ubuntu:www-data storage bootstrap/cache
   sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
   sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
   ```

---

## 13. Informasi yang perlu dikirim jika menemui error

Kirim hanya informasi tersensor berikut:

```text
OS/version       = ...
PHP version      = ...
PHP-FPM service   = ...
Apache version    = ...
APP_DIR           = ...
DB_DATABASE       = [REDACTED]
DB_USERNAME       = [REDACTED]
HTTP status       = ...
Error log message = ...
```

Jangan kirim isi `.env`, password, token TTD, JWT secret, OAuth secret, atau connection string.
