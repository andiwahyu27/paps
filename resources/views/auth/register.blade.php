@extends('layouts.app-auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="mb-2">Belum ada akses? 🔒</h4>
        <p class="mb-4">Hubungi admin (pusdiklat@bps.go.id) untuk mendapatkan akun instansi.</p>
        {{-- <div class="text-center">
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                Back to login
            </a>
        </div> --}}
    </div>
</div>
@endsection
