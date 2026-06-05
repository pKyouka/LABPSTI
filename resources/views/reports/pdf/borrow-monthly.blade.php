<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Peminjaman Bulanan - {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #0891b2; padding-bottom: 15px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #0891b2; margin-bottom: 5px; }
        .header h2 { font-size: 14px; font-weight: bold; color: #555; }
        .header p { font-size: 11px; color: #777; margin-top: 5px; }
        .summary-box { background: #ecfeff; border: 1px solid #a5f3fc; border-radius: 6px; padding: 12px; margin-bottom: 20px; }
        .summary-box p { font-size: 12px; font-weight: bold; color: #0e7490; }
        .section { margin-bottom: 20px; }
        .section h3 { font-size: 13px; font-weight: bold; color: #333; margin-bottom: 10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f1f5f9; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; text-align: left; padding: 8px 6px; border: 1px solid #e2e8f0; }
        td { font-size: 10px; padding: 6px; border: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; color: white; }
        .badge-blue { background: #2563eb; }
        .badge-green { background: #059669; }
        .badge-yellow { background: #d97706; }
        .badge-red { background: #dc2626; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .stats-grid { display: flex; gap: 10px; margin-bottom: 15px; }
        .stat-card { flex: 1; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 10px; text-align: center; }
        .stat-card .label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b; }
        .stat-card .value { font-size: 18px; font-weight: bold; color: #0284c7; }
        .day-header { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 4px; padding: 6px 10px; margin-bottom: 8px; font-weight: bold; font-size: 11px; color: #115e59; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LAPORAN PEMINJAMAN ALAT & BARANG</h1>
            <h2>Bulan {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}</h2>
            <p>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</p>
        </div>

        <div class="summary-box">
            <p>Periode: {{ $monthStart->format('d/m/Y') }} - {{ $monthEnd->format('d/m/Y') }}</p>
        </div>

        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Peminjaman</div>
                <div class="value">{{ $totalRecords }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Total Unit</div>
                <div class="value">{{ $totalQuantity }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Jumlah Hari</div>
                <div class="value">{{ count($dailyStats) }}</div>
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
                        <th class="text-center">Jumlah Peminjaman</th>
                        <th class="text-center">Total Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeklyStats as $weekKey => $week)
                        <tr>
                            <td>Minggu {{ $loop->iteration }}</td>
                            <td>{{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m/Y') }}</td>
                            <td class="text-center"><strong>{{ $week['count'] }}</strong></td>
                            <td class="text-center">{{ $week['quantity'] }} unit</td>
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
                        <th class="text-center">Jumlah Peminjaman</th>
                        <th class="text-center">Total Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyStats as $dateStr => $stat)
                        <tr>
                            <td>{{ $stat['date']->format('d/m/Y') }}</td>
                            <td>{{ $stat['date']->locale('id')->isoFormat('dddd') }}</td>
                            <td class="text-center"><strong>{{ $stat['count'] }}</strong></td>
                            <td class="text-center">{{ $stat['quantity'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Detailed Records -->
        @if($records->count() > 0)
            <div class="section">
                <h3>Detail Peminjaman per Hari</h3>
                @foreach($records as $dateStr => $dayRecords)
                    <div class="day-header">
                        {{ \Carbon\Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Peminjam</th>
                                <th>NIM</th>
                                <th class="text-center">Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dayRecords as $record)
                                <tr>
                                    <td>{{ $record->inventoryItem?->name }}</td>
                                    <td>{{ $record->user_name }}</td>
                                    <td>{{ $record->user_id_number }}</td>
                                    <td class="text-center"><strong>{{ $record->quantity }}</strong></td>
                                    <td>
                                        @if($record->status === 'dipinjam')
                                            <span class="badge badge-blue">Dipinjam</span>
                                        @elseif($record->status === 'dikembalikan')
                                            <span class="badge badge-green">Dikembalikan</span>
                                        @elseif($record->status === 'menunggu')
                                            <span class="badge badge-yellow">Menunggu</span>
                                        @else
                                            <span class="badge badge-red">{{ ucfirst($record->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @else
            <div class="section">
                <p style="text-align: center; color: #999; padding: 20px;">Tidak ada peminjaman pada bulan ini</p>
            </div>
        @endif

        <div class="footer">
            <p>Laporan ini dibuat secara otomasis oleh Sistem Informasi Laboratorium</p>
        </div>
    </div>
</body>
</html>
