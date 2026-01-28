@extends('layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Data Pegawai</h3>
        <a href="{{ route('asn.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 800px">
        <div class="card-body">
            <form action="{{ route('asn.update', $asn['ID'] ?? $asn['id']) }}" method="POST">
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

                <div class="mb-3">
                    <label class="form-label">Status Akun</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $asn['is_active'] ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$asn['is_active'] ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <div class="form-text">Jika nonaktif, pegawai tidak bisa login ke aplikasi.</div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection