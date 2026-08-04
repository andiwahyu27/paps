@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Unsur Kelembagaan /</span> {{ $step_name }}</h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('profile.penyelenggaraan', 1) }}"><button type="button"
                                    class="nav-link @if ($step == 1) active @endif">
                                    <i class="tf-icons bx bx-home"></i> Program Pelatihan
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.penyelenggaraan', 2) }}"><button type="button"
                                    class="nav-link @if ($step == 2) active @endif">
                                    <i class="tf-icons bx bx-customize"></i> Penjamin Mutu
                                </button></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                <input type="text" name="id_profile" value="{{ $profile->id }}" hidden />
                                @method('PUT')
                                @csrf
                                @switch($step)
                                    @case(1)
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>Unsur Program Kerja</h5>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-surencana">Dokumen Perencanaan Kegiatan
                                                        Organisasi</label>
                                                    <input type="file" class="form-control" id="file-surencana"
                                                        name="path_rencana_keiatan" accept="application/pdf" />
                                                    @if ($profile->path_rencana_keiatan != null)
                                                        <a href="{{ asset($profile->path_rencana_keiatan) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-sukaldik">Kalender Diklat</label>
                                                    <input type="file" class="form-control" id="file-sukaldik"
                                                        name="path_kegiatan_diklat" accept="application/pdf" />
                                                    @if ($profile->path_kegiatan_diklat != null)
                                                        <a href="{{ asset($profile->path_kegiatan_diklat) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h5>Unsur Pembiayaan</h5>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-subiaya">SBM/standar biaya pelatihan sesuai ketentuan berlaku</label>
                                                    <input type="file" class="form-control" id="file-subiaya"
                                                        name="path_pembiayaan" accept="application/pdf" />
                                                    @if ($profile->path_pembiayaan != null)
                                                        <a href="{{ asset($profile->path_pembiayaan) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    @case(2)
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>Sub Unsur Standar Mutu</h5>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-soprencana">SOP Perencanaan
                                                        Pelatihan</label>
                                                    <input type="file" class="form-control" id="file-soprencana"
                                                        name="path_sop_perencanaan" accept="application/pdf" />
                                                    @if ($profile->path_sop_perencanaan != null)
                                                        <a href="{{ asset($profile->path_sop_perencanaan) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-soplaksana">SOP Pelaksanaan
                                                        Pelatihan</label>
                                                    <input type="file" class="form-control" id="file-soplaksana"
                                                        name="path_sop_pelaksanaan" accept="application/pdf" />
                                                    @if ($profile->path_sop_pelaksanaan != null)
                                                        <a href="{{ asset($profile->path_sop_pelaksanaan) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-sopevalap">SOP Evaluasi & Pelaporan
                                                        Pelatihan</label>
                                                    <input type="file" class="form-control" id="file-sopevalap"
                                                        name="path_sop_evalap" accept="application/pdf" />
                                                    @if ($profile->path_sop_evalap != null)
                                                        <a href="{{ asset($profile->path_sop_evalap) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-buktisosialisasisop">Bukti sosialisasi SOP</label>
                                                    <input type="file" class="form-control" id="file-buktisosialisasisop"
                                                        name="path_bukti_sosialisasi_sop" accept="application/pdf" />
                                                    @if ($profile->path_bukti_sosialisasi_sop != null)
                                                        <a href="{{ asset($profile->path_bukti_sosialisasi_sop) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="file-laporanpenjaminanmutu">Laporan Penjaminan Mutu</label>
                                                    <input type="file" class="form-control" id="file-laporanpenjaminanmutu"
                                                        name="path_laporan_penjaminan_mutu" accept="application/pdf" />
                                                    @if ($profile->path_laporan_penjaminan_mutu != null)
                                                        <a href="{{ asset($profile->path_laporan_penjaminan_mutu) }}" target="_blank"><i
                                                                class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @break
                                @endswitch
                                @if ($profile->is_lock == 0)
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                @else
                                    <button class="btn btn-primary" disabled>Simpan</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pills -->
    </div>
@endsection
