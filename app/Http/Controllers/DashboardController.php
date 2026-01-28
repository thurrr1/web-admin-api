<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        // Panggil API Go Baru: GET /api/admin/jadwal/dashboard-stats
        // Pastikan route ini sudah didaftarkan di Go Fiber
        $response = $this->api->get('/admin/jadwal/dashboard-stats', [
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
        ]);

        $stats = [
            'persentase_hadir' => 0,
            'hari_ini' => [
                'hadir_tepat_waktu' => 0, 'tl_cp' => 0, 'izin' => 0, 'cuti' => 0, 'alfa' => 0, 'belum_absen' => 0
            ],
            'bulan_ini' => [
                'total_jadwal' => 0, 'hadir_tepat_waktu' => 0, 'tl_cp' => 0, 'izin' => 0, 'cuti' => 0, 'alfa' => 0, 'belum_absen' => 0
            ]
        ];

        if ($response->successful()) {
            $stats = $response->json();
        }

        return view('dashboard.index', compact('stats'));
    }
}