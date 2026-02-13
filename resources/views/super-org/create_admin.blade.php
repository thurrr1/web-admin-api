@extends('layouts.app')

@section('title', 'Tambah Admin Organisasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-dark fw-bold m-0"><i class="bi bi-person-plus-fill me-2"></i> Tambah Admin</h4>
            <p class="text-muted small mb-0">Untuk Organisasi: <strong>{{ $org['nama_organisasi'] }}</strong></p>
        </div>
        <a href="{{ route('super-org.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 800px">
        <div class="card-body p-4">
            <form action="{{ route('super-org.store-admin', $org['ID']) }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required placeholder="Nomor Induk Pegawai">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password Default <span class="text-danger">*</span></label>
                    <input type="text" name="password" class="form-control" value="{{ old('password', 'admin123') }}" required>
                    <small class="text-muted">Admin bisa mengganti password ini nanti.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', 'Admin') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Bidang / Unit <span class="text-danger">*</span></label>
                        <input type="text" name="bidang" class="form-control" value="{{ old('bidang', 'Sekretariat') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@instansi.go.id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="0812...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Role ID <span class="text-danger">*</span></label>
                    <input type="number" name="role_id" class="form-control" value="{{ old('role_id', 1) }}" required>
                    <small class="text-muted">Masukkan ID Role untuk akun ini (Default: 1 - Admin).</small>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
