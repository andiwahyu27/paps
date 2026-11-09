<?php

namespace App\Services;

use App\Models\MonitoringReport;
use App\Models\MonitoringReportLink;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MonitoringReportLinkService
{
    public function issue(MonitoringReport $report, int $actorId, ?string $expiresAt = null): array
    {
        return DB::transaction(function () use ($report, $actorId, $expiresAt) {
            MonitoringReportLink::where('monitoring_report_id', $report->id)->whereNull('deleted_at')->lockForUpdate()->get()
                ->each(function ($old) use ($actorId) {
                    $old->update(['deleted_at' => now()]);
                    $this->audit($old->monitoring_report_id, $old->id, $actorId, 'regenerated', $old->expires_at, null);
                });
            $token = bin2hex(random_bytes(32));
            $expiration = $expiresAt ? \Carbon\Carbon::parse($expiresAt, 'Asia/Jakarta')->utc() : now()->addHours(72);
            abort_if($expiration->lte(now()), 422, 'Waktu kedaluwarsa harus di masa depan.');
            $link = MonitoringReportLink::create([
                'monitoring_report_id' => $report->id, 'token_hash' => hash('sha256', $token),
                'token_encrypted' => Crypt::encryptString($token), 'created_by' => $actorId,
                'expires_at' => $expiration,
            ]);
            $this->audit($report->id, $link->id, $actorId, 'generated', null, $expiration);
            return ['link' => $link, 'token' => $token];
        });
    }

    public function updateExpiry(MonitoringReportLink $link, int $actorId, string $expiresAt): void
    {
        abort_if($link->deleted_at, 404, 'Link sudah dihapus.');
        $expiration = \Carbon\Carbon::parse($expiresAt, 'Asia/Jakarta')->utc();
        abort_if($expiration->lte(now()), 422, 'Waktu kedaluwarsa harus di masa depan.');
        $before = $link->expires_at;
        $link->update(['expires_at' => $expiration]);
        $this->audit($link->monitoring_report_id, $link->id, $actorId, 'expiry_updated', $before, $expiration);
    }

    public function delete(MonitoringReportLink $link, int $actorId): void
    {
        abort_if($link->deleted_at, 404, 'Link sudah dihapus.');
        $before = $link->expires_at;
        $link->update(['deleted_at' => now()]);
        $this->audit($link->monitoring_report_id, $link->id, $actorId, 'deleted', $before, null);
    }

    public function findActive(string $token): MonitoringReportLink
    {
        $link = MonitoringReportLink::with('report.profile', 'report.jenisPengajuan')
            ->where('token_hash', hash('sha256', $token))->firstOrFail();
        abort_if($link->deleted_at, 404, 'Link laporan ini sudah dihapus. Hubungi sekretariat atau masuk melalui akun lembaga.');
        abort_if(!$link->expires_at->gt(now()), 410, 'Link laporan ini sudah kedaluwarsa. Hubungi sekretariat atau masuk melalui akun lembaga.');
        return $link;
    }

    public function decrypt(MonitoringReportLink $link): string { return Crypt::decryptString($link->token_encrypted); }

    private function audit(int $reportId, ?int $linkId, int $actorId, string $action, $before, $after): void
    {
        DB::table('monitoring_report_link_audits')->insert([
            'monitoring_report_id' => $reportId, 'monitoring_report_link_id' => $linkId,
            'actor_id' => $actorId, 'action' => $action,
            'expires_at_before' => $before, 'expires_at_after' => $after, 'created_at' => now(),
        ]);
    }
}
