# Tanda Tangan Berita Acara Visitasi

## Ringkasan

Fitur ini menyediakan alur tanda tangan elektronik untuk Berita Acara pada tahap visitasi. Berita Acara ditandatangani oleh tiga asesor dan kepala lembaga melalui halaman publik berbasis token.

## Alur Proses

```text
Sekretariat mengisi data Berita Acara
        |
        v
Sistem membuat token pengajuan
        |
        v
Penandatangan membuka /ttd/{token}
        |
        v
Asesor 1, Asesor 2, Asesor 3, dan Kepala Lembaga menandatangani
        |
        v
Semua tanda tangan lengkap
        |
        v
Berita Acara disubmit
        |
        v
ba_submitted_at terisi dan dokumen terkunci
```

## Penandatangan

Jenis penandatangan yang didukung:

- `asesor1`
- `asesor2`
- `asesor3`
- `kepala`

Sistem memeriksa data nama dan jabatan penandatangan sebelum tanda tangan disimpan.

## Halaman Tanda Tangan

Token tanda tangan disimpan pada data pengajuan dan digunakan untuk membuka halaman:

```text
/ttd/{token}
```

Token menggunakan format hexadecimal 40 karakter.

Halaman tersebut menampilkan status tanda tangan masing-masing penandatangan dan menyediakan area untuk menggambar tanda tangan.

## Endpoint Utama

### Membuat data Berita Acara

```text
POST /ttd
```

Route:

```text
`ttd.create.post`
```

Setelah data berhasil disimpan, pengguna diarahkan ke:

```text
/ttd/{token}
```

### Menyimpan tanda tangan

```text
POST /ettd/save-signature
```

Route:

```text
`ttd.save`
```

Parameter penting:

- `token`
- `signer_type`
- `signature_data`

`signature_data` dikirim sebagai data PNG dari canvas tanda tangan.

Validasi signer:

```text
asesor1|asesor2|asesor3|kepala
```

### Melihat status tanda tangan

```text
GET /api/ttd/{token}/signatures
```

Response menyediakan:

- Status tanda tangan setiap penandatangan
- Data nama dan jabatan
- Flag `is_fully_signed`
- Flag `ba_submitted`

### Mengambil gambar tanda tangan

```text
GET /api/ttd/{token}/signatures/{signerType}/image
```

Nilai `signerType` yang valid:

```text
asesor1
asesor2
asesor3
kepala
```

### Submit Berita Acara

```text
POST /ettd/submit-ba
```

Route:

```text
`ttd.submit.ba`
```

Submit hanya berhasil jika seluruh tanda tangan sudah lengkap. Setelah berhasil, sistem mengisi:

```text
ba_submitted_at
```

### Reset Berita Acara

```text
POST /ettd/reset-ba
```

Route:

```text
`ttd.reset.ba`
```

Reset mengosongkan status submit Berita Acara sehingga proses tanda tangan dapat dibuka kembali.

### Reset satu tanda tangan

```text
POST /ettd/reset-signature
```

Route:

```text
`ttd.reset`
```

### Reset seluruh tanda tangan

```text
POST /ettd/reset-all-signatures
```

Route:

```text
`ttd.reset.all`
```

Kedua endpoint reset tanda tangan dilindungi middleware sekretariat.

### Rotasi token

```text
POST /pengajuan/{id}/ttd-token/rotate
```

Route:

```text
`ttd.token.rotate`
```

Token baru digunakan apabila token lama perlu dicabut atau diganti.

## Proteksi Setelah Submit

Jika `ba_submitted_at` sudah terisi:

- Penyimpanan data formulir ditolak
- Penyimpanan tanda tangan ditolak
- API mengembalikan HTTP `409`
- Pesan yang ditampilkan meminta Berita Acara di-reset terlebih dahulu

Contoh kondisi:

```php
if ($pengajuan->ba_submitted_at) {
    return response()->json([
        'status' => 'error',
        'message' => 'Berita Acara telah disubmit.',
    ], 409);
}
```

## Penyimpanan Data

Model utama:

```text
app/Models/DigitalSignature.php
```

Pengontrol utama:

```text
app/Http/Controllers/TtdController.php
```

Field penting pada pengajuan:

```text
ttd_token
ba_submitted_at
```

Data tanda tangan menyimpan antara lain:

- `pengajuan_id`
- `jenis_user`
- `nama_user`
- `jabatan_user`
- `ttd`
- `tgl_surat`
- `waktu_surat`
- `tgl_waktu_surat`
- `status_ttd`

File tanda tangan lama dihapus ketika tanda tangan baru berhasil disimpan.

## Perbedaan dengan Reset Penilaian

Reset TTD Berita Acara berbeda dari reset penilaian:

| Fitur | Status yang diubah |
|---|---|
| Edit Pravisitasi | `pra_visit_asesor1/2/3` |
| Edit Pravisitasi 2 | `pra_visit2_asesor` |
| Edit Paska Visitasi | `paska_visit` dan `final` |
| Submit Berita Acara | `ba_submitted_at` |
| Status tanda tangan | `digital_signatures.status_ttd` |

## File Implementasi

```text
routes/web.php
routes/api.php
app/Http/Controllers/TtdController.php
app/Models/DigitalSignature.php
```

View terkait:

```text
resources/views/ttd.blade.php
resources/views/ttd-backup.blade.php
```

## Catatan Keamanan

- Jangan membagikan token TTD kepada pihak yang tidak berkepentingan.
- Reset Berita Acara hanya dilakukan oleh sekretariat.
- Token menggunakan pola hexadecimal 40 karakter.
- Endpoint simpan tanda tangan memiliki rate limit.
- Berita Acara yang sudah disubmit harus di-reset sebelum tanda tangan dapat diubah.
