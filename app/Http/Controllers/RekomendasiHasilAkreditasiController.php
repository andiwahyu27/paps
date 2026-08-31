<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\RekomendasiHasilAkreditasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class RekomendasiHasilAkreditasiController extends Controller
{
    public function show(int $id)
    {
        $pengajuan = Pengajuan::with(['profile', 'jenis'])->findOrFail($id);
        $this->authorizePengajuan($pengajuan);

        $recommendations = RekomendasiHasilAkreditasi::where('pengajuan_id', $id)
            ->orderBy('kategori')->orderBy('urutan')->orderBy('id')->get()
            ->groupBy('kategori');

        return view('asesor.rekomendasi-hasil-sidang', [
            'pengajuan' => $pengajuan,
            'tahunPengajuan' => optional($pengajuan->created_at)->format('Y'),
            'jenisPengajuan' => $this->jenisPengajuan($pengajuan),
            'nilaiFinal' => $pengajuan->nilai_final,
            'predikatFinal' => $pengajuan->predikat_final,
            'dipertahankan' => $recommendations->get(RekomendasiHasilAkreditasi::KATEGORI_DIPERTAHANKAN, collect()),
            'diperbaiki' => $recommendations->get(RekomendasiHasilAkreditasi::KATEGORI_DIPERBAIKI, collect()),
            'submitted' => (bool) $pengajuan->rekomendasi_akreditasi_submitted_at,
        ]);
    }

    public function store(Request $request, int $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->authorizePengajuan($pengajuan);
        abort_if($pengajuan->rekomendasi_akreditasi_submitted_at, 409, 'Rekomendasi sudah disubmit dan tidak dapat diubah.');

        $data = $request->validate([
            'dipertahankan' => ['nullable', 'array'],
            'dipertahankan.*' => ['nullable', 'string', 'max:5000'],
            'diperbaiki' => ['nullable', 'array'],
            'diperbaiki.*' => ['nullable', 'string', 'max:5000'],
        ]);

        $groups = [
            RekomendasiHasilAkreditasi::KATEGORI_DIPERTAHANKAN => $data['dipertahankan'] ?? [],
            RekomendasiHasilAkreditasi::KATEGORI_DIPERBAIKI => $data['diperbaiki'] ?? [],
        ];
        $userId = auth()->id();

        DB::transaction(function () use ($groups, $id, $userId) {
            RekomendasiHasilAkreditasi::where('pengajuan_id', $id)->delete();
            foreach ($groups as $kategori => $items) {
                $urutan = 1;
                foreach ($items as $isi) {
                    $isi = trim((string) $isi);
                    if ($isi === '') continue;
                    RekomendasiHasilAkreditasi::create([
                        'pengajuan_id' => $id,
                        'kategori' => $kategori,
                        'isi' => $isi,
                        'urutan' => $urutan++,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        });

        return redirect()->route('rekomendasi.hasil.sidang.show', $id)
            ->with('success', 'Rekomendasi hasil akreditasi berhasil disimpan.');
    }

    public function submit(int $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->authorizePengajuan($pengajuan);
        abort_if($pengajuan->rekomendasi_akreditasi_submitted_at, 409, 'Rekomendasi sudah disubmit.');

        $hasRecommendations = RekomendasiHasilAkreditasi::where('pengajuan_id', $id)->exists();
        abort_unless($hasRecommendations, 422, 'Minimal satu rekomendasi harus diisi sebelum submit.');

        $pengajuan->forceFill([
            'rekomendasi_akreditasi_submitted_at' => now(),
            'rekomendasi_akreditasi_submitted_by' => auth()->id(),
        ])->saveQuietly();

        return redirect()->route('rekomendasi.hasil.sidang.show', $id)
            ->with('success', 'Rekomendasi hasil akreditasi berhasil disubmit.');
    }

    public function reopen(int $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->authorizePengajuan($pengajuan);
        abort_unless($pengajuan->rekomendasi_akreditasi_submitted_at, 409, 'Rekomendasi belum pernah disubmit.');

        $pengajuan->forceFill([
            'rekomendasi_akreditasi_submitted_at' => null,
            'rekomendasi_akreditasi_submitted_by' => null,
        ])->saveQuietly();

        return redirect()->route('rekomendasi.hasil.sidang.show', $id)
            ->with('success', 'Rekomendasi dibuka kembali dan dapat diperbaiki.');
    }

    public function exportDocx(int $id)
    {
        $pengajuan = Pengajuan::with('profile')->findOrFail($id);
        $this->authorizePengajuan($pengajuan);
        return $this->exportDocxForPengajuan($pengajuan);
    }

    public function exportDocxForPengajuan(Pengajuan $pengajuan)
    {
        $pengajuan->loadMissing('profile');
        $templatePath = public_path('template_hasil_visitasi.docx');
        abort_unless(is_file($templatePath), 404, 'Template rekomendasi tidak ditemukan.');

        $recommendations = RekomendasiHasilAkreditasi::where('pengajuan_id', $pengajuan->id)
            ->orderBy('kategori')->orderBy('urutan')->orderBy('id')->get()->groupBy('kategori');
        $processor = new TemplateProcessor($templatePath);
        $processor->setValue('tahun_pengajuan', optional($pengajuan->created_at)->format('Y') ?: '-');
        $processor->setValue('nama_lemdik', optional($pengajuan->profile)->nama_lembaga ?: '-');
        $processor->setValue('jenis_pengajuan', $this->jenisPengajuan($pengajuan));
        $processor->setValue('nilai_final', $pengajuan->nilai_final ?? '-');
        $processor->setValue('predikat_final', $pengajuan->predikat_final ?? '-');

        $tempFile = tempnam(sys_get_temp_dir(), 'rekomendasi-');
        $processor->saveAs($tempFile);
        $this->injectRecommendationLists($tempFile, [
            'Hal-hal yang harus dipertahankan' => $recommendations->get(RekomendasiHasilAkreditasi::KATEGORI_DIPERTAHANKAN, collect())->pluck('isi')->all(),
            'Hal-hal yang harus diperbaiki' => $recommendations->get(RekomendasiHasilAkreditasi::KATEGORI_DIPERBAIKI, collect())->pluck('isi')->all(),
        ]);

        $name = preg_replace('/[^A-Za-z0-9._ -]/', '', (string) optional($pengajuan->profile)->nama_lembaga) ?: 'pengajuan';
        return response()->download($tempFile, 'Rekomendasi Hasil Akreditasi '.$name.'.docx')->deleteFileAfterSend(true);
    }

    private function authorizePengajuan(Pengajuan $pengajuan): void
    {
        $user = auth()->user();
        abort_unless($user && in_array((int) $user->role, [2, 3], true), 403);
        if ((int) $user->role === 3) {
            abort_unless(in_array((int) $user->id, array_filter([
                $pengajuan->id_asesor1, $pengajuan->id_asesor2, $pengajuan->id_asesor3,
            ]), true), 403);
        }
    }

    private function jenisPengajuan(Pengajuan $pengajuan): string
    {
        return optional($pengajuan->jenis)->nama ?: '-';
    }

    private function injectRecommendationLists(string $path, array $sections): void
    {
        $zip = new ZipArchive();
        abort_unless($zip->open($path) === true, 500, 'Dokumen rekomendasi gagal dibuka.');
        $xml = $zip->getFromName('word/document.xml');
        foreach ($sections as $label => $items) {
            $list = $items ?: ['-'];
            $paragraphs = '';
            foreach ($list as $index => $item) {
                $text = ($items ? ($index + 1).'. ' : '').(string) $item;
                $paragraphs .= '<w:p><w:r><w:t xml:space="preserve">'.htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8').'</w:t></w:r></w:p>';
            }
            $pattern = '/(<w:tr\b[^>]*>.*?<w:t[^>]*>'.preg_quote($label, '/').'<\/w:t>.*?<\/w:tr>)/s';
            $xml = preg_replace_callback($pattern, function ($match) use ($paragraphs) {
                return preg_replace('/<\/w:tc>/', $paragraphs.'</w:tc>', $match[1], 1);
            }, $xml, 1);
        }
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }
}
