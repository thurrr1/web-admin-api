<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'password' => 'required'
        ]);

        // 1. Kirim Request Login ke API Go
        // Gunakan endpoint khusus Web Login (Bypass Device Verification)
        $response = null;
        try {
            $response = $this->api->post('/web-login', [
                'nip' => $request->nip,
                'password' => $request->password,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['nip' => 'Gagal terhubung ke Server API. Pastikan Go Fiber berjalan di port 3000.']);
        }

        // --- DEBUGGING ---
        // Jika masih error, uncomment baris di bawah ini, lalu coba login lagi.
        // Layar akan menampilkan isi variabel $response.
        // Jika isinya "NULL", berarti ApiService.php belum ada 'return'.
        // dd($response);
        // -----------------

        // SAFETY CHECK: Pastikan response tidak null/kosong sebelum memanggil method
        if (!$response) {
            return back()->withErrors(['nip' => 'Terjadi kesalahan internal: API Service tidak mengembalikan response.']);
        }

        // TYPE HINT: Memberitahu VS Code bahwa ini adalah Response Laravel
        /** @var \Illuminate\Http\Client\Response $response */

        // 2. Cek Response
        if ($response->successful()) {
            $data = $response->json();

            // 3. Simpan Token dan Data User ke Session Laravel
            Session::put('api_token', $data['token']);
            Session::put('refresh_token', $data['refresh_token']);
            Session::put('user', $data['data']); // nip, nama, role, dll

            // Cek Permission: Hanya user dengan permission 'edit_jadwal' yang boleh masuk
            $permissions = $data['data']['permissions'] ?? [];
            if (!in_array('edit_jadwal', $permissions)) {
                Session::flush();
                return back()->withErrors(['nip' => 'Akses ditolak: Anda tidak memiliki izin mengelola jadwal.']);
            }

            return redirect()->route('dashboard');
        }

        // Jika gagal
        return back()->withErrors([
            'nip' => $response->json('error') ?? 'Login gagal, periksa NIP dan Password.',
        ]);
    }

    public function logout()
    {
        Session::flush(); // Hapus semua session (Token & User Data)
        return redirect()->route('login');
    }
}