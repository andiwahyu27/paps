<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Item;
use App\Models\Penilaian;
use App\Models\SidangSignature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\TemplateProcessor;

class TtdSidangController extends Controller
{
    private const TIMEZONES = [
        'Asia/Jakarta' => 'Waktu Indonesia Barat',
        'Asia/Makassar' => 'Waktu Indonesia Tengah',
        'Asia/Jayapura' => 'Waktu Indonesia Timur',
    ];

    public function show(string $token)
    {
        $pengajuan = $this->findByToken($token);
        $signatures = SidangSignature::forPengajuan($pengajuan->id);
        $sidangMeta = $signatures->first();
        $nomorSurat = optional($sidangMeta)->nomor_surat;
        $hariTanggalSurat = optional($sidangMeta)->hari_tanggal_surat;
        $waktuSurat = optional($sidangMeta)->waktu_surat
            ? $sidangMeta->waktu_surat->format('H:i')
            : null;
        $tempatSurat = optional($sidangMeta)->tempat_surat;
        $zonaSurat = optional($sidangMeta)->zona_surat;
        $catatanSidang = Penilaian::where('id_pengajuan', $pengajuan->id)
            ->where('pra_paska', 'final')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('catatan')->where('catatan', '<>', '');
                })->orWhere(function ($q) {
                    $q->whereNotNull('rekomendasi')->where('rekomendasi', '<>', '');
                });
            })
            ->orderBy('id_item_penilaian')
            ->get();
        $items = Item::whereIn('id', $catatanSidang->pluck('id_item_penilaian'))
            ->get()
            ->keyBy('id');
        $sidangAssessmentSubmitted = (int) $pengajuan->final === 1;
        $backUrl = auth()->check() ? route('final', $pengajuan->id) : url('/');

        return view('ttd-sidang', [
            'pengajuan' => $pengajuan,
            'signatures' => $signatures,
            'nomorSurat' => $nomorSurat,
            'hariTanggalSurat' => $hariTanggalSurat,
            'waktuSurat' => $waktuSurat,
            'zonaSurat' => $zonaSurat,
            'tempatSurat' => $tempatSurat,
            'submitted' => (bool) $pengajuan->ba_sidang_submitted_at,
            'baSubmitted' => (bool) $pengajuan->ba_sidang_submitted_at,
            'catatanSidang' => $catatanSidang,
            'items' => $items,
            'isSekretariat' => auth()->check() && (int) auth()->user()->role === 2,
            'sidangAssessmentSubmitted' => $sidangAssessmentSubmitted,
            'backUrl' => $backUrl,
        ]);
    }

    public function createPost(Request $request)
    {
        $data = $request->validate([
            'pengajuan_id' => 'required|integer|exists:tb_pengajuans,id',
            'ketua_majelis_name' => 'required|string|max:255',
            'ketua_majelis_title' => 'required|string|max:255',
            'sekretaris_majelis_name' => 'required|string|max:255',
            'sekretaris_majelis_title' => 'required|string|max:255',
            'anggota_majelis_name' => 'required|string|max:255',
            'anggota_majelis_title' => 'required|string|max:255',
            'signature_place' => 'required|string|max:100',
            'nomor_surat' => 'required|string|max:100',
            'letter_date' => 'required|date',
            'signature_time' => 'required|date_format:H:i',
            'timezone' => 'required|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura',
        ]);

        $pengajuan = Pengajuan::findOrFail($data['pengajuan_id']);
        if ((int) $pengajuan->final !== 1) {
            return back()->with('error', 'Penilaian Sidang Majelis harus disubmit oleh asesor terlebih dahulu sebelum membuat TTD Berita Acara Sidang.');
        }
        if ($pengajuan->ba_sidang_submitted_at) {
            return back()->with('error', 'Berita Acara Sidang sudah disubmit. Reset terlebih dahulu.');
        }

        $dateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['letter_date'].' '.$data['signature_time'],
            $data['timezone']
        );
        $token = $pengajuan->ttd_sidang_token ?: Pengajuan::generateUniqueTtdToken();
        $pengajuan->forceFill(['ttd_sidang_token' => $token])->saveQuietly();
        $hariTanggal = $this->formatHariTanggalTerbilang($dateTime);
        $metadata = [
            'tgl_surat' => $dateTime->format('Y-m-d'),
            'nomor_surat' => $data['nomor_surat'],
            'waktu_surat' => $dateTime->format('H:i:s'),
            'tgl_waktu_surat' => $this->formatDateTime($dateTime),
            'tempat_surat' => $data['signature_place'],
            'zona_surat' => self::TIMEZONES[$data['timezone']],
            'hari_tanggal_surat' => $hariTanggal,
        ];

        $actors = [
            'ketua_majelis' => [$data['ketua_majelis_name'], $data['ketua_majelis_title']],
            'sekretaris_majelis' => [$data['sekretaris_majelis_name'], $data['sekretaris_majelis_title']],
            'anggota_majelis' => [$data['anggota_majelis_name'], $data['anggota_majelis_title']],
        ];
        foreach ($actors as $type => [$name, $title]) {
            $record = SidangSignature::firstOrNew([
                'pengajuan_id' => $pengajuan->id,
                'jenis_user' => $type,
            ]);
            $record->fill(array_merge($metadata, [
                'nama_user' => $name,
                'jabatan_user' => $title,
            ]));
            $record->save();
        }

        return redirect()->route('ttd.sidang.show', ['token' => $token]);
    }

    public function saveSignature(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'regex:/\A[a-f0-9]{40,64}\z/'],
            'signer_type' => ['required', 'in:'.implode(',', SidangSignature::SIGNER_TYPES)],
            'signature_data' => 'required|string|max:2800000',
        ]);
        $pengajuan = $this->findByToken($data['token']);
        if ((int) $pengajuan->final !== 1) {
            return response()->json(['status' => 'error', 'message' => 'Penilaian Sidang Majelis belum disubmit oleh asesor.'], 422);
        }
        if ($pengajuan->ba_sidang_submitted_at) {
            return response()->json(['status' => 'error', 'message' => 'Berita Acara Sidang sudah disubmit.'], 409);
        }
        $record = SidangSignature::where('pengajuan_id', $pengajuan->id)
            ->where('jenis_user', $data['signer_type'])->firstOrFail();
        if (!preg_match('/^data:image\/png;base64,/', $data['signature_data'])) {
            return response()->json(['status' => 'error', 'message' => 'Tanda tangan harus berupa PNG.'], 422);
        }
        $binary = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $data['signature_data']), true);
        if ($binary === false || strlen($binary) < 100) {
            return response()->json(['status' => 'error', 'message' => 'Data tanda tangan tidak valid.'], 422);
        }
        $directory = public_path('uploads/ttd-sidang/'.$pengajuan->id);
        File::ensureDirectoryExists($directory);
        $path = 'uploads/ttd-sidang/'.$pengajuan->id.'/'.$data['signer_type'].'.png';
        file_put_contents(public_path($path), $binary);
        $record->update(['ttd' => $path, 'status_ttd' => 'signed']);

        return response()->json(['status' => 'success', 'message' => 'Tanda tangan berhasil disimpan.']);
    }

    public function getSignatures(string $token)
    {
        $pengajuan = $this->findByToken($token);
        $signatures = SidangSignature::forPengajuan($pengajuan->id);
        $data = [];
        foreach (SidangSignature::SIGNER_TYPES as $type) {
            $signature = $signatures->get($type);
            $data[$type] = [
                'signed' => (bool) ($signature && $signature->status_ttd === 'signed' && $signature->ttd),
                'name' => $signature?->nama_user,
                'title' => $signature?->jabatan_user,
                'image_url' => $signature?->ttd ? asset($signature->ttd) : null,
            ];
        }

        return response()->json([
            'signatures' => $data,
            'is_fully_signed' => SidangSignature::isFullySigned($pengajuan->id),
            'ba_submitted' => (bool) $pengajuan->ba_sidang_submitted_at,
        ]);
    }

    public function signatureImage(string $token, string $signerType)
    {
        if (!in_array($signerType, SidangSignature::SIGNER_TYPES, true)) abort(404);
        $pengajuan = $this->findByToken($token);
        $signature = SidangSignature::where('pengajuan_id', $pengajuan->id)
            ->where('jenis_user', $signerType)->firstOrFail();
        abort_unless($signature->ttd && is_file(public_path($signature->ttd)), 404);
        return response()->file(public_path($signature->ttd), ['Cache-Control' => 'no-store']);
    }

    public function submitBeritaAcara(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'regex:/\A[a-f0-9]{40,64}\z/']]);
        $pengajuan = $this->findByToken($data['token']);
        if ((int) $pengajuan->final !== 1) {
            return response()->json(['status' => 'error', 'message' => 'Penilaian Sidang Majelis belum disubmit oleh asesor.'], 422);
        }
        if (!SidangSignature::isFullySigned($pengajuan->id)) {
            return response()->json(['status' => 'error', 'message' => 'Semua tanda tangan Majelis harus lengkap.'], 422);
        }
        $pengajuan->forceFill(['ba_sidang_submitted_at' => now()])->saveQuietly();
        return response()->json(['status' => 'success', 'message' => 'Berita Acara Sidang berhasil disubmit.']);
    }

    public function resetSignature(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'regex:/\A[a-f0-9]{40,64}\z/'],
            'signer_type' => ['required', 'in:'.implode(',', SidangSignature::SIGNER_TYPES)],
        ]);
        $pengajuan = $this->findByToken($data['token']);
        abort_if($pengajuan->ba_sidang_submitted_at, 409, 'Reset status Berita Acara Sidang terlebih dahulu.');
        $signature = SidangSignature::where('pengajuan_id', $pengajuan->id)->where('jenis_user', $data['signer_type'])->firstOrFail();
        $this->deleteSignature($signature);
        return response()->json(['status' => 'success']);
    }

    public function resetAllSignatures(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'regex:/\A[a-f0-9]{40,64}\z/']]);
        $pengajuan = $this->findByToken($data['token']);
        abort_if($pengajuan->ba_sidang_submitted_at, 409, 'Reset status Berita Acara Sidang terlebih dahulu.');
        foreach (SidangSignature::where('pengajuan_id', $pengajuan->id)->get() as $signature) $this->deleteSignature($signature);
        return response()->json(['status' => 'success']);
    }

    public function resetBeritaAcara(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'regex:/\A[a-f0-9]{40,64}\z/']]);
        $pengajuan = $this->findByToken($data['token']);
        $pengajuan->forceFill(['ba_sidang_submitted_at' => null])->saveQuietly();
        return response()->json(['status' => 'success']);
    }

    public function eksporBaSidang(int $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        abort_unless((int) $pengajuan->final === 1, 422, 'Penilaian Sidang Majelis harus disubmit oleh asesor terlebih dahulu.');
        return $this->downloadDocument($pengajuan, false);
    }

    public function eksporBaSidangTtd(int $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        abort_unless($pengajuan->ba_sidang_submitted_at, 422, 'Berita Acara Sidang belum disubmit.');
        return $this->downloadDocument($pengajuan, true);
    }

    private function downloadDocument(Pengajuan $pengajuan, bool $withSignatures)
    {
        $signatures = SidangSignature::forPengajuan($pengajuan->id);
        $first = $signatures->first();
        abort_unless($first, 422, 'Metadata Berita Acara Sidang belum tersedia.');
        if ($withSignatures) abort_unless(SidangSignature::isFullySigned($pengajuan->id), 422, 'Tanda tangan belum lengkap.');
        $processor = new TemplateProcessor(public_path('template_berita_acara_sidang.docx'));
        $profile = $pengajuan->profile;
        $jenis = $pengajuan->id_jenis == 1 ? 'Sistem Teknologi Berbasis Komputer' : 'Statistik';
        $processor->setValue('nama_lemdik', $profile->nama_lembaga ?? '-');
        $processor->setValue('nomor_surat', $first->nomor_surat ?? '-');
        $processor->setValue('hari_tanggal_surat', $first->hari_tanggal_surat ?? '-');
        $processor->setValue('waktu_surat', $first->waktu_surat?->format('H:i'));
        $processor->setValue('zona_surat', $first->zona_surat ?? '-');
        $processor->setValue('jenis_pengajuan', $jenis);
        $processor->setValue('ketua_majelis', $signatures['ketua_majelis']->nama_user ?? '-');
        $processor->setValue('sekretaris_majelis', $signatures['sekretaris_majelis']->nama_user ?? '-');
        $processor->setValue('anggota_majelis', $signatures['anggota_majelis']->nama_user ?? '-');
        $processor->setValue('tempat_surat', $first->tempat_surat ?? '-');
        $processor->setValue('tanggal', $first->tgl_surat?->format('d F Y') ?? '-');
        foreach (SidangSignature::SIGNER_TYPES as $type) {
            $key = 'ttd_'.$type;
            $signature = $signatures[$type] ?? null;
            if ($withSignatures && $signature?->ttd) $processor->setImageValue($key, ['path' => public_path($signature->ttd), 'width' => 120, 'height' => 60]);
            else $processor->setValue($key, '');
        }
        $temp = tempnam(sys_get_temp_dir(), 'ba-sidang-');
        $processor->saveAs($temp);
        return response()->download($temp, 'Berita Acara Sidang '.$profile->nama_lembaga.'.docx')->deleteFileAfterSend(true);
    }

    private function deleteSignature(SidangSignature $signature): void
    {
        if ($signature->ttd && is_file(public_path($signature->ttd))) @unlink(public_path($signature->ttd));
        $signature->update(['ttd' => null, 'status_ttd' => 'pending']);
    }

    private function findByToken(string $token): Pengajuan
    {
        return Pengajuan::where('ttd_sidang_token', $token)->firstOrFail();
    }

    private function formatDateTime(Carbon $dateTime): string
    {
        return $dateTime->locale('id')->translatedFormat('l').' Tanggal '.$dateTime->day.' '.
            $dateTime->locale('id')->translatedFormat('F').' '.$dateTime->year.', Pukul '.$dateTime->format('H.i').' '.self::TIMEZONES[$dateTime->getTimezone()->getName()];
    }

    private function formatHariTanggalTerbilang(Carbon $dateTime): string
    {
        return 'Hari '.$dateTime->locale('id')->translatedFormat('l').' Tanggal '.$this->terbilang($dateTime->day).
            ' Bulan '.$dateTime->locale('id')->translatedFormat('F').' Tahun '.$this->terbilang($dateTime->year);
    }

    private function terbilang(int $number): string
    {
        $words = ['Nol','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if ($number < 12) return $words[$number];
        if ($number < 20) return $this->terbilang($number - 10).' Belas';
        if ($number < 100) return $this->terbilang(intdiv($number, 10)).' Puluh'.($number % 10 ? ' '.$this->terbilang($number % 10) : '');
        if ($number < 200) return 'Seratus'.($number % 100 ? ' '.$this->terbilang($number % 100) : '');
        if ($number < 1000) return $this->terbilang(intdiv($number, 100)).' Ratus'.($number % 100 ? ' '.$this->terbilang($number % 100) : '');
        if ($number < 2000) return 'Seribu'.($number % 1000 ? ' '.$this->terbilang($number % 1000) : '');
        if ($number < 1000000) return $this->terbilang(intdiv($number, 1000)).' Ribu'.($number % 1000 ? ' '.$this->terbilang($number % 1000) : '');
        return (string) $number;
    }
}
