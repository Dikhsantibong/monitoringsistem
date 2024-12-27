<!DOCTYPE html>
<html>
<head>
    <title>Print Score Card</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold">Score Card for {{ $date }}</h1>
        <table class="min-w-full mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peserta</th>
                    <th>Awal</th>
                    <th>Akhir</th>
                    <th>Score</th>
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
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html> 