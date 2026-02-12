@extends('layouts.app')

@section('title', 'Edit Organisasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-pencil-square me-2"></i> Edit Organisasi</h4>
        <a href="{{ route('super-org.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('super-org.update', $org['ID']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Organisasi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_organisasi" class="form-control @error('nama_organisasi') is-invalid @enderror" value="{{ old('nama_organisasi', $org['nama_organisasi']) }}" required>
                    @error('nama_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Admin</label>
                    <input type="email" name="email_admin" class="form-control @error('email_admin') is-invalid @enderror" value="{{ old('email_admin', $org['email_admin'] ?? '') }}">
                    @error('email_admin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Update Organisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
