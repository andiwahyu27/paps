{{-- resources/views/components/modal-pelatihan.blade.php --}}
@props([
    'modalId' => 'modalLihatPelatihan',
    'title' => 'Daftar pelatihan yang diajukan',
    'pengajuans' => collect(),
    'size' => 'lg'
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($pengajuans->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">Belum ada data pelatihan yang diajukan</p>
                    </div>
                @else
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Pelatihan</th>
                                    <th>Angkatan</th>
                                    <th>Tahun</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($pengajuans as $p)
                                    @foreach ($p->pelatihan as $pelatihan)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $pelatihan->nama }}</td>
                                            <td>{{ $pelatihan->angkatan }}</td>
                                            <td>{{ $pelatihan->tahun }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
