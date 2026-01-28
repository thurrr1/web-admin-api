<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;

class BannerController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        // Ambil data banner
        // Gunakan endpoint Admin yang mengembalikan SEMUA banner (aktif & nonaktif)
        $response = $this->api->get('/admin/banner');

        /** @var Response $response */
        
        // Pastikan data tidak null (jika API mengembalikan "data": null, ubah jadi array kosong)
        $banners = ($response && $response->successful()) ? ($response->json('data') ?? []) : [];
        return view('banner.index', compact('banners'));
    }

    public function create()
    {
        return view('banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $response = $this->api->postMultipart('/admin/banner', [
            'title' => $request->title
        ], 'foto', $request->file('foto'));

        /** @var Response $response */

        if ($response->successful()) {
            return redirect()->route('banner.index')->with('success', 'Banner berhasil dibuat');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal upload banner')->withInput();
    }

    public function toggle($id)
    {
        $response = $this->api->put("/admin/banner/{$id}/toggle");

        /** @var Response $response */

        if ($response && $response->successful()) {
            return redirect()->route('banner.index')->with('success', 'Status banner berhasil diubah');
        }

        return back()->with('error', 'Gagal mengubah status banner');
    }
}