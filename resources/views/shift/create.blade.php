@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Tambah Jam Kerja Baru</h3>
        <a href="{{ route('shift.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 600px">
        <div class="card-body">
            <form action="{{ route('shift.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Jam Kerja</label>
                    <input type="text" name="nama_shift" class="form-control" placeholder="Contoh: Jam Pagi" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Jam Kerja</button>
                </div>
            </form>
        </div>
    </div>
@endsection