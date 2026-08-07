<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class HomeController extends Controller
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
    public function index()
    {
        switch (auth()->user()->role) {
            case 1: //view admin
            case 2: //view sekretariat
                $pengajuans = Pengajuan::orderBy('created_at', 'DESC')->get();
                $asesors = User::where('role', 3)->get();

                return view('sekretariat.home', ['pengajuans' => $pengajuans, 'asesors' => $asesors]);
            case 3: //view asesor
                $pengajuans = Pengajuan::orWhere('id_asesor1', auth()->user()->id)->orWhere('id_asesor2', auth()->user()->id)->orWhere('id_asesor3', auth()->user()->id)->get();
                $isPrakom = 0;
                $isStatistisi = 0;
                foreach ($pengajuans as $p) {
                    switch ($p->id_jenis) {
                        case 1:
                            $isPrakom++;
                            break;
                        case 2:
                            $isStatistisi++;
                            break;
                    }
                }

                return view('asesor.home', ['pengajuans' => $pengajuans, 'isPrakom' => $isPrakom, 'isStatistisi' => $isStatistisi]);
            case 4: //view lembaga
                $pengajuans = Pengajuan::where("id_profile", auth()->user()->id_profile)->latest()->get();
                $isPrakom = Pengajuan::where("id_profile", auth()->user()->id_profile)->where("id_jenis", 1)->where("verifikasi_permohonan", 3)->get();
                $isStatistisi = Pengajuan::where("id_profile", auth()->user()->id_profile)->where("id_jenis", 2)->where("verifikasi_permohonan", 3)->latest()->first();

                return view('lembaga.home', ['pengajuans' => $pengajuans, 'isPrakom' => $isPrakom, 'isStatistisi' => $isStatistisi]);
            default:
                return view('auth.unregistered');
                // dd(auth()->user()->role);
        }
    }

    public function error(Request $request)
    {
        $judul = $request->get('judul', 'Error');
        $pesan = $request->get('pesan', 'Terjadi kesalahan.');

        return view('error', compact('judul', 'pesan'));
    }
}
