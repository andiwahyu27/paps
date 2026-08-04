{{-- @switch(auth()->user()->role)
    @case(2)
        @extends('layouts.app-asesor')
        @break
    @case(4)
        @extends('layouts.app-lembaga')
@endswitch --}}

@extends('layouts.app-lembaga')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Profile Details</h5>
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                        src="{{ asset('sneat/assets/img/avatars/1.png') }}"
                        alt="user-avatar"
                        class="d-block rounded"
                        height="100"
                        width="100"
                        id="uploadedAvatar"
                        />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload foto akun</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input
                                type="file"
                                id="upload"
                                class="account-file-input"
                                hidden
                                accept="image/png, image/jpeg"
                                />
                            </label>
                            <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <form id="account" method="POST" onsubmit="return false">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input class="form-control" type="text" id="username" name="username" value="{{ Auth::user()->name }}" disabled/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input class="form-control" type="password" value="password" id="password" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="text" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="john.doe@example.com"/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input class="form-control" type="password" value="password" id="confirm-password" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">Phone Number</label>
                                <input type="text" id="phone" name="phone" class="form-control" placeholder="202 555 0111"/>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                        </div>
                    </form>
                </div>
                <!-- /Account -->
            </div>
        </div>
    </div>
</div>
@endsection
