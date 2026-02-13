@extends('layouts.app')

@section('title', 'Edit Admin Organisasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Data Admin</h3>
        <a href="{{ route('super-org.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="row">
        <!-- Kolom Form Edit -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('super-org.update-admin', $asn['ID'] ?? $asn['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ $asn['nama'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control bg-light" value="{{ $asn['nip'] }}" readonly>
                                <small class="text-muted">NIP tidak dapat diubah.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="{{ $asn['jabatan'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bidang / Unit Kerja</label>
                                <input type="text" name="bidang" class="form-control" value="{{ $asn['bidang'] }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $asn['email'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ $asn['no_hp'] ?? '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role / Hak Akses</label>
                            <select name="role_id" class="form-select" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role['ID'] ?? $role['id'] }}" {{ (($asn['role_id'] ?? $asn['RoleID'] ?? 0) == ($role['ID'] ?? $role['id'])) ? 'selected' : '' }}>
                                        {{ $role['nama_role'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Data Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Foto Profil -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4">Foto Profil</h5>
                    
                    @if(!empty($asn['foto']))
                        <div class="mb-3">
                            <img src="{{ env('API_BASE_URL') }}/public/asn/{{ $asn['ID'] ?? $asn['id'] }}/foto" alt="Foto Profil" class="img-fluid rounded shadow-sm" style="max-height: 300px; width: auto;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($asn['nama']) }}&size=200'">
                        </div>
                    @else
                        <div class="alert alert-secondary d-flex align-items-center justify-content-center flex-column" style="height: 200px;">
                            <i class="bi bi-person-x fs-1 mb-2"></i>
                            <span>Foto profil tidak ada</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
