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
                    @if ($role == 3 && $isHistory)
                        <div class="card-header d-flex justify-content-end">
                            <form action="{{ route('edit.final', $pengajuan->id) }}" method="POST" target="_blank">
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
                                <h5 class="card-title text-primary">
                                    {{ $isHistory ? 'Hasil Penilaian Akhir' : 'Penilaian Akhir' }}</h5>
                                <p>{{ $isHistory ? 'Saat ini anda sedang melihat hasil penilaian terhadap,' : 'Saat ini anda sedang melakukan penilaian terhadap,' }}
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
                                        <td>Berita Acara</br>Sidang</td>
                                        <td>:</td>
                                        <td>
                                            @if ((int) $pengajuan->final === 1)
                                                <a href="{{ route('ekspor.ba.sidang', $pengajuan->id) }}"
                                                    class="btn btn-sm rounded-pill btn-primary">
                                                    <i class="bx bxs-notepad"></i> Generate BA Sidang
                                                </a>
                                                @if ($sidangSubmitted)
                                                    <a href="{{ route('ekspor.ba.sidang.ttd', $pengajuan->id) }}"
                                                        class="btn btn-sm rounded-pill btn-success">
                                                        <i class="bx bxs-pen"></i> Generate BA Sidang Hasil TTD
                                                    </a>
                                                @endif
                                            @else
                                                <span class="badge rounded-pill bg-warning text-dark">Submit penilaian
                                                    Sidang Majelis terlebih dahulu</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tanda Tangan</br>BA Sidang</td>
                                        <td>:</td>
                                        <td>
                                            @if ((int) $pengajuan->final === 1)
                                                <button type="button" class="btn btn-sm rounded-pill btn-primary"
                                                    data-bs-toggle="modal" data-bs-target="#confirmSidangSignatureModal">
                                                    <i class="bx bx-pen"></i>
                                                    {{ $pengajuan->ttd_sidang_token ? 'Generate Ulang TTD Sidang' : 'Generate TTD Sidang' }}
                                                </button>
                                            @endif
                                            @if ((int) $pengajuan->final === 1 && $pengajuan->ttd_sidang_token)
                                                <a href="{{ route('ttd.sidang.show', ['token' => $pengajuan->ttd_sidang_token]) }}"
                                                    class="btn btn-sm rounded-pill btn-info">
                                                    <i class="bx bx-show"></i> Lihat TTD Sidang
                                                </a>
                                            @endif
                                            @if ((int) $pengajuan->final !== 1)
                                                <span class="badge rounded-pill bg-warning text-dark">Penilaian Sidang
                                                    Majelis belum disubmit</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Rekomendasi</br>Hasil Akreditasi</td>
                                        <td>:</td>
                                        <td>
                                            <a href="{{ route('ekspor.rekomendasi', $pengajuan->id) }}"
                                                class="btn btn-sm rounded-pill btn-primary">
                                                <i class='bx bxs-notepad'></i> Generate Template
                                            </a>
                                            @if ($pengajuan->rekomendasi_visitasi)
                                                <x-tombol-file :path="$pengajuan->rekomendasi_visitasi" label="Rekomendasi Hasil Akreditasi" />
                                                <button type="button" class="btn btn-sm rounded-pill btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#uploadRekomendasiAkreditasiModal"><i
                                                        class="bx bxs-cloud-upload"></i> Update Rekomendasi</button>
                                            @else
                                                <button type="button" class="btn btn-sm rounded-pill btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#uploadRekomendasiAkreditasiModal"><i
                                                        class="bx bxs-cloud-upload"></i> Update Rekomendasi</button>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Export Data</td>
                                        <td>:</td>
                                        <td>
                                            <form action="{{ route('ekspor.penilaian') }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $pengajuan->id }}">
                                                <input type="hidden" name="jenis_penilaian" value="Final">
                                                <button type="submit" class="btn btn-sm rounded-pill btn-success"
                                                    title="Export Data Final ke Excel">
                                                    <i class='bx bx-download'></i> Export Data Final
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @unless (!$isHistory)
                                        <tr>
                                            <td>Sertifikat</br>Akreditasi</td>
                                            <td>:</td>
                                            <td>
                                                <a href="{{ route('ekspor.sertifikat', $pengajuan->id) }}"
                                                    class="btn btn-sm rounded-pill btn-primary" uploadHasilVisitasiModal>
                                                    <i class='bx bxs-notepad'></i> Generate Sertifikat Akreditasi
                                                </a>
                                                @if ($pengajuan->sertifikat_hasil_akreditasi)
                                                    <x-tombol-file :path="$pengajuan->sertifikat_hasil_akreditasi" label="Sertifikat Hasil Akreditasi" />
                                                    <button type="button" class="btn btn-sm rounded-pill btn-warning"
                                                        data-bs-toggle="modal" data-bs-target="#uploadSertifikatModal"><i
                                                            class="bx bxs-cloud-upload"></i> Update Sertifikat</button>
                                                @else
                                                    <button type="button" class="btn btn-sm rounded-pill btn-warning"
                                                        data-bs-toggle="modal" data-bs-target="#uploadSertifikatModal"><i
                                                            class="bx bxs-cloud-upload"></i> Update Sertifikat</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endunless
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

        <!-- Table Penilaian -->
        <div class="row">
            <div class="col-12 mb-4 order-0">
                <div class="card">
                    <div class="table-responsive text-nowrap" style="padding: 10px;">
                        <table class="table table-bordered table-hover">
                            <thead style="background-color:#eefeff">
                                <tr style="text-align:center;">
                                    <th rowspan="2">
                                        <p style="word-wrap: break-word; margin-bottom:2rem">Unsur</p>
                                    </th>
                                    <th rowspan="2">
                                        <p style="word-wrap: break-word; margin-bottom:2rem">Sub Unsur</p>
                                    </th>
                                    <th rowspan="2">
                                        <p style="word-wrap: break-word; margin-bottom:2rem">Kode</p>
                                    </th>
                                    <th rowspan="2">
                                        <p style="word-wrap: break-word; margin-bottom:2rem">Item Penilaian</p>
                                    </th>
                                    <th colspan="4">Hasil Nilai Paska Visitasi</th>
                                    <th colspan="4">Penilaian Akhir</th>
                                    <th rowspan="2">
                                        <p style="word-wrap: break-word; margin-bottom:2rem">Aksi</p>
                                    </th>
                                </tr>
                                <tr style="text-align:center;">
                                    <th>Bobot</br>Unsur</th>
                                    <th>Bobot</br>Subunsur</th>
                                    <th>Bobot</br>Item</th>
                                    <th>Nilai</br>Paska Visitasi</th>
                                    <th>Bobot</br>Unsur</th>
                                    <th>Bobot</br>Subunsur</th>
                                    <th>Bobot</br>Item</th>
                                    <th>Nilai</br>Final</th>
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
                                                {{-- nilai paska --}}
                                                @if ($loop->parent->first && $loop->first)
                                                    <td rowspan="{{ $totalItemsUnsur }}" style="text-align: center;">
                                                        {{ $uu['nilai_bobot_unsur_paska'] ?? '-' }}
                                                    </td>
                                                @endif
                                                @if ($loop->first)
                                                    <td rowspan="{{ $totalItemsSubunsur }}" style="text-align: center;">
                                                        {{ $su['nilai_bobot_subunsur_paska'] ?? '-' }}
                                                    </td>
                                                @endif
                                                <td style="text-align: center;">{{ $item['nilai_bobot_paska'] }}</td>
                                                <td style="text-align:center">{{ $item['nilaipaska'] }}</td>

                                                {{-- nilai final --}}
                                                @if ($loop->parent->first && $loop->first)
                                                    <td rowspan="{{ $totalItemsUnsur }}" style="text-align: center;">
                                                        {{ $uu['nilai_bobot_unsur_final'] ?? '-' }}
                                                    </td>
                                                @endif
                                                @if ($loop->first)
                                                    <td rowspan="{{ $totalItemsSubunsur }}" style="text-align: center;">
                                                        {{ $su['nilai_bobot_subunsur_final'] ?? '-' }}
                                                    </td>
                                                @endif
                                                <td style="text-align: center;">{{ $item['nilai_bobot_final'] }}</td>
                                                <td style="text-align:center">{{ $item['nilai_final'] }}</td>
                                                <td>
                                                    <span type="button"
                                                        class="badge rounded-pill @if (empty($item['catatan_sidang'])) bg-info @else bg-warning @endif btn-nilai"
                                                        data-bs-toggle="modal" data-bs-target="#nilaiModal"
                                                        data-id-item="{{ $item['id'] }}"
                                                        data-id-title="{{ $item['kode_item'] . ' - ' . $item['nama_item'] }}">
                                                        {{ $isHistory ? 'Rekomendasi' : 'Nilai' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                                {{-- Total Nilai --}}
                                <tr style="background-color:#eefeff">
                                    <td style="text-align:right" colspan="6">Total Nilai Paska Visitasi:</td>
                                    <td style="text-align:center" colspan="2">
                                        <strong>{{ $nilai_paskavisit . ' (' . $predikat_paskavisit . ')' }}</strong>
                                    </td>
                                    <td style="text-align:right" colspan="2">Total Nilai Akhir:</td>
                                    <td style="text-align:center" colspan="3">
                                        <strong>{{ $nilai_final . ' (' . $predikat_final . ')' }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if ($pengajuan->isfinal() == 0)
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-primary float-sm-right" data-bs-toggle="modal"
                        data-bs-target="#modalSubmit">
                        Submit Nilai
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Nilai -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                @if ($isHistory)
                    <div class="modal-header">
                        <h5 class="modal-title" id="title-item">Nama Unsur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="catatan" class="form-label">Catatan Paska Visitasi</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3" disabled></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="rekomendasi" class="form-label">Rekomendasi Paska Visitasi</label>
                                <textarea class="form-control" id="rekomendasi" name="rekomendasi" rows="3" disabled></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="pengecekan_visitasi" class="form-label">Pengecekan Hasil Visitasi</label>
                                <textarea class="form-control" id="pengecekan_visitasi" name="pengecekan_visitasi" rows="3" disabled></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="catatan_sidang" class="form-label">Catatan Sidang Majelis</label>
                                <textarea class="form-control" id="catatan_sidang" name="catatan_sidang" rows="3" disabled></textarea>
                            </div>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('nilai.final') }}">
                        @csrf
                        <input type="text" name="id_pengajuan" value="{{ $pengajuan->id }}" hidden>
                        <input type="text" name="id_item" id="id-item" hidden>
                        <div class="modal-header">
                            <h5 class="modal-title" id="title-item">Nama Unsur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <small class="d-block form-label">Beri Nilai Sidang Majelis</small>
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
                                    <label for="catatan" class="form-label">Catatan Paska Visitasi</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="rekomendasi" class="form-label">Rekomendasi Paska Visitasi</label>
                                    <textarea class="form-control" id="rekomendasi" name="rekomendasi" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="pengecekan_visitasi" class="form-label">Pengecekan Hasil Visitasi</label>
                                    <textarea class="form-control" id="pengecekan_visitasi" name="pengecekan_visitasi" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="catatan_sidang" class="form-label">Catatan Sidang Majelis</label>
                                    <textarea class="form-control" id="catatan_sidang" name="catatan_sidang" rows="3"></textarea>
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

    @unless ($isHistory)
        <!-- Modal Submit -->
        <div class="modal fade" id="modalSubmit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Submit Penilaian Akhir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @unless ($isValid)
                            <div class="alert alert-warning" role="alert">
                                Lengkapi seluruh penilaian item terlebih dahulu sebelum melakukan submit.
                            </div>
                        @endunless
                        <div class="row">
                            <div class="col mb-3">
                                <label for="textSubmit" class="form-label">Konfirmasi Penilaian akhir</label>
                                <input type="text" id="textSubmit" class="form-control"
                                    placeholder="Ketik kata 'NILAI'" />
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('nilai.final.submit') }}" method="POST">
                        @csrf
                        <input type="text" name="id" value="{{ $pengajuan->id }}" hidden>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btn-submit"
                                @unless ($isValid) disabled @endunless>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endunless

    <!-- Modal Rekomendasi Hasil Akreditrasi -->
    <x-upload-modal modalId="uploadRekomendasiAkreditasiModal" title="Unggah Rekomendasi Hasil Akreditasi"
        action="{{ route('upload.rekomendasi') }}" inputName="rekomendasi_visitasi" :pengajuan="$pengajuan" />

    @unless (!$isHistory)
        <!-- Modal Sertifikat -->
        <x-upload-modal modalId="uploadSertifikatModal" title="Unggah Sertifikat Akreditasi"
            action="{{ route('upload.sertifikat') }}" inputName="sertifikat_hasil_akreditasi" :pengajuan="$pengajuan" />
    @endunless

    <!-- Modal Lihat Pelatihan -->
    <x-modal-lihatPelatihan :pengajuans="collect([$pengajuan])" />

    <!-- Modal Metadata Tanda Tangan BA Sidang -->
    <div class="modal fade" id="confirmSidangSignatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Data Tanda Tangan Berita Acara Sidang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ttd.sidang.create.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
                    <div class="modal-body">
                        <div class="alert alert-info">Isi nama dan jabatan tiga aktor Majelis sebelum masuk ke halaman
                            tanda tangan.</div>
                        @foreach ([['ketua_majelis', 'Ketua Majelis', 'Ketua Majelis Akreditasi'], ['sekretaris_majelis', 'Sekretaris Majelis', 'Sekretaris Majelis Akreditasi'], ['anggota_majelis', 'Anggota Majelis', 'Anggota Majelis Akreditasi']] as [$key, $label, $defaultTitle])
                            @php $sidangActor = $sidangSignatures->get($key); @endphp
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>{{ $label }}</strong></label>
                                    <input type="text" class="form-control" name="{{ $key }}_name"
                                        value="{{ $sidangActor->nama_user ?? '' }}"
                                        placeholder="Masukkan nama {{ strtolower($label) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Jabatan</strong></label>
                                    <input type="text" class="form-control" name="{{ $key }}_title"
                                        value="{{ $sidangActor->jabatan_user ?? $defaultTitle }}" required>
                                </div>
                            </div>
                        @endforeach
                        @php $sidangMeta = $sidangSignatures->first(); @endphp
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label"><strong>Nomor Surat *</strong></label>
                                <input type="text" class="form-control" name="nomor_surat"
                                    value="{{ $sidangMeta->nomor_surat ?? '' }}" maxlength="100"
                                    placeholder="Masukkan nomor surat" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Tempat Surat *</strong></label>
                                <input type="text" class="form-control" name="signature_place"
                                    value="{{ $sidangMeta->tempat_surat ?? '' }}" maxlength="100" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><strong>Tanggal Surat *</strong></label>
                                <input type="date" class="form-control" id="sidang_letter_date" name="letter_date"
                                    value="{{ $sidangMeta?->tgl_surat?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Zona Waktu *</strong></label>
                                <select class="form-select" id="sidang_timezone" name="timezone" required>
                                    <option value="Asia/Jakarta">WIB (UTC+7)</option>
                                    <option value="Asia/Makassar">WITA (UTC+8)</option>
                                    <option value="Asia/Jayapura">WIT (UTC+9)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><strong>Waktu Surat *</strong></label>
                                <input type="time" class="form-control" id="sidang_signature_time"
                                    name="signature_time"
                                    value="{{ $sidangMeta?->waktu_surat ? substr((string) $sidangMeta->waktu_surat, 0, 5) : now()->format('H:i') }}"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3"><label class="form-label"><strong>Hari dan Tanggal Surat
                                    Terbilang</strong></label><input type="text" class="form-control"
                                id="sidang_hari_tanggal_surat_preview" readonly><input type="hidden"
                                id="sidang_hari_tanggal_surat" name="hari_tanggal_surat"></div>
                        <div class="mb-3"><label class="form-label"><strong>Tanggal dan Waktu Tanda
                                    Tangan</strong></label><input type="text" class="form-control"
                                id="sidang_signature_datetime" readonly></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button><button type="submit"
                            class="btn btn-primary">Lanjutkan ke Tanda Tangan</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var urlgetnilai = '{{ route('nilai.final.item') }}';
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
                        $("[name=pengecekan_visitasi]").html('');
                        $("[name=catatan_sidang]").val('');
                    } else {
                        $("[name=nilai]").val([result.nilai]);
                        $("[name=catatan]").html([result.catatan]);
                        $("[name=rekomendasi]").html([result.rekomendasi]);
                        $("[name=pengecekan_visitasi]").html([result.pengecekan_visitasi]);
                        $("[name=catatan_sidang]").val(result.catatan_sidang || '');
                    }
                }
            });
        });
    </script>
    <script>
        const sidangWords = ['Nol', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
            'Sepuluh', 'Sebelas'
        ];

        function sidangTerbilang(n) {
            n = Number(n);
            if (n < 12) return sidangWords[n];
            if (n < 20) return sidangTerbilang(n - 10) + ' Belas';
            if (n < 100) return sidangTerbilang(Math.floor(n / 10)) + ' Puluh' + (n % 10 ? ' ' + sidangTerbilang(n % 10) :
                '');
            if (n < 200) return 'Seratus' + (n % 100 ? ' ' + sidangTerbilang(n % 100) : '');
            if (n < 1000) return sidangTerbilang(Math.floor(n / 100)) + ' Ratus' + (n % 100 ? ' ' + sidangTerbilang(n %
                100) : '');
            if (n < 2000) return 'Seribu' + (n % 1000 ? ' ' + sidangTerbilang(n % 1000) : '');
            if (n < 1000000) return sidangTerbilang(Math.floor(n / 1000)) + ' Ribu' + (n % 1000 ? ' ' + sidangTerbilang(n %
                1000) : '');
            return String(n);
        }

        function updateSidangMetadataPreview() {
            const value = document.getElementById('sidang_letter_date')?.value;
            if (!value) return;
            const [year, month, day] = value.split('-').map(Number);
            const date = new Date(year, month - 1, day);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            const hariTanggal =
                `Hari ${days[date.getDay()]} Tanggal ${sidangTerbilang(day)} Bulan ${months[month - 1]} Tahun ${sidangTerbilang(year)}`;
            document.getElementById('sidang_hari_tanggal_surat_preview').value = hariTanggal;
            document.getElementById('sidang_hari_tanggal_surat').value = hariTanggal;
            const time = document.getElementById('sidang_signature_time')?.value || '';
            const timezone = document.getElementById('sidang_timezone')?.value || 'Asia/Jakarta';
            const labels = {
                'Asia/Jakarta': 'Waktu Indonesia Barat',
                'Asia/Makassar': 'Waktu Indonesia Tengah',
                'Asia/Jayapura': 'Waktu Indonesia Timur'
            };
            document.getElementById('sidang_signature_datetime').value =
                `${hariTanggal}, Pukul ${time} ${labels[timezone]}`;
        }
        ['sidang_letter_date', 'sidang_signature_time', 'sidang_timezone'].forEach(id => document.getElementById(id)
            ?.addEventListener('change', updateSidangMetadataPreview));
        document.getElementById('confirmSidangSignatureModal')?.addEventListener('shown.bs.modal',
            updateSidangMetadataPreview);
    </script>

    @unless ($isHistory)
        @if ($isValid)
            <script>
                $('#textSubmit').on('input', function() {
                    var val = $.trim(this.value);
                    $('#btn-submit').prop('disabled', val !== 'NILAI');
                });
            </script>
        @endif
    @endunless

    <script>
        $(document).ready(function() {
            let tableContainer = $('.table-responsive');
            tableContainer.scrollLeft(tableContainer[0].scrollWidth);
        });
    </script>
@endpush
