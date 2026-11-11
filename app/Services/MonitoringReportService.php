<?php

namespace App\Services;

use App\Models\JenisPengajuan;
use App\Models\MonitoringAccreditation;
use App\Models\MonitoringReport;
use App\Models\MonitoringReportVersion;
use App\Models\Monev;
use App\Models\MonevKuesioner;
use App\Models\MonevPertanyaan;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MonitoringReportService
{
    public function programsFor(Profile $profile, int $year)
    {
        return MonitoringAccreditation::with('jenisPengajuan')
            ->where('profile_id', $profile->id)
            ->whereDate('valid_from', '<=', $year . '-12-31')
            ->whereDate('valid_until', '>=', $year . '-01-01')
            ->get();
    }

    public function saveDraft(MonitoringReport $report, array $answers, ?int $userId, string $channel = 'account', ?int $expectedVersion = null): MonitoringReport
    {
        return DB::transaction(function () use ($report, $answers, $userId, $channel, $expectedVersion) {
            $locked = MonitoringReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            abort_if($expectedVersion !== null && $locked->edit_version !== $expectedVersion, 409, 'Laporan berubah pada sesi lain. Salin perubahan Anda lalu muat ulang.');
            $version = max(1, (int) $locked->edit_version);
            $locked->fill(['status' => $locked->submitted_version ? 'revision_draft' : 'draft', 'edit_version' => $version + 1, 'updated_by' => $userId])->save();
            $locked->versions()->updateOrCreate(['version' => $version], ['answers' => $answers, 'submission_channel' => $channel]);
            $this->syncNormalized($locked, $answers, null);
            return $locked->fresh();
        });
    }

    public function submit(MonitoringReport $report, array $answers, Profile $profile, ?int $userId, string $channel = 'account', ?int $expectedVersion = null): MonitoringReport
    {
        return DB::transaction(function () use ($report, $answers, $profile, $userId, $channel, $expectedVersion) {
            $report = MonitoringReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            abort_if($expectedVersion !== null && $report->edit_version !== $expectedVersion, 409, 'Laporan berubah pada sesi lain. Salin perubahan Anda lalu muat ulang.');
            $version = $report->edit_version;
            $accreditation = MonitoringAccreditation::with('jenisPengajuan')
                ->where('profile_id', $profile->id)->where('jenis_pengajuan_id', $report->jenis_pengajuan_id)
                ->whereDate('valid_from', '<=', $report->report_year . '-12-31')->whereDate('valid_until', '>=', $report->report_year . '-01-01')->firstOrFail();
            $report->fill(['status' => 'submitted', 'edit_version' => $version + 1, 'submitted_version' => $version, 'updated_by' => $userId])->save();
            $report->versions()->create([
                'version' => $version, 'answers' => $answers, 'submitted_by' => $userId, 'submitted_at' => now(), 'submission_channel' => $channel,
                'identity_snapshot' => $profile->only(['id', 'nama_lembaga', 'alamat_lembaga', 'telepon', 'email', 'website']),
                'accreditation_snapshot' => $accreditation->toArray(),
            ]);
            $this->syncNormalized($report, $answers, $userId, $profile);
            return $report->fresh();
        });
    }

    private function syncNormalized(MonitoringReport $report, array $answers, ?int $submittedBy, ?Profile $profile = null): void
    {
        if (!Schema::hasTable('tr_monev')) return;
        $questionnaire = MonevKuesioner::where('kode', 'monitoring-akreditasi-pstk')->where('versi', 1)->first();
        if (!$questionnaire) return;
        $questions = MonevPertanyaan::where('kuesioner_id', $questionnaire->id)->get()->keyBy('kode');
        $monev = Monev::firstOrCreate(['legacy_report_id' => $report->id], [
            'uuid' => $report->uuid,
            'kuesioner_id' => $questionnaire->id,
            'profile_id' => $report->profile_id,
            'jenis_pengajuan_id' => $report->jenis_pengajuan_id,
            'report_year' => $report->report_year,
            'status' => 'draft',
            'edit_version' => $report->edit_version,
        ]);
        $monev->fill(['status' => $report->status === 'submitted' ? 'submitted' : 'draft', 'edit_version' => $report->edit_version]);
        if ($submittedBy !== null) {
            $monev->submitted_by = $submittedBy;
            $monev->submitted_at = now();
            $monev->identity_snapshot = $profile?->only(['id', 'nama_lembaga', 'alamat_lembaga', 'telepon', 'email', 'website']);
        }
        $monev->save();
        $monev->answers()->delete();
        foreach ($this->normalizedAnswers($answers, $questions) as [$question, $numeric, $text]) {
            $monev->answers()->create(['pertanyaan_id' => $question->id, 'numeric_value' => $numeric, 'text_value' => $text]);
        }
    }

    private function normalizedAnswers(array $answers, $questions): array
    {
        $rows = [];
        $answers['has_training'] = $answers['has_training'] ?? ((int) ($answers[10] ?? 0) > 0 ? 'Ya' : 'Tidak');
        foreach ($answers as $key => $value) {
            if ((string) $key === '12' && is_array($value)) {
                foreach (['internal' => '12a', 'external' => '12b'] as $part => $code) $this->appendNormalized($rows, $questions, $code, $value[$part] ?? null);
                continue;
            }
            $code = $key === '23_keterangan' ? '23a' : (string) $key;
            $this->appendNormalized($rows, $questions, $code, $value);
        }
        return $rows;
    }

    private function appendNormalized(array &$rows, $questions, string $code, $value): void
    {
        $question = $questions->get($code);
        if (!$question || $value === null || $value === '') return;
        $numeric = in_array($question->tipe_pertanyaan, ['integer', 'double', 'numeric'], true) ? $value : null;
        $text = $numeric === null ? (is_array($value) ? json_encode(array_values($value), JSON_UNESCAPED_UNICODE) : (string) $value) : null;
        $rows[] = [$question, $numeric, $text];
    }

    public function profileForUser(int $userId): ?Profile
    {
        return Profile::whereHas('pic', fn ($q) => $q->whereKey($userId))->first();
    }

    public function answersForReport(MonitoringReport $report): ?array
    {
        if (!Schema::hasTable('tr_monev')) return null;
        $monev = Monev::with('answers.question')->where('legacy_report_id', $report->id)->first();
        if (!$monev || $monev->answers->isEmpty()) return null;
        $answers = [];
        foreach ($monev->answers as $answer) {
            $code = $answer->question->kode;
            if ($code === '12a' || $code === '12b') {
                $answers['12'][substr($code, 2) === 'a' ? 'internal' : 'external'] = $answer->numeric_value;
            } elseif ($code === '23a') {
                $answers['23_keterangan'] = $answer->text_value;
            } elseif ($answer->numeric_value !== null) {
                $answers[$code] = $answer->numeric_value;
            } elseif ($answer->text_value !== null) {
                $decoded = json_decode($answer->text_value, true);
                $answers[$code] = is_array($decoded) ? $decoded : $answer->text_value;
            }
        }
        return $answers;
    }

    public function resetAnswers(MonitoringReport $report, int $userId): MonitoringReport
    {
        return DB::transaction(function () use ($report, $userId) {
            $locked = MonitoringReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            $version = max(1, (int) $locked->edit_version);
            $identity = $this->identityAnswersForReport($locked);
            $locked->fill([
                'status' => $locked->submitted_version ? 'revision_draft' : 'draft',
                'edit_version' => $version + 1,
                'updated_by' => $userId,
            ])->save();
            $locked->versions()->updateOrCreate([
                'version' => $version,
            ], [
                'answers' => $identity,
                'submission_channel' => 'account',
            ]);
            if (Schema::hasTable('tr_monev')) {
                $monev = Monev::where('legacy_report_id', $locked->id)->first();
                if ($monev) {
                    $monev->fill(['status' => $locked->status, 'edit_version' => $locked->edit_version])->save();
                    $monev->answers()->whereHas('question', fn ($q) => $q->whereNotIn('kode', ['1', '2', '3', '4', '5', '6', '7', '8', '9']))->delete();
                }
            }
            return $locked->fresh();
        });
    }

    private function identityAnswersForReport(MonitoringReport $report): array
    {
        $version = $report->versions()->latest('version')->first();
        $answers = $version?->answers ?? [];
        return array_intersect_key($answers, array_flip([1, 2, 3, 4, 5, 6, 7, 8, 9]));
    }

    public function legacyCount(string $table): int
    {
        return DB::connection('mysql_gen1')->table($table)->count();
    }
}
