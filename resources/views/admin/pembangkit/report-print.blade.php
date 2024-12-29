<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kesiapan Pembangkit</title>
    <style>
        /* CSS untuk tampilan print */
        @media print {
            body { 
                margin: 0; 
                padding: 20px; 
                font-family: Arial, sans-serif;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo {
                max-width: 150px;
                margin-bottom: 10px;
                float: left;
            }
            .date-info {
                margin-bottom: 20px;
                font-size: 14px;
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
                background-color: #f8f9fa; 
                font-weight: bold;
            }
            .status-cell {
                font-weight: bold;
            }
            .status-operasi { color: green; }
            .status-gangguan { color: red; }
            .status-standby { color: orange; }
            .footer {
                margin-top: 30px;
                text-align: right;
                font-size: 12px;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <img src="{{ asset('logo/navlogo.png') }}" alt="Logo" class="logo">
        <h1>Laporan Kesiapan Pembangkit</h1>
    </div>

    <div class="date-info">
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse(request('date', date('Y-m-d')))->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        <p><strong>Waktu Cetak:</strong> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('HH:mm') }} WIB</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Unit</th>
                <th>Mesin</th>
                <th>Status</th>
                <th>Beban</th>
                <th>DMN</th>
                <th>DMP</th>
            
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $log->machine->powerPlant->name }}</td>
                <td>{{ $log->machine->name }}</td>
                <td class="status-cell status-{{ strtolower($log->status) }}">
                    {{ $log->status }}
                </td>
                <td>{{ $log->load_value }}</td>
                <td>{{ $log->dmn }}</td>
                <td>{{ $log->dmp }}</td>
               
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }}</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</p>
    </div>
</body>
</html> 