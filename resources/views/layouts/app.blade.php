<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fc; /* Abu-abu sangat muda agar elemen putih terlihat menonjol */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #444;
        }
        .wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar Minimalis */
        .sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #ffffff;
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(0,0,0,0.03); /* Shadow halus di kanan */
            z-index: 100;
            position: relative;
        }
        .sidebar .brand {
            padding: 25px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #4e73df; /* Aksen warna biru modern */
            display: flex;
            align-items: center;
        }
        .sidebar a {
            color: #6c757d;
            text-decoration: none;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            margin: 4px 15px;
            border-radius: 10px; /* Rounded corners */
        }
        .sidebar a i {
            margin-right: 12px;
            font-size: 1.1rem;
        }
        .sidebar a:hover {
            background: #f8f9fa;
            color: #4e73df;
        }
        .sidebar a.active {
            background: #eef2ff; /* Biru sangat muda */
            color: #4e73df;
            font-weight: 600;
        }
        .sidebar .menu-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #adb5bd;
            margin: 25px 25px 10px;
            font-weight: 700;
        }

        /* Navbar Minimalis */
        .navbar {
            background: #ffffff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03); /* Shadow halus di bawah */
            padding: 15px 30px;
            border: none;
        }
        
        /* Content Area */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
            padding: 30px;
        }

        /* Global Card Styling (Otomatis mengubah semua card di aplikasi) */
        .card {
            border: none; /* Hilangkan border */
            border-radius: 15px; /* Sudut lebih bulat */
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); /* Drop shadow lembut */
            background: #ffffff;
            margin-bottom: 20px;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 25px;
            font-weight: 600;
            color: #4e73df;
        }
        .card-body {
            padding: 25px;
        }
        
        /* Tombol */
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            box-shadow: none !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Kiri -->
        <div class="sidebar">
            <div class="brand">
                <i class="bi bi-fingerprint me-2"></i> E-Absensi 
            </div>
            
            <div class="py-2">
                <div class="menu-header">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('asn.index') }}" class="{{ request()->routeIs('asn.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Data Pegawai
                </a>
                
                <div class="menu-header">Manajemen Jadwal</div>
                <a href="{{ route('jadwal.index') }}" class="{{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i> Jadwal Kerja
                </a>
                <a href="{{ route('shift.index') }}" class="{{ request()->routeIs('shift.*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i> Data Jam Kerja
                </a>
                <a href="{{ route('hari-libur.index') }}" class="{{ request()->routeIs('hari-libur.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Hari Libur
                </a>

                <div class="menu-header">Master Data</div>
                <a href="{{ route('organisasi.index') }}" class="{{ request()->routeIs('organisasi.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Organisasi
                </a>
                <a href="{{ route('banner.index') }}" class="{{ request()->routeIs('banner.*') ? 'active' : '' }}">
                    <i class="bi bi-card-image"></i> Banner Info
                </a>
            </div>
        </div>

        <!-- Konten Kanan -->
        <div class="content-wrapper">
            <!-- Navbar Atas -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <!-- Tombol Toggle Mobile bisa ditambahkan di sini jika perlu -->
                    
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 fw-bold text-secondary">{{ session('user')['organisasi'] ?? '' }}</h5>
                    </div>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <span class="fw-medium">{{ session('user')['nama'] ?? 'Admin' }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Konten Utama -->
            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>