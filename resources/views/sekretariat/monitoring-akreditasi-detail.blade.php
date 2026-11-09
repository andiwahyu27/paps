@extends('layouts.app-sekretariat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Monitoring &amp; Evaluasi /</span> Detail</h4>
        <a href="{{ route('monitoring-evaluasi') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Kembali</a>
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
                    <p>{{ $report->submitted_version ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    @php
        $version = $report->versions->firstWhere('version', $report->submitted_version);
        $identity = $version->identity_snapshot ?? [];
        $accreditation = $version->accreditation_snapshot ?? [];
        $answers = $version->answers ?? [];
        $identityAnswers = [
            1 => data_get($identity, 'nama_lembaga', $report->profile->nama_lembaga ?? null),
            2 => data_get($identity, 'nama_program', data_get($accreditation, 'nama_program', $report->jenisPengajuan->nama ?? null)),
            3 => data_get($identity, 'alamat_lembaga', data_get($identity, 'alamat', null)),
            4 => data_get($identity, 'telepon', null),
            5 => data_get($identity, 'email', null),
            6 => data_get($identity, 'website', null),
            7 => data_get($accreditation, 'certificate_number', null),
            8 => data_get($accreditation, 'predicate', null),
            9 => $report->report_year,
        ];
    @endphp

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Jawaban Laporan</h5></div>
        <div class="card-body">
            @foreach(range(1, 26) as $number)
                @php
                    $answer = $number <= 9 ? ($identityAnswers[$number] ?? '-') : data_get($answers, (string) $number, '-');
                    $displayAnswer = is_array($answer) ? implode(', ', $answer) : $answer;
                @endphp
                <div class="border-bottom py-2">
                    <span class="fw-semibold">Pertanyaan {{ $number }}</span>
                    <div>{{ is_scalar($displayAnswer) ? $displayAnswer : '-' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
