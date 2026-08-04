@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Unsur Kelembagaan /</span> {{ $step_name }}</h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('profile.fasilitas', 1) }}"><button type="button"
                                    class="nav-link @if ($step == 1) active @endif">
                                    <i class="tf-icons bx bx-home"></i> Sarpras Umum
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.fasilitas', 2) }}"><button type="button"
                                    class="nav-link @if ($step == 2) active @endif">
                                    <i class="tf-icons bx bx-customize"></i> Sarpras Pelatihan
                                </button></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Fasilitas LPPSK</h5>
                                        @if ($profile->is_lock == 0)
                                            <button type="button" class="btn btn-sm btn-info float-end"
                                                data-bs-toggle="modal" data-bs-target="#fasilitasModal">Tambah</button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-info float-end"
                                                disabled>Tambah</button>
                                        @endif
                                    </div>
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
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                @php $dataFound = false; @endphp
                                                @foreach ($fasilitas as $f)
                                                @php $dataFound = true; @endphp
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
                                                        <td>
                                                            @if ($profile->is_lock == 0)
                                                                <button type="button"
                                                                    class="btn btn-warning btn-sm rounded-pill"
                                                                    data-bs-toggle="modal" data_id="{{ $f->id }}"
                                                                    data-bs-target="#editFasilitas{{ $f->id }}"
                                                                    href="javascript:void(0);"><i class="bx bx-pencil"></i>
                                                                    Edit</button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm rounded-pill"
                                                                    data-bs-toggle="modal" data_id="{{ $f->id }}"
                                                                    data-bs-target="#deleteFasilitas{{ $f->id }}"
                                                                    href="javascript:void(0);"><i class="bx bx-trash"></i>
                                                                    Hapus</button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn btn-warning btn-sm rounded-pill" disabled><i
                                                                        class="bx bx-pencil"></i> Edit</button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm rounded-pill" disabled><i
                                                                        class="bx bx-trash"></i> Hapus</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="editFasilitas{{ $f->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form method="POST" action="{{ route('ubah.fasilitas') }}"
                                                                    enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf
                                                                    <input type="text" value="{{ $f->id }}"
                                                                        name="id" hidden />
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit Fasilitas</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row mb-3">
                                                                            <div class="col">
                                                                                <label for="nama-fasilitas"
                                                                                    class="form-label">Nama</label>
                                                                                <input type="text" id="nama-fasilitas"
                                                                                    class="form-control" name="nama"
                                                                                    value="{{ $f->nama }}"
                                                                                    placeholder="Nama Fasilitas" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row g-2 mb-3">
                                                                            <div class="col">
                                                                                <label for="jumlah-fasilitas"
                                                                                    class="form-label">Jumlah</label>
                                                                                <input type="text"
                                                                                    id="jumlah-fasilitas"
                                                                                    class="form-control" name="jumlah"
                                                                                    value="{{ $f->jumlah }}"
                                                                                    placeholder="2" />
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="status-fasilitas"
                                                                                    class="form-label">Status</label>
                                                                                <select id="status-fasilitas"
                                                                                    name="status" class="form-select">
                                                                                    <option value="">Pilih Status
                                                                                    </option>
                                                                                    <option value="1"
                                                                                        @if ($f->status == 1) selected @endif>
                                                                                        Milik Sendiri</option>
                                                                                    <option value="2"
                                                                                        @if ($f->status == 2) selected @endif>
                                                                                        Sewa / Kontrak</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <div class="col">
                                                                                <label for="keterangan-fasiltias"
                                                                                    class="form-label">Keterangan</label>
                                                                                <textarea class="form-control" id="keterangan-fasiltias" name="keterangan" rows="2">{{ $f->keterangan }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <div class="col">
                                                                                <label for="foto-fasilitas"
                                                                                    class="form-label">Foto</label>
                                                                                <input class="form-control" type="file"
                                                                                    id="foto-fasilitas" name="path_foto"
                                                                                    accept="image/*" />
                                                                                @if ($f->path_foto != null)
                                                                                    <a href="{{ asset($f->path_foto) }}"
                                                                                        target="_blank"><i
                                                                                            class="bx bxs-file-pdf"></i>Lihat
                                                                                        File</a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <div class="col">
                                                                                <label for="dokumen-fasilitas"
                                                                                    class="form-label">Dokumen
                                                                                    Pendukung untuk Kepemilikan
                                                                                    Sewa/Kontrak</label>
                                                                                <input class="form-control" type="file"
                                                                                    id="dokumen-fasilitas"
                                                                                    name="path_dokumen"
                                                                                    accept="application/pdf" />
                                                                                @if ($f->path_dokumen != null)
                                                                                    <a href="{{ asset($f->path_dokumen) }}"
                                                                                        target="_blank"><i
                                                                                            class="bx bxs-file-pdf"></i>Lihat
                                                                                        File</a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <div class="col">
                                                                                <label for="dokumen-pemeliharaan"
                                                                                    class="form-label">Dokumen Pemeliharaan Sarpras</label>
                                                                                <input class="form-control" type="file"
                                                                                    id="dokumen-pemeliharaan"
                                                                                    name="path_pemeliharaan"
                                                                                    accept="application/pdf" />
                                                                                @if ($f->path_pemeliharaan != null)
                                                                                    <a href="{{ asset($f->path_pemeliharaan) }}"
                                                                                        target="_blank"><i
                                                                                            class="bx bxs-file-pdf"></i>Lihat
                                                                                        File</a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-outline-secondary"
                                                                            data-bs-dismiss="modal">
                                                                            Batal
                                                                        </button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Tambah</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" data-bs-backdrop="static"
                                                        data-bs-backdrop="static" id="deleteFasilitas{{ $f->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Hapus Data Fasilitas</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Apakah Anda yakin akan menghapus data fasilitas
                                                                        {{ $f->nama }}?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        Batal
                                                                    </button>
                                                                    <form method="POST"
                                                                        action="{{ route('delete.fasilitas') }}">
                                                                        @method('delete')
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Hapus</button>
                                                                        <input type="hidden" name="id"
                                                                            id="id" value="{{ $f->id }}">
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if (!$dataFound)
                                                    <tr>
                                                        <td colspan="8">
                                                            <p class="text-center">
                                                                <span class="badge bg-label-secondary">Data belum
                                                                    ditambahkan</span>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="fasilitasModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('tambah.fasilitas') }}"
                                            enctype="multipart/form-data">
                                            <input type="text" value="{{ $step }}" name="tipe" hidden />
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Fasilitas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <label for="nama-fasilitas" class="form-label">Nama</label>
                                                        <input type="text" id="nama-fasilitas" class="form-control"
                                                            name="nama" placeholder="Nama Fasilitas" />
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col">
                                                        <label for="jumlah-fasilitas" class="form-label">Jumlah</label>
                                                        <input type="text" id="jumlah-fasilitas" class="form-control"
                                                            name="jumlah" placeholder="2" />
                                                    </div>
                                                    <div class="col">
                                                        <label for="status-fasilitas" class="form-label">Status</label>
                                                        <select id="status-fasilitas" name="status" class="form-select">
                                                            <option value="">Pilih Status</option>
                                                            <option value="1">Milik Sendiri</option>
                                                            <option value="2">Sewa / Kontrak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <label for="keterangan-fasiltias"
                                                            class="form-label">Keterangan</label>
                                                        <textarea class="form-control" id="keterangan-fasiltias" name="keterangan" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <label for="foto-fasilitas" class="form-label">Foto</label>
                                                        <input class="form-control" type="file" id="foto-fasilitas"
                                                            name="path_foto" accept="image/*" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <label for="dokumen-fasilitas" class="form-label">Dokumen
                                                            Pendukung untuk Kepemilikan Sewa/Kontrak</label>
                                                        <input class="form-control" type="file" id="dokumen-fasilitas"
                                                            name="path_dokumen" accept="application/pdf" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <label for="dokumen-pemeliharaan" class="form-label">Dokumen Pemeliharaan Sarpras</label>
                                                        <input class="form-control" type="file" id="dokumen-pemeliharaan"
                                                            name="path_pemeliharaan" accept="application/pdf" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">
                                                    Batal
                                                </button>
                                                <button type="submit" class="btn btn-primary">Tambah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pills -->
    </div>
@endsection
