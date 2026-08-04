# API.md

## Dokumentasi Endpoint PAPS

Dokumen ini berisi daftar endpoint utama di `routes/web.php` dan `routes/api.php`. Endpoint dikelompokkan berdasarkan modul dan middleware.

---

## 1. Autentikasi

| Method | URI | Name | Middleware | Keterangan |
|--------|-----|------|------------|------------|
| GET/POST | `/login` | `login` | web, guest | Halaman dan proses login |
| POST | `/logout` | `logout` | web, auth | Logout |
| GET | `/auth-redirect` | `login.google` | web, guest | Redirect ke Google OAuth |
| GET | `/auth-callback` | — | web, guest | Callback Google OAuth |
| GET | `/redirect-gojags/{type}` | `login.gojags` | web, guest | Redirect ke SSO GOJAGS |
| GET | `/callback-gojags` | — | web, guest | Callback SSO GOJAGS |
| GET | `/login-error` | `login.error` | web, guest | Halaman error login |

---

## 2. Public

| Method | URI | Name | Middleware | Keterangan |
|--------|-----|------|------------|------------|
| GET | `/` | — | web | Redirect ke `/home` |
| GET | `/home` | `home` | web, auth | Dashboard utama |
| GET | `/panduan` | `panduan` | web | Panduan pengguna |
| GET | `/pengaturan` | `pengaturan` | web | Halaman pengaturan |
| GET | `/error` | `error` | web | Halaman error umum |
| POST | `/back-to-reality` | `back.to.reality` | web, auth | Utility sekretariat |

---

## 3. E-TTD (Tanda Tangan Digital) — Public

| Method | URI | Name | Middleware | Keterangan |
|--------|-----|------|------------|------------|
| GET | `/ttd` | `ttd.public` | web | Halaman tanda tangan publik |
| POST | `/ettd/save-signature` | `ttd.save` | web | Simpan tanda tangan |
| GET | `/ttd/signatures` | `ttd.signatures` | web | Ambil daftar tanda tangan |
| POST | `/ttd/download` | `ttd.download` | web | Download dokumen |
| GET | `/ttd/{pengajuanId}` | `ttd.create` | web | Halaman tanda tangan spesifik |
| POST | `/ttd` | `ttd.create.post` | web | Proses tanda tangan |
| POST | `/ttd/{pengajuanId}` | `ttd.create.with.id` | web | Proses tanda tangan dengan ID |
| POST | `/ettd/reset-signature` | `ttd.reset` | web | Reset tanda tangan |
| GET | `/tandatangan` | — | web | Alias view tanda tangan |

---

## 4. Sekretariat (`is.sekretariat`)

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/pengguna/{role?}` | `pengguna` | Daftar pengguna |
| POST | `/pengguna/add` | `pengguna.tambah` | Tambah pengguna |
| PUT | `/pengguna/edit` | `pengguna.ubah` | Ubah pengguna |
| DELETE | `/pengguna/delete` | `pengguna.hapus` | Hapus pengguna |
| GET | `/pengguna/login/{id}` | `pengguna.login` | Login sebagai user lain |
| GET | `/pengajuan/view/{id}` | `lihat.pengajuan` | Lihat detail pengajuan |
| GET | `/pengajuan/view/{id}/rekap` | `lihat.rekap` | Lihat rekap penilaian |
| PUT | `/pengajuan/verifikasi` | `verifikasi.pengajuan` | Verifikasi pengajuan |
| POST | `/pelatihan/add` | `pelatihan.tambah` | Tambah data pelatihan |
| PUT | `/pelatihan/edit` | `pelatihan.ubah` | Ubah data pelatihan |
| DELETE | `/pelatihan/delete` | `pelatihan.hapus` | Hapus data pelatihan |
| GET | `/lembaga` | `lembaga` | Daftar lembaga |
| POST | `/lembaga/add` | `lembaga.tambah` | Tambah lembaga |
| PUT | `/lembaga/pic/add` | `pic.tambah` | Tambah PIC lembaga |
| PUT | `/lembaga/pic/delete` | `pic.hapus` | Hapus PIC lembaga |
| PUT | `/assign-asesor` | `assign.asesor` | Tetapkan asesor |
| GET | `/generate-ba/{id}` | `generate.ba` | Generate berita acara |
| GET | `/monitoring-evaluasi` | `monitoring-evaluasi` | Monitoring evaluasi |
| GET | `/monitoring-penyelenggaraan/{id}` | `monitoring-penyelenggaraan` | Monitoring penyelenggaraan |

---

## 5. Asesor / Sekretariat (`is.asesor.or.sekretariat`)

Prefix: `/pengajuan`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/pravisit2/{id}` | `pravisit2` | Penilaian pra-visit 2 |
| GET | `/pravisit2/view/{id}` | `view.pravisit2` | Lihat pra-visit 2 |
| GET | `/pravisit2/ekspor-ba/{id}` | `ekspor.ba` | Ekspor berita acara |
| GET | `/visitasi/ekspor-rekomendasi/{id}` | `ekspor.rekomendasi` | Ekspor rekomendasi |
| GET | `/ekspor-sertifikat/{id}` | `ekspor.sertifikat` | Ekspor sertifikat |
| GET | `/visitasi/{id}` | `visitasi` | Penilaian visitasi |
| POST | `/store-rekomendasi` | `upload.rekomendasi` | Simpan rekomendasi |
| POST | `/visitasi/store-ba` | `upload.ba` | Simpan berita acara |
| POST | `/store-sertifikat` | `upload.sertifikat` | Simpan sertifikat |
| GET | `/paskavisit/{id}` | `paskavisit` | Penilaian paska-visit |
| GET | `/paskavisit/view/{id}` | `view.paskavisit` | Lihat paska-visit |
| GET | `/final/{id}` | `final` | Penilaian final |
| GET | `/final/view/{id}` | `view.final` | Lihat final |
| GET | `/identitas-lembaga/{step?}` | `identitas.lembaga` | Identitas lembaga |
| POST | `/nilai/pr2/item` | `nilai.pra2.item` | Nilai item pra-visit 2 |
| POST | `/nilai/paska/item` | `nilai.paska.item` | Nilai item paska-visit |
| POST | `/nilai/final/item` | `nilai.final.item` | Nilai item final |
| POST | `/nilai/pra/catatan` | `catatan.pra.item` | Catatan item pra-visit |

Tambahan:

| Method | URI | Name | Middleware |
|--------|-----|------|------------|
| POST | `/pengajuan/ekspor-penilaian` | `ekspor.penilaian` | `is.asesor.or.sekretariat` |

---

## 6. Asesor (`is.asesor`)

Prefix: `/pengajuan`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/bukti-dukung/{pengajuan}/{kode}` | `bukti-dukung` | Lihat bukti dukung |
| POST | `/bukti-dukung/tenaga-item` | `bd.tenaga.modal` | Modal tenaga |
| GET | `/tenaga-dokumen/{id}/{step}` | `dokumen.tenaga.bukti` | Dokumen tenaga |
| GET | `/pravisit/{id}` | `pravisit` | Penilaian pra-visit |
| GET | `/pravisit/view/{id}` | `view.pravisit` | Lihat pra-visit |
| POST | `/nilai/pra/item` | `nilai.pra.item` | Nilai item pra-visit |
| POST | `/nilai/pra` | `nilai.pra` | Submit nilai pra-visit |
| POST | `/nilai/pra2` | `nilai.pra2` | Submit nilai pra-visit 2 |
| POST | `/nilai/paska` | `nilai.paska` | Submit nilai paska-visit |
| POST | `/nilai/final` | `nilai.final` | Submit nilai final |
| POST | `/nilai/pra/submit` | `nilai.pra.submit` | Final submit pra-visit |
| POST | `/nilai/pra2/submit` | `nilai.pra2.submit` | Final submit pra-visit 2 |
| POST | `/nilai/paska/submit` | `nilai.paska.submit` | Final submit paska-visit |
| POST | `/nilai/final/submit` | `nilai.final.submit` | Final submit final |

---

## 7. Lembaga (`is.lembaga`)

### 7.1 Pengajuan

Prefix: `/pengajuan`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/{type?}` | `pengajuan` | Daftar/ajukan pengajuan |
| GET | `/{type?}/edit` | `edit.pengajuan` | Edit pengajuan |
| GET | `/riwayat/{id?}` | `riwayat.pengajuan` | Riwayat pengajuan |
| POST | `/store` | `store.pengajuan` | Simpan pengajuan baru |
| POST | `/update` | `update.pengajuan` | Update pengajuan |
| POST | `/batal` | `batal.pengajuan` | Batalkan pengajuan |

### 7.2 Profil

Prefix: `/profile`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/kelembagaan/{step?}` | `profile.kelembagaan` | Profil kelembagaan |
| GET | `/tenaga/{step?}` | `profile.tenaga` | Profil tenaga |
| GET | `/fasilitas/{step?}` | `profile.fasilitas` | Profil fasilitas |
| GET | `/penyelenggaraan/{step?}` | `profile.penyelenggaraan` | Profil penyelenggaraan |
| PUT | `/update` | `profile.update` | Update profil |
| POST | `/tambah-fasilitas` | `tambah.fasilitas` | Tambah fasilitas |
| PUT | `/ubah-fasilitas` | `ubah.fasilitas` | Ubah fasilitas |
| DELETE | `/hapus-fasilitas` | `delete.fasilitas` | Hapus fasilitas |
| POST | `/tambah-tenaga` | `tambah.tenaga` | Tambah tenaga |
| POST | `/tenaga-item` | `tenaga.modal` | Modal tenaga |
| PUT | `/tenaga-update` | `ubah.tenaga` | Ubah tenaga |
| DELETE | `/tenaga-delete` | `delete.tenaga` | Hapus tenaga |
| POST | `/tambah-riwayat` | `tambah.riwayat` | Tambah riwayat |
| PUT | `/edit-riwayat` | `ubah.riwayat` | Ubah riwayat |
| DELETE | `/hapus-riwayat` | `hapus.riwayat` | Hapus riwayat |
| POST | `/kabkota` | `data.kabkota` | Ambil data kabupaten/kota |
| POST | `/lock` | `profile.lock` | Kunci profil |

### 7.3 Dokumen Tenaga

Prefix: `/profile/tenaga-dokumen`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/{id}/{step}` | `dokumen.tenaga` | Lihat dokumen tenaga |
| POST | `/add` | `add.modal.post` | Tambah dokumen tenaga |
| POST | `/edit` | `edit.modal.get` | Form edit dokumen |
| PUT | `/edit/update` | `edit.modal.update` | Update dokumen tenaga |
| DELETE | `/delete` | `delete.modal` | Hapus dokumen tenaga |

### 7.4 Program

Prefix: `/program`

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/{id}/{step?}` | `program.akreditasi` | Program akreditasi |
| POST | `/tambah-dokumen` | `store.dokumen` | Tambah dokumen program |
| PUT | `/edit-dokumen` | `edit.dokumen` | Ubah dokumen program |
| DELETE | `/hapus-dokumen` | `hapus.dokumen` | Hapus dokumen program |
| POST | `/tambah-tenaga/{id}` | `store.tenaga` | Tambah tenaga program |
| DELETE | `/hapus-tenaga` | `hapus.tenaga` | Hapus tenaga program |

---

## 8. API Routes

File: `routes/api.php`

| Method | URI | Middleware | Keterangan |
|--------|-----|------------|------------|
| GET | `/api/user` | auth:api | Data user yang sedang login |
| GET | `/api/dbtest` | — | Test koneksi database |
| GET | `/api/signatures` | — | Ambil daftar tanda tangan |

---

## 9. Catatan Umum

- Semua route POST wajib menyertakan CSRF token (`_token`) atau header `X-CSRF-TOKEN`.
- Middleware memeriksa role user sebelum mengakses endpoint tertentu.
- Beberapa endpoint menerima parameter `step` untuk mengatur tab/step form.
- Dokumen dan file diunggah sebagai multipart/form-data.

---

*Lihat juga: `AUTHENTICATION.md`, `ARSITEKTUR.md`, `DATABASE.md`.*
