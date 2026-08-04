<?php

namespace App\Http\Controllers\Lembaga;

use App\Models\DokumenPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\DokumenProgram;
use App\Models\Pelatihan;
use App\Models\Pengajuan;
use App\Models\Tenaga;
use App\Models\TenagaPelatihan;
use App\Helpers\Uploadfile;

class ProgramController extends Controller
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
    public function akreditasi($id, $step = 1)
    {
        $pelatihan = Pelatihan::find($id);
        $jenis_dokumen = DokumenProgram::where('step', $step)->get();
        $docs = DokumenPelatihan::where('id_pelatihan', $id)->get();
        switch ($step) {
            case 1:
                $step_name = "Kurikulum";
                break;
            case 2:
                $step_name = "Perencanaan dan Realisasi";
                break;
            case 3:
                $step_name = "Evaluasi Penyelenggaraan";
                break;
                // case 4:
                //     $step_name = "Hasil";
                //     break;
        }
        // Fasilitator: jenis_tenaga = 1
        // Pengelola Pelatihan: jenis_tenaga = 2
        // Pengelola Kelas: jenis_tenaga = 3
        // Pengelola SI: jenis_tenaga = 4
        // Analis Kediklatan: jenis_tenaga = 5
        $fasilitator = Tenaga::where('jenis_tenaga', 1)->get();
        $pengelolaKelas = Tenaga::where('jenis_tenaga', 3)->get();
        $tenaga = TenagaPelatihan::where('id_pelatihan', $id)->get();

        return view('lembaga.program.akreditasi', [
            'step' => $step,
            'step_name' => $step_name,
            'pelatihan' => $pelatihan,
            'jenis_dokumen' => $jenis_dokumen,
            'docs' => $docs,
            'fasilitator' => $fasilitator,
            'pengelolaKelas' => $pengelolaKelas,
            'tenaga' => $tenaga
        ]);
    }

    public function storeDokumen(Request $request)
    {
        $dokumen_pelatihan = $request->file('file-dokumen');
        if (file_exists($dokumen_pelatihan)) {
            $data = $request->validate([
                'nama' => 'required|min:3|max:255',
                'id_pelatihan' => 'required',
                'dokumen_id' => 'required'
            ]);

            $data['path_dokumen'] = Uploadfile::upload(auth()->user()->id, $dokumen_pelatihan, 'dokumen_pelatihan');
            $data['tipe'] = $request->file('file-dokumen')->getClientOriginalExtension();

            DokumenPelatihan::create($data);
            return back()->with('success', 'Dokumen berhasil diunggah');
        } else {
            return back()->with('error', 'Dokumen gagal diunggah');
        }
    }

    public function editDokumen(Request $request)
    {
        $dokumen_pelatihan = $request->file('file-dokumen');
        if (file_exists($dokumen_pelatihan)) {
            $data = $request->validate([
                'nama' => 'required|min:3|max:255',
            ]);

            $data['path_dokumen'] = Uploadfile::upload(auth()->user()->id, $dokumen_pelatihan, 'dokumen_pelatihan');
            $data['tipe'] = $request->file('file-dokumen')->getClientOriginalExtension();

            DokumenPelatihan::find($request->id)->update($data);
            return back()->with('success', 'Data berhasil diubah');
        } else {
            $data = $request->validate([
                'nama' => 'required|min:3|max:255',
            ]);

            DokumenPelatihan::find($request->id)->update($data);
            return back()->with('success', 'Data berhasil diubah');
        }
    }

    public function hapusDokumen(Request $request)
    {
        DokumenPelatihan::destroy($request->id);
        return back()->with('success', 'Berhasil menghapus data');
    }

    public function storeTenaga(Request $request, $id)
    {
        $data = $request->validate([
            'id_pelatihan' => 'required',
            'id_tenaga' => 'required'
        ]);
        $data['jenis_tenaga'] = $id;
        TenagaPelatihan::create($data);
        return back()->with('success', 'Berhasil menambah data');
    }

    public function hapusTenaga(Request $request)
    {
        TenagaPelatihan::destroy($request->id);
        return back()->with('success', 'Berhasil menghapus data');
    }
}
