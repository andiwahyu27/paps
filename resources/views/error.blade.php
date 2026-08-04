@extends('layouts.app-auth')

@section('content')
<div class="container">
    <div class="misc-wrapper">
        <h3 class="mb-2 mx-2">⚠️ {{$judul}} </h3>
        <p class="mb-4 mx-2">{{$pesan}}</p>
        <div class="text-center">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                Back to home
            </a>
        </div>
        <p></p>
        <div class="mt-6">
            <img
                src="{{ asset('sneat/assets/img/illustrations/page-misc-error-light.png') }}"
                alt="Error Illustration"
                width="500"
                class="img-fluid"
                data-app-light-img="{{ asset('sneat/assets/img/illustrations/page-misc-error-light.png') }}"
                data-app-dark-img="{{ asset('sneat/assets/img/illustrations/page-misc-error-light.png') }}"
                style="visibility: visible;"
            >
        </div>
    </div>
</div>
@endsection
