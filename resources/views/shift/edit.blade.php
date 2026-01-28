@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Jam Kerja</h3>
        <a href="{{ route('shift.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 600px">
        <div class="card-body">
            <form action="{{ route('shift.update', $shift['ID'] ?? $shift['id']) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Nama Jam Kerja</label>
                    <input type="text" name="nama_shift" class="form-control" value="{{ $shift['nama_shift'] }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" value="{{ $shift['jam_masuk'] }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" value="{{ $shift['jam_pulang'] }}" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Jam Kerja</button>
                </div>
            </form>
        </div>
    </div>
@endsection