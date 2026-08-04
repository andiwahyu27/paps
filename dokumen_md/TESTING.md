# TESTING.md

## Panduan Testing PAPS

Dokumen ini menjelaskan strategi, perintah, dan hal-hal yang perlu diperhatikan saat melakukan testing pada project PAPS.

---

## 1. Jenis Testing

### 1.1 Unit Testing
Menggunakan PHPUnit. Test case berada di folder `tests/`.

### 1.2 Manual Testing
Dilakukan via browser atau tools seperti Postman/curl untuk endpoint web dan API.

### 1.3 Integration Testing
Menguji alur end-to-end, misalnya: login → ajukan pengajuan → verifikasi → assign asesor → penilaian → generate sertifikat.

---

## 2. Struktur Test

```
tests/
├── Feature/          # Integration/feature tests
├── Unit/             # Unit tests
└── TestCase.php      # Base test case
```

---

## 3. Menjalankan Test

```bash
# Jalankan semua test
php artisan test

# Jalankan dengan PHPUnit
vendor/bin/phpunit

# Jalankan test tertentu
php artisan test --filter NamaTest

# Jalankan dengan coverage (memerlukan Xdebug/PCOV)
php artisan test --coverage
```

---

## 4. Test Environment

### Database Testing

Disarankan menggunakan database terpisah untuk testing, bukan database `paps` yang berisi data production.

Contoh konfigurasi `.env.testing`:

```dotenv
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paps_testing
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Membuat Test Database

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS paps_testing;"
mysql -u root -p paps_testing < database/pusdiklat_akreditasi.sql
```

---

## 5. Test Case yang Disarankan

### 5.1 Autentikasi
- Login dengan email/password valid.
- Login dengan email/password invalid.
- Login dengan role yang salah mencoba akses route lain.
- Login sebagai user lain oleh Sekretariat.

### 5.2 Modul Lembaga
- Lembaga dapat mengisi profil.
- Lembaga dapat mengajukan akreditasi.
- Lembaga dapat mengunggah dokumen program.
- Lembaga tidak dapat mengedit profil setelah lock.

### 5.3 Modul Sekretariat
- Sekretariat dapat verifikasi pengajuan.
- Sekretariat dapat assign asesor.
- Sekretariat dapat generate berita acara.

### 5.4 Modul Asesor
- Asesor dapat melihat pengajuan yang ditugaskan.
- Asesor dapat melakukan penilaian per tahap.
- Asesor dapat mengunggah rekomendasi/sertifikat.

### 5.5 E-TTD
- Public dapat mengakses halaman `/ttd`.
- Signature dapat disimpan dan didownload.

### 5.6 Upload
- Upload file PDF berhasil.
- Upload file non-PDF ditolak.
- File dengan nama duplikat tidak saling menimpa.

---

## 6. Contoh Test Login Manual dengan curl

```bash
# 1. Ambil CSRF token
curl -s -c cookies.txt -b cookies.txt http://127.0.0.1:8000/login > login.html
TOKEN=$(grep -o 'meta name="csrf-token" content="[^"]*"' login.html | sed 's/.*content="\([^"]*\)".*/\1/')

# 2. POST login
curl -c cookies.txt -b cookies.txt -X POST http://127.0.0.1:8000/login \
  -d "_token=$TOKEN" \
  -d "email=email@domain.com" \
  -d "password=password" \
  -L

# 3. Akses halaman home
curl -c cookies.txt -b cookies.txt http://127.0.0.1:8000/home
```

---

## 7. Test Case PHPUnit Contoh

### Contoh Feature Test Login

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role' => 4,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
}
```

---

## 8. Hal yang Perlu Diperhatikan

- **Database SQL dump**: Karena skema lengkap dari SQL dump, testing migration-only tidak mencukupi. Pastikan test database di-import dari SQL dump.
- **Integrasi eksternal**: Google OAuth, SSO GOJAGS, dan Fonnte tidak bisa di-test otomatis tanpa mock atau kredensial valid.
- **File upload**: Pastikan folder tujuan upload tersedia dan writable saat testing.
- **Session domain**: Saat testing via curl/browser lokal, pastikan `SESSION_DOMAIN` dan `APP_URL` sesuai.

---

## 9. Coverage Target (Usulan)

| Modul | Target Coverage |
|-------|-----------------|
| Autentikasi | 80% |
| Profil Lembaga | 70% |
| Pengajuan | 70% |
| Penilaian Asesor | 60% |
| Sekretariat | 60% |
| Upload | 70% |

---

*Lihat juga: `SETUP.md`, `API.md`, `TROUBLESHOOTING.md`.*
