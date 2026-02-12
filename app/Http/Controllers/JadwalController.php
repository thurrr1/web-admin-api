<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;

class JadwalController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        // Default tanggal hari ini jika tidak ada filter
        $tanggal = $request->input('tanggal', Carbon::now()->format('Y-m-d'));
        $search = $request->input('search');
        
        // Ambil jadwal harian dari API
        $response = $this->api->get('/admin/jadwal', [
            'tanggal' => $tanggal,
            'search' => $search
        ]);
        $jadwal = $response->successful() ? $response->json('data') : [];

        return view('jadwal.index', compact('jadwal', 'tanggal', 'search'));
    }

    public function createGenerate()
    {
        // Ambil data Pegawai dan Shift untuk dropdown
        $respAsn = $this->api->get('/admin/asn');
        $respShift = $this->api->get('/admin/shift');

        $pegawai = $respAsn->successful() ? $respAsn->json('data') : [];
        $shifts = $respShift->successful() ? $respShift->json('data') : [];

        return view('jadwal.generate', compact('pegawai', 'shifts'));
    }

    public function storeGenerate(Request $request)
    {
        $request->validate([
            'tipe_generate' => 'required|in:range,harian',
            'shift_id' => 'required|integer',
            'asn_ids' => 'required|array', // Harus array (checkbox)
        ]);

        if ($request->tipe_generate == 'range') {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'days'  => 'required|array', // Wajib pilih minimal 1 hari
            ]);

            $data = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'shift_id' => (int) $request->shift_id,
                'asn_ids' => array_map('intval', $request->asn_ids),
                'days' => array_map('intval', $request->days), // Kirim array hari ke Go
            ];
            $response = $this->api->post('/admin/jadwal/generate', $data);

        } else {
            // Generate Harian
            $request->validate([
                'tanggal' => 'required|date',
            ]);

            $data = [
                'tanggal' => $request->tanggal,
                'shift_id' => (int) $request->shift_id,
                'asn_ids' => array_map('intval', $request->asn_ids),
            ];
            $response = $this->api->post('/admin/jadwal/generate-daily', $data);
        }

        if ($response->successful()) {
            return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dibuat.');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal generate jadwal')->withInput();
    }

    public function edit($id)
    {
        $respJadwal = $this->api->get("/admin/jadwal/{$id}");
        $respShift = $this->api->get('/admin/shift');

        if (!$respJadwal->successful()) {
            return back()->with('error', 'Jadwal tidak ditemukan');
        }

        $jadwal = $respJadwal->json('data');
        $shifts = $respShift->successful() ? $respShift->json('data') : [];

        return view('jadwal.edit', compact('jadwal', 'shifts'));
    }



    public function downloadTemplate()
    {
        // Headers: NIP, Nama (Optional), Tanggal, Jam Masuk, Jam Pulang, Status
        $headers = ['NIP', 'Nama Pegawai', 'Tanggal (YYYY-MM-DD)', 'Jam Masuk (HH:mm)', 'Jam Pulang (HH:mm)', 'STATUS (1=Hadir, 2=Libur)'];
        
        // Contoh Data
        $data = [
            ['123456789', 'John Doe', '2024-10-25', '08:00', '17:00', 1],
            ['987654321', 'Jane Doe', '2024-10-25', '08:00', '17:00', 2],
        ];

        return Excel::download(new class($headers, $data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $headers;
            private $data;

            public function __construct($headers, $data) {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function headings(): array { return $this->headers; }
            public function collection() { return collect($this->data); }
        }, 'template_import_jadwal.xlsx');
    }

    public function import(Request $request)
    {
        // Set timeout PHP script (misal 10 menit)
        set_time_limit(600);

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Baca file Excel ke Array
            $rows = Excel::toArray(new class implements ToArray {
                public function array(array $array) { return $array; }
            }, $request->file('file_excel'));

            $dataRows = $rows[0] ?? [];
            // Hapus Header
            array_shift($dataRows);

            $payload = [];
            foreach ($dataRows as $row) {
                // Format Baru: [0]=>NIP, [1]=>Nama(Ignored), [2]=>Tanggal, [3]=>JamMasuk, [4]=>JamPulang, [5]=>Status
                
                if (empty($row[0])) continue;

                // Index geser +1 dari sebelumnya karena ada kolom Nama di index 1
                $valDate = $row[2];
                if (is_numeric($valDate)) {
                    $valDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valDate)->format('Y-m-d');
                } else {
                    // Coba parse format dd/mm/yyyy atau dd-mm-yyyy
                    try {
                        // Bersihkan spasi jika ada
                        $valDate = trim($valDate);
                        $formatted = \Carbon\Carbon::createFromFormat('d/m/Y', $valDate);
                        if (!$formatted) {
                             $formatted = \Carbon\Carbon::createFromFormat('d-m-Y', $valDate);
                        }
                        $valDate = $formatted->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Fallback atau biarkan apa adanya jika gagal parse, nanti mungkin error di API Go
                        // Atau set null agar skip?
                        // Kita biarkan string aslinya, siapa tau formatnya Y-m-d
                    }
                }
                
                $jamMasuk = $row[3];
                if (is_numeric($jamMasuk)) {
                     $jamMasuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($jamMasuk)->format('H:i');
                }
                
                $jamPulang = $row[4];
                 if (is_numeric($jamPulang)) {
                     $jamPulang = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($jamPulang)->format('H:i');
                }

                // Parse Status: 2 = False (Tidak Aktif/Libur), selain itu (1) = True
                $statusVal = $row[5] ?? 1; // Default 1
                $isActive = ($statusVal == 2) ? false : true;

                $payload[] = [
                    'nip' => (string)$row[0],
                    'tanggal' => $valDate,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang,
                    'is_active' => $isActive,
                ];
            }

            // Kirim ke Go Backend dengan timeout 600 detik
            $response = $this->api->post('/admin/jadwal/import', $payload, 600);

            if ($response->successful()) {
                 return back()->with('success', $response->json('message') ?? 'Import berhasil');
            }
            return back()->with('error', $response->json('error') ?? 'Gagal import jadwal');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $data = [];
        
        if ($request->has('shift_id')) {
             $data['shift_id'] = (int) $request->shift_id;
        }
        
        if ($request->has('is_active')) {
             $data['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        }

        $response = $this->api->put("/admin/jadwal/{$id}", $data);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Update berhasil');
        }
        return back()->with('error', 'Gagal update jadwal');
    }

    public function destroy($id)
    {
        $response = $this->api->delete("/admin/jadwal/{$id}");

        if ($response->successful()) {
            return back()->with('success', 'Jadwal berhasil dihapus');
        }

        return back()->with('error', 'Gagal menghapus jadwal');
    }

    public function destroyDate(Request $request)
    {
        $tanggal = $request->tanggal;
        $response = $this->api->delete("/admin/jadwal/date/bulk?tanggal={$tanggal}");

        if ($response->successful()) {
            return redirect()->route('jadwal.index', ['tanggal' => $tanggal])->with('success', 'Semua jadwal pada tanggal tersebut berhasil dihapus.');
        }
        return back()->with('error', $response->json('error') ?? 'Gagal menghapus jadwal massal');
    }
}