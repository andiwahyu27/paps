<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                <input type="hidden" name="id_pengajuan" value="{{ $pengajuan->id }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <input type="file" class="form-control" id="{{ $inputName }}"
                                name="{{ $inputName }}" accept="application/pdf">
                        </div>
                    </div>
                    @if($modalId === 'uploadBAModal')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tgl_dibuka" class="form-label">Tanggal Pengajuan Dibuka Kembali</label>
                            <input type="date" class="form-control" id="tgl_dibuka" name="tgl_dibuka" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tgl_ditutup" class="form-label">Tanggal Pengajuan Ditutup</label>
                            <input type="date" class="form-control" id="tgl_ditutup" name="tgl_ditutup" required>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
