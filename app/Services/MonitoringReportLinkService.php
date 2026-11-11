<?php

namespace App\Services;

use App\Models\Monev;
use App\Models\MonitoringReport;
use App\Models\MonitoringReportLink;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MonitoringReportLinkService
{
    public function __construct(private MonitoringReportService $reports)
    {
    }

    public function issue(MonitoringReport|Monev $report, int $actorId, ?string $expiresAt = null): array
    {
        return DB::transaction(function () use ($report, $actorId, $expiresAt) {
            $report = $report->newQuery()->whereKey($report->id)->lockForUpdate()->firstOrFail();
            $legacyId = $report instanceof Monev ? null : $report->id;
            $monevId = $report instanceof Monev ? $report->id : null;
            if ($report instanceof MonitoringReport && Schema::hasColumn('monitoring_report_links', 'monev_id')) {
                $monevId = Monev::where('legacy_report_id', $report->id)->value('id');
                if ($monevId === null && !$report->versions()->exists()) {
                    $monevId = $this->reports->ensureMonevReport($report)?->id;
                }
                abort_if($monevId === null, 409, 'Laporan belum dipetakan ke Monev.');
            }
            $report->links()->whereNull('deleted_at')->lockForUpdate()->get()
                ->each(function ($old) use ($actorId) {
                    $old->update(['deleted_at' => now()]);
                    $this->audit($old, $actorId, 'regenerated', $old->expires_at, null);
                });
            $token = bin2hex(random_bytes(32));
            $expiration = $expiresAt ? \Carbon\Carbon::parse($expiresAt, 'Asia/Jakarta')->utc() : now()->addHours(72);
            abort_if($expiration->lte(now()), 422, 'Waktu kedaluwarsa harus di masa depan.');
            $attributes = [
                'monitoring_report_id' => $legacyId, 'token_hash' => hash('sha256', $token),
                'token_encrypted' => Crypt::encryptString($token), 'created_by' => $actorId,
                'expires_at' => $expiration,
            ];
            if ($monevId !== null) {
                $attributes['monev_id'] = $monevId;
            }
            $link = MonitoringReportLink::create($attributes);
            $this->audit($link, $actorId, 'generated', null, $expiration);
            return ['link' => $link, 'token' => $token];
        });
    }

    public function updateExpiry(MonitoringReportLink $link, int $actorId, string $expiresAt): void
    {
        $expiration = \Carbon\Carbon::parse($expiresAt, 'Asia/Jakarta')->utc();
        DB::transaction(function () use ($link, $actorId, $expiration) {
            $locked = MonitoringReportLink::whereKey($link->id)->lockForUpdate()->firstOrFail();
            abort_if($locked->deleted_at, 404, 'Link sudah dihapus.');
            abort_if($expiration->lte(now()), 422, 'Waktu kedaluwarsa harus di masa depan.');
            $before = $locked->expires_at;
            $locked->update(['expires_at' => $expiration]);
            $this->audit($locked, $actorId, 'expiry_updated', $before, $expiration);
        });
    }

    public function delete(MonitoringReportLink $link, int $actorId): void
    {
        DB::transaction(function () use ($link, $actorId) {
            $locked = MonitoringReportLink::whereKey($link->id)->lockForUpdate()->firstOrFail();
            abort_if($locked->deleted_at, 404, 'Link sudah dihapus.');
            $locked->update(['deleted_at' => now()]);
            $this->audit($locked, $actorId, 'deleted', $locked->expires_at, null);
        });
    }

    public function findActive(string $token): MonitoringReportLink
    {
        $link = MonitoringReportLink::where('token_hash', hash('sha256', $token))->firstOrFail();
        abort_if($link->deleted_at, 404, 'Link laporan ini sudah dihapus. Hubungi sekretariat atau masuk melalui akun lembaga.');
        abort_if(!$link->expires_at->gt(now()), 410, 'Link laporan ini sudah kedaluwarsa. Hubungi sekretariat atau masuk melalui akun lembaga.');
        return $link;
    }

    public function linkedReport(MonitoringReportLink $link): MonitoringReport|Monev
    {
        return (config('monev.use_normalized_reports') ? $link->monev : $link->report) ?? abort(404);
    }

    public function decrypt(MonitoringReportLink $link): string { return Crypt::decryptString($link->token_encrypted); }

    private function audit(MonitoringReportLink $link, int $actorId, string $action, $before, $after): void
    {
        $attributes = [
            'monitoring_report_id' => config('monev.use_normalized_reports') ? null : $link->monitoring_report_id,
            'monitoring_report_link_id' => $link->id,
            'actor_id' => $actorId, 'action' => $action,
            'expires_at_before' => $before, 'expires_at_after' => $after, 'created_at' => now(),
        ];
        if (Schema::hasColumn('monitoring_report_link_audits', 'monev_id')) {
            $attributes['monev_id'] = $link->monev_id
                ?? Monev::where('legacy_report_id', $link->monitoring_report_id)->value('id');
        }
        DB::table('monitoring_report_link_audits')->insert($attributes);
    }
}
