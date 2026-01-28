@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-shield-check me-2"></i> Edit Role</h4>
        <a href="{{ route('role.index') }}" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 800px">
        <div class="card-body p-4">
            <form action="{{ route('role.update', $role['ID'] ?? $role['id']) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama Role</label>
                    <input type="text" name="nama_role" class="form-control" value="{{ $role['nama_role'] }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold mb-3">Pilih Hak Akses (Permission)</label>
                    <div class="row">
                        @php
                            // Ambil ID permission yang sudah dimiliki role ini
                            $currentPerms = array_column($role['permissions'] ?? [], 'ID');
                            if (empty($currentPerms)) {
                                $currentPerms = array_column($role['permissions'] ?? [], 'id');
                            }
                        @endphp
                        
                        @foreach($permissions as $p)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                           value="{{ $p['ID'] ?? $p['id'] }}" 
                                           id="perm_{{ $p['ID'] ?? $p['id'] }}"
                                           {{ in_array($p['ID'] ?? $p['id'], $currentPerms) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $p['ID'] ?? $p['id'] }}">
                                        {{ $p['nama_permission'] }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary shadow-sm px-4"><i class="bi bi-save me-2"></i> Update Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection