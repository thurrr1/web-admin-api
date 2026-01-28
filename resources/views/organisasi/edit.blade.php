@extends('layouts.app')

@section('title', 'Edit Lokasi Kantor')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Lokasi Kantor</h3>
        <a href="{{ route('organisasi.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 800px">
        <div class="card-body">
            <form action="{{ route('organisasi.update', $lokasi['ID'] ?? $lokasi['id'] ?? 0) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Lokasi</label>
                    <input type="text" name="nama_lokasi" class="form-control" value="{{ $lokasi['nama_lokasi'] }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" required>{{ $lokasi['alamat'] }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="{{ $lokasi['latitude'] }}" required>
                        <div class="form-text">Contoh: -0.9416</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="{{ $lokasi['longitude'] }}" required>
                        <div class="form-text">Contoh: 100.3700</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Radius Absensi (Meter)</label>
                    <input type="number" name="radius_meter" class="form-control" value="{{ $lokasi['radius_meter'] }}" required>
                    <div class="form-text">Jarak maksimal pegawai bisa absen dari titik koordinat.</div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection