<?php

namespace App\Services;

use App\Models\JenisPengajuan;
use App\Models\MonitoringAccreditation;
use App\Models\MonitoringReport;
use App\Models\MonitoringReportVersion;
use App\Models\MonitoringReportingPeriod;
use App\Models\Monev;
use App\Models\MonevKuesioner;
use App\Models\MonevPertanyaan;
use App\Models\MonevSubmission;
use App\Models\Profile;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MonitoringReportService
{
    public function reportQuery()
    {
        return config('monev.use_normalized_reports') ? Monev::query() : MonitoringReport::query();
    }

    public function periodForYear(int $year, bool $writing = false): ?MonitoringReportingPeriod
    {
        $query = MonitoringReportingPeriod::where('report_year', $year);
        if ($writing) {
            // The schedule editor takes an exclusive lock on this same row.
            // Lock it before the report, even when the year has no schedule yet.
            MonitoringReportingPeriod::settings()->sharedLock()->first();
            $query->sharedLock();
        }

        return $query->first();
    }

    public function latestSubmission(MonitoringReport|Monev $report): MonitoringReportVersion|MonevSubmission|null
    {
        if ($report instanceof Monev) {
            return $report->latestSubmission;
        }

        if (!$report->submitted_version) {
            return null;
        }

        $archive = MonevSubmission::where('monev_id', Monev::where('legacy_report_id', $report->id)->value('id'))
            ->where('version', $report->submitted_version)->first();

        return $archive ?? ($report->relationLoaded('versions')
            ? $report->versions->firstWhere('version', $report->submitted_version)
            : $report->versions()->where('version', $report->submitted_version)->first());
    }

    public function reportAccess(?MonitoringReportingPeriod $period, MonitoringReport|Monev|null $report, CarbonInterface $at, bool $writing = false): string
    {
        $state = $period?->stateAt($at) ?? 'open';
        if ($state !== 'closed' || !$report) {
            return $state;
        }

        if ($report instanceof Monev) {
            $submission = $writing ? $report->submissions()->sharedLock()->first(['id']) : $report->latestSubmission;
            if ($submission || $report->submitted_at) {
                return 'closed_submitted';
            }

            $savedAt = $report->first_draft_saved_at ? CarbonImmutable::parse($report->first_draft_saved_at, 'UTC') : null;
        } else {
            if ($report->submitted_version) {
                return 'closed_submitted';
            }

            $versions = $report->versions()->whereNotNull('created_at')->orderBy('created_at');
            $savedAt = ($writing ? $versions->sharedLock() : $versions)->first()?->created_at;
        }

        return $savedAt && $savedAt->lt($period->closes_at) ? 'late_draft' : 'closed';
    }

    private function assertPeriodAllowsChange(?MonitoringReportingPeriod $period, MonitoringReport|Monev|null $report, CarbonInterface $at, bool $reset = false): void
    {
        $access = $this->reportAccess($period, $report, $at, true);
        if ($access === 'open' || ($access === 'late_draft' && !$reset)) {
            return;
        }

        abort(422, match ($access) {
            'upcoming' => 'Periode pelaporan belum dibuka.',
            'closed_submitted' => 'Periode pelaporan sudah ditutup. Kiriman terakhir hanya dapat dilihat.',
            'late_draft' => 'Jawaban draf tidak dapat direset setelah periode pelaporan ditutup.',
            default => 'Periode pelaporan sudah ditutup. Hanya draf yang disimpan sebelum tenggat yang dapat dilanjutkan.',
        });
    }

    public function startLegacyDraft(Profile $profile, int $program, int $year, array $answers, int $userId): MonitoringReport
    {
        return DB::transaction(function () use ($profile, $program, $year, $answers, $userId) {
            $period = $this->periodForYear($year, true);
            $this->assertPeriodAllowsChange($period, null, now());
            $report = MonitoringReport::firstOrCreate([
                'profile_id' => $profile->id, 'jenis_pengajuan_id' => $program, 'report_year' => $year,
            ], ['status' => 'draft', 'updated_by' => $userId]);

            return $this->saveDraft($report, $answers, $userId);
        }, 3);
    }

    public function reportForLink(int $profile, int $program, int $year): MonitoringReport|Monev
    {
        $period = $this->periodForYear($year, true);
        $key = ['profile_id' => $profile, 'jenis_pengajuan_id' => $program, 'report_year' => $year];
        if ($period?->stateAt(now()) === 'closed') {
            return $this->reportQuery()->where($key)->lockForUpdate()->first()
                ?? abort(422, 'Periode pelaporan sudah ditutup. Laporan baru tidak dapat dibuat.');
        }

        return config('monev.use_normalized_reports')
            ? $this->findOrCreateMonev($profile, $program, $year)
            : MonitoringReport::firstOrCreate($key, ['status' => 'draft']);
    }

    public function resolveReport(string $identifier): MonitoringReport|Monev
    {
        if (!config('monev.use_normalized_reports')) {
            abort_unless(ctype_digit($identifier), 404);
            return MonitoringReport::findOrFail($identifier);
        }
        abort_unless(ctype_digit($identifier) || \Illuminate\Support\Str::isUuid($identifier), 404);
        return Monev::where(ctype_digit($identifier) ? 'legacy_report_id' : 'uuid', $identifier)->firstOrFail();
    }

    public function programsFor(Profile $profile, int $year)
    {
        return MonitoringAccreditation::with('jenisPengajuan')
            ->where('profile_id', $profile->id)
            ->whereDate('valid_from', '<=', ($year - 1) . '-12-31')
            ->whereDate('valid_until', '>=', $year . '-01-01')
            ->get();
    }

    public function saveDraft(MonitoringReport $report, array $answers, ?int $userId, string $channel = 'account', ?int $expectedVersion = null): MonitoringReport
    {
        return DB::transaction(function () use ($report, $answers, $userId, $channel, $expectedVersion) {
            $period = $this->periodForYear($report->report_year, true);
            $locked = MonitoringReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            $this->assertPeriodAllowsChange($period, $locked, now());
            abort_if($expectedVersion !== null && $locked->edit_version !== $expectedVersion, 409, 'Laporan berubah pada sesi lain. Salin perubahan Anda lalu muat ulang.');
            $version = max(1, (int) $locked->edit_version);
            $locked->fill(['status' => $locked->submitted_version ? 'revision_draft' : 'draft', 'edit_version' => $version + 1, 'updated_by' => $userId])->save();
            $locked->versions()->updateOrCreate(['version' => $version], ['answers' => $answers, 'submission_channel' => $channel]);
            $this->syncNormalized($locked, $answers);
            return $locked->fresh();
        });
    }

    public function submit(MonitoringReport $report, array $answers, Profile $profile, ?int $userId, string $channel = 'account', ?int $expectedVersion = null): MonitoringReport
    {
        return DB::transaction(function () use ($report, $answers, $profile, $userId, $channel, $expectedVersion) {
            $period = $this->periodForYear($report->report_year, true);
            $report = MonitoringReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            $receivedAt = now();
            $this->assertPeriodAllowsChange($period, $report, $receivedAt);
            abort_if($expectedVersion !== null && $report->edit_version !== $expectedVersion, 409, 'Laporan berubah pada sesi lain. Salin perubahan Anda lalu muat ulang.');
            $version = $report->edit_version;
            $accreditation = MonitoringAccreditation::with('jenisPengajuan')
                ->where('profile_id', $profile->id)->where('jenis_pengajuan_id', $report->jenis_pengajuan_id)
                ->whereDate('valid_from', '<=', ($report->report_year - 1) . '-12-31')->whereDate('valid_until', '>=', $report->report_year . '-01-01')->firstOrFail();
            $report->fill(['status' => 'submitted', 'edit_version' => $version + 1, 'submitted_version' => $version, 'updated_by' => $userId])->save();
            $submission = $report->versions()->create([
                'version' => $version, 'answers' => $answers, 'submitted_by' => $userId, 'submitted_at' => $receivedAt, 'submission_channel' => $channel,
                'identity_snapshot' => $profile->only(['id', 'nama_lembaga', 'alamat_lembaga', 'telepon', 'email', 'website']),
                'accreditation_snapshot' => $accreditation->toArray(),
            ]);
            $monev = $this->syncNormalized($report, $answers, $submission);
            if ($period && $monev) {
                $this->archiveMonevSubmission($monev, $version, $answers, $submission->only([
                    'identity_snapshot', 'accreditation_snapshot', 'submitted_by', 'submitted_at',
                ]), $channel, $period);
            }
            return $report->fresh();
        });
    }

    private function activeQuestionnaire(): ?MonevKuesioner
    {
        return MonevKuesioner::where('kode', 'monitoring-akreditasi-pstk')
            ->where('is_active', true)->orderByDesc('versi')->first();
    }

    public function questionnaireQuestions(MonitoringReport|Monev|null $report = null): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('tr_monev')) return collect();

        $questionnaireId = $report instanceof Monev
            ? $report->kuesioner_id
            : ($report ? Monev::where('legacy_report_id', $report->id)->value('kuesioner_id') : null);
        $questionnaireId ??= $this->activeQuestionnaire()?->id;

        return MonevPertanyaan::where('kuesioner_id', $questionnaireId)
            ->orderBy('order_number')->get()->keyBy('kode');
    }

    public function questionnaireForReport(MonitoringReport|Monev $report): ?MonevKuesioner
    {
        if ($report instanceof Monev) {
            return $report->questionnaire;
        }
        if (!Schema::hasTable('tr_monev')) {
            return null;
        }

        return Monev::with('questionnaire')->where('legacy_report_id', $report->id)->first()?->questionnaire;
    }

    public function reportQuestions(MonitoringReport|Monev $report): \Illuminate\Support\Collection
    {
        $questions = $this->questionnaireQuestions($report);

        return $questions->isNotEmpty() ? $questions
            : collect(range(1, 26))->map(fn ($number) => (object) ['kode' => (string) $number]);
    }

    public function answerKey(string $code): string
    {
        return match ($code) {
            '12a' => '12.internal',
            '12b' => '12.external',
            '23a' => '23_keterangan',
            default => $code,
        };
    }

    public function ensureMonevReport(MonitoringReport $report): ?Monev
    {
        if (!Schema::hasTable('tr_monev')) return null;
        $monev = Monev::where('legacy_report_id', $report->id)->first();
        if (!$monev) {
            $questionnaire = $this->activeQuestionnaire();
            if (!$questionnaire) return null;
            $monev = Monev::create([
                'legacy_report_id' => $report->id,
                'uuid' => $report->uuid,
                'kuesioner_id' => $questionnaire->id,
                'profile_id' => $report->profile_id,
                'jenis_pengajuan_id' => $report->jenis_pengajuan_id,
                'report_year' => $report->report_year,
                'status' => 'draft',
                'edit_version' => $report->edit_version,
            ]);
        }
        return $monev;
    }

    public function syncNormalized(MonitoringReport $report, array $answers, ?MonitoringReportVersion $submission = null): ?Monev
    {
        $monev = $this->ensureMonevReport($report);
        if (!$monev) return null;
        $monev->fill(['status' => $report->status === 'submitted' ? 'submitted' : 'draft', 'edit_version' => $report->edit_version]);
        if ($submission) {
            $monev->submitted_by = $submission->submitted_by;
            $monev->submitted_at = $submission->submitted_at;
            $monev->identity_snapshot = $submission->identity_snapshot;
            $monev->accreditation_snapshot = $submission->accreditation_snapshot;
        }
        $monev->save();
        $this->replaceMonevAnswers($monev, $answers);
        return $monev;
    }

    public function startMonevDraft(Profile $profile, int $program, int $year, array $answers): Monev
    {
        return DB::transaction(function () use ($profile, $program, $year, $answers) {
            $period = $this->periodForYear($year, true);
            $this->assertPeriodAllowsChange($period, null, now());
            $monev = $this->findOrCreateMonev($profile->id, $program, $year);
            abort_if($monev->edit_version !== 1, 409, 'Laporan sudah ada. Buka draf dari daftar laporan sebelum mengubahnya.');
            return $this->writeMonevDraft($monev, $answers, 1);
        }, 3);
    }

    public function findOrCreateMonev(int $profileId, int $program, int $year): Monev
    {
        $key = ['profile_id' => $profileId, 'jenis_pengajuan_id' => $program, 'report_year' => $year];
        $questionnaire = $this->activeQuestionnaire();
        abort_unless($questionnaire, 503, 'Kuesioner aktif belum tersedia.');
        DB::table('tr_monev')->upsert([array_merge($key, [
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'kuesioner_id' => $questionnaire->id, 'status' => 'draft', 'edit_version' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ])], array_keys($key), ['profile_id']);
        return Monev::where($key)->lockForUpdate()->firstOrFail();
    }

    public function writeMonevDraft(Monev $report, array $answers, int $expectedVersion): Monev
    {
        return DB::transaction(function () use ($report, $answers, $expectedVersion) {
            $period = $this->periodForYear($report->report_year, true);
            $locked = $this->lockMonevRevision($report, $expectedVersion);
            $savedAt = now();
            $this->assertPeriodAllowsChange($period, $locked, $savedAt);
            $locked->update([
                'status' => $locked->submitted_at ? 'revision_draft' : 'draft',
                'edit_version' => $locked->edit_version + 1,
                'first_draft_saved_at' => $locked->first_draft_saved_at ?? $savedAt->copy()->utc()->toDateTimeString(),
            ]);
            $this->replaceMonevAnswers($locked, $answers);
            return $locked;
        });
    }

    public function submitMonev(Monev $report, array $answers, Profile $profile, int $userId, string $channel, int $expectedVersion): Monev
    {
        return DB::transaction(function () use ($report, $answers, $profile, $userId, $channel, $expectedVersion) {
            $period = $this->periodForYear($report->report_year, true);
            $locked = $this->lockMonevRevision($report, $expectedVersion);
            $receivedAt = now();
            $this->assertPeriodAllowsChange($period, $locked, $receivedAt);
            $accreditation = $this->programsFor($profile, $locked->report_year)->firstWhere('jenis_pengajuan_id', $locked->jenis_pengajuan_id);
            abort_unless($accreditation, 404, 'Akreditasi program tidak ditemukan.');
            $submission = [
                'submitted_by' => $userId,
                'submitted_at' => $receivedAt,
                'identity_snapshot' => $profile->only(['id', 'nama_lembaga', 'alamat_lembaga', 'telepon', 'email', 'website']),
                'accreditation_snapshot' => $accreditation->toArray(),
            ];
            return $this->recordMonevSubmission($locked, $answers, $submission, $channel, $period);
        });
    }

    public function importMonev(Profile $profile, int $program, int $year, array $answers, array $submission): Monev
    {
        return DB::transaction(function () use ($profile, $program, $year, $answers, $submission) {
            $report = $this->findOrCreateMonev($profile->id, $program, $year);
            if ($report->submitted_at || $report->edit_version !== 1 || $report->answers()->exists() || $report->submissions()->exists()) {
                throw new \RuntimeException('Laporan Monev sudah memiliki jawaban atau kiriman.');
            }
            return $this->recordMonevSubmission($report, $answers, $submission, 'historical_import');
        });
    }

    private function recordMonevSubmission(Monev $report, array $answers, array $submission, string $channel, ?MonitoringReportingPeriod $period = null): Monev
    {
        $this->archiveMonevSubmission($report, $report->edit_version, $answers, $submission, $channel, $period);
        $report->update($submission + ['status' => 'submitted', 'edit_version' => $report->edit_version + 1]);
        $this->replaceMonevAnswers($report, $answers);
        return $report;
    }

    private function archiveMonevSubmission(Monev $report, int $version, array $answers, array $submission, string $channel, ?MonitoringReportingPeriod $period): void
    {
        $deadline = $period ? [
            'period_closed_at' => $period->closes_at->toDateTimeString(),
            'is_late' => $submission['submitted_at']->gte($period->closes_at),
        ] : [];
        DB::table('monev_submissions')->insert([
            'monev_id' => $report->id, 'version' => $version, 'submission_channel' => $channel,
            'answers' => json_encode($answers, JSON_THROW_ON_ERROR),
            'identity_snapshot' => json_encode($submission['identity_snapshot'], JSON_THROW_ON_ERROR),
            'accreditation_snapshot' => json_encode($submission['accreditation_snapshot'], JSON_THROW_ON_ERROR),
            'submitted_at' => $submission['submitted_at'], 'submitted_by' => $submission['submitted_by'],
        ] + $deadline);
    }

    public function resetMonev(Monev $report, int $expectedVersion): void
    {
        DB::transaction(function () use ($report, $expectedVersion) {
            $period = $this->periodForYear($report->report_year, true);
            $locked = $this->lockMonevRevision($report, $expectedVersion);
            $this->assertPeriodAllowsChange($period, $locked, now(), true);
            $identityCodes = array_map('strval', range(1, 9));
            $locked->answers()->whereHas('question', fn ($query) => $query->whereNotIn('kode', $identityCodes))->delete();
            $locked->update([
                'status' => $locked->submitted_at ? 'revision_draft' : 'draft',
                'edit_version' => $locked->edit_version + 1,
            ]);
        });
    }

    private function lockMonevRevision(Monev $report, int $expectedVersion): Monev
    {
        $locked = Monev::whereKey($report->id)->lockForUpdate()->firstOrFail();
        abort_if($locked->edit_version !== $expectedVersion, 409, 'Laporan berubah pada sesi lain. Salin perubahan Anda lalu muat ulang.');
        return $locked;
    }

    private function replaceMonevAnswers(Monev $monev, array $answers): void
    {
        $questions = MonevPertanyaan::where('kuesioner_id', $monev->kuesioner_id)->get()->keyBy('kode');
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

    public function answersForReport(MonitoringReport|Monev $report): ?array
    {
        if (!Schema::hasTable('tr_monev')) return null;
        $monev = $report instanceof Monev
            ? $report->loadMissing('answers.question')
            : Monev::with('answers.question')->where('legacy_report_id', $report->id)->first();
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
            $period = $this->periodForYear($report->report_year, true);
            $locked = MonitoringReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            $this->assertPeriodAllowsChange($period, $locked, now(), true);
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
