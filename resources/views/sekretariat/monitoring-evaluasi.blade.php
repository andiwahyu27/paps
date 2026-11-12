@extends('layouts.app-sekretariat')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @include('sekretariat.monitoring-reporting-periods')
    <div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Sekretariat /</span> Monitoring & Evaluasi</h4><a href="{{ route('monitoring-evaluasi') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i> Muat Ulang</a></div>
    <div class="card mb-4"><div class="card-body"><form class="row g-3"><div class="col-md-3"><label class="form-label">Tahun</label><select name="year" class="form-select"><option value="">Semua Tahun</option>@foreach($reportYears as $reportYear)<option value="{{ $reportYear }}" {{ request('year') == $reportYear ? 'selected' : '' }}>{{ $reportYear }}</option>@endforeach</select></div><div class="col-md-3"><label class="form-label">Status Pelaporan</label><select name="status" class="form-select"><option value="">Semua Status</option><option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Terkirim</option><option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf</option><option value="revision_draft" {{ request('status') === 'revision_draft' ? 'selected' : '' }}>Draf Revisi</option></select></div><div class="col-md-4"><label class="form-label">Cari Lembaga</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Masukkan nama lembaga..."></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100"><i class="bx bx-filter-alt"></i> Filter</button></div></form></div></div>
    <div class="row mb-4">@foreach([['total','Total Laporan','bx-file','primary'],['submitted','Sudah Dikirim','bx-check-circle','success'],['draft','Masih Draf','bx-edit','warning'],['training','Pelatihan Terselenggara','bx-calendar-check','info']] as [$key,$label,$icon,$color])<div class="col-lg-3 col-md-6 col-12 mb-4"><div class="card"><div class="card-body"><div class="avatar flex-shrink-0"><i class="bx {{ $icon }} text-{{ $color }}" style="font-size:2rem"></i></div><span class="fw-semibold d-block mb-1">{{ $label }}</span><h3 class="card-title mb-2">{{ $stats[$key] ?? 0 }}</h3><small class="text-{{ $color }} fw-semibold">{{ $key === 'training' ? 'Pelatihan' : 'Laporan' }}</small></div></div></div>@endforeach</div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('generated_link'))<div class="alert alert-info">Link form: <a href="{{ session('generated_link') }}">{{ session('generated_link') }}</a> <button class="btn btn-sm btn-outline-primary" type="button" onclick="navigator.clipboard.writeText(@js(session('generated_link')))">Salin Link</button></div>@endif
    <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Data Monitoring Akreditasi PSTK</h5><div class="d-flex gap-2"><a href="{{ route('monitoring-evaluasi.export-filtered', request()->only(['year', 'program', 'status', 'search'])) }}" class="btn btn-outline-primary btn-sm"><i class="bx bx-download"></i> Export CSV</a><button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> Print</button></div></div><div class="card-body"><div class="table-responsive"><table id="monitoringReportsTable" class="table table-striped"><thead><tr><th>No</th><th>Nama Lembaga</th><th>Program</th><th>Tahun</th><th>Status Laporan</th><th class="text-center">Pelatihan Terselenggara</th><th>Status Link</th><th>Kedaluwarsa (WIB)</th><th>Aksi</th></tr></thead><tbody>
    @php($rowNumber = 0)
    @foreach($accreditations as $accreditation)
        @php($report = $reports->first(fn($r) => $r->profile_id === $accreditation->profile_id && $r->jenis_pengajuan_id === $accreditation->jenis_pengajuan_id && $r->report_year === $year))
        @continue(request()->filled('status') && !$report)
        @php($link = $report?->links->first())
        @php($rowNumber++)

        <tr>
            <td>{{ $rowNumber }}</td>
            <td>{{ $accreditation->profile->nama_lembaga ?? '-' }}</td>
            <td>{{ $accreditation->jenisPengajuan->nama ?? '-' }}</td>
            <td>{{ $year }}</td>
            <td>
                @if (!$report)
                    <span class="badge bg-secondary">Belum dibuat</span>
                @else
                    @include('monitoring-akreditasi.report-status', ['status' => $report->status])
                @endif
            </td>
            <td class="text-center">{{ $trainingCounts->get($report?->id) ?? '—' }}</td>
            <td>@include('sekretariat.monitoring-link-controls')</td>
            <td>
                <div class="d-flex flex-column align-items-start gap-1">
                    <span>{{ $link?->expires_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '—' }}</span>
                    @if ($link && !$link->deleted_at)
                        <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal"
                            data-bs-target="#expiry-{{ $link->id }}">Ubah</button>
                    @endif
                </div>
            </td>
            <td>
                @if ($report)
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('monitoring-evaluasi.detail', $report) }}">Detail</a>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">Lainnya</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @include('sekretariat.monitoring-report-delete')
                            </div>
                        </div>
                    </div>
                @else
                    —
                @endif
            </td>
        </tr>
        @push('monitoring-link-modals')
            @include('sekretariat.monitoring-link-modals')
        @endpush
    @endforeach
    @foreach ($reports as $report)
        @if ($report->report_year !== $year || !$accreditations->contains(fn ($accreditation) =>
            $accreditation->profile_id === $report->profile_id && $accreditation->jenis_pengajuan_id === $report->jenis_pengajuan_id))
            @php($historicalLink = $report->links->first())
            @php($rowNumber++)
            <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ $report->profile->nama_lembaga ?? '-' }}</td>
                <td>{{ $report->jenisPengajuan->nama ?? '-' }}</td>
                <td>{{ $report->report_year }}</td>
                <td>@include('monitoring-akreditasi.report-status', ['status' => $report->status])</td>
                <td class="text-center">{{ $trainingCounts->get($report->id) ?? '—' }}</td>
                <td>
                    @if ($historicalLink && !$historicalLink->deleted_at && $historicalLink->expires_at?->gt(now()))
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Tidak Aktif</span>
                    @endif
                </td>
                <td>{{ $historicalLink?->expires_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '—' }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('monitoring-evaluasi.detail', $report) }}">Detail</a>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">Lainnya</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @include('sekretariat.monitoring-report-delete')
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endif
    @endforeach
    </tbody></table></div></div></div>
    @stack('monitoring-link-modals')
    @foreach ($reports as $report)
        @include('sekretariat.monitoring-report-delete', ['modalOnly' => true])
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    const monitoringReportsTable = new DataTable('#monitoringReportsTable', {
        pageLength: 10,
        lengthMenu: [10, 25, 50],
        ordering: false,
        searching: false,
        autoWidth: false,
        language: {
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ baris',
            infoEmpty: 'Tidak ada laporan',
            emptyTable: 'Tidak ada laporan untuk ditampilkan',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Selanjutnya'
            }
        }
    });

    let pageBeforePrint = null;
    window.addEventListener('beforeprint', () => {
        if (pageBeforePrint !== null) return;
        pageBeforePrint = {
            length: monitoringReportsTable.page.len(),
            page: monitoringReportsTable.page()
        };
        monitoringReportsTable.page.len(-1).draw();
    });
    window.addEventListener('afterprint', () => {
        if (pageBeforePrint === null) return;
        monitoringReportsTable.page.len(pageBeforePrint.length).page(pageBeforePrint.page).draw('page');
        pageBeforePrint = null;
    });
</script>
@endpush
