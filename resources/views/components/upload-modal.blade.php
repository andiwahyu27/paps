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
                        <div class="col mb-3">
                            <div class="alert alert-info mb-0">
                                <i class="bx bx-info-circle"></i>
                                Periode pengisian dibuka otomatis:
                                <strong>{{ optional($pengajuan->profile->start_reupload)->isoFormat('D MMMM Y') ?? '-' }}</strong>
                                s/d
                                <strong>{{ optional($pengajuan->profile->end_reupload)->isoFormat('D MMMM Y') ?? '-' }}</strong>
                            </div>
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
