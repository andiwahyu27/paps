<!doctype html>
<html lang="id" class="light-style" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring Akreditasi PSTK | PAPS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('/sneat/assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('/sneat/assets/css/demo.css') }}">
    <script src="{{ asset('/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <style>
        body { background: #f5f7fb; font-family: 'Public Sans', sans-serif; }
        .monitoring-public-header { background: #fff; border-bottom: 1px solid #e7e7ef; }
        .monitoring-public-brand { color: #5664e9; letter-spacing: .02em; }
        #monitoring-form { max-width: 1440px; }
        #monitoring-form .card { border: 1px solid #e6e8f0; box-shadow: 0 .125rem .25rem rgba(67, 89, 113, .08); }
        #monitoring-form .nav-pills .nav-link { color: #56657f; border-radius: .65rem; }
        #monitoring-form .nav-pills .nav-link.active { background: #5b50e6; color: #fff; }
        #monitoring-form .card-header { background: #fff; }
        #monitoring-form .form-control:focus, #monitoring-form .form-select:focus { border-color: #696cff; box-shadow: 0 0 0 .15rem rgba(105,108,255,.15); }
        @media (max-width: 991.98px) { #monitoring-form aside { order: 0; } #monitoring-form section { order: 1; } }
    </style>
</head>
<body>
    <header class="monitoring-public-header py-3">
        <div class="container-xxl d-flex align-items-center gap-2">
            <span class="monitoring-public-brand fw-bold">PAPS</span>
            <span class="text-muted">Monitoring Akreditasi PSTK</span>
        </div>
    </header>
    <main>@yield('content')</main>
</body>
</html>
