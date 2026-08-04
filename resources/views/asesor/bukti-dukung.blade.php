@extends('layouts.app-rekap')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @foreach ($result as $r)
            <h4 class="fw-bold"><span class="text-muted fw-light"></span>{{ $r['pelatihan'] }}</h4>
            @php $found = false; @endphp
            @foreach ($r['data'] as $d)
                @php $found = true; @endphp
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">{{ $d['title'] }}</h5>
                        @switch($d['type'])
                            @case('paragraph')
                                <p class="card-text">{!! $d['text'] !!}</p>
                            @break

                            @case('tabel_tenaga')
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Jabatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @php $dataFound = false; @endphp
                                            @foreach ($d['text'] as $tenaga)
                                                @php $dataFound = true; @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $tenaga->nama }}</td>
                                                    <td>{{ $tenaga->nip }}</td>
                                                    <td>{{ $tenaga->jabatan }}</td>
                                                    <td>
                                                        <div class="demo-inline-spacing">
                                                            <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                                                data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                                                data_nama="{{ $tenaga->nama }}"
                                                                data-bs-target="#showJabatanModal{{ $tenaga->id }}"
                                                                href="javascript:void(0);"><i class="bx bx-user-pin"></i> Riwayat
                                                                Jabatan</button>
                                                            <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                                                data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                                                data_nama="{{ $tenaga->nama }}"
                                                                data-bs-target="#showKerjaModal{{ $tenaga->id }}"
                                                                href="javascript:void(0);"><i class="bx bx-briefcase-alt"></i>
                                                                Pengalaman Kerja</button>
                                                            <a href="{{ route('dokumen.tenaga.bukti', ['id' => $tenaga->id, 'step' => $step]) }}"
                                                                type="button" class="btn btn-success btn-sm rounded-pill mdl"
                                                                data_id="{{ $tenaga->id }}"><i class='bx bxs-cloud-upload'></i>
                                                                Bukti
                                                                Dukung</a>
                                                        </div>
                                                        <div class="demo-inline-spacing">
                                                            <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                                                data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                                                data_nama="{{ $tenaga->nama }}"
                                                                data-bs-target="#showPendidikanModal{{ $tenaga->id }}"
                                                                href="javascript:void(0);"><i class='bx bxs-graduation'></i> Riwayat
                                                                Pendidikan</button>
                                                            <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                                                data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                                                data_nama="{{ $tenaga->nama }}"
                                                                data-bs-target="#showPelatihanModal{{ $tenaga->id }}"
                                                                href="javascript:void(0);"><i class='bx bx-news'></i> Riwayat
                                                                Pelatihan</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if (!$dataFound)
                                                <tr>
                                                    <td colspan="5">
                                                        <p class="text-center">
                                                            <span class="badge bg-label-secondary">Data belum ditambahkan</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                @foreach ($d['text'] as $tenaga)
                                    @include('asesor.modal.tenaga-modal')
                                @endforeach
                            @break

                            @case('tabel_sarpras')
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                                <th>Foto</th>
                                                <th>Keterangan</th>
                                                <th>Dokumen Pendukung</th>
                                                <th>Dokumen Pemeliharaan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @foreach ($d['text'] as $f)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $f->nama }}</td>
                                                    <td>{{ $f->jumlah }}</td>
                                                    <td>{{ $f->status }}
                                                        <a href="#">
                                                            <i class="bx bxs-info-circle" data-bs-toggle="tooltip"
                                                                data-bs-offset="0,4" data-bs-placement="top"
                                                                data-bs-html="true"
                                                                data-bs-original-title="<span>
                                                                @if ($f->status > 1)
                                                                Sewa / Kontrak
                                                                @else
                                                                Milik Sendiri
                                                                @endif
                                                            </span>"></i>
                                                        </a>
                                                    </td>
                                                    <td><a href="{{ asset($f->path_foto) }}" target="_blank"><img
                                                                src="{{ asset($f->path_foto) }}" alt
                                                                class="w-px-40 h-auto rounded-circle" /></a></td>
                                                    <td>{{ $f->keterangan }}</td>
                                                    <td>
                                                        @if ($f->path_dokumen)
                                                            <a class="btn btn-primary btn-sm rounded-pill"
                                                                href="{{ asset($f->path_dokumen) }}" target="_blank"><i
                                                                    class="bx bxs-file-pdf"></i> Lihat File</a>
                                                            @else
                                                                -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($f->path_pemeliharaan)
                                                            <a class="btn btn-primary btn-sm rounded-pill"
                                                                href="{{ asset($f->path_pemeliharaan) }}" target="_blank"><i
                                                                    class="bx bxs-file-pdf"></i> Lihat File</a>
                                                            @else
                                                                -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @break

                            @case('program')
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
                                            @foreach ($d['text'] as $doc)
                                                @php $dataFound = true; @endphp
                                                <tr>
                                                    <td>{{ $counter++ }}</td>
                                                    <td>{{ $doc->nama }}</td>
                                                    <td>{{ $doc->tipe }} </td>
                                                    <td>{{ $doc->updated_at->format('j-m-Y G:H:s') }}</td>
                                                    <td>
                                                        <div class="btn-group float-end" role="button">
                                                            <a role="button" href="{{ asset($doc->path_dokumen) }}"
                                                                class="btn btn-sm btn-outline-primary" target="_blank"><i
                                                                    class="bx bxs-show"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if (!$dataFound)
                                                <tr>
                                                    <td colspan="5">
                                                        <p class="text-center"><span class="badge bg-label-secondary">Data belum
                                                                ditambahkan</span></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @break
                        @endswitch
                    </div>
                </div>
            @endforeach
            @if (!$found)
                <div class="card mb-2">
                    <div class="card-body">
                        <p class="text-center"><span class="badge bg-label-secondary">Data belum ditambahkan</span></p>
                    </div>
                </div>
            @endif
        @endforeach
        <div class="row">
            <div class="col text-end">
                <a class="btn btn-primary" role="button" href="{{ route('pravisit', $pengajuan->id) }}"><i
                        class="bx bxs-chevron-left"></i>Kembali</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var urlGetModal = '{{ route('bd.tenaga.modal') }}';
        $(".mdl").click(function() {
            var idtenaga = $(this).attr("data_id");
            var nmtenaga = $(this).attr("data_nama");
            $('#nmtenaga').html(nmtenaga);
            $('#idtenaga').val(idtenaga);

            $.ajax({
                url: urlGetModal,
                type: "POST",
                data: {
                    idtenaga: idtenaga
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    console.log(result);
                    if (JSON.stringify(result) === '{}') {
                        $("[name=r_jabatans]").val([]);
                    } else {
                        $("[name=r_jabatans]").val([result]);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endpush
