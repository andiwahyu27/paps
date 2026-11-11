<?php

namespace App\Console\Commands;

use App\Models\Monev;
use App\Models\MonevKuesioner;
use App\Models\MonevPertanyaan;
use App\Models\MonitoringReport;
use App\Services\MonevAnswerCodec;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillMonev extends Command
{
    protected $signature = 'monev:backfill {--dry-run}';
    protected $description = 'Backfill monitoring reports into the normalized Monev tables';

    public function handle(MonevAnswerCodec $codec): int
    {
        $questionnaire = MonevKuesioner::where('kode', 'monitoring-akreditasi-pstk')->where('versi', 1)->firstOrFail();
        $questions = MonevPertanyaan::where('kuesioner_id', $questionnaire->id)->get()->keyBy('kode');
        $reports = MonitoringReport::with(['versions', 'profile'])->orderBy('id')->get();
        $ready = [];
        $blocked = 0;

        foreach ($reports as $report) {
            if (Monev::where('legacy_report_id', $report->id)->exists()) continue;
            $version = $report->submitted_version
                ? $report->versions->firstWhere('version', $report->submitted_version)
                : $report->versions->sortByDesc('version')->first();
            if (!$version) {
                $this->warn("Report {$report->id} tidak memiliki versi jawaban.");
                $blocked++;
                continue;
            }

            $answers = $version->answers ?: [];
            $encoded = $this->encodeAnswers($answers, $questions, $codec);
            if ($encoded === null) {
                $blocked++;
                continue;
            }
            $ready[] = [$report, $version, $encoded];
        }

        $this->line(sprintf('Dry run: %d siap, %d terblokir, %d sudah dilewati.', count($ready), $blocked, $reports->count() - count($ready) - $blocked));
        if ($this->option('dry-run') || $blocked > 0) {
            if ($blocked > 0) $this->error('Tidak ada data ditulis karena preflight menemukan masalah.');
            return $blocked > 0 ? self::FAILURE : self::SUCCESS;
        }

        DB::transaction(function () use ($ready, $questionnaire) {
            foreach ($ready as [$report, $version, $answers]) {
                $monev = Monev::create([
                    'uuid' => $report->uuid,
                    'kuesioner_id' => $questionnaire->id,
                    'profile_id' => $report->profile_id,
                    'jenis_pengajuan_id' => $report->jenis_pengajuan_id,
                    'report_year' => $report->report_year,
                    'status' => $report->status === 'submitted' ? 'submitted' : 'draft',
                    'edit_version' => $report->edit_version,
                    'submitted_by' => $version->submitted_by,
                    'submitted_at' => $version->submitted_at,
                    'identity_snapshot' => $version->identity_snapshot,
                    'accreditation_snapshot' => $version->accreditation_snapshot,
                    'legacy_report_id' => $report->id,
                ]);
                $monev->answers()->createMany($answers);
            }
        });
        $this->info('Backfill Monev berhasil disimpan.');
        return self::SUCCESS;
    }

    private function encodeAnswers(array $answers, $questions, MonevAnswerCodec $codec): ?array
    {
        $answers['has_training'] = $answers['has_training'] ?? ((int) ($answers[10] ?? 0) > 0 ? 'Ya' : 'Tidak');
        if ($answers['has_training'] === 'Ya' && (int) ($answers[10] ?? 0) < 1) {
            $this->warn('Jawaban pelatihan Ya memiliki jumlah pelatihan kosong atau nol.');
            return null;
        }
        $rows = [];
        foreach ($answers as $key => $value) {
            if ($key === '12' && is_array($value)) {
                foreach (['internal' => '12a', 'external' => '12b'] as $part => $code) {
                    if (!$this->addRow($rows, $questions, $codec, $code, $value[$part] ?? null)) return null;
                }
                continue;
            }
            $code = (string) $key;
            if ($code === '23_keterangan') $code = '23a';
            if (!$questions->has($code)) continue;
            if (!$this->addRow($rows, $questions, $codec, $code, $value)) return null;
        }
        return $rows;
    }

    private function addRow(array &$rows, $questions, MonevAnswerCodec $codec, string $code, $value): bool
    {
        $question = $questions->get($code);
        try {
            $encoded = $codec->encode($question->tipe_pertanyaan, $value);
        } catch (\Throwable $error) {
            $this->warn("Jawaban {$code} tidak valid: {$error->getMessage()}");
            return false;
        }
        if ($encoded['numeric_value'] === null && $encoded['text_value'] === null) return true;
        $rows[] = ['pertanyaan_id' => $question->id] + $encoded;
        return true;
    }
}
