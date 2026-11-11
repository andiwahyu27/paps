<?php

namespace App\Http\Controllers;

use App\Models\MonitoringReport;
use App\Models\MonitoringAccreditation;
use App\Models\MonitoringReportLink;
use App\Models\MonitoringReportingPeriod;
use App\Models\Profile;
use App\Models\Monev;
use App\Services\MonitoringReportLinkService;
use App\Services\MonitoringReportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringSekretariatController extends Controller
{
    private $monitoring;

    public function __construct(MonitoringReportService $monitoring)
    {
        $this->monitoring = $monitoring;
        $this->middleware(['auth', 'is.sekretariat']);
    }

    public function index(Request $request)
    {
        $year = (int) ($request->input('year') ?: now()->year);
        $normalized = config('monev.use_normalized_reports');
        $reports = $this->filteredReports($request)->with([
            'profile', 'jenisPengajuan', $normalized ? 'latestSubmission' : 'versions',
            'links' => fn ($query) => $query->latest('id'),
        ])->get();
        $stats = [
            'total' => $reports->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'draft' => $reports->whereIn('status', ['draft', 'revision_draft'])->count(),
            'nihil' => $reports->filter(function ($report) {
                $answers = $this->lastSubmission($report)?->answers;
                return data_get($answers, 'has_training') === 'Tidak' || (int) data_get($answers, '10', -1) === 0;
            })->count(),
        ];
        $accreditations = MonitoringAccreditation::with(['jenisPengajuan', 'profile'])
            ->whereDate('valid_from', '<=', $year . '-12-31')->whereDate('valid_until', '>=', $year . '-01-01')
            ->when($request->program, fn ($query, $program) => $query->where('jenis_pengajuan_id', $program))
            ->when($request->search, fn ($query, $name) => $query->whereHas('profile', fn ($profile) => $profile->where('nama_lembaga', 'like', "%{$name}%")))
            ->get();
        foreach ($reports as $report) {
            $link = $report->links->first(fn ($item) => !$item->deleted_at);
            if ($link && $link->expires_at->gt(now())) $link->public_url = route('monitoring-akreditasi.public.form', app(\App\Services\MonitoringReportLinkService::class)->decrypt($link));
        }
        $periods = MonitoringReportingPeriod::orderByDesc('report_year')->get();
        $globalPeriod = MonitoringReportingPeriod::global();
        $editingPeriod = $periods->firstWhere('report_year', $request->query('periode'));

        return view('sekretariat.monitoring-evaluasi', compact('reports', 'stats', 'accreditations', 'year', 'periods', 'globalPeriod', 'editingPeriod'));
    }

    public function savePeriod(Request $request)
    {
        $data = $request->validate([
            'report_year' => 'required|integer|min:2020|max:9999',
            'opens_at' => 'required|date_format:Y-m-d\TH:i',
            'closes_at' => 'required|date_format:Y-m-d\TH:i|after:opens_at',
            'activate' => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($data) {
            MonitoringReportingPeriod::settings()->lockForUpdate()->first();
            $period = MonitoringReportingPeriod::updateOrCreate(['report_year' => $data['report_year']], [
                'opens_at' => CarbonImmutable::parse($data['opens_at'], 'Asia/Jakarta')->utc()->toDateTimeString(),
                'closes_at' => CarbonImmutable::parse($data['closes_at'], 'Asia/Jakarta')->utc()->toDateTimeString(),
            ]);
            if ($data['activate'] ?? false) {
                MonitoringReportingPeriod::settings()->update(['period_id' => $period->id]);
            }
        }, 3);

        return redirect()->route('monitoring-evaluasi')->with('success', 'Periode pelaporan berhasil disimpan.');
    }

    private function filteredReports(Request $request)
    {
        $model = config('monev.use_normalized_reports') ? Monev::class : MonitoringReport::class;
        return $model::query()
            ->when($request->year, fn ($q, $v) => $q->where('report_year', $v))
            ->when($request->program, fn ($q, $v) => $q->where('jenis_pengajuan_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->search, fn ($q, $v) => $q->whereHas('profile', fn ($p) => $p->where('nama_lembaga', 'like', "%{$v}%")))
            ->latest('report_year');
    }

    public function exportFiltered(Request $request)
    {
        $reports = $this->filteredReports($request)->with(['profile', 'jenisPengajuan'])->get();
        return $this->downloadReports($reports, 'monitoring-akreditasi-filter.csv');
    }

    public function generateLink(int $profile, int $program, int $year, Request $request, MonitoringReportLinkService $links)
    {
        $data = $request->validate(['expires_at' => 'nullable|date']);
        $this->authorizeAccreditation($profile, $program, $year);
        $issued = DB::transaction(function () use ($profile, $program, $year, $links, $data) {
            $report = $this->monitoring->reportForLink($profile, $program, $year);
            return $links->issue($report, auth()->id(), $data['expires_at'] ?? null);
        }, 3);
        return back()->with('generated_link', route('monitoring-akreditasi.public.form', $issued['token']))->with('success', 'Link monitoring berhasil dibuat.');
    }

    public function regenerateLink(string $report, Request $request, MonitoringReportLinkService $links)
    {
        $data = $request->validate(['expires_at' => 'nullable|date']);
        $issued = $links->issue($this->monitoring->resolveReport($report), auth()->id(), $data['expires_at'] ?? null);
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
        abort_if($year < 2020 || $year > 9999 || ($year > now()->year && !$this->monitoring->periodForYear($year)), 422);
        abort_unless(MonitoringAccreditation::where('profile_id', $profileId)->where('jenis_pengajuan_id', $programId)
            ->whereDate('valid_from', '<=', $year . '-12-31')->whereDate('valid_until', '>=', $year . '-01-01')->exists(), 404);
    }

    public function show(string $report)
    {
        $report = $this->monitoring->resolveReport($report);
        $report->load(['profile', 'jenisPengajuan', $report instanceof Monev ? 'latestSubmission' : 'versions']);
        $submission = $this->lastSubmission($report);
        $questions = $this->reportQuestions($report);
        $answers = $this->reportAnswers($report);
        if ($report instanceof MonitoringReport && $submission) {
            $answers += $submission->answers ?? [];
        }
        return view('sekretariat.monitoring-akreditasi-detail', compact('report', 'submission', 'questions', 'answers'));
    }

    public function export(string $report)
    {
        $report = $this->monitoring->resolveReport($report);
        $report->load(['profile', 'jenisPengajuan']);
        return $this->downloadReports([$report], 'monitoring-akreditasi-' . $report->uuid . '.csv');
    }

    private function lastSubmission(MonitoringReport|Monev $report)
    {
        return $report instanceof Monev
            ? $report->latestSubmission
            : $report->versions->firstWhere('version', $report->submitted_version);
    }

    private function reportQuestions(MonitoringReport|Monev $report)
    {
        $questions = $this->monitoring->questionnaireQuestions($report);
        return $questions->isNotEmpty() ? $questions
            : collect(range(1, 26))->map(fn ($number) => (object) ['kode' => (string) $number]);
    }

    private function reportAnswers(MonitoringReport|Monev $report): array
    {
        $answers = $this->monitoring->answersForReport($report) ?? [];
        if ($report instanceof MonitoringReport) return $answers;

        $identity = $report->identity_snapshot;
        $accreditation = $report->accreditation_snapshot;
        return $answers + [
            1 => data_get($identity, 'nama_lembaga', $report->profile?->nama_lembaga),
            2 => data_get($identity, 'nama_program', $report->jenisPengajuan?->nama),
            3 => data_get($identity, 'alamat_lembaga', data_get($identity, 'alamat')),
            4 => data_get($identity, 'telepon'),
            5 => data_get($identity, 'email'),
            6 => data_get($identity, 'website'),
            7 => data_get($accreditation, 'certificate_number'),
            8 => data_get($accreditation, 'predicate'),
            9 => $report->report_year,
        ];
    }

    private function downloadReports(iterable $reports, string $filename)
    {
        return response()->streamDownload(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Lembaga', 'Program', 'Tahun', 'Status', 'Pertanyaan', 'Jawaban']);
            foreach ($reports as $report) {
                $answers = $this->reportAnswers($report);
                foreach ($this->reportQuestions($report) as $question) {
                    $value = data_get($answers, $this->monitoring->answerKey($question->kode));
                    fputcsv($handle, [
                        $this->safeCsvValue($report->profile->nama_lembaga ?? ''),
                        $this->safeCsvValue($report->jenisPengajuan->nama ?? ''),
                        $report->report_year, $report->status, $question->kode,
                        $this->safeCsvValue(is_array($value) ? implode(', ', $value) : ($value ?? '')),
                    ]);
                }
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function safeCsvValue($value): string
    {
        $value = (string) $value;
        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }
}
