# AUTHENTICATION.md

## Dokumentasi Autentikasi & Otorisasi PAPS

PAPS memiliki 3 mekanisme login dan otorisasi berbasis role sederhana.

---

## 1. Mekanisme Login

### 1.1 Email & Password

Menggunakan Laravel default authentication (`Auth::routes()`). User memiliki kolom:

- `email`
- `password` (hash bcrypt)
- `role` (integer)
- `id_profile` (opsional, untuk lembaga)

Route:

```
GET  /login     -> LoginController@showLoginForm
POST /login     -> LoginController@login
POST /logout    -> LoginController@logout
```

Setelah login sukses, user diarahkan ke `/home` (didefinisikan di `RouteServiceProvider::HOME`).

### 1.2 Google OAuth

Route:

```
GET /auth-redirect   -> LoginController@redirectToProvider
GET /auth-callback   -> LoginController@handleProviderCallback
```

Menggunakan Laravel Socialite dengan provider Google.

Variabel `.env` yang diperlukan:

```dotenv
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=http://localhost:8000/auth-callback
GOOGLE_ENDPOINT_TOKEN=https://oauth2.googleapis.com/token
GOOGLE_ENDPOINT_USERINFO=https://www.googleapis.com/oauth2/v3/userinfo
GOOGLE_SCOPE='https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email'
```

### 1.3 SSO BPS (GOJAGS)

Route:

```
GET /redirect-gojags/{type} -> LoginController@redirectToGojags
GET /callback-gojags        -> LoginController@authenticateWithGojags
GET /login-error            -> LoginController@loginError
```

SSO GOJAGS menggunakan Keycloak/OpenID Connect. Setelah login SSO, sistem memvalidasi token dan membuatkan atau mengupdate user lokal.

Variabel `.env` yang diperlukan:

```dotenv
SSO_URL=https://sso.bps.go.id
SSO_REALM=pegawai-bps
SSO_SCOPE="openid profile-pegawai"
SSO_ID=...
CLIENT_URL=https://gojags-training.bps.go.id
CLIENT_SECRET=...
APP_GOJAGS_URL=https://gojags-training.bps.go.id
PAPS_REDIRECT_URI=http://localhost:8000/callback-gojags
PAPS_UUID=...
JWT_SECRET=...
```

---

## 2. Role-Based Access Control (RBAC)

Role disimpan di kolom `role` pada tabel `users`.

| Role | Nilai | Middleware |
|------|-------|------------|
| Sekretariat | 2 | `is.sekretariat` |
| Asesor | 3 | `is.asesor` |
| Lembaga | 4 | `is.lembaga` |
| Asesor/Sekretariat | 3/2 | `is.asesor.or.sekretariat` |

### Implementasi Middleware

Middleware memeriksa `Auth::user()->role` dan mengembalikan `abort(503)` jika tidak sesuai.

```php
// Contoh: IsLembagaMiddleware
if (Auth::user()->role == 4) {
    return $next($request);
} else {
    return abort(503, 'Anda tidak memiliki hak akses');
}
```

### Cara Menambahkan Route dengan Middleware

```php
Route::group(['middleware' => 'is.sekretariat'], function () {
    Route::get('/pengguna', [SekretariatController::class, 'pengguna']);
});
```

---

## 3. Session & Cookie

Session menggunakan driver `cookie`. Konfigurasi penting di `.env`:

```dotenv
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_DOMAIN=akreditasi.etc-nso.id
SANCTUM_STATEFUL_DOMAINS=akreditasi.etc-nso.id
```

### Catatan untuk Testing Lokal

Jika testing di `localhost` atau `127.0.0.1`, ubah `SESSION_DOMAIN` dan `SANCTUM_STATEFUL_DOMAINS` sesuai domain lokal:

```dotenv
APP_URL=http://127.0.0.1:8000
SESSION_DOMAIN=127.0.0.1
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000
```

Tanpa konfigurasi ini, browser/curl tidak akan mengirimkan cookie kembali ke server, menyebabkan error **419 CSRF Token Mismatch**.

---

## 4. JWT untuk Client

Aplikasi menggunakan `firebase/php-jwt` untuk memvalidasi token dari client eksternal.

```php
use App\Helpers\JwtHelper;

$decoded = JwtHelper::validateToken($token);
```

Secret JWT diatur di `.env`:

```dotenv
JWT_SECRET=...
```

---

## 5. Login sebagai User Lain

Sekretariat dapat login sebagai user lain tanpa mengetahui password:

```
GET /pengguna/login/{id}
```

Handler: `SekretariatController@loginPengguna`

Fitur ini berguna untuk troubleshooting akun lembaga/asesor.

---

## 6. Keamanan

- Password selalu di-hash dengan bcrypt.
- Session cookie dienkripsi.
- CSRF token wajib ada di semua form POST.
- Middleware melindungi route berdasarkan role.
- Kredensial OAuth dan SSO disimpan di `.env`.

---

## 7. Alur Login Standar

```
User akses /login
    │
    ▼
Login via email/Google/SSO
    │
    ▼
Autentikasi berhasil
    │
    ▼
Redirect ke /home
    │
    ▼
Middleware memeriksa role untuk setiap request selanjutnya
```

---

*Lihat juga: `SETUP.md`, `API.md`, `ARSITEKTUR.md`.*
