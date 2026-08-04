{{-- Riwayat Jabatan --}}
<div class="modal fade" id="showJabatanModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex">
                    <h5 class="modal-title">Riwayat Jabatan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4">
                <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jabatan</th>
                                <th>Tugas</th>
                                <th>Rentang Waktu</th>
                                <th>Instansi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($tenaga->r_jabatans) < 1)
                                <tr>
                                    <td colspan="6">
                                        <p class="text-center"><span class="badge bg-label-secondary">Riwayat
                                                Jabatan belum ditambahkan</span></p>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($r_jabatans as $r_jabatan)
                                    @if ($r_jabatan->tenaga_id === $tenaga->id)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $r_jabatan->jabatan }}</td>
                                            <td>{{ $r_jabatan->tugas }}</td>
                                            <td>{{ $r_jabatan->periode }}</td>
                                            <td>{{ $r_jabatan->instansi }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Kerja --}}
<div class="modal fade" id="showKerjaModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex">
                    <h5 class="modal-title">Pengalaman Kerja</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4">
                <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jabatan</th>
                                <th>Tugas</th>
                                <th>Tahun</th>
                                <th>Instansi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($tenaga->r_kerjas) < 1)
                                <tr>
                                    <td colspan="6">
                                        <p class="text-center"><span class="badge bg-label-secondary">Pengalaman
                                                Kerja belum ditambahkan</span></p>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($r_kerjas as $r_kerja)
                                    @if ($r_kerja->tenaga_id === $tenaga->id)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $r_kerja->jabatan }}</td>
                                            <td>{{ $r_kerja->tugas }}</td>
                                            <td>{{ $r_kerja->tahun }}</td>
                                            <td>{{ $r_kerja->instansi }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Pendidikan --}}
<div class="modal fade" id="showPendidikanModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex">
                    <h5 class="modal-title">Riwayat Pendidikan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4">
                <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenjang</th>
                                <th>Perguruan Tinggi - Jurusan</th>
                                <th>Tahun Lulus</th>
                                <th>Kota/Negara</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($tenaga->r_pendidikans) < 1)
                                <tr>
                                    <td colspan="6">
                                        <p class="text-center"><span class="badge bg-label-secondary">Riwayat
                                                Pendidikan belum ditambahkan</span></p>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($r_pendidikans as $r_pendidikan)
                                    @if ($r_pendidikan->tenaga_id === $tenaga->id)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $r_pendidikan->jenjang }}</td>
                                            <td>{{ $r_pendidikan->sekolah }}</td>
                                            <td>{{ $r_pendidikan->tahun }}</td>
                                            <td>{{ $r_pendidikan->kota_negara }}</td>
                                            <td>{{ $r_pendidikan->keterangan }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Pelatihan --}}
<div class="modal fade" id="showPelatihanModal{{ $tenaga->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex">
                    <h5 class="modal-title">Riwayat Pelatihan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4">
                <p class="lead"><strong id="nmtenaga">{{ $tenaga->nama }}</strong></p>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelatihan</th>
                                <th>Penyelenggara/Kota</th>
                                <th>Tahun</th>
                                <th>Sertifikat</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($tenaga->r_pelatihans) < 1)
                                <tr>
                                    <td colspan="6">
                                        <p class="text-center"><span class="badge bg-label-secondary">Riwayat
                                                Pendidikan belum ditambahkan</span></p>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($r_pelatihans as $r_pelatihan)
                                    @if ($r_pelatihan->tenaga_id === $tenaga->id)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $r_pelatihan->pelatihan }}</td>
                                            <td>{{ $r_pelatihan->penyelenggara }}</td>
                                            <td>{{ $r_pelatihan->tahun }}</td>
                                            <td>{{ $r_pelatihan->sertifikasi }}</td>
                                            <td>{{ $r_pelatihan->keterangan }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
