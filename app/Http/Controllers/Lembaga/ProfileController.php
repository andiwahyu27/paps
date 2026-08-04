<?php

namespace App\Http\Controllers\Lembaga;

use App\Models\User;
use App\Models\Tenaga;
use App\Models\Pangkat;
use App\Models\Profile;
use App\Models\Wilayah;
use App\Models\Fasilitas;
use App\Models\Pelatihan;
use App\Models\Pengajuan;
use App\Models\Penilaian;
use App\Helpers\Uploadfile;
use App\Models\RiwayatKerja;
use Illuminate\Http\Request;
use App\Models\DokumenTenaga;
use App\Models\JenisPengajuan;
use App\Models\RiwayatJabatan;
use App\Models\DokumenPelatihan;
use App\Models\RiwayatPelatihan;
use App\Models\RiwayatPendidikan;
use App\Models\JenisDokumenTenaga;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
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
    public function kelembagaan($step = 1)
    {
        $profile = Profile::find(auth()->user()->id_profile);
        $provinsi = Wilayah::where('level', 1)->get();
        $kabkota = [];

        if ($profile->provinsi) {
            $_provinsi = Wilayah::find($profile->provinsi);
            $kabkota = Wilayah::where('kode', 'like', $_provinsi->kode . '.%')->get();
        }

        switch ($step) {
            case 1:
                $step_name = "Identitas Lembaga Penyelenggara";
                break;
            case 2:
                $step_name = "Dokumen Pendukung";
                break;
        }

        return view('lembaga.profile.kelembagaan', [
            'step' => $step,
            'step_name' => $step_name,
            'provinsi' => $provinsi,
            'kabkota' => $kabkota,
            'profile' => $profile
        ]);
    }

    public function penyelenggaraan($step = 1)
    {
        $profile = Profile::find(auth()->user()->id_profile);
        switch ($step) {
            case 1:
                $step_name = "Program Pelatihan";
                break;
            case 2:
                $step_name = "Penjamin Mutu";
                break;
        }

        return view('lembaga.profile.penyelenggaraan', [
            'step' => $step,
            'step_name' => $step_name,
            'profile' => $profile
        ]);
    }

    /* public function updateProfile(Request $request)
    {
        $profile = Profile::find($request->id_profile);
        $validatedData = $request->validate(Profile::$updateRules);

        $path_surat_pernyataan_pimpinan = $request->file('path_surat_pernyataan_pimpinan');
        if (file_exists($path_surat_pernyataan_pimpinan)) {
            $validatedData['path_surat_pernyataan_pimpinan'] = Uploadfile::upload(auth()->user()->id, $path_surat_pernyataan_pimpinan, 'dokumen_profile');
        }

        $path_sk_pemerintah = $request->file('path_sk_pemerintah');
        if (file_exists($path_sk_pemerintah)) {
            $validatedData['path_sk_pemerintah'] = Uploadfile::upload(auth()->user()->id, $path_sk_pemerintah, 'dokumen_profile');
        }

        $path_surat_izin_operasional = $request->file('path_surat_izin_operasional');
        if (file_exists($path_surat_izin_operasional)) {
            $validatedData['path_surat_izin_operasional'] = Uploadfile::upload(auth()->user()->id, $path_surat_izin_operasional, 'dokumen_profile');
        }

        $path_akte_pendirian = $request->file('path_akte_pendirian');
        if (file_exists($path_akte_pendirian)) {
            $validatedData['path_akte_pendirian'] = Uploadfile::upload(auth()->user()->id, $path_akte_pendirian, 'dokumen_profile');
        }

        $path_rencana_keiatan = $request->file('path_rencana_keiatan');
        if (file_exists($path_rencana_keiatan)) {
            $validatedData['path_rencana_keiatan'] = Uploadfile::upload(auth()->user()->id, $path_rencana_keiatan, 'dokumen_profile');
        }

        $path_kegiatan_diklat = $request->file('path_kegiatan_diklat');
        if (file_exists($path_kegiatan_diklat)) {
            $validatedData['path_kegiatan_diklat'] = Uploadfile::upload(auth()->user()->id, $path_kegiatan_diklat, 'dokumen_profile');
        }

        $path_pembiayaan = $request->file('path_pembiayaan');
        if (file_exists($path_pembiayaan)) {
            $validatedData['path_pembiayaan'] = Uploadfile::upload(auth()->user()->id, $path_pembiayaan, 'dokumen_profile');
        }

        $path_sop_perencanaan = $request->file('path_sop_perencanaan');
        if (file_exists($path_sop_perencanaan)) {
            $validatedData['path_sop_perencanaan'] = Uploadfile::upload(auth()->user()->id, $path_sop_perencanaan, 'dokumen_profile');
        }

        $path_sop_pelaksanaan = $request->file('path_sop_pelaksanaan');
        if (file_exists($path_sop_pelaksanaan)) {
            $validatedData['path_sop_pelaksanaan'] = Uploadfile::upload(auth()->user()->id, $path_sop_pelaksanaan, 'dokumen_profile');
        }

        $path_sop_evalap = $request->file('path_sop_evalap');
        if (file_exists($path_sop_evalap)) {
            $validatedData['path_sop_evalap'] = Uploadfile::upload(auth()->user()->id, $path_sop_evalap, 'dokumen_profile');
        }

        $path_bukti_sosialisasi_sop = $request->file('path_bukti_sosialisasi_sop');
        if (file_exists($path_bukti_sosialisasi_sop)) {
            $validatedData['path_bukti_sosialisasi_sop'] = Uploadfile::upload(auth()->user()->id, $path_bukti_sosialisasi_sop, 'dokumen_profile');
        }

        $path_laporan_penjaminan_mutu = $request->file('path_laporan_penjaminan_mutu');
        if (file_exists($path_laporan_penjaminan_mutu)) {
            $validatedData['path_laporan_penjaminan_mutu'] = Uploadfile::upload(auth()->user()->id, $path_laporan_penjaminan_mutu, 'dokumen_profile');
        }

        $profile->update($validatedData);

        return back()->with('success', 'Berhasil mengubah data profile');
    }
    */
  	
  	// update profile baru
  	public function updateProfile(Request $request)
	{
    $profile = Profile::find($request->id_profile);
    $validatedData = $request->validate(Profile::$updateRules);

    $fileFields = [
        'path_surat_pernyataan_pimpinan',
        'path_sk_pemerintah',
        'path_surat_izin_operasional',
        'path_akte_pendirian',
        'path_rencana_keiatan',
        'path_kegiatan_diklat',
        'path_pembiayaan',
        'path_sop_perencanaan',
        'path_sop_pelaksanaan',
        'path_sop_evalap',
        'path_bukti_sosialisasi_sop',
        'path_laporan_penjaminan_mutu',
    ];

    foreach ($fileFields as $field) {
        if ($request->hasFile($field) && $request->file($field)->isValid()) {
            $validatedData[$field] = Uploadfile::upload(
                auth()->user()->id,
                $request->file($field),
                'dokumen_profile'
            );
        }
    }

    $profile->update($validatedData);
    return back()->with('success', 'Berhasil mengubah data profile');
	}

    public function getKabkota(Request $a)
    {
        if ($a->ajax()) {
            $provinsi = Wilayah::find($a->id_provinsi);
            $kabkota = Wilayah::where('kode', 'like', $provinsi->kode . '.%')->get();

            return response()->json([
                'status' => 'success',
                'data' => $kabkota,
                'message' => 'Data Kabupaten/Kota Ditemukan'
            ], 200);
        }
    }

    public function fasilitas($step = 1)
    {
        $profile = Profile::find(auth()->user()->id_profile);
        $fasilitas = Fasilitas::where('tipe', $step)->where('id_profile', auth()->user()->id_profile)->get();
        switch ($step) {
            case 1:
                $step_name = "Sarpras Umum";
                break;
            case 2:
                $step_name = "Sarpras Pelatihan";
                break;
        }

        return view('lembaga.profile.fasilitas', [
            'step' => $step,
            'step_name' => $step_name,
            'profile' => $profile,
            'fasilitas' => $fasilitas
        ]);
    }

    public function tambahFasilitas(Request $request)
    {
        $validatedData = $request->validate(Fasilitas::$createRules);
        $validatedData['id_profile'] = auth()->user()->id_profile;
        $path_foto = $request->file('path_foto');
        if (file_exists($path_foto)) {
            $validatedData['path_foto'] = Uploadfile::upload(auth()->user()->id, $path_foto, 'dokumen_fasilitas');
        }
        $path_dokumen = $request->file('path_dokumen');
        if (file_exists($path_dokumen)) {
            $validatedData['path_dokumen'] = Uploadfile::upload(auth()->user()->id, $path_dokumen, 'dokumen_profile');
        }
        $path_pemeliharaan = $request->file('path_pemeliharaan');
        if (file_exists($path_pemeliharaan)) {
            $validatedData['path_pemeliharaan'] = Uploadfile::upload(auth()->user()->id, $path_pemeliharaan, 'dokumen_profile');
        }
        Fasilitas::create($validatedData);

        return back()->with('success', 'Berhasil menambahkan fasiltias');
    }

    public function ubahFasilitas(Request $request)
    {
        $validatedData = $request->validate(Fasilitas::$updateRules);
        $path_foto = $request->file('path_foto');
        if (file_exists($path_foto)) {
            $validatedData['path_foto'] = Uploadfile::upload(auth()->user()->id, $path_foto, 'dokumen_fasilitas');
        }
        $path_dokumen = $request->file('path_dokumen');
        if (file_exists($path_dokumen)) {
            $validatedData['path_dokumen'] = Uploadfile::upload(auth()->user()->id, $path_dokumen, 'dokumen_profile');
        }
        $path_pemeliharaan = $request->file('path_pemeliharaan');
        if (file_exists($path_pemeliharaan)) {
            $validatedData['path_pemeliharaan'] = Uploadfile::upload(auth()->user()->id, $path_pemeliharaan, 'dokumen_profile');
        }
        Fasilitas::find($request->id)->update($validatedData);

        return back()->with('success', 'Berhasil mengubah data fasilitas');
    }

    public function deleteFasilitas(Request $request)
    {
        Fasilitas::destroy($request->id);
        return back()->with('success', 'Berhasil menghapus data fasilitas');
    }

    public function tenaga($step = 1)
    {
        switch ($step) {
            case 1:
                $step_name = "Fasilitator";
                $tenaga = Tenaga::where('jenis_tenaga', 1)->where('id_profile', auth()->user()->id_profile)->get();
                break;
            case 2:
                $step_name = "Pengelola Pelatihan";
                $tenaga = Tenaga::where('jenis_tenaga', 2)->where('id_profile', auth()->user()->id_profile)->get();
                break;
            case 3:
                $step_name = "Pengelola Kelas";
                $tenaga = Tenaga::where('jenis_tenaga', 3)->where('id_profile', auth()->user()->id_profile)->get();
                break;
            case 4:
                $step_name = "Pengelola SI";
                $tenaga = Tenaga::where('jenis_tenaga', 4)->where('id_profile', auth()->user()->id_profile)->get();
                break;
            case 5:
                $step_name = "Analis Kebutuhan Diklat";
                $tenaga = Tenaga::where('jenis_tenaga', 5)->where('id_profile', auth()->user()->id_profile)->get();
                break;
        }

        $r_jabatan = RiwayatJabatan::get();
        $r_kerja = RiwayatKerja::get();
        $r_pelatihan = RiwayatPelatihan::get();
        $r_pendidikan = RiwayatPendidikan::get();
        $pangkat = Pangkat::get();
        $profile = Profile::find(auth()->user()->id_profile);

        return view('lembaga.profile.tenaga', [
            'step' => $step,
            'step_name' => $step_name,
            'tenagas' => $tenaga,
            'r_jabatans' => $r_jabatan,
            'r_kerjas' => $r_kerja,
            'r_pelatihans' => $r_pelatihan,
            'r_pendidikans' => $r_pendidikan,
            'pangkats' => $pangkat,
            'profile' => $profile
        ]);
    }

    public function tambahTenaga(Request $request)
    {
        $validatedData = $request->validate(Tenaga::$createRules);
        $validatedData['id_profile'] = auth()->user()->id_profile;
        Tenaga::create($validatedData);

        return back()->with('success', 'Berhasil menambahkan pengelola');
    }

    public function ubahTenaga(Request $request)
    {
        $validatedData = $request->validate(Tenaga::$updateRules);
        Tenaga::find($request->id)->update($validatedData);

        return back()->with('success', 'Berhasil mengubah data pengelola');
    }

    public function deleteTenaga(Request $request)
    {
        Tenaga::destroy($request->id);

        return back()->with('success', 'Berhasil menghapus data pengelola');
    }

    public function getModal(Request $request)
    {
        $id = $request->idtenaga;
        $data_riwayat = RiwayatJabatan::where('tenaga_id', $id)->get();

        return response()->json($data_riwayat);
    }

    public function tambahRiwayat(Request $request)
    {
        switch ($request->riwayat) {
            case 'jabatan':
                $riwayat = 'data Riwayat Jabatan';
                $data = $request->validate([
                    'jabatan' => 'required|min:3',
                    'tugas' => 'required|min:3',
                    'periode' => 'required|max:12',
                    'instansi' => 'required|min:3',
                    'tenaga_id' => 'required'
                ]);
                RiwayatJabatan::create($data);
                break;

            case 'kerja':
                $riwayat = 'data Pengalaman Kerja';
                $data = $request->validate([
                    'jabatan' => 'required|min:3',
                    'tugas' => 'required|min:3',
                    'tahun' => 'required|digits:4',
                    'instansi' => 'required|min:3',
                    'tenaga_id' => 'required'
                ]);
                RiwayatKerja::create($data);
                break;

            case 'pelatihan':
                $riwayat = 'data Riwayat Pelatihan';
                $data = $request->validate([
                    'pelatihan' => 'required|min:3',
                    'penyelenggara' => 'required|min:3',
                    'tahun' => 'required|digits:4',
                    'sertifikasi' => 'required|min:3',
                    'keterangan' => 'nullable',
                    'tenaga_id' => 'required'
                ]);
                RiwayatPelatihan::create($data);
                break;

            case 'pendidikan':
                $riwayat = 'data Riwayat Pendidikan';
                $data = $request->validate([
                    'jenjang' => 'required|min:2',
                    'sekolah' => 'required|min:3',
                    'tahun' => 'required|digits:4',
                    'kota_negara' => 'required|min:3',
                    'keterangan' => 'nullable',
                    'tenaga_id' => 'required'
                ]);
                RiwayatPendidikan::create($data);
                break;
        }

        return back()->with('success', 'Berhasil menambahkan ' . $riwayat);
    }

    public function ubahRiwayat(Request $request)
    {
        switch ($request->riwayat) {
            case 'jabatan':
                $riwayat = 'data Riwayat Jabatan';
                RiwayatJabatan::find($request->id)->update([
                    'jabatan' => $request->jabatan,
                    'tugas' => $request->tugas,
                    'periode' => $request->periode,
                    'instansi' => $request->instansi
                ]);
                break;

            case 'kerja':
                $riwayat = 'data Pengalaman Kerja';
                RiwayatKerja::find($request->id)->update([
                    'jabatan' => $request->jabatan,
                    'tugas' => $request->tugas,
                    'tahun' => $request->tahun,
                    'instansi' => $request->instansi
                ]);
                break;

            case 'pelatihan':
                $riwayat = 'data Riwayat Pelatihan';
                RiwayatPelatihan::find($request->id)->update([
                    'pelatihan' => $request->pelatihan,
                    'penyelenggara' => $request->penyelenggara,
                    'tahun' => $request->tahun,
                    'sertifikasi' => $request->sertifikasi,
                    'keterangan' => $request->keterangan
                ]);
                break;

            case 'pendidikan':
                $riwayat = 'data Riwayat Pendidikan';
                RiwayatPendidikan::find($request->id)->update([
                    'jenjang' => $request->jenjang,
                    'sekolah' => $request->sekolah,
                    'tahun' => $request->tahun,
                    'kota_negara' => $request->kota_negara,
                    'keterangan' => $request->keterangan
                ]);
                break;
        }

        return back()->with('success', 'Berhasil mengubah ' . $riwayat);
    }

    public function hapusRiwayat(Request $request)
    {
        switch ($request->riwayat) {
            case 'jabatan':
                $riwayat = 'data Riwayat Jabatan';
                RiwayatJabatan::destroy($request->id);
                break;
            case 'kerja':
                $riwayat = 'data Pengalaman Kerja';
                RiwayatKerja::destroy($request->id);
                break;
            case 'pelatihan':
                $riwayat = 'data Riwayat Pelatihan';
                RiwayatPelatihan::destroy($request->id);
                break;
            case 'pendidikan':
                $riwayat = 'data Riwayat Pendidikan';
                RiwayatPendidikan::destroy($request->id);
                break;
        }

        return back()->with('success', 'Berhasil menghapus ' . $riwayat);
    }

    public function lockProfile(Request $request)
    {
        Profile::find($request->id)->update(['is_lock' => $request->is_lock]);

        return back()->with('success', 'Status Berhasil Diubah');
    }

    public function dokumenTenaga($id, $step = 1)
    {
        switch ($step) {
            case 1:
                $step_name = 'Fasilitator';
                $mtdocs = JenisDokumenTenaga::where('step', $step)->get();
                $docs = DokumenTenaga::where('tenaga_id', $id)->get();
                break;
            case 2:
                $step_name = 'Pengelola Pelatihan';
                $mtdocs = JenisDokumenTenaga::where('step', $step)->get();
                $docs = DokumenTenaga::where('tenaga_id', $id)->get();
                break;
            case 3:
                $step_name = 'Pengelola Kelas';
                $mtdocs = JenisDokumenTenaga::where('step', $step)->get();
                $docs = DokumenTenaga::where('tenaga_id', $id)->get();
                break;
            case 4:
                $step_name = 'Pengelola SI';
                $mtdocs = JenisDokumenTenaga::where('step', $step)->get();
                $docs = DokumenTenaga::where('tenaga_id', $id)->get();
                break;
            case 5:
                $step_name = 'Analis Kebutuhan Diklat';
                $mtdocs = JenisDokumenTenaga::where('step', $step)->get();
                $docs = DokumenTenaga::where('tenaga_id', $id)->get();
                break;
        }
        $idprofile = Tenaga::find($id)->id_profile;
        $profile = Profile::find($idprofile);
        // dd($id);
        $viewData = [
            'id' => $id,
            'docs' => $docs,
            'step' => $step,
            'step_name' => $step_name,
            'mtdocs' => $mtdocs,
            'profile' => $profile
        ];

        $userRole = auth()->user()->role;
        if ($userRole === 4) {
            return view('lembaga.profile.tenaga-dokumen', $viewData);
        } elseif ($userRole === 3) {
            return view('asesor.tenaga-dokumen-asesor', $viewData);
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function addModalDocPost(Request $request)
    {
        $dokumen_tenaga = $request->file('file-dokumen');
        $tenaga_id = $request->tenaga_id;
        if (file_exists($dokumen_tenaga)) {
            $data = $request->validate([
                'nama' => 'required|min:3|max:255',
                'tenaga_id' => 'required',
                'tenaga_dokumen_id' => 'required'
            ]);

            $data['path_dokumen'] = Uploadfile::upload($tenaga_id, $dokumen_tenaga, 'dokumen_tenaga');
            $data['tipe'] = $request->file('file-dokumen')->getClientOriginalExtension();

            DokumenTenaga::create($data);
            return back()->with('success', 'Dokumen berhasil diunggah');
        } else {
            return back()->with('error', 'Dokumen gagal diunggah');
        }
    }

    public function editModalDoc(Request $request)
    {
        $id = $request->id_doc;
        $doc = DokumenTenaga::find($id);
        $nama = $doc->nama;
        $path = asset($doc->path_dokumen);
        $result = array('nama' => $nama, 'path' => $path);

        return response()->json($result);
    }

    public function editModalDocPost(Request $request)
    {
        $dokumen_tenaga = $request->file('file-dokumen');
        $tenaga_id = $request->tenaga_id;
        if (file_exists($dokumen_tenaga)) {
            $data = $request->validate([
                'nama' => 'required|min:3|max:255',
            ]);

            $data['path_dokumen'] = Uploadfile::upload($tenaga_id, $dokumen_tenaga, 'dokumen_tenaga');
            $data['tipe'] = $request->file('file-dokumen')->getClientOriginalExtension();

            DokumenTenaga::find($request->id_doc)->update($data);
            return back()->with('success', 'Data berhasil diubah');
        } else {
            $data = $request->validate([
                'nama' => 'required|min:3|max:255',
            ]);

            DokumenTenaga::find($request->id_doc)->update($data);
            return back()->with('success', 'Data berhasil diubah');
        }
    }
    public function deleteModalDoc(Request $request)
    {
        DokumenTenaga::destroy($request->id_doc_del);
        return back()->with('success', 'Berhasil menghapus data');
    }
}
