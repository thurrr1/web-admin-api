@extends('layouts.app')

@section('title', 'Tambah Pegawai')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-person-plus me-2"></i> Tambah Pegawai</h4>
        <a href="{{ route('asn.index') }}" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 800px">
        <div class="card-body p-4">
            
            {{-- Tampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Section Import Excel --}}
            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-spreadsheet me-2"></i> Import Data dari Excel</h6>
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <form action="{{ route('asn.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="form-label small text-muted">Upload file Excel (.xlsx / .csv)</label>
                                <div class="input-group">
                                    <input type="file" name="file_excel" class="form-control" required>
                                    <button type="submit" class="btn btn-success text-white">
                                        <i class="bi bi-upload me-1"></i> Import
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('asn.template') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-square me-2"></i> Input Manual</h6>

            <form action="{{ route('asn.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Default</label>
                    <input type="text" name="password" class="form-control" value="{{ old('password', '123456') }}" required>
                    <small class="text-muted">Pegawai bisa mengganti password ini nanti di aplikasi.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bidang / Unit Kerja</label>
                        <input type="text" name="bidang" class="form-control" value="{{ old('bidang') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Contoh: pegawai@instansi.go.id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role / Hak Akses</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role['ID'] ?? $role['id'] }}" {{ old('role_id') == ($role['ID'] ?? $role['id']) ? 'selected' : '' }}>
                                {{ $role['nama_role'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary shadow-sm px-4"><i class="bi bi-save me-2"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection