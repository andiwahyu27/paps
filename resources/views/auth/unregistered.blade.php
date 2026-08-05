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
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                Kembali ke halaman login
            </a>
        </div>
    </div>
</div>
@endsection
