# Dokumentasi API PAPS (Pelatihan Akreditasi)

Dokumentasi lengkap untuk endpoint API integrasi PAPS dengan sistem GoJAGS.

> [!CAUTION]
> **PERHATIAN KEAMANAN**: Semua request ke API ini **HARUS** dilakukan dari **sisi backend/server**. Jangan pernah memanggil API ini dari frontend (browser/client-side) karena akan mengekspos kredensial autentikasi Anda.
>
> **Catatan repo**: Client Secret pada dokumen ini sengaja di-mask. Nilai asli hanya disimpan di file `.env` server produksi dan tidak boleh di-commit ke repository.

---

## Status Integrasi di Aplikasi PAPS

| Komponen | Status |
|----------|--------|
| `app/Services/PapsApiService.php` | Tersedia (dasar: `load`, `store`, `update`, `delete`) |
| `config/services.php` → `services.paps` | Tersedia (`base_url`, `client_id`, `client_secret`, `timeout`) |
| `.env` / `.env.example` | Tersedia (`PAPS_BASE_URL`, `PAPS_CLIENT_ID`, `PAPS_CLIENT_SECRET`, `PAPS_TIMEOUT`) |
| Pemanggilan dari controller / UI | **Belum** — menunggu keputusan alur bisnis |

### Kesenjangan yang harus diselesaikan sebelum integrasi diaktifkan

1. **Belum ada `uuid_akreditasi` di database PAPS.** Saat ini tabel `tb_pengajuans`
   tidak memiliki kolom UUID akreditasi; satu-satunya kolom `uuid` di database
   adalah milik tabel `failed_jobs`. Perlu kolom baru (mis. `tb_profile_lembagas.uuid_akreditasi`
   atau `tb_pengajuans.uuid_akreditasi`) beserta mekanisme pengisiannya.
2. **Belum ada kolom `uuid_diklat`** untuk menyimpan UUID kelas balikan GoJAGS
   (hasil `store` / `load`) agar `update` dan `delete` bisa menyasar kelas yang benar.
   Tanpa ini, integrasi hanya bisa `store` (rawan duplikat) dan tidak bisa
   memperbarui/menghapus.
3. **Data pelatihan di PAPS belum selengkap payload GoJAGS.** Tabel `tb_pelatihans`
   hanya punya `nama`, `angkatan`, `tahun`, `id_pengajuan`. API mewajibkan
   `tgl_mulai`, `tgl_selesai`, `jumlah_jp`, `id_jenis_diklat`, `tipe_diklat`,
   `nip_pic`, `instansi_penyelenggara` (dan `tgl_mulai_klasikal`/`tgl_selesai_klasikal`
   untuk `luring`/`hybrid`). Perlu penambahan kolom + penyesuaian form Sekretariat
   (`tambahPelatihan` / `ubahPelatihan`).
4. **`nip_pic` harus terdaftar di GoJAGS.** Perlu sumber data NIP PIC yang tervalidasi.

Rekomendasi alur: sinkronisasi dipicu dari aksi Sekretariat (tambah/ubah/hapus pelatihan),
di-*queue* (bukan inline), dengan penyimpanan `uuid_diklat` hasil `store` — lihat
bagian "Catatan Penting" di bawah.

---

## Autentikasi

Semua endpoint memerlukan **Bearer Token** yang dikirim melalui header `Authorization`.

### Format Token

Bearer token merupakan kombinasi dari **Client ID** dan **Client Secret** yang di-encode menggunakan **Base64** dengan pemisah `:` (titik dua).

```
Format: Base64(client_id:client_secret)
```

### Kredensial

| Parameter | Nilai |
|-----------|-------|
| Client ID | `90ac84eb-d0b3-44f6-87af-69490ff30ae4` |
| Client Secret | `***` (lihat `.env` server, variabel `PAPS_CLIENT_SECRET`) |

### Cara Generate Token

```php
$client_id = config('services.paps.client_id');
$client_secret = config('services.paps.client_secret');
$token = base64_encode($client_id . ':' . $client_secret);
```

> Nilai asli kredensial tidak ditulis di dokumen ini. `PapsApiService` membangun
> token secara otomatis dari konfigurasi tersebut.

---

## Base URL

```
https://gojags.web.bps.go.id/server/api/v1/paps
```

---

## Referensi Nilai

### Jenis Diklat (`id_jenis_diklat`)

| ID | Keterangan |
|----|------------|
| `20` | Fungsional Terampil |
| `21` | Fungsional Ahli |

### Tipe Diklat (`tipe_diklat`)

| Nilai | Keterangan |
|-------|------------|
| `luring` | Pelatihan tatap muka/klasikal |
| `daring` | Pelatihan online |
| `hybrid` | Blended learning (kombinasi daring & luring) |
| `unspecified` | Belum ditentukan |

---

## 1. Load Pelatihan

Mengambil daftar pelatihan/kelas berdasarkan UUID akreditasi.

### Endpoint

```
GET /server/api/v1/paps/load/{uuid_akreditasi}
```

### Parameter URL

| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `uuid_akreditasi` | string | Ya | UUID unik dari nomor akreditasi di PAPS |

### Contoh Request (Laravel)

```php
<?php

use Illuminate\Support\Facades\Http;

// Generate token
$token = base64_encode(
    config('services.paps.client_id') . ':' . config('services.paps.client_secret')
);

$uuid_akreditasi = 'abc123-def456-ghi789';

// Kirim request
$response = Http::withToken($token)
    ->get("https://gojags.web.bps.go.id/server/api/v1/paps/load/{$uuid_akreditasi}");

if ($response->successful()) {
    $data = $response->json();
    // Proses data...
} else {
    // Handle error
    $error = $response->json();
}
```

### Contoh Response (Sukses)

```json
{
    "status": "Berhasil mengambil data",
    "data": [
        {
            "uuid": "d1e2f3g4-h5i6-j7k8-l9m0-n1o2p3q4r5s6",
            "nama_diklat": "Pelatihan Fungsional Statistisi Ahli",
            "kelas": "Angkatan I Tahun 2025",
            "tgl_mulai": "2025-02-01",
            "tgl_selesai": "2025-02-14",
            "jumlah_jp": 80,
            "tgl_mulai_klasikal": "2025-02-10",
            "tgl_selesai_klasikal": "2025-02-14",
            "id_jenis_diklat": 21,
            "tipe_diklat": "hybrid",
            "nip_pic": "199001012020011001",
            "nama_pic": "Ahmad Budiman",
            "link_kelas": "https://gojags.web.bps.go.id/management/pelatihan?diklat=d1e2f3g4-h5i6-j7k8-l9m0-n1o2p3q4r5s6"
        }
    ]
}
```

### Contoh Response (Data Kosong)

```json
{
    "status": "Berhasil mengambil data",
    "data": []
}
```

### Keterangan Response

| Field | Keterangan |
|-------|------------|
| `uuid` | UUID unik kelas pelatihan di GoJAGS |
| `nama_diklat` | Nama pelatihan |
| `kelas` | Nama kelas/angkatan |
| `tgl_mulai` | Tanggal mulai pelatihan (format: YYYY-MM-DD) |
| `tgl_selesai` | Tanggal selesai pelatihan (format: YYYY-MM-DD) |
| `jumlah_jp` | Jumlah Jam Pelajaran keseluruhan |
| `tgl_mulai_klasikal` | Tanggal mulai sesi tatap muka (nullable) |
| `tgl_selesai_klasikal` | Tanggal selesai sesi tatap muka (nullable) |
| `id_jenis_diklat` | ID jenis diklat (20 atau 21) |
| `tipe_diklat` | Tipe pembelajaran |
| `nip_pic` | NIP PIC kelas |
| `nama_pic` | Nama PIC kelas |
| `link_kelas` | URL untuk mengakses kelas di GoJAGS |

---

## 2. Store Pelatihan

Membuat kelas pelatihan baru. Jika nomor akreditasi (UUID akreditasi) belum ada, maka akan otomatis dibuat group course baru.

### Endpoint

```
POST /server/api/v1/paps/store
```

### Parameter Request

| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `uuid_akreditasi` | string | Ya | UUID unik dari nomor akreditasi di PAPS |
| `nama_diklat` | string | Ya | Nama pelatihan |
| `kelas` | string | Ya | Nama kelas/angkatan (contoh: "Angkatan I Tahun 2025") |
| `tgl_mulai` | date | Ya | Tanggal mulai pelatihan (format: YYYY-MM-DD) |
| `tgl_selesai` | date | Ya | Tanggal selesai pelatihan (format: YYYY-MM-DD) |
| `jumlah_jp` | integer | Ya | Jumlah Jam Pelajaran |
| `tgl_mulai_klasikal` | date | Tidak | Tanggal mulai tatap muka (wajib jika tipe hybrid/luring) |
| `tgl_selesai_klasikal` | date | Tidak | Tanggal selesai tatap muka (wajib jika tipe hybrid/luring) |
| `id_jenis_diklat` | integer | Ya | ID jenis diklat: `20` (Terampil) atau `21` (Ahli) |
| `tipe_diklat` | string | Ya | Tipe pembelajaran: `hybrid`, `luring`, `daring`, `unspecified` |
| `nip_pic` | string | Ya | NIP PIC kelas (18 digit, harus sudah terdaftar di GoJAGS) |
| `instansi_penyelenggara` | string | Ya | Nama instansi penyelenggara |

### Contoh Request (Laravel)

```php
<?php

use Illuminate\Support\Facades\Http;

// Generate token
$token = base64_encode(
    config('services.paps.client_id') . ':' . config('services.paps.client_secret')
);

// Kirim request
$response = Http::withToken($token)
    ->post('https://gojags.web.bps.go.id/server/api/v1/paps/store', [
        'uuid_akreditasi' => 'abc123-def456-ghi789',
        'nama_diklat' => 'Pelatihan Fungsional Statistisi Ahli',
        'kelas' => 'Angkatan I Tahun 2025',
        'tgl_mulai' => '2025-02-01',
        'tgl_selesai' => '2025-02-14',
        'jumlah_jp' => 80,
        'tgl_mulai_klasikal' => '2025-02-10',
        'tgl_selesai_klasikal' => '2025-02-14',
        'id_jenis_diklat' => 21,
        'tipe_diklat' => 'hybrid',
        'nip_pic' => '199001012020011001',
        'instansi_penyelenggara' => 'Pusat Pendidikan dan Pelatihan BPS'
    ]);

if ($response->successful()) {
    $data = $response->json();
    $uuid_diklat = $data['data']['uuid']; // Simpan UUID ini untuk update/delete
} else {
    // Handle error
    $error = $response->json();
}
```

### Contoh Response (Sukses)

```json
{
    "status": "Berhasil menyimpan data",
    "data": {
        "uuid": "d1e2f3g4-h5i6-j7k8-l9m0-n1o2p3q4r5s6",
        "nama_diklat": "Pelatihan Fungsional Statistisi Ahli",
        "kelas": "Angkatan I Tahun 2025",
        "tgl_mulai": "2025-02-01",
        "tgl_selesai": "2025-02-14",
        "jumlah_jp": 80,
        "id_jenis_diklat": 21,
        "tipe_diklat": "hybrid",
        "created_at": "2025-01-26T14:00:00.000000Z",
        "updated_at": "2025-01-26T14:00:00.000000Z"
    }
}
```

> [!IMPORTANT]
> Simpan `uuid` dari response untuk digunakan pada endpoint update dan delete.

---

## 3. Update Pelatihan

Mengupdate data kelas pelatihan yang sudah ada.

### Endpoint

```
POST /server/api/v1/paps/update
```

### Parameter Request

| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `uuid_akreditasi` | string | Ya | UUID nomor akreditasi |
| `uuid_diklat` | string | Ya | UUID kelas pelatihan (dari response store/load) |
| `nama_diklat` | string | Ya | Nama pelatihan |
| `kelas` | string | Ya | Nama kelas/angkatan |
| `tgl_mulai` | date | Ya | Tanggal mulai pelatihan |
| `tgl_selesai` | date | Ya | Tanggal selesai pelatihan |
| `jumlah_jp` | integer | Ya | Jumlah Jam Pelajaran |
| `tgl_mulai_klasikal` | date | Tidak | Tanggal mulai tatap muka |
| `tgl_selesai_klasikal` | date | Tidak | Tanggal selesai tatap muka |
| `id_jenis_diklat` | integer | Ya | ID jenis diklat: `20` atau `21` |
| `tipe_diklat` | string | Ya | Tipe pembelajaran |
| `nip_pic` | string | Ya | NIP PIC kelas |

### Contoh Request (Laravel)

```php
<?php

use Illuminate\Support\Facades\Http;

// Generate token
$token = base64_encode(
    config('services.paps.client_id') . ':' . config('services.paps.client_secret')
);

$response = Http::withToken($token)
    ->post('https://gojags.web.bps.go.id/server/api/v1/paps/update', [
        'uuid_akreditasi' => 'abc123-def456-ghi789',
        'uuid_diklat' => 'd1e2f3g4-h5i6-j7k8-l9m0-n1o2p3q4r5s6',
        'nama_diklat' => 'Pelatihan Fungsional Statistisi Ahli',
        'kelas' => 'Angkatan I Tahun 2025 (Revisi)',
        'tgl_mulai' => '2025-02-01',
        'tgl_selesai' => '2025-02-21',
        'jumlah_jp' => 100,
        'tgl_mulai_klasikal' => '2025-02-15',
        'tgl_selesai_klasikal' => '2025-02-21',
        'id_jenis_diklat' => 21,
        'tipe_diklat' => 'hybrid',
        'nip_pic' => '199001012020011001'
    ]);

if ($response->successful()) {
    $data = $response->json();
    // Data berhasil diupdate
} else {
    // Handle error
    $error = $response->json();
}
```

### Contoh Response (Sukses)

```json
{
    "status": "Berhasil mengupdate data",
    "data": {
        "uuid": "d1e2f3g4-h5i6-j7k8-l9m0-n1o2p3q4r5s6",
        "nama_diklat": "Pelatihan Fungsional Statistisi Ahli",
        "kelas": "Angkatan I Tahun 2025 (Revisi)",
        "tgl_mulai": "2025-02-01",
        "tgl_selesai": "2025-02-21",
        "jumlah_jp": 100,
        "updated_at": "2025-01-26T15:00:00.000000Z"
    }
}
```

---

## 4. Delete Pelatihan

Menghapus kelas pelatihan. Jika tidak ada kelas lagi dalam satu group course (nomor akreditasi), maka group course akan otomatis dihapus.

### Endpoint

```
POST /server/api/v1/paps/delete
```

### Parameter Request

| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `uuid_diklat` | string | Ya | UUID kelas pelatihan yang akan dihapus |

### Contoh Request (Laravel)

```php
<?php

use Illuminate\Support\Facades\Http;

// Generate token
$token = base64_encode(
    config('services.paps.client_id') . ':' . config('services.paps.client_secret')
);

$response = Http::withToken($token)
    ->post('https://gojags.web.bps.go.id/server/api/v1/paps/delete', [
        'uuid_diklat' => 'd1e2f3g4-h5i6-j7k8-l9m0-n1o2p3q4r5s6'
    ]);

if ($response->successful()) {
    // Data berhasil dihapus
} else {
    // Handle error
    $error = $response->json();
}
```

### Contoh Response (Sukses)

```json
{
    "status": "Berhasil menghapus data"
}
```

> [!WARNING]
> Penghapusan bersifat permanen. Pastikan untuk mengkonfirmasi dengan pengguna sebelum menghapus data.

---

## Response Error

### 401 Unauthorized - Token Tidak Tersedia

```json
{
    "status": "Unauthorized",
    "message": "Access denied, you do not have permission to access this resource"
}
```

### 401 Unauthorized - Token Tidak Valid

```json
{
    "status": "Unauthorized",
    "message": "Invalid token"
}
```

### 400 Bad Request - Jenis Diklat Tidak Valid

```json
{
    "status": "Jenis diklat tidak valid"
}
```

### 400 Bad Request - Kelas Sudah Ada

```json
{
    "status": "Kelas tersebut sudah tersedia"
}
```

### 404 Not Found - Akreditasi Tidak Ditemukan

```json
{
    "status": "Nomor akreditasi tidak ditemukan"
}
```

### 404 Not Found - Kelas Tidak Ditemukan

```json
{
    "status": "Kelas tidak ditemukan"
}
```

### 422 Validation Error

```json
{
    "message": "The nama_diklat field is required.",
    "errors": {
        "nama_diklat": ["The nama_diklat field is required."]
    }
}
```

---

## Helper Class

Implementasi helper class ada di `app/Services/PapsApiService.php` (bukan sekadar contoh).
Ringkasannya:

```php
<?php

namespace App\Services;

use App\Services\PapsApiService;

$paps = new PapsApiService();

if (! $paps->isConfigured()) {
    // kredensial belum diisi di .env
}

$result = $paps->loadPelatihan($uuidAkreditasi);
// ['success' => bool, 'http_status' => int, 'status' => string|null,
//  'data' => array|null, 'errors' => array|null, 'message' => string|null]
```

Seluruh method mengembalikan array terstruktur (tidak melempar exception untuk kegagalan
jaringan/HTTP) supaya pemanggil bisa menampilkan pesan ke pengguna, dan setiap kegagalan
dicatat ke log dengan channel default.

### Konfigurasi Environment

Tambahkan ke file `.env`:

```env
PAPS_BASE_URL=https://gojags.web.bps.go.id/server/api/v1/paps
PAPS_CLIENT_ID=
PAPS_CLIENT_SECRET=
PAPS_TIMEOUT=30
```

Isi nilai `PAPS_CLIENT_ID` dan `PAPS_CLIENT_SECRET` langsung di server; jangan di-commit.

Tambahkan ke file `config/services.php`:

```php
'paps' => [
    'base_url' => env('PAPS_BASE_URL', 'https://gojags.web.bps.go.id/server/api/v1/paps'),
    'client_id' => env('PAPS_CLIENT_ID'),
    'client_secret' => env('PAPS_CLIENT_SECRET'),
    'timeout' => env('PAPS_TIMEOUT', 30),
],
```

---

## Catatan Penting

> [!WARNING]
> - Jangan pernah mengekspos Client ID dan Client Secret di frontend/client-side
> - Simpan kredensial di file `.env` dan pastikan file tersebut tidak ter-commit ke repository
> - Gunakan HTTPS untuk semua request ke API
> - NIP PIC harus sudah terdaftar dan pernah login ke GoJAGS
> - Rate limit: Hindari melakukan request secara berlebihan dalam waktu singkat

Catatan tambahan untuk PAPS:

- Setelah mengubah `.env` di server, jalankan `php artisan config:cache` (atau clear) karena
  konfigurasi dibaca lewat `config()`.
- Jangan panggil API ini dari request user secara langsung; gunakan job/queue agar
  kegagalan jaringan GoJAGS tidak menggagalkan transaksi Sekretariat.
- `delete` bersifat permanen dan akan menghapus group course bila kelas terakhir dihapus —
  sediakan konfirmasi eksplisit di UI sebelum memanggilnya.
