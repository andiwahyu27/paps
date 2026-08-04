@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span>Permohonan Pengajuan Akreditasi {{ $jenis->nama }}</span></h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            <form method="POST" action="{{ route('store.pengajuan') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <h5>Formulir Permohonan Pengajuan Akreditasi Pelatihan</h5>
                                    <div class="col-12">
                                        <input name="jenis" value="{{ $type }}" hidden>
                                        <div class="mb-3">
                                            <label class="form-label required" for="surat-permohonan">Surat
                                                Permohonan</label>
                                            <input type="file" class="form-control" id="surat-permohonan"
                                                name="surat_permohonan" accept="application/pdf" required />
                                                @if(!empty($pengajuan->surat_permohonan))
                                                    <a href="{{ asset($pengajuan->surat_permohonan) }}"
                                                        target="_blank"><i class="bx bxs-file-pdf"></i> Lihat</a>
                                                @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="surat-akreditasi-lembaga">Surat Akreditasi
                                                Lembaga</label>
                                            <input type="file" class="form-control" id="surat-akreditasi-lembaga"
                                                name="surat_akreditasi_lembaga" accept="application/pdf" />
                                                @if(!empty($pengajuan->surat_permohonan))
                                                    <a href="{{ asset($pengajuan->surat_akreditasi_lembaga) }}"
                                                        target="_blank"><i class="bx bxs-file-pdf"></i> Lihat</a>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
