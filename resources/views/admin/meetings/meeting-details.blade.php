<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Details</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Include your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #0A749B;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Meeting Details for {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h1>

    @if($scoreCards->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peserta</th>
                    <th>Awal</th>
                    <th>Akhir</th>
                    <th>Score</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scoreCards as $index => $scoreCard)
                    @foreach($scoreCard['peserta'] as $peserta)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $peserta['jabatan'] }}</td>
                            <td>{{ $peserta['awal'] }}</td>
                            <td>{{ $peserta['akhir'] }}</td>
                            <td>{{ $peserta['skor'] }}</td>
                            <td>-</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada data untuk tanggal ini.</p>
    @endif
</body>
</html>