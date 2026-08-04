<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="description" content="" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/favicon.ico') }}" />


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/fonts/boxicons.css') }}" />
    {{-- <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'> --}}

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/core.css') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('/sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.css" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .required::after {
            content: ' *';
            color: red;
        }

        .table-responsive {
            overflow: visible !important;
            position: relative;
        }

        .table-responsive>table {
            overflow-x: auto;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            /* muncul di bawah tombol */
            left: 0;
            z-index: 1000;
            /* pastikan di atas konten lain */
        }
    </style>

    <!-- jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!-- Helpers -->
    <script src="{{ asset('/sneat/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('/sneat/assets/js/config.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('icon')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="/" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                viewBox="0 0 563.08 563.08">
                                <defs>
                                    <style>
                                        .cls-1 {
                                            fill: none;
                                        }

                                        .cls-2 {
                                            clip-path: url(#clip-path);
                                        }

                                        .cls-3 {
                                            fill: url(#linear-gradient);
                                        }

                                        .cls-4 {
                                            clip-path: url(#clip-path-2);
                                        }

                                        .cls-5 {
                                            fill: url(#linear-gradient-2);
                                        }
                                    </style>
                                    <clipPath id="clip-path">
                                        <path class="cls-1"
                                            d="M344.54,218.54a89.1,89.1,0,0,0-151.18,50.22v25.56a89.1,89.1,0,1,0,151.18-75.78M132.16,132.16A211.26,211.26,0,0,0,117.3,414.42V281.54a164.22,164.22,0,1,1,76.06,138.57v53.45a211.28,211.28,0,1,0-61.2-341.4Z" />
                                    </clipPath>
                                    <linearGradient id="linear-gradient" x1="420.8" y1="123.25" x2="-9.89"
                                        y2="612.8" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#114fee" />
                                        <stop offset="1" stop-color="#db17b9" />
                                    </linearGradient>
                                    <clipPath id="clip-path-2">
                                        <path class="cls-1"
                                            d="M281.54,0a281.54,281.54,0,1,1-88.18,549V473.56a211.4,211.4,0,1,0-76.06-59.14v95.8A281.54,281.54,0,0,1,281.54,0" />
                                    </clipPath>
                                    <linearGradient id="linear-gradient-2" x1="95.66" y1="492.26" x2="489.98"
                                        y2="45.24" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#15b0f8" />
                                        <stop offset="1" stop-color="#16ea9e" />
                                    </linearGradient>
                                </defs>
                                <g id="Layer_2" data-name="Layer 2">
                                    <g id="Layer_1-2" data-name="Layer 1">
                                        <g class="cls-2">
                                            <rect class="cls-3" x="70.28" y="70.28" width="422.51" height="422.51" />
                                        </g>
                                        <g class="cls-4">
                                            <rect class="cls-5" width="563.08" height="563.08" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">PAPS</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Daftar Pengajuan</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('monitoring-evaluasi') ? 'active' : '' }}">
                        <a href="{{ route('monitoring-evaluasi') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                            <div>Monitoring & Evaluasi</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('lembaga') ? 'active' : '' }}">
                        <a href="{{ route('lembaga') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-dock-top"></i>
                            <div>Lembaga</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('pengguna') ? 'active' : '' }}">
                        <a href="{{ route('pengguna') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div>Pengguna</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <h5 class="text-primary" style="margin: 0;">Platform Akreditasi Pelatihan Prakom dan
                                    Statistisi</h5>
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <img src="{{ asset('sneat/assets/img/avatars/1.png') }}" alt
                                                    class="w-px-40 h-auto rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('login') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Toast -->
                    @if (session('success'))
                        @include('components.toast', [
                            'type' => 'success',
                            'message' => session('success'),
                        ])
                    @endif

                    @if (session('error'))
                        @include('components.toast', ['type' => 'error', 'message' => session('error')])
                    @endif

                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            @include('components.toast', ['type' => 'error', 'message' => $error])
                        @endforeach
                    @endif
                    <!-- / Toast -->

                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>, made with ❤️ by
                                <a href="#" target="_blank" class="footer-link fw-bolder">NOC Pusdiklat BPS</a>
                            </div>
                        </div>
                </div>
                </footer>
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    {{-- <script src="{{ asset('sneat/assets/js/ui-modals.js') }}"></script> --}}
    @stack('scripts')

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- script Toast Notification -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const myToastEl = document.getElementById('myToast');
            if (myToastEl) {
                const option = {
                    animation: true,
                    delay: 2000
                };
                const toastBootstrap = new bootstrap.Toast(myToastEl, option);
                toastBootstrap.show();
            }
        });
    </script>
</body>

</html>
