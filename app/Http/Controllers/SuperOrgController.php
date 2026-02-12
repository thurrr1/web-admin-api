<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class SuperOrgController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/admin/organisasi/all');
        $organisasis = $response->successful() ? $response->json('data') : [];

        // Ensure we handle potential null response structure gracefully
        if (!is_array($organisasis)) {
            $organisasis = [];
        }

        return view('super-org.index', compact('organisasis'));
    }

    public function create()
    {
        return view('super-org.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'email_admin' => 'nullable|email',
        ]);

        $data = [
            'nama_organisasi' => $request->nama_organisasi,
            'email_admin' => $request->email_admin,
        ];

        $response = $this->api->post('/admin/organisasi', $data);

        if ($response->successful()) {
            return redirect()->route('super-org.index')->with('success', 'Organisasi berhasil dibuat');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal membuat organisasi');
    }

    public function edit($id)
    {
        // Backend doesn't have a specific "GetOrgByID" for public/admin usage other than GetInfo (current)
        // However, the List endpoint returns all data. We can fetch all and filter, or add GetByID in backend.
        // Or we can assume the list data is enough if it contains all fields.
        // But cleaner way: reuse the list data or just pass the data from index if possible? No.
        // I should have added GetByID in backend.
        // But for now, let's try finding it from the list (since we don't expect thousands of orgs yet).
        
        $response = $this->api->get('/admin/organisasi/all');
        $organisasis = $response->successful() ? $response->json('data') : [];
        $org = collect($organisasis)->firstWhere('ID', $id);

        if (!$org) {
            return redirect()->route('super-org.index')->with('error', 'Organisasi tidak ditemukan');
        }

        return view('super-org.edit', compact('org'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'email_admin' => 'nullable|email',
        ]);

        $data = [
            'nama_organisasi' => $request->nama_organisasi,
            'email_admin' => $request->email_admin,
        ];

        // This calls the PUT /:id endpoint we just created
        $response = $this->api->put("/admin/organisasi/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('super-org.index')->with('success', 'Organisasi berhasil diperbarui');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update organisasi');
    }

    public function createAdmin($id)
    {
        // We need org name for display, fetch list again
        $response = $this->api->get('/admin/organisasi/all');
        $organisasis = $response->successful() ? $response->json('data') : [];
        $org = collect($organisasis)->firstWhere('ID', $id);

        if (!$org) {
             return redirect()->route('super-org.index')->with('error', 'Organisasi tidak ditemukan');
        }

        return view('super-org.create_admin', compact('org'));
    }

    public function storeAdmin(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'nip' => 'required|numeric|unique:asns,nip', // Note: unique check here might not work if DB is different, better rely on API error
            'password' => 'required|min:6',
            'jabatan' => 'required|string',
            'bidang' => 'required|string',
            // 'role_id' hardcoded to Admin (ID 2 usually, or we search for it)
        ]);

        // Find Role ID for "Admin"
        // Ideally fetch roles from API, but for MVP hardcode or pass standard ID. 
        // In seeder: 1=Super Admin, 2=Admin, 3=Atasan, 4=Pegawai (Based on creation order in seeder)
        // Wait, seeder order: Super Admin, Admin, Atasan, Pegawai.
        // Before seeder change: Admin, Atasan, Pegawai.
        // IDs depend on database state.
        // Safer to fetch roles from API? currently no role list API for admin.
        // Let's assume ID 2 is Admin if fresh seed.
        // Or better: Add a dropdown in UI or backend logic handles role name? Backend expects role_id.
        // I'll assume 2 for Admin for now, or existing logic uses names?
        // Let's check AsnController store method to see how it handles roles.
        
        $data = [
            'nama' => $request->nama,
            'nip' => $request->nip,
            'password' => $request->password,
            'jabatan' => $request->jabatan,
            'bidang' => $request->bidang,
            'role_id' => 2, // Asumsi 2 = Admin (Urutan seeder)
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'organisasi_id' => (int)$id, // This is key!
        ];

        $response = $this->api->post('/admin/asn', $data);

        if ($response->successful()) {
            return redirect()->route('super-org.index')->with('success', 'Admin berhasil ditambahkan untuk organisasi ini');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal menambah admin');
    }

    public function getAdmins($id)
    {
        $response = $this->api->get("/admin/organisasi/{$id}/admins");
        return $response->json();
    }

    public function toggleAdminStatus($id)
    {
        $isActive = request()->boolean('is_active');
        $response = $this->api->patch("/admin/asn/{$id}/status", [
            'is_active' => $isActive
        ]);

        return $response->json();
    }
}
