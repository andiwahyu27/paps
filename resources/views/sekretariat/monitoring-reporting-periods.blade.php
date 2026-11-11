<div class="card mb-4">
    <div class="card-body">
        <h5>Periode Pelaporan</h5>
        <div class="input-group mb-3">
            <input type="url" id="global-report-url" class="form-control" aria-label="URL pelaporan" readonly
                value="{{ route('monitoring-akreditasi.global') }}">
            <button type="button" class="btn btn-outline-primary" id="copy-global-report-url">Salin Link Pelaporan</button>
        </div>
        <p id="global-report-url-status" role="status" class="small mb-2" hidden></p>
        @if (!$globalPeriod)
            <p class="text-muted">Periode tujuan link global belum ditetapkan.</p>
        @endif
        <form method="POST" action="{{ route('monitoring-evaluasi.period.save') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-2">
                <label for="period-year" class="form-label">Tahun</label>
                <input type="number" id="period-year" name="report_year" min="2020" max="9999" class="form-control"
                    value="{{ old('report_year', $editingPeriod?->report_year ?? $year) }}" required>
                @error('report_year')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="period-opens" class="form-label">Buka (WIB)</label>
                <input type="datetime-local" id="period-opens" name="opens_at" class="form-control"
                    value="{{ old('opens_at', $editingPeriod?->opens_at->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')) }}" required>
                @error('opens_at')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="period-closes" class="form-label">Tutup (WIB)</label>
                <input type="datetime-local" id="period-closes" name="closes_at" class="form-control"
                    value="{{ old('closes_at', $editingPeriod?->closes_at->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')) }}" required>
                @error('closes_at')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-auto">
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" name="activate" id="period-activate" value="1" {{ old('activate') ? 'checked' : '' }}>
                    <label class="form-check-label" for="period-activate">Jadikan tujuan link global</label>
                </div>
                @error('activate')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-auto">
                <button class="btn btn-primary">Simpan Periode</button>
            </div>
        </form>
        @if ($periods->isNotEmpty())
            <div class="table-responsive mt-3">
                <table class="table">
                    <thead><tr><th>Tahun</th><th>Buka (WIB)</th><th>Tutup (WIB)</th><th>Status Jadwal</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr>
                                <td>{{ $period->report_year }}
                                    @if ($globalPeriod?->id === $period->id)<span class="badge bg-primary">Tujuan link global</span>@endif
                                </td>
                                <td>{{ $period->opens_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                                <td>{{ $period->closes_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                                <td>{{ ['upcoming' => 'Belum buka', 'open' => 'Terbuka', 'closed' => 'Ditutup'][$period->stateAt(now())] }}</td>
                                <td><a href="{{ route('monitoring-evaluasi', ['periode' => $period->report_year]) }}">Atur</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
<script>
    document.getElementById('copy-global-report-url').addEventListener('click', async () => {
        const input = document.getElementById('global-report-url');
        const status = document.getElementById('global-report-url-status');
        status.hidden = false;
        try {
            await navigator.clipboard.writeText(input.value);
            status.textContent = 'Link pelaporan disalin.';
        } catch {
            input.focus();
            input.select();
            status.textContent = 'Salin URL dari kolom di atas.';
        }
    });
</script>
