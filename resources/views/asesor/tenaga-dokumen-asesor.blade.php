@extends('layouts.app-rekap')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold">
            <span class="text-muted fw-light">
                <a href="{{ redirect()->back()->getTargetUrl()}}">
                {{$step_name}} </a>/ </span>Bukti Dukung </h4>
        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            @foreach ($mtdocs as $mtdoc)
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">{{ $mtdoc->nama }}</h5>
                                        </div>
                                        <div class="table-responsive text-nowrap">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Dokumen</th>
                                                        <th>Tipe</th>
                                                        <th>Timestamp</th>
                                                        <th class="float-end">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">

                                                        @php
                                                            $counter = 1;
                                                            $dataFound = false;
                                                        @endphp
                                                        @foreach ($docs as $doc)
                                                            @if ($mtdoc->id == $doc->tenaga_dokumen_id)
                                                            @php $dataFound = true; @endphp
                                                                <tr>
                                                                    <td>{{ $counter++ }}</td>
                                                                    <td>{{ $doc->nama }}</td>
                                                                    <td>{{ $doc->tipe }} </td>
                                                                    <td>{{ $doc->updated_at->format('j-m-Y G:H:s') }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="btn-group float-end" role="button">
                                                                            <a role="button"
                                                                                href="{{ asset($doc->path_dokumen) }}"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                target="_blank"><i
                                                                                    class="bx bxs-show"></i></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @if(!$dataFound)
                                                        <tr>
                                                            <td colspan="5">
                                                                <p class="text-center"><span
                                                                        class="badge bg-label-secondary">dokumen
                                                                        pendukung
                                                                        belum ditambahkan</span></p>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col text-end">
                <a class="btn btn-primary" role="button" href="{{ redirect()->back()->getTargetUrl()}}"><i class="bx bxs-chevron-left"></i>Kembali</a>
            </div>
        </div>

    </div>
@endsection
