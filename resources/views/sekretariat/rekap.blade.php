@extends('layouts.app-rekap')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold"><span class="text-muted fw-light"></span>{{ $pelatihan->fullname() }}</h4>
        {{-- Daftar Pengajar --}}
        {{-- <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Fasilitator Bertugas</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($tenaga as $t)
                                        @if ($t->jenis_tenaga == 1)
                                            <tr>
                                                <td>{{ $counter++ }}</td>
                                                <td>{{ $t->tenaga->nama }}
                                                <td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> --}}

        {{-- Daftar Pengelola Kelas --}}
        {{-- <div class="row">
                <div class="col-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Daftar Pengelola Kelas</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $counter = 1
                                @endphp
                                @foreach ($tenaga as $t)
                                    @if ($t->jenis_tenaga == 3)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $t->tenaga->nama }}<td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}
        <div class="card card-action mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Profile Lembaga</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kolom</th>
                                        <th>Uraian</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                        $counter = 1;
                                        $dataFound = false;

                                        // Mapping nama kolom ke relasi
                                        $wilayahFields = [
                                            'provinsi' => 'provinsiWilayah',
                                            'kabupaten_kota' => 'kabupatenWilayah',
                                        ];
                                    @endphp
                                    @foreach ($filteredColumns as $f)
                                        @php $dataFound = true; @endphp
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ ucwords(str_replace('_', ' ', str_replace('path', 'File', $f))) }}</td>
                                            <td>
                                                @php
                                                    $value = $profile[$f] ?? null;
                                                @endphp

                                                {{-- Cek apakah field adalah relasi wilayah --}}
                                                @if (array_key_exists($f, $wilayahFields))
                                                    {{ optional($profile->{$wilayahFields[$f]})->nama ?? 'Wilayah tidak ditemukan' }}

                                                    {{-- Cek apakah value adalah file --}}
                                                @elseif (is_string($value) && (str_contains($value, 'dokumen_profile/') ))
                                                    <a href="{{ asset($value) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary"><i class="bx bxs-show"></i>
                                                        Lihat File
                                                    </a>

                                                    {{-- Default: tampilkan tipe data --}}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if (!$dataFound)
                                        <tr>
                                            <td colspan="3">
                                                <p class="text-center">
                                                    <span class="badge bg-label-secondary">Data belum ditambahkan</span>
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($jenis_dokumen as $jd)
            <div class="card card-action mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">{{ $jd->nama }}</h5>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Dokumen</th>
                                            <th class="float-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @php
                                            $counter = 1;
                                            $dataFound = false;
                                        @endphp
                                        @foreach ($docs as $doc)
                                            @if ($jd->id === $doc->dokumen_id)
                                                @php $dataFound = true; @endphp
                                                <tr>
                                                    <td>{{ $counter++ }}</td>
                                                    <td>{{ $doc->nama }}</td>
                                                    <td>
                                                        <div class="btn-group float-end" role="button">
                                                            <a role="button" href="{{ asset($doc->path_dokumen) }}"
                                                                class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="bx bxs-show"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        @if (!$dataFound)
                                            <tr>
                                                <td colspan="3">
                                                    <p class="text-center">
                                                        <span class="badge bg-label-secondary">Data belum ditambahkan</span>
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
