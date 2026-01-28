@extends('layouts.app')

@section('title', 'Tambah Lokasi Kantor')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Tambah Lokasi Kantor</h3>
        <a href="{{ route('organisasi.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 800px">
        <div class="card-body">
            <form action="{{ route('organisasi.store-lokasi') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Lokasi</label>
                    <input type="text" name="nama_lokasi" class="form-control" placeholder="Contoh: Kantor Pusat" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" placeholder="-0.9416" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" placeholder="100.3700" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Radius Absensi (Meter)</label>
                    <input type="number" name="radius_meter" class="form-control" value="50" required>
                    <div class="form-text">Jarak maksimal pegawai bisa absen dari titik koordinat.</div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection