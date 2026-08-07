<!DOCTYPE html>

<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
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

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/core.css') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('/sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('/sneat/assets/vendor/js/helpers.js') }}"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('/sneat/assets/js/config.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="#" class="app-brand-link gap-2">
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
                                            <linearGradient id="linear-gradient" x1="420.8" y1="123.25"
                                                x2="-9.89" y2="612.8" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#114fee" />
                                                <stop offset="1" stop-color="#db17b9" />
                                            </linearGradient>
                                            <clipPath id="clip-path-2">
                                                <path class="cls-1"
                                                    d="M281.54,0a281.54,281.54,0,1,1-88.18,549V473.56a211.4,211.4,0,1,0-76.06-59.14v95.8A281.54,281.54,0,0,1,281.54,0" />
                                            </clipPath>
                                            <linearGradient id="linear-gradient-2" x1="95.66" y1="492.26"
                                                x2="489.98" y2="45.24" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#15b0f8" />
                                                <stop offset="1" stop-color="#16ea9e" />
                                            </linearGradient>
                                        </defs>
                                        <g id="Layer_2" data-name="Layer 2">
                                            <g id="Layer_1-2" data-name="Layer 1">
                                                <g class="cls-2">
                                                    <rect class="cls-3" x="70.28" y="70.28" width="422.51"
                                                        height="422.51" />
                                                </g>
                                                <g class="cls-4">
                                                    <rect class="cls-5" width="563.08" height="563.08" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </span>
                                <span class="app-brand-text demo text-body fw-bolder">PAPS</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        @yield('content')
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
