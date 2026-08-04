@extends('layouts.app-sekretariat')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span>Permohonan Pengajuan Akreditasi {{ $pengajuan->jenis->nama }}</span><br><span
                class="fw-bold">{{ $pengajuan->nama_lembaga }}</span></h4>

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                @switch($pengajuan->verifikasi_permohonan)
                                    @case(0)
                                        <h5 class="card-title text-warning">Permohonan Pengajuan Belum Diverifikasi</h5>
                                    @break

                                    @case(1)
                                        <h5 class="card-title text-primary">Permohonan Pengajuan Akreditasi Disetujui</h5>
                                    @break

                                    @case(2)
                                        <h5 class="card-title text-danger">Permohonan Dibatalkan</h5>
                                    @break

                                    @case(3)
                                        <h5 class="card-title text-danger">Permohonan Ditolak</h5>
                                    @break
                                @endswitch
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label required" for="surat-permohonan">Surat Permohonan</label>
                                        <a href="{{ asset($pengajuan->surat_permohonan) }}" target="_blank"><i
                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="surat-akreditasi-lembaga">Surat Akreditasi
                                            Lembaga</label>
                                        @if ($pengajuan->surat_akreditasi_lembaga != null)
                                            <a href="{{ asset($pengajuan->surat_akreditasi_lembaga) }}" target="_blank"><i
                                                    class="bx bxs-file-pdf"></i>Lihat File</a>
                                        @else
                                            <span class="badge rounded-pill bg-label-secondary">Tidak ada file</span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="surat-akreditasi-lembaga">Surat Tanggapan
                                            Permohonan</label>
                                        @if ($pengajuan->surat_tanggapan_permohonan != null)
                                            <a href="{{ asset($pengajuan->surat_tanggapan_permohonan) }}" target="_blank"><i
                                                    class="bx bxs-file-pdf"></i>Lihat File</a>
                                        @else
                                            <span class="badge rounded-pill bg-label-secondary">Sekretariat belum
                                                upload</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($pengajuan->profile->is_lock == 0)
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalVerifikasi">Verifikasi Permohonan</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" disabled>Verifikasi
                                        Permohonan</button>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalPengisian">Verifikasi Pengisian</button>
                                @if ($pengajuan->profile->is_lock == 0)
                                    <button type="button" class="btn btn-sm btn-primary" disabled>Plot Asesor</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#asesorModal">Plot Asesor</button>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('sneat/assets/img/illustrations/girl-doing-yoga-light.png') }}"
                                    height="140" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($pengajuan->verifikasi_permohonan == 1)
                <div class="col-12">
                    <div class="nav-align-top mb-4">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                                <div class="row">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Daftar Pengajuan Akreditasi Pelatihan</h5>
                                        <div class="float-end">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#addPelatihan">Tambah Pelatihan</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="table-responsive text-nowrap" style="padding: 10px;">
                                            <table id="example" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Pelatihan</th>
                                                        <th>Angkatan</th>
                                                        <th>Tahun</th>
                                                        <th>Progres Akreditasi</th>
                                                        <th>Dokumen Akreditasi</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                @if ($pelatihan)
                                                    <tbody class="table-border-bottom-0">
                                                        @foreach ($pelatihan as $p)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $p->nama }}</td>
                                                                <td>{{ $p->angkatan }}</td>
                                                                <td>{{ $p->tahun }}</td>
                                                                <td><button type="button"
                                                                        class="btn btn-sm rounded-pill btn-success"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modalProgres"
                                                                        data-pelatihan-id="{{ $p->id }}"><i
                                                                            class='bx bx-show'></i> Lihat Progres</button>
                                                                </td>
                                                                <td><a href="{{ route('lihat.rekap', ['id' => $p->id]) }}"
                                                                        class="btn btn-sm rounded-pill btn-info"><i
                                                                            class='bx bx-file'></i> Lihat Dokumen
                                                                        Akreditasi</a>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-warning btn-sm rounded-pill"
                                                                        data-bs-toggle="modal"
                                                                        data_id="{{ $p->id }}"
                                                                        data-bs-target="#editPelatihan{{ $p->id }}"
                                                                        href="javascript:void(0);"><i
                                                                            class="bx bx-pencil"></i>
                                                                        Edit</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm rounded-pill"
                                                                        data-bs-toggle="modal"
                                                                        data_id="{{ $p->id }}"
                                                                        data-bs-target="#deletePelatihan{{ $p->id }}"
                                                                        href="javascript:void(0);"><i
                                                                            class="bx bx-trash"></i>
                                                                        Hapus</button>
                                                                </td>
                                                            </tr>

                                                            <div class="modal fade" data-bs-backdrop="static"
                                                                id="editPelatihan{{ $p->id }}" tabindex="-1"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <form method="POST"
                                                                            action="{{ route('pelatihan.ubah') }}">
                                                                            @method('PUT')
                                                                            @csrf
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Ubah Data Pelatihan
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="row g-2 mb-3">
                                                                                    <div class="col">
                                                                                        <label for="nama"
                                                                                            class="form-label">Nama
                                                                                            Pelatihan</label>
                                                                                        <input type="text"
                                                                                            name="nama" id="nama"
                                                                                            class="form-control"
                                                                                            value="{{ $p->nama }}" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row g-2 mb-3">
                                                                                    <div class="col">
                                                                                        <label for="angkatan"
                                                                                            class="form-label">Angkatan</label>
                                                                                        <input type="text"
                                                                                            name="angkatan" id="angkatan"
                                                                                            class="form-control"
                                                                                            value="{{ $p->angkatan }}" />
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <label for="tahun"
                                                                                            class="form-label">Email</label>
                                                                                        <input type="text"
                                                                                            name="tahun" id="tahun"
                                                                                            class="form-control"
                                                                                            value="{{ $p->tahun }}" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-outline-secondary"
                                                                                    data-bs-dismiss="modal">Batal</button>
                                                                                <button type="submit"
                                                                                    class="btn btn-warning">Ubah</button>
                                                                                <input type="hidden" name="id"
                                                                                    id="id"
                                                                                    value="{{ $p->id }}">
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal fade" data-bs-backdrop="static"
                                                                data-bs-backdrop="static"
                                                                id="deletePelatihan{{ $p->id }}" tabindex="-1"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Hapus Pelatihan</h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Apakah Anda yakin akan menghapus data
                                                                                pelatihan?
                                                                            </p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-outline-secondary"
                                                                                data-bs-dismiss="modal">Batal</button>
                                                                            <form method="POST"
                                                                                action="{{ route('pelatihan.hapus') }}">
                                                                                @method('delete')
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="btn btn-danger">Hapus</button>
                                                                                <input type="hidden" name="id"
                                                                                    id="id"
                                                                                    value="{{ $p->id }}">
                                                                            </form>
                                                                        </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </tbody>
                                                @else
                                                    <tbody class="table-border-bottom-0">
                                                        <tr>
                                                            <td colspan="6" class="text-center">
                                                                <span class="badge rounded-pill bg-label-secondary">Data
                                                                    belum diinput Tim Sekretariat</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" data-bs-backdrop="static" id="addPelatihan" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('pelatihan.tambah') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Pelatihan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col">
                                            <label for="nama" class="form-label">Nama Pelatihan</label>
                                            <input type="text" name="nama" id="nama" class="form-control"
                                                placeholder="Nama Pelatihan" />
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col">
                                            <label for="angkatan" class="form-label">Angkatan</label>
                                            <input type="text" name="angkatan" id="angkatan" class="form-control"
                                                placeholder="Angkatan" />
                                        </div>
                                        <div class="col">
                                            <label for="tahun" class="form-label">Tahun</label>
                                            <input type="text" name="tahun" id="tahun" class="form-control"
                                                placeholder="Tahun" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">Tambah</button>
                                    <input type="hidden" name="id_pengajuan" id="id_pengajuan"
                                        value="{{ $pengajuan->id }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Progres --}}
                <div class="modal fade" id="modalProgres" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Progres Proses Akreditasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md">
                                        <div id="accordionIcon" class="accordion accordion-without-arrow">
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header text-body d-flex justify-content-between"
                                                    id="accordionIconOne">
                                                    <button type="button" class="accordion-button collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#accordionIcon-1"
                                                        aria-controls="accordionIcon-1">
                                                        Profile Kelembagaan <span
                                                            class="badge rounded-pill bg-warning m-2">{{ round($progressProfile, 2) }}
                                                            %</span>
                                                    </button>
                                                </h2>
                                                <div id="accordionIcon-1" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionIcon">
                                                    <div class="accordion-body">
                                                        @if ($progressProfile < 100)
                                                            Berikut isian yang belum lengkap: <br>
                                                            @foreach ($nullProfile as $p)
                                                                <small><span
                                                                        class="badge rounded-pill bg-danger mr-2">{{ ucwords(str_replace('_', ' ', str_replace('path', 'File', $p))) }}</span></small>
                                                            @endforeach
                                                        @else
                                                            <span class="badge bg-success">Semua
                                                                dokumen
                                                                lengkap</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="accordion-item card">
                                                <h2 class="accordion-header text-body d-flex justify-content-between"
                                                    id="accordionIconTwo">
                                                    <button type="button" class="accordion-button collapsed"
                                                        data-bs-toggle="collapse" data-bs-target="#accordionIcon-2"
                                                        aria-controls="accordionIcon-2">
                                                        Kelengkapan Pelatihan <span
                                                            class="badge rounded-pill bg-warning m-2">{{ round($progressPelatihan, 2) }}
                                                            %</span>
                                                    </button>
                                                </h2>
                                                <div id="accordionIcon-2" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionIcon">
                                                    @foreach ($nullPelatihan as $np)
                                                        <div class="accordion-body mb-2">
                                                            Pelatihan: <span
                                                                class="badge rounded-pill bg-secondary mr-2">{{ $np['pelatihan'] }}</span>
                                                            <br>
                                                            @if ($progressPelatihan < 100)
                                                                Berikut isian yang belum lengkap: <br>
                                                                @foreach ($np['nullEachPelatihan'] as $nep)
                                                                    <small><span
                                                                            class="badge rounded-pill bg-danger mr-2">{{ ucwords(str_replace('_', ' ', str_replace('path', 'File', $nep))) }}</span></small>
                                                                @endforeach
                                                            @else
                                                                <span class="badge bg-success">Semua dokumen lengkap</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- Pills -->
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('verifikasi.pengajuan') }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Permohonan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="row g-2 mb-3">
                                <div class="col">
                                    <label for="status" class="form-label">Surat Tanggapan Permohonan</label>
                                    <input type="file" class="form-control" id="surat-tanggapan-permohonan"
                                        name="surat_tanggapan_permohonan" accept="application/pdf">
                                </div>
                            </div>
                            <div class="col">
                                <label for="status" class="form-label">Status Permohonan</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="1" @if ($pengajuan->verifikasi_permohonan == 1) selected @endif>Diterima
                                    </option>
                                    <option value="3" @if ($pengajuan->verifikasi_permohonan == 3) selected @endif>Ditolak
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Verifikasi</button>
                        <input type="hidden" name="id" id="id" value="{{ $pengajuan->id }}">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="modalPengisian" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.lock') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Permohonan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="is_lock" class="form-label">Status Permohonan</label>
                                <select class="form-control" id="is_lock" name="is_lock" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="1" @if ($pengajuan->profile->is_lock == 1) selected @endif>Ditutup
                                    </option>
                                    <option value="0" @if ($pengajuan->profile->is_lock == 0) selected @endif>Dibuka
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Verifikasi</button>
                        <input type="hidden" name="id" id="id" value="{{ $pengajuan->profile->id }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" data-bs-backdrop="static" id="asesorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('assign.asesor') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $pengajuan->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Manajemen Tim Asesor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="asesor1" class="form-label">Asesor 1 (Ketua)</label>
                                <select id="asesor1" name="asesor1" class="form-select">
                                    @if (is_null($pengajuan->id_asesor1))
                                        <option value="">Pilih Asesor</option>
                                    @endif
                                    @foreach ($asesors as $asesor)
                                        <option value="{{ $asesor->id }}"
                                            {{ $asesor->id == $pengajuan->id_asesor1 ? 'selected' : '' }}>
                                            {{ $asesor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="asesor2" class="form-label">Asesor 2 (Anggota)</label>
                                <select id="asesor2" name="asesor2" class="form-select">
                                    @if (is_null($pengajuan->id_asesor2))
                                        <option value="">Pilih Asesor</option>
                                    @endif
                                    @foreach ($asesors as $asesor)
                                        <option value="{{ $asesor->id }}"
                                            {{ $asesor->id == $pengajuan->id_asesor2 ? 'selected' : '' }}>
                                            {{ $asesor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="asesor3" class="form-label">Asesor 3 (Anggota)</label>
                                <select id="asesor3" name="asesor3" class="form-select">
                                    @if (is_null($pengajuan->id_asesor3))
                                        <option value="">Pilih Asesor</option>
                                    @endif
                                    @foreach ($asesors as $asesor)
                                        <option value="{{ $asesor->id }}"
                                            {{ $asesor->id == $pengajuan->id_asesor3 ? 'selected' : '' }}>
                                            {{ $asesor->name }}
                                        </option>
                                    @endforeach
                                </select>
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

@push('scripts')
    <script></script>
@endpush
