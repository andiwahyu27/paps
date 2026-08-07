@extends('layouts.app-auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="mb-2">Akun Belum Terdaftar 🔒</h4>
        <p class="mb-4">
            Akun Anda belum terdaftar di sistem PAPS.
            Hubungi tim sekretariat akreditasi untuk mendaftarkan akun.
        </p>
        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center mx-auto px-4">
                    <i class="bx bx-arrow-back me-2"></i>
                    Kembali ke halaman login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
