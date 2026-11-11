@extends('layouts.monitoring-public')

@section('content')
<div class="container-xxl py-4">
    <div class="card">
        <div class="card-body">
            <h1 class="h4">Pelaporan Monitoring Akreditasi PSTK {{ $period?->report_year }}</h1>
            @if (!$period)
                <p class="mb-0">Sekretariat belum menetapkan periode pelaporan.</p>
            @elseif ($state === 'upcoming')
                <p class="mb-0">Pelaporan dibuka pada {{ $period->opens_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
            @elseif ($state === 'closed')
                <p class="mb-0">Pelaporan ditutup pada {{ $period->closes_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
            @else
                <p class="mb-0">Pelaporan berlangsung hingga {{ $period->closes_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</p>
            @endif
        </div>
    </div>
</div>
@endsection
