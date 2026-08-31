<div class="row g-4">
    <div class="col-md-6">
        <h6 class="text-primary">Hal-hal yang harus dipertahankan</h6>
        @if ($dipertahankan->isNotEmpty())
            <ol>
                @foreach ($dipertahankan as $item)
                    <li class="mb-2">{{ $item->isi }}</li>
                @endforeach
            </ol>
        @else
            <p class="text-muted">Belum ada rekomendasi.</p>
        @endif
    </div>
    <div class="col-md-6">
        <h6 class="text-primary">Hal-hal yang harus diperbaiki</h6>
        @if ($diperbaiki->isNotEmpty())
            <ol>
                @foreach ($diperbaiki as $item)
                    <li class="mb-2">{{ $item->isi }}</li>
                @endforeach
            </ol>
        @else
            <p class="text-muted">Belum ada rekomendasi.</p>
        @endif
    </div>
</div>
