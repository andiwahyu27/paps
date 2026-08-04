<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">{{ $step_name }}</h5>
            @if ($profile->is_lock == 0)
                <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                    data-bs-target="#addPengelolaModal">Tambah</button>
            @else
                <button type="button" class="btn btn-sm btn-info float-end" disabled>Tambah</button>
            @endif
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php $dataFound = false; @endphp
                    @foreach ($tenagas as $tenaga)
                        @php $dataFound = true; @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tenaga->nama }}</td>
                            <td>{{ $tenaga->nip }}</td>
                            <td>{{ $tenaga->jabatan }}</td>
                            <td>
                                <div class="demo-inline-spacing">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                        data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                        data_nama="{{ $tenaga->nama }}"
                                        data-bs-target="#showJabatanModal{{ $tenaga->id }}"
                                        href="javascript:void(0);"><i class="bx bx-user-pin"></i> Riwayat
                                        Jabatan</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                        data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                        data_nama="{{ $tenaga->nama }}"
                                        data-bs-target="#showKerjaModal{{ $tenaga->id }}"
                                        href="javascript:void(0);"><i class="bx bx-briefcase-alt"></i> Pengalaman
                                        Kerja</button>
                                    <a href="{{ route('dokumen.tenaga', ['id' => $tenaga->id, 'step' => $step]) }}"
                                        type="button" class="btn btn-success btn-sm rounded-pill mdl"
                                        data_id="{{ $tenaga->id }}"><i class='bx bxs-cloud-upload'></i> Bukti
                                        Dukung</a>
                                </div>
                                <div class="demo-inline-spacing">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                        data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                        data_nama="{{ $tenaga->nama }}"
                                        data-bs-target="#showPendidikanModal{{ $tenaga->id }}"
                                        href="javascript:void(0);"><i class='bx bxs-graduation'></i> Riwayat
                                        Pendidikan</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill mdl"
                                        data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                        data_nama="{{ $tenaga->nama }}"
                                        data-bs-target="#showPelatihanModal{{ $tenaga->id }}"
                                        href="javascript:void(0);"><i class='bx bx-news'></i> Riwayat Pelatihan</button>
                                    @if ($profile->is_lock == 0)
                                        <button type="button" class="btn btn-warning btn-sm rounded-pill"
                                            data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                            data-bs-target="#editPengelolaModal{{ $tenaga->id }}"
                                            href="javascript:void(0);"><i class="bx bx-pencil" data-bs-toggle="tooltip"
                                                data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                                data-bs-original-title="<span><i class='bx bx-pencil'></i> Edit Pengelola</span>"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-pill"
                                            data-bs-toggle="modal" data_id="{{ $tenaga->id }}"
                                            data-bs-target="#deletePengelolaModal{{ $tenaga->id }}"
                                            href="javascript:void(0);"><i class="bx bx-trash" data-bs-toggle="tooltip"
                                                data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                                data-bs-original-title="<span><i class='bx bx-trash'></i> Hapus Pengelola</span>"></i></button>
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm rounded-pill" disabled><i
                                                class="bx bx-pencil" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                data-bs-placement="top" data-bs-html="true"
                                                data-bs-original-title="<span><i class='bx bx-pencil'></i> Edit Pengelola</span>"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-pill" disabled><i
                                                class="bx bx-trash" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                data-bs-placement="top" data-bs-html="true"
                                                data-bs-original-title="<span><i class='bx bx-trash'></i> Hapus Pengelola</span>"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if (!$dataFound)
                        <tr>
                            <td colspan="5">
                                <p class="text-center">
                                    <span class="badge bg-label-secondary">Data belum ditambahkan</span>
                                </p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        var urlGetModal = '{{ route('tenaga.modal') }}';
        $(".mdl").click(function() {
            var idtenaga = $(this).attr("data_id");
            var nmtenaga = $(this).attr("data_nama");
            $('#nmtenaga').html(nmtenaga);
            $('#idtenaga').val(idtenaga);

            $.ajax({
                url: urlGetModal,
                type: "POST",
                data: {
                    idtenaga: idtenaga
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    console.log(result);
                    if (JSON.stringify(result) === '{}') {
                        $("[name=r_jabatans]").val([]);
                    } else {
                        $("[name=r_jabatans]").val([result]);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endpush
