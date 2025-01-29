<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan SR/WO Closed</title>
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
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #009BBF;
            display: flex;
            align-items: center;
        }
        
        .logo-container {
            margin-right: 20px;
        }
        
        .logo {
            height: 50px;
            width: auto;
        }
        
        .header-content {
            flex-grow: 1;
            text-align: center;
        }
        
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #009BBF;
            margin: 5px 0;
        }
        
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .date {
            font-size: 12px;
            color: #888;
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
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .section {
            margin: 15px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #009BBF;
            margin: 10px 0;
            padding-left: 5px;
            border-left: 3px solid #009BBF;
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
        
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            background: #4CAF50;
            color: white;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ $logoSrc }}" class="logo" alt="PLN Logo">
        </div>
        <div class="header-content">
            <div class="title">Laporan Service Request dan Work Order</div>
            <div class="subtitle">Status: Closed</div>
            <div class="date">{{ now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Service Request (SR)</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">ID SR</th>
                    <th width="45%">Deskripsi</th>
                    <th width="15%">Status</th>
                    <th width="20%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($srReports as $index => $sr)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>SR-{{ str_pad($sr->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $sr->description }}</td>
                    <td><span class="status">{{ $sr->status }}</span></td>
                    <td>{{ $sr->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data SR yang closed</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Work Order (WO)</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">ID WO</th>
                    <th width="45%">Deskripsi</th>
                    <th width="15%">Status</th>
                    <th width="20%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($woReports as $index => $wo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>WO-{{ str_pad($wo->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $wo->description }}</td>
                    <td><span class="status">{{ $wo->status }}</span></td>
                    <td>{{ $wo->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data WO yang closed</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} PLN Nusantara Power - Unit Pembangkitan Kendari</p>
        <p>Dokumen ini digenerate pada {{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>
</body>
</html> 