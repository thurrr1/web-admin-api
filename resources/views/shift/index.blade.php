@extends('layouts.app')

@section('title', 'Data Shift')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-clock me-2"></i> Data Jam Kerja</h4>
        <a href="{{ route('shift.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jam Kerja
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Nama Jam Kerja</th>
                            <th class="py-3">Jam Masuk</th>
                            <th class="py-3">Jam Pulang</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $index => $s)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $s['nama_shift'] }}</td>
                                <td><span class="badge bg-success bg-opacity-10 text-success px-3">{{ $s['jam_masuk'] }}</span></td>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger px-3">{{ $s['jam_pulang'] }}</span></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('shift.edit', $s['ID'] ?? $s['id']) }}" class="btn btn-sm btn-light text-primary shadow-sm mx-1"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('shift.destroy', $s['ID'] ?? $s['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus shift ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data shift.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <small class="text-muted">* Jam Kerja yang sedang digunakan dalam jadwal tidak dapat dihapus.</small>
    </div>
@endsection