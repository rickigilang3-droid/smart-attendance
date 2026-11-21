<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 3px 0 0; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN PRESENSI KEHADIRAN SISWA</h2>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }} | Kelas: {{ $selectedClass ? $selectedClass->name : 'Semua Kelas' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">NISN</th>
                <th>Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="12%">Jam Masuk</th>
                <th width="12%">Jam Pulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->date }}</td>
                    <td>{{ $log->student->nisn ?? '-' }}</td>
                    <td><strong>{{ $log->student->name ?? '-' }}</strong></td>
                    <td>{{ $log->student->schoolClass->name ?? '-' }}</td>
                    <td>{{ $log->scan_in ?? '-' }}</td>
                    <td>{{ $log->scan_out ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

</body>
</html>