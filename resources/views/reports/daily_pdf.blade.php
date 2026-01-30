<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kehadiran Harian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; font-weight: bold; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th.name-col { text-align: left; }
        td.name-col { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        LAPORAN KEHADIRAN PEGAWAI<br>
        {{ strtoupper($organisasi) }}<br>
        {{ strtoupper($tanggal_full) }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 120px;">NIP</th>
                <th class="name-col">Nama</th>
                <th style="width: 80px;">Masuk</th>
                <th style="width: 80px;">Pulang</th>
                <th style="width: 100px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['nip'] }}</td>
                    <td class="name-col">{{ $row['nama'] }}</td>
                    <td>{{ $row['masuk'] }}</td>
                    <td>{{ $row['pulang'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Data Kosong / Tidak Ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
