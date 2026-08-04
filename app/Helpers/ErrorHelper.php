<?php

namespace App\Helpers;

if (!function_exists('redirectError')) {
    function redirectError(string $pesan = 'Terjadi kesalahan.', string $judul = 'Error')
    {
        return redirect()->route('error')->withInput([
            'judul' => $judul,
            'pesan' => $pesan
        ]);
    }
}
