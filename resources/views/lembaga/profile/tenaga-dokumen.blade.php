@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a
                    href="{{ route('profile.tenaga', ['step' => $step]) }}">Unsur
                    Tenaga Kediklatan /
                    {{ $step_name }}</a></span> / Dokumen Pendukung</h4>
        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            @foreach ($mtdocs as $mtdoc)
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">{{ $mtdoc->nama }}</h5>
                                            @if ($profile->is_lock == 0)
                                                <button type="button" class="btn btn-sm btn-info float-end btn-add-modal"
                                                    data-bs-toggle="modal" data-id-doc="{{ $mtdoc->id }}"
                                                    data-title="{{ $mtdoc->nama }}"
                                                    data-bs-target="#addDokumenModal">Tambah</button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-info float-end disabled">Tambah</button>
                                            @endif
                                        </div>
                                        <div class="table-responsive text-nowrap">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Dokumen</th>
                                                        <th>Tipe</th>
                                                        <th>Timestamp</th>
                                                        <th class="float-end">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @php
                                                        $counter = 1;
                                                        $dataFound = false;
                                                    @endphp
                                                    @foreach ($docs as $doc)
                                                        @if ($mtdoc->id == $doc->tenaga_dokumen_id)
                                                            @php $dataFound = true; @endphp
                                                            <tr>
                                                                <td>{{ $counter++ }}</td>
                                                                <td>{{ $doc->nama }}</td>
                                                                <td>{{ $doc->tipe }} </td>
                                                                <td>{{ $doc->updated_at->format('j-m-Y G:H:s') }}
                                                                </td>
                                                                <td>
                                                                    @if ($profile->is_lock == 0)
                                                                        <div class="btn-group float-end" role="button">
                                                                            <a role="button"
                                                                                href="{{ asset($doc->path_dokumen) }}"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                target="_blank"><i
                                                                                    class="bx bxs-show"></i></a>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-primary btn-edit-modal"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#editDokumenModal"
                                                                                data-id-doc="{{ $doc->id }}"><i
                                                                                    class="bx bxs-pencil"></i></button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-primary btn-del-modal"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#hapusDokumenModal"
                                                                                data-id-doc="{{ $doc->id }}"><i
                                                                                    class="bx bx-trash"></i></button>
                                                                        </div>
                                                                    @else
                                                                        <div class="btn-group float-end" role="button">
                                                                            <a role="button"
                                                                                href="{{ asset($doc->path_dokumen) }}"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                target="_blank"><i
                                                                                    class="bx bxs-show"></i></a>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    @if (!$dataFound)
                                                        <tr>
                                                            <td colspan="5">
                                                                <p class="text-center">
                                                                    <span class="badge bg-label-secondary">Data belum
                                                                        ditambahkan</span>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                            <div class="modal fade" id="addDokumenModal" tabindex="-1" style="display: none;"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('add.modal.post') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="tenaga_id" id="tenaga_id"
                                                value="{{ $id }}">
                                            <input type="hidden" name="tenaga_dokumen_id" id="tenaga_dokumen_id">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="title">Judul Dokumen Bukti Dukung</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label for="nama" class="form-label">Nama Dokumen</label>
                                                        <input type="text" name="nama" id="nama"
                                                            class="form-control" placeholder="Nama Dokumen">
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label for="file-dokumen" class="form-label">File
                                                            Dokumen</label>
                                                        <input type="file" name="file-dokumen" id="file-dokumen"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Tambah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editDokumenModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('edit.modal.update') }}" method="post"
                                            enctype="multipart/form-data">
                                            @method('PUT')
                                            @csrf
                                            <input type="hidden" name="tenaga_id" id="tenaga_id"
                                                value="{{ $id }}">
                                            <input type="hidden" name="id_doc" id="id_doc">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Dokumen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label for="nama" class="form-label">Nama
                                                            Dokumen</label>
                                                        <input type="text" name="nama" id="nama"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <label for="file-dokumen" class="form-label">File
                                                            Dokumen</label>
                                                        <input type="file" name="file-dokumen" id="file-dokumen"
                                                            class="form-control">
                                                    </div>
                                                    <a href="#" target="_blank" id="path_dok"><i
                                                            class="bx bxs-file-pdf"></i>Lihat File</a>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">
                                                    Batal
                                                </button>
                                                <button type="submit" class="btn btn-warning">Ubah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="hapusDokumenModal" tabindex="-1" style="display: none;"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('delete.modal') }}" method="post"
                                            enctype="multipart/form-data">
                                            @method('delete')
                                            @csrf
                                            <input type="hidden" name="tenaga_id" id="tenaga_id"
                                                value="{{ $id }}">
                                            <input type="hidden" name="id_doc_del" id="id_doc_del">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Dokumen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin akan menghapus data ini?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">
                                                    Batal
                                                </button>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col text-end">
                <a class="btn btn-primary" href="{{ route('profile.tenaga', ['step' => $step]) }}"><i class='bx bxs-chevron-left' ></i> Kembali</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Add Modal Script --}}
    <script>
        $(".btn-add-modal").click(function() {
            var tenaga_dokumen_id = $(this).attr("data-id-doc");
            var title = $(this).attr("data-title");
            $('#title').html(title);
            $('#tenaga_dokumen_id').val(tenaga_dokumen_id);
        });
    </script>

    {{-- Edit Modal Script --}}
    <script>
        var url = '{{ route('edit.modal.get') }}';
        $(".btn-edit-modal").click(function() {
            var id_doc = $(this).attr("data-id-doc");
            $('#id_doc').val(id_doc);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_doc: id_doc
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    console.log(result);
                    if (JSON.stringify(result) === '{}') {
                        $("[name=nama]").val('');
                        $("[id=path_dok]").attr('href', '#');
                    } else {
                        $("[name=nama]").val([result['nama']]);
                        $("[id=path_dok]").attr('href', [result['path']]);
                    }
                }
            });
        });
    </script>

    {{-- Hapus Modal Script --}}
    <script>
        $(".btn-del-modal").click(function() {
            var id_doc_del = $(this).attr("data-id-doc");
            $('#id_doc_del').val(id_doc_del);
            console.log(id_doc_del);
        });
    </script>

    <script>
        $('#textSubmit').keyup(function() {
            var val = $.trim(this.value);
            if (val == 'NILAI') {
                $('#btn-submit').removeAttr("disabled");
            } else {
                $('#btn-submit').addAttr("disabled");
            }
        })
    </script>
@endpush
