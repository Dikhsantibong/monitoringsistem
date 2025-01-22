<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Score Card Daily</title>
    <style>
        @page {
            size: A4;
            margin: 2.2cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 25px;
            font-size: 15px;
        }
        .logo {
            position: absolute;
            top: 25px;
            left: 25px;
            width: 240px;
            height: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-top: 80px;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .header p {
            margin: 6px 0;
            font-size: 15px;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #000;
            padding: 12px 14px;
            text-align: left;
            font-size: 14px;
            line-height: 1.5;
        }
        th {
            background-color: #0A749B;
            color: white;
            font-size: 15px;
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
        .report-table th { font-size: 11px; }
        .report-table td { font-size: 10px; }
        .status-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            padding: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 50px 0 10px;
        }
        .sr-table th, .sr-table td {
            font-size: 12px;
            padding: 8px;
        }
        .status-operasi { background-color: #28a745; color: white; }
        .status-standby { background-color: #ffc107; color: black; }
        .status-maintenance { background-color: #dc3545; color: white; }
        .status-emergency { background-color: #dc3545; color: white; }
        .status-shutdown { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <!-- Halaman pertama - Score Card -->
    <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
    
    @if(isset($data) && isset($date))
        <div class="header">
            <h2>SCORE CARD DAILY</h2>
            <p>Tanggal: {{ is_string($date) ? \Carbon\Carbon::parse($date)->format('d F Y') : $date->format('d F Y') }}</p>
            <p>Lokasi: {{ $data['lokasi'] }}</p>
            <p>Waktu: {{ \Carbon\Carbon::parse($data['waktu_mulai'])->format('H:i') }} - 
                     {{ \Carbon\Carbon::parse($data['waktu_selesai'])->format('H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>   
                    <th style="width: 27%">Peserta</th>
                    <th style="width: 10%">Awal</th>
                    <th style="width: 10%">Akhir</th>
                    <th style="width: 13%">Skor</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['peserta'] as $index => $peserta)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $peserta['jabatan'] }}</td>
                        <td style="text-align: center;">{{ $peserta['skor'] == 50 ? 0 : '' }}</td>
                        <td style="text-align: center;">{{ $peserta['skor'] == 100 ? 1 : '' }}</td>
                        <td style="text-align: center;">{{ $peserta['skor'] }}</td>
                        <td class="keterangan">{{ $peserta['keterangan'] }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 1 }}</td>
                    <td>Ketepatan waktu mulai</td>
                    <td style="text-align: center;">Start</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($data['waktu_mulai'])->format('H:i') }}</td>
                    <td style="text-align: center;">{{ $data['skor_waktu_mulai'] }}</td>
                    <td class="keterangan">-10 per 3 menit keterlambatan</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 2 }}</td>
                    <td>Kesiapan Panitia</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['kesiapan_panitia'] }}</td>
                    <td class="keterangan">-20 per peralatan tidak siap</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 3 }}</td>
                    <td>Kesiapan Bahan</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['kesiapan_bahan'] }}</td>
                    <td class="keterangan">-20 per bahan tidak siap</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 4 }}</td>
                    <td>Aktivitas Luar</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['aktivitas_luar'] }}</td>
                    <td class="keterangan">-10 per gangguan</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 5 }}</td>
                    <td>Gangguan Diskusi</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['gangguan_diskusi'] }}</td>
                    <td class="keterangan">-10 per gangguan</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 6 }}</td>
                    <td>Gangguan Keluar Masuk</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['gangguan_keluar_masuk'] }}</td>
                    <td class="keterangan">-10 per gangguan</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 7 }}</td>
                    <td>Gangguan Interupsi</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['gangguan_interupsi'] }}</td>
                    <td class="keterangan">-10 per gangguan</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 8 }}</td>
                    <td>Ketegasan Moderator</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['ketegasan_moderator'] }}</td>
                    <td class="keterangan">Obyektif</td>
                </tr>

                <tr>
                    <td style="text-align: center;">{{ count($data['peserta']) + 9 }}</td>
                    <td>Kelengkapan SR</td>
                    <td style="text-align: center;" colspan="2"></td>
                    <td style="text-align: center;">{{ $data['kelengkapan_sr'] }}</td>
                    <td class="keterangan">Kaidah, Dokumentasi, CMMS</td>
                </tr>

                @php
                    $totalScorePeserta = collect($data['peserta'])->sum('skor');
                    $totalScoreKetentuan = 
                        $data['kesiapan_panitia'] +
                        $data['kesiapan_bahan'] +
                        $data['aktivitas_luar'] +
                        $data['gangguan_diskusi'] +
                        $data['gangguan_keluar_masuk'] +
                        $data['gangguan_interupsi'] +
                        $data['ketegasan_moderator'] +
                        $data['kelengkapan_sr'] +
                        $data['skor_waktu_mulai'];
                    $grandTotal = $totalScorePeserta + $totalScoreKetentuan;
                @endphp
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Score:</td>
                    <td style="text-align: center;">{{ $grandTotal }}</td>
                    <td>Total score peserta dan ketentuan</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="error">
            Data tidak lengkap untuk generate PDF
        </div>
    @endif

    <!-- Halaman Daftar Hadir -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>DAFTAR HADIR RAPAT</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 25%">Nama</th>
                    <th style="width: 20%">Jabatan</th>
                    <th style="width: 20%">Divisi</th>
                    <th style="width: 15%">Waktu Hadir</th>
                    <th style="width: 15%">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $index => $attendance)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $attendance->name }}</td>
                        <td>{{ $attendance->position }}</td>
                        <td>{{ $attendance->division }}</td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($attendance->created_at)->format('H:i') }}
                        </td>
                        <td style="text-align: center;">
                            @if($attendance->signature)
                                <img src="{{ $attendance->signature }}" alt="Tanda Tangan" style="max-height: 30px;">
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data kehadiran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Halaman Status Pembangkit -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>LAPORAN STATUS PEMBANGKIT</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Unit</th>
                    <th style="width: 10%">Waktu</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 15%">Daya (MW)</th>
                    <th style="width: 15%">MVAR</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $currentUnit = ''; @endphp
                @forelse($machineStatuses as $index => $log)
                    @if($currentUnit != $log->machine->unit)
                        @php $currentUnit = $log->machine->unit; @endphp
                        <tr>
                            <td colspan="7" style="background-color: #f8f9fa; font-weight: bold;">
                                {{ $log->machine->unit }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $log->machine->name }}</td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ strtolower($log->status) }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($log->power_output, 2) }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($log->mvar, 2) }}
                        </td>
                        <td>{{ $log->notes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Tidak ada data status pembangkit
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Ringkasan Status -->
        <div style="margin-top: 20px;">
            <table class="report-table">
                <thead>
                    <tr>
                        <th colspan="2">Ringkasan Status Pembangkit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 30%">Total Unit Beroperasi</td>
                        <td>
                            {{ $machineStatuses->where('status', 'OPERASI')->count() }} Unit
                        </td>
                    </tr>
                    <tr>
                        <td>Total Daya Terbangkit</td>
                        <td>
                            {{ number_format($machineStatuses->sum('power_output'), 2) }} MW
                        </td>
                    </tr>
                    <tr>
                        <td>Total MVAR</td>
                        <td>
                            {{ number_format($machineStatuses->sum('mvar'), 2) }} MVAR
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Halaman Service Request -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        <div class="header">
            <h2>DAFTAR SERVICE REQUEST</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        <table class="sr-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Nomor SR</th>
                    <th style="width: 20%">Unit</th>
                    <th style="width: 30%">Deskripsi</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 15%">Prioritas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($serviceRequests as $index => $sr)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $sr->number }}</td>
                        <td>{{ $sr->unit }}</td>
                        <td>{{ $sr->description }}</td>
                        <td style="text-align: center;">{{ $sr->status }}</td>
                        <td style="text-align: center;">{{ $sr->priority }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data service request</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Halaman WO Backlog -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        <div class="header">
            <h2>DAFTAR WORK ORDER BACKLOG</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Nomor WO</th>
                    <th style="width: 20%">Unit</th>
                    <th style="width: 25%">Deskripsi</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 20%">Target Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $index => $wo)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $wo->number }}</td>
                        <td>{{ $wo->unit }}</td>
                        <td>{{ $wo->description }}</td>
                        <td style="text-align: center;">{{ $wo->status }}</td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($wo->target_date)->format('d/m/Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data work order</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Halaman Notes -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        <div class="header">
            <h2>CATATAN PEMBAHASAN LAIN-LAIN</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        <div style="margin-top: 30px;">
            @if(isset($notes) && !empty($notes))
                {!! nl2br(e($notes)) !!}
            @else
                <p style="text-align: center;">Tidak ada catatan tambahan</p>
            @endif
        </div>
    </div>

    <!-- Halaman Pengesahan -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        <div class="header">
            <h2>LEMBAR PENGESAHAN</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        <div class="signatures-grid">
            <div class="signature-box">
                <p>Moderator</p>
                <div class="signature-line"></div>
                <p>{{ $moderator->name ?? '________________' }}</p>
                <p>{{ $moderator->position ?? '________________' }}</p>
            </div>
            <div class="signature-box">
                <p>Notulis</p>
                <div class="signature-line"></div>
                <p>{{ $notulis->name ?? '________________' }}</p>
                <p>{{ $notulis->position ?? '________________' }}</p>
            </div>
        </div>
    </div>
</body>
</html>