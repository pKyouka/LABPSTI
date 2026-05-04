<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 36px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #0f172a;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 42%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-26deg);
            font-size: 58px;
            font-weight: 900;
            letter-spacing: 0.12em;
            color: rgba(34, 197, 94, 0.18);
            border: 6px solid rgba(34, 197, 94, 0.18);
            padding: 18px 28px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 12px;
        }

        .title {
            font-size: 20px;
            font-weight: 800;
            color: #14532d;
            margin: 0 0 6px 0;
        }

        .subtitle {
            margin: 0;
            color: #475569;
        }

        .badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 8px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .section {
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 8px 0;
        }

        .label {
            width: 170px;
            color: #64748b;
            font-weight: 700;
        }

        .value {
            color: #0f172a;
            font-weight: 600;
        }

        .footer {
            margin-top: 28px;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="watermark">SUDAH DI ACC</div>

    <div class="header">
        <div>
            <h1 class="title">Bukti Peminjaman Alat & Barang</h1>
            <p class="subtitle">Dokumen ini diterbitkan setelah peminjaman disetujui admin.</p>
        </div>
        <div class="badge">Disetujui</div>
    </div>

    <div class="section">
        <table>
            <tr>
                <td class="label">Nama Peminjam</td>
                <td class="value">{{ $record->user_name }}</td>
            </tr>
            <tr>
                <td class="label">NIM / ID</td>
                <td class="value">{{ $record->user_id_number }}</td>
            </tr>
            <tr>
                <td class="label">Barang</td>
                <td class="value">{{ $record->inventoryItem?->name }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah</td>
                <td class="value">{{ $record->quantity }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Pinjam</td>
                <td class="value">{{ $record->borrowed_at?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Ready</td>
                <td class="value">{{ $record->ready_at?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Bisa Diambil</td>
                <td class="value">{{ $record->pickup_at?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Status ACC</td>
                <td class="value">{{ $record->approval_status }}</td>
            </tr>
            <tr>
                <td class="label">Disetujui Oleh</td>
                <td class="value">{{ $record->approver?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal ACC</td>
                <td class="value">{{ $record->approved_at?->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <td class="label">Keperluan</td>
                <td class="value">{{ $record->purpose }}</td>
            </tr>
            <tr>
                <td class="label">Catatan</td>
                <td class="value">{{ $record->notes ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        PDF ini memiliki watermark hijau sebagai penanda bahwa peminjaman sudah di-ACC admin.
        Keaslian file dapat diverifikasi dari hash MD5 dan SHA-256 yang tersimpan di sistem.
    </div>
</body>
</html>
