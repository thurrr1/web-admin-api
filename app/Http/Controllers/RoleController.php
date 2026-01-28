<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;

class RoleController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/admin/roles');
        /** @var Response $response */
        $roles = $response->successful() ? $response->json('data') : [];
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        // Ambil daftar permission untuk checkbox
        $response = $this->api->get('/admin/roles/permissions');
        /** @var Response $response */
        $permissions = $response->successful() ? $response->json('data') : [];
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string',
            'permissions' => 'array'
        ]);

        $data = [
            'nama_role' => $request->nama_role,
            'permission_ids' => array_map('intval', $request->permissions ?? [])
        ];

        $response = $this->api->post('/admin/roles', $data);
        /** @var Response $response */

        if ($response->successful()) {
            return redirect()->route('role.index')->with('success', 'Role berhasil dibuat');
        }
        return back()->withErrors($response->json('error') ?? 'Gagal membuat role');
    }

    public function edit($id)
    {
        $responseRole = $this->api->get("/admin/roles/{$id}");
        $responsePerms = $this->api->get('/admin/roles/permissions');

        /** @var Response $responseRole */
        if (!$responseRole->successful()) {
            return redirect()->route('role.index')->with('error', 'Role tidak ditemukan');
        }

        $role = $responseRole->json('data');
        /** @var Response $responsePerms */
        $permissions = $responsePerms->successful() ? $responsePerms->json('data') : [];

        return view('role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_role' => 'required|string',
            'permissions' => 'array'
        ]);

        $data = [
            'nama_role' => $request->nama_role,
            'permission_ids' => array_map('intval', $request->permissions ?? [])
        ];

        $response = $this->api->put("/admin/roles/{$id}", $data);
        /** @var Response $response */

        if ($response->successful()) {
            return redirect()->route('role.index')->with('success', 'Role berhasil diupdate');
        }
        return back()->withErrors($response->json('error') ?? 'Gagal update role');
    }

    public function destroy($id)
    {
        $response = $this->api->delete("/admin/roles/{$id}");
        /** @var Response $response */
        if ($response->successful()) {
            return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
        }
        return back()->with('error', 'Gagal menghapus role');
    }
}
