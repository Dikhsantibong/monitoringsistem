@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Laporan SR/WO Closed</title>
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
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
        <h2>Laporan SR/WO Closed</h2>
        <p>Tanggal: {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tipe</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($srReports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>SR</td>
                <td>{{ $report->id }}</td>
                <td>{{ Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ $report->status }}</td>
            </tr>
            @endforeach
            @foreach($woReports as $index => $report)
            <tr>
                <td>{{ count($srReports) + $index + 1 }}</td>
                <td>WO</td>
                <td>{{ $report->id }}</td>
                <td>{{ Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ $report->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    @push('scripts')
        
    @endpush
</body>
</html>