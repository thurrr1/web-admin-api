<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;


class ApiService
{
    protected $baseUrl;

    public function __construct()
    {
        // URL Backend Go Fiber (Pastikan port sesuai)
        // $this->baseUrl = 'http://localhost:3000/api';
        $this->baseUrl = env('API_BASE_URL', 'http://localhost:3000/api');
    }

    public function get($endpoint, $params = [], $timeout = 60)
    {
        return $this->request('get', $endpoint, $params, $timeout);
    }

    public function post($endpoint, $data = [], $timeout = 60)
    {
        return $this->request('post', $endpoint, $data, $timeout);
    }

    public function put($endpoint, $data = [], $timeout = 60)
    {
        return $this->request('put', $endpoint, $data, $timeout);
    }

    public function delete($endpoint, $data = [], $timeout = 60)
    {
        return $this->request('delete', $endpoint, $data, $timeout);
    }

    public function patch($endpoint, $data = [], $timeout = 60)
    {
        return $this->request('patch', $endpoint, $data, $timeout);
    }

    /**
     * Wrapper request dengan Auto Refresh Token
     */
    protected function request($method, $endpoint, $data = [], $timeout = 60)
    {
        $token = Session::get('api_token');
        $url = $this->baseUrl . $endpoint;

        // 1. Request Pertama
        $response = Http::withToken($token)->timeout($timeout)->$method($url, $data);

        // 2. Jika 401 (Unauthorized), coba Refresh Token
        if ($response->status() === 401) {
            Log::info("TESTING: Token Expired saat akses {$endpoint}. Mencoba refresh...");

            // Jangan refresh jika errornya dari endpoint login (memang salah password)
            if ($endpoint !== '/login' && $this->refreshToken()) {
                Log::info("TESTING: Refresh Token BERHASIL! Mengulangi request...");
                // Jika refresh berhasil, ambil token baru
                $newToken = Session::get('api_token');
                // Retry request asli dengan token baru
                $response = Http::withToken($newToken)->timeout($timeout)->$method($url, $data);
            }
        }

        return $response;
    }

    /**
     * Logic Refresh Token ke API Go Fiber
     */
    protected function refreshToken()
    {
        $refreshToken = Session::get('refresh_token');
        
        if (!$refreshToken) {
            Log::warning("Refresh Token GAGAL: Session expired atau tidak ada refresh token.");
            return false;
        }

        // Panggil endpoint refresh token
        $response = Http::post($this->baseUrl . '/refresh-token', [
            'refresh_token' => $refreshToken
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // Simpan token baru ke session
            Session::put('api_token', $data['token']);
            Session::put('refresh_token', $data['refresh_token']);
            Session::save(); // PENTING: Paksa simpan session agar terbaca di request berikutnya
            
            Log::info("Refresh Token BERHASIL. Token baru disimpan.");
            return true;
        }

        Log::error("Refresh Token GAGAL: API merespon " . $response->status() . " - " . $response->body());
        return false;
    }
    public function postMultipart($endpoint, $data, $fileKey, $file)
    {
        // Sesuaikan dengan properti base URL di class Anda (misal: $this->baseUrl)
        $url = $this->baseUrl . $endpoint;

        // Ambil token dari session (sesuaikan jika Anda menyimpannya dengan cara berbeda di method get/post)
        $token = session('api_token');

        return Http::withToken($token)
            ->attach(
                $fileKey,
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->post($url, $data);
    }
    
}
