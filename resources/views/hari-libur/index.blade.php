@extends('layouts.app')

@section('title', 'Hari Libur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-calendar-event me-2"></i> Data Hari Libur</h4>
        <a href="{{ route('hari-libur.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Hari Libur
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Keterangan</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($libur as $index => $l)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2" style="font-size: 0.9rem">
                                        {{ \Carbon\Carbon::parse($l['tanggal'])->translatedFormat('d F Y') }}
                                    </span>
                                </td>
                                <td class="fw-medium text-dark">{{ $l['keterangan'] }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('hari-libur.edit', $l['ID'] ?? $l['id']) }}" class="btn btn-sm btn-light text-primary shadow-sm mx-1"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('hari-libur.destroy', $l['ID'] ?? $l['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data hari libur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection