@extends('layouts.app-asesor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-6 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Program Pelatihan Teknis di Bidang Sistem Teknologi Berbasis Komputer 💻</h5>
                                @if ($isPrakom > 0)
                                    <p class="mb-4">
                                        Saat ini terdapat {{ $isPrakom }} Lembaga yang perlu anda nilai untuk Program Pelatihan Teknis di Bidang Sistem Teknologi Berbasis Komputer.
                                    </p>

                                    <a href="#" class="btn btn-sm btn-success">Lihat Lembaga</a>
                                @else
                                    <p class="mb-4">
                                        Saat ini belum ada Lembaga yang masuk ke dalam penilaian akreditasi untuk Program Pelatihan Teknis di Bidang Sistem Teknologi Berbasis Komputer.
                                    </p>

                                    <a href="#" class="btn btn-sm btn-warning">Lihat Riwayat Penilaian</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('sneat/assets/img/illustrations/man-with-laptop-light.png') }}"
                                    height="140" alt="View Badge User" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Program Pelatihan Teknis di Bidang Statistisk 📈</h5>
                                @if ($isStatistisi > 0)
                                    <p class="mb-4">
                                        Saat ini ada {{ $isPrakom }} Lembaga yang perlu anda nilai untuk Program Pelatihan Teknis di Bidang Statistisk.
                                    </p>

                                    <a href="#" class="btn btn-sm btn-success">Lihat Penilaian</a>
                                @else
                                    <p class="mb-4">
                                        Saat ini belum ada Lembaga yang masuk ke dalam penilaian akreditasi untuk Program Pelatihan Teknis di Bidang Statistisk.
                                    </p>

                                    <a href="#" class="btn btn-sm btn-warning">Lihat Riwayat Penilaian</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('sneat/assets/img/illustrations/man-with-data.png') }}" height="140"
                                    alt="View Badge User" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">Daftar Riwayat Penilaian</h5>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap" style="padding: 10px;">
                            <table id="example" class="table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Lembaga</th>
                                        <th>Program</th>
                                        <th>Pelatihan</th>
                                        <th>Pra-Visit 1</th>
                                        <th>Pra-Visit 2</th>
                                        <th>Visitasi</th>
                                        <th>Paska-Visit</th>
                                        <th>Sidang Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($pengajuans as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $p->profile->nama_lembaga }}</td>
                                            <td>{{ $p->jenis->nama }}</td>
                                            <td>
                                                <button type="button" class="btn rounded-pill btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalLihatPelatihan">
                                                    <span class="badge rounded-pill bg-white text-info">
                                                        {{ $p->pelatihan->count() }}
                                                    </span> KEGIATAN
                                                </button>
                                            </td>
                                            <td>
                                                @if ($p->ispravisit1() == 1)
                                                    <a href="{{ route('view.pravisit', $p->id) }}"
                                                        class="badge rounded-pill bg-success">Sudah Dinilai</a>
                                                @else
                                                    <a href="{{ route('pravisit', $p->id) }}"
                                                        class="badge rounded-pill bg-warning">Belum Dinilai</a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->ispravisit1() == 1)
                                                    @if ($p->ispravisit2() == 1)
                                                        <a href="{{ route('view.pravisit2', $p->id) }}"
                                                            class="badge rounded-pill bg-success">Sudah Dinilai</a>
                                                    @else
                                                        <a href="{{ route('pravisit2', $p->id) }}"
                                                            class="badge rounded-pill bg-warning">Belum Dinilai</a>
                                                    @endif
                                                @else
                                                    <span class="badge rounded-pill bg-danger">Belum Pra Visit 1</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->ispravisit2() == 1)
                                                        <a href="{{ route('visitasi', $p->id) }}"
                                                            @if ($p->isvisitasi() == 1)
                                                            class="badge rounded-pill bg-success"> Sudah Dinilai
                                                            @else
                                                            class="badge rounded-pill bg-warning"> Belum Dinilai
                                                            @endif
                                                        </a>
                                                @else
                                                    <span class="badge rounded-pill bg-danger">Belum Pra Visit 2</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->isvisitasi() == 1)
                                                    @if ($p->ispaskavisit() == 1)
                                                        <a href="{{ route('view.paskavisit', $p->id) }}"
                                                            class="badge rounded-pill bg-success">Sudah Dinilai</a>
                                                    @else
                                                        <a href="{{ route('paskavisit', $p->id) }}"
                                                            class="badge rounded-pill bg-warning">Belum Dinilai</a>
                                                    @endif
                                                @else
                                                    <span class="badge rounded-pill bg-danger">Belum Visitasi</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->ispaskavisit() == 1)
                                                    @if($p->isfinal() == 1)
                                                        <a href="{{ route('view.final', $p->id) }}" class="badge rounded-pill bg-success">Lihat Hasil</a>
                                                    @else
                                                        <a href="{{ route('final', $p->id) }}" class="badge rounded-pill bg-warning">Belum Dinilai</a>
                                                    @endif
                                                @else
                                                    <span class="badge rounded-pill bg-danger">Belum Paska Visit</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Pelatihan -->
    <x-modal-lihatPelatihan :pengajuans="$pengajuans" />
@endsection

@push('scripts')
    <script>
        new DataTable('#example', {
            responsive: true,
            autoWidth: false,
        });
    </script>
@endpush

