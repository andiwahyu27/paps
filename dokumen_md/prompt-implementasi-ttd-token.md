# Prompt Implementasi URL E-TTD Berbasis Token

Anda adalah Senior Laravel 8 Developer dan Security Reviewer. Kerjakan perubahan langsung pada repository PAPS.

## Tujuan

Ubah URL halaman tanda tangan digital dari:

```text
/ttd/123
```

menjadi:

```text
/ttd/1b7d1f5d9f1f4e34b93b71d15cb5e8a9
```

Gunakan satu token publik acak per pengajuan. Link yang sama dibagikan oleh sekretariat kepada ketua asesor, anggota asesor, dan kepala instansi. Tidak perlu login, OTP, token per signer, atau perangkat berbeda.

Token harus:

- tidak mengekspos `pengajuan_id`;
- unik untuk setiap pengajuan;
- stabil sampai dirotasi;
- berbentuk hexadecimal lowercase;
- dibuat dengan sumber acak cryptographically secure;
- bukan `md5(id)`, `sha1(id)`, `base64_encode(id)`, atau enkripsi reversible dari ID.

Gunakan:

```php
bin2hex(random_bytes(20))
```

Hasilnya 40 karakter hexadecimal lowercase.

## Scope wajib diperiksa

```text
routes/web.php
routes/api.php
app/Http/Controllers/TtdController.php
app/Models/Pengajuan.php
app/Models/DigitalSignature.php
resources/views/ttd.blade.php
database/pusdiklat_akreditasi.sql
database/migrations/
tests/
```

Cari seluruh referensi terhadap:

```text
/ttd/{pengajuanId}
pengajuan_id
ttd.public
ttd.create
ttd.create.with.id
api/signatures
save-signature
reset-signature
download
```

Source code, route aktual, model, SQL dump, dan test adalah sumber kebenaran utama.

## Database

Tambahkan kolom berikut pada tabel pengajuan aktual:

```php
$table->string('ttd_token', 64)->nullable()->unique();
```

Buat migration Laravel baru dan update juga:

```text
database/pusdiklat_akreditasi.sql
```

Jangan mengubah data lain pada SQL dump.

## Generator token

Tambahkan method model:

```php
public static function generateUniqueTtdToken(): string
```

Method harus:

1. membuat token dengan `bin2hex(random_bytes(20))`;
2. memeriksa collision pada database;
3. mengulang sampai token unik ditemukan.

Pengajuan baru harus otomatis mendapat token melalui model event `creating` atau lokasi pembuatan pengajuan yang paling minimal dan aman.

Jangan membuat token baru setiap kali halaman dibuka.

## Backfill

Buat command aman:

```bash
php artisan paps:backfill-ttd-token
```

Command harus:

- hanya memproses record dengan `ttd_token IS NULL`;
- menggunakan chunk;
- tidak mengganti token lama;
- aman dijalankan ulang;
- tidak mencetak token atau data sensitif;
- melaporkan jumlah record yang berhasil diisi.

## Route publik

Ubah route menjadi:

```php
Route::get('/ttd/{token}', [TtdController::class, 'show'])
    ->where('token', '[a-f0-9]{40}')
    ->name('ttd.show');
```

Nama parameter harus `token`, bukan `id`, `uuid`, atau `pengajuanId`.

Pastikan route statis ditempatkan sebelum route dinamis agar tidak konflik dengan:

```text
GET /ttd
POST /ttd
GET /ttd/signatures
POST /ttd/download
```

URL lama seperti `/ttd/123` tidak boleh membuka pengajuan dan harus menghasilkan `404`.

## Controller halaman

Cari pengajuan hanya berdasarkan token:

```php
$pengajuan = Pengajuan::where('ttd_token', $token)->firstOrFail();
```

Jangan menerjemahkan token kembali menjadi ID dan jangan mencari berdasarkan kolom `id`.

## Blade

Perbarui:

```text
resources/views/ttd.blade.php
```

Semua request JavaScript harus menggunakan token, bukan `pengajuan_id`.

Contoh:

```php
<script>
    const ttdToken = @json($pengajuan->ttd_token);
</script>
```

Hapus penggunaan seperti:

```javascript
/ttd/${pengajuanId}
/api/signatures?pengajuan_id=${pengajuanId}
```

Gunakan:

```javascript
/ttd/${ttdToken}
/api/ttd/${ttdToken}/signatures
```

Link share harus menghasilkan:

```text
https://domain-aplikasi/ttd/1b7d1f5d9f1f4e34b93b71d15cb5e8a9
```

Pertahankan desain Blade yang ada semaksimal mungkin.

## Simpan tanda tangan

Endpoint save harus menerima token dan mencari pengajuan berdasarkan token.

Request frontend boleh mengirim:

```json
{
  "token": "1b7d1f5d9f1f4e34b93b71d15cb5e8a9",
  "signer_type": "asesor1",
  "signature_data": "data:image/png;base64,..."
}
```

Backend harus:

1. mencari pengajuan berdasarkan `ttd_token`;
2. mengambil `pengajuan_id` dari hasil query server;
3. mengabaikan atau menolak `pengajuan_id` dari browser;
4. tidak mempercayai `signer_name` dan `signer_title`;
5. mengambil nama dan jabatan signer dari database;
6. memvalidasi `signer_type` dengan whitelist;
7. memvalidasi ukuran, decode base64, MIME, dan format gambar;
8. membatasi format ke PNG bila itu format yang digunakan;
9. menggunakan nama file acak;
10. tidak mencatat base64 signature ke log.

Whitelist:

```php
[
    'asesor1',
    'asesor2',
    'asesor3',
    'kepala',
]
```

Satu link harus tetap dapat digunakan untuk menandatangani semua posisi tersebut.

## Larangan overwrite

Jika signature untuk kombinasi berikut sudah ada:

```text
pengajuan_id + signer_type
```

endpoint save harus menolak overwrite di backend dengan `409 Conflict` atau `422 Unprocessable Entity`.

Pesan:

```text
Tanda tangan untuk posisi ini sudah tersimpan.
```

Jangan hanya mengunci kotak pada frontend.

## Reset tanda tangan

Reset hanya boleh dilakukan oleh Sekretariat dengan role `2`.

Gunakan middleware:

```text
auth
is.sekretariat
```

Reset publik melalui token yang sama dilarang.

Setelah reset, posisi tersebut boleh ditandatangani kembali menggunakan link yang sama.

Catat minimal:

- pengajuan;
- signer type;
- user sekretariat;
- waktu reset.

Jangan log gambar tanda tangan atau data sensitif.

## Daftar signature

Ubah endpoint menjadi token-based, misalnya:

```text
GET /api/ttd/{token}/signatures
```

Endpoint harus:

- mencari pengajuan berdasarkan token;
- tidak menerima `pengajuan_id`;
- mengembalikan data minimum yang dibutuhkan UI;
- tidak mengembalikan filesystem path;
- tidak mengekspos data sensitif.

Contoh respons:

```json
{
  "signatures": {
    "asesor1": {
      "signed": true,
      "name": "Nama dari database",
      "signed_at": "2026-08-05T10:00:00+07:00"
    },
    "asesor2": {
      "signed": false
    },
    "asesor3": {
      "signed": false
    },
    "kepala": {
      "signed": true,
      "name": "Nama dari database",
      "signed_at": "2026-08-05T10:05:00+07:00"
    }
  }
}
```

Jika gambar tanda tangan perlu ditampilkan, sajikan melalui controller yang memvalidasi token. Jangan expose path file langsung.

## Download dokumen

Periksa endpoint download. Jika masih menerima `pengajuan_id`, ubah agar menggunakan token.

Controller harus mencari pengajuan berdasarkan token dan memastikan dokumen yang diunduh berasal dari pengajuan yang sama.

## Rotasi link

Tambahkan endpoint Sekretariat:

```text
POST /pengajuan/{id}/ttd-token/rotate
```

Middleware:

```text
auth
is.sekretariat
```

Rotasi harus:

1. menghasilkan token baru;
2. menyimpan token baru;
3. membuat link lama tidak valid;
4. tidak menghapus signature yang sudah tersimpan;
5. mencatat aksi ke log.

## Validasi keamanan minimum

Terapkan:

- CSRF untuk POST web;
- rate limit pada endpoint publik save signature;
- validasi panjang token;
- validasi ukuran payload signature;
- validasi base64;
- validasi MIME sebenarnya;
- nama file acak;
- pencegahan path traversal;
- error generik tanpa stack trace;
- tidak membaca atau menampilkan `.env`;
- tidak menampilkan secret, password, token production, cookie, atau data pribadi.

Gunakan throttle yang sesuai Laravel 8, misalnya:

```php
throttle:20,1
```

## Kompatibilitas workflow

Jangan mengubah ketentuan berikut:

- hanya ada satu link untuk satu pengajuan;
- link yang sama dibagikan kepada semua signer;
- semua posisi tanda tangan tampil pada halaman yang sama;
- tidak perlu login;
- tidak perlu OTP;
- tidak perlu perangkat berbeda;
- signer boleh menandatangani dari perangkat mana pun;
- sekretariat tetap membagikan satu URL;
- desain Blade dipertahankan semaksimal mungkin;
- hindari refactor besar di luar scope.

## Test wajib

Tambahkan Feature Test minimal untuk:

### Token route

1. Token valid membuka pengajuan yang benar.
2. Token tidak dikenal menghasilkan `404`.
3. `/ttd/123` tidak membuka pengajuan.
4. Token dengan format tidak valid menghasilkan `404`.
5. Token pengajuan A tidak membuka pengajuan B.

### Token generation

6. Pengajuan baru otomatis mendapat token.
7. Token memiliki format `[a-f0-9]{40}`.
8. Token unik antar-pengajuan.
9. Backfill hanya mengisi token kosong.
10. Backfill tidak mengubah token lama.

### Save signature

11. Signature dapat disimpan dengan token valid.
12. Request tanpa token ditolak.
13. Token invalid ditolak.
14. `pengajuan_id` palsu dari browser tidak digunakan.
15. `signer_type` di luar whitelist ditolak.
16. `signer_name` dari browser tidak digunakan.
17. Signature kedua untuk signer yang sama ditolak.
18. Signer lain pada pengajuan yang sama tetap dapat menandatangani.
19. Payload terlalu besar ditolak.
20. File non-PNG ditolak.

### Daftar signature

21. Daftar signature menggunakan token.
22. Query dengan `pengajuan_id` tidak membuka data.
23. Token pengajuan A tidak membaca signature pengajuan B.
24. Respons tidak mengandung filesystem path.

### Reset dan rotasi

25. Public tidak dapat reset.
26. Asesor tidak dapat reset.
27. Lembaga tidak dapat reset.
28. Sekretariat dapat reset.
29. Setelah reset, signer dapat menandatangani kembali.
30. Sekretariat dapat merotasi token.
31. Token lama tidak berlaku setelah rotasi.
32. Token baru tetap mengakses signature pengajuan yang sama.

Ingat bahwa schema utama PAPS berasal dari:

```text
database/pusdiklat_akreditasi.sql
```

Jangan berasumsi semua tabel tersedia melalui migration default.

## Output agent

Lakukan perubahan kode langsung, bukan hanya memberi rekomendasi.

Setelah selesai, tampilkan:

1. Findings awal.
2. Daftar file yang diubah.
3. Ringkasan implementasi.
4. Migration yang dibuat.
5. Command backfill.
6. Route lama dan route baru.
7. Test yang ditambahkan.
8. Command yang dijalankan.
9. Hasil test.
10. Risiko residual.
11. Langkah deployment.

Langkah deployment minimal:

```bash
php artisan migrate
php artisan paps:backfill-ttd-token
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan test
```

Jangan menjalankan command destruktif.

## Acceptance criteria

Implementasi selesai jika:

- URL publik berbentuk `/ttd/{token}`;
- token hexadecimal lowercase acak;
- URL tidak mengekspos ID pengajuan;
- `/ttd/123` menghasilkan `404`;
- satu token dipakai bersama seluruh signer;
- tidak ada kewajiban login atau OTP;
- semua signer dapat menandatangani dari perangkat mana pun;
- backend mencari pengajuan berdasarkan token;
- browser tidak menentukan `pengajuan_id`;
- nama signer berasal dari database;
- signature tidak dapat ditimpa tanpa reset Sekretariat;
- link dapat dirotasi;
- data lama dapat dibackfill;
- migration dan SQL dump konsisten;
- test utama lulus.
