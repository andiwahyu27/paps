# INTEGRATIONS.md

## Dokumentasi Integrasi Eksternal PAPS

Dokumen ini menjelaskan integrasi aplikasi PAPS dengan layanan pihak ketiga.

---

## 1. Google OAuth

### Tujuan
Memungkinkan user login menggunakan akun Google.

### Package
`laravel/socialite`

### Route
- `/auth-redirect` — redirect ke Google.
- `/auth-callback` — callback dari Google.

### Konfigurasi `.env`

```dotenv
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=https://akreditasi.etc-nso.id/auth-callback
GOOGLE_ENDPOINT_TOKEN=https://oauth2.googleapis.com/token
GOOGLE_ENDPOINT_USERINFO=https://www.googleapis.com/oauth2/v3/userinfo
GOOGLE_SCOPE='https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email'
```

### Setup di Google Cloud Console
1. Buat project di Google Cloud Console.
2. Buat OAuth 2.0 Credentials.
3. Tambahkan authorized redirect URI: `https://akreditasi.etc-nso.id/auth-callback`.
4. Isi `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` di `.env`.

### Catatan
- Redirect URI harus sama persis dengan yang didaftarkan.
- Untuk lokal, daftarkan juga `http://localhost:8000/auth-callback`.

---

## 2. SSO BPS (GOJAGS)

### Tujuan
Login menggunakan akun pegawai BPS melalui SSO internal.

### Teknologi
Keycloak / OpenID Connect.

### Route
- `/redirect-gojags/{type}` — redirect ke SSO.
- `/callback-gojags` — callback dari SSO.

### Konfigurasi `.env`

```dotenv
SSO_URL=https://sso.bps.go.id
SSO_REALM=pegawai-bps
SSO_SCOPE="openid profile-pegawai"
SSO_ID=your_client_id
CLIENT_URL=https://gojags-training.bps.go.id
CLIENT_SECRET=your_client_secret
APP_GOJAGS_URL=https://gojags-training.bps.go.id
PAPS_REDIRECT_URI=https://akreditasi.etc-nso.id/callback-gojags
PAPS_UUID=your_uuid
JWT_SECRET=your_jwt_secret
```

### Alur
1. User klik login GOJAGS.
2. Aplikasi redirect ke `SSO_URL` dengan parameter client_id, redirect_uri, scope, dll.
3. User login di SSO BPS.
4. SSO redirect ke `PAPS_REDIRECT_URI` dengan authorization code.
5. Aplikasi tukar code dengan access token.
6. Aplikasi ambil informasi user dari token.
7. Jika user belum ada, buat user baru di tabel `users`.

### Catatan
- Pastikan `CLIENT_SECRET` dan `PAPS_UUID` tidak bocor.
- JWT secret digunakan untuk validasi token dari client.

---

## 3. Fonnte (WhatsApp Gateway)

### Tujuan
Mengirim notifikasi WhatsApp ke user terkait status pengajuan.

### Implementasi
`app/Helpers/Notification.php`

### Endpoint
```
POST https://api.fonnte.com/send
```

### Parameter
- `target` — nomor tujuan (format internasional).
- `message` — isi pesan.
- `countryCode` — kode negara (default 62).

### Konfigurasi
Token API disimpan di header Authorization:

```php
CURLOPT_HTTPHEADER => array(
    'Authorization: YOUR_FONNTE_TOKEN'
),
```

### Catatan
- Pastikan token valid dan masih aktif.
- Format nomor harus benar, contoh: `6281234567890`.
- Penggunaan token di source code perlu dipindahkan ke `.env` untuk keamanan.

---

## 4. Mail/SMTP

### Tujuan
Mengirim email notifikasi, reset password, dll.

### Konfigurasi `.env`

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@akreditasi.etc-nso.id
MAIL_FROM_NAME="${APP_NAME}"
```

### Catatan
- Saat ini konfigurasi default mengarah ke Mailtrap (testing).
- Untuk production, ganti dengan SMTP provider sesuai kebutuhan.

---

## 5. Generate PDF

### Tujuan
Membuat dokumen PDF seperti berita acara, rekomendasi, sertifikat.

### Package
`barryvdh/laravel-dompdf`

### Contoh Penggunaan

```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('pdf.sertifikat', $data);
return $pdf->download('sertifikat.pdf');
```

---

## 6. Generate Word

### Tujuan
Membuat dokumen Word (.docx) untuk template yang bisa diedit.

### Package
`phpoffice/phpword`

### Contoh Penggunaan

```php
use PhpOffice\PhpWord\PhpWord;

$phpWord = new PhpWord();
$section = $phpWord->addSection();
$section->addText('Berita Acara');
$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('berita_acara.docx');
```

---

## 7. E-TTD (Digital Signature)

### Tujuan
Menyimpan dan mengelola tanda tangan digital.

### Route Publik
- `/ttd`
- `/ttd/{pengajuanId}`
- `/ettd/save-signature`
- `/ttd/signatures`
- `/ttd/download`

### Implementasi
Controller: `TtdController`
Tabel: `tr_digital_signatures`

### Catatan
- Halaman ini dapat diakses publik, sehingga perlu validasi token/ID.
- Data tanda tangan disimpan di database.

---

## 8. Clockwork

### Tujuan
Debugging dan profiling request di development.

### Package
`itsgoingd/clockwork`

### Akses
Clockwork toolbar tersedia di browser jika `APP_DEBUG=true`.

### Catatan
- Nonaktifkan di production untuk alasan keamanan dan performa.

---

## 9. Checklist Integrasi

Sebelum deploy ke production:

- [ ] Google OAuth Client ID dan Secret valid.
- [ ] Redirect URI Google sudah terdaftar untuk domain production.
- [ ] SSO GOJAGS client secret dan UUID valid.
- [ ] Redirect URI GOJAGS sudah terdaftar.
- [ ] Token Fonnte valid.
- [ ] SMTP server production sudah dikonfigurasi.
- [ ] dompdf dan phpword dapat berjalan di server production.

---

*Lihat juga: `AUTHENTICATION.md`, `DEPLOYMENT.md`, `SETUP.md`.*
