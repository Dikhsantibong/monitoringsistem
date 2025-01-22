<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Score Card Daily</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #333;
            margin: 0;
            padding: 10px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            margin: 5px 0;
        }
        .score-summary {
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .score-item {
            display: inline-block;
            margin-right: 20px;
        }
        .score-value {
            font-weight: bold;
            color: #0A749B;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0A749B;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SCORE CARD DAILY</h2>
    </div>

    <div class="info-section">
        <div class="info-item">
            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($data['tanggal'])->format('d F Y') }}
        </div>
        <div class="info-item">
            <strong>Lokasi:</strong> {{ $data['lokasi'] }}
        </div>
        <div class="info-item">
            <strong>Waktu:</strong> {{ $data['waktu_mulai'] }} - {{ $data['waktu_selesai'] }}
        </div>
    </div>

    <div class="score-summary">
        <div class="score-item">
            <span>Kesiapan Panitia:</span>
            <span class="score-value">{{ $data['kesiapan_panitia'] }}%</span>
        </div>
        <div class="score-item">
            <span>Kesiapan Bahan:</span>
            <span class="score-value">{{ $data['kesiapan_bahan'] }}%</span>
        </div>
        <div class="score-item">
            <span>Aktivitas Luar:</span>
            <span class="score-value">{{ $data['aktivitas_luar'] }}%</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Peserta</th>
                <th style="width: 10%">Awal</th>
                <th style="width: 10%">Akhir</th>
                <th style="width: 10%">Skor</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['peserta'] as $index => $peserta)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $peserta['jabatan'] }}</td>
                    <td style="text-align: center">{{ $peserta['awal'] }}</td>
                    <td style="text-align: center">{{ $peserta['akhir'] }}</td>
                    <td style="text-align: center">{{ $peserta['skor'] }}</td>
                    <td>{{ $peserta['keterangan'] }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right"><strong>Total Score:</strong></td>
                <td style="text-align: center">{{ $data['total_score'] }}%</td>
                <td>Total dari score peserta dan ketentuan rapat</td>
            </tr>
        </tbody>
    </table>
</body>
</html> 