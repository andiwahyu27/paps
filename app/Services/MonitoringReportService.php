<?php

namespace App\Services;

use App\Models\JenisPengajuan;
use App\Models\MonitoringAccreditation;
use App\Models\MonitoringReport;
use App\Models\MonitoringReportVersion;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

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
            return $report->fresh();
        });
    }

    public function profileForUser(int $userId): ?Profile
    {
        return Profile::whereHas('pic', fn ($q) => $q->whereKey($userId))->first();
    }

    public function legacyCount(string $table): int
    {
        return DB::connection('mysql_gen1')->table($table)->count();
    }
}
