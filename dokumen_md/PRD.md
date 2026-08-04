# Product Requirements Document (PRD)

## PAPS — Platform Akreditasi Pelatihan Prakom & Statistisi

---

## 1. Visi & Tujuan

PAPS adalah sistem berbasis web untuk mengelola seluruh siklus akreditasi pelatihan bagi calon **Pranata Komputer (Prakom)** dan **Statistisi**. Platform ini menghubungkan tiga pihak utama — **Lembaga Pelatihan**, **Asesor**, dan **Sekretariat** — dalam satu alur kerja yang terstruktur, terukur, dan terdokumentasi.

**Tujuan utama:**
- Mempermudah lembaga dalam mengajukan dan mengelola dokumen akreditasi.
- Memberikan ruang kerja bagi asesor untuk menilai kelayakan lembaga dan program pelatihan.
- Memungkinkan sekretariat untuk memverifikasi, memantau, dan menerbitkan hasil akreditasi secara efisien.

---

## 2. Pengguna & Peran

| Peran | Kode Role | Tanggung Jawab Utama |
|-------|-----------|----------------------|
| **Sekretariat** | 2 | Manajemen pengguna, verifikasi pengajuan, penugasan asesor, monitoring, generate dokumen hasil |
| **Asesor** | 3 | Melakukan penilaian pra-visit, visitasi, paska-visit, final, dan mengunggah rekomendasi/sertifikat |
| **Lembaga** | 4 | Mengisi profil lembaga, mengajukan akreditasi, mengunggah dokumen program pelatihan |
| **Tamu/Public** | — | Mengakses halaman tanda tangan digital (e-TTD) publik |

---

## 3. Fitur Berdasarkan Peran

### 3.1 Autentikasi & Akses
- Login dengan email dan password (Laravel default auth).
- Login dengan Google OAuth.
- Login dengan SSO BPS (GOJAGS).
- Role-based access control (RBAC) sederhana berdasarkan kolom `role` pada tabel `users`.
- Sekretariat dapat "login sebagai" pengguna lain untuk membantu troubleshooting.

### 3.2 Modul Lembaga
- **Profil Lembaga**:
  - Kelembagaan (data pimpinan, SK pemerintah, akte pendirian, izin operasional, dll).
  - Tenaga (fasilitator, pengelola pelatihan, pengelola kelas, dll).
  - Fasilitas (sarana prasarana).
  - Penyelenggaraan (rencana kegiatan, SOP, laporan penjaminan mutu, dll).
- **Dokumen Tenaga**: mengelola dokumen pendukung per tenaga.
- **Pengajuan Akreditasi**: membuat, mengedit, membatalkan, dan melihat riwayat pengajuan.
- **Program Pelatihan**: mengelola dokumen program pelatihan per pengajuan (kurikulum, jadwal, sertifikat, dll).
- **Lock Profil**: mengunci profil agar tidak dapat diedit lagi setelah siap dinilai.

### 3.3 Modul Asesor
- Melihat daftar pengajuan yang ditugaskan.
- Penilaian tahap:
  - **Pra-Visit 1**
  - **Pra-Visit 2**
  - **Visitasi**
  - **Paska-Visit**
  - **Final**
- Penilaian per item butir akreditasi dengan catatan dan bukti dukung.
- Pengunggahan berita acara, rekomendasi, dan sertifikat akreditasi.
- Identitas lembaga yang dinilai.

### 3.4 Modul Sekretariat
- Manajemen pengguna (tambah, ubah, hapus, login sebagai).
- Manajemen data master pelatihan.
- Manajemen lembaga dan PIC lembaga.
- Verifikasi pengajuan.
- Penugasan asesor (sampai 3 asesor per pengajuan).
- Monitoring evaluasi dan penyelenggaraan.
- Generate berita acara.
- Lihat rekap penilaian.

### 3.5 Modul E-TTD (Tanda Tangan Digital)
- Halaman publik untuk pengisian tanda tangan digital.
- Penyimpanan signature.
- Download dokumen yang sudah ditandatangani.
- Reset signature.

---

## 4. Alur Bisnis

### 4.1 Siklus Pengajuan Akreditasi

```
Lembaga                Sekretariat              Asesor
   |                        |                       |
   |-- Isi Profil --------->|                       |
   |-- Ajukan Akreditasi -->|                       |
   |                        |-- Verifikasi -------->|
   |                        |-- Assign Asesor ----->|
   |                        |                       |-- Pra-Visit 1
   |                        |                       |-- Pra-Visit 2
   |                        |                       |-- Visitasi
   |                        |                       |-- Paska-Visit
   |                        |                       |-- Final
   |                        |-- Generate BA/Sertifikat
   |<-- Hasil Akreditasi ---|                       |
```

### 4.2 Tahapan Penilaian

1. **Pra-Visit 1**: Asesor menilai dokumen dasar lembaga.
2. **Pra-Visit 2**: Penilaian lebih mendalam, dapat diakses oleh asesor dan sekretariat.
3. **Visitasi**: Penilaian lapangan/visitasi.
4. **Paska-Visit**: Evaluasi tindak lanjut setelah visitasi.
5. **Final**: Keputusan akhir akreditasi.

Setiap tahap mencakup:
- Penilaian per item butir.
- Pengisian catatan.
- Submit penilaian.
- Generate dokumen hasil (BA, rekomendasi, sertifikat).

---

## 5. Kebutuhan Fungsional

| ID | Kebutuhan | Peran |
|----|-----------|-------|
| FR-001 | Lembaga dapat mendaftar dan login | Lembaga |
| FR-002 | Lembaga dapat mengisi dan mengupdate profil | Lembaga |
| FR-003 | Lembaga dapat mengajukan akreditasi | Lembaga |
| FR-004 | Lembaga dapat mengunggah dokumen program pelatihan | Lembaga |
| FR-005 | Lembaga dapat mengunci profil | Lembaga |
| FR-006 | Sekretariat dapat mengelola pengguna | Sekretariat |
| FR-007 | Sekretariat dapat memverifikasi pengajuan | Sekretariat |
| FR-008 | Sekretariat dapat menetapkan asesor | Sekretariat |
| FR-009 | Sekretariat dapat melakukan monitoring | Sekretariat |
| FR-010 | Asesor dapat melihat pengajuan yang ditugaskan | Asesor |
| FR-011 | Asesor dapat melakukan penilaian per tahap | Asesor |
| FR-012 | Asesor dapat mengunggah berita acara dan rekomendasi | Asesor |
| FR-013 | Sistem dapat generate sertifikat dan berita acara | Sekretariat/Asesor |
| FR-014 | Sistem mendukung tanda tangan digital publik | Public |
| FR-015 | Sistem mengirim notifikasi WhatsApp | Sistem |

---

## 6. Kebutuhan Non-Fungsional

| ID | Kebutuhan | Keterangan |
|----|-----------|------------|
| NFR-001 | Performa | Halaman utama muat dalam < 3 detik |
| NFR-002 | Keamanan | Password di-hash, session cookie dienkripsi, CSRF protection aktif |
| NFR-003 | Skalabilitas | Dapat menangani ratusan pengajuan aktif |
| NFR-004 | Ketersediaan | Dapat diakses selama jam kerja dengan uptime > 99% |
| NFR-005 | Audit Trail | Setiap perubahan status pengajuan tercatat waktu dan aktor |
| NFR-006 | Dokumen | Mendukung upload PDF dan generate dokumen Word/PDF |
| NFR-007 | Bahasa | Bahasa Indonesia sebagai default locale |

---

## 7. Model Data Utama

### 7.1 Entitas dan Relasi

- **User** memiliki satu **Profile** (`id_profile`).
- **Profile** memiliki banyak **User** (PIC).
- **Profile** memiliki banyak **Tenaga**, **Fasilitas**, dan **Riwayat**.
- **User** (Lembaga) memiliki banyak **Pengajuan**.
- **Pengajuan** memiliki banyak **Pelatihan**.
- **Pengajuan** memiliki banyak **Penilaian**.
- **Pengajuan** ditugaskan kepada 3 **User** (Asesor).
- **Pelatihan** memiliki banyak **DokumenPelatihan** dan **TenagaPelatihan**.

### 7.2 Master Data

- **Unsur** → **Subunsur** → **Item** (butir penilaian).
- **JenisPengajuan**: Pranata Komputer, Statistisi.
- **Pangkat**: daftar pangkat ASN.
- **Wilayah**: provinsi dan kabupaten/kota.
- **DokumenProgram**: jenis dokumen wajib program pelatihan.

---

## 8. Integrasi Eksternal

| Layanan | Fungsi | Catatan |
|---------|--------|---------|
| Google OAuth | Login menggunakan akun Google | Diperlukan `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` |
| SSO BPS (GOJAGS) | Login menggunakan akun pegawai BPS | Diperlukan `CLIENT_ID`, `CLIENT_SECRET`, dan `SSO_URL` |
| Fonnte API | Pengiriman notifikasi WhatsApp | Diperlukan token API |
| Mailtrap/SMTP | Pengiriman email | Konfigurasi di `.env` |

---

## 9. Antarmuka Pengguna

### 9.1 Halaman Publik
- `/login` — halaman login.
- `/ttd` — halaman tanda tangan digital.
- `/panduan` — panduan penggunaan.

### 9.2 Dashboard Per Peran
- **Lembaga**: dashboard profil, pengajuan aktif, riwayat.
- **Asesor**: dashboard penilaian per tahap.
- **Sekretariat**: dashboard verifikasi, monitoring, manajemen pengguna.

### 9.3 Responsive Design
- Menggunakan Bootstrap 5.
- Layout dasar berbasis template admin vertical menu.

---

## 10. Keamanan

- Autentikasi wajib untuk semua halaman kecuali publik (`/ttd`, `/login`, `/panduan`).
- Middleware memeriksa role user sebelum mengakses route tertentu.
- CSRF token pada semua form.
- Session menggunakan encrypted cookie.
- Upload file dibatasi untuk tipe PDF.
- Penamaan file unik untuk mencegah konflik dan overwrite.
- Kredensial eksternal disimpan di `.env`, tidak di-hardcode (kecuali token sementara yang perlu dievaluasi).

---

## 11. Indikator Keberhasilan (KPI)

- Persentase pengajuan yang terselesaikan dalam SLA yang ditentukan.
- Jumlah lembaga yang terdaftar dan aktif mengajukan.
- Persentase penilaian yang diselesaikan oleh asesor tepat waktu.
- Minimalisasi kesalahan dokumen akreditasi melalui validasi sistem.
- Kepuasan pengguna dari masing-masing peran.

---

## 12. Batasan & Asumsi

- Database saat ini dikelola melalui SQL dump, bukan sepenuhnya via migration.
- Role-based access bersifat sederhana (integer di kolom `role`).
- Notifikasi WhatsApp bergantung pada ketersediaan layanan Fonnte.
- SSO Google dan GOJAGS memerlukan konfigurasi domain dan kredensial yang valid.

---

## 13. Roadmap (Usulan)

| Fase | Fokus |
|------|-------|
| **Sekarang** | Stabilisasi fitur inti: profil, pengajuan, penilaian, generate dokumen |
| **Selanjutnya** | Notifikasi real-time, audit log lengkap, dashboard analitik |
| **Kedepan** | API terstruktur, mobile-friendly, integrasi e-meterai resmi |

---

*Dokumen ini dibuat berdasarkan analisis kode dan arsitektur PAPS.*
