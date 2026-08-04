@extends('layouts.app-sekretariat')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span>Daftar Pengguna</span></h4>

        <div class="row">
            <div class="col-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('pengguna', 4) }}"><button type="button"
                                    class="nav-link @if ($role == 4) active @endif">
                                    <i class="tf-icons bx bx-customize"></i> PIC Lembaga
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pengguna', 3) }}"><button type="button"
                                    class="nav-link @if ($role == 3) active @endif">
                                    <i class="tf-icons bx bx-group"></i> Asesor
                                </button></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pengguna', 2) }}"><button type="button"
                                    class="nav-link @if ($role == 2) active @endif">
                                    <i class="tf-icons bx bxs-user-account"></i> Sekretrariat
                                </button></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-justified-step" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Daftar Pengguna</h5>
                                        <button type="button" class="btn btn-sm btn-info float-end" data-bs-toggle="modal"
                                            data-bs-target="#addPengguna">Tambah</button>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    @if ($role == 4)
                                                        <th>Lembaga</th>
                                                    @endif
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                @foreach ($pengguna as $p)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        @if ($role == 4)
                                                            @isset($p->profile)
                                                                <td>{{ $p->profile->nama_lembaga }}</td>
                                                            @else
                                                                <td><span class="badge text-bg-warning">belum assign lembaga
                                                                    </span></td>
                                                            @endisset
                                                        @endif
                                                        <td>{{ $p->name }}</td>
                                                        <td>{{ $p->email }}</td>
                                                        <td>
                                                            <div class="demo-inline-spacing">
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm rounded-pill"
                                                                    data-bs-toggle="modal" data_id="{{ $p->id }}"
                                                                    data-bs-target="#loginPengguna{{ $p->id }}"
                                                                    href="javascript:void(0);"><i class="bx bxs-show"></i>
                                                                    Login As</button>
                                                                <button type="button"
                                                                    class="btn btn-warning btn-sm rounded-pill"
                                                                    data-bs-toggle="modal" data_id="{{ $p->id }}"
                                                                    data-bs-target="#editPengguna{{ $p->id }}"
                                                                    href="javascript:void(0);"><i class="bx bx-pencil"></i>
                                                                    Edit Pengguna</button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm rounded-pill"
                                                                    data-bs-toggle="modal" data_id="{{ $p->id }}"
                                                                    data-bs-target="#deletePengguna{{ $p->id }}"
                                                                    href="javascript:void(0);"><i class="bx bx-trash"></i>
                                                                    Hapus Pengguna</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" data-bs-backdrop="static"
                                                        id="loginPengguna{{ $p->id }}" tabindex="-1"
                                                    >
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Login Sebagai Pengguna</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Apakah anda yakin akan login sebagai pengguna ini?<br>
                                                                    {{ $p->name }}
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-outline-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        Batal
                                                                    </button>
                                                                    <a href="{{ route('pengguna.login', $p->id) }}">
                                                                        <button type="submit"
                                                                            class="btn btn-warning shadow">Ya,
                                                                            login!</button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" data-bs-backdrop="static"
                                                        id="editPengguna{{ $p->id }}" tabindex="-1"
                                                    >
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form method="POST" action="{{ route('pengguna.ubah') }}">
                                                                    @method('PUT')
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Ubah Data Pengguna</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row g-2 mb-3">
                                                                            <div class="col">
                                                                                <label for="name"
                                                                                    class="form-label">Nama Pengguna</label>
                                                                                <input type="text" name="name"
                                                                                    class="form-control"
                                                                                    value="{{ $p->name }}" />
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="email"
                                                                                    class="form-label">Email</label>
                                                                                <input type="email" name="email"
                                                                                    class="form-control"
                                                                                    value="{{ $p->email }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-outline-secondary"
                                                                            data-bs-dismiss="modal">
                                                                            Batal
                                                                        </button>
                                                                        <button type="submit"
                                                                            class="btn btn-warning">Ubah</button>
                                                                        <input type="hidden" name="id"
                                                                            value="{{ $p->id }}">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" data-bs-backdrop="static"
                                                        data-bs-backdrop="static" id="deletePengguna{{ $p->id }}"
                                                        tabindex="-1">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Hapus Pengguna</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Apakah Anda yakin akan menghapus data pengguna
                                                                        {{ $p->name }}?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        Batal
                                                                    </button>
                                                                    <form method="POST"
                                                                        action="{{ route('pengguna.hapus') }}">
                                                                        @method('delete')
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Hapus</button>
                                                                        <input type="hidden" name="id"
                                                                            value="{{ $p->id }}">
                                                                    </form>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pills -->
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="addPengguna" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('pengguna.tambah') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="name" class="form-label">Nama Pengguna</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama" />
                            </div>
                            <div class="col">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    placeholder="pengelola@mail.com" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" name="password"
                                        placeholder="············" aria-describedby="password">
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                        <input type="hidden" name="role" value="{{ $role }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script async="" defer="" src="https://buttons.github.io/buttons.js"></script>

    <script>
        const passwordField = document.getElementById("password");
        const togglePassword = document.querySelector(".cursor-pointer i");

        togglePassword.addEventListener("click", function() {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.classList.remove("bx-hide");
                togglePassword.classList.add("bx-show");
            } else {
                passwordField.type = "password";
                togglePassword.classList.remove("bx-show");
                togglePassword.classList.add("bx-hide");
            }
        });
    </script>
@endsection
