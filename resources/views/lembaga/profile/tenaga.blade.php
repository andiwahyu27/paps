@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Unsur Tenaga Kediklatan /</span> {{ $step_name }}
        </h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('profile.tenaga', 1) }}"><button type="button"
                                    class="nav-link @if ($step == 1) active @endif">
                                    <i class="tf-icons bx bx-group"></i> Fasilitator
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.tenaga', 2) }}"><button type="button"
                                    class="nav-link @if ($step == 2) active @endif">
                                    <i class="tf-icons bx bx-customize"></i> Pengelola Pelatihan
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.tenaga', 3) }}"><button type="button"
                                    class="nav-link @if ($step == 3) active @endif">
                                    <i class="tf-icons bx bx-book-reader"></i> Pengelola Kelas
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.tenaga', 4) }}"><button type="button"
                                    class="nav-link @if ($step == 4) active @endif">
                                    <i class="tf-icons bx bx-transfer-alt"></i> Pengelola SI
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.tenaga', 5) }}"><button type="button"
                                    class="nav-link @if ($step == 5) active @endif">
                                    <i class="tf-icons bx bx-book"></i> Analis Kebutuhan Diklat
                                </button></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            @include('lembaga.profile._tabel-tenaga')
                            @include('lembaga.modal.profile-modal')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pills -->
    </div>
@endsection
