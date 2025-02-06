<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .logo {
            height: 60px;
            margin-bottom: 10px;
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
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        <h2>PT PLN (Persero) UPDK KENDARI</h2>
        <h3>{{ $title }}</h3>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                @if($type == 'sr')
                    <th>ID SR</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Prioritas</th>
                    <th>Unit</th>
                    <th>Tanggal</th>
                @elseif($type == 'wo')
                    <th>ID WO</th>
                    <th>Unit</th>
                    <th>Deskripsi</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Prioritas</th>
                  
                @else
                    <th>ID Backlog</th>
                    <th>No WO</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Backlog</th>
                    <th>Status</th>
                    <th>Unit</th>
                    <th>Keterangan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    @if($type == 'sr')
                        <td>SR-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->priority }}</td>
                        <td>{{ optional($item->powerPlant)->name ?? '-' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    @elseif($type == 'wo')
                        <td>WO-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional($item->powerPlant)->name ?? '-' }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->priority }}</td>
                        
                    @else
                        <td>BL-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $item->no_wo }}</td>
                        <td>{{ $item->deskripsi }}</td>
                        <td>{{ date('d/m/Y', strtotime($item->tanggal_backlog)) }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ optional($item->powerPlant)->name ?? '-' }}</td>
                        <td>{{ $item->keterangan }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html> 