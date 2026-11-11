@php
    $monitoring = app(\App\Services\MonitoringReportService::class);
    $filled = static fn ($value) => is_scalar($value) && trim((string) $value) !== '';
    $withoutTraining = data_get($answers, 'has_training') === 'Tidak';
@endphp
<div class="card">
    <div class="card-header"><h5 class="mb-0">Jawaban Laporan Monitoring</h5></div>
    <div class="card-body">
        <dl class="row mb-3">
            <div class="col-md-6">
                <dt>Jenis Kuesioner</dt>
                <dd>{{ $questionnaire?->jenis ?? '—' }}</dd>
            </div>
            <div class="col-md-6">
                <dt>Versi Kuesioner</dt>
                <dd>{{ $questionnaire?->versi ?? '—' }}</dd>
            </div>
        </dl>
        <div class="row g-3">
            @foreach ($questions as $question)
                @php
                    $code = $question->kode;
                    $number = (int) $code;
                    $value = data_get($answers, $monitoring->answerKey($code));
                    $value = is_array($value) ? implode(', ', array_filter($value, $filled)) : $value;
                @endphp
                @continue(($withoutTraining && $number >= 10 && $number <= 23) || !$filled($value))
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <strong>{{ $question->pertanyaan ?? 'Pertanyaan ' . $code }}</strong>
                        <div class="mt-2 text-break">{!! nl2br(e($code === '9' ? sprintf('%04d', (int) $value) : $value)) !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
