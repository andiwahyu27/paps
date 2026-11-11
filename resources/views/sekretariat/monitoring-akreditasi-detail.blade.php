@extends('layouts.app-sekretariat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Monitoring &amp; Evaluasi /</span> Detail</h4>
        <div><a href="{{ route('monitoring-evaluasi.export', $report) }}" class="btn btn-outline-success me-2"><i class="bx bx-download"></i> Ekspor CSV</a><a href="{{ route('monitoring-evaluasi') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Kembali</a></div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Lembaga</strong>
                    <p>{{ $report->profile->nama_lembaga ?? '-' }}</p>
                    <strong>Program / Tahun</strong>
                    <p>{{ $report->jenisPengajuan->nama ?? '-' }} / {{ $report->report_year }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Status</strong>
                    <p><span class="badge bg-success">{{ $report->status }}</span></p>
                    <strong>Versi terkirim</strong>
                    <p>{{ $submission?->version ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Jawaban Laporan</h5></div>
        <div class="card-body">
            @foreach($questions as $question)
                @php
                    $answer = data_get($answers, app(\App\Services\MonitoringReportService::class)->answerKey($question->kode));
                    $displayAnswer = is_array($answer) ? implode(', ', $answer) : ($answer ?? '-');
                @endphp
                <div class="border-bottom py-2">
                    <span class="fw-semibold">{{ $question->pertanyaan ?? 'Pertanyaan ' . $question->kode }}</span>
                    <div>{{ is_scalar($displayAnswer) ? $displayAnswer : '-' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
