@extends('layouts.app-auth')

@section('content')
<div class="text-center mb-4">
    <h3>🔐 Sekretariat</h3>
    <p class="text-muted">Platform Akreditasi PAPS</p>
</div>

<form method="POST" action="{{ route('sekrelogin') }}" class="mb-3">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="sekre2600@pusdiklat.id" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <button type="submit" class="btn btn-primary w-100">Masuk</button>
</form>

<div class="text-center mt-3">
    <a href="/login" class="text-muted small">← Login biasa</a>
</div>
@endsection
