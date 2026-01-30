<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function monthlyRecap(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $response = $this->api->get('/admin/reports/monthly', [
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal mengambil data laporan: ' . $response->body());
        }

        $data = $response->json();
        
        // Generate PDF
        $pdf = Pdf::loadView('reports.monthly_pdf', $data);
        $pdf->setPaper('legal', 'landscape'); // Legal Landscape agar muat tabel panjang
        
        return $pdf->download('laporan_bulanan_' . $bulan . '_' . $tahun . '.pdf');
    }

    public function dailyRecap(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $response = $this->api->get('/admin/reports/daily', [
            'tanggal' => $tanggal
        ]);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal mengambil data laporan');
        }

        $data = $response->json();

        // Generate PDF
        $pdf = Pdf::loadView('reports.daily_pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('laporan_harian_' . $tanggal . '.pdf');
    }
}
