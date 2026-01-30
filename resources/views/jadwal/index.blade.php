@extends('layouts.app')

@section('title', 'Jadwal Harian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-calendar-week me-2"></i> Jadwal Kerja Pegawai</h4>
        <div>
            <a href="{{ route('reports.daily', ['tanggal' => $tanggal]) }}" class="btn btn-outline-danger shadow-sm me-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Rekap Harian
            </a>
            <a href="{{ route('jadwal.import.view') }}" class="btn btn-outline-success shadow-sm me-2">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </a>
            <a href="{{ route('jadwal.generate') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-calendar-plus me-1"></i> Buat Jadwal Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('jadwal.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label for="tanggal" class="form-label fw-bold">Pilih Tanggal:</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
                </div>
                <div class="col-auto">
                    <label for="search" class="form-label fw-bold">Cari Pegawai:</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama / NIP..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-search me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary">Daftar Jadwal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
                @if(!empty($jadwal) && count($jadwal) > 0)
                    <form action="{{ route('jadwal.destroy-date') }}" method="POST" onsubmit="return confirm('PERINGATAN: Anda akan menghapus SEMUA jadwal pegawai pada tanggal {{ $tanggal }}. Lanjutkan?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"><i class="bi bi-trash me-1"></i> Hapus Semua Jadwal Hari Ini</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Nama Pegawai</th>
                            <th class="py-3">Nama Shift</th>
                            <th class="py-3">Jam Kerja</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Status Kehadiran</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $index => $j)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $j['asn']['nama'] ?? '-' }}</div>
                                    <small class="text-muted">{{ $j['asn']['nip'] ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info px-3">{{ $j['shift']['nama_shift'] ?? '-' }}</span>
                                </td>
                                <td>
                                    {{ $j['shift']['jam_masuk'] ?? '00:00' }} - {{ $j['shift']['jam_pulang'] ?? '00:00' }}
                                </td>
                                <td>
                                    <form action="{{ route('jadwal.update', $j['ID'] ?? $j['id']) }}" method="POST" id="form-active-{{ $j['ID'] ?? $j['id'] }}">
                                        @csrf @method('PUT')
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_active" value="false">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="true" 
                                                onchange="document.getElementById('form-active-{{ $j['ID'] ?? $j['id'] }}').submit()"
                                                {{ ($j['is_active'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="flexSwitchCheckDefault">
                                                {{ ($j['is_active'] ?? true) ? 'Hari Kerja' : 'Hari Libur' }}
                                            </label>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    @php
                                        $status = $j['status_kehadiran'] ?? 'BELUM ABSEN';
                                        $badgeClass = 'bg-secondary';
                                        if ($status == 'HADIR') {
                                            $badgeClass = 'bg-success';
                                        } elseif ($status == 'IZIN' || $status == 'CUTI') {
                                            $badgeClass = 'bg-info text-dark';
                                        } elseif (strpos($status, 'TERLAMBAT') !== false) {
                                            $badgeClass = 'bg-warning text-dark';
                                        } elseif ($status == 'ALPHA') {
                                            $badgeClass = 'bg-danger';
                                            $status = 'Tanpa Keterangan';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    @if(!empty($j['jam_masuk_real']))
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                            In: {{ \Carbon\Carbon::parse($j['jam_masuk_real'])->format('H:i') }}
                                            @if(!empty($j['jam_pulang_real']))
                                                | Out: {{ \Carbon\Carbon::parse($j['jam_pulang_real'])->format('H:i') }}
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('jadwal.edit', $j['ID'] ?? $j['id']) }}" class="btn btn-sm btn-light text-primary shadow-sm mx-1"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('jadwal.destroy', $j['ID'] ?? $j['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal pegawai ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Tidak ada jadwal pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection