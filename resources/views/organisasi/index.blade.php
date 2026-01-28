@extends('layouts.app')

@section('title', 'Profil Organisasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-building me-2"></i> Profil Organisasi</h4>
    </div>

    @if($org)
        @php
            // Handle kemungkinan key 'Lokasi' (PascalCase) atau 'lokasi' (snake_case)
            $lokasis = $org['lokasis'] ?? $org['Lokasis'] ?? [];
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-uppercase small ls-1">Informasi Umum</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editOrganisasiModal">
                    <i class="bi bi-pencil me-1"></i> Edit Profil
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 fw-bold">Nama Organisasi</div>
                    <div class="col-md-9">: {{ $org['nama_organisasi'] }}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3 fw-bold">Email Admin</div>
                    <div class="col-md-9">: {{ $org['email_admin'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-uppercase small ls-1">Daftar Lokasi Kantor (Titik Absensi)</h6>
                <a href="{{ route('organisasi.create-lokasi') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Lokasi
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Nama Lokasi</th>
                                <th class="py-3">Alamat</th>
                                <th class="py-3">Koordinat</th>
                                <th class="py-3">Radius</th>
                                <th class="text-end pe-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lokasis as $loc)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $loc['nama_lokasi'] }}</td>
                                    <td>{{ $loc['alamat'] }}</td>
                                    <td>{{ $loc['latitude'] }}, {{ $loc['longitude'] }}</td>
                                    <td>{{ $loc['radius_meter'] }} m</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('organisasi.edit', $loc['ID'] ?? $loc['id']) }}" class="btn btn-sm btn-light text-primary shadow-sm mx-1"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('organisasi.destroy-lokasi', $loc['ID'] ?? $loc['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus lokasi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada data lokasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Edit Organisasi -->
        <div class="modal fade" id="editOrganisasiModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profil Organisasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('organisasi.update-info') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Organisasi</label>
                                <input type="text" name="nama_organisasi" class="form-control" value="{{ $org['nama_organisasi'] }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Admin</label>
                                <input type="email" name="email_admin" class="form-control" value="{{ $org['email_admin'] ?? '' }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">Gagal memuat data organisasi.</div>
    @endif
@endsection