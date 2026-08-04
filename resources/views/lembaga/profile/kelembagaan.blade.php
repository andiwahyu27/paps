@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Unsur Kelembagaan /</span> {{ $step_name }}</h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('profile.kelembagaan', 1) }}"><button type="button"
                                    class="nav-link @if ($step == 1) active @endif">
                                    <i class="tf-icons bx bx-home"></i> Identitas Lembaga Penyelenggara PSTK
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.kelembagaan', 2) }}"><button type="button"
                                    class="nav-link @if ($step == 2) active @endif">
                                    <i class="tf-icons bx bx-customize"></i> Dokumen Pendukung
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
                                            <h5>Pernyataan Pimpinan Lembaga</h5>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="nama-pimpinan">Nama Pimpinan</label>
                                                    <input type="text" class="form-control" id="nama-pimpinan"
                                                        name="nama_pimpinan" value="{{ $profile->nama_pimpinan }}"
                                                        placeholder="Nama Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="jabatan-pimpinan">Jabatan Pimpinan</label>
                                                    <input type="text" class="form-control" id="jabatan-pimpinan"
                                                        name="jabatan_pimpinan" value="{{ $profile->jabatan_pimpinan }}"
                                                        placeholder="Jabatan Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="alamat-pimpinan">Alamat Unit Kerja</label>
                                                    <textarea id="alamat-pimpinan" class="form-control" name="alamat_unit_kerja" value="{{ $profile->alamat_unit_kerja }}"
                                                        placeholder="Alamat Lengkap Pimpinan">{{ $profile->alamat_unit_kerja }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="nip-pimpinan">NIP Pimpinan</label>
                                                    <input type="text" class="form-control" id="nip-pimpinan" name="nip_pimpinan"
                                                        value="{{ $profile->nip_pimpinan }}" placeholder="NIP Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="unitkerja-pimpinan">Unit Kerja</label>
                                                    <input type="text" class="form-control" id="unitkerja-pimpinan"
                                                        name="unit_kerja" value="{{ $profile->unit_kerja }}"
                                                        placeholder="Unit Kerja Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="pernyataan-pimpinan">Surat Pernyataan</label>
                                                    <input type="file" class="form-control" id="pernyataan-pimpinan"
                                                        name="path_surat_pernyataan_pimpinan" accept="application/pdf" />
                                                    @if ($profile->path_surat_pernyataan_pimpinan != null)
                                                        <a href="{{ asset($profile->path_surat_pernyataan_pimpinan) }}"
                                                            target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a>
                                                    @else
                                                    <label class="form-label mt-2" for="template-pernyataan">Template: <a href="https://docs.google.com/document/d/1vcD0AkxZbitwqQRTbQt4yETvG5gDs4Tl/edit"
                                                        target="_blank"><i class="bx bxs-file-pdf"></i>unduh file</a></label>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <h5>Identitas Lembaga Penyelenggara PSTK</h5>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="nama-lembaga">Nama Lembaga Pelatihan</label>
                                                    <input type="text" class="form-control" id="nama-lembaga" name="nama_lembaga"
                                                        value="{{ $profile->nama_lembaga }}"
                                                        placeholder="Nama Lembaga Pelatihan" />
                                                </div>
                                                <div class="mb-3">
                                                    <label for="provinsi" class="form-label">Provinsi</label>
                                                    <select class="form-select" id="provinsi" aria-label="Provinsi"
                                                        name="provinsi">
                                                        <option value="">Pilih Provinsi</option>
                                                        @foreach ($provinsi as $p)
                                                            <option value="{{ $p->id }}"
                                                                @if ($profile->provinsi == $p->id) selected @endif>
                                                                {{ $p->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kabkota" class="form-label">Kabupaten/Kota</label>
                                                    <select class="form-select" id="kabkota" aria-label="Kabupaten/Kota"
                                                        name="kabupaten_kota">
                                                        <option value="">Pilih Kabupaten/Kota</option>
                                                        @foreach ($kabkota as $p)
                                                            <option value="{{ $p->id }}"
                                                                @if ($profile->kabupaten_kota == $p->id) selected @endif>
                                                                {{ $p->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="alamat">Alamat</label>
                                                    <textarea id="alamat" class="form-control" name="alamat_lembaga" value="{{ $profile->alamat_lembaga }}"
                                                        placeholder="Alamat Lengkap Lembaga">{{ $profile->alamat_lembaga }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="telepon">Telepon Lembaga</label>
                                                    <input type="text" class="form-control phone-mask" id="telepon"
                                                        name="telepon" value="{{ $profile->telepon }}"
                                                        placeholder="658 799 8941" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="faksimili">Faksimili Lembaga</label>
                                                    <input type="text" class="form-control phone-mask" id="faksimili"
                                                        name="faksimili" value="{{ $profile->faksimili }}"
                                                        placeholder="658 799 8941" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="email">Email Lembaga</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        value="{{ $profile->email }}" placeholder="lembaga@mail.com" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="website">Website Lembaga</label>
                                                    <input type="text" class="form-control" id="website" name="website"
                                                        value="{{ $profile->website }}" placeholder="website.com" />
                                                </div>
                                            </div>
                                        </div>

                                    @break

                                    @case(2)
                                        <div class="row">
                                            <h5>Dokumen Pendukung</h5>
                                            <div class="col-12">
                                                <h6>SK Pendirian Lembaga dan Uraian Tupoksi/SOP/SOTK Lembaga Diklat</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="nosurat-skpemerintah">Nomor</label>
                                                            <input type="text" class="form-control" id="nosurat-skpemerintah"
                                                                name="nomor_sk_pemerintah"
                                                                value="{{ $profile->nomor_sk_pemerintah }}"
                                                                placeholder="Nomor Surat" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="tanggal-skpemerintah">Tanggal</label>
                                                            <input type="date" class="form-control" id="tanggal-skpemerintah"
                                                                name="tanggal_sk_pemerintah"
                                                                value="{{ $profile->tanggal_sk_pemerintah }}"
                                                                placeholder="Tanggal Surat" />
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="tentang-skpemerintah">Tentang</label>
                                                            <input type="text" class="form-control" id="tentang-skpemerintah"
                                                                name="tentang_sk_pemerintah"
                                                                value="{{ $profile->tentang_sk_pemerintah }}"
                                                                placeholder="Tentang Surat" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="file-skpemerintah">File Surat</label>
                                                            <input type="file" class="form-control" id="file-skpemerintah"
                                                                name="path_sk_pemerintah" accept="application/pdf" />
                                                            @if ($profile->path_sk_pemerintah != null)
                                                                <a href="{{ asset($profile->path_sk_pemerintah) }}"
                                                                    target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h6>Uraian Tugas sebagai Lembaga Pelatihan</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="nosurat-izinop">Nomor</label>
                                                            <input type="text" class="form-control" id="nosurat-izinop"
                                                                name="no_surat_izin_operasional"
                                                                value="{{ $profile->no_surat_izin_operasional }}"
                                                                placeholder="Nomor Surat" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="tanggal-izinop">Tanggal</label>
                                                            <input type="date" class="form-control" id="tanggal-izinop"
                                                                name="tanggal_surat_izin_operasional"
                                                                value="{{ $profile->tanggal_surat_izin_operasional }}"
                                                                placeholder="Tanggal Surat" />
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="penerbit-izinop">Diterbitkan
                                                                oleh</label>
                                                            <input type="text" class="form-control" id="penerbit-izinop"
                                                                name="penerbit_surat_izin_operasional"
                                                                value="{{ $profile->penerbit_surat_izin_operasional }}"
                                                                placeholder="Penerbit" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="file-izinop">File Surat</label>
                                                            <input type="file" class="form-control" id="file-izinoph"
                                                                name="path_surat_izin_operasional" accept="application/pdf" />
                                                            @if ($profile->path_surat_izin_operasional != null)
                                                                <a href="{{ asset($profile->path_surat_izin_operasional) }}"
                                                                    target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- <h6>Akte Pendirian dari Notaris (Khusus Non Pemerintah)</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="nosurat-akte">Nomor</label>
                                                            <input type="text" class="form-control" id="nosurat-akte"
                                                                name="nomor_akte_pendirian"
                                                                value="{{ $profile->nomor_akte_pendirian }}"
                                                                placeholder="Nomor Akte" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="tanggal-akte">Tanggal</label>
                                                            <input type="date" class="form-control" id="tanggal-akte"
                                                                name="tanggal_akte_pendirian"
                                                                value="{{ $profile->tanggal_akte_pendirian }}"
                                                                placeholder="Tanggal Akte" />
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="ttd-akte">Ditandatangani oleh</label>
                                                            <input type="text" class="form-control" id="ttd-akte"
                                                                name="ttd_akte_pendirian"
                                                                value="{{ $profile->ttd_akte_pendirian }}"
                                                                placeholder="Penandatangan" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="file-akte">File Akte</label>
                                                            <input type="file" class="form-control" id="file-akte"
                                                                name="path_akte_pendirian" accept="application/pdf" />
                                                            @if ($profile->path_akte_pendirian != null)
                                                                <a href="{{ asset($profile->path_akte_pendirian) }}"
                                                                    target="_blank"><i class="bx bxs-file-pdf"></i>Lihat File</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    @break
                                @endswitch
                                @if ($profile->is_lock == 0)
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                @else
                                    <a href="#" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" data-bs-original-title="Tidak bisa menambahkan data. Silahkan hubungi Tim Sekretariat">Simpan</a>
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

@push('scripts')
    <script>
        $('#provinsi').on('change', function() {
            $.ajax({
                url: "{{ route('data.kabkota') }}",
                type: "POST",
                data: {
                    "id_provinsi": $('#provinsi').val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#kabkota').empty().append(
                        '<option selected value="">Pilih Kabupaten/Kota</option>');
                    $.each(response.data, function(i, item) {
                        $('#kabkota').append($('<option>', {
                            value: item.id,
                            text: item.nama
                        }));
                    });
                }
            });
        });
    </script>
@endpush
