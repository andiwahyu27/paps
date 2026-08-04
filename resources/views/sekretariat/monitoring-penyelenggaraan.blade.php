@extends('layouts.app-sekretariat')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold py-3 mb-0">
                    <span class="text-muted fw-light">Monitoring & Evaluasi /</span> Detail Penyelenggaraan
                </h4>
            </div>
            <a href="{{ route('monitoring-evaluasi') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>

        <!-- Informasi Lembaga -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-building me-2"></i>Informasi Lembaga</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lembaga:</label>
                            <p class="mb-0">{{ $lembaga['nama'] ?? 'Pusdiklat Tekfunghan Badiklat Kementerian Pertahanan Republik Indonesia' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lokasi:</label>
                            <p class="mb-0">{{ $lembaga['lokasi'] ?? 'Jakarta' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Akreditasi:</label>
                            <p class="mb-0"><span class="badge bg-success">{{ $lembaga['status'] ?? 'Terakreditasi A' }}</span></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Masa Berlaku:</label>
                            <p class="mb-0">{{ $lembaga['masa_berlaku'] ?? '26 September 2029' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Penyelenggaraan -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-book text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Pelatihan</span>
                        <h3 class="card-title mb-2">{{ count($pelatihan ?? []) ?: 1 }}</h3>
                        <small class="text-primary fw-semibold">Program</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-group text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Peserta</span>
                        <h3 class="card-title mb-2">25</h3>
                        <small class="text-success fw-semibold">Orang</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-star text-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Rata-rata Nilai</span>
                        <h3 class="card-title mb-2">{{ $rataRataNilai ?? 85.7 }}</h3>
                        <small class="text-warning fw-semibold">Skala 100</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-chart text-info" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Evaluasi Penyelenggaraan</span>
                        <h3 class="card-title mb-2">{{ $evaluasiPenyelenggaraan ?? 88.5 }}</h3>
                        <small class="text-info fw-semibold">Skala 100</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Pelatihan -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>Daftar Pelatihan yang Diselenggarakan</h5>
                <div>
                    <button type="button" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-export"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="pelatihanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelatihan</th>
                                <th>Angkatan</th>
                                <th>Tahun</th>
                                <th>Jumlah Peserta</th>
                                <th>Nilai Rata-rata Peserta</th>
                                <th>Evaluasi Penyelenggaraan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dummyPelatihan = [
                                    [
                                        'nama' => 'Pelatihan Fungsional Pranata Komputer Kategori Keahlian Angkatan I Tahun 2025',
                                        'angkatan' => 'I',
                                        'tahun' => '2025',
                                        'peserta' => 25,
                                        'nilai_rata' => 85.7,
                                        'evaluasi' => 88.5,
                                        'status' => 'Selesai'
                                    ]
                                ];
                            @endphp
                            @foreach($dummyPelatihan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $item['nama'] }}</h6>
                                        <small class="text-muted">Program Pelatihan</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">{{ $item['angkatan'] }}</span></td>
                                <td>{{ $item['tahun'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-group me-1 text-primary"></i>
                                        <span class="fw-semibold">{{ $item['peserta'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item['nilai_rata'] >= 85)
                                            <span class="badge bg-success me-2">{{ $item['nilai_rata'] }}</span>
                                            <small class="text-success">Sangat Baik</small>
                                        @elseif($item['nilai_rata'] >= 75)
                                            <span class="badge bg-warning me-2">{{ $item['nilai_rata'] }}</span>
                                            <small class="text-warning">Baik</small>
                                        @else
                                            <span class="badge bg-danger me-2">{{ $item['nilai_rata'] }}</span>
                                            <small class="text-danger">Perlu Perbaikan</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item['evaluasi'] >= 85)
                                            <span class="badge bg-success me-2">{{ $item['evaluasi'] }}</span>
                                            <small class="text-success">Sangat Baik</small>
                                        @elseif($item['evaluasi'] >= 75)
                                            <span class="badge bg-warning me-2">{{ $item['evaluasi'] }}</span>
                                            <small class="text-warning">Baik</small>
                                        @else
                                            <span class="badge bg-danger me-2">{{ $item['evaluasi'] }}</span>
                                            <small class="text-danger">Perlu Perbaikan</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($item['status'] == 'Selesai')
                                        <span class="badge bg-success">{{ $item['status'] }}</span>
                                    @elseif($item['status'] == 'Berlangsung')
                                        <span class="badge bg-warning">{{ $item['status'] }}</span>
                                    @else
                                        <span class="badge bg-info">{{ $item['status'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="bx bx-show me-1"></i>
                                                Detail Pelatihan</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-group me-1"></i>
                                                Daftar Peserta</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-chart me-1"></i>
                                                Laporan Evaluasi</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-download me-1"></i>
                                                Download Sertifikat</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#pelatihanTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": [8]
                }]
            });
        });
    </script>
@endpush
