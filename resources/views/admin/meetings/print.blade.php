<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan Rapat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Rapat</h1>
        <p>Tanggal: {{ request('date') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Jabatan</th>
                <th>Waktu Hadir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($scoreCards as $index => $scoreCard)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $scoreCard->peserta }}</td>
                    <td>{{ $scoreCard->jabatan }}</td>
                    <td>{{ $scoreCard->waktu_hadir }}</td>
                    <td>{{ $scoreCard->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html> 