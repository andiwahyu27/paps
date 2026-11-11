@extends('layouts.app-lembaga')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Lembaga / Monitoring Akreditasi PSTK /</span> Hasil Laporan</h4>
        <div>
            @if ($canEdit)
                <a href="{{ route('monitoring-akreditasi.edit', $report) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i>Edit</a>
            @endif
            <a href="{{ route('monitoring-akreditasi.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-4"><div class="card-body"><div class="row g-3">
        <div class="col-md-4"><small class="text-muted d-block">Lembaga</small><strong>{{ $profile->nama_lembaga }}</strong></div>
        <div class="col-md-4"><small class="text-muted d-block">Program</small><strong>{{ $accreditation?->jenisPengajuan?->nama ?? $report->jenisPengajuan?->nama ?? '-' }}</strong></div>
        <div class="col-md-2"><small class="text-muted d-block">Tahun</small><strong>{{ $report->report_year }}</strong></div>
        <div class="col-md-2"><small class="text-muted d-block">Status</small>@include('monitoring-akreditasi.report-status', ['status' => $displayStatus])</div>
    </div></div></div>
    @include('monitoring-akreditasi.report-summary')
</div>
@endsection
