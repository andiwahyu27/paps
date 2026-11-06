# Rencana Monitoring Akreditasi PSTK

## 1. Tujuan dan keputusan

Bangun modul sementara pada PAPS dengan membaca database gen1 dan gen2 serta menyimpan laporan baru di gen2. Migrasi penuh dilakukan saat perpindahan ke akreditasi-laravel12.

- Satu laporan per lembaga × program Prakom/Statistisi × tahun.
- Pengisian hanya oleh role lembaga (4); sekretariat (2) membaca, merekap, mencetak, dan mengekspor.
- Lembaga boleh memperbaiki laporan; setiap pengiriman menyimpan versi tetap.
- Laporan nihil tetap disampaikan.
- Tahun laporan harus beririsan dengan masa akreditasi dan tidak melebihi tahun berjalan.
- Nomor sertifikat, predikat, dan masa berlaku berasal dari daftar resmi sekretariat.
- Kedua database tersedia dari server PAPS; autentikasi tetap memakai akun gen2.
- Desain mengikuti `resources/views/sekretariat/monitoring-evaluasi.blade.php`.
- Seluruh pengujian berikut masih berupa rencana, bukan hasil pengujian.

Sumber analisis: dump `20261029_db_paps2.sql`, `20261105_db_paps_gen1.sql`, dan instrumen `Instrumen Monitoring PSTK Terakreditasi_Sari 14102025.pdf`.

## 2. Akses data dan penyimpanan

### Temuan dan pendekatan

Kelima nama lembaga gen1 ditemukan di gen2, tetapi ID transaksi bertumpang tindih dan master penilaian/dokumen berbeda. Predikat gen1 kosong; tanggal penetapan/kedaluwarsa kosong pada kedua dump. Nomor sertifikat belum terstruktur. Data pelatihan merupakan pendukung pengajuan, bukan catatan lengkap kegiatan setelah akreditasi. Monitoring saat ini menggunakan data contoh.

Pertahankan koneksi gen2 dan tambahkan `mysql_gen1` dengan hak baca. Semua penulisan monitoring melalui gen2. Jangan menggabungkan transaksi/master berdasarkan ID saja.

- Profil gen2 menjadi identitas utama. Pemetaan eksplisit: sumber + ID profil asal → ID profil gen2; kandidat kesamaan nama harus diverifikasi.
- Layanan pembaca menyeragamkan profil, program, pengajuan, tenaga, dan referensi dokumen. Isi awal dari gen2; nilai kosong dapat dilengkapi gen1 dengan penanda sumber tanpa menimpa profil utama.
- Referensi historis selalu membawa sumber dan ID asal. Tautan berkas hanya tersedia setelah lokasi fisik dan akses diverifikasi.
- Akun lembaga harus memiliki `id_profile` yang benar.

| Tabel baru gen2 | Isi |
|---|---|
| monitoring_profile_maps | Pemetaan profil asal ke profil utama |
| monitoring_accreditations | Program, nomor sertifikat, predikat, masa berlaku, referensi asal |
| monitoring_reports | UUID, lembaga, program, tahun, draf, pengubah, revisi penyimpanan, versi terkirim terakhir |
| monitoring_report_versions | Snapshot jawaban, identitas, akreditasi, pengirim, nomor versi, waktu kirim |

Keunikan lembaga–program–tahun ditegakkan di database. Impor Artisan menerima CSV pemetaan dan CSV akreditasi (sumber, ID profil asal, program, nomor sertifikat, predikat A/B/C, tanggal mulai/akhir). Impor idempoten; tolak pemetaan ambigu, tanggal terbalik, dan konflik data resmi.

Untuk Laravel 12, sediakan ekspor JSON `schema_version: 1` berisi UUID, referensi sumber, akreditasi, dan seluruh versi. Jawaban memakai nomor pertanyaan serta kode pilihan stabil yang terpisah dari label. Laporan tersimpan tetap terbaca saat gen1 gagal diakses.

## 3. Pemetaan instrumen dan aturan form

Nomor dan pilihan instrumen dipertahankan. Isian awal dikonfirmasi lembaga; koreksi kontak disimpan pada laporan tanpa mengubah profil utama atau penguncian pengajuan.

| No. | Pertanyaan | Sumber/perlakuan |
|---|---|---|
| 1 | Nama lembaga | Profil akun; pemilik tidak dapat diganti |
| 2 | Program terakreditasi | Pilihan daftar resmi milik lembaga |
| 3 | Alamat | Isi awal alamat_lembaga; boleh dikoreksi |
| 4 | Telepon | Isi awal telepon; boleh dikoreksi |
| 5 | Email | Email profil lembaga |
| 6 | Website | Isi awal website; boleh kosong |
| 7 | Nomor sertifikat | Daftar resmi sekretariat |
| 8 | Predikat A/B/C | Daftar resmi; predikat gen2 sebagai pembanding |
| 9 | Tahun pemantauan | Tahun yang memenuhi masa akreditasi |
| 10 | Jumlah pelatihan | Diisi lembaga; bukan hitungan data pengajuan |
| 11 | Jumlah peserta | Diisi lembaga |
| 12 | Peserta internal/eksternal | Diisi lembaga; total sama dengan nomor 11 |
| 13 | Kesesuaian program | Diisi lembaga |
| 14 | Metode pelatihan | Pilihan jamak |
| 15 | Penilaian kelulusan | Satu penilaian umum tahunan oleh lembaga |
| 16 | Penilaian penyelenggaraan | Diisi lembaga |
| 17 | Pengajar internal/eksternal | Pilihan jamak; data asal belum memadai |
| 18 | Jabatan pengajar | Pilihan jamak; jabatan tenaga sebagai referensi |
| 19 | Kelengkapan dokumen mutu | Konfirmasi lembaga dengan referensi SOP/dokumen |
| 20 | Pemutakhiran dokumen | Diisi lembaga; updated_at bukan bukti substansi |
| 21 | Kepatuhan persyaratan | Diisi lembaga; nilai historis hanya referensi |
| 22 | Evaluasi peserta | Diisi lembaga; dokumen belum berupa rekap terstruktur |
| 23 | Tindak lanjut | Ada/tidak; keterangan wajib bila Ada |
| 24 | Kendala | Diisi lembaga |
| 25 | Rencana tahun depan | Diisi lembaga; tahun tujuan = tahun laporan + 1 |
| 26 | Kebutuhan pembinaan | Diisi lembaga |

- Laporkan kegiatan dalam bagian tahun yang berada pada masa akreditasi. Gabungkan rentang perpanjangan tanpa menghitung periode dua kali; tampilkan sertifikat relevan.
- Draf boleh parsial; pengiriman memvalidasi semua jawaban yang berlaku. Angka bilangan bulat tidak negatif; internal + eksternal = total peserta.
- Bila nomor 10 nol, peserta wajib nol; nomor 13–18 dan 22–23 menjadi tidak berlaku. Nomor 19–21 dan 24–26 tetap diisi; nomor 24 menjelaskan alasan nihil.
- Nomor 24–26 boleh berisi pernyataan Tidak ada bila sesuai.
- Setiap kirim membuat snapshot tetap secara transaksional. Draf perbaikan tidak mengganti versi yang dilihat sekretariat hingga dikirim ulang.
- Pemeriksaan nomor revisi mencegah dua tab/PIC saling menimpa.
- Rute lembaga `/monitoring-akreditasi-pstk`: daftar, buat, draf, detail, kirim, riwayat. Semua tindakan memeriksa auth, role, dan kepemilikan di server.
- Rekap sekretariat menggunakan `/monitoring-evaluasi` dengan detail dan ekspor tersendiri.

## 4. Layout frontend

### Gaya visual

Gunakan layouts.app-lembaga dan layouts.app-sekretariat; aset Blade/Bootstrap yang sudah tersedia. Ikuti container-xxl, breadcrumb, card, mb-4, input Bootstrap, Boxicons, badge, tabel table-striped, dan DataTables berbahasa Indonesia. Warna, tipografi, radius, bayangan, dan tombol mengikuti tema sekarang.

Empat kartu statistik: empat kolom desktop, dua tablet, satu ponsel. Label terlihat dan status tidak bergantung pada warna saja. Filter, statistik, tabel, dan ekspor harus memakai kriteria sama; filter tahun benar-benar terhubung, bukan nilai tetap.

### A. Daftar lembaga

```text
Lembaga / Monitoring Akreditasi PSTK           [Buat Laporan]
[Tahun | Program | Status | Reset]
[Wajib Lapor] [Sudah Dikirim] [Belum Dikirim] [Laporan Nihil]
[Program | Tahun | Predikat | Status | Kirim Terakhir | Aksi]
```

Baris per program–tahun. Wajib Lapor menghitung pasangan yang memenuhi masa akreditasi. Belum Dikirim mencakup belum dibuat dan draf. Status: Belum dibuat, Draf, Terkirim, Perbaikan belum dikirim; nihil sebagai badge tambahan. Aksi: Buat, Lanjutkan Draf, Lihat, Perbaiki, Riwayat. Jelaskan profil belum tertaut, sertifikat belum tersedia, atau tidak ada tahun yang memenuhi.

### B. Form

```text
Monitoring Akreditasi PSTK / Form Laporan          [Kembali]
[Lembaga | Program | Tahun | Sertifikat]
[Status | Disimpan terakhir | Kelengkapan]
[A. Identitas] [B. Pelaksanaan] [C. Sumber Daya] [D. Evaluasi]
[Kartu bagian aktif: nomor, label, input, bantuan, validasi]
[Sebelumnya] [Berikutnya]      [Simpan Draf] [Tinjau & Kirim]
```

- Empat tab nav-pills dalam satu form; berpindah tab mempertahankan isian.
- Identitas/angka pendek dua kolom desktop, narasi selebar kartu; ponsel satu kolom.
- Kelengkapan = jumlah pertanyaan wajib terisi dari pertanyaan yang berlaku.
- Isian profil memiliki bantuan untuk diperiksa kembali; sertifikat hanya-baca.
- Referensi tenaga/dokumen dapat dibuka pada pertanyaan terkait, tanpa otomatis mengonfirmasi jawaban.
- Beralih ke nihil tidak langsung menghapus jawaban draf; server mengabaikan jawaban yang tidak berlaku saat pengiriman.
- Tinjau & Kirim membuka ringkasan A–D dan konfirmasi kebenaran data.
- Kesalahan membuka tab terkait, memfokuskan input pertama, dan tampil pada ringkasan.
- Indikator proses mencegah klik ganda; kegagalan jaringan mempertahankan isian. Peringatkan saat meninggalkan perubahan belum tersimpan.

### C. Dashboard sekretariat

```text
Sekretariat / Monitoring & Evaluasi
[Tahun | Program | Predikat | Status Pelaporan]
[Cari Lembaga | Reset]
[Wajib Lapor] [Sudah Melapor] [Belum Melapor] [Laporan Nihil]
[Tabel]                                  [Ekspor CSV] [Cetak]
Lembaga | Program | Tahun | Predikat | Masa Berlaku
Status Pelaporan | Kegiatan | Peserta | Aksi
```

Tahun berjalan sebagai default. Baris per lembaga–program–tahun yang wajib melapor; satuan statistik dijelaskan. Belum melapor menampilkan — untuk kegiatan/peserta, bukan nol. Aksi hanya Lihat/Riwayat. CSV mencakup seluruh hasil filter, bukan halaman aktif. Cetak memuat judul, filter, waktu, tabel dan menyembunyikan navigasi/tombol. Rekap memakai versi terkirim terakhir, tanpa skor evaluasi baru.

### D. Detail dan riwayat

Header identitas, program, tahun, sertifikat, versi, waktu kirim. Empat kartu A–D berisi jawaban; kartu ringkasan memuat kegiatan, total peserta, internal, eksternal. Riwayat menunjukkan versi/waktu/pengirim dan membuka snapshot terkait. Draf perbaikan tidak terlihat oleh sekretariat. Sediakan keadaan kosong, pemuatan, gangguan sumber, dan referensi tidak tersedia.

## 5. Dry test dan aktivasi

### A. Dry run tanpa penulisan

Perintah yang akan dibuat:

```bash
php artisan monitoring:preflight
php artisan monitoring:import-profile-maps --file=... --dry-run
php artisan monitoring:import-accreditations --file=... --dry-run
```

Preflight memeriksa koneksi, akses baca, tabel/kolom, kandidat pemetaan, dan kelengkapan referensi. Dry run hanya parsing/validasi serta rencana perubahan, tanpa insert/update/delete.

Keluaran: jumlah valid, penambahan, data sama, konflik, ditolak; nomor baris dan alasan; profil tanpa pasangan, pemetaan ambigu, masa berlaku invalid, data resmi belum lengkap. Exit 0 bila siap, nonzero bila ada penghalang. Jangan mencetak kredensial, hash password, atau token. Pada database uji terisolasi, bandingkan isi sebelum/sesudah: wajib tidak berubah pada kedua sumber dan tabel monitoring.

### B. Simulasi staging

Gunakan salinan kedua database, akun uji, dan sertifikat bertanda simulasi. Penulisan hanya pada staging. Integrasi menggunakan MySQL agar perbedaan skema nyata teruji; middleware tetap aktif pada pengujian akses.

| Skenario | Hasil wajib |
|---|---|
| ID sama lintas sumber | Tidak mencampur data |
| Impor dua kali | Tidak duplikat |
| CSV konflik/ambigu | Ditolak dengan alasan |
| Akun tanpa profil | Tidak dapat membuat laporan; pesan jelas |
| Tamu/asesor/sekretariat membuka form | Ditolak sesuai auth/role |
| Lembaga mengganti UUID/ID pemilik | Tidak mendapat akses baca/tulis |
| Tahun di luar masa berlaku/mendatang | Ditolak |
| Sertifikat beririsan | Kewajiban/laporan tidak ganda |
| Draf parsial dibuka ulang | Jawaban utuh |
| Total peserta tidak sesuai | Pengiriman ditolak |
| Laporan nihil | Nol dan tidak berlaku sesuai aturan |
| Versi 1 lalu draf perbaikan | Sekretariat tetap melihat versi 1 |
| Kirim perbaikan | Versi 2 aktif; versi 1 tetap terbaca |
| Dua PIC bersamaan | Konflik terdeteksi, tidak menimpa |
| Gen1 gagal | Laporan tetap terbaca; referensi diberi pesan |
| Filter/statistik/tabel/ekspor | Total dan hasil konsisten |
| Ekspor JSON | Semua versi, data resmi, referensi tersedia |

### C. Dry test frontend

Uji browser pada 375, 768, dan 1440 px. Periksa kesesuaian tema; form tidak meluber; tabel bergulir hanya dalam kontainer; tab mempertahankan isian; validasi membuka bagian benar; radio/checkbox/pilihan jamak/draf/ringkasan/riwayat bekerja; semua filter termasuk tahun dan C; konsistensi statistik dan ekspor; keadaan kosong/draf/terkirim/nihil/perbaikan/konflik/gagal jaringan; akses keyboard, label, kesalahan; cetak sesuai filter tanpa navigasi.

Catat matriks lulus/gagal dan tangkapan layar daftar, form, detail, serta ponsel sebagai bukti.

### D. Urutan dan syarat aktivasi

1. Verifikasi profil, daftar resmi, akun, koneksi baca, lokasi berkas.
2. Jalankan preflight dan dry run impor; selesaikan konflik.
3. Implementasikan tabel, pembaca, otorisasi, form, validasi, nihil, versi.
4. Bangun frontend, rekap, filter, detail, ekspor, cetak.
5. Simulasikan di staging dan selesaikan kegagalan alur utama.
6. Cadangkan gen2, jalankan migration tambahan/impor tervalidasi, aktifkan melalui konfigurasi fitur.

Siap bila skenario wajib lulus, jumlah lembaga–program sesuai daftar resmi, dan ekspor utuh. Jika bermasalah, nonaktifkan konfigurasi tanpa menghapus laporan.

Asumsi tahap pertama: tanpa framework frontend baru, unggahan baru, peserta individual, atau persetujuan sekretariat. Implementasi aplikasi Laravel 12 menjadi pekerjaan lanjutan.
