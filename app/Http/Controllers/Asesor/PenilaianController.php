<?php

namespace App\Http\Controllers\Asesor;

use App\Helpers\Uploadfile;
use App\Http\Controllers\Controller;
use App\Models\DokumenPelatihan;
use App\Models\DigitalSignature;
use App\Models\Fasilitas;
use App\Models\Item;
use App\Models\Pelatihan;
use App\Models\Pengajuan;
use App\Models\Penilaian;
use App\Models\Profile;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKerja;
use App\Models\RiwayatPelatihan;
use App\Models\RiwayatPendidikan;
use App\Models\Subunsur;
use App\Models\Tenaga;
use App\Models\TenagaPelatihan;
use App\Models\Unsur;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\TemplateProcessor;

class PenilaianController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function buktidukung($id_pengajuan, $kode)
    {
        $result = [];
        $step = null;
        $pengajuan = Pengajuan::find($id_pengajuan);
        $profile = $pengajuan->profile;
        $fasilitator = Tenaga::where('jenis_tenaga', 1)->get();
        $pengelola = Tenaga::where('jenis_tenaga', 2)->get();
        $tenaga = TenagaPelatihan::where('id_pelatihan', $id_pengajuan)->get();
        $item = Item::where('kode_item', $kode)->first();
        $r_jabatan = RiwayatJabatan::join('tb_tenaga', 'tb_riwayat_jabatan.tenaga_id', '=', 'tb_tenaga.id')
            ->where('tb_tenaga.id_profile', $profile->id)
            ->get();
        $r_kerja = RiwayatKerja::join('tb_tenaga', 'tb_riwayat_kerja.tenaga_id', '=', 'tb_tenaga.id')
            ->where('tb_tenaga.id_profile', $profile->id)
            ->get();
        $r_pelatihan = RiwayatPelatihan::join('tb_tenaga', 'tb_riwayat_pelatihan.tenaga_id', '=', 'tb_tenaga.id')
            ->where('tb_tenaga.id_profile', $profile->id)
            ->get();
        $r_pendidikan = RiwayatPendidikan::join('tb_tenaga', 'tb_riwayat_pendidikan.tenaga_id', '=', 'tb_tenaga.id')
            ->where('tb_tenaga.id_profile', $profile->id)
            ->get();
        switch ($kode) {
            case '1.1.1':
                $data = [];
                $mapping = [
                    'nomor_sk_pemerintah' => 'sk_pemerintah',
                    'no_surat_izin_operasional' => 'uraian_tugas',
                ];

                foreach ($mapping as $property => $item) {
                    if ($profile->$property) {
                        $data[] = $this->getItem($profile, $item);
                    }
                }

                $result[] = [
                    'pelatihan' => '1.1.1. Kelembagaan',
                    'data' => $data,
                ];
                break;
            case '2.1.1':
                $result[] = [
                    'pelatihan' => '2.1.1. Kompetensi Fasilitator',
                    'data' => [$this->getItem($profile, 'fasilitator')],
                ];
                $step = 1;
                break;
            case '2.2.1':
                $result[] = [
                    'pelatihan' => '2.2.1. Pengelola Pelatihan (MOT)',
                    'data' => [$this->getItem($profile, 'tenaga_diklat')],
                ];
                $step = 2;
                break;

            case '2.3.1':
                $result[] = [
                    'pelatihan' => '2.3.1. Pengelola Kelas',
                    'data' => [$this->getItem($profile, 'tenaga_kelas')],
                ];
                $step = 3;
                break;

            case '2.4.1':
                $result[] = [
                    'pelatihan' => '2.4.1. Pengelola Sistem Informasi',
                    'data' => [$this->getItem($profile, 'tenaga_si')],
                ];
                $step = 4;
                break;

            case '2.5.1':
                $result[] = [
                    'pelatihan' => '2.5.1. Analis Kebutuhan Diklat',
                    'data' => [$this->getItem($profile, 'tenaga_analis')],
                ];
                $step = 5;
                break;
            case '3.1.1':
                $data = [];
                $data[] = $this->getItem($profile, 'sarpras_umum');
                $data[] = $this->getItem($profile, 'sarpras_diklat');
                $result[] = [
                    'pelatihan' => '3.1.1. Sarana dan Prasarana Lembaga Penyelenggara PSTK',
                    'data' => $data,
                ];
                break;
            case '4.1.1':
                $data = [];
                if ($profile->path_rencana_keiatan) {
                    $data[] = $this->getItem($profile, 'dok_rencana');
                }
                if ($profile->path_kegiatan_diklat) {
                    $data[] = $this->getItem($profile, 'dok_kalender');
                }
                $result[] = [
                    'pelatihan' => '4.1.1. Program Pelatihan',
                    'data' => $data,
                ];
                break;
            case '4.2.1':
                $id_pelatihanList = Pelatihan::where('id_pengajuan', $pengajuan->id)->pluck('id');
                $dokumenIds = [3, 4, 5, 6];
                $mapping = [
                    3 => 'dok_perencanaan_program',
                    4 => 'dok_laporan_penyelenggaraan',
                    5 => 'sertifikat_peserta',
                    6 => 'diseminasi_publikasi_perencanaan_program',
                ];

                foreach ($id_pelatihanList as $index => $id_pelatihan) {
                    $pelatihan = Pelatihan::find($id_pelatihan);
                    if (!$pelatihan) {
                        continue; // Skip kalau pelatihan tidak ditemukan
                    }

                    $dokumenList = DokumenPelatihan::where('id_pelatihan', $id_pelatihan)
                        ->whereIn('dokumen_id', $dokumenIds)
                        ->get()
                        ->groupBy('dokumen_id');

                    $pelatihanData = [];

                    foreach ($dokumenIds as $docId) {
                        if (isset($dokumenList[$docId])) {
                            $pelatihanData[] = $this->getItem($id_pelatihan, $mapping[$docId]);
                        }
                    }

                    $result[] = [
                        'pelatihan' => 'Pelatihan '.($index + 1).': '.$pelatihan->nama,
                        'data' => $pelatihanData,
                    ];
                }
                break;

            case '4.2.2':
                $id_pelatihanList = Pelatihan::where('id_pengajuan', $pengajuan->id)->pluck('id');
                $dokumenIds = [7, 8];
                $mapping = [
                    7 => 'hasil_evaluasi_kepuasan',
                    8 => 'tingkat_kelulusan_peserta',
                ];

                foreach ($id_pelatihanList as $index => $id_pelatihan) {
                    $pelatihan = Pelatihan::find($id_pelatihan);
                    if (!$pelatihan) {
                        continue; // Skip kalau pelatihan tidak ditemukan
                    }

                    $dokumenList = DokumenPelatihan::where('id_pelatihan', $id_pelatihan)
                        ->whereIn('dokumen_id', $dokumenIds)
                        ->get()
                        ->groupBy('dokumen_id');

                    $pelatihanData = [];

                    foreach ($dokumenIds as $docId) {
                        if (isset($dokumenList[$docId])) {
                            $pelatihanData[] = $this->getItem($id_pelatihan, $mapping[$docId]);
                        }
                    }

                    $result[] = [
                        'pelatihan' => 'Pelatihan '.($index + 1).': '.$pelatihan->nama,
                        'data' => $pelatihanData,
                    ];
                }
                break;
            case '5.1.1':
                $data = [];
                if ($profile->path_pembiayaan) {
                    $data[] = $this->getItem($profile, 'dok_biaya');
                }
                $result[] = [
                    'pelatihan' => '5.1.1. Standar Biaya Pelatihan PSTK',
                    'data' => $data,
                ];
                break;
            case '6.1.1':
                $data = [];
                $data[] = $this->getItem($profile, 'sop_rencana');
                $data[] = $this->getItem($profile, 'sop_pelaksanaan');
                $data[] = $this->getItem($profile, 'sop_evalap');
                $data[] = $this->getItem($profile, 'bukti_sosialisasi_sop');
                $data[] = $this->getItem($profile, 'laporan_penjaminan_mutu');

                $result[] = [
                    'pelatihan' => '6.1.1. Standar Mutu',
                    'data' => $data,
                ];
                break;
            case '6.2.1':
                $id_pelatihanList = Pelatihan::where('id_pengajuan', $pengajuan->id)->pluck('id');
                $dokumenIds = [1, 2];
                $mapping = [
                    1 => 'jadwal_pelatihan_pstk',
                    2 => 'bukti_persetujuan',
                ];

                foreach ($id_pelatihanList as $index => $id_pelatihan) {
                    $pelatihan = Pelatihan::find($id_pelatihan);
                    if (!$pelatihan) {
                        continue; // Skip kalau pelatihan tidak ditemukan
                    }

                    $dokumenList = DokumenPelatihan::where('id_pelatihan', $id_pelatihan)
                        ->whereIn('dokumen_id', $dokumenIds)
                        ->get()
                        ->groupBy('dokumen_id');

                    $pelatihanData = [];

                    foreach ($dokumenIds as $docId) {
                        if (isset($dokumenList[$docId])) {
                            $pelatihanData[] = $this->getItem($id_pelatihan, $mapping[$docId]);
                        }
                    }

                    $result[] = [
                        'pelatihan' => 'Pelatihan '.($index + 1).': '.$pelatihan->nama,
                        'data' => $pelatihanData,
                    ];
                }
                break;
        }

        return view('asesor.bukti-dukung', [
            'result' => $result,
            'item' => $item,
            'r_jabatans' => $r_jabatan,
            'r_kerjas' => $r_kerja,
            'r_pelatihans' => $r_pelatihan,
            'r_pendidikans' => $r_pendidikan,
            'fasilitator' => $fasilitator,
            'pengelola' => $pengelola,
            'tenaga' => $tenaga,
            'kode' => $kode,
            'step' => $step,
            'pengajuan' => $pengajuan,
        ]);
        // return view('asesor.bukti-dukung', compact($result));
    }

    private function getItem($profile, $bukti)
    {
        $type = 'paragraph';
        $title = '';
        $text = '';
        // dd($bukti);
        switch ($bukti) {
            case 'sk_pemerintah':
                $title = 'SK Pendirian Lembaga dan Uraian Tupoksi/SOP/SOTK Lembaga Diklat';
                if ($profile->path_sk_pemerintah) {
                    $text = 'Nomor: '.$profile->no_surat_izin_operasional.'<br> Tanggal: '.$profile->tanggal_surat_izin_operasional.'<br> Penerbit: '.$profile->penerbit_surat_izin_operasional.'<br><a href="'.asset($profile->path_sk_pemerintah).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'uraian_tugas':
                $title = 'Uraian Tugas sebagai Lembaga Pelatihan';
                if ($profile->path_surat_izin_operasional) {
                    $text = 'Nomor: '.$profile->no_surat_izin_operasional.'<br> Tanggal: '.$profile->tanggal_surat_izin_operasional.'<br> Penerbit: '.$profile->penerbit_surat_izin_operasional.'<br><a href="'.asset($profile->path_surat_izin_operasional).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'akte_swasta':
                $title = 'Akte Pendirian';
                if ($profile->path_akte_pendirian) {
                    $text = $profile->nomor_akte_pendirian.'<br>'.$profile->tanggal_akte_pendirian.'<br>'.$profile->ttd_akte_pendirian.'<br><a href="'.asset($profile->path_akte_pendirian).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'sop_rencana':
                $title = 'SOP Perencanaan Pelatihan';
                if ($profile->path_sop_perencanaan) {
                    $text = '<a href="'.asset($profile->path_sop_perencanaan).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'sop_pelaksanaan':
                $title = 'SOP Pelaksanaan Pelatihan';
                if ($profile->path_sop_pelaksanaan) {
                    $text = '<a href="'.asset($profile->path_sop_pelaksanaan).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'sop_evalap':
                $title = 'SOP Evaluasi & Pelaporan Pelatihan';
                if ($profile->path_sop_evalap) {
                    $text = '<a href="'.asset($profile->path_sop_evalap).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'bukti_sosialisasi_sop':
                $title = 'Bukti sosialisasi SOP';
                if ($profile->path_bukti_sosialisasi_sop) {
                    $text = '<a href="'.asset($profile->path_bukti_sosialisasi_sop).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'laporan_penjaminan_mutu':
                $title = 'Laporan Penjaminan Mutu';
                if ($profile->path_laporan_penjaminan_mutu) {
                    $text = '<a href="'.asset($profile->path_laporan_penjaminan_mutu).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'fasilitator':
                $title = 'Fasilitator';
                $text = Tenaga::where('id_profile', $profile->id)->where('jenis_tenaga', 1)->get();
                $type = 'tabel_tenaga';
                break;
            case 'tenaga_diklat':
                $title = 'Pengelola Pelatihan';
                $text = Tenaga::where('id_profile', $profile->id)->where('jenis_tenaga', 2)->get();
                $type = 'tabel_tenaga';
                break;
            case 'tenaga_kelas':
                $title = 'Pengelola Kelas';
                $text = Tenaga::where('id_profile', $profile->id)->where('jenis_tenaga', 3)->get();
                $type = 'tabel_tenaga';
                break;
            case 'tenaga_si':
                $title = 'Pengelola Sistem Informasi';
                $text = Tenaga::where('id_profile', $profile->id)->where('jenis_tenaga', 4)->get();
                $type = 'tabel_tenaga';
                break;
            case 'tenaga_analis':
                $title = 'Analis Kebutuhan Diklat';
                $text = Tenaga::where('id_profile', $profile->id)->where('jenis_tenaga', 5)->get();
                $type = 'tabel_tenaga';
                break;
            case 'sarpras_umum':
                $title = 'Sarana Prasarana Umum';
                $text = Fasilitas::where('id_profile', $profile->id)->where('tipe', 1)->get();
                $type = 'tabel_sarpras';
                break;
            case 'sarpras_diklat':
                $title = 'Sarana Prasarana Pelatihan';
                $text = Fasilitas::where('id_profile', $profile->id)->where('tipe', 2)->get();
                $type = 'tabel_sarpras';
                break;
            case 'dok_rencana':
                $title = 'Dokumen Perencanaan Kegiatan Organisasi';
                if ($profile->path_rencana_keiatan) {
                    $text = '<a href="'.asset($profile->path_rencana_keiatan).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'dok_kalender':
                $title = 'Kalender Kegiatan Pelatihan';
                if ($profile->path_kegiatan_diklat) {
                    $text = '<a href="'.asset($profile->path_kegiatan_diklat).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'dok_biaya':
                $title = 'SBM/standar biaya pelatihan sesuai ketentuan berlaku';
                if ($profile->path_pembiayaan) {
                    $text = '<a href="'.asset($profile->path_pembiayaan).'" target="_blank"><i class="bx bxs-file-pdf"></i> Lihat File</a>';
                } else {
                    $text = '<span class="text-danger">File tidak ditemukan</span>';
                }
                break;
            case 'jadwal_pelatihan_pstk':
                $title = 'Jadwal Pelatihan yang Disusun oleh Lembaga Penyelenggara PSTK';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 1)->get();
                $type = 'program';
                break;
            case 'bukti_persetujuan':
                $title = 'Bukti Persetujuan dari BPS terkait Pengembangan Kurikulum';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 2)->get();
                $type = 'program';
                break;
            case 'dok_perencanaan_program':
                $title = 'Dokumen Perencanaan Program Pelatihan';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 3)->get();
                $type = 'program';
                break;
            case 'dok_laporan_penyelenggaraan':
                $title = 'Dokumen Laporan Penyelenggaraan Pelatihan';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 4)->get();
                $type = 'program';
                break;
            case 'sertifikat_peserta':
                $title = 'Sertifikat Peserta';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 5)->get();
                $type = 'program';
                break;
            case 'diseminasi_publikasi_perencanaan_program':
                $title = 'Diseminasi atau Publikasi Perencanaan Program Pelatihan';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 6)->get();
                $type = 'program';
                break;
            case 'hasil_evaluasi_kepuasan':
                $title = 'Hasil Evaluasi Kepuasan';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 7)->get();
                $type = 'program';
                break;
            case 'tingkat_kelulusan_peserta':
                $title = 'Tingkat Kelulusan Peserta';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 8)->get();
                $type = 'program';
                break;
                // case 'kalender_diklat':
                //     $title = 'Kalender Diklat';
                //     $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 12)->get();
                //     $type = 'program';
                //     break;
                // case 'bahan_ajar':
                //     $title = 'Materi/Bahan Ajar';
                //     $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 13)->get();
                //     $type = 'program';
                //     break;
                // case 'jadwal_pelatihan':
                //     $title = 'Jadwal Pelatihan';
                //     $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 14)->get();
                //     $type = 'program';
                //     break;
                // case 'daftar_program':
                //     $title = 'Daftar Program';
                //     $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 15)->get();
                //     $type = 'program';
                //     break;
                // case 'hasil_ikp':
                //     $title = 'Hasil IKP';
                //     $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 16)->get();
                //     $type = 'program';
                //     break;
            case 'ba_ujian_sertifikasi':
                $title = 'Berita Acara Ujian Sertifikasi';
                $text = DokumenPelatihan::where('id_pelatihan', $profile)->where('dokumen_id', 17)->get();
                $type = 'program';
                break;
        }

        $result = ['title' => $title, 'text' => $text, 'type' => $type];

        return $result;
    }

    // Penilaian Pra Visit
    private function calculatePravisitData($id, $checkValidation = false)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $checkasesor = $pengajuan->checkasesor();
        switch ($checkasesor) {
            case 1:
                $asesor = $pengajuan->id_asesor1;
                break;
            case 2:
                $asesor = $pengajuan->id_asesor2;
                break;
            case 3:
                $asesor = $pengajuan->id_asesor3;
                break;
        }
        // $asesor = auth()->user()->id;

        // Load all penilaian data at once
        $allPenilaian = Penilaian::where('id_pengajuan', $id)
            ->where('id_asesor', $asesor)
            ->get()
            ->groupBy(['id_item_penilaian', 'pra_paska']);

        $data = [];
        $nilai_akhir = 0;
        $isValid = true;

        $unsurs = Unsur::with(['subunsurs.items'])->get();
        foreach ($unsurs as $unsur) {
            $temp_su = [];
            $totalNilaiSubunsurUnsur = 0;

            foreach ($unsur->subunsurs as $subunsur) {
                $temp_items = [];
                $totalNilaiItemSubunsur = 0;

                foreach ($subunsur->items as $item) {
                    $penilaianItem = $allPenilaian->get($item->id, []);
                    $penilaianpra = isset($penilaianItem['pra']) ? collect($penilaianItem['pra'])->first() : null;

                    // Hitung nilai pra
                    $nilaipra = $penilaianpra ? $penilaianpra->nilai : 0;
                    $nilai_bobot = round(($nilaipra * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsur += $nilai_bobot;

                    if ($checkValidation && !$penilaianpra) {
                        $isValid = false;
                    }

                    $temp_items[] = [
                        'id' => $item->id,
                        'kode_item' => $item->kode_item,
                        'nama_item' => $item->nama_item,
                        'bobot_item' => $item->bobot_item,
                        'nilaipra' => $nilaipra,
                        'nilai_bobot' => $nilai_bobot,
                    ];
                }

                // Setelah semua subunsur selesai:
                $nilai_bobot_subunsur = round(($totalNilaiItemSubunsur * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsur += $nilai_bobot_subunsur;

                $temp_su[] = [
                    'id_su' => $subunsur->id,
                    'su' => $subunsur->nama_subunsur,
                    'bobot_subunsur' => $subunsur->bobot_subunsur,
                    'nilai_bobot_subunsur' => $nilai_bobot_subunsur,
                    'items' => $temp_items,
                ];
            }

            $nilai_bobot_unsur = round(($totalNilaiSubunsurUnsur * $unsur->bobot_unsur) / 100, 2);
            $nilai_akhir += $nilai_bobot_unsur;

            $data[] = [
                'id_uu' => $unsur->id,
                'unsur' => $unsur->nama_unsur,
                'bobot_unsur' => $unsur->bobot_unsur,
                'nilai_bobot_unsur' => $nilai_bobot_unsur,
                'nilai_akhir' => $nilai_akhir,
                'subunsurs' => $temp_su,
            ];
        }

        return [
            'data' => $data,
            'pengajuan' => $pengajuan,
            'nilai_akhir' => round($nilai_akhir, 2),
            'isValid' => $isValid,
        ];
    }

    public function pravisit($id)
    {
        $result = $this->calculatePravisitData($id);
        $predikat = $this->getPredikat($result['nilai_akhir']);

        return view('asesor.pravisit', array_merge($result, [
            'predikat' => $predikat,
            'isHistory' => false,
        ]));
    }

    public function pravisitView($id)
    {
        $result = $this->calculatePravisitData($id);
        $predikat = $this->getPredikat($result['nilai_akhir']);

        return view('asesor.pravisit', array_merge($result, [
            'predikat' => $predikat,
            'isHistory' => true,
        ]));
    }

    public function editPravisit($id)
    {
        $asesor = Pengajuan::getasesor($id);

        if (!$asesor) {
            abort(403);
        }

        Pengajuan::where('id', $id)->update([
            "pra_visit_asesor{$asesor}" => 0,
        ]);

        return redirect()->route('pravisit', $id);
    }

    // Penilaian Pra Visitasi 2
    private function calculatePravisit2Data($id, $checkValidation = false)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $asesor1 = $pengajuan->id_asesor1;
        $asesor2 = $pengajuan->id_asesor2;
        $asesor3 = $pengajuan->id_asesor3;
        $asesorIds = [$pengajuan->id_asesor1, $pengajuan->id_asesor2, $pengajuan->id_asesor3];

        // Load all penilaian data at once
        $allPenilaian = Penilaian::where('id_pengajuan', $id)
            ->whereIn('id_asesor', $asesorIds)
            ->get()
            ->groupBy(['id_item_penilaian', 'pra_paska']);

        $data = [];
        $nilai_akhir = 0;
        $isValid = true;

        $unsurs = Unsur::with(['subunsurs.items'])->get();
        foreach ($unsurs as $unsur) {
            $temp_su = [];
            $totalNilaiSubunsurUnsur = 0;

            foreach ($unsur->subunsurs as $subunsur) {
                $temp_items = [];
                $totalNilaiItemSubunsur = 0;

                foreach ($subunsur->items as $item) {
                    $penilaianpra_1 = Penilaian::where('id_pengajuan', $id)->where('id_asesor', $asesor1)->where('id_item_penilaian', $item->id)->where('pra_paska', 'pra')->first();
                    $penilaianpra_2 = Penilaian::where('id_pengajuan', $id)->where('id_asesor', $asesor2)->where('id_item_penilaian', $item->id)->where('pra_paska', 'pra')->first();
                    $penilaianpra_3 = Penilaian::where('id_pengajuan', $id)->where('id_asesor', $asesor3)->where('id_item_penilaian', $item->id)->where('pra_paska', 'pra')->first();
                    // $penilaianpra2 = $allPenilaian->get($item->id, [])->get('pra2')?->first();
                    $penilaianItem = $allPenilaian->get($item->id, []);
                    $penilaianpra2 = isset($penilaianItem['pra2']) ? collect($penilaianItem['pra2'])->first() : null;

                    // Hitung nilai pra2
                    $nilaipra_1 = $penilaianpra_1 ? $penilaianpra_1->nilai : 0;
                    $nilaipra_2 = $penilaianpra_2 ? $penilaianpra_2->nilai : 0;
                    $nilaipra_3 = $penilaianpra_3 ? $penilaianpra_3->nilai : 0;
                    $nilaipra2 = $penilaianpra2 ? $penilaianpra2->nilai : 0;
                    $nilai_bobot = round(($nilaipra2 * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsur += $nilai_bobot;

                    if ($checkValidation && !$penilaianpra2) {
                        $isValid = false;
                    }

                    $temp_items[] = [
                        'id' => $item->id,
                        'kode_item' => $item->kode_item,
                        'nama_item' => $item->nama_item,
                        'bobot_item' => $item->bobot_item,
                        'nilaipra_1' => $nilaipra_1,
                        'nilaipra_2' => $nilaipra_2,
                        'nilaipra_3' => $nilaipra_3,
                        'nilaipra2' => $nilaipra2,
                        'nilai_bobot' => $nilai_bobot,
                    ];
                }

                // Setelah semua subunsur selesai:
                $nilai_bobot_subunsur = round(($totalNilaiItemSubunsur * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsur += $nilai_bobot_subunsur;

                $temp_su[] = [
                    'id_su' => $subunsur->id,
                    'su' => $subunsur->nama_subunsur,
                    'bobot_subunsur' => $subunsur->bobot_subunsur,
                    'nilai_bobot_subunsur' => $nilai_bobot_subunsur,
                    'items' => $temp_items,
                ];
            }

            $nilai_bobot_unsur = round(($totalNilaiSubunsurUnsur * $unsur->bobot_unsur) / 100, 2);
            $nilai_akhir += $nilai_bobot_unsur;

            $data[] = [
                'id_uu' => $unsur->id,
                'unsur' => $unsur->nama_unsur,
                'bobot_unsur' => $unsur->bobot_unsur,
                'nilai_bobot_unsur' => $nilai_bobot_unsur,
                'nilai_akhir' => $nilai_akhir,
                'subunsurs' => $temp_su,
            ];
        }

        return [
            'data' => $data,
            'pengajuan' => $pengajuan,
            'nilai_akhir' => round($nilai_akhir, 2),
            'isValid' => $isValid,
        ];
    }

    public function pravisit2($id)
    {
        $result = $this->calculatePravisit2Data($id);
        $predikat = $this->getPredikat($result['nilai_akhir']);
        $jenis = 'pravisit2';

        $this->updatePengajuan($id, $result['nilai_akhir'], $jenis);

        return view('asesor.pravisit2', array_merge($result, [
            'predikat' => $predikat,
            'isHistory' => false,
        ]));
    }

    public function pravisitView2($id)
    {
        $result = $this->calculatePravisit2Data($id);
        $predikat = $this->getPredikat($result['nilai_akhir']);
        $jenis = 'pravisit2';

        $this->updatePengajuan($id, $result['nilai_akhir'], $jenis);

        return view('asesor.pravisit2', array_merge($result, [
            'predikat' => $predikat,
            'isHistory' => true,
        ]));
    }

    public function editPravisit2($id)
    {
        Pengajuan::where('id', $id)->update([
            'pra_visit2_asesor' => 0,
        ]);

        return redirect()->route('pravisit2', $id);
    }

    // Penilaian Visitasi
    public function visitasi($id)
    {
        $result = $this->calculatePravisit2Data($id);
        $predikat = $this->getPredikat($result['nilai_akhir']);
        $hasDigitalSignature = DigitalSignature::where('pengajuan_id', $result['pengajuan']->id)
            ->where('status_ttd', 'signed')
            ->exists();

        return view('asesor.visitasi', array_merge($result, [
            'predikat' => $predikat,
            'hasDigitalSignature' => $hasDigitalSignature,
        ]));
    }

    // Penilaian Paska Visitasi
    private function calculatePaskavisitData($id, $checkValidation = false)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $asesorIds = [$pengajuan->id_asesor1, $pengajuan->id_asesor2, $pengajuan->id_asesor3];

        // Load all penilaian data at once
        $allPenilaian = Penilaian::where('id_pengajuan', $id)
            ->whereIn('id_asesor', $asesorIds)
            ->get()
            ->groupBy(['id_item_penilaian', 'pra_paska']);

        $data = [];
        $nilai_akhir2 = 0;
        $nilai_paskavisit = 0;
        $isValid = true;

        $unsurs = Unsur::with(['subunsurs.items'])->get();

        foreach ($unsurs as $unsur) {
            $temp_su = [];
            $totalNilaiSubunsurUnsur = 0;
            $totalNilaiSubunsurUnsurPaska = 0;

            foreach ($unsur->subunsurs as $subunsur) {
                $totalNilaiItemSubunsur = 0;
                $totalNilaiItemSubunsurPaska = 0;
                $temp_items = [];

                foreach ($subunsur->items as $item) {
                    $penilaianItem = $allPenilaian->get($item->id, []);
                    $penilaianpra2 = isset($penilaianItem['pra2']) ? collect($penilaianItem['pra2'])->first() : null;
                    $penilaianpaska = isset($penilaianItem['paska']) ? collect($penilaianItem['paska'])->first() : null;

                    // Hitung nilai pra2
                    $nilaipra2 = $penilaianpra2 ? $penilaianpra2->nilai : 0;
                    $nilai_bobot2 = round(($nilaipra2 * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsur += $nilai_bobot2;

                    // Hitung nilai paska
                    $nilaipaska = $penilaianpaska ? $penilaianpaska->nilai : 0;
                    $nilai_bobot_paska = round(($nilaipaska * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsurPaska += $nilai_bobot_paska;

                    if ($checkValidation && !$penilaianpaska) {
                        $isValid = false;
                    }

                    $temp_items[] = [
                        'id' => $item->id,
                        'kode_item' => $item->kode_item,
                        'nama_item' => $item->nama_item,
                        'bobot_item' => $item->bobot_item,
                        'nilaipra2' => $nilaipra2,
                        'nilai_bobot2' => $nilai_bobot2,
                        'nilaipaska' => $nilaipaska,
                        'nilai_bobot_paska' => $nilai_bobot_paska,
                        'pengecekan_visitasi' => $penilaianpaska ? $penilaianpaska->pengecekan_visitasi : null,
                    ];
                }

                $nilai_bobot_subunsur = round(($totalNilaiItemSubunsur * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsur += $nilai_bobot_subunsur;

                $nilai_bobot_subunsur_paska = round(($totalNilaiItemSubunsurPaska * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsurPaska += $nilai_bobot_subunsur_paska;

                $temp_su[] = [
                    'id_su' => $subunsur->id,
                    'su' => $subunsur->nama_subunsur,
                    'bobot_subunsur' => $subunsur->bobot_subunsur,
                    'nilai_bobot_subunsur' => $nilai_bobot_subunsur,
                    'nilai_bobot_subunsur_paska' => $nilai_bobot_subunsur_paska,
                    'items' => $temp_items,
                ];
            }

            $nilai_bobot_unsur2 = round(($totalNilaiSubunsurUnsur * $unsur->bobot_unsur) / 100, 2);
            $nilai_akhir2 += $nilai_bobot_unsur2;

            $nilai_bobot_unsur_paska = round(($totalNilaiSubunsurUnsurPaska * $unsur->bobot_unsur) / 100, 2);
            $nilai_paskavisit += $nilai_bobot_unsur_paska;

            $data[] = [
                'id_uu' => $unsur->id,
                'unsur' => $unsur->nama_unsur,
                'bobot_unsur' => $unsur->bobot_unsur,
                'nilai_bobot_unsur2' => $nilai_bobot_unsur2,
                'nilai_bobot_unsur_paska' => $nilai_bobot_unsur_paska,
                'nilai_akhir2' => $nilai_akhir2,
                'nilai_paskavisit' => $nilai_paskavisit,
                'subunsurs' => $temp_su,
            ];
        }

        return [
            'data' => $data,
            'pengajuan' => $pengajuan,
            'nilai_akhir2' => round($nilai_akhir2, 2),
            'nilai_paskavisit' => round($nilai_paskavisit, 2),
            'isValid' => $isValid,
        ];
    }

    public function paskavisit($id)
    {
        $result = $this->calculatePaskavisitData($id, true);
        $predikat2 = $this->getPredikat($result['nilai_akhir2']);
        $predikat_paskavisit = $this->getPredikat($result['nilai_paskavisit']);
        $jenis = 'paskavisit';

        $this->updatePengajuan($id, $result['nilai_paskavisit'], $jenis);

        return view('asesor.paskavisit', array_merge($result, [
            'predikat2' => $predikat2,
            'predikat_paskavisit' => $predikat_paskavisit,
            'isHistory' => false,
        ]));
    }

    public function paskavisitView($id)
    {
        $result = $this->calculatePaskavisitData($id);
        $predikat2 = $this->getPredikat($result['nilai_akhir2']);
        $predikat_paskavisit = $this->getPredikat($result['nilai_paskavisit']);
        $jenis = 'paskavisit';

        $this->updatePengajuan($id, $result['nilai_paskavisit'], $jenis);

        return view('asesor.paskavisit', array_merge($result, [
            'predikat2' => $predikat2,
            'predikat_paskavisit' => $predikat_paskavisit,
            'isHistory' => true,
        ]));
    }

    public function editPaskavisit($id)
    {
        Pengajuan::where('id', $id)->update([
            'paska_visit' => 0,
            'final' => 0,
        ]);

        return redirect()->route('paskavisit', $id);
    }

    // Penilaian Final
    private function calculateFinalData($id, $checkValidation = false)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $asesorIds = [$pengajuan->id_asesor1, $pengajuan->id_asesor2, $pengajuan->id_asesor3];

        // Load all penilaian data at once
        $allPenilaian = Penilaian::where('id_pengajuan', $id)
            ->whereIn('id_asesor', $asesorIds)
            ->get()
            ->groupBy(['id_item_penilaian', 'pra_paska']);

        $data = [];
        $nilai_pra2 = 0;
        $nilai_paskavisit = 0;
        $nilai_final = 0;
        $isValid = true;

        $unsurs = Unsur::with(['subunsurs.items'])->get();

        foreach ($unsurs as $unsur) {
            $temp_su = [];
            $totalNilaiSubunsurUnsurPra2 = 0;
            $totalNilaiSubunsurUnsurPaska = 0;
            $totalNilaiSubunsurUnsurFinal = 0;

            foreach ($unsur->subunsurs as $subunsur) {
                $totalNilaiItemSubunsurPra2 = 0;
                $totalNilaiItemSubunsurPaska = 0;
                $totalNilaiItemSubunsurFinal = 0;
                $temp_items = [];

                foreach ($subunsur->items as $item) {
                    $penilaianItem = $allPenilaian->get($item->id, []);

                    $penilaianpra2 = isset($penilaianItem['pra2']) ? collect($penilaianItem['pra2'])->first() : null;
                    $penilaianpaska = isset($penilaianItem['paska']) ? collect($penilaianItem['paska'])->first() : null;
                    $penilaianfinal = isset($penilaianItem['final']) ? collect($penilaianItem['final'])->first() : null;

                    // Hitung nilai pra2
                    $nilaipra2 = $penilaianpra2 ? $penilaianpra2->nilai : 0;
                    $nilai_bobot_pra2 = round(($nilaipra2 * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsurPra2 += $nilai_bobot_pra2;

                    // Hitung nilai paska
                    $nilaipaska = $penilaianpaska ? $penilaianpaska->nilai : 0;
                    $nilai_bobot_paska = round(($nilaipaska * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsurPaska += $nilai_bobot_paska;

                    // Hitung nilai final
                    $nilaifinal = $penilaianfinal ? $penilaianfinal->nilai : 0;
                    $nilai_bobot_final = round(($nilaifinal * $item->bobot_item) / 100, 2);
                    $totalNilaiItemSubunsurFinal += $nilai_bobot_final;

                    if ($checkValidation && !$penilaianpaska) {
                        $isValid = false;
                    }

                    $temp_items[] = [
                        'id' => $item->id,
                        'kode_item' => $item->kode_item,
                        'nama_item' => $item->nama_item,
                        'bobot_item' => $item->bobot_item,
                        'nilai_bobot_pra2' => $nilai_bobot_pra2,
                        'nilai_bobot_paska' => $nilai_bobot_paska,
                        'nilai_bobot_final' => $nilai_bobot_final,
                        'nilaipra2' => $nilaipra2,
                        'nilaipaska' => $nilaipaska,
                        'nilai_final' => $nilaifinal,
                    ];
                }
                $nilai_bobot_subunsur_pra2 = round(($totalNilaiItemSubunsurPra2 * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsurPra2 += $nilai_bobot_subunsur_pra2;

                $nilai_bobot_subunsur_paska = round(($totalNilaiItemSubunsurPaska * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsurPaska += $nilai_bobot_subunsur_paska;

                $nilai_bobot_subunsur_final = round(($totalNilaiItemSubunsurFinal * $subunsur->bobot_subunsur) / 100, 2);
                $totalNilaiSubunsurUnsurFinal += $nilai_bobot_subunsur_final;

                $temp_su[] = [
                    'id_su' => $subunsur->id,
                    'su' => $subunsur->nama_subunsur,
                    'bobot_subunsur' => $subunsur->bobot_subunsur,
                    'nilai_bobot_subunsur_pra2' => $nilai_bobot_subunsur_pra2,
                    'nilai_bobot_subunsur_paska' => $nilai_bobot_subunsur_paska,
                    'nilai_bobot_subunsur_final' => $nilai_bobot_subunsur_final,
                    'items' => $temp_items,
                ];
            }
            $nilai_bobot_unsur_pra2 = round(($totalNilaiSubunsurUnsurPra2 * $unsur->bobot_unsur) / 100, 2);
            $nilai_pra2 += $nilai_bobot_unsur_pra2;

            $nilai_bobot_unsur_paska = round(($totalNilaiSubunsurUnsurPaska * $unsur->bobot_unsur) / 100, 2);
            $nilai_paskavisit += $nilai_bobot_unsur_paska;

            $nilai_bobot_unsur_final = round(($totalNilaiSubunsurUnsurFinal * $unsur->bobot_unsur) / 100, 2);
            $nilai_final += $nilai_bobot_unsur_final;

            $data[] = [
                'id_uu' => $unsur->id,
                'unsur' => $unsur->nama_unsur,
                'bobot_unsur' => $unsur->bobot_unsur,
                'nilai_bobot_unsur_pra2' => $nilai_bobot_unsur_pra2,
                'nilai_bobot_unsur_paska' => $nilai_bobot_unsur_paska,
                'nilai_bobot_unsur_final' => $nilai_bobot_unsur_final,
                'nilai_paskavisit' => $nilai_paskavisit,
                'nilai_final' => $nilai_final,
                'subunsurs' => $temp_su,
            ];
        }

        return [
            'data' => $data,
            'pengajuan' => $pengajuan,
            'nilai_pra2' => round($nilai_pra2, 2),
            'nilai_paskavisit' => round($nilai_paskavisit, 2),
            'nilai_final' => round($nilai_final, 2),
            'isValid' => $isValid,
        ];
    }

    public function final($id)
    {
        $result = $this->calculateFinalData($id, true);
        $predikat_pra2 = $this->getPredikat($result['nilai_pra2']);
        $predikat_paskavisit = $this->getPredikat($result['nilai_paskavisit']);
        $predikat_final = $this->getPredikat($result['nilai_final']);
        $jenis = 'final';
        $this->updatePengajuan($id, $result['nilai_final'], $jenis);

        return view('asesor.final', array_merge($result, [
            'predikat_pra2' => $predikat_pra2,
            'predikat_paskavisit' => $predikat_paskavisit,
            'predikat_final' => $predikat_final,
            'isHistory' => false,
        ]));
    }

    public function editFinal($id)
    {
        Pengajuan::where('id', $id)->update(['final' => 0]);

        return redirect()->route('final', $id);
    }

    public function finalView($id)
    {
        $result = $this->calculateFinalData($id, true);
        $predikat_pra2 = $this->getPredikat($result['nilai_pra2']);
        $predikat_paskavisit = $this->getPredikat($result['nilai_paskavisit']);
        $predikat_final = $this->getPredikat($result['nilai_final']);
        $jenis = 'final';
        $this->updatePengajuan($id, $result['nilai_final'], $jenis);

        return view('asesor.final', array_merge($result, [
            'predikat_pra2' => $predikat_pra2,
            'predikat_paskavisit' => $predikat_paskavisit,
            'predikat_final' => $predikat_final,
            'isHistory' => true,
        ]));
    }

    // Export
    public function eksporBA($id)
    {
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $tanggal_visitasi = $pengajuan->created_at; // sementara diisi created at
        $nama_lemdik = $pengajuan->profile->nama_lembaga;
        $hari_penyebut = $tanggal_visitasi->isoFormat('dddd');
        $tanggal_penyebut = $tanggal_visitasi->isoFormat('D MMMM Y');
        $pukul_penyebut = $tanggal_visitasi->isoFormat('HH.mm');
        $nama_asesor1 = $pengajuan->asesor1->name;
        $nama_asesor2 = $pengajuan->asesor2->name;
        $nama_asesor3 = $pengajuan->asesor3->name;
        $tanggal = $tanggal_visitasi->isoFormat('D MMMM Y');
        $jabatan_kepala = $pengajuan->profile->jabatan_pimpinan;
        $nama_kepala = $pengajuan->profile->nama_pimpinan;
        $start_reupload = $tanggal_visitasi->addDays(1)->isoFormat('dddd, D MMMM Y');
        $end_reupload = $tanggal_visitasi->addDays(4)->isoFormat('dddd, D MMMM Y');
        $start_regrade = $tanggal_visitasi->addDays(5)->isoFormat('dddd, D MMMM Y');
        if ($pengajuan->id_jenis == 1) {
            $jenis_pengajuan = 'Sistem Teknologi Berbasis Komputer';
        } else {
            $jenis_pengajuan = 'Statistik';
        }

        $templatePath = public_path('template_berita_acara_master.docx');
        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setValue('nama_lemdik', $nama_lemdik);
        $templateProcessor->setValue('hari_penyebut', $hari_penyebut);
        $templateProcessor->setValue('tanggal_penyebut', $tanggal_penyebut);
        $templateProcessor->setValue('pukul_penyebut', $pukul_penyebut);
        $templateProcessor->setValue('nama_asesor1', $nama_asesor1);
        $templateProcessor->setValue('nama_asesor2', $nama_asesor2);
        $templateProcessor->setValue('nama_asesor3', $nama_asesor3);
        $templateProcessor->setValue('tanggal', $tanggal);
        $templateProcessor->setValue('jabatan_kepala', $jabatan_kepala);
        $templateProcessor->setValue('nama_kepala', $nama_kepala);
        $templateProcessor->setValue('start_reupload', $start_reupload);
        $templateProcessor->setValue('end_reupload', $end_reupload);
        $templateProcessor->setValue('start_regrade', $start_regrade);
        $templateProcessor->setValue('jenis_pengajuan', $jenis_pengajuan);

        $this->applyBaNotes($templateProcessor, $id);

        $fileName = 'Berita Acara '.$nama_lemdik.'.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    private function applyBaNotes(TemplateProcessor $templateProcessor, $id)
    {
        // Catatan noteA
        $itemsA = Penilaian::where('id_item_penilaian', 1)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteA', count($itemsA), true, true);
        foreach ($itemsA as $index => $item) {
            $templateProcessor->setValue('itemA#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2A', count($itemsA), true, true);
        foreach ($itemsA as $index => $item) {
            $templateProcessor->setValue('item2A#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteB
        $itemsB = Penilaian::where('id_item_penilaian', 2)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteB', count($itemsB), true, true);
        foreach ($itemsB as $index => $item) {
            $templateProcessor->setValue('itemB#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2B', count($itemsB), true, true);
        foreach ($itemsB as $index => $item) {
            $templateProcessor->setValue('item2B#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteC
        $itemsC = Penilaian::where('id_item_penilaian', 3)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteC', count($itemsC), true, true);
        foreach ($itemsC as $index => $item) {
            $templateProcessor->setValue('itemC#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2C', count($itemsC), true, true);
        foreach ($itemsC as $index => $item) {
            $templateProcessor->setValue('item2C#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteD
        $itemsD = Penilaian::where('id_item_penilaian', 4)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteD', count($itemsD), true, true);
        foreach ($itemsD as $index => $item) {
            $templateProcessor->setValue('itemD#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2D', count($itemsD), true, true);
        foreach ($itemsD as $index => $item) {
            $templateProcessor->setValue('item2D#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteE
        $itemsE = Penilaian::where('id_item_penilaian', 5)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteE', count($itemsE), true, true);
        foreach ($itemsE as $index => $item) {
            $templateProcessor->setValue('itemE#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2E', count($itemsE), true, true);
        foreach ($itemsE as $index => $item) {
            $templateProcessor->setValue('item2E#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteF
        $itemsF = Penilaian::where('id_item_penilaian', 6)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteF', count($itemsF), true, true);
        foreach ($itemsF as $index => $item) {
            $templateProcessor->setValue('itemF#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2F', count($itemsF), true, true);
        foreach ($itemsF as $index => $item) {
            $templateProcessor->setValue('item2F#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteG
        $itemsG = Penilaian::where('id_item_penilaian', 7)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteG', count($itemsG), true, true);
        foreach ($itemsG as $index => $item) {
            $templateProcessor->setValue('itemG#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2G', count($itemsG), true, true);
        foreach ($itemsG as $index => $item) {
            $templateProcessor->setValue('item2G#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteH
        $itemsH = Penilaian::where('id_item_penilaian', 8)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteH', count($itemsH), true, true);
        foreach ($itemsH as $index => $item) {
            $templateProcessor->setValue('itemH#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2H', count($itemsH), true, true);
        foreach ($itemsH as $index => $item) {
            $templateProcessor->setValue('item2H#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteI
        $itemsI = Penilaian::whereBetween('id_item_penilaian', [9, 10])
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteI', count($itemsI), true, true);
        foreach ($itemsI as $index => $item) {
            $templateProcessor->setValue('itemI#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2I', count($itemsI), true, true);
        foreach ($itemsI as $index => $item) {
            $templateProcessor->setValue('item2I#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteJ
        $itemsJ = Penilaian::where('id_item_penilaian', 11)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteJ', count($itemsJ), true, true);
        foreach ($itemsJ as $index => $item) {
            $templateProcessor->setValue('itemJ#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2J', count($itemsJ), true, true);
        foreach ($itemsJ as $index => $item) {
            $templateProcessor->setValue('item2J#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteK
        $itemsK = Penilaian::where('id_item_penilaian', 12)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteK', count($itemsK), true, true);
        foreach ($itemsK as $index => $item) {
            $templateProcessor->setValue('itemK#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2K', count($itemsK), true, true);
        foreach ($itemsK as $index => $item) {
            $templateProcessor->setValue('item2K#'.($index + 1), $item->rekomendasi);
        }

        // Catatan noteL
        $itemsL = Penilaian::where('id_item_penilaian', 13)
            ->where('id_pengajuan', $id)
            ->where('pra_paska', 'pra2')
            ->select('catatan', 'rekomendasi')
            ->get();

        $templateProcessor->cloneBlock('noteL', count($itemsL), true, true);
        foreach ($itemsL as $index => $item) {
            $templateProcessor->setValue('itemL#'.($index + 1), $item->catatan);
        }

        $templateProcessor->cloneBlock('note2L', count($itemsL), true, true);
        foreach ($itemsL as $index => $item) {
            $templateProcessor->setValue('item2L#'.($index + 1), $item->rekomendasi);
        }
    }

    public function eksporBAHasilTtd($id)
    {
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $nama_lemdik = $pengajuan->profile->nama_lembaga;
        if ($pengajuan->id_jenis == 1) {
            $jenis_pengajuan = 'Sistem Teknologi Berbasis Komputer';
        } else {
            $jenis_pengajuan = 'Statistik';
        }

        $signatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('status_ttd', 'signed')
            ->get()
            ->keyBy('jenis_user');

        // Data surat dari modal generate (tr_digital_signatures)
        $sigData = $signatures->first();
        $tglSurat = $sigData ? \Carbon\Carbon::parse($sigData->tgl_surat) : \Carbon\Carbon::now();
        $hariSurat = $tglSurat->isoFormat('dddd');
        $tanggalSurat = $tglSurat->isoFormat('D MMMM Y');
        $waktuSurat = $sigData && $sigData->waktu_surat ? \Carbon\Carbon::parse($sigData->waktu_surat)->format('H.i') : $tglSurat->format('H.i');
        $zonaSurat = $sigData->zona_surat ?? 'Waktu Indonesia Barat';
        $tempatSurat = $sigData->tempat_surat ?? 'Jakarta';

        // Perhitungan hari kerja (skip Sabtu-Minggu)
        $startReupload = $this->addBusinessDays($tglSurat, 1)->isoFormat('dddd, D MMMM Y');
        $endReupload = $this->addBusinessDays($tglSurat, 3)->isoFormat('dddd, D MMMM Y');
        $startRegrade = $this->addBusinessDays($tglSurat, 4)->isoFormat('dddd, D MMMM Y');

        $namaAsesor1 = optional($signatures->get('asesor1'))->nama_user ?? optional($pengajuan->asesor1)->name;
        $namaAsesor2 = optional($signatures->get('asesor2'))->nama_user ?? optional($pengajuan->asesor2)->name;
        $namaAsesor3 = optional($signatures->get('asesor3'))->nama_user ?? optional($pengajuan->asesor3)->name;
        $jabatanKepala = optional($signatures->get('kepala'))->jabatan_user ?? optional($pengajuan->profile)->jabatan_pimpinan;
        $namaKepala = optional($signatures->get('kepala'))->nama_user ?? optional($pengajuan->profile)->nama_pimpinan;

        $templateProcessor = new TemplateProcessor(public_path('template_berita_acara.docx'));
        $templateProcessor->setValue('nama_lemdik', $nama_lemdik);
        $templateProcessor->setValue('jenis_pengajuan', $jenis_pengajuan);
        $templateProcessor->setValue('hari_surat', $hariSurat);
        $templateProcessor->setValue('tanggal_surat', $tanggalSurat);
        $templateProcessor->setValue('waktu_surat', $waktuSurat);
        $templateProcessor->setValue('zona_surat', $zonaSurat);
        $templateProcessor->setValue('tempat_surat', $tempatSurat);
        $templateProcessor->setValue('tanggal', $tanggalSurat);
        $templateProcessor->setValue('nama_asesor1', $namaAsesor1);
        $templateProcessor->setValue('nama_asesor2', $namaAsesor2);
        $templateProcessor->setValue('nama_asesor3', $namaAsesor3);
        $templateProcessor->setValue('jabatan_kepala', $jabatanKepala);
        $templateProcessor->setValue('nama_kepala', $namaKepala);
        $templateProcessor->setValue('start_reupload', $startReupload);
        $templateProcessor->setValue('end_reupload', $endReupload);
        $templateProcessor->setValue('start_regrade', $startRegrade);

        // Tanda tangan dari tr_digital_signatures
        foreach (['asesor1', 'asesor2', 'asesor3', 'kepala'] as $signerType) {
            $signature = $signatures->get($signerType);
            $path = $signature && $signature->ttd ? public_path($signature->ttd) : null;
            if ($path && is_file($path)) {
                $templateProcessor->setImageValue('ttd_' . $signerType, [
                    'path' => $path, 'width' => 110, 'height' => 45, 'ratio' => true,
                ]);
            } else {
                $templateProcessor->setValue('ttd_' . $signerType, '');
            }
        }

        $this->applyBaNotes($templateProcessor, $id);

        $fileName = 'Berita Acara TTD '.$nama_lemdik.'.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    private function addBusinessDays(\Carbon\Carbon $date, int $days): \Carbon\Carbon
    {
        $result = $date->copy();
        $added = 0;
        while ($added < $days) {
            $result->addDay();
            // Skip Sabtu/Minggu dan hari libur nasional/cuti bersama (konsisten dengan Profile::LIBUR_NASIONAL_2026)
            if ($result->isWeekend() || in_array($result->format('Y-m-d'), \App\Models\Profile::LIBUR_NASIONAL_2026)) {
                continue;
            }
            $added++;
        }
        return $result;
    }

    private function prepareBaTemplateWithSignaturePlaceholders()
    {
        $source = public_path('template_berita_acara.docx');
        $target = tempnam(sys_get_temp_dir(), 'paps-ba-');
        copy($source, $target);
        $zip = new \ZipArchive();
        if ($zip->open($target) !== true) {
            throw new \RuntimeException('Template Berita Acara tidak dapat dibuka.');
        }
        $xml = $zip->getFromName('word/document.xml');
        foreach (['asesor1', 'asesor2', 'asesor3', 'kepala'] as $signerType) {
            $needle = '${nama_' . $signerType . '}';
            $replacement = '${ttd_' . $signerType . '}&#xA;' . $needle;
            $xml = preg_replace('/' . preg_quote($needle, '/') . '/', $replacement, $xml, 1);
        }
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
        return $target;
    }

    public function eksporRekomendasi($id)
    {
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $tahun_pengajuan = $pengajuan->created_at->isoFormat('YYYY'); // sementara diisi created at
        $nama_lemdik = $pengajuan->profile->nama_lembaga;
        if ($pengajuan->id_jenis == 1) {
            $jenis_pengajuan = 'Sistem Teknologi Berbasis Komputer';
        } else {
            $jenis_pengajuan = 'Statistik';
        }
        $nilai_final = $pengajuan->nilai_final;
        $predikat_final = $pengajuan->predikat_final;

        $templateProcessor = new TemplateProcessor('template_hasil_visitasi.docx');
        $templateProcessor->setValue('tahun_pengajuan', $tahun_pengajuan);
        $templateProcessor->setValue('nama_lemdik', $nama_lemdik);
        $templateProcessor->setValue('jenis_pengajuan', $jenis_pengajuan);
        $templateProcessor->setValue('nilai_final', $nilai_final);
        $templateProcessor->setValue('predikat_final', $predikat_final);

        $fileName = 'Rekomendasi Hasil Visitasi '.$nama_lemdik.'.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function eksporSertifikat($id)
    {
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return back()->with('error', 'Data tidak ditemukan');
        }
        $nomor_sertifikat = 'contoh/B-2145/02600/DL.100/2024';
        $nomor_kepka = 'contoh/174';
        $tanggal_visitasi = $pengajuan->created_at;
        $tanggal_pengajuan = $tanggal_visitasi->isoFormat('D MMMM Y'); // sementara diisi created at
        $tahun_pengajuan = $tanggal_visitasi->isoFormat('YYYY'); // sementara diisi created at
        $nama_lembaga = $pengajuan->profile->nama_lembaga;
        if ($pengajuan->id_jenis == 1) {
            $jenis_pengajuan = 'Sistem Teknologi Berbasis Komputer';
        } else {
            $jenis_pengajuan = 'Statistik';
        }

        $predikat_final = $pengajuan->predikat_final;
        ($pengajuan->nilai_final >= 3.51) ? $masa_berlaku = '5 Tahun' : $masa_berlaku = '3 Tahun';

        $templateProcessor = new TemplateProcessor('template_sertifikat_hasil_akreditasi.docx');
        $templateProcessor->setValue('nomor_sertifikat', $nomor_sertifikat);
        $templateProcessor->setValue('nomor_kepka', $nomor_kepka);
        $templateProcessor->setValue('tanggal_pengajuan', $tanggal_pengajuan);
        $templateProcessor->setValue('tahun_pengajuan', $tahun_pengajuan);
        $templateProcessor->setValue('nama_lembaga', $nama_lembaga);
        $templateProcessor->setValue('jenis_pengajuan', $jenis_pengajuan);
        $templateProcessor->setValue('predikat_final', $predikat_final);
        $templateProcessor->setValue('masa_berlaku', $masa_berlaku);

        $fileName = 'Sertifikat Hasil Akreditasi '.$nama_lembaga.'.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function eksporPenilaian(Request $request)
    {
        // dd($request);
        // Validasi input data
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:tb_pengajuans,id',
            'jenis_penilaian' => 'required|string|in:Paska,Final',
        ]);

        $id = $validatedData['id'];
        $jenis_penilaian = $validatedData['jenis_penilaian'];

        // Pilih metode perhitungan berdasarkan jenis penilaian
        if ($jenis_penilaian === 'Paska') {
            $result = $this->calculatePaskavisitData($id);
        } elseif ($jenis_penilaian === 'Final') {
            $result = $this->calculateFinalData($id);
        } else {
            // Default ke paska visitasi jika jenis tidak dikenali
            $result = $this->calculatePaskavisitData($id);
        }

        $pengajuan = $result['pengajuan'];
        $data = $result['data'];

        // Get profile lembaga data
        $profile = $pengajuan->profile;
        $nama_lembaga = $profile ? $profile->nama_lembaga : 'Nama Lembaga Tidak Ditemukan';

        // Ambil nilai dan predikat berdasarkan jenis penilaian
        if ($jenis_penilaian === 'paska') {
            $nilai = $pengajuan->nilai_paskavisit ?? 'belum submit nilai';
            $predikat = $pengajuan->predikat_paskavisit ?? '-';
        } elseif ($jenis_penilaian === 'final') {
            $nilai = $pengajuan->nilai_final ?? 'belum submit nilai';
            $predikat = $pengajuan->predikat_final ?? '-';
        } else {
            // Default ke paska visitasi
            $nilai = $pengajuan->nilai_paskavisit ?? 'belum submit nilai';
            $predikat = $pengajuan->predikat_paskavisit ?? '-';
        }

        // Load Excel template
        $templatePath = public_path('template_ekspor_penilaian.xlsx');
        if (!file_exists($templatePath)) {
            // Fallback to create new spreadsheet if template doesn't exist
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set basic headers if no template
            $headers = [
                'A1' => 'No', 'B1' => 'Unsur', 'C1' => 'Sub Unsur', 'D1' => 'Komponen Penilaian',
                'E1' => 'Catatan Visitasi', 'F1' => 'Rekomendasi Visitasi', 'G1' => 'Pengecekan Hasil Visitasi',
                'H1' => 'Nilai Item', 'I1' => 'Nilai Komponen', 'J1' => 'Nilai Sub Unsur',
                'K1' => 'Nilai Unsur',
            ];
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }
            $startRow = 2;
        } else {
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Replace template placeholders
            $this->replaceTemplatePlaceholders($sheet, [
                '{{nama_lembaga}}' => $nama_lembaga,
                '{{A1}}' => $jenis_penilaian,
                '{{nilai}}' => $nilai,
                '{{predikat}}' => substr($predikat, -1),
            ]);

            // Find the starting row for data (after headers)
            $startRow = $this->findDataStartRow($sheet);
        }

        // Prepare data for export
        $exportData = [];
        $no = 1;

        foreach ($data as $unsur) {
            foreach ($unsur['subunsurs'] as $subunsur) {
                foreach ($subunsur['items'] as $item) {
                    // Skip item with id 14
                    if ($item['id'] == 14) {
                        continue;
                    }

                    // Get penilaian data for this item
                    if ($jenis_penilaian === 'paska') {
                        $penilaian = Penilaian::where('id_pengajuan', $id)
                            ->where('id_item_penilaian', $item['id'])
                            ->where('pra_paska', 'paska')
                            ->first();
                    } elseif ($jenis_penilaian === 'final') {
                        $penilaian = Penilaian::where('id_pengajuan', $id)
                            ->where('id_item_penilaian', $item['id'])
                            ->where('pra_paska', 'final')
                            ->first();
                    } else {
                        $penilaian = Penilaian::where('id_pengajuan', $id)
                            ->where('id_item_penilaian', $item['id'])
                            ->where('pra_paska', 'paska')
                            ->first();
                    }

                    $exportData[] = [
                        'No' => $no++,
                        'Unsur' => $unsur['unsur'],
                        'Sub Unsur' => $subunsur['su'],
                        'Komponen Penilaian' => $item['nama_item'],
                        // Catatan visitasi from catatan asesor
                        'Catatan Visitasi' => $penilaian ? $penilaian->catatan : '',
                        // Rekomendasi visitasi from rekomendasi asesor
                        'Rekomendasi Visitasi' => $penilaian ? $penilaian->rekomendasi : '',
                        // Pengecekan hasil visitasi
                        'Pengecekan Hasil Visitasi' => $penilaian ? $penilaian->pengecekan_visitasi : '',
                        // Map nilai visitasi to nilai item berdasarkan jenis penilaian
                        'Nilai Item' => $jenis_penilaian === 'final' ? $item['nilaifinal'] : $item['nilaipaska'],
                        // Map nilai komponen to nilai bobot item berdasarkan jenis penilaian
                        'Nilai Komponen' => $jenis_penilaian === 'final' ? $item['nilai_bobot_final'] : $item['nilai_bobot_paska'],
                        // Map nilai sub unsur to nilai bobot subunsur berdasarkan jenis penilaian
                        'Nilai Sub Unsur' => $jenis_penilaian === 'final' ? ($subunsur['nilai_bobot_subunsur_final'] ?? 0) : ($subunsur['nilai_bobot_subunsur_paska'] ?? 0),
                        // Map nilai unsur to nilai bobot unsur berdasarkan jenis penilaian
                        'Nilai Unsur' => $jenis_penilaian === 'final' ? ($unsur['nilai_bobot_unsur_final'] ?? 0) : ($unsur['nilai_bobot_unsur_paska'] ?? 0),
                    ];
                }
            }
        }

        // Add data to sheet
        $row = $startRow;
        foreach ($exportData as $data) {
            $sheet->setCellValue('E'.$row, $data['Catatan Visitasi']);
            $sheet->setCellValue('F'.$row, $data['Rekomendasi Visitasi']);
            $sheet->setCellValue('G'.$row, $data['Pengecekan Hasil Visitasi']);
            $sheet->setCellValue('H'.$row, $data['Nilai Item']);
            $sheet->setCellValue('I'.$row, $data['Nilai Komponen']);
            $sheet->setCellValue('J'.$row, $data['Nilai Sub Unsur']);
            $sheet->setCellValue('K'.$row, $data['Nilai Unsur']);
            ++$row;
        }

        // Create filename
        // Generate filename berdasarkan jenis penilaian
        $filename = $jenis_penilaian === 'Final'
            ? 'Penilaian_Final_'.$nama_lembaga.'_'.date('Y-m-d_H-i-s').'.xlsx'
            : 'Penilaian_Paska_Visitasi_'.$nama_lembaga.'_'.date('Y-m-d_H-i-s').'.xlsx';

        // Set response headers
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function storeRekomendasi(Request $request)
    {
        $idpengajuan = $request->id_pengajuan;
        $path_rekomendasi_visitasi = null;
        if ($request->hasFile('rekomendasi_visitasi')) {
            $rekomendasi_visitasi = $request->file('rekomendasi_visitasi');
            $path_rekomendasi_visitasi = Uploadfile::upload($idpengajuan, $rekomendasi_visitasi, 'hasil_visitasi');
        }

        $UpdateData = [
            'rekomendasi_visitasi' => $path_rekomendasi_visitasi,
        ];

        Pengajuan::updateOrCreate(['id' => $idpengajuan], $UpdateData);

        return redirect()->back();
    }

    public function storeBeritaAcara(Request $request)
    {
        $request->validate([
            'id_pengajuan' => 'required|integer',
            'berita_acara' => 'nullable|file|mimes:pdf',
        ]);

        $idpengajuan = $request->id_pengajuan;
        $path_berita_acara = null;
        if ($request->hasFile('berita_acara')) {
            $berita_acara = $request->file('berita_acara');
            $path_berita_acara = Uploadfile::upload($idpengajuan, $berita_acara, 'berita_acara');
        }

        // Update pengajuan
        $UpdateData = [
            'berita_acara' => $path_berita_acara,
            'visitasi' => 1,
        ];
        Pengajuan::updateOrCreate(['id' => $idpengajuan], $UpdateData);

        // Set is_lock sesuai periode reupload otomatis (Opsi C):
        // tgl_surat dari tr_digital_signatures -> start = H+1, end = H+3 hari kerja
        $pengajuan = Pengajuan::find($idpengajuan);
        if ($pengajuan && $pengajuan->id_profile) {
            $profile = \App\Models\Profile::find($pengajuan->id_profile);

            $signature = \App\Models\DigitalSignature::where('pengajuan_id', $idpengajuan)
                ->whereNotNull('tgl_surat')
                ->orderByDesc('tgl_surat')
                ->first();

            if ($signature) {
                $tglSurat = \Carbon\Carbon::parse($signature->tgl_surat);
                $startReupload = $profile->getStartReuploadAttribute(); // H+1 hari kerja
                $endReupload = $profile->getEndReuploadAttribute();     // H+3 hari kerja
                $today = \Carbon\Carbon::today();

                $isLock = 1;
                if ($today->gte($startReupload) && $today->lte($endReupload)) {
                    $isLock = 0; // dalam rentang → terbuka
                }

                $profile->forceFill(['is_lock' => $isLock])->saveQuietly();

                session()->flash('success', "Pengisian dibuka kembali mulai {$startReupload->isoFormat('D MMMM Y')} sampai dengan {$endReupload->isoFormat('D MMMM Y')}");
            }
        }

        return redirect()->back();
    }

    public function storeSertifikatAkreditasi(Request $request)
    {
        $idpengajuan = $request->id_pengajuan;
        $path_sertifikat_hasil_akreditasi = null;
        if ($request->hasFile('sertifikat_hasil_akreditasi')) {
            $sertifikat_hasil_akreditasi = $request->file('sertifikat_hasil_akreditasi');
            $path_sertifikat_hasil_akreditasi = Uploadfile::upload($idpengajuan, $sertifikat_hasil_akreditasi, 'sertifikat_hasil_akreditasi');
        }

        $UpdateData = [
            'sertifikat_hasil_akreditasi' => $path_sertifikat_hasil_akreditasi,
        ];

        Pengajuan::updateOrCreate(['id' => $idpengajuan], $UpdateData);

        return redirect()->back();
    }

    private function updatePengajuan($id, $nilai, $jenis)
    {
        // Validasi jenis yang diperbolehkan
        $jenisValid = ['pravisit2', 'paskavisit', 'final'];

        if (!in_array($jenis, $jenisValid)) {
            throw new \InvalidArgumentException('Jenis nilai tidak valid. Gunakan: '.implode(', ', $jenisValid));
        }

        // Hitung predikat berdasarkan nilai
        $predikat = $this->getPredikat($nilai);

        // Data yang akan diupdate
        $updateData = [
            "nilai_{$jenis}" => $nilai,
            "predikat_{$jenis}" => $predikat,
        ];

        Pengajuan::updateOrCreate(
            ['id' => $id],
            $updateData
        );
    }

    /**
     * Replace template placeholders in Excel sheet.
     */
    private function replaceTemplatePlaceholders($sheet, $placeholders)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; ++$row) {
            for ($col = 'A'; $col <= $highestColumn; ++$col) {
                $cellValue = $sheet->getCell($col.$row)->getValue();
                if (is_string($cellValue)) {
                    foreach ($placeholders as $placeholder => $replacement) {
                        if (strpos($cellValue, $placeholder) !== false) {
                            $newValue = str_replace($placeholder, $replacement, $cellValue);
                            $sheet->setCellValue($col.$row, $newValue);
                        }
                    }
                }
            }
        }
    }

    /**
     * Find the starting row for data entry in template.
     */
    private function findDataStartRow($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Look for common header patterns to determine data start row
        for ($row = 1; $row <= $highestRow; ++$row) {
            $cellValue = $sheet->getCell('A'.$row)->getValue();
            if (is_string($cellValue) && (strtolower($cellValue) === 'no' || $cellValue === 'No')) {
                return $row + 1; // Data starts after header row
            }
        }

        // Default to row 2 if no header pattern found
        return 2;
    }

    private function getPredikat($nilai)
    {
        if ($nilai >= 3.51) {
            return 'Terakreditasi A';
        } elseif ($nilai >= 3.00) {
            return 'Terakreditasi B';
        }

        return 'Tidak Terakreditasi';
    }

    public function nilaiItemPra(Request $request)
    {
        $pengajuan = $request->idpengajuan;
        $asesor = auth()->user()->id;
        $item = $request->iditem;
        $penilaian = Penilaian::where('id_pengajuan', $pengajuan)->where('id_asesor', $asesor)->where('id_item_penilaian', $item)->where('pra_paska', 'pra')->first();

        return response()->json($penilaian);
    }

    public function nilaiItemPra2(Request $request)
    {
        $pengajuan = $request->idpengajuan;
        $item = $request->iditem;
        $penilaian = Penilaian::where('id_pengajuan', $pengajuan)->where('id_item_penilaian', $item)->where('pra_paska', 'pra2')->first();

        return response()->json($penilaian);
    }

    public function nilaiItemPaska(Request $request)
    {
        $pengajuan = $request->idpengajuan;
        $item = $request->iditem;
        $penilaian = Penilaian::where('id_pengajuan', $pengajuan)->where('id_item_penilaian', $item)->where('pra_paska', 'paska')->first();

        return response()->json($penilaian);
    }

    public function nilaiItemFinal(Request $request)
    {
        $pengajuan = $request->idpengajuan;
        $item = $request->iditem;
        $penilaian = Penilaian::where('id_pengajuan', $pengajuan)->where('id_item_penilaian', $item)->where('pra_paska', 'final')->first();

        return response()->json($penilaian);
    }

    public function catatanItemPra(Request $request)
    {
        $request->validate([
            'idpengajuan' => 'required|integer',
            'iditem' => 'required|integer',
            'asesor' => 'required|integer',
        ]);

        $pengajuan = $request->idpengajuan;
        $item = $request->iditem;
        $asesor = $request->asesor;

        $catatan = Penilaian::where('id_pengajuan', $pengajuan)
            ->where('id_item_penilaian', $item)
            ->where('id_asesor', $asesor)
            ->first();

        if ($catatan && !empty($catatan->catatan)) {
            return response()->json($catatan->catatan);
        } else {
            return response()->json([]); // Kembalikan object kosong, bukan string
        }
    }

    public function nilaiPra(Request $request)
    {
        $validatedData = $request->validate([
            'id_pengajuan' => 'required|integer',
            'id_item' => 'required|integer',
            'nilai' => 'required',
            'catatan' => 'nullable|string',
            'id_asesor' => 'required',
        ]);

        $asesorId = $validatedData['id_asesor'];
        $pengajuanId = $validatedData['id_pengajuan'];
        $itemId = $validatedData['id_item'];

        // Data untuk create/update
        $data = [
            'id_asesor' => $asesorId,
            'id_pengajuan' => $pengajuanId,
            'id_item_penilaian' => $itemId,
            'nilai' => $validatedData['nilai'],
            'catatan' => $validatedData['catatan'],
        ];

        // Handle pra penilaian
        $this->handlePenilaian($pengajuanId, $asesorId, $itemId, 'pra', $data);

        return redirect()->back();
    }

    public function nilaiPra2(Request $request)
    {
        $validatedData = $request->validate([
            'id_pengajuan' => 'required|integer',
            'id_item' => 'required|integer',
            'nilai' => 'required',
            'catatan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);

        $asesorId = auth()->id();
        $pengajuanId = $validatedData['id_pengajuan'];
        $itemId = $validatedData['id_item'];

        // Data untuk create/update
        $data = [
            'id_asesor' => $asesorId,
            'id_pengajuan' => $pengajuanId,
            'id_item_penilaian' => $itemId,
            'nilai' => $validatedData['nilai'],
            'catatan' => $validatedData['catatan'],
            'rekomendasi' => $validatedData['rekomendasi'],
        ];

        // Handle pra2 penilaian dan copas ke paska
        $this->handlePenilaian($pengajuanId, $asesorId, $itemId, 'pra2', $data);

        return redirect()->back();
    }

    public function nilaiPaska(Request $request)
    {
        $validatedData = $request->validate([
            'id_pengajuan' => 'required|integer',
            'id_item' => 'required|integer',
            'nilai' => 'required',
            'catatan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'pengecekan_visitasi' => 'nullable|string',
        ]);

        $asesorId = auth()->id();
        $pengajuanId = $validatedData['id_pengajuan'];
        $itemId = $validatedData['id_item'];

        // Data untuk create/update
        $data = [
            'id_asesor' => $asesorId,
            'id_pengajuan' => $pengajuanId,
            'id_item_penilaian' => $itemId,
            'nilai' => $validatedData['nilai'],
            'catatan' => $validatedData['catatan'],
            'rekomendasi' => $validatedData['rekomendasi'],
            'pengecekan_visitasi' => $validatedData['pengecekan_visitasi'],
        ];
        // Handle paska penilaian dan final penilaian
        $this->handlePenilaian($pengajuanId, $asesorId, $itemId, 'paska', $data);

        return redirect()->back();
    }

    public function nilaiFinal(Request $request)
    {
        $validatedData = $request->validate([
            'id_pengajuan' => 'required|integer',
            'id_item' => 'required|integer',
            'nilai' => 'required',
            'catatan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'pengecekan_visitasi' => 'nullable|string',
        ]);

        $asesorId = auth()->id();
        $pengajuanId = $validatedData['id_pengajuan'];
        $itemId = $validatedData['id_item'];

        // Data untuk create/update
        $data = [
            'id_asesor' => $asesorId,
            'id_pengajuan' => $pengajuanId,
            'id_item_penilaian' => $itemId,
            'nilai' => $validatedData['nilai'],
            'catatan' => $validatedData['catatan'],
            'rekomendasi' => $validatedData['rekomendasi'],
            'pengecekan_visitasi' => $validatedData['pengecekan_visitasi'],
        ];

        // Handle paska penilaian
        $this->handlePenilaian($pengajuanId, $asesorId, $itemId, 'final', $data);

        return redirect()->back();
    }

    private function handlePenilaian($pengajuanId, $asesorId, $itemId, $type, $data)
    {
        $penilaian = Penilaian::firstOrNew([
            'id_pengajuan' => $pengajuanId,
            'id_asesor' => $asesorId,
            'id_item_penilaian' => $itemId,
            'pra_paska' => $type,
        ]);

        $penilaian->fill($data);
        $penilaian->save();
    }

    public function nilaiPraSubmit(Request $request)
    {
        $asesor = Pengajuan::getasesor($request->id);
        $field = "pra_visit_asesor{$asesor}";

        Pengajuan::where('id', $request->id)->update([$field => 1]);

        return redirect()->route('home');
    }

    public function nilaiPra2Submit(Request $request)
    {
        // Duplicate nilai pra2 ke paska sebelum submit
        $this->duplicateNilaiToNextStage($request->id, 'pra2', 'paska');
        
        return $this->updateStatusSubmit($request->id, 'pra_visit2_asesor');
    }
    
    /**
     * Duplicate semua nilai dari satu tahap ke tahap berikutnya
     * 
     * @param int $pengajuanId ID pengajuan
     * @param string $fromStage Tahap asal (pra2, paska)
     * @param string $toStage Tahap tujuan (paska, final)
     */
    private function duplicateNilaiToNextStage($pengajuanId, $fromStage, $toStage)
    {
        // Ambil semua penilaian dari tahap asal untuk pengajuan ini
        $penilaianSource = Penilaian::where('id_pengajuan', $pengajuanId)
            ->where('pra_paska', $fromStage)
            ->get();
            
        foreach ($penilaianSource as $source) {
            // Cek apakah sudah ada penilaian di tahap tujuan untuk item dan asesor yang sama
            $existingTarget = Penilaian::where('id_pengajuan', $pengajuanId)
                ->where('id_asesor', $source->id_asesor)
                ->where('id_item_penilaian', $source->id_item_penilaian)
                ->where('pra_paska', $toStage)
                ->first();
                
            // Jika belum ada, buat duplikat dari tahap asal ke tahap tujuan
            if (!$existingTarget) {
                $duplicateData = [
                    'id_asesor' => $source->id_asesor,
                    'id_pengajuan' => $source->id_pengajuan,
                    'id_item_penilaian' => $source->id_item_penilaian,
                    'pra_paska' => $toStage,
                    'nilai' => $source->nilai,
                    'catatan' => $source->catatan,
                    'rekomendasi' => $source->rekomendasi,
                ];
                
                // Field khusus untuk tahap tertentu
                if ($toStage === 'paska' || $toStage === 'final') {
                    $duplicateData['pengecekan_visitasi'] = $source->pengecekan_visitasi ?? null;
                }
                
                Penilaian::create($duplicateData);
            }
        }
    }

    public function nilaiPaskaSubmit(Request $request)
    {
        // Duplicate nilai paska ke final sebelum submit
        $this->duplicateNilaiToNextStage($request->id, 'paska', 'final');
        
        return $this->updateStatusSubmit($request->id, 'paska_visit');
    }

    public function nilaiFinalSubmit(Request $request)
    {
        return $this->updateStatusSubmit($request->id, 'final');
    }

    protected function updateStatusSubmit($id, $field)
    {
        Pengajuan::where('id', $id)->update([$field => 1]);

        return redirect()->route('home');
    }

    public function identitasLembaga($id)
    {
        $profile = Profile::find($id);
        $provinsi = Wilayah::where('level', 1)->get();
        $kabkota = [];
        $step = 1;

        if ($profile->provinsi) {
            $_provinsi = Wilayah::find($profile->provinsi);
            $kabkota = Wilayah::where('kode', 'like', $_provinsi->kode.'.%')->get();
        }

        switch ($step) {
            case 1:
                $step_name = 'Identitas Lembaga Penyelenggara';
                break;
            case 2:
                $step_name = 'Dokumen Pendukung';
                break;
        }

        return view('asesor.identitas-lembaga', [
            'step' => $step,
            'step_name' => $step_name,
            'provinsi' => $provinsi,
            'kabkota' => $kabkota,
            'profile' => $profile,
        ]);
    }
}
