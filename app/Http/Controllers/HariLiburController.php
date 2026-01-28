<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/admin/hari-libur');
        $libur = $response->successful() ? $response->json('data') : [];
        return view('hari-libur.index', compact('libur'));
    }

    public function create()
    {
        return view('hari-libur.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $response = $this->api->post('/admin/hari-libur', $request->all());

        if ($response->successful()) {
            return redirect()->route('hari-libur.index')->with('success', 'Hari libur berhasil ditambahkan');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal menambah data')->withInput();
    }

    public function edit($id)
    {
        // Kita ambil semua data lalu filter, karena endpoint GET /hari-libur/{id} belum didaftarkan di route Go
        $response = $this->api->get('/admin/hari-libur');
        
        if ($response->successful()) {
            $liburs = collect($response->json('data'));
            // Handle kemungkinan key ID (PascalCase dari Go) atau id
            $libur = $liburs->firstWhere('ID', $id) ?? $liburs->firstWhere('id', $id);
            
            if ($libur) {
                return view('hari-libur.edit', compact('libur'));
            }
        }

        return redirect()->route('hari-libur.index')->with('error', 'Data tidak ditemukan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $response = $this->api->put("/admin/hari-libur/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('hari-libur.index')->with('success', 'Data berhasil diupdate');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update data');
    }

    public function destroy($id)
    {
        $response = $this->api->delete("/admin/hari-libur/{$id}");

        if ($response->successful()) {
            return redirect()->route('hari-libur.index')->with('success', 'Data berhasil dihapus');
        }

        return back()->with('error', 'Gagal menghapus data');
    }
}