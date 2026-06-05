<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penggunaan Lab Bulanan - {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #e11d48; padding-bottom: 15px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #e11d48; margin-bottom: 5px; }
        .header h2 { font-size: 14px; font-weight: bold; color: #555; }
        .header p { font-size: 11px; color: #777; margin-top: 5px; }
        .summary-box { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 6px; padding: 12px; margin-bottom: 20px; }
        .summary-box p { font-size: 12px; font-weight: bold; color: #be123c; }
        .section { margin-bottom: 20px; }
        .section h3 { font-size: 13px; font-weight: bold; color: #333; margin-bottom: 10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f1f5f9; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; text-align: left; padding: 8px 6px; border: 1px solid #e2e8f0; }
        td { font-size: 10px; padding: 6px; border: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) { background: #f8fafc; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .stats-grid { display: flex; gap: 10px; margin-bottom: 15px; }
        .stat-card { flex: 1; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 6px; padding: 10px; text-align: center; }
        .stat-card .label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b; }
        .stat-card .value { font-size: 18px; font-weight: bold; color: #e11d48; }
        .day-header { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 4px; padding: 6px 10px; margin-bottom: 8px; font-weight: bold; font-size: 11px; color: #9f1239; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LAPORAN PENGGUNAAN LAB</h1>
            <h2>Bulan {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}</h2>
            <p>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</p>
        </div>

        <div class="summary-box">
            <p>Periode: {{ $monthStart->format('d/m/Y') }} - {{ $monthEnd->format('d/m/Y') }}</p>
        </div>

        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Penggunaan</div>
                <div class="value">{{ $totalRecords }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Jumlah Hari</div>
                <div class="value">{{ count($dailyStats) }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Jumlah Minggu</div>
                <div class="value">{{ count($weeklyStats) }}</div>
            </div>
        </div>

        <!-- Weekly Summary -->
        <div class="section">
            <h3>Ringkasan per Minggu</h3>
            <table>
                <thead>
                    <tr>
                        <th>Minggu</th>
                        <th>Periode</th>
                        <th class="text-center">Jumlah Penggunaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeklyStats as $weekKey => $week)
                        <tr>
                            <td>Minggu {{ $loop->iteration }}</td>
                            <td>{{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m/Y') }}</td>
                            <td class="text-center"><strong>{{ $week['count'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Daily Stats -->
        <div class="section">
            <h3>Statistik Harian</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th class="text-center">Jumlah Penggunaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyStats as $dateStr => $stat)
                        <tr>
                            <td>{{ $stat['date']->format('d/m/Y') }}</td>
                            <td>{{ $stat['date']->locale('id')->isoFormat('dddd') }}</td>
                            <td class="text-center"><strong>{{ $stat['count'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Detailed Records -->
        @if($records->count() > 0)
            <div class="section">
                <h3>Detail Penggunaan per Hari</h3>
                @foreach($records as $dateStr => $dayRecords)
                    <div class="day-header">
                        {{ \Carbon\Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Lab</th>
                                <th>Pengguna</th>
                                <th>NIM</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dayRecords as $record)
                                <tr>
                                    <td>{{ $record->category?->name }}</td>
                                    <td>{{ $record->user_name }}</td>
                                    <td>{{ $record->user_id_number }}</td>
                                    <td>{{ $record->start_time->format('H:i') }} - {{ $record->end_time->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @else
            <div class="section">
                <p style="text-align: center; color: #999; padding: 20px;">Tidak ada penggunaan lab pada bulan ini</p>
            </div>
        @endif

        <div class="footer">
            <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi Laboratorium</p>
        </div>
    </div>
</body>
</html>
