@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-shield-plus me-2"></i> Tambah Role Baru</h4>
        <a href="{{ route('role.index') }}" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 800px">
        <div class="card-body p-4">
            <form action="{{ route('role.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama Role</label>
                    <input type="text" name="nama_role" class="form-control" placeholder="Contoh: HRD, Supervisor" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold mb-3">Pilih Hak Akses (Permission)</label>
                    <div class="row">
                        @foreach($permissions as $p)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p['ID'] ?? $p['id'] }}" id="perm_{{ $p['ID'] ?? $p['id'] }}">
                                    <label class="form-check-label" for="perm_{{ $p['ID'] ?? $p['id'] }}">
                                        {{ $p['nama_permission'] }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary shadow-sm px-4"><i class="bi bi-save me-2"></i> Simpan Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection