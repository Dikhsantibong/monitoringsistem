<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembahasan {{ $discussion->no_pembahasan }}</title>
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
            width: 25%;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
        .commitment-table {
            margin-top: 20px;
        }
        .commitment-table th {
            background-color: #f4f4f4;
        }
        .section-title {
            margin-top: 20px;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        @media print {
            body {
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('logo/navlog1.png') }}" alt="Logo" class="logo">
        <h2>PT PLN NP UP KENDARI</h2>
        <h3>Detail Pembahasan</h3>
    </div>

    <!-- Main Discussion Details -->
    <div class="section-title">Informasi Pembahasan</div>
    <table>
        <tbody>
            <tr>
                <th>No SR</th>
                <td>{{ $discussion->sr_number }}</td>
            </tr>
            <tr>
                <th>No Pembahasan</th>
                <td>{{ $discussion->no_pembahasan }}</td>
            </tr>
            <tr>
                <th>Unit</th>
                <td>{{ $discussion->unit }}</td>
            </tr>
            <tr>
                <th>Topic</th>
                <td>{{ $discussion->topic }}</td>
            </tr>
            <tr>
                <th>Target</th>
                <td>{{ $discussion->target }}</td>
            </tr>
            <tr>
                <th>PIC</th>
                <td>{{ $discussion->pic }}</td>
            </tr>
            <tr>
                <th>Risk Level</th>
                <td>{{ $discussion->risk_level }}</td>
            </tr>
            <tr>
                <th>Priority Level</th>
                <td>{{ $discussion->priority_level }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $discussion->status }}</td>
            </tr>
            <tr>
                <th>Target Deadline</th>
                <td>{{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Commitments Section -->
    <div class="section-title">Daftar Komitmen</div>
    <table class="commitment-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi</th>
                <th>PIC</th>
                <th>Deadline</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($discussion->commitments as $index => $commitment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $commitment->description }}</td>
                    <td>{{ $commitment->pic }}</td>
                    <td>{{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $commitment->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center">Tidak ada komitmen yang tercatat</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Documents Section -->
    @if($discussion->document_path)
    <div class="section-title">Dokumen Pendukung</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @php
                $paths = json_decode($discussion->document_path) ?? [$discussion->document_path];
                $descriptions = json_decode($discussion->document_description) ?? [$discussion->document_description];
            @endphp
            @foreach($paths as $index => $path)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $descriptions[$index] ?? basename($path) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Print Button - Only visible on screen -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">
            Print Dokumen
        </button>
    </div>
</body>
</html> 