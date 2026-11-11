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
                    <p>@include('monitoring-akreditasi.report-status', ['status' => $report->status])</p>
                    <strong>Waktu Kirim Terakhir</strong>
                    <p>
                        {{ $submittedAt ? $submittedAt->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') . ' WIB' : 'Belum dikirim' }}
                        @if ($submission?->is_late)
                            <span class="badge bg-warning">Terlambat</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('monitoring-akreditasi.report-summary')
</div>
@endsection
