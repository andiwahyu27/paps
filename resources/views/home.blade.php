@extends('layouts.app-auth')

@section('content')
<h2>Selamat datang, {{ $name }}! 🎉</h2>
<p>Login berhasil. Role: 
    @if($role == 2) Sekretariat
    @elseif($role == 3) Asesor
    @elseif($role == 4) Lembaga
    @else User
    @endif
</p>
<a href="/login" class="btn btn-secondary">Logout</a>
