<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class OrganisasiController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/admin/organisasi');
        $org = $response->successful() ? $response->json('data') : null;

        return view('organisasi.index', compact('org'));
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'email_admin' => 'nullable|email',
        ]);

        $data = [
            'nama_organisasi' => $request->nama_organisasi,
            'email_admin' => $request->email_admin,
        ];

        $response = $this->api->put('/admin/organisasi', $data);

        if ($response->successful()) {
            return redirect()->route('organisasi.index')->with('success', 'Informasi organisasi berhasil diperbarui');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update informasi organisasi');
    }

    public function createLokasi()
    {
        return view('organisasi.create');
    }

    public function edit($id)
    {
        // Kita ambil data terbaru dulu
        $response = $this->api->get('/admin/organisasi');
        $org = $response->successful() ? $response->json('data') : null;

        // Pastikan ID lokasi cocok (biasanya relasi 1-to-1 untuk MVP ini)
        // Cari lokasi berdasarkan ID dari list 'lokasis'
        $lokasis = collect($org['lokasis'] ?? []);
        $lokasi = $lokasis->firstWhere('ID', $id) ?? $lokasis->firstWhere('id', $id);

        if (!$lokasi) {
            return redirect()->route('organisasi.index')->with('error', 'Lokasi tidak ditemukan');
        }
        
        return view('organisasi.edit', compact('lokasi'));
    }

    public function storeLokasi(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required',
            'alamat' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|numeric',
        ]);

        $data = [
            'nama_lokasi' => $request->nama_lokasi,
            'alamat' => $request->alamat,
            'latitude' => (float) $request->latitude,
            'longitude' => (float) $request->longitude,
            'radius_meter' => (float) $request->radius_meter,
        ];

        $response = $this->api->post("/admin/organisasi/lokasi", $data);

        if ($response->successful()) {
            return redirect()->route('organisasi.index')->with('success', 'Lokasi baru berhasil ditambahkan');
        }
        return back()->withErrors($response->json('error') ?? 'Gagal menambah lokasi');
    }

    public function updateLokasi(Request $request, $id)
    {
        // Update Lokasi Kantor
        $request->validate([
            'nama_lokasi' => 'required',
            'alamat' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|numeric',
        ]);

        // Pastikan tipe data float/double dikirim dengan benar
        $data = [
            'nama_lokasi' => $request->nama_lokasi,
            'alamat' => $request->alamat,
            'latitude' => (float) $request->latitude,
            'longitude' => (float) $request->longitude,
            'radius_meter' => (float) $request->radius_meter,
        ];

        $response = $this->api->put("/admin/organisasi/lokasi/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('organisasi.index')->with('success', 'Lokasi kantor berhasil diperbarui');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update lokasi');
    }

    public function destroyLokasi($id)
    {
        $response = $this->api->delete("/admin/organisasi/lokasi/{$id}");

        if ($response->successful()) {
            return redirect()->route('organisasi.index')->with('success', 'Lokasi berhasil dihapus');
        }
        return back()->with('error', 'Gagal menghapus lokasi');
    }
}