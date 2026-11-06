<?php

namespace App\Http\Controllers;

use App\Models\MonitoringReport;
use App\Models\Profile;
use App\Services\MonitoringReportService;
use Illuminate\Http\Request;

class MonitoringAkreditasiController extends Controller
{
    private $service;

    public function __construct(MonitoringReportService $service)
    {
        $this->service = $service;
        $this->middleware(['auth', 'is.lembaga']);
    }

    private function profile(): Profile
    {
        return $this->service->profileForUser(auth()->id()) ?? abort(403, 'Profil lembaga belum terhubung.');
    }

    public function index(Request $request)
    {
        $profile = $this->profile();
        $year = (int) $request->input('year', now()->year);
        $reports = MonitoringReport::with(['jenisPengajuan', 'versions'])
            ->where('profile_id', $profile->id)->when($request->program, fn ($q, $v) => $q->where('jenis_pengajuan_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))->orderByDesc('report_year')->get();
        return view('lembaga.monitoring-akreditasi.index', compact('profile', 'reports', 'year'));
    }

    public function create(Request $request)
    {
        $profile = $this->profile(); $year = (int) $request->input('year', now()->year);
        $accreditations = $this->service->programsFor($profile, $year);
        return view('lembaga.monitoring-akreditasi.form', ['profile' => $profile, 'report' => null, 'accreditations' => $accreditations, 'answers' => [], 'year' => $year]);
    }

    public function store(Request $request)
    {
        $profile = $this->profile();
        $data = $this->validateReport($request);
        $report = MonitoringReport::firstOrCreate(['profile_id' => $profile->id, 'jenis_pengajuan_id' => $data['jenis_pengajuan_id'], 'report_year' => $data['report_year']], ['status' => 'draft', 'updated_by' => auth()->id()]);
        $this->service->saveDraft($report, $data['answers'], auth()->id());
        return redirect()->route('monitoring-akreditasi.edit', $report)->with('success', 'Draf monitoring berhasil disimpan.');
    }

    public function edit(MonitoringReport $report)
    {
        $this->authorizeReport($report); $profile = $this->profile();
        $version = $report->versions()->latest('version')->first();
        return view('lembaga.monitoring-akreditasi.form', ['profile' => $profile, 'report' => $report, 'accreditations' => $this->service->programsFor($profile, $report->report_year), 'answers' => $version?->answers ?? [], 'year' => $report->report_year]);
    }

    public function update(Request $request, MonitoringReport $report)
    {
        $this->authorizeReport($report); $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id);
        $this->service->saveDraft($report, $data['answers'], auth()->id());
        return back()->with('success', 'Draf monitoring berhasil disimpan.');
    }

    public function submit(Request $request, MonitoringReport $report)
    {
        $this->authorizeReport($report); $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id, true);
        $this->service->submit($report, $data['answers'], $this->profile(), auth()->id());
        return redirect()->route('monitoring-akreditasi.index')->with('success', 'Laporan monitoring berhasil dikirim.');
    }

    private function authorizeReport(MonitoringReport $report): void
    { if ($report->profile_id !== $this->profile()->id) abort(403); }

    private function validateReport(Request $request, ?int $fixedYear = null, ?int $fixedProgram = null, bool $submit = false): array
    {
        $rules = ['jenis_pengajuan_id' => 'required|integer', 'report_year' => 'required|integer|min:2020|max:' . now()->year, 'answers' => 'array'];
        $data = $request->validate($rules); $data['jenis_pengajuan_id'] = $fixedProgram ?? $data['jenis_pengajuan_id']; $data['report_year'] = $fixedYear ?? $data['report_year'];
        if ($submit) {
            validator($data['answers'] ?? [], ['10' => 'required|integer|min:0', '11' => 'required|integer|min:0', '24' => 'required|string|max:5000', '25' => 'required|string|max:5000', '26' => 'required|string|max:5000'])->validate();
        }
        return $data;
    }
}
