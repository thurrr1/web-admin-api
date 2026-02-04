<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsnController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        // Ambil data pegawai dari API
        $response = $this->api->get('/admin/asn', ['search' => $search]);
        $pegawai = $response->successful() ? $response->json('data') : [];

        return view('asn.index', compact('pegawai', 'search'));
    }

    public function create()
    {
        // Ambil data roles untuk dropdown (jika API roles sudah ada)
        $response = $this->api->get('/admin/roles');
        $roles = $response->successful() ? $response->json('data') : [];
        return view('asn.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required',
            'password' => 'required|min:6',
            'jabatan' => 'required',
            'bidang' => 'required',
            'role_id' => 'required|integer',
            'email'   => 'nullable|email',
            'no_hp'   => 'nullable|string',
        ]);

        // Kirim data ke API
        $data = $request->all();
        $data['role_id'] = (int) $request->role_id; // Wajib integer untuk Go

        $response = $this->api->post('/admin/asn', $data);

        /** @var Response $response */

        if ($response->successful()) {
            return redirect()->route('asn.index')->with('success', 'Pegawai berhasil ditambahkan');
        }

        // Ambil pesan error dari API, atau default jika kosong
        $errorMsg = $response->json('error') ?? 'Gagal menambah pegawai (Cek log server Go)';
        return back()->withErrors(['api_error' => $errorMsg])->withInput();
    }

    public function downloadTemplate()
    {
        // Generate XLSX Template menggunakan Anonymous Class
        return Excel::download(new class implements FromCollection, WithHeadings {
            public function headings(): array
            {
                return ['Nama Lengkap', 'NIP', 'Jabatan', 'Bidang', 'Email', 'No HP', 'ID Role (2=Atasan, 3=Pegawai)'];
            }

            public function collection()
            {
                return collect([
                    ['Budi Santoso', '199001012022011001', 'Staf IT', 'Teknologi Informasi', 'budi@example.com', '08123456789', '2']
                ]);
            }
        }, 'template_import_asn.xlsx');
    }

    public function import(Request $request)
    {
        set_time_limit(300); // Perpanjang max execution time php (5 menit)

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            // Baca file Excel ke Array
            // FIX: Gunakan Anonymous Class yang mengimplementasikan ToArray
            $rows = Excel::toArray(new class implements ToArray {
                public function array(array $array) { return $array; }
            }, $request->file('file_excel'));
            $dataRows = $rows[0] ?? [];

            // Hapus Header (Baris pertama)
            array_shift($dataRows);

            $payload = [];
            foreach ($dataRows as $row) {
                // Pastikan row tidak kosong
                if (empty($row[0]) || empty($row[1])) continue;

                $nama = trim($row[0]);
                $nip = trim($row[1]);

                // Generate Password: 4 Angka pertama NIP + Kata pertama Nama
                // Contoh: NIP 1990... Nama Budi Santoso -> Pass: 1990Budi
                $firstWordName = explode(' ', $nama)[0];
                $cleanFirstWord = preg_replace('/[^a-zA-Z]/', '', $firstWordName);
                $finalFirstWord = ucfirst(strtolower($cleanFirstWord));

                $generatedPassword = substr($nip, 0, 4) . $finalFirstWord;

                $payload[] = [
                    'nama'     => $nama,
                    'nip'      => $nip,
                    'password' => $generatedPassword,
                    'jabatan'  => $row[2] ?? '-',
                    'bidang'   => $row[3] ?? '-',
                    'email'    => $row[4] ?? '',
                    'no_hp'    => $row[5] ?? '',
                    'role_id'  => !empty($row[6]) ? (int)$row[6] : 2, // Default Role ID 2 (User) jika kosong/0
                ];
            }

            // Kirim JSON Array ke Go Fiber
            // Kirim JSON Array ke Go Fiber (Timeout 300 detik)
            $response = $this->api->post('/admin/asn/import', $payload, 300);

            if ($response->successful()) {
                return redirect()->route('asn.index')->with('success', $response->json('message'));
            }

            return back()->withErrors($response->json('error') ?? 'Gagal import data');
        } catch (\Exception $e) {
            return back()->withErrors('Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Ambil detail pegawai dari API
        $response = $this->api->get("/admin/asn/{$id}");
        $respRoles = $this->api->get('/admin/roles');
        
        if (!$response->successful()) {
            return redirect()->route('asn.index')->with('error', 'Data tidak ditemukan');
        }

        $asn = $response->json('data');
        $roles = $respRoles->successful() ? $respRoles->json('data') : [];
        return view('asn.edit', compact('asn', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'bidang' => 'required',
            'role_id' => 'required|integer',
            'is_active' => 'required',
            'email'   => 'nullable|email',
            'no_hp'   => 'nullable|string',
        ]);

        // Pastikan is_active dikirim sebagai boolean (true/false) bukan string "1"/"0"
        $data = $request->all();
        $data['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        $data['role_id'] = (int) $request->role_id;

        $response = $this->api->put("/admin/asn/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('asn.index')->with('success', 'Data pegawai berhasil diupdate');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal update pegawai');
    }

    public function destroy($id)
    {
        $response = $this->api->delete("/admin/asn/{$id}");

        if ($response->successful()) {
            return redirect()->route('asn.index')->with('success', 'Pegawai berhasil dihapus');
        }

        return back()->with('error', 'Gagal menghapus pegawai');
    }

    public function resetDevice($id)
    {
        $response = $this->api->delete("/admin/asn/{$id}/device");

        if ($response->successful()) {
            return back()->with('success', 'Device ID berhasil di-reset. Pegawai bisa login di HP baru.');
        }
        return back()->with('error', 'Gagal reset device');
    }
}