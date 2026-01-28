@extends('layouts.app')

@section('title', 'Edit Hari Libur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Hari Libur</h3>
        <a href="{{ route('hari-libur.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 600px">
        <div class="card-body">
            <form action="{{ route('hari-libur.update', $libur['ID'] ?? $libur['id']) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $libur['tanggal'] }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="{{ $libur['keterangan'] }}" required>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection