<?php

namespace App\Http\Controllers;

use App\Models\MonitoringReport;
use Illuminate\Http\Request;

class MonitoringSekretariatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is.sekretariat']);
    }

    public function index(Request $request)
    {
        $reports = MonitoringReport::with(['profile', 'jenisPengajuan', 'versions'])
            ->when($request->year, fn ($q, $v) => $q->where('report_year', $v))
            ->when($request->program, fn ($q, $v) => $q->where('jenis_pengajuan_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->search, fn ($q, $v) => $q->whereHas('profile', fn ($p) => $p->where('nama_lembaga', 'like', "%{$v}%")))
            ->latest('report_year')->get();
        $stats = [
            'total' => $reports->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'draft' => $reports->whereIn('status', ['draft', 'revision_draft'])->count(),
            'nihil' => $reports->filter(fn ($r) => data_get($r->versions->where('version', $r->submitted_version)->first()?->answers, '10') === 0)->count(),
        ];
        return view('sekretariat.monitoring-evaluasi', compact('reports', 'stats'));
    }

    public function show(MonitoringReport $report)
    {
        $report->load(['profile', 'jenisPengajuan', 'versions' => fn ($q) => $q->latest('version')]);
        return view('sekretariat.monitoring-akreditasi-detail', compact('report'));
    }
}
