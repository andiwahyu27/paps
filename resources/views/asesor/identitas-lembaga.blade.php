@extends('layouts.app-rekap')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> {{ $step_name }}</h4>
        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                                @switch($step)
                                    @case(1)
                                        <div class="row">
                                            <h5>Pernyataan Pimpinan Lembaga</h5>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="nama-pimpinan">Nama Pimpinan</label>
                                                    <input disabled type="text" class="form-control" id="nama-pimpinan"
                                                        name="nama_pimpinan" value="{{ $profile->nama_pimpinan }}"
                                                        placeholder="Nama Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="jabatan-pimpinan">Jabatan Pimpinan</label>
                                                    <input disabled type="text" class="form-control" id="jabatan-pimpinan"
                                                        name="jabatan_pimpinan" value="{{ $profile->jabatan_pimpinan }}"
                                                        placeholder="Jabatan Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="alamat-pimpinan">Alamat Unit Kerja</label>
                                                    <textarea disabled id="alamat-pimpinan" class="form-control" name="alamat_unit_kerja" value="{{ $profile->alamat_unit_kerja }}"
                                                        placeholder="Alamat Lengkap Pimpinan">{{ $profile->alamat_unit_kerja }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="nip-pimpinan">NIP Pimpinan</label>
                                                    <input disabled type="text" class="form-control" id="nip-pimpinan" name="nip_pimpinan"
                                                        value="{{ $profile->nip_pimpinan }}" placeholder="NIP Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="unitkerja-pimpinan">Unit Kerja</label>
                                                    <input disabled type="text" class="form-control" id="unitkerja-pimpinan"
                                                        name="unit_kerja" value="{{ $profile->unit_kerja }}"
                                                        placeholder="Unit Kerja Pimpinan Lembaga" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="pernyataan-pimpinan">Surat Pernyataan</label>
                                                    <input class="form-control" id="pernyataan-pimpinan" disabled/>
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
                                                    <input disabled type="text" class="form-control" id="nama-lembaga" name="nama_lembaga"
                                                        value="{{ $profile->nama_lembaga }}"
                                                        placeholder="Nama Lembaga Pelatihan" />
                                                </div>
                                                <div class="mb-3">
                                                    <label for="provinsi" class="form-label">Provinsi</label>
                                                    <select class="form-select" id="provinsi" aria-label="Provinsi"
                                                        name="provinsi" disabled>
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
                                                        name="kabupaten_kota" disabled>
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
                                                        placeholder="Alamat Lengkap Lembaga" disabled>{{ $profile->alamat_lembaga }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="telepon">Telepon Lembaga</label>
                                                    <input disabled type="text" class="form-control phone-mask" id="telepon"
                                                        name="telepon" value="{{ $profile->telepon }}"
                                                        placeholder="658 799 8941" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="faksimili">Faksimili Lembaga</label>
                                                    <input disabled type="text" class="form-control phone-mask" id="faksimili"
                                                        name="faksimili" value="{{ $profile->faksimili }}"
                                                        placeholder="658 799 8941" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="email">Email Lembaga</label>
                                                    <input disabled type="email" class="form-control" id="email" name="email"
                                                        value="{{ $profile->email }}" placeholder="lembaga@mail.com" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="website">Website Lembaga</label>
                                                    <input disabled type="text" class="form-control" id="website" name="website"
                                                        value="{{ $profile->website }}" placeholder="website.com" />
                                                </div>
                                            </div>
                                        </div>
                                    @break
                                @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pills -->
    </div>
@endsection
