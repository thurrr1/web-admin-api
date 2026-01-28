@extends('layouts.app')

@section('title', 'Import Jadwal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-file-earmark-spreadsheet me-2"></i> Import Jadwal dari Excel</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="alert alert-info">
                    <small>
                        <i class="bi bi-info-circle me-1"></i> Pastikan format Excel Anda sesuai:
                        <br>Kolom 1: NIP Pegawai
                        <br>Kolom 2: Nama Pegawai (Opsional/Diabaikan sistem)
                        <br>Kolom 3: Tanggal (YYYY-MM-DD)
                        <br>Kolom 4: Jam Masuk (HH:mm)
                        <br>Kolom 5: Jam Pulang (HH:mm)
                        <br>Kolom 6: Status (1 = Masuk, 2 = Libur/Nonaktif)
                        <br>Baris pertama adalah Header dan akan diabaikan.
                    </small>
                    <div class="mt-2">
                        <a href="{{ route('jadwal.template') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i> Download Template Excek</a>
                    </div>
                </div>

                <form action="{{ route('jadwal.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file_excel" class="form-label">Pilih File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Import Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #loading-overlay {
        position: fixed;
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%;
        background: rgba(255, 255, 255, 0.85);
        z-index: 9999;
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(2px);
    }
</style>
@endpush

@section('scripts') <!-- Assuming 'scripts' stack exists, otherwise put directly before endsection but layout likely uses stack -->
<!-- Loading Overlay -->
<div id="loading-overlay">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="mt-3 fw-bold text-dark">Sedang Mengimport Data...</h5>
    <p class="text-muted">Mohon tunggu dan jangan tutup halaman ini.</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const overlay = document.getElementById('loading-overlay');

        if(form && overlay) {
            form.addEventListener('submit', function() {
                overlay.style.display = 'flex';
                // Optional: Disable submit button to prevent double submit
                const btn = this.querySelector('button[type="submit"]');
                if(btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';
                }
            });
        }
    });
</script>
@endsection
