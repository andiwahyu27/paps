@extends('layouts.app-lembaga')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold"><span class="text-muted fw-light"></span>Panduan Penggunaan Aplikasi PAPS</h4>
        <div class="card mb-1">
            <div class="card-body">
                <h5 class="card-title">Daftar Panduan</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>1</td>
                                <td>Panduan Penggunaan Aplikasi untuk Lembaga</td>
                                <td>
                                    <a role="button"
                                        href="https://docs.google.com/document/d/1gRTT8TC4n6MUSop882kbs0-re5vewQMkH6QvAmpZRcM/edit?usp=sharing"
                                        class="btn btn-sm btn-outline-primary" target="_blank"><i class="bx bxs-show"
                                            data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                            data-bs-html="true" title=""
                                            data-bs-original-title="<span>Baca Panduan</span>"></i></a>
                                </td>
                            </tr>
                            @if(auth()->user()->role==3)
                            <tr>
                                <td>2</td>
                                <td>Panduan Penggunaan Aplikasi untuk Asesor</td>
                                <td>
                                    <a role="button"
                                        href="https://docs.google.com/document/d/1JxP3FBt7VG_oAhtKR-kK1Gz-shDvHga3EUqP1zW-S54/edit?usp=sharing"
                                        class="btn btn-sm btn-outline-primary" target="_blank"><i class="bx bxs-show"
                                            data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                            data-bs-html="true" title=""
                                            data-bs-original-title="<span>Baca Panduan</span>"></i></a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
