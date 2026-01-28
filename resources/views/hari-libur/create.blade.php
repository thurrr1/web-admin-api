@extends('layouts.app')

@section('title', 'Tambah Hari Libur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Tambah Hari Libur</h3>
        <a href="{{ route('hari-libur.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 600px">
        <div class="card-body">
            <form action="{{ route('hari-libur.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: HUT RI ke-79" required>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection