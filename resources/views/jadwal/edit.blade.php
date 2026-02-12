@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Jadwal Pegawai</h3>
        <a href="{{ route('jadwal.index', ['tanggal' => $jadwal['tanggal']]) }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 600px">
        <div class="card-body">
            <form action="{{ route('jadwal.update', $jadwal['ID'] ?? $jadwal['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tanggal_redirect" value="{{ $jadwal['tanggal'] }}">

                <div class="mb-3">
                    <label class="form-label">Nama Pegawai</label>
                    <input type="text" class="form-control bg-light" value="{{ $jadwal['asn']['nama'] ?? '-' }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($jadwal['tanggal'])->translatedFormat('d F Y') }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ganti Jam Kerja</label>
                    <select name="shift_id" class="form-select" required>
                        @foreach($shifts as $s)
                            <option value="{{ $s['ID'] ?? $s['id'] }}" {{ ($jadwal['shift_id'] == ($s['ID'] ?? $s['id'])) ? 'selected' : '' }}>
                                {{ $s['nama_shift'] }} ({{ $s['jam_masuk'] }} - {{ $s['jam_pulang'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection