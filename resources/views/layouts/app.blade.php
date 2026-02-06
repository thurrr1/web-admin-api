<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Overlay for Mobile Sidebar -->
        <div class="overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Kiri -->
        <div class="sidebar" id="sidebar">
            <div class="brand d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-fingerprint me-2"></i> Absensi Online
                </div>
                <button class="btn btn-sm btn-link d-md-none text-muted" id="closeSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
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
                <div class="menu-header">Developer</div>
                <a href="{{ url('/api-docs') }}" target="_blank">
                    <i class="bi bi-code-slash"></i> API Docs
                </a>
            </div>
        </div>

        <!-- Konten Kanan -->
        <div class="content-wrapper">
            <!-- Navbar Atas -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <!-- Tombol Toggle Sidebar (Mobile Only) -->
                    <button class="btn btn-link text-dark me-3 d-md-none" id="sidebarToggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 fw-bold text-secondary d-none d-sm-block">{{ session('user')['organisasi'] ?? '' }}</h5>
                    </div>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <span class="fw-medium d-none d-sm-inline">{{ session('user')['nama'] ?? 'Admin' }}</span>
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
    <script>
        // Sidebar Toggle Script
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('closeSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
    @yield('scripts')
</body>
</html>