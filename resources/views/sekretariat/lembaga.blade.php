@extends('layouts.app-sekretariat')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span>Daftar Lembaga</span></h4>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                            data-bs-target="#addLembaga">Tambah Lembaga</button>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lembaga</th>
                                    <th>PIC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($lembaga as $l)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $l->nama_lembaga }}</td>
                                        <td>
                                            @foreach ($l->pic as $pl)
                                                <button type="button" class="btn btn-danger btn-xs rounded-pill"
                                                    data-bs-toggle="modal" data_id="{{ $pl->id }}"
                                                    data-bs-target="#deletePic{{ $pl->id }}"
                                                    href="javascript:void(0);">{{ $pl->name . ' (' . $pl->email . ')' }}<i
                                                        class="bx bx-xs bx-trash"></i></button><br>
                                                <div class="modal fade" data-bs-backdrop="static" data-bs-backdrop="static"
                                                    id="deletePic{{ $pl->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus PIC {{ $l->nama_lembaga }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin akan menghapus data pengguna
                                                                    {{ $pl->name }}?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <form method="POST" action="{{ route('pic.hapus') }}">
                                                                    @method('put')
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-danger">Hapus</button>
                                                                    <input type="hidden" name="id_user"
                                                                        value="{{ $pl->id }}" hidden>
                                                                </form>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm rounded-pill"
                                                data-bs-toggle="modal" data_id="{{ $l->id }}"
                                                data-bs-target="#addPic{{ $l->id }}" href="javascript:void(0);"><i
                                                    class="bx bx-plus"></i> Tambah PIC</button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" data-bs-backdrop="static" id="addPic{{ $l->id }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('pic.tambah') }}">
                                                    @method('PUT')
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tambah PIC</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-2 mb-3">
                                                            <div class="col">
                                                                <input type="text" name="id_profile"
                                                                    value="{{ $l->id }}" hidden />
                                                                <label for="name" class="form-label">Nama
                                                                    Pengguna</label>
                                                                <select class="form-control" id="nama" name="id_user"
                                                                    required>
                                                                    <option value="">Pilih PIC</option>
                                                                    @foreach ($pengguna as $p)
                                                                        <option value="{{ $p->id }}">
                                                                            {{ $p->name . ' - ' . $p->email }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Tambah</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="addLembaga" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('lembaga.tambah') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Lembaga</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="name" class="form-label">Nama Lembaga</label>
                                <input type="text" name="nama_lembaga" id="name" class="form-control"
                                    placeholder="Nama" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
