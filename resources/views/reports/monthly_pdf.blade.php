<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kehadiran Pegawai Bulanan</title>
    <style>
        @page { margin: 15px 40px; }
        body { font-family: sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 10px; font-weight: bold; font-size: 14px; }
        
        /* Revert to standard collapse, relying on memory_limit increase */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 3px; 
            text-align: center; 
        }
        
        th.name-col { text-align: left; width: 120px; }
        td.name-col { text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px; }
        .dark-cell { background-color: #888; }
        .legend { font-size: 9px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        LAPORAN KEHADIRAN PEGAWAI<br>
        {{ strtoupper($organisasi) }}<br>
        BULAN {{ $bulan_tahun }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2" style="width: 70px;">NIP</th>
                <th rowspan="2" class="name-col">Nama</th>
                <th colspan="{{ $days_count }}">Tanggal</th>
                <th rowspan="2" style="width: 15px;">TL</th>
                <th rowspan="2" style="width: 15px;">CP</th>
                <th rowspan="2" style="width: 15px;">TK</th>
                <th rowspan="2" style="width: 15px;">C</th>
                <th rowspan="2" style="width: 15px;">I</th>
                <th rowspan="2" style="width: 15px;">T1</th>
                <th rowspan="2" style="width: 15px;">T2</th>
                <th rowspan="2" style="width: 15px;">T3</th>
                <th rowspan="2" style="width: 15px;">T4</th>
            </tr>
            <tr>
                @for($i = 1; $i <= $days_count; $i++)
                    <th style="width: 16px;">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['nip'] }}</td>
                    <td class="name-col">{{ $row['nama'] }}</td>
                    
                    @for($i = 1; $i <= $days_count; $i++)
                        @php 
                            $dayKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $code = $row['daily'][$dayKey] ?? '';
                            $class = ($code == '') ? 'dark-cell' : '';
                        @endphp
                        <td class="{{ $class }}">{{ $code }}</td>
                    @endfor

                    <td>{{ $row['stats']['tl'] }}</td>
                    <td>{{ $row['stats']['cp'] }}</td>
                    <td>{{ $row['stats']['tk'] }}</td>
                    <td>{{ $row['stats']['c'] }}</td>
                    <td>{{ $row['stats']['i'] }}</td>
                    <td>{{ $row['stats']['t1'] }}</td>
                    <td>{{ $row['stats']['t2'] }}</td>
                    <td>{{ $row['stats']['t3'] }}</td>
                    <td>{{ $row['stats']['t4'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 12 + $days_count }}" style="text-align: center;">Data Kosong / Tidak Ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="legend">
        Keterangan: 
        H: Hadir, C: Cuti, I: Izin, -: Tanpa Keterangan (TK/Alpha), 
        TL: Terlambat, CP: Cepat Pulang, 
        T1: Telat 1-30m, T2: Telat 31-60m, T3: Telat 61-90m, T4: Telat >90m
    </div>
</body>
</html>
