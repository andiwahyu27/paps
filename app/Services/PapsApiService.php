<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Klien API integrasi PAPS -> GoJAGS (Pelatihan Akreditasi).
 *
 * Dokumentasi lengkap: dokumen_md/API_PAPS.md
 *
 * Kredensial dibaca dari config('services.paps.*') sehingga harus berada di .env
 * (PAPS_CLIENT_ID, PAPS_CLIENT_SECRET). Service ini HANYA boleh dipanggil dari
 * sisi backend; jangan pernah mengekspos token ke frontend.
 */
class PapsApiService
{
    private const DEFAULT_BASE_URL = 'https://gojags.web.bps.go.id/server/api/v1/paps';

    private string $baseUrl;

    private ?string $token = null;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.paps.base_url', self::DEFAULT_BASE_URL),
            '/'
        );
        $this->timeout = (int) config('services.paps.timeout', 30);

        $clientId = config('services.paps.client_id');
        $clientSecret = config('services.paps.client_secret');

        if (filled($clientId) && filled($clientSecret)) {
            $this->token = base64_encode($clientId . ':' . $clientSecret);
        }
    }

    /**
     * Apakah kredensial sudah tersedia di environment.
     */
    public function isConfigured(): bool
    {
        return $this->token !== null;
    }

    /**
     * Ambil daftar kelas pelatihan berdasarkan UUID akreditasi.
     *
     * @return array{success: bool, http_status: int|null, status: string|null, data: array|null, errors: array|null, message: string|null}
     */
    public function loadPelatihan(string $uuidAkreditasi): array
    {
        return $this->request('GET', '/load/' . rawurlencode($uuidAkreditasi));
    }

    /**
     * Buat kelas pelatihan baru di GoJAGS.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, http_status: int|null, status: string|null, data: array|null, errors: array|null, message: string|null}
     */
    public function storePelatihan(array $payload): array
    {
        return $this->request('POST', '/store', $payload);
    }

    /**
     * Perbarui kelas pelatihan yang sudah ada. Wajib menyertakan uuid_diklat.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, http_status: int|null, status: string|null, data: array|null, errors: array|null, message: string|null}
     */
    public function updatePelatihan(array $payload): array
    {
        return $this->request('POST', '/update', $payload);
    }

    /**
     * Hapus kelas pelatihan. Bersifat permanen di sisi GoJAGS.
     *
     * @return array{success: bool, http_status: int|null, status: string|null, data: array|null, errors: array|null, message: string|null}
     */
    public function deletePelatihan(string $uuidDiklat): array
    {
        return $this->request('POST', '/delete', ['uuid_diklat' => $uuidDiklat]);
    }

    /**
     * Eksekusi request ke GoJAGS dan normalisasi hasilnya.
     *
     * Kegagalan transport / HTTP tidak dilempar sebagai exception agar pemanggil
     * bisa menampilkan pesan ke pengguna; semuanya dicatat ke log.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, http_status: int|null, status: string|null, data: array|null, errors: array|null, message: string|null}
     */
    private function request(string $method, string $endpoint, array $payload = []): array
    {
        $this->assertConfigured();

        $url = $this->baseUrl . $endpoint;

        try {
            $client = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->timeout($this->timeout);

            $response = $method === 'GET'
                ? $client->get($url)
                : $client->post($url, $payload);
        } catch (Throwable $e) {
            Log::error('PAPS API request gagal (transport)', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'http_status' => null,
                'status' => null,
                'data' => null,
                'errors' => null,
                'message' => 'Gagal menghubungi server GoJAGS: ' . $e->getMessage(),
            ];
        }

        $body = $this->decodeBody($response->body());
        $success = $response->successful();

        if (! $success) {
            Log::warning('PAPS API request tidak sukses', [
                'method' => $method,
                'endpoint' => $endpoint,
                'http_status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 1000),
            ]);
        }

        return [
            'success' => $success,
            'http_status' => $response->status(),
            'status' => is_array($body) ? ($body['status'] ?? null) : null,
            'data' => is_array($body) ? ($body['data'] ?? null) : null,
            'errors' => is_array($body) ? ($body['errors'] ?? null) : null,
            'message' => is_array($body)
                ? ($body['message'] ?? ($body['status'] ?? null))
                : mb_substr((string) $response->body(), 0, 500),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeBody(string $body): ?array
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function assertConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                'Kredensial API PAPS belum dikonfigurasi. Isi PAPS_CLIENT_ID dan '
                . 'PAPS_CLIENT_SECRET di .env, lalu jalankan php artisan config:cache.'
            );
        }
    }
}
