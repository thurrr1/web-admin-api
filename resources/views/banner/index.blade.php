@extends('layouts.app')

@section('title', 'Banner Informasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-card-image me-2"></i> Banner Informasi</h4>
        <a href="{{ route('banner.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Banner
        </a>
    </div>

    <div class="row">
        @forelse($banners as $b)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden {{ $b['is_active'] ? '' : 'bg-light opacity-75' }}">
                    <!-- Gambar Banner -->
                    <div class="position-relative">
                        <img src="{{ $b['foto'] }}" class="card-img-top {{ $b['is_active'] ? '' : 'grayscale' }}" alt="{{ $b['title'] }}" style="height: 200px; object-fit: cover;">
                        @if(!$b['is_active'])
                            <div class="position-absolute top-50 start-50 translate-middle badge bg-dark px-3 py-2">NONAKTIF</div>
                        @endif
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-dark">{{ $b['title'] }}</h5>
                        <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                            <span class="badge {{ $b['is_active'] ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} px-3">
                                {{ $b['is_active'] ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            
                            <form action="{{ route('banner.toggle', $b['ID'] ?? $b['id']) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm {{ $b['is_active'] ? 'btn-light text-danger' : 'btn-success text-white' }} shadow-sm" 
                                        title="{{ $b['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi {{ $b['is_active'] ? 'bi-power' : 'bi-check-lg' }}"></i> 
                                    {{ $b['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    Belum ada banner aktif. Silakan tambahkan banner baru.
                </div>
            </div>
        @endforelse
    </div>

    <style>
        .grayscale { filter: grayscale(100%); }
    </style>
@endsection