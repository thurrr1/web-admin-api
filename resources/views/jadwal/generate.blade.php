@extends('layouts.app')

@section('title', 'Generate Jadwal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Generate Jadwal Bulanan</h3>
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('jadwal.store-generate') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tipe Jadwal</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_generate" id="tipeRange" value="range" checked onchange="toggleForm()">
                                    <label class="form-check-label" for="tipeRange">Range Tanggal</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_generate" id="tipeHarian" value="harian" onchange="toggleForm()">
                                    <label class="form-check-label" for="tipeHarian">Harian (Tanggal Spesifik)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3" id="formRange">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="formHarian">
                            <label class="form-label">Pilih Tanggal</label>
                            <input type="date" name="tanggal" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Jam Kerja</label>
                            <select name="shift_id" class="form-select" required>
                                <option value="">-- Pilih Jam Kerja --</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s['ID'] ?? $s['id'] }}">
                                        {{ $s['nama_shift'] }} ({{ $s['jam_masuk'] }} - {{ $s['jam_pulang'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" id="divWeekend">
                            <div class="mb-3">
                                <label class="form-label d-block">Pilih Hari Kerja (Jadwal akan digenerate pada hari yang dicentang)</label>
                                <div class="d-flex flex-wrap gap-3 p-3 border rounded bg-light">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="1" id="day1" checked>
                                        <label class="form-check-label" for="day1">Senin</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="2" id="day2" checked>
                                        <label class="form-check-label" for="day2">Selasa</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="3" id="day3" checked>
                                        <label class="form-check-label" for="day3">Rabu</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="4" id="day4" checked>
                                        <label class="form-check-label" for="day4">Kamis</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="5" id="day5" checked>
                                        <label class="form-check-label" for="day5">Jumat</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="6" id="day6">
                                        <label class="form-check-label" for="day6">Sabtu</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="0" id="day0">
                                        <label class="form-check-label" for="day0">Minggu</label>
                                    </div>
                                </div>
                                <div class="form-text text-primary"><i class="bi bi-info-circle"></i> Tanggal merah (Hari Libur Nasional) akan otomatis dilewati.</div>
                            </div>
                            
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Pegawai</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label" for="checkAll">Pilih Semua Pegawai</label>
                            </div>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($pegawai as $p)
                                    <div class="form-check">
                                        <input class="form-check-input asn-checkbox" type="checkbox" name="asn_ids[]" value="{{ $p['ID'] ?? $p['id'] }}" id="asn_{{ $p['ID'] ?? $p['id'] }}">
                                        <label class="form-check-label" for="asn_{{ $p['ID'] ?? $p['id'] }}">
                                            {{ $p['nama'] }} <span class="text-muted">({{ $p['nip'] }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Buat Jadwal</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="alert alert-info">
                <h5>Petunjuk</h5>
                <p>Fitur ini digunakan untuk membuat jadwal kerja pegawai.</p>
                <ul>
                    <li><strong>Range Tanggal:</strong> Membuat jadwal otomatis untuk rentang tanggal tertentu (bisa lewati weekend).</li>
                    <li><strong>Harian:</strong> Membuat jadwal untuk satu tanggal spesifik saja (misal: jadwal lembur atau pengganti).</li>
                    <li>Pilih Shift yang berlaku (misal: Shift Pagi).</li>
                    <li>Centang pegawai yang akan dijadwalkan.</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.asn-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        function toggleForm() {
            const isRange = document.getElementById('tipeRange').checked;
            const formRange = document.getElementById('formRange');
            const formHarian = document.getElementById('formHarian');
            const divWeekend = document.getElementById('divWeekend');

            if (isRange) {
                formRange.classList.remove('d-none');
                formHarian.classList.add('d-none');
                divWeekend.classList.remove('d-none');
            } else {
                formRange.classList.add('d-none');
                formHarian.classList.remove('d-none');
                divWeekend.classList.add('d-none');
            }
        }
    </script>
@endsection