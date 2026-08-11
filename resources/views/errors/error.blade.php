@php
    $code = isset($exception) && method_exists($exception, 'getStatusCode')
        ? $exception->getStatusCode()
        : 500;
    $titles = [
        403 => 'Forbidden',
        404 => 'Page Not Found',
        419 => 'Session Expired',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];
    $title = $titles[$code] ?? 'Error';
    $messages = [
        403 => 'Anda tidak memiliki hak akses untuk membuka halaman ini.',
        404 => 'Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.',
        419 => 'Sesi Anda telah berakhir. Silakan login kembali untuk melanjutkan.',
        500 => 'Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.',
        503 => 'Layanan sedang tidak tersedia. Silakan coba beberapa saat lagi.',
    ];
    $message = $messages[$code] ?? 'Terjadi kesalahan yang tidak terduga.';
    $isAuthenticated = auth()->check();
    $homeUrl = $isAuthenticated ? route('home') : route('login');
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $code }} | {{ $title }} - PAPS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Public Sans', 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .misc-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(191, 87, 0, 0.15);
            border-top: 6px solid #f7941d;
            padding: 60px 40px;
            max-width: 640px;
            width: 100%;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            line-height: 1.1;
            color: #f7941d;
            letter-spacing: 2px;
        }

        .error-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #4a3728;
            margin: 10px 0 8px;
        }

        .error-message {
            font-size: 1rem;
            color: #8a5a2b;
            margin-bottom: 28px;
            max-width: 420px;
        }

        .btn-home {
            display: inline-block;
            background: #ef6c00;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            padding: 12px 32px;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(239, 108, 0, 0.35);
        }

        .btn-home:hover {
            background: #d35400;
            transform: translateY(-1px);
            color: #fff;
        }

        .error-brand {
            margin-top: 30px;
            font-size: 0.8rem;
            color: #b98a55;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="misc-wrapper">
        <div class="error-code">{{ $code }}</div>
        <div class="error-title">{{ $title }}</div>
        <p class="error-message">{{ $message }}</p>
        <a href="{{ $homeUrl }}" class="btn-home">
            @if($isAuthenticated)
                Kembali ke Halaman Utama
            @else
                Kembali ke Login
            @endif
        </a>
        <div class="error-brand">PAPS - Platform Akreditasi Pelatihan Prakom &amp; Statistisi</div>
    </div>
</body>
</html>
