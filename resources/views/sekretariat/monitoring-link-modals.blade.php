@if (!$link)
    <div class="modal fade" id="generate-{{ $accreditation->profile_id }}-{{ $accreditation->jenis_pengajuan_id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('monitoring-evaluasi.link.generate', [$accreditation->profile_id, $accreditation->jenis_pengajuan_id, $year]) }}" class="modal-content">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Generate Link Monitoring</h5></div>
                <div class="modal-body">
                    <p>{{ $accreditation->profile->nama_lembaga }} — {{ $accreditation->jenisPengajuan->nama }} — {{ $year }}</p>
                    <label class="form-label" for="generate-expiry-{{ $accreditation->profile_id }}-{{ $accreditation->jenis_pengajuan_id }}">Tanggal dan waktu kedaluwarsa (WIB)</label>
                    <input id="generate-expiry-{{ $accreditation->profile_id }}-{{ $accreditation->jenis_pengajuan_id }}" type="datetime-local" name="expires_at" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Link</button>
                </div>
            </form>
        </div>
    </div>
@elseif (!$link->deleted_at)
    <div class="modal fade" id="expiry-{{ $link->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('monitoring-evaluasi.link.expiry', $link) }}" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header"><h5 class="modal-title">Edit Kedaluwarsa</h5></div>
                <div class="modal-body">
                    <label class="form-label" for="expiry-date-{{ $link->id }}">Tanggal dan waktu (WIB)</label>
                    <input id="expiry-date-{{ $link->id }}" type="datetime-local" name="expires_at" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endif
