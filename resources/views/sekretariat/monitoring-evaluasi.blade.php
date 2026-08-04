@extends('layouts.app-sekretariat')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Sekretariat /</span> Monitoring & Evaluasi
        </h4>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label">Filter Status Akreditasi</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="terakreditasi-a">Terakreditasi A</option>
                            <option value="terakreditasi-b">Terakreditasi B</option>
                            <option value="belum-terakreditasi">Belum Terakreditasi</option>
                            <option value="ditolak">Akreditasi Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterTahun" class="form-label">Filter Tahun</label>
                        <select class="form-select" id="filterTahun">
                            <option value="">Semua Tahun</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="searchLembaga" class="form-label">Cari Lembaga</label>
                        <input type="text" class="form-control" id="searchLembaga"
                            placeholder="Masukkan nama lembaga...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-award text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Terakreditasi A</span>
                        <h3 class="card-title mb-2">4</h3>
                        <small class="text-success fw-semibold">Lembaga</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-medal text-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Terakreditasi B</span>
                        <h3 class="card-title mb-2">1</h3>
                        <small class="text-warning fw-semibold">Lembaga</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-time text-info" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Belum Terakreditasi</span>
                        <h3 class="card-title mb-2">0</h3>
                        <small class="text-info fw-semibold">Lembaga</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-x-circle text-danger" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Akreditasi Ditolak</span>
                        <h3 class="card-title mb-2">0</h3>
                        <small class="text-danger fw-semibold">Lembaga</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Monitoring & Evaluasi Lembaga</h5>
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
                    <table class="table table-striped" id="monitoringTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lembaga</th>
                                <th>Status Akreditasi</th>
                                <th>Masa Berlaku</th>
                                <th>Nilai Akreditasi</th>
                                <th>Rekomendasi Perbaikan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">Pusdiklat Tekfunghan Badiklat Kementerian Pertahanan Republik Indonesia</h6>
                                            <small class="text-muted">Jakarta</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Terakreditasi A</span></td>
                                <td>26 September 2029</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">3.57</span>
                                        <small class="text-muted">Sangat Baik</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">Pertahankan kualitas program pelatihan</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('monitoring-penyelenggaraan', 1) }}"><i class="bx bx-show me-1"></i>
                                                Detail</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-download me-1"></i>
                                                Download Laporan</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">BPSDMD Provinsi Jawa Tengah</h6>
                                            <small class="text-muted">Jawa Tengah</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Terakreditasi A</span></td>
                                <td>26 September 2029</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">3.59</span>
                                        <small class="text-muted">Sangat Baik</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">Pertahankan kualitas program pelatihan</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('monitoring-penyelenggaraan', 2) }}"><i class="bx bx-show me-1"></i>
                                                Detail</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-download me-1"></i>
                                                Download Laporan</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">BPSDMD Provinsi Kalimantan Selatan</h6>
                                            <small class="text-muted">Kalimantan Selatan</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Terakreditasi B</span></td>
                                <td>26 September 2027</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">3.08</span>
                                        <small class="text-muted">Baik</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">Pertahankan kualitas program pelatihan</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('monitoring-penyelenggaraan', 3) }}"><i class="bx bx-show me-1"></i>
                                                Detail</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-download me-1"></i>
                                                Download Laporan</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">Balai Pelatihan Kesehatan Cikarang</h6>
                                            <small class="text-muted">Jawa Barat</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Terakreditasi A</span></td>
                                <td>26 September 2030</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">3.59</span>
                                        <small class="text-muted">Sangat Baik</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">-</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('monitoring-penyelenggaraan', 4) }}"><i class="bx bx-show me-1"></i>
                                                Detail</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-download me-1"></i>
                                                Download Laporan</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">Pusat Pendidikan dan Pelatihan Keuangan Publik</h6>
                                            <small class="text-muted">Jakarta</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Terakreditasi A</span></td>
                                <td>26 September 2030</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">3.65</span>
                                        <small class="text-muted">Sangat Baik</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">-</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('monitoring-penyelenggaraan', 5) }}"><i class="bx bx-show me-1"></i>
                                                Detail</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            <a class="dropdown-item" href="#"><i class="bx bx-download me-1"></i>
                                                Download Laporan</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
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
            $('#monitoringTable').DataTable({
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
                    "targets": [6]
                }]
            });

            // Filter functionality
            $('#filterStatus').on('change', function() {
                var status = $(this).val();
                if (status) {
                    $('#monitoringTable').DataTable().column(2).search(status).draw();
                } else {
                    $('#monitoringTable').DataTable().column(2).search('').draw();
                }
            });

            $('#searchLembaga').on('keyup', function() {
                $('#monitoringTable').DataTable().column(1).search(this.value).draw();
            });
        });
    </script>
@endpush
