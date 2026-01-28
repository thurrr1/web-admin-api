<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/admin/shift');
        $shifts = $response->successful() ? $response->json('data') : [];
        return view('shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('shift.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ]);

        $response = $this->api->post('/admin/shift', $request->all());

        if ($response->successful()) {
            return redirect()->route('shift.index')->with('success', 'Shift berhasil ditambahkan');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal menambah shift')->withInput();
    }

    public function edit($id)
    {
        // Karena endpoint GET /shift/{id} belum ada di Go, kita ambil semua lalu filter di sini
        // Ini aman karena jumlah shift biasanya sedikit
        $response = $this->api->get('/admin/shift');
        
        if ($response->successful()) {
            $shifts = collect($response->json('data'));
            $shift = $shifts->firstWhere('ID', $id) ?? $shifts->firstWhere('id', $id);
            
            if ($shift) {
                return view('shift.edit', compact('shift'));
            }
        }

        return redirect()->route('shift.index')->with('error', 'Data shift tidak ditemukan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ]);

        $response = $this->api->put("/admin/shift/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('shift.index')->with('success', 'Shift berhasil diupdate');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update shift');
    }

    public function destroy($id)
    {
        $response = $this->api->delete("/admin/shift/{$id}");

        if ($response->successful()) {
            return redirect()->route('shift.index')->with('success', 'Shift berhasil dihapus');
        }

        return back()->with('error', $response->json('error') ?? 'Gagal menghapus shift');
    }
}