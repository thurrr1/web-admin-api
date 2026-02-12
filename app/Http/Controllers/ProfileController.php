<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProfileController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function editPassword()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed', // butuh field password_baru_confirmation di view
        ]);

        $response = $this->api->put('/asn/password', [
            'old_password' => $request->password_lama,
            'new_password' => $request->password_baru,
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Password berhasil diubah');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal mengubah password');
    }
}
