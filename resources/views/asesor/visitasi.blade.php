@extends('layouts.app-asesor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Visitasi Lembaga Penyelenggara Program Pelatihan</h5>
                                <p>Saat ini anda sedang melihat hasil penilaian terhadap,
                                <table style="width: 100%;">
                                    <tr>
                                        <td>Program</td>
                                        <td>:</td>
                                        <td>{{ $pengajuan->id_jenis == 1 ? 'Program Pelatihan Teknis di Bidang Sistem Teknologi Berbasis Komputer' : 'Pelatihan Teknis di Bidang Statistik' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pelatihan</td>
                                        <td>:</td>
                                        <td><button type="button" class="btn rounded-pill btn-info btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalLihatPelatihan">
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
                                        <td><a href="{{ route('identitas.lembaga', $pengajuan->profile->id) }}"
                                                class="btn btn-sm rounded-pill btn-info"><i class="bx bx-show"></i> Lihat
                                                Profile Lembaga</a></td>
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
                                    <tr>
                                        <td>Generate</br>Berita Acara</td>
                                        <td>:</td>
                                        <td>
                                            <a href="{{ route('ekspor.ba', $pengajuan->id) }}"
                                                class="btn btn-sm rounded-pill btn-primary">
                                                <i class='bx bxs-notepad'></i> Generate BA
                                            </a>
                                            @if ($pengajuan->berita_acara)
                                                <x-tombol-file :path="$pengajuan->berita_acara" label="Berita Acara" />
                                                <button type="button" class="btn btn-sm rounded-pill btn-warning"
                                                    data-bs-toggle="modal" data-bs-target="#uploadBAModal"><i
                                                        class="bx bxs-cloud-upload"></i> Update Berita Acara</button>
                                            @else
                                                <button type="button" class="btn btn-sm rounded-pill btn-warning"
                                                    data-bs-toggle="modal" data-bs-target="#uploadBAModal"><i
                                                        class="bx bxs-cloud-upload"></i> Update Berita Acara</button>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tanda Tangan</br>Online</td>
                                        <td>:</td>
                                        <td><button type="button" class="btn btn-sm rounded-pill btn-danger"
                                                data-bs-toggle="modal" data-bs-target="#confirmSignatureModal"><i
                                                    class='bx bx-pen'></i> Tandatangani</button></td>
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

        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card" style="height: 80vh">
                    <div class="table-responsive text-nowrap" style="padding: 10px;">
                        <table class="table table-bordered table-hover">
                            <thead style="background-color:#eefeff;">
                                <tr style="text-align:center;">
                                    <th width=10% rowspan="2">
                                        <p style="word-wrap: break-word">Unsur</p>
                                    </th>
                                    <th width=10% rowspan="2">
                                        <p style="word-wrap: break-word">Sub Unsur</p>
                                    </th>
                                    <th width=3% rowspan="2">
                                        <p style="word-wrap: break-word">Kode</p>
                                    </th>
                                    <th width=40% rowspan="2">
                                        <p style="word-wrap: break-word">Item Penilaian</p>
                                    </th>
                                    <th width="5%" rowspan="2">
                                        Bobot</br>Unsur
                                    </th>
                                    <th width="5%" rowspan="2">
                                        Bobot</br>Subunsur
                                    </th>
                                    <th width="5%" rowspan="2">
                                        Bobot</br>Item
                                    </th>
                                    <th width=7% rowspan="2">
                                        Nilai</br>Pravisit 2
                                    </th>
                                    <th width=7% rowspan="2">
                                        <p style="word-wrap: break-word">Aksi</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $uu)
                                    @php
                                        $totalItemsUnsur = collect($uu['subunsurs'])->sum(
                                            fn($su) => count($su['items']),
                                        );
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
                                                    <a
                                                        href="{{ route('bukti-dukung', ['pengajuan' => $pengajuan->id, 'kode' => $item['kode_item']]) }}">
                                                        <i class="bx bx-sm bxs-file"></i>
                                                    </a>
                                                </td>
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
                                                <td style="text-align:center">{{ $item['nilaipra2'] }}</td>
                                                <td>
                                                    <span type="button" class="badge rounded-pill bg-warning btn-nilai"
                                                        data-bs-toggle="modal" data-bs-target="#nilaiModal"
                                                        data-id-item="{{ $item['id'] }}"
                                                        data-id-title="{{ $item['kode_item'] . ' - ' . $item['nama_item'] }}">Rekomendasi</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                                <tr style="background-color:#eefeff">
                                    <td style="text-align:right" colspan="4">Total Nilai Pravisitasi 2: </td>
                                    <td style="text-align:center" colspan="5">
                                        <strong>{{ $nilai_akhir . ' (' . $predikat . ')' }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
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
            </div>
        </div>
    </div>

    <div class="modal fade" id="catModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('nilai.pra') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="title-item" name="title-item">Catatan Asesor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <p class="form-control" id="catatan" name="catatan" rows="3"></p>
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

    <!-- Modal Berita Acara -->
    <x-upload-modal modalId="uploadBAModal" title="Unggah Berita Acara" action="{{ route('upload.ba') }}"
        inputName="berita_acara" :pengajuan="$pengajuan" />

    <!-- Modal Lihat Pelatihan -->
    <x-modal-lihatPelatihan :pengajuans="collect([$pengajuan])" />

    <!-- Modal Konfirmasi Tanda Tangan -->
    <div class="modal fade" id="confirmSignatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Data Tanda Tangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="confirmSignatureForm" action="/ttd" method="POST">
                    @csrf
                    <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle"></i>
                            Silakan konfirmasi data berikut sebelum melanjutkan ke halaman tanda tangan digital.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Asesor 1 (Ketua Tim)</strong></label>
                                <input type="text" class="form-control" id="asesor1_name" name="asesor1_name"
                                    value="{{ $pengajuan->asesor1 ? $pengajuan->asesor1->name : '' }}"
                                    placeholder="Masukkan nama Asesor 1 (Ketua Tim)" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Asesor 2</strong></label>
                                <input type="text" class="form-control" id="asesor2_name" name="asesor2_name"
                                    value="{{ $pengajuan->asesor2 ? $pengajuan->asesor2->name : '' }}"
                                    placeholder="Masukkan nama Asesor 2" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Asesor 3</strong></label>
                                <input type="text" class="form-control" id="asesor3_name" name="asesor3_name"
                                    value="{{ $pengajuan->asesor3 ? $pengajuan->asesor3->name : '' }}"
                                    placeholder="Masukkan nama Asesor 3" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Nama Pimpinan Lembaga</strong></label>
                                <input type="text" class="form-control" id="leader_name" name="leader_name"
                                    value="{{ $pengajuan->profile->nama_pimpinan ?? '' }}"
                                    placeholder="Masukkan nama pimpinan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><strong>Jabatan Pimpinan Lembaga</strong></label>
                                <input type="text" class="form-control" id="leader_title" name="leader_title"
                                    value="{{ $pengajuan->profile->jabatan_pimpinan ?? '' }}"
                                    placeholder="Masukkan jabatan pimpinan" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Tanggal Surat *</strong></label>
                                <input type="date" class="form-control" id="letter_date" name="letter_date"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><strong>Waktu Surat *</strong></label>
                                <input type="time" class="form-control" id="signature_time" name="signature_time"
                                    value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Tanggal dan Waktu Tanda Tangan</strong></label>
                                <input type="text" class="form-control" id="signature_datetime" name="signature_datetime"
                                    readonly>
                                <div class="form-text">Otomatis tergenerate dari tanggal dan waktu surat</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Tanggal Surat</strong></label>
                                <input type="text" class="form-control" id="signature_date" name="signature_date"
                                    readonly>
                                <div class="form-text">Otomatis tergenerate dari tanggal surat</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Lanjutkan ke Tanda Tangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to update formatted date and time
        function updateFormattedDateTime() {
            const letterDate = document.getElementById('letter_date').value;
            const signatureTime = document.getElementById('signature_time').value;

            if (letterDate && signatureTime) {
                const date = new Date(letterDate + 'T' + signatureTime);

                // Indonesian day names
                const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                const dayName = dayNames[date.getDay()];
                const day = date.getDate();
                const monthName = monthNames[date.getMonth()];
                const year = date.getFullYear();
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');

                // Format: "Hari Tanggal DD Bulan YYYY, Pukul HH.MM Waktu Indonesia Barat"
                const formattedDateTime = `${dayName} Tanggal ${day} ${monthName} ${year}, Pukul ${hours}.${minutes} Waktu Indonesia Barat`;
                document.getElementById('signature_datetime').value = formattedDateTime;

                // Format: "Jakarta, DD Bulan YYYY"
                const formattedDate = `Jakarta, ${day} ${monthName} ${year}`;
                document.getElementById('signature_date').value = formattedDate;
            }
        }

        // Add event listeners
        document.getElementById('letter_date').addEventListener('change', updateFormattedDateTime);
        document.getElementById('signature_time').addEventListener('change', updateFormattedDateTime);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateFormattedDateTime);

        document.getElementById('confirmSignatureForm').addEventListener('submit', function(e) {
            const signatureDateTime = document.getElementById('signature_datetime').value;
            const signatureDate = document.getElementById('signature_date').value;
            const asesor1Name = document.getElementById('asesor1_name').value;
            const asesor2Name = document.getElementById('asesor2_name').value;
            const asesor3Name = document.getElementById('asesor3_name').value;
            const leaderName = document.getElementById('leader_name').value;
            const leaderTitle = document.getElementById('leader_title').value;

            // Validate all required fields
            if (!signatureDateTime.trim()) {
                alert('Silakan isi tanggal dan waktu tanda tangan.');
                e.preventDefault();
                return;
            }

            if (!signatureDate.trim()) {
                alert('Silakan isi tanggal surat.');
                e.preventDefault();
                return;
            }

            if (!asesor1Name.trim() || !asesor2Name.trim() || !asesor3Name.trim() || !leaderName.trim() || !leaderTitle.trim()) {
                alert('Silakan lengkapi semua data asesor dan pimpinan.');
                e.preventDefault();
                return;
            }
        });
    </script>
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
    {{-- get catatan --}}
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
                    console.log(result);
                    if (JSON.stringify(result) === '{}') {
                        $("[name=catatan]").html(
                            '<p class="text-center"><span class="badge bg-label-secondary">tidak ada catatan</span></p>'
                        );
                        $("[name=title-item]").html(title);
                    } else {
                        $("[name=catatan]").html([result]);
                        $("[name=title-item]").html(title);
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            let tableContainer = $('.table-responsive');
            tableContainer.scrollLeft(tableContainer[0].scrollWidth);
        });
    </script>
@endpush
