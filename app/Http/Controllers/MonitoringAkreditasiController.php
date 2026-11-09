<?php

namespace App\Http\Controllers;

use App\Models\MonitoringReport;
use App\Models\Profile;
use App\Services\MonitoringReportService;
use App\Services\MonitoringReportLinkService;
use Illuminate\Http\Request;

class MonitoringAkreditasiController extends Controller
{
    private $service;

    public function __construct(MonitoringReportService $service)
    {
        $this->service = $service;
        $this->middleware(['auth', 'is.lembaga'])->except(['publicForm', 'publicDraft', 'publicSubmit']);
    }

    public function publicForm(string $token, MonitoringReportLinkService $links)
    {
        $link = $links->findActive($token);
        $report = $link->report; $version = $report->versions()->where('version', $report->submitted_version ?? max(1, $report->edit_version - 1))->first();
        $accreditation = $this->service->programsFor($report->profile, $report->report_year)->firstWhere('jenis_pengajuan_id', $report->jenis_pengajuan_id);
        $answers = array_replace($this->identityAnswers($report->profile, $accreditation, $report->report_year), $version?->answers ?? []);
        return response()->view('lembaga.monitoring-akreditasi.public-form', [
            'profile' => $report->profile, 'report' => $report, 'accreditations' => $this->service->programsFor($report->profile, $report->report_year),
            'answers' => $answers, 'year' => $report->report_year, 'token' => $token, 'link' => $link,
        ])->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer');
    }

    public function publicDraft(Request $request, string $token, MonitoringReportLinkService $links)
    {
        $link = $links->findActive($token); $data = $this->validateReport($request, $link->report->report_year, $link->report->jenis_pengajuan_id);
        $report = $this->service->saveDraft($link->report, $data['answers'] ?? [], null, 'secretariat_link', $request->integer('edit_version'));
        return response()->json(['ok' => true, 'edit_version' => $report->edit_version])->header('Cache-Control', 'no-store, private');
    }

    public function publicSubmit(Request $request, string $token, MonitoringReportLinkService $links)
    {
        $link = $links->findActive($token); $data = $this->validateReport($request, $link->report->report_year, $link->report->jenis_pengajuan_id, true);
        $data['answers'] = $this->lockIdentityAnswers($data['answers'] ?? [], $link->report->profile, $link->report->jenis_pengajuan_id, $link->report->report_year);
        $this->service->submit($link->report, $data['answers'], $link->report->profile, null, 'secretariat_link', $request->integer('edit_version'));
        return response()->view('lembaga.monitoring-akreditasi.public-complete')->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer');
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

    private function identityAnswers(Profile $profile, $accreditation, int $year): array
    {
        return [1 => $profile->nama_lembaga, 2 => $accreditation?->jenisPengajuan?->nama, 3 => $profile->alamat_lembaga,
            4 => $profile->telepon, 5 => $profile->email, 6 => $profile->website, 7 => $accreditation?->certificate_number,
            8 => $accreditation?->predicate, 9 => $year];
    }

    private function lockIdentityAnswers(array $answers, Profile $profile, int $programId, int $year): array
    {
        $accreditation = $this->service->programsFor($profile, $year)->firstWhere('jenis_pengajuan_id', $programId);
        foreach ([1, 2, 7, 8, 9] as $number) $answers[$number] = $this->identityAnswers($profile, $accreditation, $year)[$number] ?? null;
        return $answers;
    }

    public function store(Request $request)
    {
        $profile = $this->profile();
        $data = $this->validateReport($request);
        abort_unless($this->service->programsFor($profile, (int) $data['report_year'])->contains('jenis_pengajuan_id', (int) $data['jenis_pengajuan_id']), 403);
        $report = MonitoringReport::firstOrCreate(['profile_id' => $profile->id, 'jenis_pengajuan_id' => $data['jenis_pengajuan_id'], 'report_year' => $data['report_year']], ['status' => 'draft', 'updated_by' => auth()->id()]);
        $report = $this->service->saveDraft($report, $data['answers'] ?? [], auth()->id());
        if ($request->expectsJson()) return response()->json(['ok' => true, 'edit_version' => $report->edit_version, 'edit_url' => route('monitoring-akreditasi.edit', $report)]);
        return redirect()->route('monitoring-akreditasi.edit', $report)->with('success', 'Draf monitoring berhasil disimpan.');
    }

    public function edit(MonitoringReport $report)
    {
        $this->authorizeReport($report); $profile = $this->profile();
        $version = $report->versions()->where('version', $report->submitted_version ?? max(1, $report->edit_version - 1))->first();
        $accreditation = $this->service->programsFor($profile, $report->report_year)->firstWhere('jenis_pengajuan_id', $report->jenis_pengajuan_id);
        return view('lembaga.monitoring-akreditasi.form', ['profile' => $profile, 'report' => $report, 'accreditations' => $this->service->programsFor($profile, $report->report_year), 'answers' => array_replace($this->identityAnswers($profile, $accreditation, $report->report_year), $version?->answers ?? []), 'year' => $report->report_year]);
    }

    public function update(Request $request, MonitoringReport $report)
    {
        $this->authorizeReport($report); $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id);
        $report = $this->service->saveDraft($report, $data['answers'] ?? [], auth()->id(), 'account', $request->integer('edit_version'));
        if ($request->expectsJson()) return response()->json(['ok' => true, 'edit_version' => $report->edit_version]);
        return back()->with('success', 'Draf monitoring berhasil disimpan.');
    }

    public function submit(Request $request, MonitoringReport $report)
    {
        $this->authorizeReport($report); $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id, true);
        $data['answers'] = $this->lockIdentityAnswers($data['answers'] ?? [], $this->profile(), $report->jenis_pengajuan_id, $report->report_year);
        $this->service->submit($report, $data['answers'], $this->profile(), auth()->id(), 'account', $request->integer('edit_version'));
        if ($request->expectsJson()) return response()->json(['ok' => true]);
        return redirect()->route('monitoring-akreditasi.index')->with('success', 'Laporan monitoring berhasil dikirim.');
    }

    private function authorizeReport(MonitoringReport $report): void
    { if ($report->profile_id !== $this->profile()->id) abort(403); }

    private function validateReport(Request $request, ?int $fixedYear = null, ?int $fixedProgram = null, bool $submit = false): array
    {
        $rules = ['jenis_pengajuan_id' => 'required|integer', 'report_year' => 'required|integer|min:2020|max:' . now()->year, 'answers' => 'sometimes|array', 'answers.*' => 'nullable', 'edit_version' => 'nullable|integer|min:1'];
        $data = $request->validate($rules); $data['jenis_pengajuan_id'] = $fixedProgram ?? $data['jenis_pengajuan_id']; $data['report_year'] = $fixedYear ?? $data['report_year'];
        if ($submit) {
            $answers = $data['answers'] ?? [];
            $rules = [];
            foreach (range(1, 26) as $question) $rules[$question] = 'required';
            foreach ([10, 11] as $question) $rules[$question] = 'required|integer|min:0';
            $rules[12] = ['required', 'array'];
            $rules['12.internal'] = 'required|integer|min:0';
            $rules['12.external'] = 'required|integer|min:0';
            foreach ([24, 25, 26] as $question) $rules[$question] = 'required|string|max:5000';
            if ((int) ($answers[10] ?? -1) === 0) {
                foreach ([13, 14, 15, 16, 17, 18, 22, 23] as $question) $rules[$question] = 'nullable';
                if ((int) ($answers[11] ?? -1) !== 0) throw \Illuminate\Validation\ValidationException::withMessages(['answers.11' => 'Jumlah peserta harus nol jika jumlah pelatihan nol.']);
            }
            validator($answers, $rules)->validate();
            if ((int) $answers[12]['internal'] + (int) $answers[12]['external'] !== (int) $answers[11]) {
                throw \Illuminate\Validation\ValidationException::withMessages(['answers.12' => 'Jumlah peserta internal dan eksternal harus sama dengan total peserta.']);
            }
            $data['answers'] = $answers;
        }
        return $data;
    }
}
