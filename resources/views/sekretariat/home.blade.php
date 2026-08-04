@extends('layouts.app-sekretariat')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">Riwayat Pengajuan</h5>
                    <div class="table-responsive text-nowrap table-wrapper" style="padding: 10px; overflow-x: auto;">
                        <table id="example" class="table table-bordered table-hover display nowrap" style="width: 100%;">
                            <thead style="background-color:#eefeff;">
                                <tr>
                                    <th>No</th>
                                    <th>Lembaga</th>
                                    <th>Program</th>
                                    <th>Pelatihan</th>
                                    <th>Waktu Pengajuan</th>
                                    <th>Status</th>
                                    <th>Pengisian</th>
                                    <th>Status Penilaian</th>
                                    <th>Pengajuan</th>
                                    <th>Penilaian Asesor</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($pengajuans as $p)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $p->profile->nama_lembaga }}</td>
                                        <td>{{ $p->jenis->nama }}</td>
                                        <td>
                                            <button type="button" class="btn rounded-pill btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalLihatPelatihan">
                                                    <span class="badge rounded-pill bg-white text-info">
                                                        {{ $p->pelatihan->count() }}
                                                    </span> Kegiatan
                                            </button>
                                        </td>
                                        <td>{{ $p->created_at }}</td>
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
                                            @if ($p->verifikasi_permohonan == 1)
                                                @if ($p->profile->is_lock == 1)
                                                    <span class="badge rounded-pill bg-danger">Ditutup</span>
                                                @else
                                                    <span class="badge rounded-pill bg-success">Dibuka</span>
                                                @endif
                                            @else
                                                <span class="badge rounded-pill bg-danger">Ditutup</span>
                                            @endif
                                        </td>
                                        <td>
                                            @isset($p->asesor1)
                                                @if ($p->pra_visit_asesor1 == 1)
                                                    <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="Asesor 1 ({{ $p->asesor1->name }}) sudah menilai"
                                                        class="pull-up badge badge-center rounded-pill bg-success">1</span>
                                                @else
                                                    <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="Asesor 1 ({{ $p->asesor1->name }}) sedang menilai"
                                                        class="pull-up badge badge-center rounded-pill bg-warning">1</span>
                                                @endif
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="belum assign asesor 1"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary">1</span>
                                            @endisset
                                            @isset($p->asesor2)
                                                @if ($p->pra_visit_asesor2 == 1)
                                                    <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="Asesor 2 ({{ $p->asesor2->name }}) sudah menilai"
                                                        class="pull-up badge badge-center rounded-pill bg-success">2</span>
                                                @else
                                                    <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="Asesor 2 ({{ $p->asesor2->name }}) sedang menilai"
                                                        class="pull-up badge badge-center rounded-pill bg-warning">2</span>
                                                @endif
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="belum assign asesor 2"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary">2</span>
                                            @endisset
                                            @isset($p->asesor3)
                                                @if ($p->pra_visit_asesor3 == 1)
                                                    <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="Asesor 3 ({{ $p->asesor3->name }}) sudah menilai"
                                                        class="pull-up badge badge-center rounded-pill bg-success">3</span>
                                                @else
                                                    <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="Asesor 3 ({{ $p->asesor3->name }}) sedang menilai"
                                                        class="pull-up badge badge-center rounded-pill bg-warning">3</span>
                                                @endif
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="belum assign asesor 3"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary">3</span>
                                            @endisset
                                            @if($p->pra_visit2_asesor == 1)
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Pravisit 2 sudah dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-success"><i
                                                        class='bx bxs-user'></i></span>
                                            @elseif ($p->pra_visit_asesor1 + $p->pra_visit_asesor2 + $p->pra_visit_asesor3 > 0)
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Pravisit 2 sedang dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-warning"><i
                                                        class='bx bxs-user'></i></span>
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Pravisit 2 belum dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary"><i
                                                        class='bx bxs-user'></i></span>
                                            @endif
                                            @if($p->visitasi == 1)
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Visitasi sudah dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-success"><i
                                                        class='bx bxs-user'></i></span>
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Visitasi belum dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary"><i
                                                        class='bx bxs-user'></i></span>
                                            @endif
                                            @if($p->paska_visit==1)
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Paskavisit sudah dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-success"><i
                                                        class='bx bxs-user'></i></span>
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Paskavisit belum dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary"><i
                                                        class='bx bxs-user'></i></span>
                                            @endif
                                            @if($p->final ==1)
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Final sudah dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-success"><i
                                                        class='bx bxs-user'></i></span>
                                            @elseif($p->paska_visit == 1)
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Final sedang dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-warning"><i
                                                        class='bx bxs-user'></i></span>
                                            @else
                                                <span data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-bs-placement="top" data-bs-original-title="Final belum dinilai"
                                                    class="pull-up badge badge-center rounded-pill bg-secondary"><i
                                                        class='bx bxs-user'></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('lihat.pengajuan', $p->id) }}" class="btn btn-sm rounded-pill btn-info"><i class="bx bx-show"></i> Lihat</a>
                                        </td>
                                        <td>
                                            @if($p->pra_visit_asesor1 == 1 || $p->pra_visit_asesor2 == 1 || $p->pra_visit_asesor3 == 1)
                                                <a href="{{ route('pravisit2', $p->id) }}" class="btn btn-sm rounded-pill btn-info"><i class="bx bx-show"></i> Lihat Penilaian</a>
                                            @else
                                                <a href="#" class="btn btn-sm rounded-pill btn-secondary"><i class="bx bx-xs bxs-error"></i> Belum Dinilai {{ $p->ispravisit1() }}</a>
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

    <div class="modal fade" data-bs-backdrop="static" id="generateBA" tapindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="#">
                    @csrf
                    <input class="d-none" type="text" id="idpengajuan" name="idpengajuan" value="">
                    <div class="modal-header">
                        <h5 class="modal-title">Formulir Berita Acara Tim Asesor Akreditasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <p class="mb-0">Tanggal Pembuatan Surat</p>
                            <div class="col">
                                <label for="tgl_buat_surat" class="col-form-label">Tanggal</label>
                                <input type="date" class="form-control" value="" id="tgl_buat_surat"
                                    name="tgl_buat_surat">
                            </div>
                            <div class="col">
                                <label for="wkt_buat_surat" class="col-form-label">Waktu</label>
                                <input type="time" class="form-control" value="" id="wkt_buat_surat"
                                    name="wkt_buat_surat">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <p class="mb-0">Perbaikan Setelah Visitasi (3 Hari Kerja)</p>
                            <div class="col">
                                <label for="tgl_perbaikan_1" class="form-label">dari</label>
                                <input type="date" class="form-control" value="" id="tgl_perbaikan_1"
                                    name="tgl_perbaikan_1">
                            </div>
                            <div class="col">
                                <label for="tgl_perbaikan_2" class="form-label">sampai</label>
                                <input type="date" class="form-control" value="" id="tgl_perbaikan_2"
                                    name="tgl_perbaikan_2">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <p class="mb-0">Tanggal Penilaian Ulang</p>
                            <small class="text-light fw-semibold mb-2">(Pilih Hari Kerja Setelah Semua Dokumen Perbaikan
                                Diunggah)</small>
                            <div class="col-md-6">
                                <label for="tgl_regrade" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" value="" id="tgl_regrade"
                                    name="tgl_regrade">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Buat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Pelatihan -->
    <x-modal-lihatPelatihan :pengajuans="$pengajuans" />
@endsection

@push('scripts')
    <script>
    //     new DataTable('#example', {
    //     responsive: false, // disable, biar scroll biasa
    //     scrollX: true,
    //     autoWidth: false,
    // });
        new DataTable('#example', {
        scrollX: true
    });
    </script>
    <script>
        $(document).ready(function() {
            $(".btn-ba").click(function() {

                var id_pengajuan = $(this).attr("data-id-pengajuan");
                alert(id_pengajuan)
                $('#idpengajuan').val(id_pengajuan.toString());
            });
        });
    </script>
@endpush
