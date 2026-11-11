@extends('layouts.monitoring-public')

@section('content')
<div class="container-xxl py-4">
    <div class="card">
        <div class="card-body">
            <h1 class="h4">Pelaporan Monitoring Akreditasi PSTK {{ $period?->report_year }}</h1>
            @if (!$period)
                <p>Sekretariat belum menetapkan periode pelaporan.</p>
            @elseif ($state === 'upcoming')
                <p>Pelaporan dibuka pada {{ $period->opens_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
            @elseif ($state === 'closed')
                <p>Pelaporan ditutup pada {{ $period->closes_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
            @else
                <p>Pelaporan berlangsung hingga {{ $period->closes_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
            @endif
            @if (!empty($notice))
                <p class="mb-0">{{ $notice }}</p>
            @endif
            @isset($reportsUrl)
                <a href="{{ $reportsUrl }}" class="btn btn-outline-primary">Laporan Saya</a>
            @endisset
            @if (isset($programs) && $programs->isNotEmpty())
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Program</th><th>Status Laporan</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($programs as $program)
                                <tr>
                                    <td>{{ $program['name'] }}</td>
                                    <td>{{ $program['status'] }}</td>
                                    <td>
                                        @if ($program['url'])
                                            <a href="{{ $program['url'] }}" class="btn btn-sm btn-outline-primary">{{ $program['label'] }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
