<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembahasan Lain-lain</title>
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
        }
        .footer {
            margin-top: 30px;
            text-align: right;
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
        <h2>Laporan Pembahasan Lain-lain</h2>
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
                    <td colspan="9" class="text-center">Tidak ada data yang tersedia</td>
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
            // Tunggu sebentar untuk memastikan semua gambar dan style dimuat
            setTimeout(function() {
                // Sembunyikan tombol print sebelum print dialog muncul
                document.querySelector('.no-print').style.display = 'none';
                
                // Trigger print dialog
                window.print();
            }, 500); // Kurangi delay menjadi 500ms untuk respon lebih cepat
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