@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="row">
        <!-- Left Column: Statistik Harian (Vertical) -->

        <div class="col-lg-3 mb-4">
            <h2 class="text-dark fw-bold mb-4">Dashboard</h2>
            <div class="text-muted mb-4">
                <i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
            <h6 class="mb-3 text-secondary fw-bold text-uppercase small ls-1">Statistik Hari Ini</h6>
            

            <!-- Card Hadir Tepat Waktu -->
            <div class="card border-0 shadow-sm border-start border-4 border-success mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Hadir Tepat Waktu</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $stats['hari_ini']['hadir_tepat_waktu'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill text-gray-300 display-4" style="color: #d1e7dd;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card TL/CP -->
            <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">TL / CP</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ ($stats['hari_ini']['tl_cp'] ?? 0) + ($stats['hari_ini']['tl_cp_diizinkan'] ?? 0) }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Terlambat / Cepat Pulang</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle-fill text-gray-300 display-4" style="color: #fff3cd;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Izin/Cuti -->
            <div class="card border-0 shadow-sm border-start border-4 border-info mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Izin / Cuti</div>
                            @php $izinCuti = ($stats['hari_ini']['izin'] ?? 0) + ($stats['hari_ini']['cuti'] ?? 0); @endphp
                            <div class="h4 mb-0 fw-bold text-dark">{{ $izinCuti }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text-fill text-gray-300 display-4" style="color: #cff4fc;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Belum Absen / Alfa -->
            <div class="card border-0 shadow-sm border-start border-4 border-secondary mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Belum Absen / Tanpa Keterangan</div>
                            @php $belumAlfa = ($stats['hari_ini']['belum_absen'] ?? 0) + ($stats['hari_ini']['alfa'] ?? 0); @endphp
                            <div class="h4 mb-0 fw-bold text-dark">{{ $belumAlfa }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-dash-fill text-gray-300 display-4" style="color: #e2e3e5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Statistik Bulanan & Filter -->
        <div class="col-lg-9">
            <!-- Filter Bulan & Tahun -->
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-body py-3">
                    <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-auto fw-bold text-secondary">Filter Rekap:</div>
                        <div class="col-auto">
                            <select name="bulan" class="form-select form-select-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ (request('bulan', date('n')) == $m) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="tahun" class="form-select form-select-sm">
                                @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                    <option value="{{ $y }}" {{ (request('tahun', date('Y')) == $y) ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Row: Statistik Bulanan (Grafik) -->
            <div class="row">
                <!-- Left Column (Inside Right): Bar Chart Harian -->
                <div class="col-md-9 mb-1">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 fw-bold text-primary">Statistik Harian ({{ $stats['meta']['bulan'] ?? '' }} {{ $stats['meta']['tahun'] ?? '' }})</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar" style="position: relative; height: 400px;">
                                <canvas id="dailyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

        @php
            $total = $stats['bulan_ini']['total_jadwal'] ?? 0;
            $hadir = $stats['bulan_ini']['hadir_tepat_waktu'] ?? 0;
            $tl_cp = $stats['bulan_ini']['tl_cp'] ?? 0;
            $tl_cp_izin = $stats['bulan_ini']['tl_cp_diizinkan'] ?? 0;
            $izin = $stats['bulan_ini']['izin'] ?? 0;
            $cuti = $stats['bulan_ini']['cuti'] ?? 0;
            $alfa = $stats['bulan_ini']['alfa'] ?? 0;
            $belum = $stats['bulan_ini']['belum_absen'] ?? 0;

            function hitungPersen($val, $total) {
                return $total > 0 ? round(($val / $total) * 100, 1) : 0;
            }
        @endphp

                <!-- Right Column (Inside Right): Doughnut Chart + Persentase -->
                <div class="col-md-3 mb-2">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-2  d-flex flex-row align-items-center justify-content-center">
                            <!-- <h6 class="m-0 fw-bold text-primary">Rekapitulasi Kehadiran Bulanan</h6> -->
                            <a href="{{ route('reports.monthly', ['bulan' => request('bulan', date('n')), 'tahun' => request('tahun', date('Y'))]) }}" class="btn btn-sm btn-danger  shadow-sm">
                                <i class="bi bi-file-earmark-pdf  me-1"></i> Rekap Bulanan
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Chart Doughnut & Total Side-by-Side -->
                            <div class="d-flex align-items-center justify-content-center mb-4">
                                <div style="position: relative; height: 80px; width: 80px;">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                                <div class="ms-4 text-center">
                                    <div class="display-4 fw-bold text-dark">{{ $stats['bulan_ini']['total_jadwal'] ?? 0 }}</div>
                                    <div class="text-muted small ls-1">Total Jadwal</div>
                                </div>
                            </div>
                            
                            <hr>

                            <div class="mb-0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold text-success"><i class="bi bi-circle-fill me-1"></i> Hadir Tepat Waktu</span>
                                    <span class="small fw-bold">{{ hitungPersen($hadir, $total) }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ hitungPersen($hadir, $total) }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold text-warning"><i class="bi bi-circle-fill me-1"></i> TL / CP</span>
                                    <span class="small fw-bold">{{ hitungPersen($tl_cp, $total) }}% <span class="text-muted" style="font-size: 0.7em">(Tanpa Izin)</span></span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ hitungPersen($tl_cp, $total) }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold" style="color: #fd7e14"><i class="bi bi-circle-fill me-1"></i> TL / CP (Diizinkan)</span>
                                    <span class="small fw-bold">{{ hitungPersen($tl_cp_izin, $total) }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar" style="background-color: #fd7e14; width: {{ hitungPersen($tl_cp_izin, $total) }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold text-info"><i class="bi bi-circle-fill me-1"></i> Izin</span>
                                    <span class="small fw-bold">{{ hitungPersen($izin, $total) }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: {{ hitungPersen($izin, $total) }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold" style="color: #6f42c1"><i class="bi bi-circle-fill me-1"></i> Cuti</span>
                                    <span class="small fw-bold">{{ hitungPersen($cuti, $total) }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar" style="background-color: #6f42c1; width: {{ hitungPersen($cuti, $total) }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold text-danger"><i class="bi bi-circle-fill me-1"></i> Tanpa Keterangan</span>
                                    <span class="small fw-bold">{{ hitungPersen($alfa, $total) }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-danger" style="width: {{ hitungPersen($alfa, $total) }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-bold text-secondary"><i class="bi bi-circle-fill me-1"></i> Belum Absen</span>
                                    <span class="small fw-bold">{{ hitungPersen($belum, $total) }}%</span>
                                </div>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" style="width: {{ hitungPersen($belum, $total) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. CHART DOUGHNUT (Bulanan) ---
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            
            // Data dari Controller
            const dataStats = {
                hadir: {{ $stats['bulan_ini']['hadir_tepat_waktu'] ?? 0 }},
                tl_cp: {{ $stats['bulan_ini']['tl_cp'] ?? 0 }},
                tl_cp_izin: {{ $stats['bulan_ini']['tl_cp_diizinkan'] ?? 0 }},
                izin: {{ $stats['bulan_ini']['izin'] ?? 0 }},
                cuti: {{ $stats['bulan_ini']['cuti'] ?? 0 }},
                alfa: {{ $stats['bulan_ini']['alfa'] ?? 0 }},
                belum: {{ $stats['bulan_ini']['belum_absen'] ?? 0 }}
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir Tepat Waktu', 'TL / CP (Tanpa Izin)', 'TL / CP (Diizinkan)', 'Izin', 'Cuti', 'Alfa', 'Belum Absen'],
                    datasets: [{
                        data: [
                            dataStats.hadir, 
                            dataStats.tl_cp, 
                            dataStats.tl_cp_izin,
                            dataStats.izin, 
                            dataStats.cuti, 
                            dataStats.alfa, 
                            dataStats.belum
                        ],
                        backgroundColor: [
                            '#198754', // Success (Hadir)
                            '#ffc107', // Warning (TL/CP)
                            '#fd7e14', // Orange (TL/CP Diizinkan)
                            '#0dcaf0', // Info (Izin)
                            '#6f42c1', // Purple (Cuti)
                            '#dc3545', // Danger (Alfa)
                            '#cccecf'  // Secondary (Belum Absen - disamakan dengan progress bar)
                        ],
                        borderColor: [
                            '#157347',
                            '#ffca2c',
                            '#e36d0e',
                            '#31d2f2',
                            '#59359a',
                            '#bb2d3b',
                            '#bfbfbf'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide legend as requested
                        }
                    },
                    cutout: '70%', // Membuat efek Donut lebih tipis
                }
            });

            // --- 2. CHART BAR (Harian) ---
            const ctxDaily = document.getElementById('dailyChart').getContext('2d');

            @php
                // Generate Labels Tanggal (1 s/d Jumlah Hari di Bulan Terpilih)
                $selBulan = request('bulan', date('n'));
                $selTahun = request('tahun', date('Y'));
                $daysInMonth = \Carbon\Carbon::createFromDate($selTahun, $selBulan, 1)->daysInMonth;
                $labels = range(1, $daysInMonth);

                // --- PROSES DATA HARIAN DARI DETAIL API ---
                $dailyData = [];
                
                // 1. Inisialisasi data kosong (0) untuk setiap tanggal
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $dailyData[$i] = ['hadir' => 0, 'tl_cp' => 0, 'tl_cp_izin' => 0, 'izin' => 0, 'cuti' => 0, 'alfa' => 0, 'belum' => 0];
                }

                // 2. Isi data berdasarkan detail kehadiran dari API
                $details = $stats['bulan_ini']['detail'] ?? [];
                
                foreach ($details as $d) {
                    // Konversi ke array dan lowercase keys agar aman
                    $d = array_change_key_case((array)$d, CASE_LOWER);

                    $tgl = $d['tanggal'] ?? null;
                    $statusMasuk = $d['status_masuk'] ?? $d['statusmasuk'] ?? '';
                    $statusPulang = $d['status_pulang'] ?? $d['statuspulang'] ?? '';
                    // Asumsi field permission id ada di detail api, misal 'perizinan_kehadiran_id' atau 'perizinankehadiranid'
                    $izinKoreksiId = $d['perizinan_kehadiran_id'] ?? $d['perizinankehadiranid'] ?? null;

                    if ($tgl) {
                        $dayIndex = (int) date('j', strtotime($tgl));
                        
                        if (isset($dailyData[$dayIndex])) {
                            if ($statusMasuk == 'IZIN') {
                                $dailyData[$dayIndex]['izin']++;
                            } elseif ($statusMasuk == 'CUTI') {
                                $dailyData[$dayIndex]['cuti']++;
                            } elseif ($statusMasuk == 'TERLAMBAT' || $statusPulang == 'PULANG_CEPAT') {
                                if ($izinKoreksiId) {
                                     // TL/CP Dengan Izin -> Orange
                                     $dailyData[$dayIndex]['tl_cp_izin']++; 
                                } else {
                                     // TL/CP Tanpa Izin -> Kuning
                                     $dailyData[$dayIndex]['tl_cp']++;
                                }
                            } elseif ($statusMasuk == 'HADIR') {
                                $dailyData[$dayIndex]['hadir']++;
                            } elseif ($statusMasuk == 'ALFA') {
                                $dailyData[$dayIndex]['alfa']++;
                            } elseif ($statusMasuk == 'BELUM_ABSEN') {
                                $dailyData[$dayIndex]['belum']++;
                            }
                        }
                    }
                }
                
                function getDailySeries($data, $days, $key) {
                    $series = [];
                    foreach ($days as $d) {
                        $series[] = $data[$d][$key] ?? 0; 
                    }
                    return json_encode($series);
                }
            @endphp

            const dailyLabels = {!! json_encode($labels) !!};

            console.log("Details Raw:", @json($details));
            console.log("Daily Data Processed:", @json($dailyData));

            new Chart(ctxDaily, {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: {!! getDailySeries($dailyData, $labels, 'hadir') !!},
                            backgroundColor: '#198754',
                        },
                        {
                            label: 'TL / CP',
                            data: {!! getDailySeries($dailyData, $labels, 'tl_cp') !!},
                            backgroundColor: '#ffc107',
                        },
                        {
                            label: 'TL / CP (Diizinkan)',
                            data: {!! getDailySeries($dailyData, $labels, 'tl_cp_izin') !!},
                            backgroundColor: '#fd7e14', // Orange
                        },
                        {
                            label: 'Izin',
                            data: {!! getDailySeries($dailyData, $labels, 'izin') !!},
                            backgroundColor: '#0dcaf0',
                        },
                        {
                            label: 'Cuti',
                            data: {!! getDailySeries($dailyData, $labels, 'cuti') !!},
                            backgroundColor: '#6f42c1', // Purple
                        },
                        {
                            label: 'Alfa',
                            data: {!! getDailySeries($dailyData, $labels, 'alfa') !!},
                            backgroundColor: '#dc3545',
                        },
                        {
                            label: 'Belum Absen',
                            data: {!! getDailySeries($dailyData, $labels, 'belum') !!},
                            backgroundColor: '#cccecf', 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (e) => {
                        const points = e.chart.getElementsAtEventForMode(e, 'index', { intersect: false }, true);

                        if (points.length > 0) {
                            const firstPoint = points[0];
                            const dayIndex = firstPoint.index;
                            const day = dailyLabels[dayIndex];
                            
                            // Ambil Tahun dan Bulan dar variable PHP yang sudah ada di scope blade
                            const year = "{{ request('tahun', date('Y')) }}";
                            const month = "{{ request('bulan', date('n')) }}";
                            
                            // Pad month and day with leading zero if needed
                            const paddedMonth = month.toString().padStart(2, '0');
                            const paddedDay = day.toString().padStart(2, '0');
                            
                            const dateStr = `${year}-${paddedMonth}-${paddedDay}`;
                            const url = `{{ url('/jadwal') }}?tanggal=${dateStr}&search=`;
                            
                            window.location.href = url;
                        }
                    },
                    scales: {
                        x: {
                            stacked: true, // Mengaktifkan Stacked Bar
                            grid: { display: false },
                            cursor: 'pointer' // Add cursor pointer to indicate clickable
                        },
                        y: {
                            stacked: true, // Mengaktifkan Stacked Bar
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Hide legend as requested
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    }
                }
            });
        });
    </script>
@endsection