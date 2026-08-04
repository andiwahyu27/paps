@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a
                    href="{{ route('pengajuan', $pelatihan->pengajuan->id_jenis) }}">Akreditasi
                    Pelatihan/</a></span>{{ $pelatihan->fullname() }}</h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('program.akreditasi', [$pelatihan->id, 1]) }}"><button type="button"
                                    class="nav-link @if ($step == 1) active @endif">
                                    <i class="tf-icons bx bx-home"></i> Kurikulum Pelatihan
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('program.akreditasi', [$pelatihan->id, 2]) }}"><button type="button"
                                    class="nav-link @if ($step == 2) active @endif">
                                    <i class="tf-icons bx bx-customize"></i> Perencanaan dan Realisasi Penyelenggaraan
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('program.akreditasi', [$pelatihan->id, 3]) }}"><button type="button"
                                    class="nav-link @if ($step == 3) active @endif">
                                    <i class="tf-icons bx bx-book-reader"></i> Evaluasi Penyelenggaraan
                                </button></a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('program.akreditasi', [$pelatihan->id, 4]) }}"><button type="button"
                                    class="nav-link @if ($step == 4) active @endif">
                                    <i class="tf-icons bx bx-transfer-alt"></i> Hasil
                                </button></a>
                        </li> --}}
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            @switch($step)
                                @case(2)
                                    {{-- @include('lembaga.program._tahap2') --}}
                                @case(1)
                                @case(3)

                                @case(4)
                                    @foreach ($jenis_dokumen as $jd)
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="mb-0">{{ $jd->nama }}</h5>
                                                    @if ($pelatihan->pengajuan->profile->is_lock == 0)
                                                        <button type="button" class="btn btn-sm btn-info float-end"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="{{ '#addDokumenModal' . $jd->id }}">Tambah</button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-secondary float-end"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-custom-class="tooltip-info" data-bs-original-title="Tidak bisa menambahkan data. Silahkan hubungi Tim Sekretariat"
                                                            >Tambah</button>
                                                    @endif
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
                                                                @if ($jd->id === $doc->dokumen_id)
                                                                @php $dataFound = true; @endphp
                                                                    <tr>
                                                                        <td>{{ $counter++ }}</td>
                                                                        <td>{{ $doc->nama }}</td>
                                                                        <td>{{ $doc->tipe }} </td>
                                                                        <td>{{ $doc->updated_at->format('j-m-Y G:H:s') }}</td>
                                                                        <td>
                                                                            <div class="btn-group float-end" role="button">
                                                                                @if ($pelatihan->pengajuan->profile->is_lock == 0)
                                                                                    <a role="button"
                                                                                        href="{{ asset($doc->path_dokumen) }}"
                                                                                        class="btn btn-sm btn-outline-primary"
                                                                                        target="_blank"><i
                                                                                            class="bx bxs-show"></i></a>
                                                                                    <button type="button" id="doc"
                                                                                        class="btn btn-sm btn-outline-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="{{ '#editDokumenModal' . $doc->id }}"><i
                                                                                            class="bx bxs-pencil"></i></button>
                                                                                    <button type="button" id="doc"
                                                                                        class="btn btn-sm btn-outline-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="{{ '#hapusDokumenModal' . $doc->id }}"><i
                                                                                            class='bx bx-trash'></i></button>
                                                                                @else
                                                                                    <a role="button"
                                                                                        href="{{ asset($doc->path_dokumen) }}"
                                                                                        class="btn btn-sm btn-outline-primary"
                                                                                        target="_blank"><i
                                                                                            class="bx bxs-show"></i>Lihat data</a>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach

                                                            @if(!$dataFound)
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <p class="text-center"><span
                                                                                class="badge bg-label-secondary">Data
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
                                        <div class="modal fade" id="{{ 'addDokumenModal' . $jd->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('store.dokumen') }}" method="post"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ $jd->nama }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-12 mb-3">
                                                                    <label for="nama" class="form-label">Nama Dokumen</label>
                                                                    <input type="text" name="nama" id="nama"
                                                                        class="form-control" placeholder="Nama Dokumen" />
                                                                </div>
                                                                <div class="col-12 mb-3">
                                                                    <label for="file-dokumen" class="form-label">File
                                                                        Dokumen</label>
                                                                    <input type="file" name="file-dokumen" id="file-dokumen"
                                                                        class="form-control" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Tambah</button>
                                                            <input type="hidden" name="dokumen_id" id="dokumen_id"
                                                                value="{{ $jd->id }}">
                                                            <input type="hidden" name="id_pelatihan" id="id_pelatihan"
                                                                value="{{ $pelatihan->id }}">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @break

                            @endswitch
                            @include('lembaga.modal.program-modal')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pills -->
    </div>
@endsection
