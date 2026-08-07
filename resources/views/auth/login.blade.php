@extends('layouts.app-auth')

@section('content')
<h4 class="mb-2">Selamat datang di PAPS! 👋</h4>
<p class="mb-4">Platform Akreditasi Pelatihan Prakom dan Statistisi</p>

{{-- <form id="formAuthentication" method="POST" action="{{ route('login') }}" class="mb-3" >
    @csrf
    <div class="mb-3">
        <label for="email" class="">Username</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your username" autofocus/>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
            <label class="form-label" for="password">Password</label>
        </div>
        <div class="input-group input-group-merge">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100" type="submit"><i class="fa fa-google"></i>Sign in</button>
    </div>
</form> --}}
<div class="my-3">
    <a href="{{route('login.gojags', ['type' => 'sso'])}}" class="btn btn-primary w-100"><i class='bx bx-log-in-circle me-2' ></i><span>Masuk dengan SSO BPS</span></a>
</div>
<div class="my-3">
    <a href="{{route('login.gojags', ['type' => 'google'])}}" class="btn btn-outline-primary w-100"><i class="bx bxl-google me-2"></i><span>Masuk dengan Google</span></a>
</div>
@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        {{ $errors->first() }}
    </div>
@endif
@endsection
