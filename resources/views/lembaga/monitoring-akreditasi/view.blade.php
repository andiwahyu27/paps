@extends('layouts.app-lembaga')

@section('content')
@php
    $questions = [
        1 => 'Nama lembaga pelatihan', 2 => 'Program pelatihan yang terakreditasi', 3 => 'Alamat',
        4 => 'Nomor telepon', 5 => 'E-mail', 6 => 'Website', 7 => 'Nomor sertifikat akreditasi',
        8 => 'Status akreditasi', 9 => 'Tahun pemantauan', 10 => 'Jumlah pelatihan yang dilaksanakan pada tahun ini',
        11 => 'Jumlah peserta pelatihan pada tahun ini', 12 => 'Asal peserta pelatihan',
        13 => 'Kesesuaian pelaksanaan dengan program yang terakreditasi', 14 => 'Metode pelatihan yang digunakan',
        15 => 'Penilaian kelulusan peserta pelatihan', 16 => 'Penilaian penyelenggaraan pelatihan secara umum',
        17 => 'Tenaga pengajar yang digunakan', 18 => 'Jabatan tenaga pengajar',
        19 => 'Ketersediaan dokumen mutu pelatihan', 20 => 'Pemutakhiran dokumen akreditasi',
        21 => 'Kepatuhan terhadap persyaratan akreditasi', 22 => 'Evaluasi peserta terhadap pelatihan',
        23 => 'Tindak lanjut hasil pelatihan', 24 => 'Kendala atau permasalahan dalam penyelenggaraan pelatihan',
        25 => 'Rencana pelatihan tahun depan', 26 => 'Kebutuhan dukungan atau pembinaan dari instansi pembina/BPS',
    ];
    $hasValue = static function ($value): bool {
        if (is_array($value)) {
            return count(array_filter($value, static fn ($item) => $item !== null && $item !== '')) > 0;
        }

        return $value !== null && $value !== '' && (!is_string($value) || trim($value) !== '');
    };
    $hasTraining = data_get($answers, 'has_training');
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Lembaga / Monitoring Akreditasi PSTK /</span> Hasil Laporan</h4>
        <div>
            <a href="{{ route('monitoring-akreditasi.edit', $report) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i>Edit</a>
            <a href="{{ route('monitoring-akreditasi.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-4"><div class="card-body"><div class="row g-3">
        <div class="col-md-4"><small class="text-muted d-block">Lembaga</small><strong>{{ $profile->nama_lembaga }}</strong></div>
        <div class="col-md-4"><small class="text-muted d-block">Program</small><strong>{{ $accreditation?->jenisPengajuan?->nama ?? '-' }}</strong></div>
        <div class="col-md-2"><small class="text-muted d-block">Tahun</small><strong>{{ $report->report_year }}</strong></div>
        <div class="col-md-2"><small class="text-muted d-block">Status</small><span class="badge bg-{{ $report->status === 'submitted' ? 'success' : 'warning' }}">{{ $report->status === 'submitted' ? 'Terkirim' : 'Draf' }}</span></div>
    </div></div></div>
    <div class="card"><div class="card-header"><h5 class="mb-0">Jawaban Laporan Monitoring</h5></div><div class="card-body">
        <div class="alert alert-info">Halaman ini menampilkan hasil laporan dalam mode baca. Pilih Edit jika ada jawaban yang perlu diperbarui.</div>
        <div class="row g-3">
            @if ($hasValue($hasTraining))
                <div class="col-12"><div class="border rounded p-3"><strong>Apakah lembaga menyelenggarakan pelatihan setelah terakreditasi?</strong><div>{{ $hasTraining }}</div></div></div>
            @endif
            @foreach($questions as $number => $label)
                @php
                    $isTrainingQuestion = $number >= 10 && $number <= 23;
                    $internal = data_get($answers, '12.internal');
                    $external = data_get($answers, '12.external');
                    $value = $number === 12
                        ? 'Internal: '.($internal ?? '0').'; Eksternal: '.($external ?? '0')
                        : data_get($answers, (string) $number);
                    $value = is_array($value) ? implode(', ', $value) : $value;
                    if ($number === 9 && $hasValue($value)) {
                        $value = sprintf('%04d', (int) $value);
                    }
                    $questionHasValue = $number === 12
                        ? $hasValue($internal) || $hasValue($external)
                        : $hasValue($value);
                @endphp
                @if ((!$isTrainingQuestion || $hasTraining === 'Ya') && $questionHasValue)
                    <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Pertanyaan {{ $number }}</small><strong>{{ $label }}</strong><div class="mt-2">{!! nl2br(e($value)) !!}</div></div></div>
                @endif
            @endforeach
        </div>
    </div></div>
</div>
@endsection
