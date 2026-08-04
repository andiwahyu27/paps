# DATABASE.md

## Dokumentasi Database PAPS

Database utama bernama **paps**. Skema lengkap dan data master berasal dari file `database/pusdiklat_akreditasi.sql`, bukan dari Laravel migration.

---

## 1. Catatan Penting

- **Tidak semua tabel dibuat via migration**. File migration Laravel hanya berisi 3 tabel default: `users`, `password_resets`, `failed_jobs`.
- **Skema lengkap dan data master** (unsur, subunsur, item, jenis pengajuan, pangkat, wilayah, dll) berada di `database/pusdiklat_akreditasi.sql`.
- Jika ingin menambah tabel, sebaiknya buat migration baru agar versi tetap terkontrol. Data master tetap bisa di-import dari SQL dump.

---

## 2. Daftar Tabel

### 2.1 Tabel Default Laravel

| Tabel | Fungsi |
|-------|--------|
| `users` | Data autentikasi pengguna |
| `password_resets` | Token reset password |
| `failed_jobs` | Log job yang gagal |
| `migrations` | Riwayat migration |

### 2.2 Tabel Master (`mt_*`)

| Tabel | Model | Fungsi |
|-------|-------|--------|
| `mt_unsurs` | `Unsur` | Unsur penilaian akreditasi |
| `mt_subunsurs` | `Subunsur` | Sub-unsur penilaian |
| `mt_items` | `Item` | Butir penilaian beserta bobot |
| `mt_jenis_pengajuans` | `JenisPengajuan` | Jenis pengajuan: Pranata Komputer, Statistisi |
| `mt_pangkat` | `Pangkat` | Daftar pangkat ASN |
| `mt_wilayah` | `Wilayah` | Data provinsi dan kabupaten/kota |
| `mt_program_dokumens` | `DokumenProgram` | Daftar dokumen wajib program pelatihan |
| `mt_tenaga_dokumens` | `DokumenTenaga` | Daftar dokumen wajib tenaga |

### 2.3 Tabel Transaksi (`tb_*`)

| Tabel | Model | Fungsi |
|-------|-------|--------|
| `tb_profile_lembagas` | `Profile` | Profil lembaga pelatihan |
| `tb_pengajuans` | `Pengajuan` | Permohonan akreditasi |
| `tb_pelatihans` | `Pelatihan` | Program pelatihan dalam satu pengajuan |
| `tb_pelatihan_dokumens` | `DokumenPelatihan` | Dokumen per program pelatihan |
| `tb_pelatihan_tenagas` | `TenagaPelatihan` | Tenaga pengajar per program pelatihan |
| `tb_penilaians` | `Penilaian` | Nilai asesor per item pengajuan |
| `tb_tenaga` | `Tenaga` | Data tenaga lembaga |
| `tb_fasilitas` | `Fasilitas` | Fasilitas lembaga |
| `tb_riwayat_jabatan` | `RiwayatJabatan` | Riwayat jabatan tenaga |
| `tb_riwayat_kerja` | `RiwayatKerja` | Riwayat pekerjaan tenaga |
| `tb_riwayat_pelatihan` | `RiwayatPelatihan` | Riwayat pelatihan tenaga |
| `tb_riwayat_pendidikan` | `RiwayatPendidikan` | Riwayat pendidikan tenaga |
| `tr_digital_signatures` | `DigitalSignature` | Data tanda tangan digital |

---

## 3. Relasi Antar Tabel

```
users
  ├── id_profile ──> tb_profile_lembagas
  └── role (2, 3, 4)

tb_profile_lembagas
  ├── provinsi ──> mt_wilayah
  ├── kabupaten_kota ──> mt_wilayah
  ├── banyak tb_tenaga
  ├── banyak tb_fasilitas
  └── banyak tb_riwayat_*

tb_pengajuans
  ├── id_profile ──> tb_profile_lembagas
  ├── id_jenis ──> mt_jenis_pengajuans
  ├── id_asesor1 ──> users
  ├── id_asesor2 ──> users
  ├── id_asesor3 ──> users
  └── banyak tb_pelatihans

tb_pelatihans
  ├── id_pengajuan ──> tb_pengajuans
  ├── banyak tb_pelatihan_dokumens
  └── banyak tb_pelatihan_tenagas

tb_penilaians
  ├── id_pengajuan ──> tb_pengajuans
  └── id_item ──> mt_items (implisit)
```

---

## 4. Master Data Kunci

### 4.1 Jenis Pengajuan

| ID | Nama |
|----|------|
| 1 | Pranata Komputer |
| 2 | Statistisi |

### 4.2 Role User

| ID | Peran |
|----|-------|
| 2 | Sekretariat |
| 3 | Asesor |
| 4 | Lembaga |

### 4.3 Unsur Penilaian

Struktur: **Unsur → Subunsur → Item**. Setiap item memiliki bobot yang digunakan untuk perhitungan akreditasi.

Contoh item:

| Kode | Nama | Bobot |
|------|------|-------|
| 1.1.1 | Kelembagaan | 100 |
| 1.2.1 | Fasilitator | 35 |
| 1.2.2 | Pengelola Pelatihan | 20 |
| 1.3.1 | Sarana Prasarana | 100 |
| 1.4.1 | Program Pelatihan | 100 |
| 2.1.1 | Program Pelatihan dan Pengembangan Kurikulum | 100 |
| 2.2.3 | Evaluasi Penyelenggaraan Pelatihan | 80 |

---

## 5. Panduan Modifikasi Skema

### Jika Menambah Tabel Baru

1. Buat migration Laravel:

```bash
php artisan make:migration create_nama_tabels
```

2. Jalankan migration:

```bash
php artisan migrate
```

3. Update `database/pusdiklat_akreditasi.sql` agar konsisten dengan skema production.

### Jika Mengubah Data Master

Data master (tabel `mt_*`) sebaiknya diubah melalui seeders atau langsung di SQL dump. Hindari mengubah manual di production tanpa backup.

---

## 6. Backup & Restore

### Backup

```bash
mysqldump -h 127.0.0.1 -u root -p paps > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore

```bash
mysql -h 127.0.0.1 -u root -p paps < database/pusdiklat_akreditasi.sql
```

---

## 7. Catatan Kolom Penting

### `tb_pengajuans`

| Kolom | Keterangan |
|-------|------------|
| `id_profile` | Lembaga pemohon |
| `id_jenis` | Jenis pengajuan (1/2) |
| `id_asesor1`, `id_asesor2`, `id_asesor3` | Asesor yang ditugaskan |
| `pra_visit_asesor1`, `pra_visit_asesor2`, `pra_visit_asesor3` | Status pra-visit per asesor |
| `pra_visit2_asesor` | Status pra-visit 2 |
| `visitasi` | Status visitasi |
| `paska_visit` | Status paska-visit |
| `final` | Status final |
| `deleted_at` | Soft delete |

### `users`

| Kolom | Keterangan |
|-------|------------|
| `role` | 2=Sekretariat, 3=Asesor, 4=Lembaga |
| `id_profile` | Relasi ke lembaga (untuk role 4) |

---

*Lihat juga: `SETUP.md`, `ARSITEKTUR.md`, `API.md`.*
