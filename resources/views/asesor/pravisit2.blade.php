@extends('layouts.app-asesor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card">
                    @php
                        $role = auth()->user()->role;
                        // $role : 2 == sekretariat
                        // $role : 3 == asesor
                    @endphp
                    @if($role == 3 && $isHistory)
                        <div class="card-header d-flex justify-content-end">
                            <form action="{{ route('edit.pravisit2', $pengajuan->id) }}" method="POST" target="_blank">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm rounded-pill">
                                    <i class="bx bx-edit"></i> Edit Penilaian
                                </button>
                            </form>
                        </div>
                    @endif
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $isHistory ? 'Hasil Penilaian Pravisitasi 2' : 'Penilaian Pravisitasi 2' }}</h5>
                                <p>{{ $isHistory ? 'Saat ini anda sedang melihat hasil penilaian terhadap,' : 'Saat ini anda sedang melakukan penilaian terhadap,' }}
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 27%;">Program</td>
                                        <td style="width: 2%">:</td>
                                        <td>{{ $pengajuan->id_jenis == 1 ? 'Program Pelatihan Teknis di Bidang Sistem Teknologi Berbasis Komputer' : 'Pelatihan Teknis di Bidang Statistik' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pelatihan</td>
                                        <td>:</td>
                                        <td><button type="button" class="btn rounded-pill btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalLihatPelatihan">
                                                    <i class="bx bx-show"></i> Lihat Daftar Pelatihan
                                                </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Lembaga</td>
                                        <td>:</td>
                                        <td>{{ $pengajuan->profile->nama_lembaga }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td>:</td>
                                        <td>{{ $pengajuan->profile->alamat_lembaga }}
                                    </tr>
                                    <tr>
                                        <td>Identitas Lembaga</td>
                                        <td>:</td>
                                        <td><a href="{{route('identitas.lembaga', $pengajuan->profile->id)}}" class="btn btn-sm rounded-pill btn-info"><i class="bx bx-show"></i> Lihat Profile Lembaga</a></td>
                                    </tr>
                                    <tr>
                                        <td>Dok. Pendukung</td>
                                        <td>:</td>
                                        <td>
                                            <x-tombol-file :path="$pengajuan->surat_permohonan" label="Surat Permohonan" />
                                            <x-tombol-file :path="$pengajuan->surat_akreditasi_lembaga" label="Akreditasi LAN" />
                                            <x-tombol-file :path="$pengajuan->surat_tanggapan_permohonan" label="Surat Tanggapan Permohonan" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('sneat/assets/img/illustrations/building.png') }}" height="140"
                                    alt="View Badge User" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Penilaian --}}
        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card" style="height: 80vh">
                    <div class="table-responsive text-nowrap" style="padding: 10px;">
                        <table class="table table-bordered table-hover">
                            <thead style="background-color:#eefeff;">
                                <tr style="text-align:center;">
                                    <th width=10% rowspan="2"><p style="word-wrap: break-word">Unsur</p></th>
                                    <th width=10% rowspan="2"><p style="word-wrap: break-word">Sub Unsur</p></th>
                                    <th width=3% rowspan="2"><p style="word-wrap: break-word">Kode</p></th>
                                    <th width=40% rowspan="2"><p style="word-wrap: break-word">Item Penilaian</p></th>
                                    @php
                                        $isAssessorComplete =
                                            isset($pengajuan->asesor1->name) &&
                                            isset($pengajuan->asesor2->name) &&
                                            isset($pengajuan->asesor3->name);

                                        $role = auth()->user()->role;
                                    @endphp
                                    <th width="5%" colspan="3">
                                        @if ($isAssessorComplete)
                                            Nilai Asesor Pravisit 1
                                        @else
                                            Nilai Asesor <span class="badge bg-danger">(assign asesor belum
                                                lengkap)</span>
                                        @endif
                                    </th>
                                    @if($role == 3 )
                                    <th width="5%" rowspan="2"><p style="word-wrap: break-word">Bobot</br>Unsur</p></th>
                                    <th width="5%" rowspan="2"><p style="word-wrap: break-word">Bobot</br>Subunsur</p></th>
                                    <th width="5%" rowspan="2"><p style="word-wrap: break-word">Bobot</br>Item</p></th>
                                    <th width=7% rowspan="2">
                                        <p style="word-wrap: break-word">Nilai</br>Pravisit 2</p>
                                    </th>
                                    <th width=7% rowspan="2"><p style="word-wrap: break-word">Aksi</p></th>
                                    @endif
                                </tr>
                                <tr style="text-align:center;">
                                    @foreach (['asesor1', 'asesor2', 'asesor3'] as $asesor)
                                        <th>
                                            @isset($pengajuan->$asesor->name)
                                                Asesor {{ $asesor[strlen($asesor) - 1] }}
                                                <a href="#">
                                                    <i class="bx bxs-info-circle" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                        data-bs-placement="top" data-bs-html="true"
                                                        data-bs-original-title="<span>{{ $pengajuan->$asesor->name }}</span>"></i>
                                                </a>
                                            @else
                                                <span class="badge bg-danger">Asesor
                                                    {{ $asesor[strlen($asesor) - 1] }}</span>
                                            @endisset
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $uu)
                                    @php
                                        $totalItemsUnsur = collect($uu['subunsurs'])->sum(fn($su) => count($su['items']));
                                        $printedUnsur = false;
                                    @endphp
                                    @foreach ($uu['subunsurs'] as $su)
                                        @php
                                            $totalItemsSubunsur = count($su['items']);
                                            $printedSubunsur = false;
                                        @endphp
                                        @foreach ($su['items'] as $item)
                                            @if ($item['id'] == 14)
                                                @continue
                                            @endif
                                            <tr>
                                                @if (!$printedUnsur)
                                                    <td rowspan="{{ $totalItemsUnsur }}">
                                                        {{ $uu['unsur'] }}<br>({{ $uu['bobot_unsur'] }}%)
                                                    </td>
                                                    @php $printedUnsur = true; @endphp
                                                @endif
                                                @if (!$printedSubunsur)
                                                    <td rowspan="{{ $totalItemsSubunsur }}">
                                                        {{ $su['su'] }}<br>({{ $su['bobot_subunsur'] }}%)
                                                    </td>
                                                    @php $printedSubunsur = true; @endphp
                                                @endif
                                                <td style="text-align:center">{{ $item['kode_item'] }}</td>
                                                <td>
                                                    {{ $item['nama_item'] }} ({{ $item['bobot_item'] }}%)
                                                    <a href="{{ route('bukti-dukung', ['pengajuan' => $pengajuan->id, 'kode' => $item['kode_item']]) }}">
                                                        <i class="bx bx-sm bxs-file"></i>
                                                    </a>
                                                </td>
                                                @foreach (['nilaipra_1', 'nilaipra_2', 'nilaipra_3'] as $index => $score)
                                                    <td style="text-align: center">
                                                        @if (isset($pengajuan->{'asesor' . ($index + 1)}->name))
                                                            @if ($item[$score] < 1)
                                                                -
                                                            @else
                                                                {{ $item[$score] }}
                                                                <a type="button" href="#" class="btn-catatan"
                                                                    data-bs-toggle="modal" data-bs-target="#catModal"
                                                                    data-placement="right"
                                                                    title="Buka catatan asesor {{ $index + 1 }}"
                                                                    data-id-item="{{ $item['id'] }}"
                                                                    data-asesor="{{ $pengajuan->{'id_asesor' . ($index + 1)} }}"
                                                                    data-urut="{{ $index + 1 }}">
                                                                    <i class='bx bx-message-rounded-dots'></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach

                                                {{-- Penilaian Asesor --}}
                                                @if($role == 3)
                                                    @if ($loop->parent->first && $loop->first)
                                                        <td rowspan="{{ $totalItemsUnsur }}" style="text-align: center;">
                                                            {{ $uu['nilai_bobot_unsur'] ?? '-' }}
                                                        </td>
                                                    @endif
                                                    @if ($loop->first)
                                                        <td rowspan="{{ $totalItemsSubunsur }}" style="text-align: center;">
                                                            {{ $su['nilai_bobot_subunsur'] ?? '-' }}
                                                        </td>
                                                    @endif
                                                    <td style="text-align: center;">{{ $item['nilai_bobot'] }}</td>
                                                    @foreach (['nilaipra2'] as $index => $score2)
                                                        <td style="text-align: center">
                                                            @if ($item[$score2] < 1)
                                                                -
                                                            @else
                                                                {{ $item[$score2] }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        <span type="button"
                                                            class="badge rounded-pill @if ($item['nilaipra2'] < 1) bg-info @else bg-warning @endif btn-nilai"
                                                            data-bs-toggle="modal" data-bs-target="#nilaiModal"
                                                            data-id-item="{{ $item['id'] }}"
                                                            data-id-title="{{ $item['kode_item'] . ' - ' . $item['nama_item'] }}">
                                                            {{$isHistory ? 'Rekomendasi' : 'Nilai' }}
                                                        </span>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach

                                @if($role == 3)
                                <tr style="background-color:#eefeff">
                                    <td style="text-align:right" colspan="7">Total Nilai Pravisitasi 2: </td>
                                    <td style="text-align:center" colspan="5">
                                        <strong>{{ $nilai_akhir . ' (' . $predikat . ')' }}</strong>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        @if ($pengajuan->ispravisit2()==0)
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary float-sm-right" data-bs-toggle="modal" data-bs-target="#modalSubmit">Submit Nilai</button>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Nilai -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                @if($isHistory)
                <div class="modal-header">
                    <h5 class="modal-title" id="title-item">Nama Unsur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="catatan" class="form-label">catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" disabled></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="rekomendasi" class="form-label">rekomendasi</label>
                            <textarea class="form-control" id="rekomendasi" name="rekomendasi" rows="3" disabled></textarea>
                        </div>
                    </div>
                </div>
                @else
                <form method="POST" action="{{ route('nilai.pra2') }}">
                    @csrf
                    <input type="text" name="id_pengajuan" value="{{ $pengajuan->id }}" hidden>
                    <input type="text" name="id_item" id="id-item" hidden>
                    <div class="modal-header">
                        <h5 class="modal-title" id="title-item">Nama Unsur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <small class="d-block form-label">Beri Nilai Pravisit 2</small>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="nilai" id="nilai4"
                                        value="4" required />
                                    <label class="form-check-label" for="nilai4">4 - Sangat Baik</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="nilai" id="nilai3"
                                        value="3" />
                                    <label class="form-check-label" for="nilai3">3 - Baik</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="nilai" id="nilai2"
                                        value="2" />
                                    <label class="form-check-label" for="nilai2">2 - Cukup</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="nilai" id="nilai1"
                                        value="1" />
                                    <label class="form-check-label" for="nilai1">1 - Kurang</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="catatan" class="form-label">Catatan Asesor</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="rekomendasi" class="form-label">Rekomendasi Asesor</label>
                                <textarea class="form-control" id="rekomendasi" name="rekomendasi" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="catModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('nilai.pra') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="title-item" name="title-item">Catatan Asesor Pravisitasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                {{-- <textarea class="form-control" id="catatan" name="catatan" rows="3" disabled></textarea> --}}
                                <!-- Tambahkan di HTML -->
                                <div id="catatan-display" class="form-control" style="min-height: 80px; border: 1px solid #ddd; padding: 10px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @unless($isHistory)
    <!-- Modal Submit -->
    <div class="modal fade" id="modalSubmit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Submit Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="textSubmit" class="form-label">Konfirmasi Penilaian</label>
                            <input type="text" id="textSubmit" class="form-control" placeholder="Ketik kata 'NILAI'" />
                        </div>
                    </div>
                </div>
                <form action="{{ route('nilai.pra2.submit') }}" method="POST">
                    @csrf
                    <input type="text" name="id" value="{{ $pengajuan->id }}" hidden>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endunless

    <!-- Modal Lihat Pelatihan -->
    <x-modal-lihatPelatihan :pengajuans="collect([$pengajuan])" />
@endsection

@push('scripts')
    <script>
        var urlgetnilai = '{{ route('nilai.pra2.item') }}';
        var idpengajuan = '{{ $pengajuan->id }}';
        $(".btn-nilai").click(function() {
            var iditem = $(this).attr("data-id-item");
            var title = $(this).attr("data-id-title");
            $('#title-item').html(title);
            $('#id-item').val(iditem);
            $.ajax({
                url: urlgetnilai,
                type: "POST",
                data: {
                    idpengajuan: idpengajuan,
                    iditem: iditem
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (JSON.stringify(result) === '{}') {
                        $("[name=nilai]").val([]);
                        $("[name=catatan]").html('');
                        $("[name=rekomendasi]").html('');
                    } else {
                        $("[name=nilai]").val([result.nilai]);
                        $("[name=catatan]").html([result.catatan]);
                        $("[name=rekomendasi]").html([result.rekomendasi]);
                    }
                }
            });
        });
    </script>
    <script>
        var urlgetcatatan = '{{ route('catatan.pra.item') }}';
        var idpengajuan = '{{ $pengajuan->id }}';
        var idjenis = '{{ $pengajuan->id_jenis }}';
        $(".btn-catatan").click(function() {
            var iditem = $(this).attr("data-id-item");
            var asesor = $(this).attr("data-asesor");
            var urut = $(this).attr("data-urut");
            var catatan = $(this).attr("data-catatan");
            var title = 'Catatan Asesor ' + urut;
            $('#id-item').val(iditem);

            $.ajax({
                url: urlgetcatatan,
                type: "POST",
                data: {
                    idpengajuan: idpengajuan,
                    iditem: iditem,
                    asesor: asesor
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (!result || (Array.isArray(result) && result.length === 0) ||
                        (typeof result === 'object' && Object.keys(result).length === 0)) {
                            $('#catatan-display').html(
                            '<p class="text-center"><span class="badge bg-label-secondary">tidak ada catatan asesor</span></p>'
                        ).css('background-color', '');
                        $('[name=title-item]').html(title);
                    } else {
                        $('#catatan-display').html(result)
                            .css('background-color', '#eceef1');
                        $('[name=title-item]').html(title);
                    }
                }
            });
        });
    </script>

    @unless($isHistory)
    <script>
        $('#textSubmit').keyup(function() {
            var val = $.trim(this.value);
            if (val == 'NILAI') {
                $('#btn-submit').removeAttr("disabled");
            } else {
                $('#btn-submit').attr("disabled", true);
            }
        })
    </script>
    @endunless

    <script>
        $(document).ready(function() {
            let tableContainer = $('.table-responsive');
            tableContainer.scrollLeft(tableContainer[0].scrollWidth);
        });
    </script>
@endpush
