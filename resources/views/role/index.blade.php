@extends('layouts.app')

@section('title', 'Kelola Role')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-shield-lock me-2"></i> Kelola Role & Permission</h4>
        <div>
            <a href="{{ route('asn.index') }}" class="btn btn-light shadow-sm me-2"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
            <a href="{{ route('role.create') }}" class="btn btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Role</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Nama Role</th>
                            <th class="py-3">Hak Akses (Permission)</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $index => $r)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $r['nama_role'] }}</td>
                                <td>
                                    @foreach($r['permissions'] ?? [] as $p)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary mb-1">{{ $p['nama_permission'] }}</span>
                                    @endforeach
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('role.edit', $r['ID'] ?? $r['id']) }}" class="btn btn-sm btn-light text-primary shadow-sm mx-1"><i class="bi bi-pencil"></i></a>
                                    @if($r['nama_role'] !== 'Admin') 
                                        <form action="{{ route('role.destroy', $r['ID'] ?? $r['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus role ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data role.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection