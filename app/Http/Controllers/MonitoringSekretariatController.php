<?php

namespace App\Http\Controllers;

use App\Models\MonitoringReport;
use App\Models\MonitoringAccreditation;
use App\Models\MonitoringReportLink;
use App\Models\Profile;
use App\Services\MonitoringReportLinkService;
use Illuminate\Http\Request;

class MonitoringSekretariatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is.sekretariat']);
    }

    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $reports = MonitoringReport::with(['profile', 'jenisPengajuan', 'versions', 'links' => fn ($q) => $q->latest()])
            ->when($request->year, fn ($q, $v) => $q->where('report_year', $v))
            ->when($request->program, fn ($q, $v) => $q->where('jenis_pengajuan_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->search, fn ($q, $v) => $q->whereHas('profile', fn ($p) => $p->where('nama_lembaga', 'like', "%{$v}%")))
            ->latest('report_year')->get();
        $stats = [
            'total' => $reports->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'draft' => $reports->whereIn('status', ['draft', 'revision_draft'])->count(),
            'nihil' => $reports->filter(fn ($r) => (int) data_get($r->versions->where('version', $r->submitted_version)->first()?->answers, '10', -1) === 0)->count(),
        ];
        $accreditations = MonitoringAccreditation::with(['jenisPengajuan', 'profile'])->whereDate('valid_from', '<=', $year . '-12-31')->whereDate('valid_until', '>=', $year . '-01-01')->get();
        foreach ($reports as $report) {
            $link = $report->links->first(fn ($item) => !$item->deleted_at);
            if ($link && $link->expires_at->gt(now())) $link->public_url = route('monitoring-akreditasi.public.form', app(\App\Services\MonitoringReportLinkService::class)->decrypt($link));
        }
        return view('sekretariat.monitoring-evaluasi', compact('reports', 'stats', 'accreditations', 'year'));
    }

    public function generateLink(int $profile, int $program, int $year, Request $request, MonitoringReportLinkService $links)
    {
        $data = $request->validate(['expires_at' => 'nullable|date']);
        $this->authorizeAccreditation($profile, $program, $year);
        $report = MonitoringReport::firstOrCreate(['profile_id' => $profile, 'jenis_pengajuan_id' => $program, 'report_year' => $year], ['status' => 'draft']);
        $issued = $links->issue($report, auth()->id(), $data['expires_at'] ?? null);
        return back()->with('generated_link', route('monitoring-akreditasi.public.form', $issued['token']))->with('success', 'Link monitoring berhasil dibuat.');
    }

    public function regenerateLink(MonitoringReport $report, Request $request, MonitoringReportLinkService $links)
    {
        $data = $request->validate(['expires_at' => 'nullable|date']);
        $issued = $links->issue($report, auth()->id(), $data['expires_at'] ?? null);
        return back()->with('generated_link', route('monitoring-akreditasi.public.form', $issued['token']))->with('success', 'Link monitoring berhasil dibuat ulang.');
    }

    public function updateLinkExpiry(MonitoringReportLink $link, Request $request, MonitoringReportLinkService $links)
    {
        $data = $request->validate(['expires_at' => 'required|date|after:now']);
        $links->updateExpiry($link, auth()->id(), $data['expires_at']);
        return back()->with('success', 'Kedaluwarsa link berhasil diperbarui.');
    }

    public function deleteLink(MonitoringReportLink $link, MonitoringReportLinkService $links)
    {
        $links->delete($link, auth()->id());
        return back()->with('success', 'Link berhasil dihapus. Laporan tetap tersimpan.');
    }

    private function authorizeAccreditation(int $profileId, int $programId, int $year): void
    {
        abort_if($year < 2020 || $year > now()->year, 422);
        abort_unless(MonitoringAccreditation::where('profile_id', $profileId)->where('jenis_pengajuan_id', $programId)
            ->whereDate('valid_from', '<=', $year . '-12-31')->whereDate('valid_until', '>=', $year . '-01-01')->exists(), 404);
    }

    public function show(MonitoringReport $report)
    {
        $report->load(['profile', 'jenisPengajuan', 'versions' => fn ($q) => $q->latest('version')]);
        return view('sekretariat.monitoring-akreditasi-detail', compact('report'));
    }
}
