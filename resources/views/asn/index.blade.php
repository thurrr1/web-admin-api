@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-people me-2"></i> Data Pegawai</h4>
        <div class="d-flex gap-2">
            <form action="{{ route('asn.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIP..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            @php
                    $permissions = session('user')['permissions'] ?? [];
                @endphp
                @if(in_array('kelola_organisasi', $permissions))
            <a href="{{ route('role.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-shield-lock me-1"></i> Role
            </a>
            @endif
            <a href="{{ route('asn.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Nama & NIP</th>
                            <th class="py-3">Jabatan / Bidang</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Status</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $index => $p)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p['nama'] }}</div>
                                    <small class="text-muted">{{ $p['nip'] }}</small>
                                </td>
                                <td>
                                    <div class="text-dark">{{ $p['jabatan'] }}</div>
                                    <small class="text-muted">{{ $p['bidang'] }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $p['role']['nama_role'] ?? $p['Role']['nama_role'] ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $p['is_active'] ? 'success' : 'danger' }} bg-opacity-10 text-{{ $p['is_active'] ? 'success' : 'danger' }} px-3 py-2">
                                        {{ $p['is_active'] ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('asn.reset-device', $p['ID'] ?? $p['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset device ID pegawai ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-warning shadow-sm" title="Reset Device HP"><i class="bi bi-phone"></i></button>
                                    </form>
                                    <a href="{{ route('asn.edit', $p['ID'] ?? $p['id']) }}" class="btn btn-sm btn-light text-primary shadow-sm mx-1"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('asn.destroy', $p['ID'] ?? $p['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pegawai ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data pegawai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection