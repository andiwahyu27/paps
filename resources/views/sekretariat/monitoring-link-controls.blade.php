<div class="d-flex flex-column align-items-start gap-2">
    @if (!$link)
        <span class="badge bg-secondary">Belum dibuat</span>
    @elseif ($link->deleted_at)
        <span class="badge bg-danger">Dihapus</span>
    @elseif ($link->expires_at->lte(now()))
        <span class="badge bg-warning">Kedaluwarsa</span>
    @else
        <span class="badge bg-success">Aktif</span>
    @endif

    <div class="dropdown">
        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">Kelola Link</button>
        <div class="dropdown-menu">
            @if (!$link)
                <form method="POST" action="{{ route('monitoring-evaluasi.link.generate', [$accreditation->profile_id, $accreditation->jenis_pengajuan_id, $year]) }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Buat link (3 hari)</button>
                </form>
                <button type="button" class="dropdown-item" data-bs-toggle="modal"
                    data-bs-target="#generate-{{ $accreditation->profile_id }}-{{ $accreditation->jenis_pengajuan_id }}">Tentukan tanggal</button>
            @else
                @if (!$link->deleted_at && $link->public_url)
                    <button type="button" class="dropdown-item"
                        onclick="navigator.clipboard.writeText(@js($link->public_url))">Salin Link</button>
                @endif
                <form method="POST" action="{{ route('monitoring-evaluasi.link.regenerate', $report) }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Generate Ulang</button>
                </form>
                @if (!$link->deleted_at)
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('monitoring-evaluasi.link.delete', $link) }}"
                        onsubmit="return confirm('Hapus link ini? Laporan dan jawabannya tetap tersimpan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">Hapus Link</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
