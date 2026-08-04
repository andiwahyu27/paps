@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span>Permohonan Pengajuan Akreditasi {{ $pengajuan->jenis->nama }}</span></h4>

        <div class="row">
            @if ($pengajuan->verifikasi_permohonan == 0)
                <div class="col-lg-12 mb-4 order-0">
                    <div class="card">
                        <div class="d-flex align-items-end row">
                            <div class="col-sm-7">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Permohonan Sedang Diverifikasi</h5>
                                    <p class="mb-4">
                                        Permohonan pengajuan lembaga anda telah kami terima, anda akan mendapatkan
                                        notifikasi ketika permohonan telah selesai diverifikasi.
                                    </p>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#modalBatal">Batalkan Permohonan</button>
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
                <div class="col-12">
                    <div class="nav-align-top mb-4">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                                <form method="POST" action="{{ route('update.pengajuan') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <h5>Formulir Permohonan Pengajuan Akreditasi Pelatihan</h5>
                                        <div class="col-12">
                                            <input name="id" value="{{ $pengajuan->id }}" hidden />
                                            <div class="mb-3">
                                                <label class="form-label required" for="surat-permohonan">Surat
                                                    Permohonan</label>
                                                <input type="file" class="form-control" id="surat-permohonan"
                                                    name="surat_permohonan" accept="application/pdf" />
                                                <a href="{{ asset($pengajuan->surat_permohonan) }}" target="_blank"><i
                                                        class="bx bxs-file-pdf"></i>Lihat File</a>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="surat-akreditasi-lembaga">Surat Akreditasi
                                                    Lembaga</label>
                                                <input type="file" class="form-control" id="surat-akreditasi-lembaga"
                                                    name="surat_akreditasi_lembaga" accept="application/pdf" />
                                                @if ($pengajuan->surat_akreditasi_lembaga != null)
                                                    <a href="{{ asset($pengajuan->surat_akreditasi_lembaga) }}"
                                                        target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a><button
                                                        type="button" class="btn btn-sm" style="color: red;"><i
                                                            class="bx bx-trash"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-lg-12 mb-4 order-0">
                    <div class="card">
                        <div class="d-flex align-items-end row">
                            <div class="col-sm-7">
                                <div class="card-body">
                                    @switch($pengajuan->verifikasi_permohonan)
                                        @case(1)
                                            <h5 class="card-title text-primary">Permohonan Pengajuan Akreditasi Disetujui</h5>
                                            <p class="mb-4">
                                                Permohonan pengajuan lembaga anda telah selesai diverifikasi, silakan lanjut ke
                                                proses akreditasi. Jika profile kelembagaan dan kelengkapan pelatihan sudah terisi,
                                                silahkan ajukan akreditasi Anda ke tahap selanjutnya.
                                            </p>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#modalProgres">Lihat Progres</button>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalAkreditasi">Pengajuan Akreditasi</button>
                                        @break

                                        @case(2)
                                            <h5 class="card-title text-danger">Permohonan Dibatalkan</h5>
                                            <p class="mb-4">
                                                Permohonan pengajuan lembaga anda telah dibatalkan.
                                            </p>
                                            <a href="{{ route('pengajuan', ['type' => $pengajuan->id_jenis]) }}"
                                                class="btn btn-sm btn-primary">Buat Pengajuan Baru</a>
                                        @break

                                        @case(3)
                                            <h5 class="card-title text-danger">Permohonan Ditolak</h5>
                                            <p class="mb-4">
                                                Permohonan pengajuan lembaga anda telah ditolak.
                                            </p>
                                            <a href="{{ route('pengajuan', ['type' => $pengajuan->id_jenis]) }}"
                                                class="btn btn-sm btn-primary">Buat Pengajuan Baru</a>
                                        @break
                                    @endswitch
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
                <div class="col-12">
                    <div class="nav-align-top mb-2">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                                <div class="row">
                                    <h5>Formulir Permohonan Pengajuan Akreditasi Pelatihan</h5>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label required" for="surat-permohonan">Surat
                                                Permohonan</label>
                                            <a href="{{ asset($pengajuan->surat_permohonan) }}" target="_blank"><i
                                                    class="bx bxs-file-pdf"></i>Lihat File</a>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="surat-akreditasi-lembaga">Surat Akreditasi
                                                Lembaga</label>
                                            @if ($pengajuan->surat_akreditasi_lembaga != null)
                                                <a href="{{ asset($pengajuan->surat_akreditasi_lembaga) }}"
                                                    target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a>
                                            @else
                                                <span class="badge rounded-pill bg-label-secondary">Tidak ada file</span>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="surat-akreditasi-lembaga">Surat Tanggapan
                                                Permohonan</label>
                                            @if ($pengajuan->surat_tanggapan_permohonan != null)
                                                <a href="{{ asset($pengajuan->surat_tanggapan_permohonan) }}"
                                                    target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a>
                                            @else
                                                <span class="badge rounded-pill bg-label-secondary">Belum ada file</span>
                                            @endif
                                        </div>
                                        @if ($pengajuan->profile->is_lock == 0)
                                            <div class="mb-3">
                                                <a href="{{route('edit.pengajuan', $pengajuan->id_jenis)}}" class="btn btn-sm rounded-pill btn-warning"><i
                                                    class='bx bxs-pencil'></i> Edit Permohonan</a>
                                            </div>
                                        @endif
                                    </div>
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
                                        <h5>Daftar Pengajuan Akreditasi Pelatihan</h5>
                                        <div class="col-12">
                                            <div class="table-responsive text-nowrap" style="padding: 10px;">
                                                <table id="example" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Pelatihan</th>
                                                            <th>Angkatan</th>
                                                            <th>Tahun</th>
                                                            <th>Kelengkapan Pelatihan</th>
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
                                                                    <td>
                                                                        @if ($pengajuan->profile->is_lock == 0)
                                                                            <a href="{{ route('program.akreditasi', $p->id) }}"
                                                                                class="btn btn-sm rounded-pill btn-warning"><i
                                                                                    class='bx bxs-pencil'></i>Entri</a>
                                                                        @else
                                                                            <a href="{{ route('program.akreditasi', $p->id) }}"
                                                                                class="btn btn-sm rounded-pill btn-info"><i
                                                                                    class='bx bxs-show'></i> Lihat
                                                                                data</a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @else
                                                        <tbody class="table-border-bottom-0">
                                                            <tr>
                                                                <td colspan="6" class="text-center">
                                                                    <span
                                                                        class="badge rounded-pill bg-label-secondary">Data
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
                @endif
            @endif
        </div>
        <!-- Pills -->
    </div>

    @if ($pengajuan)
        <div class="modal fade" id="modalBatal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Batalkan Permohonan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="textBatal" class="form-label">Konfirmasi Pembatalan</label>
                                <input type="text" id="textBatal" class="form-control"
                                    placeholder="Ketik kata 'BATAL'" />
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('batal.pengajuan') }}" method="POST">
                        @csrf
                        <input type="text" name="id" value="{{ $pengajuan->id }}" hidden>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger" id="btn-batal" disabled>Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAkreditasi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Pengajuan Akreditasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div>
                                <p>Pastikan Anda sudah memeriksa isian kelengkapan akreditasi Anda sebelum melakukan Pengajuan
                                    Akreditasi</p>
                            </div>
                            <div class="col-md mx-auto text-center">
                                <img src="{{asset('sneat/assets/img/illustrations/sitting-girl-with-laptop.png')}}" height="220" alt="Pengajuan Avatar">
                            </div>
                        </div>
                        @if ($pengajuan->profile->is_lock == 0)
                            <div class="row mt-5">
                                <div class="col mb-3">
                                    <label for="textAkreditasi" class="form-label">Konfirmasi Pengajuan</label>
                                    <input type="text" id="textAkreditasi" class="form-control"
                                        placeholder="Ketik kata 'AKREDITASI'" />
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value=""
                                            id="checkAkreditasi" onclick="checkAkreditasi()" />
                                        <label class="form-check-label" for="checkAkreditasi">
                                            <div class="small fw-semibold">Dengan mengkonfirmasi pengajuan ini, lembaga
                                                tidak dapat mengubah semua isian termasuk Profile Lembaga. Pastikan semua
                                                data telah sesuai dan dapat dipertanggungjawabkan.</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <form action="{{ route('profile.lock') }}" method="POST">
                        @csrf
                        <input type="text" name="id" value="{{ $pengajuan->profile->id }}" hidden>
                        <input type="text" name="is_lock" value="1" hidden>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btn-akreditasi" disabled>Kirimkan</button>
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
                        <h5 class="modal-title" id="modalCenterTitle">Progres Kelengkapan Dokumen Akreditasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <p>Progres kelengkapan dokumen akreditasi tidak harus <span
                                class="badge rounded-pill bg-warning">100%</span>, namun pastikan tidak ada isian yang terlewat</p>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div id="accordionIcon" class="accordion accordion-without-arrow">
                                    <div class="accordion-item card active">
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
                                        <div id="accordionIcon-1" class="accordion-collapse collapse show"
                                            data-bs-parent="#accordionIcon">
                                            <div class="accordion-body">
                                                @if ($progressProfile < 100)
                                                    Berikut isian yang belum lengkap: <br>
                                                    @foreach ($nullProfile as $p)
                                                        <small><span
                                                                class="badge rounded-pill bg-danger mr-2">{{ ucwords(str_replace('_', ' ', str_replace('path', 'File', $p))) }}</span></small>
                                                    @endforeach
                                                @else
                                                    <div class="accordion-body"><span class="badge bg-success">Semua
                                                            dokumen
                                                            lengkap</span></div>
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
@endsection

@push('scripts')
    <script>
        $('#textBatal').keyup(function() {
            var val = $.trim(this.value);
            if (val == 'BATAL') {
                $('#btn-batal').removeAttr("disabled");
            } else {
                $('#btn-akreditasi').attr("disabled", 'disabled');
            }
        })

        function checkAkreditasi() {
            var val = $('#textAkreditasi').val();
            var check = $('#checkAkreditasi').is(":checked");
            if (val == 'AKREDITASI' && check) {
                $('#btn-akreditasi').removeAttr("disabled");
            } else {
                console.log("lohe")
                $('#btn-akreditasi').attr("disabled", 'disabled');
            }
        }

        $('#textAkreditasi').keyup(function() {
            checkAkreditasi();
        })
    </script>
@endpush
