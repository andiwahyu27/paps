<div class="row">
    <div class="col-6 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Fasilitator Bertugas</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php
                        $counter = 1;
                    @endphp
                    @foreach ($tenaga as $t)
                        @if ($t->jenis_tenaga == 2)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>
                                    <option value="{{ $t->id }}">{{ $t->tenaga->nama }}
                                <td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Pengelola Kelas</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php
                        $counter = 1;
                    @endphp
                    @foreach ($tenaga as $t)
                        @if ($t->jenis_tenaga == 3)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>
                                    <option value="{{ $t->id }}">{{ $t->tenaga->nama }}
                                <td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
