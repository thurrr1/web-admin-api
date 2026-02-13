@extends('layouts.app')

@section('title', 'Tambah Banner')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Upload Banner Baru</h3>
        <a href="{{ route('banner.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm" style="max-width: 600px">
        <div class="card-body">
            <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Judul Banner</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Pengumuman Libur Lebaran" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">File Gambar</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" required>
                    <div class="form-text">Format: JPG, PNG. Maksimal 10MB.</div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Upload Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection