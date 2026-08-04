<div class="row">
    <div class="col-6 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Fasilitator Bertugas</h5>
            @if ($pelatihan->pengajuan->profile->is_lock == 0)
                <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                    data-bs-target="#addFasilitator">Tambah</button>
            @else
                <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
            @endif
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
                        @if ($t->jenis_tenaga == 2)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>
                                    <option value="{{ $t->id }}">{{ $t->tenaga->nama }}
                                <td>
                                <td>
                                    @if ($pelatihan->pengajuan->profile->is_lock == 0)
                                        <button type="button"class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="{{ '#hapusTenaga' . $t->id }}">Hapus <i
                                                class='bx bx-trash'></i></button>
                                    @else
                                        <button type="button"class="btn btn-sm btn-danger" disabled>Hapus <i
                                                class='bx bx-trash'></i></button>
                                    @endif
                                </td>
                            </tr>
                            <div class="modal fade" id="{{ 'hapusTenaga' . $t->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('hapus.tenaga') }}" method="post"
                                            enctype="multipart/form-data">
                                            @method('delete')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Dokumen</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin akan menghapus data ini?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                                <input type="hidden" name="id" value="{{ $t->id }}">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Pengelola Kelas</h5>
            @if ($pelatihan->pengajuan->profile->is_lock == 0)
                <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                    data-bs-target="#addPengelolaKelas">Tambah</button>
            @else
                <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
            @endif
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
                        @if ($t->jenis_tenaga == 3)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>
                                    <option value="{{ $t->id }}">{{ $t->tenaga->nama }}
                                <td>
                                <td>
                                    @if ($pelatihan->pengajuan->profile->is_lock == 0)
                                        <button type="button"class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="{{ '#hapusTenaga' . $t->id }}">Hapus <i
                                                class='bx bx-trash'></i></button>
                                    @else
                                        <button type="button"class="btn btn-sm btn-danger" disabled>Hapus <i
                                                class='bx bx-trash'></i></button>
                                    @endif
                                </td>
                            </tr>
                            <div class="modal fade" id="{{ 'hapusTenaga' . $t->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('hapus.tenaga') }}" method="post"
                                            enctype="multipart/form-data">
                                            @method('delete')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Dokumen</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin akan menghapus data ini?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                                <input type="hidden" name="id" value="{{ $t->id }}">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addFasilitator" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('store.tenaga', 2) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Formulir Tambah Fasilitator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="nama" class="form-label">Nama Fasilitator</label>
                            <select class="form-control" id="nama" name="id_tenaga" required>
                                <option value="">Pilih Fasilitator</option>
                                @foreach ($fasilitator as $f)
                                    <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                    <input type="hidden" name="id_pelatihan" value="{{ $pelatihan->id }}">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addPengelolaKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('store.tenaga', 3) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Formulir Tambah Pengelola Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="nama" class="form-label">Nama Pengelola Kelas</label>
                            <select class="form-control" id="nama" name="id_tenaga" required>
                                <option value="">Pilih Pengelola</option>
                                @foreach ($pengelolaKelas as $f)
                                    <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                    <input type="hidden" name="id_pelatihan" value="{{ $pelatihan->id }}">
                </div>
            </form>
        </div>
    </div>
</div>
