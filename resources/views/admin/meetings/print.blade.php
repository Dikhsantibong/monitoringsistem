<!DOCTYPE html>
<html>
<head>
    <title>Laporan Daily {{ session('unit') === 'mysql' ? 'UP Kendari' : str_replace(['mysql_', '_'], ['', ' '], ucwords(session('unit'))) }} {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') }}</title>
   
    <style>
        @page {
            size: A4;
            margin: 2.2cm;
        }
        body { 
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            font-size: 15px;
        }
        .logo {
            position: absolute;
            top: 20px;
            left: 25px;
            width: 200px;
            height: auto;   
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-top: 60px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .header p {
            margin: 4px 0;
            font-size: 14px;
            text-align: left;
        }

        /* Content section styles */
        .content-section {
            margin-bottom: 30px;
            break-inside: avoid;
        }

        /* Table container styles */
        .table-container {
            margin: 20px 0;
            break-inside: auto;
        }

        table { 
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            page-break-inside: auto;
        }

        tr { 
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        th, td { 
            border: 1px solid #e5e7eb;
            padding: 12px 14px;
            text-align: left;
            font-size: 14px;
            line-height: 1.5;
            color: #374151;
        }
        th { 
            background: linear-gradient(180deg, #0A749B 0%, #086384 100%);
            color: white;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px;
            text-align: left;
            border: none;
            white-space: nowrap;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #f3f4f6;
        }
        td.keterangan {
            font-size: 13px;
            color: #6b7280;
        }
        .total-row td {
            background-color: #f8fafc;
            font-weight: 600;
            border-top: 2px solid #e5e7eb;
            padding: 16px 14px;
        }
        @media print {
            .error {
                display: none;
            }
            html, body {
                width: 210mm;
                height: 297mm;
            }
            table { 
                page-break-inside: auto;
                margin-bottom: 0;
                box-shadow: none;
            }
            tr {
                page-break-inside: avoid;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            
            .report-table, .sr-table, .wo-table {
                page-break-inside: auto;
            }
            
            .report-table tr, .sr-table tr, .wo-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .unit-section {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
            
            /* Only create new page when content exceeds page height */
            .page-break {
                page-break-before: auto;
            }
            
            /* Force page break only when needed */
            .force-break {
                page-break-before: always;
            }
            th {
                background: #0A749B !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .status-badge,
            .priority-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .stat-item {
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }
            .scorecard-table-first-page {
                page-break-inside: avoid;
                margin-bottom: 0;
            }
            
            /* Ensure content fits on first page */
            .first-page-content {
                max-height: calc(297mm - 4.4cm);
                overflow: visible;
            }

            /* Content wrapper for page height control */
            .content-wrapper {
                max-height: calc(297mm - 40mm);
                overflow: hidden;
            }

            .content-section {
                break-inside: avoid;
                margin-bottom: 20px;
            }

            .table-container {
                break-inside: auto;
            }

            /* Remove forced page breaks */
            .page-break {
                page-break-before: auto;
            }

            /* Only break page when content exceeds height */
            .content-section:not(:first-child) {
                page-break-before: auto;
            }
        }

        /* Style untuk halaman kedua */
        .page-break {
            page-break-before: always;
            clear: both;
        }
        .report-table {
            margin: 20px 0;
        }
        .report-table th {
            font-size: 13px;
            padding: 12px;
            text-align: center;
        }
        .report-table td {
            font-size: 13px;
            padding: 10px;
            vertical-align: middle;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            text-align: center;
            min-width: 80px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .status-operasi { 
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .status-gangguan { 
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .status-standby { 
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
        .sr-table {
            margin: 20px 0;
        }
        .sr-table th {
            font-size: 13px;
            padding: 12px;
        }
        .sr-table td {
            font-size: 13px;
            padding: 10px;
            vertical-align: middle;
        }
        .priority-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            text-align: center;
            min-width: 80px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .priority-high {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .priority-medium {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
        .priority-low {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .notes-table {
            margin: 20px 0;
        }
        .notes-table th {
            background-color: #f8fafc;
            color: #1f2937;
            font-weight: 600;
            font-size: 13px;
        }
        .notes-table td {
            font-size: 13px;
            padding: 10px;
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr;
            margin-top: 40px;
            padding: 20px;
        }
        .signature-box {
            text-align: center;
            padding: 20px;
        }
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .signature-image {
            max-width: 200px;
            max-height: 100px;
            margin: 0 auto;
        }
        .signature-line {
            margin-top: 10px;
            border-top: 1px solid black;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        .unit-section {
            margin-bottom: 2rem;
            page-break-inside: avoid;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 20px 0;
        }
        .stat-item {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stat-item span {
            color: #6b7280;
            font-size: 13px;
            display: block;
            margin-bottom: 4px;
        }
        .stat-item strong {
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
            display: block;
        }
        .attendance-table th {
            font-size: 13px;
            padding: 12px;
            text-align: center;
        }
        .attendance-table td {
            font-size: 13px;
            padding: 10px;
            vertical-align: middle;
        }
        .wo-table th {
            font-size: 13px;
            padding: 12px;
            text-align: center;
        }
        .wo-table td {
            font-size: 13px;
            padding: 10px;
            vertical-align: middle;
        }
        .status-pemeliharaan { background-color: #ffedd5; color: #9a3412; }
        .status-overhaul { background-color: #ede9fe; color: #5b21b6; }
        .status-default { background-color: #f3f4f6; color: #374151; }
        .deadline-info {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .commitment-item {
            margin-bottom: 5px;
            padding: 3px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .commitment-desc {
            margin-bottom: 2px;
        }
        .pic-item {
            margin-bottom: 5px;
        }
        .risk-badge, .priority-badge, .status-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .risk-t { background-color: #fee2e2; color: #991b1b; }
        .risk-mt { background-color: #fef3c7; color: #92400e; }
        .risk-mr { background-color: #f3f4f6; color: #374151; }
        .risk-r { background-color: #dcfce7; color: #166534; }
        .status-open { background-color: #fee2e2; color: #991b1b; }
        .status-closed { background-color: #dcfce7; color: #166534; }
        .overdue-row {
            background-color: #fff1f1;
        }
        
        .overdue {
            color: #dc2626;
        }
        
        .closed-info {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
        
        .document-badge {
            background-color: #0A749B;
            color: white;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
        }

        /* Specific styles for first page scorecard table */
        .scorecard-table-first-page {
            margin-top: 15px;
            font-size: 13px;
        }

        .scorecard-table-first-page th {
            padding: 8px 10px;
            font-size: 13px;
            white-space: normal;
        }

        .scorecard-table-first-page td {
            padding: 6px 8px;
            font-size: 13px;
            line-height: 1.3;
        }

        .scorecard-table-first-page .keterangan {
            font-size: 12px;
        }

        .scorecard-table-first-page .total-row td {
            padding: 8px 10px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <!-- Untuk setiap unit -->
    @if(!empty($allScoreCards))
        @foreach($allScoreCards as $unitName => $data)
            @php
                $currentSession = session('unit');
                $shouldDisplay = false;
                
                // Jika login sebagai mysql (admin), tampilkan semua unit
                if ($currentSession === 'mysql') {
                    $shouldDisplay = true;
                } else {
                    // Untuk unit lain, hanya tampilkan data unit mereka sendiri
                    $unitMapping = [
                        'mysql_wua_wua' => 'Wua-Wua',
                        'mysql_bau_bau' => 'Bau-Bau',
                        'mysql_poasia' => 'Poasia',
                        'mysql_kolaka' => 'Kolaka'
                    ];
                    
                    $shouldDisplay = ($currentSession && isset($unitMapping[$currentSession]) && $unitName === $unitMapping[$currentSession]);
                }
            @endphp

            @if($shouldDisplay)
                @if($loop->iteration > 1)
                    <div class="page-break"></div>
                @endif
                
                <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">

                <div class="header">
                    <h2>SCORE CARD DAILY - {{ $unitName }}</h2>
                    <p>Tanggal: {{ is_string($date) ? \Carbon\Carbon::parse($date)->format('d F Y') : $date->format('d F Y') }}</p>
                    <p>Lokasi: {{ $data['lokasi'] }}</p>
                    <p>Waktu: {{ \Carbon\Carbon::parse($data['waktu_mulai'])->format('H:i') }} - 
                             {{ \Carbon\Carbon::parse($data['waktu_selesai'])->format('H:i') }}</p>
                </div>

                <table class="scorecard-table-first-page">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">No</th>   
                            <th style="width: 27%; text-align: center;">Peserta</th>
                            <th style="width: 10%; text-align: center;">Awal</th>
                            <th style="width: 10%; text-align: center;">Akhir</th>
                            <th style="width: 13%; text-align: center;">Skor</th>
                            <th style="text-align: center;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['peserta'] as $jabatan => $pesertaData)
                            <tr>
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                <td>{{ is_array($pesertaData) ? $jabatan : $pesertaData['jabatan'] }}</td>
                                <td style="text-align: center;">{{ is_array($pesertaData) ? ($pesertaData['skor'] == 50 ? '0' : '') : ($pesertaData['awal'] ?? '') }}</td>
                                <td style="text-align: center;">{{ is_array($pesertaData) ? ($pesertaData['skor'] == 100 ? '1' : '') : ($pesertaData['akhir'] ?? '') }}</td>
                                <td style="text-align: center;">{{ is_array($pesertaData) ? $pesertaData['skor'] : ($pesertaData['skor'] ?? 0) }}</td>
                                <td class="keterangan">{{ is_array($pesertaData) ? ($pesertaData['keterangan'] ?? '-') : '-' }}</td>
                            </tr>
                        @endforeach

                        <!-- Ketentuan rows -->
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
                            <td class="keterangan">-20 per aktivitas luar</td>
                        </tr>

                        <tr>
                            <td style="text-align: center;">{{ count($data['peserta']) + 5 }}</td>
                            <td>Gangguan Diskusi</td>
                            <td style="text-align: center;" colspan="2"></td>
                            <td style="text-align: center;">{{ $data['gangguan_diskusi'] }}</td>
                            <td class="keterangan">-20 per gangguan diskusi kecil</td>
                        </tr>

                        <tr>
                            <td style="text-align: center;">{{ count($data['peserta']) + 6 }}</td>
                            <td>Gangguan Keluar Masuk</td>
                            <td style="text-align: center;" colspan="2"></td>
                            <td style="text-align: center;">{{ $data['gangguan_keluar_masuk'] }}</td>
                            <td class="keterangan">-20 per peserta keluar masuk</td>
                        </tr>

                        <tr>
                            <td style="text-align: center;">{{ count($data['peserta']) + 7 }}</td>
                            <td>Gangguan Interupsi</td>
                            <td style="text-align: center;" colspan="2"></td>
                            <td style="text-align: center;">{{ $data['gangguan_interupsi'] }}</td>
                            <td class="keterangan">-20 per interupsi dari luar</td>
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

                        <!-- Total Score dengan style khusus -->
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
                            
                            // Hitung persentase
                            $maxPossibleScore = (count($data['peserta']) * 100) + (900); // 900 adalah total maksimum untuk ketentuan
                            $percentageScore = ($grandTotal / $maxPossibleScore) * 100;
                        @endphp
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Total Score:</td>
                            <td style="text-align: center;">{{ number_format($percentageScore, 2) }}%</td>
                            <td>Total score peserta dan ketentuan</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        @endforeach
    @else
        <div class="content-section">
            <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
            <div class="header">
                <h2>SCORE CARD DAILY</h2>
                <p>Tanggal: {{ is_string($date) ? \Carbon\Carbon::parse($date)->format('d F Y') : $date->format('d F Y') }}</p>
            </div>
            <div class="alert-message" style="text-align: center; padding: 20px; margin: 20px 0; background-color: #f3f4f6; border-radius: 8px;">
                <p style="color: #6b7280; font-size: 16px;">Tidak ada data score card yang tersedia untuk tanggal ini</p>
            </div>
        </div>
    @endif

    <!-- Halaman kedua - Daftar Hadir (dipindah ke sini) -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>DAFTAR HADIR RAPAT</h2>
            <p>Tanggal: {{ is_string($date) ? \Carbon\Carbon::parse($date)->format('d F Y') : $date->format('d F Y') }}</p>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 25%; text-align: center;">Nama</th>
                    <th style="width: 20%; text-align: center;">Jabatan</th>
                    <th style="width: 20%; text-align: center;">Divisi</th>
                    <th style="width: 15%; text-align: center;">Waktu</th>
                    <th style="width: 15%; text-align: center;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $index => $attendance)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $attendance->name }}</td>
                        <td>{{ $attendance->position }}</td>
                        <td>{{ $attendance->division }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($attendance->time)->format('H:i') }}</td>
                        <td>
                            @if($attendance->signature)
                                <img src="{{ $attendance->signature }}" alt="Tanda tangan" style="max-height: 40px;">
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

    <!-- Halaman ketiga - Report Table -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>LAPORAN STATUS PEMBANGKIT</h2>
            <p>Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '-' }} 
               s/d {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '-' }}</p>
        </div>

        @foreach($powerPlants as $powerPlant)
            <div class="unit-section mb-4">
                <h3 class="text-lg font-semibold uppercase mb-2">STATUS MESIN - {{ $powerPlant->name }}</h3>
                
                <!-- Statistik Unit -->
                <div class="stats-grid">
                    @php
                        $totalDMP = 0;
                        $totalDMN = 0;
                        $totalBeban = 0;

                        // Hitung total dari data yang ditampilkan di tabel
                        foreach($powerPlant->machines as $machine) {
                            $machineLog = $logs->where('machine_id', $machine->id)->first();
                            if ($machineLog) {
                                $totalDMP += is_numeric($machineLog->dmp) ? (float) $machineLog->dmp : 0;
                                $totalDMN += is_numeric($machineLog->dmn) ? (float) $machineLog->dmn : 0;
                                if ($machineLog->status === 'Operasi') {
                                    $totalBeban += is_numeric($machineLog->load_value) ? (float) $machineLog->load_value : 0;
                                }
                            }
                        }
                    @endphp
                    
                    <div class="stat-item">
                        <span>DMN Total:</span>
                        <strong>{{ number_format($totalDMN, 1) }} MW</strong>
                    </div>
                    <div class="stat-item">
                        <span>DMP Total:</span>
                        <strong>{{ number_format($totalDMP, 1) }} MW</strong>
                    </div>
                    
                    <div class="stat-item">
                        <span>Total Beban:</span>
                        <strong>{{ number_format($totalBeban, 1) }} MW</strong>
                    </div>
                </div>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">No</th>
                            <th style="text-align: center;">Mesin</th>
                            <th style="text-align: center;">DMN</th>
                            <th style="text-align: center;">DMP</th>
                            <th style="text-align: center;">Beban</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Component</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($powerPlant->machines as $index => $machine)
                            @php
                                $machineLog = $logs->where('machine_id', $machine->id)->first();
                                $status = $machineLog?->status ?? '-';
                                
                                // Tentukan status class berdasarkan status
                                $statusClass = match($status) {
                                    'Operasi' => 'status-operasi',
                                    'Standby' => 'status-standby',
                                    'Gangguan' => 'status-gangguan',
                                    'Pemeliharaan' => 'status-pemeliharaan',
                                    'Overhaul' => 'status-overhaul',
                                    default => 'status-default'
                                };
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $machine->name }}</td>
                                <td style="text-align: center;">{{ $machineLog?->dmn ?? '-' }}</td>
                                <td style="text-align: center;">{{ $machineLog?->dmp ?? '-' }}</td>
                                <td style="text-align: center;">{{ $machineLog?->load_value ?? '-' }}</td>
                                <td style="text-align: center;">
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td style="text-align: center;">{{ $machineLog?->component ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <!-- Service Request Table -->
    <div class="content-section">
        <div class="header">
            <h2>DAFTAR SERVICE REQUEST</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        
        <div class="table-container">
            <table class="sr-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">No SR</th>
                        <th style="width: 20%;">Unit</th> <!-- Added Unit column -->
                        <th style="width: 35%;">Deskripsi</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Type SR</th>
                        <th style="width: 15%;">Prioritas</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceRequests as $sr)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $sr->id }}</td>
                            <td>{{ $sr->power_plant ?? '-' }}</td> <!-- Displaying unit name -->
                            <td>{{ $sr->description }}</td>
                            <td class="text-center">
                                <span class="status-badge {{ 
                                    $sr->status === 'Completed' ? 'status-operasi' : 
                                    ($sr->status === 'In Progress' ? 'status-standby' : 
                                    'status-gangguan') 
                                }}">
                                    {{ $sr->status }}
                                </span>
                            </td>
                            <td class="text-center">{{ $sr->tipe_sr }}</td>
                            <td class="text-center">{{ $sr->priority }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">Tidak ada Service Request untuk tanggal ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Work Order Table -->
    <div class="content-section">
        <div class="header">
            <h2>DAFTAR WORK ORDER</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        
        <div class="table-container">
            <table class="wo-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">No WO</th>
                        <th style="width: 35%;">Deskripsi</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Prioritas</th>
                        <th style="width: 15%;">Jadwal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $wo->id }}</td>
                            <td>{{ $wo->description }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($wo->schedule_start)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($wo->schedule_finish)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="status-badge {{ 
                                    $wo->status === 'Completed' ? 'status-operasi' : 
                                    ($wo->status === 'In Progress' ? 'status-standby' : 
                                    'status-gangguan') 
                                }}">
                                    {{ $wo->status }}
                                </span>
                            </td>
                            <td>{{ $wo->priority }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($wo->schedule_start)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($wo->schedule_finish)->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">Tidak ada Work Order untuk tanggal ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- WO Backlog Table -->
    <div class="content-section">
        <div class="header">
            <h2>DAFTAR WORK ORDER BACKLOG</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        
        <div class="table-container">
            <table class="wo-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">No WO</th>
                        <th style="width: 35%;">Deskripsi</th>
                        <th style="width: 15%;">Tanggal Backlog</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($woBacklogs as $wo)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $wo->no_wo }}</td>
                            <td>{{ $wo->deskripsi }}</td>
                            <td>{{ \Carbon\Carbon::parse($wo->tanggal_backlog)->format('d/m/Y') }}</td>
                            <td >
                                <span class="status-badge {{ 
                                    $wo->status === 'Completed' ? 'status-operasi' : 
                                    ($wo->status === 'In Progress' ? 'status-standby' : 
                                    'status-gangguan') 
                                }}">
                                    {{ $wo->status }}
                                </span>
                            </td>
                            <td>{{ $wo->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada Work Order Backlog untuk tanggal ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Halaman Pembahasan Lain-lain -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>PEMBAHASAN LAIN-LAIN</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="report-table" style="margin: 0 auto; width: 95%;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 15%; text-align: center;">No Pembahasan</th>
                    <th style="width: 40%; text-align: center;">Topik</th>
                    <th style="width: 25%; text-align: center;">PIC</th>
                    <th style="width: 15%; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($otherDiscussions as $discussion)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="font-size: 10px;">{{ $discussion->no_pembahasan }}</td>
                        <td style="font-size: 10px;">{{ $discussion->topic }}</td>
                        <td style="font-size: 10px;">{{ $discussion->pic }}</td>
                        <td style="font-size: 10px; text-align: center;">
                            <span class="status-badge status-{{ strtolower($discussion->status) }}">
                                {{ $discussion->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 15px; background-color: #f9fafb; border: 1px solid #e5e7eb;">
                            <div style="font-size: 11px; margin-bottom: 8px; display: flex; gap: 15px;">
                                <div><strong>Unit:</strong> {{ $discussion->unit }}</div>
                                <div><strong>No SR:</strong> {{ $discussion->sr_number ?? '-' }}</div>  
                                <div><strong>Target:</strong> {{ $discussion->target }}</div>
                                <div><strong>Deadline:</strong> {{ $discussion->deadline ? \Carbon\Carbon::parse($discussion->deadline)->format('d/m/Y') : '-' }}</div>
                            </div>
                            
                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                <div style="margin-top: 10px;">
                                    <strong style="font-size: 11px; display: block; margin-bottom: 8px;">Commitments:</strong>
                                    <div style="display: grid; gap: 8px;">
                                        @foreach($discussion->commitments as $commitment)
                                            <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #e5e7eb;">
                                                <div style="font-size: 11px; margin-bottom: 5px;">
                                                    {{ $commitment->description }}
                                                </div>
                                                <div style="font-size: 11px; color: #6b7280; display: flex; gap: 15px;">
                                                    <span><strong>PIC:</strong> {{ $commitment->pic }}</span>
                                                    <span><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') }}</span>
                                                    <span><strong>Status:</strong> {{ $commitment->status }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada pembahasan yang masih open untuk tanggal ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Setelah halaman Notes, tambahkan halaman Pengesahan -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>LEMBAR PENGESAHAN</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <div style="margin-top: 100px; text-align: center;">
            <p style="font-weight: bold; margin-bottom: 100px;">MANAJER UP</p>
            <div style="width: 200px; border-top: 1px solid black; margin: 0 auto;"></div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Always trigger print dialog regardless of data existence
            window.print();
        }
    </script>
</body>
</html>
            