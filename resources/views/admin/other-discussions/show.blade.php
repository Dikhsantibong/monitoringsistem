<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan Pembahasan Lain-lain</title>
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
        .info-table th {
            width: 25%;
        }
        th {
            background-color: #f4f4f4;
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
        .page-break {
            page-break-after: always;
        }
        .center-text {
            text-align: center;
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

    @forelse($discussions as $discussion)
        <div class="header">
            <img src="{{ asset('logo/navlog1.png') }}" alt="Logo" class="logo">
            <h2>PT PLN NP UP KENDARI</h2>
            <h3>Detail Pembahasan Mingguan</h3>
        </div>

        <!-- Main Discussion Details -->
        <div class="section-title">Informasi Pembahasan</div>
        <table class="info-table">
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
                    <td>{{ $discussion->unit_name ?? $discussion->unit }}</td>
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
                @if(isset($discussion->commitments) && count($discussion->commitments) > 0)
                    @foreach($discussion->commitments as $index => $commitment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $commitment->description }}</td>
                            <td>{{ $commitment->pic }}</td>
                            <td>{{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $commitment->status }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center">Tidak ada komitmen yang tercatat</td>
                    </tr>
                @endif
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
                        <td style="width: 5%">{{ $index + 1 }}</td>
                        <td>{{ $descriptions[$index] ?? basename($path) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="page-break"></div>
    @empty
        <div class="header">
            <img src="{{ asset('logo/navlog1.png') }}" alt="Logo" class="logo">
            <h2>PT PLN NP UP KENDARI</h2>
            <h3>Laporan Mingguan Pembahasan Lain-lain</h3>
            <p>Periode: {{ $filters['start_date'] ?? 'Semua' }} - {{ $filters['end_date'] ?? 'Semua' }}</p>
        </div>
        <p style="text-align: center;">Tidak ada data pembahasan yang tersedia.</p>
        <div class="page-break"></div>
    @endforelse

    <div class="header">
        <img src="{{ asset('logo/navlog1.png') }}" alt="Logo" class="logo">
        <h2>PT PLN NP UP KENDARI</h2>
        <h3>Lampiran: Data Absensi Weekly</h3>
        <p>Periode: {{ $filters['start_date'] ?? 'Semua' }} - {{ $filters['end_date'] ?? 'Semua' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">No</th>
                <th style="text-align: center;">Nama</th>
                <th style="text-align: center;">Jabatan / Divisi</th>
                <th style="text-align: center;">Unit Source</th>
                <th style="text-align: center;">Waktu Absen</th>
                <th style="width: 150px; text-align: center;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $idx => $attendance)
                <tr>
                    <td class="center-text">{{ $idx + 1 }}</td>
                    <td>{{ $attendance->name }}</td>
                    <td>{{ $attendance->position }} <br> <small>{{ $attendance->division }}</small></td>
                    <td class="center-text">{{ $attendance->unit_name ?? $attendance->unit_source }}</td>
                    <td class="center-text">{{ \Carbon\Carbon::parse($attendance->time)->format('d/m/Y H:i') }}</td>
                    <td class="center-text">
                        @if($attendance->signature)
                            <img src="{{ $attendance->signature }}" style="max-height: 40px; max-width: 120px;">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center-text">Tidak ada data absensi untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Tombol Print (hanya muncul di layar) -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">
            Print Dokumen
        </button>
    </div>
</body>
</html>