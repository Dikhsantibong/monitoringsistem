<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembahasan Lain-lain</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 240px;
            height: auto;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 0.5px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
        .commitment-list {
            margin: 0;
            padding-left: 15px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo/navlog1.png') }}" alt="Logo" class="logo">
        <h2>PT PLN NP UP KENDARI</h2>
        <h3>Laporan Pembahasan</h3>
        @if(request('start_date') && request('end_date'))
            <p>Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No SR</th>
                <th>No Pembahasan</th>
                <th>Unit</th>
                <th>Topic</th>
                <th>Target</th>
                <th>PIC</th>
                <th>Status</th>
                <th>Deadline</th>
            </tr>
        </thead>
        <tbody>
            @forelse($discussions as $index => $discussion)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $discussion->sr_number }}</td>
                    <td>{{ $discussion->no_pembahasan }}</td>
                    <td>{{ $discussion->unit }}</td>
                    <td>{{ $discussion->topic }}</td>
                    <td>{{ $discussion->target }}</td>
                    <td>{{ $discussion->pic }}</td>
                    <td>{{ $discussion->status }}</td>
                    <td>{{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}</td>
                </tr>
                @if($discussion->commitments->count() > 0)
                    <tr>
                        <td colspan="9">
                            <strong>Commitments:</strong>
                            <ul class="commitment-list">
                                @foreach($discussion->commitments as $commitment)
                                    <li>
                                        {{ $commitment->description }} 
                                        (PIC: {{ $commitment->pic }}, 
                                        Deadline: {{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}, 
                                        Status: {{ $commitment->status }})
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" style="text-align: center">Tidak ada data yang tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 