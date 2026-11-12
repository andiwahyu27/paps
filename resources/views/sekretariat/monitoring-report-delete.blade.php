@if (!($modalOnly ?? false))
<button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
    data-bs-target="#hapus-laporan-{{ $report->uuid }}">Hapus Laporan</button>
@else
<div class="modal fade" id="hapus-laporan-{{ $report->uuid }}" tabindex="-1"
    aria-labelledby="judul-hapus-laporan-{{ $report->uuid }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="judul-hapus-laporan-{{ $report->uuid }}">Konfirmasi Hapus Laporan</h5>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus laporan monitoring berikut?</p>
                <dl class="row mb-3">
                    <dt class="col-sm-3">Lembaga</dt>
                    <dd class="col-sm-9 text-break">{{ $report->profile->nama_lembaga ?? 'Lembaga ini' }}</dd>
                    <dt class="col-sm-3">Program</dt>
                    <dd class="col-sm-9 text-break">{{ $report->jenisPengajuan->nama ?? 'Program ini' }}</dd>
                    <dt class="col-sm-3">Tahun</dt>
                    <dd class="col-sm-9">{{ $report->report_year }}</dd>
                </dl>
                <p class="text-muted mb-0">Jawaban, riwayat kiriman, dan link laporan terkait juga akan dihapus.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('monitoring-evaluasi.delete', $report) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Laporan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
