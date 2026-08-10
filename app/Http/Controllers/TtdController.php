<?php

namespace App\Http\Controllers;

use App\Models\DigitalSignature;
use App\Models\Item;
use App\Models\Penilaian;
use App\Models\Unsur;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TtdController extends Controller
{
    private const SIGNER_TYPES = ['asesor1', 'asesor2', 'asesor3', 'kepala'];

    public function index()
    {
        abort(404);
    }

    public function show($token)
    {
        return $this->renderSignaturePage($this->findByToken($token));
    }

    public function createPost(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'regex:/\A[a-f0-9]{40}\z/'],
            'signature_place' => 'required|string|max:100',
            'letter_date' => 'required|date',
            'signature_time' => 'required|date_format:H:i',
            'timezone' => 'required|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura',
            'asesor1_name' => 'required|string|max:255',
            'asesor2_name' => 'required|string|max:255',
            'asesor3_name' => 'required|string|max:255',
            'leader_name' => 'required|string|max:255',
            'leader_title' => 'required|string|max:255',
        ]);

        $pengajuan = $this->findByToken($validated['token']);
        if ($pengajuan->ba_submitted_at) {
            return response()->json(['status' => 'error', 'message' => 'Berita Acara telah disubmit.'], 409);
        }
        $letterDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['letter_date'] . ' ' . $validated['signature_time'],
            $validated['timezone']
        );
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $formData = $validated;
        $formData['datetime'] = DigitalSignature::generateIndonesianDateTime($letterDateTime);
        $formData['signature_date'] = $validated['signature_place'] . ', ' .
            $letterDateTime->day . ' ' . $months[$letterDateTime->month] . ' ' . $letterDateTime->year;

        $this->saveFormDataToDigitalSignature($pengajuan->id, $formData);

        return $this->renderSignaturePage($pengajuan, $formData);
    }

    public function saveSignature(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'regex:/\A[a-f0-9]{40}\z/'],
            'signer_type' => ['required', 'in:' . implode(',', self::SIGNER_TYPES)],
            'signature_data' => 'required|string|max:2800000',
        ]);

        $pengajuan = $this->findByToken($validated['token']);
        if ($pengajuan->ba_submitted_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita Acara telah disubmit. Reset Berita Acara terlebih dahulu untuk mengubah tanda tangan.',
            ], 409);
        }
        $signer = $this->getSigner($pengajuan, $validated['signer_type']);

        if (!$signer['name'] || !$signer['title']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data penandatangan belum lengkap.',
            ], 422);
        }

        $existing = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('jenis_user', $validated['signer_type'])
            ->first();
        $previousSignaturePath = $existing ? $existing->ttd : null;
        $signaturePath = null;
        try {
            $imageData = $this->decodePng($validated['signature_data']);
            if ($imageData === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File tanda tangan tidak valid.',
                ], 422);
            }

            $directory = public_path('tandatangandigital');
            File::ensureDirectoryExists($directory);
            $filename = 'signature_' . bin2hex(random_bytes(16)) . '.png';
            $signaturePath = 'tandatangandigital/' . $filename;

            if (file_put_contents(public_path($signaturePath), $imageData, LOCK_EX) === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanda tangan gagal disimpan.',
                ], 500);
            }

            $signature = DigitalSignature::updateOrCreate(
                [
                    'pengajuan_id' => $pengajuan->id,
                    'jenis_user' => $validated['signer_type'],
                ],
                [
                    'nama_user' => $signer['name'],
                    'jabatan_user' => $signer['title'],
                    'ttd' => $signaturePath,
                    'tgl_surat' => now()->format('Y-m-d'),
                    'waktu_surat' => now()->format('H:i:s'),
                    'tgl_waktu_surat' => DigitalSignature::generateIndonesianDateTime(),
                    'status_ttd' => 'signed',
                ]
            );
            if ($previousSignaturePath && $previousSignaturePath !== $signaturePath) {
                $this->deleteSignatureFile($previousSignaturePath);
            }
        } catch (\Throwable $e) {
            $this->deleteSignatureFile($signaturePath);
            Log::error('E-TTD signature save failed.', [
                'pengajuan_id' => $pengajuan->id,
                'signer_type' => $validated['signer_type'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Tanda tangan gagal disimpan.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Signature saved successfully',
            'data' => [
                'signer_type' => $signature->jenis_user,
                'name' => $signature->nama_user,
                'title' => $signature->jabatan_user,
                'signature_url' => route('ttd.signature.image', [
                    'token' => $pengajuan->ttd_token,
                    'signerType' => $signature->jenis_user,
                ]),
                'signed_at' => $signature->updated_at->toISOString(),
            ],
        ]);
    }

    public function getSignatures($token)
    {
        $pengajuan = $this->findByToken($token);
        $signatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('status_ttd', 'signed')
            ->get()
            ->keyBy('jenis_user');

        $data = [];
        foreach (self::SIGNER_TYPES as $signerType) {
            $signature = $signatures->get($signerType);
            $data[$signerType] = $signature ? [
                'signed' => true,
                'name' => $signature->nama_user,
                'title' => $signature->jabatan_user,
                'signed_at' => optional($signature->updated_at)->toISOString(),
                'signature_url' => route('ttd.signature.image', [
                    'token' => $pengajuan->ttd_token,
                    'signerType' => $signerType,
                ]),
            ] : ['signed' => false];
        }

        return response()->json([
            'signatures' => $data,
            'is_fully_signed' => collect($data)->every(function ($signature) {
                return $signature['signed'];
            }),
            'ba_submitted' => (bool) $pengajuan->ba_submitted_at,
        ]);
    }

    public function signatureImage($token, $signerType)
    {
        if (!in_array($signerType, self::SIGNER_TYPES, true)) {
            abort(404);
        }

        $pengajuan = $this->findByToken($token);
        $signature = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('jenis_user', $signerType)
            ->where('status_ttd', 'signed')
            ->firstOrFail();
        $path = public_path($signature->ttd);

        if (!$signature->ttd || !is_file($path) || realpath($path) === false ||
            strpos(realpath($path), realpath(public_path('tandatangandigital')) . DIRECTORY_SEPARATOR) !== 0) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function resetTtd(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'regex:/\A[a-f0-9]{40}\z/'],
            'signer_type' => ['required', 'in:' . implode(',', self::SIGNER_TYPES)],
        ]);
        $pengajuan = $this->findByToken($validated['token']);
        if ($pengajuan->ba_submitted_at) {
            return response()->json(['status' => 'error', 'message' => 'Reset Berita Acara terlebih dahulu.'], 409);
        }
        $signature = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('jenis_user', $validated['signer_type'])
            ->first();

        if ($signature) {
            $this->deleteSignatureFile($signature->ttd);
            $signature->delete();
        }

        Log::info('E-TTD signature reset.', [
            'pengajuan_id' => $pengajuan->id,
            'signer_type' => $validated['signer_type'],
            'user_id' => auth()->id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Tanda tangan berhasil direset.']);
    }

    public function resetAllSignatures(Request $request)
    {
        $validated = $request->validate(['token' => ['required', 'regex:/\\A[a-f0-9]{40}\\z/']]);
        $pengajuan = $this->findByToken($validated['token']);
        if ($pengajuan->ba_submitted_at) {
            return response()->json(['status' => 'error', 'message' => 'Reset Berita Acara terlebih dahulu.'], 409);
        }

        $signatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)->get();
        foreach ($signatures as $signature) {
            $this->deleteSignatureFile($signature->ttd);
        }
        DigitalSignature::where('pengajuan_id', $pengajuan->id)->delete();

        Log::info('E-TTD all signatures reset.', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => auth()->id(),
            'count' => $signatures->count(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Semua tanda tangan berhasil direset.']);
    }

    public function submitBeritaAcara(Request $request)
    {
        $validated = $request->validate(['token' => ['required', 'regex:/\A[a-f0-9]{40}\z/']]);
        $pengajuan = $this->findByToken($validated['token']);
        if ($pengajuan->ba_submitted_at) {
            return response()->json(['status' => 'success', 'message' => 'Berita Acara sudah disubmit.']);
        }
        if (!DigitalSignature::isPengajuanFullySigned($pengajuan->id)) {
            return response()->json(['status' => 'error', 'message' => 'Semua tanda tangan harus lengkap sebelum submit.'], 422);
        }
        $pengajuan->forceFill(['ba_submitted_at' => now()])->saveQuietly();
        return response()->json(['status' => 'success', 'message' => 'Berita Acara berhasil disubmit.']);
    }

    public function resetBeritaAcara(Request $request)
    {
        $validated = $request->validate(['token' => ['required', 'regex:/\A[a-f0-9]{40}\z/']]);
        $pengajuan = $this->findByToken($validated['token']);
        $pengajuan->forceFill(['ba_submitted_at' => null])->saveQuietly();
        Log::info('E-TTD Berita Acara reset.', ['pengajuan_id' => $pengajuan->id, 'user_id' => auth()->id()]);
        return response()->json(['status' => 'success', 'message' => 'Berita Acara berhasil direset.']);
    }

    public function rotateToken($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->ttd_token = Pengajuan::generateUniqueTtdToken();
        $pengajuan->saveQuietly();

        Log::info('E-TTD token rotated.', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Link E-TTD berhasil dirotasi.');
    }

    public function downloadDocument(Request $request)
    {
        $request->validate(['token' => ['required', 'regex:/\A[a-f0-9]{40}\z/']]);
        $this->findByToken($request->token);

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen siap didownload',
        ]);
    }

    private function findByToken($token)
    {
        if (!is_string($token) || !preg_match('/\A[a-f0-9]{40}\z/', $token)) {
            abort(404);
        }

        return Pengajuan::where('ttd_token', $token)->firstOrFail();
    }

    private function getSigner(Pengajuan $pengajuan, $signerType)
    {
        if ($signerType === 'kepala') {
            return [
                'name' => optional($pengajuan->profile)->nama_pimpinan,
                'title' => optional($pengajuan->profile)->jabatan_pimpinan,
            ];
        }

        $asesor = $pengajuan->{$signerType};

        return [
            'name' => optional($asesor)->name,
            'title' => $signerType === 'asesor1' ? 'Ketua Tim Asesor' : 'Anggota Tim Asesor',
        ];
    }

    private function decodePng($signatureData)
    {
        if (!preg_match('/\Adata:image\/png;base64,([A-Za-z0-9+\/=]+)\z/', $signatureData, $matches)) {
            return false;
        }

        $decoded = base64_decode($matches[1], true);
        if ($decoded === false || strlen($decoded) > 2 * 1024 * 1024) {
            return false;
        }

        $imageInfo = @getimagesizefromstring($decoded);
        if (!$imageInfo || ($imageInfo['mime'] ?? null) !== 'image/png') {
            return false;
        }

        return $decoded;
    }

    private function deleteSignatureFile($relativePath)
    {
        if (!$relativePath) {
            return;
        }

        $root = realpath(public_path('tandatangandigital'));
        $path = realpath(public_path($relativePath));
        if ($root && $path && strpos($path, $root . DIRECTORY_SEPARATOR) === 0 && is_file($path)) {
            @unlink($path);
        }
    }

    private function renderSignaturePage(Pengajuan $pengajuan, array $formData = [])
    {
        $signatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('status_ttd', 'signed')
            ->get()
            ->keyBy('jenis_user');
        $pending = DigitalSignature::where('pengajuan_id', $pengajuan->id)
            ->where('status_ttd', 'signed')
            ->latest('created_at')
            ->first();
        $asesorData = [
            'asesor1' => ['name' => optional($pengajuan->asesor1)->name, 'title' => 'Ketua Tim Asesor'],
            'asesor2' => ['name' => optional($pengajuan->asesor2)->name, 'title' => 'Anggota Tim Asesor'],
            'asesor3' => ['name' => optional($pengajuan->asesor3)->name, 'title' => 'Anggota Tim Asesor'],
        ];
        $leaderData = [
            'name' => optional($pengajuan->profile)->nama_pimpinan,
            'title' => optional($pengajuan->profile)->jabatan_pimpinan,
        ];
        $signatureDate = $formData['signature_date'] ?? null;
        $customDateTime = $pending->tgl_waktu_surat ?? DigitalSignature::generateIndonesianDateTime();
        $baSubmitted = (bool) $pengajuan->ba_submitted_at;
        $isSekretariat = auth()->check() && (int) auth()->user()->role === 2;
        $catatanVisitasi = Penilaian::where('id_pengajuan', $pengajuan->id)
            ->where('pra_paska', 'pra2')
            ->where(function ($query) {
                $query->whereNotNull('catatan')->orWhereNotNull('rekomendasi');
            })
            ->orderBy('id_item_penilaian')->get();
        $items = Item::whereIn('id', $catatanVisitasi->pluck('id_item_penilaian'))->get()->keyBy('id');
        $unsurs = Unsur::whereIn('id', $items->pluck('id_unsur'))->get()->keyBy('id');
        $namaLembaga = optional($pengajuan->profile)->nama_lembaga ?? 'Belum ditentukan';
        $namaPimpinan = optional($pengajuan->profile)->nama_pimpinan ?? 'Belum ditentukan';

        return view('ttd', compact(
            'pengajuan', 'signatures', 'formData', 'asesorData', 'leaderData',
            'signatureDate', 'customDateTime', 'namaLembaga', 'namaPimpinan',
            'baSubmitted', 'catatanVisitasi', 'items', 'unsurs',
            'isSekretariat'
        ));
    }

    private function saveFormDataToDigitalSignature($pengajuanId, $formData)
    {
        $userData = [
            ['jenis_user' => 'asesor1', 'nama_user' => $formData['asesor1_name'], 'jabatan_user' => 'Ketua Tim Asesor'],
            ['jenis_user' => 'asesor2', 'nama_user' => $formData['asesor2_name'], 'jabatan_user' => 'Anggota Tim Asesor'],
            ['jenis_user' => 'asesor3', 'nama_user' => $formData['asesor3_name'], 'jabatan_user' => 'Anggota Tim Asesor'],
            ['jenis_user' => 'kepala', 'nama_user' => $formData['leader_name'], 'jabatan_user' => $formData['leader_title']],
        ];

        foreach ($userData as $user) {
            $signature = DigitalSignature::where('pengajuan_id', $pengajuanId)
                ->where('jenis_user', $user['jenis_user'])
                ->first();
            $status = $signature && $signature->status_ttd === 'signed' ? 'signed' : 'pending';

            DigitalSignature::updateOrCreate(
                ['pengajuan_id' => $pengajuanId, 'jenis_user' => $user['jenis_user']],
                [
                    'nama_user' => $user['nama_user'],
                    'jabatan_user' => $user['jabatan_user'],
                    'tgl_surat' => $formData['letter_date'],
                    'waktu_surat' => $formData['signature_time'] . ':00',
                    'tgl_waktu_surat' => $formData['datetime'],
                    'status_ttd' => $status,
                ]
            );
        }
    }
}
