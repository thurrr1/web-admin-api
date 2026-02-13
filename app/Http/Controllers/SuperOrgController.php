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
            'nip' => 'required|numeric',
            'password' => 'required|min:6',
            'jabatan' => 'required|string',
            'bidang' => 'required|string',
            'role_id' => 'required|integer',
        ]);
        
        $data = [
            'nama' => $request->nama,
            'nip' => $request->nip,
            'password' => $request->password,
            'jabatan' => $request->jabatan,
            'bidang' => $request->bidang,
            'role_id' => (int)$request->role_id, 
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

    public function editAdmin($id)
    {
        // Ambil detail ASN dari API
        $response = $this->api->get("/admin/asn/{$id}");
        // Ambil Roles
        $respRoles = $this->api->get('/admin/roles');

        if (!$response->successful()) {
            return redirect()->route('super-org.index')->with('error', 'Data admin tidak ditemukan');
        }

        $asn = $response->json('data');
        $roles = $respRoles->successful() ? $respRoles->json('data') : [];

        return view('super-org.edit_admin', compact('asn', 'roles'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'bidang' => 'required',
            'role_id' => 'required|integer',
            'email'   => 'nullable|email',
            'no_hp'   => 'nullable|string',
        ]);

        $data = $request->all();
        $data['role_id'] = (int) $request->role_id;
        // Kita tidak update is_active dari sini (biarkan value lama atau handle terpisah)
        // Namun API update ASN mungkin butuh is_active jika sifatnya replace all fields.
        // Cek AsnController: metodeny PUT /admin/asn/{id}. 
        // Best practice: fetch existing data, merge, then update OR API supports patch.
        // Utk aman: kita ambil data existing dulu utk dapat is_active nya?
        // Atau kita bisa kirim is_active yang sudah ada di DB jika API mewajibkan.
        // Tapi lihat AsnController::update, dia kirim semua.
        // Mari kita fetch dulu utk ambil is_active existing.
        
        $current = $this->api->get("/admin/asn/{$id}");
        if ($current->successful()) {
            $currData = $current->json('data');
            $data['is_active'] = $currData['is_active']; // Pertahankan status lama
            $data['organisasi_id'] = $currData['OrganisasiID'] ?? $currData['organisasi_id']; // Pertahankan org id
        }

        $response = $this->api->put("/admin/asn/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('super-org.index')->with('success', 'Data admin berhasil diperbarui');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update admin')->withInput();
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
