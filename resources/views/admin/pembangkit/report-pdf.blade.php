<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kesiapan Pembangkit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        <img src="{{ public_path('navlogo.png') }}" alt="Logo" style="width: 100px; height: auto;">
        <h1>Laporan Kesiapan Pembangkit</h1>
        <p>Tanggal: {{ date('d/m/Y') }}</p>
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
                <th>Kronologi</th>
                <th>Deskripsi</th>
                <th>Action Plan</th>
                <th>Progres</th>
                <th>Target Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->machine->powerPlant->name }}</td>
                    <td>{{ $log->machine->name }}</td>
                    <td>{{ $log->status }}</td>
                    <td>{{ $log->load_value }}</td>
                    <td>{{ $log->dmn }}</td>
                    <td>{{ $log->dmp }}</td>
                    <td>{{ $log->kronologi }}</td>
                    <td>{{ $log->deskripsi }}</td>
                    <td>{{ $log->action_plan }}</td>
                    <td>{{ $log->progres }}</td>
                    <td>{{ $log->target_selesai ? $log->target_selesai->format('d/m/Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 