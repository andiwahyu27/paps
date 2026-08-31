@extends('layouts.app-asesor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Rekomendasi Hasil Akreditasi</h5>
                    <p class="mb-0 text-muted">{{ $pengajuan->profile->nama_lembaga ?? '-' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('final', $pengajuan->id) }}" class="btn btn-secondary btn-sm rounded-pill">Kembali</a>
                    <a href="{{ route('rekomendasi.hasil.sidang.export.docx', $pengajuan->id) }}" class="btn btn-success btn-sm rounded-pill">
                        Export to DOCX
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3"><strong>Tahun Pengajuan</strong><br>{{ $tahunPengajuan ?: '-' }}</div>
                    <div class="col-md-3"><strong>Jenis Pengajuan</strong><br>{{ $jenisPengajuan }}</div>
                    <div class="col-md-3"><strong>Nilai Final</strong><br>{{ $nilaiFinal ?? '-' }}</div>
                    <div class="col-md-3"><strong>Predikat Final</strong><br>{{ $predikatFinal ?? '-' }}</div>
                </div>

                @if ($submitted)
                    <div class="alert alert-success">Rekomendasi sudah disubmit dan bersifat read-only.</div>
                    @include('asesor.partials.rekomendasi-hasil-list', ['dipertahankan' => $dipertahankan, 'diperbaiki' => $diperbaiki])
                    <form method="POST" action="{{ route('rekomendasi.hasil.sidang.reopen', $pengajuan->id) }}" class="mt-3" onsubmit="return confirm('Buka kembali rekomendasi agar dapat diperbaiki?');">
                        @csrf
                        <button type="submit" class="btn btn-warning">BUKA KEMBALI UNTUK PERBAIKAN</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('rekomendasi.hasil.sidang.store', $pengajuan->id) }}" id="recommendationForm">
                        @csrf
                        @foreach ([
                            'dipertahankan' => ['title' => 'Hal-hal yang harus dipertahankan', 'items' => $dipertahankan],
                            'diperbaiki' => ['title' => 'Hal-hal yang harus diperbaiki', 'items' => $diperbaiki],
                        ] as $category => $section)
                            <section class="mb-4 recommendation-section" data-category="{{ $category }}">
                                <h6 class="text-primary">{{ $section['title'] }}</h6>
                                <div class="recommendation-items">
                                    @forelse ($section['items'] as $item)
                                        <div class="input-group mb-2 recommendation-item">
                                            <textarea name="{{ $category }}[]" class="form-control" rows="2" maxlength="5000" required>{{ $item->isi }}</textarea>
                                            <button type="button" class="btn btn-outline-danger remove-recommendation">Hapus</button>
                                        </div>
                                    @empty
                                        <div class="input-group mb-2 recommendation-item">
                                            <textarea name="{{ $category }}[]" class="form-control" rows="2" maxlength="5000" placeholder="Tulis satu rekomendasi" required></textarea>
                                            <button type="button" class="btn btn-outline-danger remove-recommendation">Hapus</button>
                                        </div>
                                    @endforelse
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm add-recommendation">Tambah Poin</button>
                            </section>
                        @endforeach
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">SIMPAN REKOMENDASI</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('rekomendasi.hasil.sidang.submit', $pengajuan->id) }}" class="mt-2" onsubmit="return confirm('Submit rekomendasi? Setelah submit data menjadi read-only.');">
                        @csrf
                        <button type="submit" class="btn btn-warning">SUBMIT REKOMENDASI</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.recommendation-section').forEach(section => {
            const category = section.dataset.category;
            const container = section.querySelector('.recommendation-items');
            section.querySelector('.add-recommendation').addEventListener('click', () => {
                const item = document.createElement('div');
                item.className = 'input-group mb-2 recommendation-item';
                item.innerHTML = `<textarea name="${category}[]" class="form-control" rows="2" maxlength="5000" placeholder="Tulis satu rekomendasi" required></textarea><button type="button" class="btn btn-outline-danger remove-recommendation">Hapus</button>`;
                container.appendChild(item);
            });
            section.addEventListener('click', event => {
                if (!event.target.classList.contains('remove-recommendation')) return;
                const items = container.querySelectorAll('.recommendation-item');
                if (items.length > 1) event.target.closest('.recommendation-item').remove();
            });
        });
    </script>
@endsection
