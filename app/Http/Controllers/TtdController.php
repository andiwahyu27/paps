<?php

namespace App\Http\Controllers;

use App\Models\DigitalSignature;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TtdController extends Controller
{
    /**
     * Show the digital signature page for public access.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Halaman tanda tangan digital dapat diakses tanpa login
        return view('ttd');
    }

    public function create($pengajuanId, Request $request)
    {
        $pengajuan = Pengajuan::findOrFail($pengajuanId);

        // Get form data from request if available
        $formData = [
            'datetime' => $request->get('datetime'),
            'signature_date' => $request->get('signature_date'),
            'asesor1_name' => $request->get('asesor1_name'),
            'asesor2_name' => $request->get('asesor2_name'),
            'asesor3_name' => $request->get('asesor3_name'),
            'leader_name' => $request->get('leader_name'),
            'leader_title' => $request->get('leader_title')
        ];

        // Save form data to DigitalSignature if present
        if ($formData['datetime'] && $formData['asesor1_name']) {
            $this->saveFormDataToDigitalSignature($pengajuan->id, $formData);
        }

        // Get existing signatures
        $signatures = DigitalSignature::getPengajuanSignatures($pengajuan->id);

        // Prepare data for view
        $asesorData = [
            'asesor1' => ['name' => $formData['asesor1_name'] ?? $pengajuan->asesor1->name, 'title' => 'Ketua Tim Asesor'],
            'asesor2' => ['name' => $formData['asesor2_name'] ?? $pengajuan->asesor2->name, 'title' => 'Anggota Tim Asesor'],
            'asesor3' => ['name' => $formData['asesor3_name'] ?? $pengajuan->asesor3->name, 'title' => 'Anggota Tim Asesor']
        ];

        $leaderData = [
            'name' => $formData['leader_name'] ?? $pengajuan->profile->nama_pimpinan,
            'title' => $formData['leader_title'] ?? $pengajuan->profile->jabatan_pimpinan
        ];

        // Generate Indonesian date format for signature date
        $defaultDate = 'Jakarta, ' . now()->day . ' ' .
            ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][now()->month] .
            ' ' . now()->year;
        $signatureDate = $formData['signature_date'] ?? $defaultDate;

        // Get customDateTime from the first available signature's tgl_waktu_surat
        // Check both signed and pending signatures
        $allSignatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)
                                        ->whereIn('status_ttd', ['signed', 'pending'])
                                        ->orderBy('created_at', 'asc')
                                        ->get();

        $customDateTime = 'Belum ditentukan';
        if ($allSignatures->isNotEmpty()) {
            $firstSignature = $allSignatures->first();
            $customDateTime = $firstSignature->tgl_waktu_surat ?? 'Belum ditentukan';
        } else {
            // If no signatures exist yet, generate current Indonesian datetime as fallback
            $customDateTime = DigitalSignature::generateIndonesianDateTime();
        }

        // Get namaLembaga from pengajuan profile
        $namaLembaga = $pengajuan->profile->nama_lembaga ?? 'Belum ditentukan';

        // Get namaPimpinan from pengajuan profile
        $namaPimpinan = $pengajuan->profile->nama_pimpinan ?? 'Belum ditentukan';

        return view('ttd', compact('pengajuan', 'signatures', 'formData', 'asesorData', 'leaderData', 'signatureDate', 'customDateTime', 'namaLembaga', 'namaPimpinan'));
    }

    public function createPost(Request $request, $pengajuanId = null)
    {
        // Get pengajuan_id from route parameter or request body
        $pengajuanId = $pengajuanId ?? $request->get('pengajuan_id');
        if (!$pengajuanId) {
            return response()->json(['error' => 'Pengajuan ID required'], 400);
        }

        $request->validate([
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

        $pengajuan = Pengajuan::findOrFail($pengajuanId);

        // Get form data from request if available
        $formData = [
            'signature_place' => $request->get('signature_place'),
            'letter_date' => $request->get('letter_date'),
            'signature_time' => $request->get('signature_time'),
            'timezone' => $request->get('timezone'),
            'asesor1_name' => $request->get('asesor1_name'),
            'asesor2_name' => $request->get('asesor2_name'),
            'asesor3_name' => $request->get('asesor3_name'),
            'leader_name' => $request->get('leader_name'),
            'leader_title' => $request->get('leader_title')
        ];

        $letterDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $formData['letter_date'] . ' ' . $formData['signature_time'],
            $formData['timezone']
        );
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $formData['datetime'] = DigitalSignature::generateIndonesianDateTime($letterDateTime);
        $formData['signature_date'] = $formData['signature_place'] . ', ' .
            $letterDateTime->day . ' ' . $months[$letterDateTime->month] . ' ' . $letterDateTime->year;
        // return $formData;
        // Save form data to DigitalSignature if present
        if ($formData['datetime'] && $formData['asesor1_name']) {
            $this->saveFormDataToDigitalSignature($pengajuan->id, $formData);
        }

        // Get existing signatures
        $signatures = DigitalSignature::getPengajuanSignatures($pengajuan->id);

        // Prepare data for view
        $asesorData = [
            'asesor1' => ['name' => $formData['asesor1_name'] ?? $pengajuan->asesor1->name, 'title' => 'Ketua Tim Asesor'],
            'asesor2' => ['name' => $formData['asesor2_name'] ?? $pengajuan->asesor2->name, 'title' => 'Anggota Tim Asesor'],
            'asesor3' => ['name' => $formData['asesor3_name'] ?? $pengajuan->asesor3->name, 'title' => 'Anggota Tim Asesor']
        ];

        $leaderData = [
            'name' => $formData['leader_name'] ?? $pengajuan->profile->nama_pimpinan,
            'title' => $formData['leader_title'] ?? $pengajuan->profile->jabatan_pimpinan
        ];

        // Generate Indonesian date format for signature date
        $defaultDate = 'Jakarta, ' . now()->day . ' ' .
            ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][now()->month] .
            ' ' . now()->year;
        $signatureDate = $formData['signature_date'] ?? $defaultDate;

        // Get customDateTime from the first available signature's tgl_waktu_surat
        // Check both signed and pending signatures
        $allSignatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)
                                        ->whereIn('status_ttd', ['signed', 'pending'])
                                        ->orderBy('created_at', 'asc')
                                        ->get();

        $customDateTime = 'Belum ditentukan';
        if ($allSignatures->isNotEmpty()) {
            $firstSignature = $allSignatures->first();
            $customDateTime = $firstSignature->tgl_waktu_surat ?? 'Belum ditentukan';
        } else {
            // If no signatures exist yet, generate current Indonesian datetime as fallback
            $customDateTime = DigitalSignature::generateIndonesianDateTime();
        }

        // Get namaLembaga from pengajuan profile
        $namaLembaga = $pengajuan->profile->nama_lembaga ?? 'Belum ditentukan';

        // Get namaPimpinan from pengajuan profile
        $namaPimpinan = $pengajuan->profile->nama_pimpinan ?? 'Belum ditentukan';

        return view('ttd', compact('pengajuan', 'signatures', 'formData', 'asesorData', 'leaderData', 'signatureDate', 'customDateTime', 'namaLembaga', 'namaPimpinan'));
    }


    /**
     * Show the digital signature page with pengajuan data.
     *
     * @param int $pengajuanId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createTtd($pengajuanId, Request $request)
    {
        $pengajuan = Pengajuan::findOrFail($pengajuanId);

        // Get form data from request if available
        $formData = [
            'datetime' => $request->get('datetime'),
            'signature_date' => $request->get('signature_date'),
            'asesor1_name' => $request->get('asesor1_name'),
            'asesor2_name' => $request->get('asesor2_name'),
            'asesor3_name' => $request->get('asesor3_name'),
            'leader_name' => $request->get('leader_name'),
            'leader_title' => $request->get('leader_title')
        ];

        // Save form data to DigitalSignature if present
        if ($formData['datetime'] && $formData['asesor1_name']) {
            $this->saveFormDataToDigitalSignature($pengajuan->id, $formData);
        }

        // Get existing signatures
        $signatures = DigitalSignature::getPengajuanSignatures($pengajuan->id);

        // Prepare data for view
        $asesorData = [
            'asesor1' => ['name' => $formData['asesor1_name'] ?? $pengajuan->asesor1->name, 'title' => 'Ketua Tim Asesor'],
            'asesor2' => ['name' => $formData['asesor2_name'] ?? $pengajuan->asesor2->name, 'title' => 'Anggota Tim Asesor'],
            'asesor3' => ['name' => $formData['asesor3_name'] ?? $pengajuan->asesor3->name, 'title' => 'Anggota Tim Asesor']
        ];

        $leaderData = [
            'name' => $formData['leader_name'] ?? $pengajuan->profile->nama_pimpinan,
            'title' => $formData['leader_title'] ?? $pengajuan->profile->jabatan_pimpinan
        ];

        // Generate Indonesian date format for signature date
        $defaultDate = 'Jakarta, ' . now()->day . ' ' .
            ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][now()->month] .
            ' ' . now()->year;
        $signatureDate = $formData['signature_date'] ?? $defaultDate;

        // Get customDateTime from the first available signature's tgl_waktu_surat
        // Check both signed and pending signatures
        $allSignatures = DigitalSignature::where('pengajuan_id', $pengajuan->id)
                                        ->whereIn('status_ttd', ['signed', 'pending'])
                                        ->orderBy('created_at', 'asc')
                                        ->get();

        $customDateTime = 'Belum ditentukan';
        if ($allSignatures->isNotEmpty()) {
            $firstSignature = $allSignatures->first();
            $customDateTime = $firstSignature->tgl_waktu_surat ?? 'Belum ditentukan';
        } else {
            // If no signatures exist yet, generate current Indonesian datetime as fallback
            $customDateTime = DigitalSignature::generateIndonesianDateTime();
        }

        // Get namaLembaga from pengajuan profile
        $namaLembaga = $pengajuan->profile->nama_lembaga ?? 'Belum ditentukan';

        // Get namaPimpinan from pengajuan profile
        $namaPimpinan = $pengajuan->profile->nama_pimpinan ?? 'Belum ditentukan';

        return view('ttd', compact('pengajuan', 'signatures', 'formData', 'asesorData', 'leaderData', 'signatureDate', 'customDateTime', 'namaLembaga', 'namaPimpinan'));
    }

    /**
      * Save signature data.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\JsonResponse
      */
     public function saveSignature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengajuan_id' => 'required|integer',
            'signer_type' => 'required|string|max:50',
            'signer_name' => 'required|string|max:255',
            'signer_title' => 'required|string|max:255',
            'signature_data' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get pengajuan from ID
            $pengajuan = Pengajuan::find($request->pengajuan_id);
            if (!$pengajuan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengajuan not found'
                ], 404);
            }

            // Check if signature already exists to delete old file
            $existingSignature = DigitalSignature::where([
                'pengajuan_id' => $pengajuan->id,
                'jenis_user' => $request->signer_type
            ])->first();

            // Delete old signature file if exists
            if ($existingSignature && $existingSignature->ttd) {
                $oldFilePath = public_path($existingSignature->ttd);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Process signature image data
            $signatureData = $request->signature_data;
            $signaturePath = null;

            if ($signatureData) {
                // Remove data:image/png;base64, prefix if present
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
                $imageData = base64_decode($imageData);

                // Generate unique filename
                $filename = 'signature_' . $pengajuan->id . '_' . $request->signer_type . '_' . time() . '.png';
                $signaturePath = 'tandatangandigital/' . $filename;
                $fullPath = public_path($signaturePath);

                // Save image file
                file_put_contents($fullPath, $imageData);
            }

            // Use updateOrCreate to allow signature replacement
            $signature = DigitalSignature::updateOrCreate(
                [
                    'pengajuan_id' => $pengajuan->id,
                    'jenis_user' => $request->signer_type
                ],
                [
                    'nama_user' => $request->signer_name,
                    'jabatan_user' => $request->signer_title,
                    'ttd' => $signaturePath, // Store file path instead of base64 data
                    'tgl_surat' => now()->format('Y-m-d'),
                    'waktu_surat' => now()->format('H:i:s'),
                    'tgl_waktu_surat' => DigitalSignature::generateIndonesianDateTime(),
                    'status_ttd' => 'signed'
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Signature saved successfully',
                'data' => [
                    'signature_url' => asset($signature->ttd),
                    'signed_at' => $signature->updated_at->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save signature: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get signatures for a specific document.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSignatures(Request $request)
    {
        try {
            $pengajuanId = $request->get('pengajuan_id');

            if (!$pengajuanId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengajuan ID is required',
                ], 400);
            }

            // Verify pengajuan exists
            $pengajuan = Pengajuan::find($pengajuanId);
            if (!$pengajuan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengajuan not found'
                ], 404);
            }

            $signatures = DigitalSignature::getPengajuanSignatures($pengajuan->id);
            $isFullySigned = DigitalSignature::isPengajuanFullySigned($pengajuan->id);

            // Map signatures to expected format
            $mappedSignatures = $signatures->map(function ($signature) {
                // Convert file path to full URL for display
                $ttdUrl = $signature->ttd ? asset($signature->ttd) : null;

                return [
                    'jenis_user' => $signature->jenis_user,
                    'nama_user' => $signature->nama_user,
                    'jabatan_user' => $signature->jabatan_user,
                    'ttd' => $ttdUrl,
                    'tgl_waktu_surat' => $signature->tgl_waktu_surat,
                    'status_ttd' => $signature->status_ttd
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'signatures' => $mappedSignatures,
                    'signature_count' => $signatures->count(),
                    'is_fully_signed' => $isFullySigned
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve signatures: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save form data from modal to DigitalSignature records.
     *
     * @param \App\Models\Pengajuan $pengajuan
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function saveFormDataToDigitalSignature($pengajuanId, $formData)
    {
        $userData = [
             'asesor1' => [
                 'jenis_user' => 'asesor1',
                 'nama_user' => $formData['asesor1_name'],
                 'jabatan_user' => 'Ketua Tim Asesor'
             ],
             'asesor2' => [
                 'jenis_user' => 'asesor2',
                 'nama_user' => $formData['asesor2_name'],
                 'jabatan_user' => 'Anggota Tim Asesor'
             ],
             'asesor3' => [
                 'jenis_user' => 'asesor3',
                 'nama_user' => $formData['asesor3_name'],
                 'jabatan_user' => 'Anggota Tim Asesor'
             ],
             'kepala' => [
                 'jenis_user' => 'kepala',
                 'nama_user' => $formData['leader_name'],
                 'jabatan_user' => $formData['leader_title']
             ]
         ];

         $tglSurat = $formData['letter_date'] ?? Carbon::now()->format('Y-m-d');
         $waktuSurat = ($formData['signature_time'] ?? Carbon::now()->format('H:i')) . ':00';
         $tglWaktuSurat = $formData['datetime'] ?? DigitalSignature::generateIndonesianDateTime();

        foreach ($userData as $user) {
            if (!empty($user['nama_user'])) {
                DigitalSignature::updateOrCreate(
                    [
                        'pengajuan_id' => $pengajuanId,
                        'jenis_user' => $user['jenis_user']
                    ],
                    [
                        'nama_user' => $user['nama_user'],
                        'jabatan_user' => $user['jabatan_user'],
                        'tgl_surat' => $tglSurat,
                        'waktu_surat' => $waktuSurat,
                        'tgl_waktu_surat' => $tglWaktuSurat,
                        'status_ttd' => 'pending'
                    ]
                );
            }
        }
    }

    /**
     * Download signed document (optional - for future enhancement).
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument(Request $request)
    {
        // Implementasi untuk download dokumen yang sudah ditandatangani
        // Saat ini hanya mengembalikan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen siap didownload',
        ]);
    }
}
