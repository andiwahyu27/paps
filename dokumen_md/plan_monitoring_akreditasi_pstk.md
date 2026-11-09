# Rencana Monitoring Akreditasi PSTK

## 1. Tujuan dan keputusan

### Pembagian task implementasi dan status

Status berikut mencerminkan kondisi kode saat ini; “parsial” berarti masih ada bagian yang belum aman atau belum diuji untuk dipakai.

| Task | Hasil kerja | Status |
|---|---|---|
| 1. Fondasi data dan laporan | Migration tambahan, akses dua database, satu laporan per lembaga–program–tahun, validasi, draf, snapshot versi, dan konflik revisi | Kode selesai; dry run importer sekarang memeriksa konflik yang sama dengan mode tulis. Migration perlu dipastikan `Ran` pada database lokal sebelum aktivasi |
| 2. Form lembaga | Menu mandiri, panduan dan blok A–D, data awal, simpan draf, kirim, perbaikan | Parsial: kerangka form dan navigasi blok dibuat; validasi lengkap dan alur submit belum lulus uji |
| 3. Link sekretariat | Generate default/kustom, salin, edit kedaluwarsa, Hapus Link, generate ulang, audit, dan akses publik | Parsial: migration, token, route dan sebagian UI tersedia; pengelolaan tanggal kustom/ulang dan keamanan belum diuji menyeluruh |
| 4. Rekap dan dry test | Daftar semua pasangan wajib, filter/statistik, akses, staging, dry run, ekspor/cetak | Belum selesai: rekap baru merangkai laporan dan akreditasi untuk tahun terpilih; dry test staging belum dijalankan |

Task 1 menjadi dasar task 2 dan 3. Task 4 menutup validasi integrasi; aktivasi produksi hanya setelah seluruh task selesai dan skenario staging lulus.

Bangun modul sementara pada PAPS dengan membaca database gen1 dan gen2 serta menyimpan laporan baru di gen2. Migrasi penuh dilakukan saat perpindahan ke akreditasi-laravel12.

- Satu laporan per lembaga × program Prakom/Statistisi × tahun.
- Pengisian melalui link tanpa login atau menu mandiri oleh role lembaga (4). Sekretariat (2) memantau, mengelola link, merekap, mencetak, dan mengekspor.
- Kedua jalur menggunakan laporan yang sama; dua program pada satu lembaga memerlukan dua laporan per tahun.
- Lembaga boleh memperbaiki laporan; setiap pengiriman menyimpan versi tetap.
- Laporan nihil tetap disampaikan.
- Tahun laporan harus beririsan dengan masa akreditasi dan tidak melebihi tahun berjalan.
- Nomor sertifikat, predikat, dan masa berlaku berasal dari daftar resmi sekretariat.
- Kedua database tersedia dari server PAPS; autentikasi menu mandiri tetap memakai akun gen2; akses publik menggunakan token link terbatas pada satu laporan.
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
| monitoring_report_versions | Snapshot jawaban, identitas, akreditasi, pengirim nullable, kanal pengisian, nomor versi, waktu kirim |
| monitoring_report_links | Laporan, hash token unik, token terenkripsi, pembuat, waktu dibuat, kedaluwarsa, dan penghapusan |
| monitoring_report_link_audits | Aksi link, laporan/link terkait, pelaku, waktu, kedaluwarsa sebelum/sesudah; tanpa token mentah |

Keunikan lembaga–program–tahun ditegakkan di database. Impor Artisan menerima CSV pemetaan dan CSV akreditasi (sumber, ID profil asal, program, nomor sertifikat, predikat A/B/C, tanggal mulai/akhir). Impor idempoten; tolak pemetaan ambigu, tanggal terbalik, dan konflik data resmi.

Untuk Laravel 12, sediakan ekspor JSON `schema_version: 1` berisi UUID, referensi sumber, akreditasi, dan seluruh versi. Jawaban memakai nomor pertanyaan serta kode pilihan stabil yang terpisah dari label. Laporan tersimpan tetap terbaca saat gen1 gagal diakses.

### Pengelolaan link dan akses publik

- Sekretariat membuat link dari baris lembaga–program–tahun pada Monitoring & Evaluasi. Generate pertama membuat atau mengambil laporan secara transaksional; kendala unik mencegah duplikasi dengan pembuatan mandiri bersamaan.
- Aksi: **Generate Link**, **Salin Link**, **Edit Kedaluwarsa**, **Hapus Link**, dan **Generate Ulang**. Pembagian link dilakukan manual melalui salin, tanpa pengiriman email/WhatsApp otomatis.
- Generate menyediakan pilihan default **3 hari (72 jam sejak dibuat)** atau tanggal dan jam khusus yang harus lebih besar dari waktu server saat disimpan. Input dan tampilan memakai **Asia/Jakarta (WIB)**; penyimpanan waktu memakai UTC.
- Edit Kedaluwarsa memperpendek atau memperpanjang masa aktif tanpa mengganti URL. Link kedaluwarsa dapat aktif kembali dengan waktu baru di masa depan.
- Hapus Link memerlukan konfirmasi dan langsung menonaktifkan akses, tanpa menghapus laporan, draf, versi, atau riwayat audit. Link yang dihapus tidak dapat diaktifkan kembali melalui edit tanggal.
- Generate Ulang menghasilkan token baru dan menonaktifkan token lama secara transaksional. Pilih kembali default 72 jam atau waktu khusus; jawaban serta versi dipertahankan. Generate biasa pada link yang sudah ada menampilkan statusnya dan mengarahkan ke aksi pengelolaan, bukan membuat token tambahan.
- Status link: Belum dibuat, Aktif, Kedaluwarsa, Dihapus. Generate ulang menonaktifkan link lama dengan catatan audit penggantian. Hanya satu link aktif untuk satu laporan.
- Token acak minimal 256 bit; simpan hash unik untuk pencarian dan nilai terenkripsi untuk salin ulang. Token mentah tidak masuk log atau ekspor. Audit mencatat generate, edit kedaluwarsa, hapus, dan generate ulang beserta pelaku dan nilai waktu sebelum/sesudah.
- Endpoint publik `/monitoring-akreditasi-pstk/form/{token}` menyediakan buka form, simpan draf, kirim, dan bukti kirim. Daftarkan jalur ini di luar middleware login/role lembaga. Setiap request memeriksa token, penghapusan, dan batas kedaluwarsa berdasarkan waktu server; saat waktu server sama dengan batas, akses sudah ditolak.
- Lembaga, program, dan tahun ditentukan dari laporan pada server, bukan payload pengguna. Token hanya mengizinkan satu laporan. Link aktif mengizinkan draf serta pengiriman perbaikan sampai batas akses.
- Endpoint sekretariat untuk generate, edit kedaluwarsa, hapus, dan generate ulang wajib autentikasi serta role sekretariat. Terapkan CSRF, pembatasan request, `Cache-Control: no-store`, dan `Referrer-Policy: no-referrer` pada jalur publik; hindari analitik pihak ketiga yang membawa URL token.
- Catat kanal snapshot: akun lembaga atau link sekretariat. Pengirim akun boleh kosong pada pengisian publik dan tidak boleh dianggap pengguna terautentikasi.
- Akses mandiri tetap memeriksa role lembaga dan kepemilikan profil; laporan dari link muncul di menu akun yang sesuai. Kedaluwarsa atau penghapusan link tidak membatasi akses mandiri. Penolakan role memakai 403, bukan 503.
- Kedua jalur berbagi draf, validasi, nihil, snapshot, dan pemeriksaan revisi. Tambahkan migration baru yang mempertahankan tabel serta laporan lama.

## 3. Pemetaan instrumen dan aturan form

Nomor dan pilihan instrumen dipertahankan. Isian awal dikonfirmasi lembaga; koreksi kontak disimpan pada laporan tanpa mengubah profil utama atau penguncian pengajuan.

| No. | Pertanyaan | Sumber/perlakuan |
|---|---|---|
| 1 | Nama lembaga | Profil pemilik laporan, dari akun atau link; pemilik tidak dapat diganti |
| 2 | Program terakreditasi | Daftar resmi milik lembaga; dikunci setelah laporan dipilih melalui link atau menu mandiri |
| 3 | Alamat | Isi awal alamat_lembaga; boleh dikoreksi |
| 4 | Telepon | Isi awal telepon; boleh dikoreksi |
| 5 | Email | Email profil lembaga |
| 6 | Website | Isi awal website; boleh kosong |
| 7 | Nomor sertifikat | Daftar resmi sekretariat |
| 8 | Predikat A/B/C | Daftar resmi; predikat gen2 sebagai pembanding |
| 9 | Tahun pemantauan | Tahun yang memenuhi masa akreditasi |
| 10 | Jumlah pelatihan | Diisi lembaga; bukan hitungan data pengajuan |
| 11 | Jumlah peserta | Diisi lembaga |
| 12 | Peserta internal/eksternal | Objek `answers[12]` berisi `internal` dan `external`; keduanya bilangan bulat nonnegatif dan jumlahnya sama dengan nomor 11 |
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
- Pemeriksaan nomor revisi mencegah dua tab/PIC, termasuk akses akun dan link bersamaan, saling menimpa. Konflik memberi kesempatan menyalin perubahan sebelum memuat versi terbaru.
- Rute lembaga `/monitoring-akreditasi-pstk`: daftar, buat, draf, detail, kirim, riwayat. Semua tindakan memeriksa auth, role, dan kepemilikan di server.
- Rekap sekretariat menggunakan `/monitoring-evaluasi` dengan detail dan ekspor tersendiri.

## 4. Layout frontend

### Gaya visual

Gunakan layouts.app-lembaga dan layouts.app-sekretariat; halaman publik memakai header PAPS sederhana dengan komponen isi form yang sama; aset Blade/Bootstrap yang sudah tersedia. Ikuti container-xxl, breadcrumb, card, mb-4, input Bootstrap, Boxicons, badge, tabel table-striped, dan DataTables berbahasa Indonesia. Warna, tipografi, radius, bayangan, dan tombol mengikuti tema sekarang.

Empat kartu statistik: empat kolom desktop, dua tablet, satu ponsel. Label terlihat dan status tidak bergantung pada warna saja. Filter, statistik, tabel, dan ekspor harus memakai kriteria sama; filter tahun benar-benar terhubung, bukan nilai tetap.

### A. Daftar lembaga

```text
Lembaga / Monitoring Akreditasi PSTK           [Buat Laporan]
[Tahun | Program | Status | Reset]
[Wajib Lapor] [Sudah Dikirim] [Belum Dikirim] [Laporan Nihil]
[Program | Tahun | Predikat | Status | Kirim Terakhir | Aksi]
```

Baris per program–tahun. Wajib Lapor menghitung pasangan yang memenuhi masa akreditasi. Belum Dikirim mencakup belum dibuat dan draf. Status: Belum dibuat, Draf, Terkirim, Perbaikan belum dikirim; nihil sebagai badge tambahan. Aksi: Buat, Lanjutkan Draf, Lihat, Perbaiki, Riwayat. Jelaskan profil belum tertaut, sertifikat belum tersedia, atau tidak ada tahun yang memenuhi.

### B. Form per blok

Gunakan pola sidebar dan kartu aktif pada gambar referensi pengguna dengan warna, tipografi, kartu, dan tombol tema PAPS. Tidak menyertakan fitur wawancara, rekaman, atau AI dari contoh.

```text
Monitoring Akreditasi PSTK / Form Laporan
[Lembaga | Program | Tahun | Sertifikat]
[Status | Disimpan terakhir | Progres | Batas akses link (WIB)]
[Daftar blok di kiri]          [Kartu blok aktif di kanan]
Panduan                       Nomor, label, bantuan, wajib, input, validasi
A. Identitas (1–9)
B. Pelaksanaan (10–16)
C. Sumber Daya (17–21)
D. Evaluasi (22–26)
                              [Sebelumnya] [Simpan Draf] [Selanjutnya]
```

- Panduan Pengisian Laporan Monev menjadi blok awal. Jelaskan satu laporan per program–tahun, periode kegiatan, pemeriksaan data awal, nihil, draf, kirim, perbaikan, dan masa berlaku link.
- Blok A: Identitas Lembaga; B: Pelaksanaan Pelatihan Setelah Terakreditasi; C: Sumber Daya Pelatihan; D: Evaluasi dan Tindak Lanjut. Hanya satu blok tampil; penanda blok aktif jelas. Pada ponsel, navigasi berada di atas kartu.
- Identitas/angka pendek dua kolom desktop, narasi selebar kartu; ponsel satu kolom. Setiap pertanyaan menampilkan nomor, label, bantuan, tanda wajib, dan kesalahan.
- Progres menghitung pertanyaan wajib terisi yang berlaku, tanpa memasukkan panduan.
- Isian profil diberi bantuan untuk diperiksa; sertifikat hanya-baca. Lembaga, program, dan tahun laporan terpilih dikunci.
- Referensi tenaga/dokumen dapat dibuka tanpa otomatis mengonfirmasi jawaban. Beralih ke nihil tidak langsung menghapus draf; jawaban yang tidak berlaku diabaikan server saat kirim.
- Simpan Draf, Sebelumnya, dan Selanjutnya berada di bawah kartu. Perpindahan blok, termasuk lewat sidebar, menyimpan draf parsial. Jika gagal, pertahankan input dan tunda perpindahan.
- Blok D menyediakan Tinjau & Kirim: ringkasan A–D dan konfirmasi kebenaran data sebelum kirim. Validasi membuka blok terkait dan memfokuskan input pertama yang salah.
- Indikator proses mencegah klik ganda. Kegagalan jaringan mempertahankan isian; peringatkan saat meninggalkan perubahan belum tersimpan.
- Link kedaluwarsa atau dihapus menampilkan pesan untuk menghubungi sekretariat atau login melalui menu mandiri. Halaman yang sudah terbuka tetap harus ditolak server saat menyimpan setelah akses berakhir.

### C. Dashboard sekretariat

```text
Sekretariat / Monitoring & Evaluasi
[Tahun | Program | Predikat | Status Pelaporan]
[Cari Lembaga | Reset]
[Wajib Lapor] [Sudah Melapor] [Belum Melapor] [Laporan Nihil]
[Tabel]                                  [Ekspor CSV] [Cetak]
Lembaga | Program | Tahun | Predikat | Masa Berlaku
Status Pelaporan | Status Link | Kedaluwarsa (WIB) | Kegiatan | Peserta | Aksi
```

Tahun berjalan sebagai default. Baris per lembaga–program–tahun yang wajib melapor; satuan statistik dijelaskan. Belum melapor menampilkan — untuk kegiatan/peserta, bukan nol. Aksi Lihat/Riwayat serta Generate Link, Salin Link, Edit Kedaluwarsa, Hapus Link, dan Generate Ulang sesuai status. Dialog generate/edit menampilkan lembaga, program, tahun, pilihan default 3 hari atau tanggal/jam khusus, dan label WIB. Seluruh pasangan wajib lapor ditampilkan walaupun belum membuat laporan. Status link terpisah dari status pelaporan; generate tidak berarti sudah melapor. CSV mencakup seluruh hasil filter, bukan halaman aktif. Cetak memuat judul, filter, waktu, tabel dan menyembunyikan navigasi/tombol. Rekap memakai versi terkirim terakhir, tanpa skor evaluasi baru.

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
| Tamu/asesor/sekretariat membuka jalur akun | Tamu diarahkan login; role selain lembaga ditolak 403 |
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
| Satu lembaga, dua program, satu tahun | Dua laporan dan dua link terpisah |
| Generate bersamaan dengan pembuatan mandiri | Tidak menghasilkan laporan ganda |
| Default 3 hari dan waktu khusus WIB | Tepat 72 jam atau waktu pilihan; disimpan UTC; waktu lampau ditolak |
| Edit kedaluwarsa | URL tetap; batas akses berubah; link kedaluwarsa dapat diaktifkan kembali |
| Hapus Link | Langsung menolak baca/tulis termasuk halaman terbuka; jawaban dan versi tetap utuh |
| Edit tanggal link dihapus | Ditolak; harus Generate Ulang |
| Generate Ulang | Token lama ditolak; URL baru memakai 72 jam atau waktu khusus; jawaban tetap utuh |
| Tamu memakai link aktif | Dapat membuka, menyimpan, melanjutkan draf, mengirim, dan melihat bukti kirim |
| Token untuk laporan lain atau payload pemilik diganti | Tidak memberi akses lintas laporan |
| Simpan/kirim tepat pada atau setelah batas waktu | Ditolak oleh server |
| Link kedaluwarsa/dihapus lalu akses mandiri | Akun lembaga pemilik tetap dapat melapor |
| Draf link dibuka melalui akun lembaga | Draf sama; lembaga lain tidak mendapat akses |
| Akses link dan akun bersamaan | Konflik revisi terdeteksi; tidak saling menimpa |
| Kirim melalui kedua jalur | Validasi, nihil, versi, statistik, dan ekspor konsisten |
| Audit pengelolaan link | Pelaku, aksi, waktu, perubahan kedaluwarsa tercatat tanpa token mentah |


### C. Dry test frontend

Uji browser pada 375, 768, dan 1440 px. Periksa kesesuaian tema; form tidak meluber; tabel bergulir hanya dalam kontainer; panduan serta navigasi blok A–D mempertahankan isian dan menyimpan saat berpindah; progres benar; validasi membuka bagian benar; radio/checkbox/pilihan jamak/draf/ringkasan/riwayat bekerja; semua filter termasuk tahun dan C; konsistensi statistik dan ekspor; keadaan kosong/draf/terkirim/nihil/perbaikan/konflik/gagal jaringan; akses keyboard, label, kesalahan; cetak sesuai filter tanpa navigasi.

Uji dialog generate/edit, WIB, salin, konfirmasi Hapus Link, generate ulang, dan pesan kedaluwarsa/dihapus. Simulasi perubahan link dan pengiriman hanya pada database staging.

Catat matriks lulus/gagal dan tangkapan layar daftar, form, detail, serta ponsel sebagai bukti.

### D. Urutan dan syarat aktivasi

1. Verifikasi profil, daftar resmi, akun, koneksi baca, lokasi berkas.
2. Jalankan preflight dan dry run impor; selesaikan konflik.
3. Implementasikan tabel, pembaca, otorisasi, form, validasi, nihil, versi.
4. Bangun akses publik, pengelolaan link dan audit, form per blok, rekap, filter, detail, ekspor, cetak.
5. Simulasikan di staging dan selesaikan kegagalan alur utama.
6. Cadangkan gen2, jalankan migration tambahan/impor tervalidasi, aktifkan melalui konfigurasi fitur.

Siap bila skenario wajib lulus, jumlah lembaga–program sesuai daftar resmi, dan ekspor utuh. Jika bermasalah, nonaktifkan konfigurasi tanpa menghapus laporan.

Asumsi tahap pertama: tanpa framework frontend baru, unggahan baru, peserta individual, atau persetujuan sekretariat. Implementasi aplikasi Laravel 12 menjadi pekerjaan lanjutan.

Revisi ini merupakan dokumen perencanaan. Perubahan aplikasi, commit, dan push menjadi tahap implementasi berikutnya. Gunakan istilah **Hapus Link** secara konsisten pada antarmuka, pesan, dan pengujian.
