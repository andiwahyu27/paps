<div class="modal fade" data-bs-backdrop="static" id="addPengelolaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('tambah.tenaga') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah {{ $step_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control"
                                placeholder="Nama" />
                        </div>
                        <div class="col">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="pengelola@mail.com" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control"
                                placeholder="200101012020021001" />
                        </div>
                        <div class="col">
                            <label for="nip" class="form-label">NIP / NRP </label>
                            <input type="text" name="nip" id="nip" class="form-control"
                                placeholder="200101012020021001" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                placeholder="Salatiga" />
                        </div>
                        <div class="col">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                placeholder="28 Agustus 1995" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="id_pangkat" class="form-label">Pangkat/Golongan</label>
                            {{-- <select class="form-select pangkat" name="state" id="id_pangkat"
                                style="width: 100%; height: 100%"> --}}
                            <select class="form-select" name="id_pangkat" id="id_pangkat">
                                <option selected>Pilih Pangkat/Golongan</option>
                                @foreach ($pangkats as $pangkat)
                                    <option value="{{ $pangkat->id }}">{{ $pangkat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" id="jabatan" class="form-control"
                                placeholder="Jabatan" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="alamat_kantor" class="form-label">Alamat Kantor</label>
                            <input type="text" name="alamat_kantor" id="alamat_kantor" class="form-control"
                                placeholder="Jl. Raya Jagakarsa" />
                        </div>
                        <div class="col">
                            <label for="telp_kantor" class="form-label">Telp Kantor</label>
                            <input type="text" name="telp_kantor" id="telp_kantor" class="form-control"
                                placeholder="(021) 12341234" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="alamat_rumah" class="form-label">Alamat Rumah</label>
                            <input type="text" name="alamat_rumah" id="alamat_rumah" class="form-control"
                                placeholder="Jl. Raya Jagakarsa" />
                        </div>
                        <div class="col">
                            <label for="telp_rumah" class="form-label">Telp Rumah</label>
                            <input type="text" name="telp_rumah" id="telp_rumah" class="form-control"
                                placeholder="(021) 12341234" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="hp" class="form-label">No. Handphone</label>
                            <input type="text" name="hp" id="hp" class="form-control"
                                placeholder="0821 1234 1234" />
                        </div>
                        <div class="col">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" name="npwp" id="npwp" class="form-control"
                                placeholder="1234 1234 1234" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                    <input type="hidden" name="jenis_tenaga" id="jenis_tenaga" value="{{ $step }}">
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($tenagas as $tenaga)
    {{-- Tenaga Modal --}}
    <div class="modal fade" data-bs-backdrop="static" id="editPengelolaModal{{ $tenaga->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('ubah.tenaga') }}">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Pengelola Pelatihan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="{{ $tenaga->nama }}" />
                            </div>
                            <div class="col">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ $tenaga->email }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" name="nik" id="nik" class="form-control"
                                    value="{{ $tenaga->nik }}" />
                            </div>
                            <div class="col">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" name="nip" id="nip" class="form-control"
                                    value="{{ $tenaga->nip }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                    value="{{ $tenaga->tempat_lahir }}" />
                            </div>
                            <div class="col">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                    value="{{ $tenaga->tanggal_lahir }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="id_pangkat" class="form-label">Pangkat/Golongan</label>
                                <select name="id_pangkat" id="id_pangkat" class="form-control">
                                    <option>Pilih Pangkat/Golongan</option>
                                    @foreach ($pangkats as $pangkat)
                                        <option value="{{ $pangkat->id }}"
                                            {{ $pangkat->id == $tenaga->id_pangkat ? 'selected' : '' }}>
                                            {{ $pangkat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control"
                                    value="{{ $tenaga->jabatan }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="alamat_kantor" class="form-label">Alamat Kantor</label>
                                <input type="text" name="alamat_kantor" id="alamat_kantor" class="form-control"
                                    value="{{ $tenaga->alamat_kantor }}" />
                            </div>
                            <div class="col">
                                <label for="telp_kantor" class="form-label">Telp Kantor</label>
                                <input type="text" name="telp_kantor" id="telp_kantor" class="form-control"
                                    value="{{ $tenaga->telp_kantor }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="alamat_rumah" class="form-label">Alamat Rumah</label>
                                <input type="text" name="alamat_rumah" id="alamat_rumah" class="form-control"
                                    value="{{ $tenaga->alamat_rumah }}" />
                            </div>
                            <div class="col">
                                <label for="telp_rumah" class="form-label">Telp Rumah</label>
                                <input type="text" name="telp_rumah" id="telp_rumah" class="form-control"
                                    value="{{ $tenaga->telp_rumah }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="hp" class="form-label">No. Handphone</label>
                                <input type="text" name="hp" id="hp" class="form-control"
                                    value="{{ $tenaga->hp }}" />
                            </div>
                            <div class="col">
                                <label for="npwp" class="form-label">NPWP</label>
                                <input type="text" name="npwp" id="npwp" class="form-control"
                                    value="{{ $tenaga->npwp }}" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Ubah</button>
                        <input type="hidden" name="id" id="id" value="{{ $tenaga->id }}">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" data-bs-backdrop="static"
        id="deletePengelolaModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Pengelola Pelatihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menghapus data pengelola {{ $tenaga->nama }}?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('delete.tenaga') }}">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                        <input type="hidden" name="id" id="id" value="{{ $tenaga->id }}">
                    </form>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Riwayat Jabatan --}}
    <div class="modal fade" id="showJabatanModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Riwayat Jabatan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4">
                    <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
                    @if ($profile->is_lock == 0)
                        <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                            data-bs-target="#addJabatanModal{{ $tenaga->id }}">Tambah</button>
                    @else
                        <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
                    @endif
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jabatan</th>
                                    <th>Tugas</th>
                                    <th>Rentang Waktu</th>
                                    <th>Instansi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if (count($tenaga->r_jabatans) < 1)
                                    <tr>
                                        <td colspan="6">
                                            <p class="text-center"><span class="badge bg-label-secondary">Riwayat
                                                    Jabatan belum ditambahkan</span></p>
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($r_jabatans as $r_jabatan)
                                        @if ($r_jabatan->tenaga_id === $tenaga->id)
                                            <tr>
                                                <td>{{ $counter++ }}</td>
                                                <td>{{ $r_jabatan->jabatan }}</td>
                                                <td>{{ $r_jabatan->tugas }}</td>
                                                <td>{{ $r_jabatan->periode }}</td>
                                                <td>{{ $r_jabatan->instansi }}</td>
                                                <td>
                                                    @if ($profile->is_lock == 0)
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#editJabatanModal{{ $r_jabatan->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#deleteJabatanModal{{ $r_jabatan->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @else
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="addJabatanModal{{ $tenaga->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Tambah Riwayat Jabatan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class="text-muted ps-4">Riwayat Jabatan (diisi mulai dari jabatan saat ini)</p>
                <div class="modal-body">
                    <form method="POST" action="{{ route('tambah.riwayat') }}">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="nama-jabatan-1" class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" id="nama-jabatan-1" class="form-control"
                                    placeholder="Jabatan" />
                            </div>
                            <div class="col">
                                <label for="tugas-1" class="form-label">Tugas</label>
                                <input type="text" name="tugas" id="tugas-1" class="form-control"
                                    placeholder="Tugas" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="rentang-waktu-1" class="form-label">Rentang Waktu</label>
                                <input type="text" name="periode" id="rentang-waktu-1" class="form-control"
                                    placeholder="2021-2024" />
                            </div>
                            <div class="col">
                                <label for="instansi-1" class="form-label">Instansi</label>
                                <input type="text" name="instansi" id="instansi-1" class="form-control"
                                    placeholder="Pusdiklat BPS RI" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                            <input type="hidden" name="tenaga_id" id="tenaga_id" value="{{ $tenaga->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="jabatan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Kerja --}}
    <div class="modal fade" id="showKerjaModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Pengalaman Kerja</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4">
                    <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
                    @if ($profile->is_lock == 0)
                        <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                            data-bs-target="#addKerjaModal{{ $tenaga->id }}">Tambah</button>
                    @else
                        <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
                    @endif
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jabatan</th>
                                    <th>Tugas</th>
                                    <th>Tahun</th>
                                    <th>Instansi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if (count($tenaga->r_kerjas) < 1)
                                    <tr>
                                        <td colspan="6">
                                            <p class="text-center"><span class="badge bg-label-secondary">Pengalaman
                                                    Kerja belum ditambahkan</span></p>
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($r_kerjas as $r_kerja)
                                        @if ($r_kerja->tenaga_id === $tenaga->id)
                                            <tr>
                                                <td>{{ $counter++ }}</td>
                                                <td>{{ $r_kerja->jabatan }}</td>
                                                <td>{{ $r_kerja->tugas }}</td>
                                                <td>{{ $r_kerja->tahun }}</td>
                                                <td>{{ $r_kerja->instansi }}</td>
                                                <td>
                                                    @if ($profile->is_lock == 0)
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#editKerjaModal{{ $r_kerja->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#deleteKerjaModal{{ $r_kerja->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @else
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="addKerjaModal{{ $tenaga->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Tambah Pengalaman Kerja</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('tambah.riwayat') }}">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="nama-jabatan-1" class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" id="nama-jabatan-1" class="form-control"
                                    placeholder="Jabatan" />
                            </div>
                            <div class="col">
                                <label for="tugas-1" class="form-label">Tugas</label>
                                <input type="text" name="tugas" id="tugas-1" class="form-control"
                                    placeholder="Tugas" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="rentang-waktu-1" class="form-label">Tahun</label>
                                <input type="text" name="tahun" id="rentang-waktu-1" class="form-control"
                                    placeholder="2021" />
                            </div>
                            <div class="col">
                                <label for="instansi-1" class="form-label">Instansi</label>
                                <input type="text" name="instansi" id="instansi-1" class="form-control"
                                    placeholder="Pusdiklat BPS RI" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                            <input type="hidden" name="tenaga_id" id="tenaga_id" value="{{ $tenaga->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="kerja">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Pendidikan --}}
    <div class="modal fade" id="showPendidikanModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Riwayat Pendidikan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4">
                    <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
                    @if ($profile->is_lock == 0)
                        <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                            data-bs-target="#addPendidikanModal{{ $tenaga->id }}">Tambah</button>
                    @else
                        <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
                    @endif
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenjang</th>
                                    <th>Perguruan Tinggi - Jurusan</th>
                                    <th>Tahun Lulus</th>
                                    <th>Kota/Negara</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if (count($tenaga->r_pendidikans) < 1)
                                    <tr>
                                        <td colspan="6">
                                            <p class="text-center"><span class="badge bg-label-secondary">Riwayat
                                                    Pendidikan belum ditambahkan</span></p>
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($r_pendidikans as $r_pendidikan)
                                        @if ($r_pendidikan->tenaga_id === $tenaga->id)
                                            <tr>
                                                <td>{{ $counter++ }}</td>
                                                <td>{{ $r_pendidikan->jenjang }}</td>
                                                <td>{{ $r_pendidikan->sekolah }}</td>
                                                <td>{{ $r_pendidikan->tahun }}</td>
                                                <td>{{ $r_pendidikan->kota_negara }}</td>
                                                <td>{{ $r_pendidikan->keterangan }}</td>
                                                <td>
                                                    @if ($profile->is_lock == 0)
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#editPendidikanModal{{ $r_pendidikan->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#deletePendidikanModal{{ $r_pendidikan->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @else
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="addPendidikanModal{{ $tenaga->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Tambah Riwayat Pendidikan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('tambah.riwayat') }}">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="jenjang" class="form-label">Jenjang</label>
                                <input type="text" name="jenjang" id="jenjang" class="form-control"
                                    placeholder="S1" />
                            </div>
                            <div class="col">
                                <label for="sekolah" class="form-label">Pendidikan Tinggi - Jurusan</label>
                                <input type="text" name="sekolah" id="sekolah" class="form-control"
                                    placeholder="Universitas Indonesia - Ilmu Komputer" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="tahun" class="form-label">Tahun Lulus</label>
                                <input type="text" name="tahun" id="tahun" class="form-control"
                                    placeholder="2021" />
                            </div>
                            <div class="col">
                                <label for="kota_negara" class="form-label">Kota/Negara</label>
                                <input type="text" name="kota_negara" id="kota_negara" class="form-control"
                                    placeholder="Jakarta" />
                            </div>
                        </div>
                        <div class="col">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" class="form-control" placeholder="Cumlaude"
                                rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                            <input type="hidden" name="tenaga_id" id="tenaga_id" value="{{ $tenaga->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="pendidikan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Pelatihan --}}
    <div class="modal fade" id="showPelatihanModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Riwayat Pelatihan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4">
                    <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
                    @if ($profile->is_lock == 0)
                        <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                            data-bs-target="#addPelatihanModal{{ $tenaga->id }}">Tambah</button>
                    @else
                        <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
                    @endif
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelatihan</th>
                                    <th>Penyelenggara/Kota</th>
                                    <th>Tahun</th>
                                    <th>Sertifikat</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if (count($tenaga->r_pelatihans) < 1)
                                    <tr>
                                        <td colspan="6">
                                            <p class="text-center"><span class="badge bg-label-secondary">Riwayat
                                                    Pendidikan belum ditambahkan</span></p>
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($r_pelatihans as $r_pelatihan)
                                        @if ($r_pelatihan->tenaga_id === $tenaga->id)
                                            <tr>
                                                <td>{{ $counter++ }}</td>
                                                <td>{{ $r_pelatihan->pelatihan }}</td>
                                                <td>{{ $r_pelatihan->penyelenggara }}</td>
                                                <td>{{ $r_pelatihan->tahun }}</td>
                                                <td>{{ $r_pelatihan->sertifikasi }}</td>
                                                <td>{{ $r_pelatihan->keterangan }}</td>
                                                <td>
                                                    @if ($profile->is_lock == 0)
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#editPelatihanModal{{ $r_pelatihan->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="link" data-bs-toggle="modal"
                                                            data-bs-target="#deletePelatihanModal{{ $r_pelatihan->id }}"
                                                            href="javascript:void(0);"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @else
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-pencil me-1"></i></a>
                                                        <a class="disabled" href="#"><i
                                                                class="bx bx-trash me-1"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="addPelatihanModal{{ $tenaga->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Tambah Riwayat Pelatihan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('tambah.riwayat') }}">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="pelatihan" class="form-label">Nama Pelatihan</label>
                                <input type="text" name="pelatihan" id="pelatihan" class="form-control"
                                    placeholder="Pelatihan Pranata Komputer" />
                            </div>
                            <div class="col">
                                <label for="penyelenggara" class="form-label">Penyelenggara</label>
                                <input type="text" name="penyelenggara" id="penyelenggara" class="form-control"
                                    placeholder="Pusdiklat BPS RI" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="text" name="tahun" id="tahun" class="form-control"
                                    placeholder="2021" />
                            </div>
                            <div class="col">
                                <label for="sertifikasi" class="form-label">Sertifikasi</label>
                                <input type="text" name="sertifikasi" id="sertifikasi" class="form-control"
                                    placeholder="Sertifikasi" />
                            </div>
                        </div>
                        <div class="col">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" class="form-control" placeholder="-"
                                rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                            <input type="hidden" name="tenaga_id" id="tenaga_id" value="{{ $tenaga->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="pelatihan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($r_jabatans as $r_jabatan)
    <div class="modal fade" data-bs-backdrop="static" id="editJabatanModal{{ $r_jabatan->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Ubah Riwayat Jabatan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class="text-muted ps-4">Riwayat Jabatan (diisi mulai dari jabatan saat ini)</p>

                <div class="modal-body">
                    <form method="POST" action="{{ route('ubah.riwayat') }}">
                        @method('PUT')
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="nama-jabatan-1" class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" id="nama-jabatan-1" class="form-control"
                                    value="{{ $r_jabatan->jabatan }}" />
                            </div>
                            <div class="col">
                                <label for="tugas-1" class="form-label">Tugas</label>
                                <input type="text" name="tugas" id="tugas-1" class="form-control"
                                    value="{{ $r_jabatan->tugas }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="rentang-waktu-1" class="form-label">Rentang Waktu</label>
                                <input type="text" name="periode" id="rentang-waktu-1" class="form-control"
                                    value="{{ $r_jabatan->periode }}" />
                            </div>
                            <div class="col">
                                <label for="instansi-1" class="form-label">Instansi</label>
                                <input type="text" name="instansi" id="instansi-1" class="form-control"
                                    value="{{ $r_jabatan->instansi }}" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Ubah</button>
                            <input type="hidden" name="id" value="{{ $r_jabatan->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="jabatan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="deleteJabatanModal{{ $r_jabatan->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Hapus Riwayat Jabatan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menghapus data riwayat jabatan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('hapus.riwayat') }}">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                        <input type="hidden" name="id" value="{{ $r_jabatan->id }}">
                        <input type="hidden" name="riwayat" id="riwayat" value="jabatan">
                    </form>
                </div>

            </div>
        </div>
    </div>
@endforeach

@foreach ($r_kerjas as $r_kerja)
    <div class="modal fade" data-bs-backdrop="static" id="editKerjaModal{{ $r_kerja->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Ubah Pengalaman Kerja</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('ubah.riwayat') }}">
                        @method('PUT')
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="nama-jabatan-1" class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" id="nama-jabatan-1" class="form-control"
                                    value="{{ $r_kerja->jabatan }}" />
                            </div>
                            <div class="col">
                                <label for="tugas-1" class="form-label">Tugas</label>
                                <input type="text" name="tugas" id="tugas-1" class="form-control"
                                    value="{{ $r_kerja->tugas }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="rentang-waktu-1" class="form-label">Tahun</label>
                                <input type="text" name="periode" id="rentang-waktu-1" class="form-control"
                                    value="{{ $r_kerja->tahun }}" />
                            </div>
                            <div class="col">
                                <label for="instansi-1" class="form-label">Instansi</label>
                                <input type="text" name="instansi" id="instansi-1" class="form-control"
                                    value="{{ $r_kerja->instansi }}" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Ubah</button>
                            <input type="hidden" name="id" value="{{ $r_kerja->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="jabatan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="deleteKerjaModal{{ $r_kerja->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Hapus Pengalaman Kerja</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menghapus data pengalaman kerja?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('hapus.riwayat') }}">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                        <input type="hidden" name="id" value="{{ $r_kerja->id }}">
                        <input type="hidden" name="riwayat" id="riwayat" value="kerja">
                    </form>
                </div>

            </div>
        </div>
    </div>
@endforeach

@foreach ($r_pendidikans as $r_pendidikan)
    <div class="modal fade" data-bs-backdrop="static" id="editPendidikanModal{{ $r_pendidikan->id }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Ubah Riwayat Pendidikan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('ubah.riwayat') }}">
                        @method('put')
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="jenjang" class="form-label">Jenjang</label>
                                <input type="text" name="jenjang" id="jenjang" class="form-control"
                                    value="{{ $r_pendidikan->jenjang }}" />
                            </div>
                            <div class="col">
                                <label for="sekolah" class="form-label">Pendidikan Tinggi (termasuk
                                    jurusan)</label>
                                <input type="text" name="sekolah" id="sekolah" class="form-control"
                                    value="{{ $r_pendidikan->sekolah }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="tahun" class="form-label">Tahun Lulus</label>
                                <input type="text" name="tahun" id="tahun" class="form-control"
                                    value="{{ $r_pendidikan->tahun }}" />
                            </div>
                            <div class="col">
                                <label for="kota_negara" class="form-label">Kota/Negara</label>
                                <input type="text" name="kota_negara" id="kota_negara" class="form-control"
                                    value="{{ $r_pendidikan->kota_negara }}" />
                            </div>
                        </div>
                        <div class="col">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" class="form-control"
                                value="{{ $r_pendidikan->keterangan }}" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Ubah</button>
                            <input type="hidden" name="id" value="{{ $r_pendidikan->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="pendidikan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="deletePendidikanModal{{ $r_pendidikan->id }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Hapus Riwayat Pendidikan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menghapus data riwayat pendidikan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('hapus.riwayat') }}">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                        <input type="hidden" name="id" value="{{ $r_pendidikan->id }}">
                        <input type="hidden" name="riwayat" id="riwayat" value="pendidikan">
                    </form>
                </div>

            </div>
        </div>
    </div>
@endforeach

@foreach ($r_pelatihans as $r_pelatihan)
    <div class="modal fade" data-bs-backdrop="static" id="editPelatihanModal{{ $r_pelatihan->id }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Ubah Riwayat Pelatihan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <p class="text-muted ps-4">Riwayat Pelatihan terkait Pengelola Pelatihan</p>
                <div class="modal-body">
                    <form method="POST" action="{{ route('ubah.riwayat') }}">
                        @method('put')
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="pelatihan" class="form-label">Nama Pelatihan</label>
                                <input type="text" name="pelatihan" id="pelatihan" class="form-control"
                                    value="{{ $r_pelatihan->pelatihan }}" />
                            </div>
                            <div class="col">
                                <label for="penyelenggara" class="form-label">Penyelenggara/Kota</label>
                                <input type="text" name="penyelenggara" id="penyelenggara"
                                    class="form-control" value="{{ $r_pelatihan->penyelenggara }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="text" name="tahun" id="tahun" class="form-control"
                                    value="{{ $r_pelatihan->tahun }}" />
                            </div>
                            <div class="col">
                                <label for="sertifikasi" class="form-label">Sertifikasi</label>
                                <input type="text" name="sertifikasi" id="sertifikasi" class="form-control"
                                    value="{{ $r_pelatihan->sertifikasi }}" />
                            </div>
                        </div>
                        <div class="col">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" class="form-control"
                                value="{{ $r_pelatihan->keterangan }}" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Ubah</button>
                            <input type="hidden" name="id" value="{{ $r_pelatihan->id }}">
                            <input type="hidden" name="riwayat" id="riwayat" value="pelatihan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="deletePelatihanModal{{ $r_pelatihan->id }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex">
                        <h5 class="modal-title">Hapus Riwayat Pelatihan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menghapus data riwayat pelatihan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('hapus.riwayat') }}">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                        <input type="hidden" name="id" value="{{ $r_pelatihan->id }}">
                        <input type="hidden" name="riwayat" id="riwayat" value="pelatihan">
                    </form>
                </div>

            </div>
        </div>
    </div>
@endforeach

@push('scripts')
    <script>
        $(document).ready(function() {

            $('.pangkat').select2({
                dropdownParent: $('#addPengelolaModal'),
                placeholder: 'Pilih Pangkat/Golongan',

            });

            // $('.pangkat').select2();
        });
    </script>
@endpush
