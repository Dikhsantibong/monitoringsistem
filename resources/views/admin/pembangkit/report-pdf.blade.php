<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kesiapan Pembangkit</title>
    <style>
        @page {
            margin: 1cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #009BBF;
        }
        
        .logo {
            height: 50px;
            width: auto;
            margin-bottom: 10px;
        }
        
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #009BBF;
            margin: 5px 0;
        }
        
        .date {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        
        th {
            background-color: #009BBF;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: normal;
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-cell {
            font-weight: bold;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo/navlog1.png') }}" class="logo" alt="Logo PLN">
        <div class="title">Laporan Kesiapan Pembangkit</div>
        <div class="date">Tanggal: {{ now()->translatedFormat('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="20%">Unit</th>
                <th width="20%">Mesin</th>
                <th width="17%">DMN</th>
                <th width="17%">DMP</th>
                <th width="18%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->machine->powerPlant->name }}</td>
                    <td>{{ $log->machine->name }}</td>
                    <td>{{ $log->dmn ?: '-' }}</td>
                    <td>{{ $log->dmp ?: '-' }}</td>
                    <td class="status-cell">{{ $log->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data yang tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} PLN Nusantara Power - Unit Pembangkitan Kendari</p>
        <p>Dokumen ini digenerate pada {{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>
</body>
</html> 