<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Kehadiran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 60px;
            width: auto;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
        }
        .periode {
            margin-bottom: 10px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .logo img {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $logoSrc }}" alt="PLN Logo">
        </div>
        <div class="company-name">PT PLN (Persero) UPDK KENDARI</div>
        <div class="title">Rekapitulasi Kehadiran Rapat</div>
        <div class="periode">
            Periode: {{ $tanggalAwal }} - {{ $tanggalAkhir }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Divisi</th>
                <th>Jabatan</th>
                <th>Waktu Hadir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $hadir)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $hadir->name }}</td>
                    <td>{{ Carbon\Carbon::parse($hadir->time)->format('d/m/Y') }}</td>
                    <td>{{ $hadir->division }}</td>
                    <td>{{ $hadir->position }}</td>
                    <td>{{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') }}</td>
                    <td>
                        {{ Carbon\Carbon::parse($hadir->time)->format('H:i:s') <= '09:00:00' 
                           ? 'Tepat Waktu' 
                           : 'Terlambat' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 