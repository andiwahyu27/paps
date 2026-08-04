@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-6 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Program Pelatihan Sistem Teknologi Berbasis Komputer 💻
                                </h5>
                                @php
                                    $pengajuan = $pengajuans->first();
                                @endphp

                                @if ($pengajuan && $pengajuan->id_jenis == 1)
                                    @if ($pengajuan->final == 1 && $pengajuan->verifikasi_permohonan == 1)
                                        <p class="mb-4">
                                            Yeyy, akreditasi Program Pelatihan Sistem Teknologi Berbasis Komputer instansi
                                            anda
                                            masih berlaku hingga.
                                        </p>

                                        <a href="{{ asset($pengajuan->sertifikat_hasil_akreditasi) }}"
                                            class="btn btn-sm btn-success">
                                            Lihat Sertifikat
                                        </a>
                                    @elseif($pengajuan->final == 1 && $pengajuan->verifikasi_permohonan == 3)
                                        <p class="mb-4">
                                            Saat ini, pengajuan akreditasi Program
                                            Pelatihan Sistem Teknologi Berbasis Komputer instansi Anda ditolak. Silahkan lakukan pengajuan ulang
                                        </p>

                                        <a href="{{ route('pengajuan', ['type' => '1']) }}" class="btn btn-sm btn-warning">
                                            Buat Pengajuan
                                        </a>
                                    @else
                                        <p class="mb-4">
                                            Saat ini, instansi anda sedang melakukan pengajuan akreditasi untuk Program
                                            Pelatihan Sistem Teknologi Berbasis Komputer.
                                        </p>

                                        <a href="{{ route('riwayat.pengajuan', $pengajuan->id) }}" class="btn btn-sm btn-info">
                                            Lihat Permohonan
                                        </a>
                                    @endif
                                @else
                                    <p class="mb-4">
                                        Saat ini, instansi anda belum melakukan pengajuan akreditasi untuk Program Pelatihan
                                        Sistem Teknologi Berbasis Komputer.
                                    </p>

                                    @if (auth()->user()->id_profile)
                                        <a href="{{ route('pengajuan', ['type' => '1']) }}" class="btn btn-sm btn-warning">
                                            Buat Pengajuan
                                        </a>
                                    @else
                                        <a href="{{ route('error', ['judul' => 'Akses Ditolak', 'pesan' => 'Anda belum terdaftar sebagai PIC Lembaga. Hubungi Tim Sekretariat!']) }}"
                                            class="btn btn-sm btn-secondary">
                                            Buat Pengajuan
                                        </a>
                                    @endif
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
                                <h5 class="card-title text-primary">Program Pelatihan Statistik 📈</h5>
                                @php
                                    $pengajuan = $pengajuans->first();
                                @endphp
                                @if ($pengajuan && $pengajuan->id_jenis == 2)
                                    @if ($pengajuan->final == 1 && $pengajuan->verifikasi_permohonan == 1)
                                        <p class="mb-4">
                                            Yeyy, akreditasi Program Pelatihan Statistik instansi anda
                                            masih berlaku hingga.
                                        </p>

                                        <a href="{{ asset($pengajuan->sertifikat_hasil_akreditasi) }}"
                                            class="btn btn-sm btn-success">
                                            Lihat Sertifikat
                                        </a>
                                    @elseif($pengajuan->final == 1 && $pengajuan->verifikasi_permohonan == 3)
                                            <p class="mb-4">
                                                Saat ini, pengajuan akreditasi Program
                                                Pelatihan Statistik instansi Anda ditolak. Silahkan lakukan pengajuan ulang
                                            </p>

                                            <a href="{{ route('pengajuan', ['type' => '2']) }}" class="btn btn-sm btn-warning">
                                                Buat Pengajuan
                                            </a>
                                    @else
                                        <p class="mb-4">
                                            Saat ini, instansi anda sedang melakukan pengajuan akreditasi untuk Program
                                            Pelatihan Sistem Teknologi Berbasis Komputer.
                                        </p>

                                        <a href="{{ route('riwayat.pengajuan', $pengajuan->id) }}" class="btn btn-sm btn-info">
                                            Lihat Permohonan
                                        </a>
                                    @endif
                                @else
                                    <p class="mb-4">
                                        Saat ini, instansi anda belum melakukan pengajuan akreditasi untuk Program Pelatihan
                                        Statistik.
                                    </p>

                                    @if (auth()->user()->id_profile)
                                        <a href="{{ route('pengajuan', ['type' => '2']) }}" class="btn btn-sm btn-warning">
                                            Buat Pengajuan
                                        </a>
                                    @else
                                        <a href="{{ route('error', ['judul' => 'Akses Ditolak', 'pesan' => 'Anda belum terdaftar sebagai PIC Lembaga. Hubungi Tim Sekretariat!']) }}"
                                            class="btn btn-sm btn-secondary">
                                            Buat Pengajuan
                                        </a>
                                    @endif
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

        @if (auth()->user()->id_profile)
            <div class="row">
                <div class="col-12 mb-4 order-0">
                    <div class="card">
                        <h5 class="card-header">Riwayat Pengajuan</h5>
                        <div class="table-responsive text-nowrap" style="padding: 10px;">
                            <table id="example" class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Program Pelatihan</th>
                                        <th>Status Permohonan</th>
                                        <th>Status Akreditasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($pengajuans as $p)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $p->jenis->nama }}</td>
                                            <td>
                                                @switch($p->verifikasi_permohonan)
                                                    @case(0)
                                                        <span class="badge rounded-pill bg-warning">Sedang Diajukan</span>
                                                    @break

                                                    @case(1)
                                                        <span class="badge rounded-pill bg-success">Diterima</span>
                                                    @break

                                                    @case(2)
                                                        <span class="badge rounded-pill bg-danger">Dibatalkan</span>
                                                    @break

                                                    @case(3)
                                                        <span class="badge rounded-pill bg-danger">Ditolak</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($p->verifikasi_permohonan)
                                                    @case(0)
                                                        <span class="badge rounded-pill bg-warning">Menunggu Permohonan</span>
                                                    @break

                                                    @case(1)
                                                        @switch($p->status)
                                                            @case(0)
                                                                <span class="badge rounded-pill bg-info">Proses Akreditasi</span>
                                                            @break

                                                            @case(1)
                                                                <span class="badge rounded-pill bg-success">Terakreditasi</span>
                                                            @break
                                                        @endswitch
                                                    @break

                                                    @case(2)
                                                    @case(3)
                                                        <span class="badge rounded-pill bg-danger">Batal Akreditasi</span>
                                                    @break

                                                @endswitch
                                            </td>
                                            <td>
                                                @if ($p->verifikasi_permohonan == 1)
                                                    <a class="btn btn-info btn-sm rounded-pill"
                                                        href="{{ route('riwayat.pengajuan', $p->id) }}"><i
                                                            class="bx bxs-show"></i>
                                                        Lihat Permohonan
                                                    </a>
                                                @else
                                                    <x-button-edit judul="Edit Permohonan"
                                                        href="{{ route('pengajuan', $p->id_jenis) }}" />
                                                    <x-button-delete judul="Delete Permohonan"
                                                        href="{{ route('pengajuan', $p->id_jenis) }}" />
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
        @else
            <div class="alert alert-danger" role="alert">
                <strong>Perhatian!</strong> Anda belum terdaftar sebagai PIC Lembaga. Silahkan hubungi <span><strong>Tim
                        Sekretariat</strong></span> untuk melakukan pendaftaran
                terlebih dahulu.
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        new DataTable('#example');
    </script>
    <script>
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    </script>
@endpush
