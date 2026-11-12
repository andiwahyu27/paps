<button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
    data-bs-target="#hapus-laporan-{{ $report->uuid }}">Hapus Laporan</button>
<div class="modal fade" id="hapus-laporan-{{ $report->uuid }}" tabindex="-1"
    aria-labelledby="judul-hapus-laporan-{{ $report->uuid }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="judul-hapus-laporan-{{ $report->uuid }}">Konfirmasi Hapus Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus laporan monitoring
                <strong>{{ $report->profile->nama_lembaga ?? 'lembaga ini' }}</strong>
                untuk {{ $report->jenisPengajuan->nama ?? 'program ini' }} tahun {{ $report->report_year }}?
                Jawaban, riwayat kiriman, dan link laporan terkait juga akan dihapus.
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
