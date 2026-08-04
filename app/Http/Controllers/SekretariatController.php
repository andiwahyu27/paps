<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenaga;
use App\Models\Profile;
use App\Models\Fasilitas;
use App\Models\Pelatihan;
use App\Models\Pengajuan;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use App\Models\DokumenProgram;
use App\Models\TenagaPelatihan;
use App\Models\DokumenPelatihan;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Schema;
use App\Helpers\Uploadfile;
use App\Http\Controllers\Lembaga\PengajuanController;

class SekretariatController extends Controller
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

    public function pengguna($role = 4)
    {
        $pengguna = User::where('role', $role)->get();

        return view('sekretariat.pengguna', ['role' => $role, 'pengguna' => $pengguna]);
    }

    public function tambahPengguna(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => 'required|min:3|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:3|max:255',
            'role'     => 'required',
        ]);

        // Cek apakah email sudah terdaftar
        if (User::where('email', $validatedData['email'])->exists()) {
            return back()->with('error', 'Email sudah terdaftar.');
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);
        return back()->with('success', 'Berhasil menambahkan pengguna.');
    }

    public function ubahPengguna(Request $request)
    {
        User::find($request->id)->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return back()->with('success', 'Berhasil mengubah data pengguna');
    }

    public function hapusPengguna(Request $request)
    {
        User::destroy($request->id);

        return back()->with('success', 'Berhasil menghapus data pengguna');
    }

    public function loginPengguna($id)
    {
        try {
            $dataUser = User::find($id);

            if ($dataUser) {
                session(['original_user_id' => auth()->id()]); // Simpan user asli
                Auth::loginUsingId($id);
                return redirect()->route('home')->with("success", 'Sukses Login Sebagai ' . $dataUser->name);
            } else {
                return redirect()->back()->with('error', 'Data User Tidak Ditemukan!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi Kesalahan Saat Melakukan Login!');
        }
    }

    public function backToReality()
    {
        try {
            $originalUserId = session('original_user_id');

            if ($originalUserId) {
                Auth::loginUsingId($originalUserId); // Login kembali ke user asli
                session()->forget('original_user_id'); // Hapus dari session
                return redirect()->route('home')->with('success', 'Berhasil kembali ke akun asli');
            } else {
                return redirect()->back()->with('error', 'Tidak ada session pengguna sebelumnya!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal kembali ke akun asli!');
        }
    }

    public function lihatPermohonan($id)
    {
        $pengajuanController = new \App\Http\Controllers\Lembaga\PengajuanController();
        $data = $pengajuanController->calculatedProgresPelatihan($id);
        $data['asesors'] = User::where('role', 3)->get();
        return view('sekretariat.lihat-pengajuan', $data);
    }

    public function verifikasiPermohonan(Request $request)
    {
        $surat_tanggapan_permohonan = $request->file('surat_tanggapan_permohonan');
        if (file_exists($surat_tanggapan_permohonan)) {
            $path_surat_tanggapan_permohonan = Uploadfile::upload(auth()->user()->id, $surat_tanggapan_permohonan, 'surat_tanggapan_permohonan');
        } else {
            $path_surat_tanggapan_permohonan = null;
        }

        Pengajuan::where('id', $request->id)->update([
            'surat_tanggapan_permohonan' => $path_surat_tanggapan_permohonan,
            'verifikasi_permohonan' => $request->status
        ]);
        // dd($request);
        return back()->with('success', 'Berhasil verifikasi pengajuan lembaga');
    }

    public function tambahPelatihan(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:255|min:3',
            'angkatan' => 'required|max:5|min:1',
            'tahun' => 'required|max:4|min:4',
            'id_pengajuan' => 'required'
        ]);
        $pelatihan = Pelatihan::create($validatedData);

        return back()->with('success', 'Berhasil menambahkan pelatihan');
    }

    public function ubahPelatihan(Request $request)
    {
        Pelatihan::find($request->id)->update([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'tahun' => $request->tahun
        ]);

        return back()->with('success', 'Berhasil mengubah data pelatihan');
    }

    public function hapusPelatihan(Request $request)
    {
        Pelatihan::destroy($request->id);

        return back()->with('success', 'Berhasil menghapus data pelatihan');
    }

    public function lembaga()
    {
        $lembaga = Profile::all();
        $pengguna = User::where('role', 4)->whereNull('id_profile')->get();

        return view('sekretariat.lembaga', ['lembaga' => $lembaga, 'pengguna' => $pengguna]);
    }

    public function tambahLembaga(Request $request)
    {
        $validatedData = $request->validate([
            'nama_lembaga' => 'required|max:255|min:3'
        ]);
        $lembaga = Profile::create($validatedData);

        return back()->with('success', 'Berhasil menambahkan lembaga');
    }

    public function tambahPic(Request $request)
    {
        $validatedData = $request->validate([
            'id_profile' => 'required',
            'id_user' => 'required'
        ]);
        User::find($request->id_user)->update([
            'id_profile' => $request->id_profile
        ]);

        return back()->with('success', 'Berhasil menambahkan PIC lembaga');
    }

    public function hapusPic(Request $request)
    {
        $validatedData = $request->validate([
            'id_user' => 'required'
        ]);
        User::find($request->id_user)->update([
            'id_profile' => NULL
        ]);

        return back()->with('success', 'Berhasil mengahapus PIC Lembaga');
    }

    public function assignAsesor(Request $request)
    {
        Pengajuan::find($request->id)->update([
            'id_asesor1' => $request->asesor1,
            'id_asesor2' => $request->asesor2,
            'id_asesor3' => $request->asesor3
        ]);

        return back()->with('success', 'Berhasil assign asesor');
    }

    /**
     * Show monitoring and evaluation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function monitoringEvaluasi()
    {
        return view('sekretariat.monitoring-evaluasi');
    }

    /**
     * Show monitoring penyelenggaraan detail page.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function monitoringPenyelenggaraan($id)
    {
        // Dummy data - in real implementation, fetch from database based on $id
        $lembaga = [
            'nama' => 'Pusdiklat Tekfunghan Badiklat Kementerian Pertahanan Republik Indonesia',
            'lokasi' => 'Jakarta',
            'status' => 'Terakreditasi A',
            'masa_berlaku' => '26 September 2029'
        ];

        $totalPeserta = 200;
        $rataRataNilai = 85.7;
        $evaluasiPenyelenggaraan = 88.5;

        return view('sekretariat.monitoring-penyelenggaraan', compact(
            'lembaga',
            'totalPeserta', 
            'rataRataNilai',
            'evaluasiPenyelenggaraan'
        ));
    }

    public function lihatRekap($id)
    {
        $pelatihan = Pelatihan::find($id);
        $profile = Profile::find($pelatihan->pengajuan->id_profile);
        $columnProfile = Schema::getColumnListing('tb_profile_lembagas');
        // Daftar kolom yang ingin dikecualikan
        $excludedColumns = [
            'id',
            'nomor_akte_pendirian',
            'tanggal_akte_pendirian',
            'ttd_akte_pendirian',
            'path_akte_pendirian',
            'is_lock',
            'created_at',
            'updated_at' //8 poin
        ];

        // Menyaring kolom yang tidak termasuk dalam daftar pengecualian
        $filteredColumns = array_diff($columnProfile, $excludedColumns);
        // dd($filteredColumns);
        $fasilitator = Tenaga::where('jenis_tenaga', 1)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $pengelolaPelatihan = Tenaga::where('jenis_tenaga', 2)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $pengelolaKelas = Tenaga::where('jenis_tenaga', 3)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $pengelolaSi = Tenaga::where('jenis_tenaga', 4)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $analisDiklat = Tenaga::where('jenis_tenaga', 5)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $fasilitasUmum = Fasilitas::where('tipe', 1)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $fasilitasDiklat = Fasilitas::where('tipe', 2)->where('id_profile', $pelatihan->pengajuan->id_profile)->get();
        $tenaga = TenagaPelatihan::where('id_pelatihan', $id)->get();
        $jenis_dokumen = DokumenProgram::all();
        $docs = DokumenPelatihan::where('id_pelatihan', $id)->get();
        // dd($fasilitasUmum, $fasilitasDiklat);
        return view('sekretariat.rekap', [
            'pelatihan' => $pelatihan,
            'profile' => $profile,
            'filteredColumns' => $filteredColumns,
            'jenis_dokumen' => $jenis_dokumen,
            'docs' => $docs,
            'fasilitator' => $fasilitator,
            'pengelolaPelatihan' => $pengelolaPelatihan,
            'pengelolaKelas' => $pengelolaKelas,
            'pengelolaSi' => $pengelolaSi,
            'analisDiklat' => $analisDiklat,
            'fasilitasUmum' => $fasilitasUmum,
            'fasilitasDiklat' => $fasilitasDiklat,
            'tenaga' => $tenaga
        ]);
    }
}
