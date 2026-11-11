<?php

namespace App\Http\Controllers;

use App\Models\MonitoringReport;
use App\Models\MonitoringReportingPeriod;
use App\Models\Monev;
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
        $this->middleware(['auth', 'is.lembaga']);
    }

    public function global()
    {
        $period = MonitoringReportingPeriod::global();
        $state = $period?->stateAt(now());

        return view('lembaga.monitoring-akreditasi.period', compact('period', 'state'));
    }

    public function publicForm(string $token, MonitoringReportLinkService $links)
    {
        $link = $links->findActive($token);
        $report = $links->linkedReport($link);
        $this->authorizeReport($report);
        $accreditation = $this->service->programsFor($report->profile, $report->report_year)->firstWhere('jenis_pengajuan_id', $report->jenis_pengajuan_id);
        $answers = array_replace($this->identityAnswers($report->profile, $accreditation, $report->report_year), $this->editableAnswers($report));
        return response()->view('lembaga.monitoring-akreditasi.public-form', [
            'profile' => $report->profile, 'report' => $report, 'accreditations' => $this->service->programsFor($report->profile, $report->report_year),
            'answers' => $answers, 'year' => $report->report_year, 'token' => $token, 'link' => $link, 'accreditation' => $accreditation,
            'questionnaireQuestions' => $this->service->questionnaireQuestions($report),
        ])->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer');
    }

    public function publicDraft(Request $request, string $token, MonitoringReportLinkService $links)
    {
        $link = $links->findActive($token);
        $report = $links->linkedReport($link);
        $this->authorizeReport($report);
        $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id);
        $answers = $this->lockIdentityAnswers($data['answers'] ?? [], $report->profile, $report->jenis_pengajuan_id, $report->report_year);
        $report = $this->saveDraft($request, $report, $answers, 'secretariat_link');
        return response()->json(['ok' => true, 'edit_version' => $report->edit_version])->header('Cache-Control', 'no-store, private');
    }

    public function publicSubmit(Request $request, string $token, MonitoringReportLinkService $links)
    {
        $link = $links->findActive($token);
        $report = $links->linkedReport($link);
        $this->authorizeReport($report);
        $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id, true, $report);
        $answers = $this->lockIdentityAnswers($data['answers'], $report->profile, $report->jenis_pengajuan_id, $report->report_year);
        $report = $this->submitReport($request, $report, $answers, 'secretariat_link');
        return response()->json(['ok' => true, 'message' => 'Laporan berhasil dikirim.', 'edit_version' => $report->edit_version])
            ->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer');
    }

    private function profile(): Profile
    {
        return $this->service->profileForUser(auth()->id()) ?? abort(403, 'Profil lembaga belum terhubung.');
    }

    public function index(Request $request)
    {
        $profile = $this->profile();
        $year = (int) $request->input('year', now()->year);
        $model = config('monev.use_normalized_reports') ? Monev::class : MonitoringReport::class;
        $reports = $model::with('jenisPengajuan')
            ->where('profile_id', $profile->id)->when($request->program, fn ($q, $v) => $q->where('jenis_pengajuan_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))->orderByDesc('report_year')->get();
        return view('lembaga.monitoring-akreditasi.index', compact('profile', 'reports', 'year'));
    }

    public function create(Request $request)
    {
        $profile = $this->profile(); $year = (int) $request->input('year', now()->year);
        $accreditations = $this->service->programsFor($profile, $year);
        return view('lembaga.monitoring-akreditasi.form', ['profile' => $profile, 'report' => null, 'accreditations' => $accreditations, 'accreditation' => null, 'answers' => $this->identityAnswers($profile, null, $year), 'year' => $year, 'questionnaireQuestions' => $this->service->questionnaireQuestions()]);
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
        $data['answers'] = $this->lockIdentityAnswers($data['answers'] ?? [], $profile, (int) $data['jenis_pengajuan_id'], (int) $data['report_year']);
        if (config('monev.use_normalized_reports')) {
            $report = $this->service->startMonevDraft($profile, $data['jenis_pengajuan_id'], $data['report_year'], $data['answers']);
        } else {
            $report = MonitoringReport::firstOrCreate(['profile_id' => $profile->id, 'jenis_pengajuan_id' => $data['jenis_pengajuan_id'], 'report_year' => $data['report_year']], ['status' => 'draft', 'updated_by' => auth()->id()]);
            $report = $this->service->saveDraft($report, $data['answers'], auth()->id());
        }
        if ($request->expectsJson()) return response()->json(['ok' => true, 'edit_version' => $report->edit_version, 'edit_url' => route('monitoring-akreditasi.edit', $report)]);
        return redirect()->route('monitoring-akreditasi.edit', $report)->with('success', 'Draf monitoring berhasil disimpan.');
    }

    public function edit(string $report)
    {
        $report = $this->service->resolveReport($report);
        $this->authorizeReport($report); $profile = $this->profile();
        $accreditation = $this->service->programsFor($profile, $report->report_year)->firstWhere('jenis_pengajuan_id', $report->jenis_pengajuan_id);
        return view('lembaga.monitoring-akreditasi.form', ['profile' => $profile, 'report' => $report, 'accreditations' => $this->service->programsFor($profile, $report->report_year), 'accreditation' => $accreditation, 'answers' => array_replace($this->identityAnswers($profile, $accreditation, $report->report_year), $this->editableAnswers($report)), 'year' => $report->report_year, 'questionnaireQuestions' => $this->service->questionnaireQuestions($report)]);
    }

    public function view(string $report)
    {
        $report = $this->service->resolveReport($report);
        $this->authorizeReport($report);
        $profile = $this->profile();
        $accreditation = $this->service->programsFor($profile, $report->report_year)->firstWhere('jenis_pengajuan_id', $report->jenis_pengajuan_id);
        $answers = array_replace($this->identityAnswers($profile, $accreditation, $report->report_year), $this->service->answersForReport($report) ?? []);
        return view('lembaga.monitoring-akreditasi.view', compact('report', 'profile', 'accreditation', 'answers'));
    }

    public function reset(Request $request, string $report)
    {
        $report = $this->service->resolveReport($report);
        $this->authorizeReport($report);
        if ($report instanceof Monev) {
            $data = $request->validate(['edit_version' => 'required|integer|min:1']);
            $this->service->resetMonev($report, (int) $data['edit_version']);
        } else {
            $this->service->resetAnswers($report, auth()->id());
        }
        return redirect()->route('monitoring-akreditasi.view', $report)->with('success', 'Jawaban berhasil direset. Laporan kembali menjadi draf.');
    }

    public function update(Request $request, string $report)
    {
        $report = $this->service->resolveReport($report);
        $this->authorizeReport($report); $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id);
        $data['answers'] = $this->lockIdentityAnswers($data['answers'] ?? [], $this->profile(), $report->jenis_pengajuan_id, $report->report_year);
        $report = $this->saveDraft($request, $report, $data['answers'], 'account');
        if ($request->expectsJson()) return response()->json(['ok' => true, 'edit_version' => $report->edit_version]);
        return back()->with('success', 'Draf monitoring berhasil disimpan.');
    }

    public function submit(Request $request, string $report)
    {
        $report = $this->service->resolveReport($report);
        $this->authorizeReport($report); $data = $this->validateReport($request, $report->report_year, $report->jenis_pengajuan_id, true, $report);
        $data['answers'] = $this->lockIdentityAnswers($data['answers'] ?? [], $this->profile(), $report->jenis_pengajuan_id, $report->report_year);
        $report = $this->submitReport($request, $report, $data['answers'], 'account');
        if ($request->expectsJson()) return response()->json(['ok' => true, 'message' => 'Laporan berhasil dikirim.', 'edit_version' => $report->edit_version]);
        return redirect()->route('monitoring-akreditasi.index')->with('success', 'Laporan monitoring berhasil dikirim.');
    }

    private function editableAnswers(MonitoringReport|Monev $report): array
    {
        $answers = $this->service->answersForReport($report);
        if ($answers !== null || $report instanceof Monev) {
            return $answers ?? [];
        }
        $version = $report->versions()->when($report->submitted_version, fn ($q) => $q->where('version', $report->submitted_version), fn ($q) => $q->latest('version'))->first()
            ?: $report->versions()->latest('version')->first();
        return $version?->answers ?? [];
    }

    private function saveDraft(Request $request, MonitoringReport|Monev $report, array $answers, string $channel): MonitoringReport|Monev
    {
        $revision = (int) $request->input('edit_version');
        return $report instanceof Monev
            ? $this->service->writeMonevDraft($report, $answers, $revision)
            : $this->service->saveDraft($report, $answers, $request->user()->id, $channel, $revision);
    }

    private function submitReport(Request $request, MonitoringReport|Monev $report, array $answers, string $channel): MonitoringReport|Monev
    {
        $revision = (int) $request->input('edit_version');
        return $report instanceof Monev
            ? $this->service->submitMonev($report, $answers, $report->profile, $request->user()->id, $channel, $revision)
            : $this->service->submit($report, $answers, $report->profile, $request->user()->id, $channel, $revision);
    }

    private function authorizeReport(MonitoringReport|Monev $report): void
    { if ($report->profile_id !== $this->profile()->id) abort(403); }

    private function validateReport(Request $request, ?int $fixedYear = null, ?int $fixedProgram = null, bool $submit = false, MonitoringReport|Monev|null $report = null): array
    {
        $rules = ['jenis_pengajuan_id' => 'required|integer', 'report_year' => 'required|integer|min:2020|max:' . now()->year, 'answers' => 'sometimes|array', 'answers.*' => 'nullable', 'edit_version' => 'nullable|integer|min:1'];
        $data = $request->validate($rules); $data['jenis_pengajuan_id'] = $fixedProgram ?? $data['jenis_pengajuan_id']; $data['report_year'] = $fixedYear ?? $data['report_year'];
        if ($submit) {
            $questions = $this->service->questionnaireQuestions($report);
            $answers = $data['answers'] ?? [];
            $rules = [];
            foreach (range(1, 26) as $question) $rules[$question] = 'required';
            $rules[6] = 'nullable|string|max:5000';
            $rules['has_training'] = 'required|in:Ya,Tidak';
            foreach ([10, 11] as $question) $rules[$question] = 'required|integer|min:0';
            $rules[12] = ['required', 'array'];
            $rules['12.internal'] = 'required|integer|min:0';
            $rules['12.external'] = 'required|integer|min:0';
            $singleChoice = [
                13 => ['Sesuai', 'Ada modifikasi minor', 'Tidak sesuai'],
                15 => ['Sangat memuaskan', 'Memuaskan', 'Cukup memuaskan', 'Tidak lulus'],
                16 => ['Sangat baik', 'Baik', 'Kurang baik', 'Tidak baik'],
                19 => ['Lengkap', 'Sebagian', 'Tidak ada'],
                20 => ['Sudah dilakukan', 'Belum dilakukan'],
                21 => ['Terpenuhi', 'Sebagian', 'Tidak terpenuhi'],
                22 => ['Sangat puas', 'Puas', 'Cukup', 'Kurang'],
                23 => ['Ada', 'Tidak ada'],
            ];
            foreach ($singleChoice as $question => $choices) {
                $rules[$question] = ['required', \Illuminate\Validation\Rule::in($questions->get($question)?->opsi ?? $choices)];
            }
            $multipleChoice = [
                14 => ['Tatap muka', 'Daring', 'Blended learning'],
                17 => ['Internal', 'Eksternal'],
                18 => ['Widyaiswara', 'Pejabat Struktural', 'Fungsional Lainnya'],
            ];
            foreach ($multipleChoice as $question => $choices) {
                $rules[$question] = 'required|array|min:1';
                $rules[$question . '.*'] = ['required', 'string', \Illuminate\Validation\Rule::in($questions->get($question)?->opsi ?? $choices)];
            }
            foreach ([24, 25, 26] as $question) $rules[$question] = 'required|string|max:5000';
            $rules['23_keterangan'] = 'nullable|required_if:23,Ada|string|max:5000';
            if (($answers['has_training'] ?? null) === 'Ya') {
                $rules[10] = 'required|integer|min:1';
            } else {
                foreach (range(10, 23) as $question) $rules[$question] = 'nullable';
                $rules['12.internal'] = 'nullable';
                $rules['12.external'] = 'nullable';
                foreach ([14, 17, 18] as $question) $rules[$question . '.*'] = 'nullable';
                $rules['23_keterangan'] = 'nullable';
            }
            $answerRules = [];
            foreach ($rules as $key => $rule) {
                $answerRules['answers.' . $key] = $rule;
            }
            validator(['answers' => $answers], $answerRules)->validate();
            if (($answers['has_training'] ?? null) === 'Ya' && (int) $answers[12]['internal'] + (int) $answers[12]['external'] !== (int) $answers[11]) {
                throw \Illuminate\Validation\ValidationException::withMessages(['answers.12' => 'Jumlah peserta internal dan eksternal jika dijumlahkan harus sama dengan jumlah peserta pada pertanyaan nomor 11.']);
            }
            $data['answers'] = $answers;
        }
        return $data;
    }
}
