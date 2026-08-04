<?php

namespace App\Http\Controllers\Lembaga;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\DokumenPelatihan;
use App\Models\DokumenProgram;
use App\Models\Fasilitas;
use App\Models\JenisPengajuan;
use App\Models\Pelatihan;
use App\Models\Pengajuan;
use App\Models\Tenaga;
use App\Helpers\Uploadfile;
use Illuminate\Support\Facades\Schema;
use App\Models\Profile;

use function PHPUnit\Framework\isNull;

class PengajuanController extends Controller
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
    public function permohonan($type)
    {
        $pengajuan = Pengajuan::where('id_profile', auth()->user()->id_profile)
            ->where('id_jenis', $type)
            ->where('verifikasi_permohonan', '<>', 2)
            ->latest()
            ->first();

        $jenis = JenisPengajuan::find($type);

        if ($pengajuan) {
            $pelatihan = Pelatihan::where('id_pengajuan', $pengajuan->id)->get();

            if ($pelatihan->isNotEmpty()) {
                return redirect()->route('riwayat.pengajuan', $pengajuan->id);
            }
        }

        return view('lembaga.permohonan', [
            'pengajuan' => $pengajuan,
            'type' => $type,
            'jenis' => $jenis,
        ]);
    }

    public function editPermohonan($type)
    {
        $pengajuan = Pengajuan::where('id_profile', auth()->user()->id_profile)
            ->where('id_jenis', $type)
            ->where('verifikasi_permohonan', '<>', 2)
            ->latest()
            ->first();

        $jenis = JenisPengajuan::find($type);

        return view('lembaga.permohonan', [
            'pengajuan' => $pengajuan,
            'type' => $type,
            'jenis' => $jenis,
        ]);
    }

    public function riwayatPermohonan($id)
    {
        $data = $this->calculatedProgresPelatihan($id);
        return view('lembaga.pengajuan', $data);
    }

    public function calculatedProgresPelatihan($id)
    {
        $pengajuan = Pengajuan::find($id);
        $pelatihan = Pelatihan::where("id_pengajuan", $pengajuan->id)->get();
        $profile = Profile::find($pengajuan->id_profile);

        // Hitung progress profile
        $columnProfile = Schema::getColumnListing('tb_profile_lembagas');
        $excludedColumns = ['id', 'nomor_akte_pendirian', 'tanggal_akte_pendirian', 'ttd_akte_pendirian', 'path_akte_pendirian', 'is_lock', 'created_at', 'updated_at'];
        $filteredColumns = array_diff($columnProfile, $excludedColumns);

        $c = count($filteredColumns) + 7; // 7 poin tambahan
        $n = 0;
        $nullProfile = [];

        foreach ($filteredColumns as $column) {
            if (is_null($profile->$column)) {
                $nullProfile[] = $column;
                $n++;
            }
        }

        // Hitung tenaga kerja dan fasilitas
        $tenagaTypes = [
            1 => "Fasilitator",
            2 => "Pengelola Pelatihan",
            3 => "Pengelola Kelas",
            4 => "Pengelola SI",
            5 => "Analis Kebutuhan Diklat"
        ];

        foreach ($tenagaTypes as $type => $label) {
            if (Tenaga::where('jenis_tenaga', $type)->where('id_profile', $pengajuan->id_profile)->doesntExist()) {
                $nullProfile[] = $label;
                $n++;
            }
        }

        if (Fasilitas::where('tipe', 1)->where('id_profile', $pengajuan->id_profile)->doesntExist()) {
            $nullProfile[] = "Fasilitas Umum";
            $n++;
        }
        if (Fasilitas::where('tipe', 2)->where('id_profile', $pengajuan->id_profile)->doesntExist()) {
            $nullProfile[] = "Fasilitas Diklat";
            $n++;
        }

        // Hitung progress pelatihan
        $nullPelatihan = [];
        $columnPelatihan = DokumenProgram::all();
        $tcp = 0;
        $tnp = 0;

        foreach ($pelatihan as $p) {
            $tempProgress = ['pelatihan' => $p->fullname(), 'nullEachPelatihan' => []];
            $cp = $columnPelatihan->count();
            $np = 0;

            foreach ($columnPelatihan as $col) {
                if (DokumenPelatihan::where('id_pelatihan', $p->id)->where('dokumen_id', $col->id)->doesntExist()) {
                    $tempProgress['nullEachPelatihan'][] = $col->nama;
                    $np++;
                }
            }

            $p->progress = (($cp - $np) / $cp) * 100;
            $nullPelatihan[] = $tempProgress;
            $tcp += $cp;
            $tnp += $np;
        }

        $progressProfile = ($c > 0) ? (($c - $n) / $c) * 100 : 0;
        $progressPelatihan = ($pelatihan->count() > 0 && $tcp > 0) ? (($tcp - $tnp) / $tcp) * 100 : 0;

        return [
            'pengajuan' => $pengajuan,
            'pelatihan' => $pelatihan,
            'progressProfile' => $progressProfile,
            'nullProfile' => $nullProfile,
            'progressPelatihan' => $progressPelatihan,
            'nullPelatihan' => $nullPelatihan
        ];
    }

    public function storePermohonan(Request $request)
    {
        // Cek id_profile
        if (is_null(auth()->user()->id_profile)) {
            return redirect()->route('error')->withInput([
                'judul' => 'Error',
                'pesan' => 'Akun Anda belum didaftarkan sebagai PIC Lembaga. Silahkan hubungi Tim Sekretariat Akreditasi untuk mendaftar.'
            ]);
        }

        $surat_permohonan = $request->file('surat_permohonan');
        $path_surat_permohonan = Uploadfile::upload(auth()->user()->id, $surat_permohonan, 'surat_permohonan');

        $surat_akreditasi_lembaga = $request->file('surat_akreditasi_lembaga');
        if (file_exists($surat_akreditasi_lembaga)) {
            $path_surat_akreditasi_lembaga = Uploadfile::upload(auth()->user()->id, $surat_akreditasi_lembaga, 'surat_akreditasi_lembaga');
        } else {
            $path_surat_akreditasi_lembaga = null;
        }

        Pengajuan::create([
            'id_profile' => auth()->user()->id_profile,
            'surat_permohonan' => $path_surat_permohonan,
            'surat_akreditasi_lembaga' => $path_surat_akreditasi_lembaga,
            'verifikasi_permohonan' => 0,
            'id_jenis' => $request->jenis
        ]);

        return redirect()->route('pengajuan', $request->jenis);
    }

    public function updatePermohonan(Request $request)
    {
        $pengajuan = Pengajuan::find($request->id);

        $surat_permohonan = $request->file('surat_permohonan');
        if (file_exists($surat_permohonan)) {
            $path_surat_permohonan = Uploadfile::upload(auth()->user()->id, $surat_permohonan, 'surat_permohonan');
        } else {
            $path_surat_permohonan = $pengajuan->surat_permohonan;
        }

        $surat_akreditasi_lembaga = $request->file('surat_akreditasi_lembaga');
        if (file_exists($surat_akreditasi_lembaga)) {
            $path_surat_akreditasi_lembaga = Uploadfile::upload(auth()->user()->id, $surat_akreditasi_lembaga, 'surat_akreditasi_lembaga');
        } else {
            $path_surat_akreditasi_lembaga = $pengajuan->surat_permohonan;
        }

        Pengajuan::where('id', $request->id)->update([
            'surat_permohonan' => $path_surat_permohonan,
            'surat_akreditasi_lembaga' => $path_surat_akreditasi_lembaga,
            'verifikasi_permohonan' => 0
        ]);

        return redirect()->route('pengajuan', $pengajuan->jenis);
    }

    public function batalPermohonan(Request $request)
    {
        Pengajuan::where('id', $request->id)->update([
            'verifikasi_permohonan' => 2
        ]);

        return redirect()->route('home');
    }
}
