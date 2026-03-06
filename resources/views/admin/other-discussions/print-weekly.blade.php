<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan Pembahasan Lain-lain</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
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
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .page-break {
            page-break-before: always;
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
        <h2>Laporan Mingguan Pembahasan Lain-lain</h2>
        <p>Periode: {{ $filters['start_date'] ?? 'Semua' }} - {{ $filters['end_date'] ?? 'Semua' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No SR</th>
                <th>No Pembahasan</th>
                <th>Unit</th>
                <th>Topik</th>
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
                    <td>{{ $discussion->unit_name ?? $discussion->unit }}</td>
                    <td>{{ $discussion->topic }}</td>
                    <td>{{ $discussion->target }}</td>
                    <td>{{ $discussion->pic }}</td>
                    <td>{{ $discussion->status }}</td>
                    <td>{{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}</td>
                </tr>
                @if(isset($discussion->commitments) && count($discussion->commitments) > 0)
                    <tr>
                        <td colspan="9">
                            <strong>Commitments:</strong>
                            <ul>
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
                    <td colspan="9" style="text-align: center;">Tidak ada data pembahasan yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <h2>Lampiran: Data Absensi Weekly</h2>
        <p>Periode: {{ $filters['start_date'] ?? 'Semua' }} - {{ $filters['end_date'] ?? 'Semua' }}</p>
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

    <!-- Tombol Print (hanya muncul di layar) -->
    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print
        </button>
    </div>

    <script>
        // Jalankan print segera setelah DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                // Sembunyikan tombol print sebelum print dialog muncul
                document.querySelector('.no-print').style.display = 'none';
                
                // Trigger print dialog
                window.print();
            }, 500);
        });

        // Tutup tab setelah print selesai atau dibatalkan
        window.onafterprint = function() {
            window.close();
        };

        // Fallback jika print dibatalkan
        window.onbeforeunload = function() {
            window.close();
        };
    </script>
</body>
</html>
