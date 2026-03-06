<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan Pembahasan Lain-lain</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .page-break {
            page-break-before: always;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        @media print {
            .no-print {
                display: none;
            }
            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <h1 style="margin: 0; font-size: 18px;">LAPORAN MINGGUAN PEMBAHASAN LAIN-LAIN</h1>
        <p style="margin: 5px 0;">Periode: {{ $filters['start_date'] ?? 'Semua' }} s/d {{ $filters['end_date'] ?? 'Semua' }}</p>
    </div>

    <div class="section-title">Data Pembahasan</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">Unit</th>
                <th style="width: 100px;">No Pembahasan</th>
                <th>Topik & Komitmen</th>
                <th style="width: 100px;">PIC</th>
                <th style="width: 80px;">Deadline</th>
                <th style="width: 60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($discussions as $index => $discussion)
                <tr>
                    <td rowspan="{{ $discussion->commitments->count() > 0 ? $discussion->commitments->count() + 1 : 1 }}" style="text-align: center; vertical-align: top;">{{ $index + 1 }}</td>
                    <td rowspan="{{ $discussion->commitments->count() > 0 ? $discussion->commitments->count() + 1 : 1 }}" style="vertical-align: top;">{{ $discussion->unit_name ?? $discussion->unit }}</td>
                    <td rowspan="{{ $discussion->commitments->count() > 0 ? $discussion->commitments->count() + 1 : 1 }}" style="vertical-align: top;">{{ $discussion->no_pembahasan }}</td>
                    <td style="font-weight: bold; background-color: #fafafa;">{{ $discussion->topic }}</td>
                    <td style="background-color: #fafafa;">{{ $discussion->pic }}</td>
                    <td style="text-align: center; background-color: #fafafa;">{{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center; background-color: #fafafa;">{{ $discussion->status }}</td>
                </tr>
                @foreach($discussion->commitments as $commitment)
                    <tr>
                        <td style="padding-left: 20px;">
                            • {{ $commitment->description }}
                        </td>
                        <td>{{ $commitment->pic }}</td>
                        <td style="text-align: center;">{{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: center;">{{ $commitment->status }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data pembahasan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="header">
        <h1 style="margin: 0; font-size: 18px;">LAMPIRAN: DATA ABSENSI WEEKLY</h1>
        <p style="margin: 5px 0;">Periode: {{ $filters['start_date'] ?? 'Semua' }} s/d {{ $filters['end_date'] ?? 'Semua' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama</th>
                <th>Jabatan / Divisi</th>
                <th>Unit Source</th>
                <th>Waktu Absen</th>
                <th style="width: 150px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $idx => $attendance)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $attendance->name }}</td>
                    <td>{{ $attendance->position }} <br> <small>{{ $attendance->division }}</small></td>
                    <td>{{ $attendance->unit_name ?? $attendance->unit_source }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($attendance->time)->format('d/m/Y H:i') }}</td>
                    <td style="text-align: center;">
                        @if($attendance->signature)
                            <img src="{{ $attendance->signature }}" style="max-height: 40px; max-width: 120px;">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data absensi untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #009BB9; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            <i class="fas fa-print"></i> CETAK LAPORAN
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                // window.print();
            }, 1000);
        });
    </script>
</body>
</html>
