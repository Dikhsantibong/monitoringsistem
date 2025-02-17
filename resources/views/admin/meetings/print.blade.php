<!DOCTYPE html>
<html>
<head>
   
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
            padding: 12px 14px;
            text-align: left;
            border: 1px solid #0A749B;
        }
        td.keterangan {
            font-size: 14px;
        }
        .total-row td {
            padding: 14px;
            font-size: 15px;
            font-weight: bold;
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
                page-break-inside: avoid;
                margin-bottom: 0;
            }
        }

        /* Style untuk halaman kedua */
        .page-break {
            page-break-before: always;
        }
        .report-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th {
            background-color: #0A749B;
            color: white;
            font-size: 12px;
            padding: 10px;
            border: 1px solid #0A749B;
            text-align: left;
        }
        .report-table td {
            font-size: 10px;
            padding: 6px;
            border: 1px solid #000;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
        }
        .status-operasi {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-gangguan {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-standby {
            background-color: #fef9c3;
            color: #854d0e;
        }
        .sr-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .sr-table th {
            background-color: #0A749B;
            color: white;
            font-size: 12px;
            padding: 10px;
            border: 1px solid #0A749B;
            text-align: left;
        }
        .sr-table td {
            font-size: 11px;
            padding: 8px;
            border: 1px solid #000;
            vertical-align: middle;
        }
        .priority-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
        }
        .priority-high {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .priority-medium {
            background-color: #fef9c3;
            color: #854d0e;
        }
        .priority-low {
            background-color: #dcfce7;
            color: #166534;
        }
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px; /* Ukuran font lebih kecil */
        }
        .notes-table th, .notes-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
        .notes-table th {
            background-color: #f0f0f0;
            font-weight: bold;
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
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .stat-item {
            padding: 0.5rem;
            background-color: #f3f4f6;
            border-radius: 0.375rem;
            text-align: center;
        }
        .stat-item span {
            display: block;
            font-size: 0.875rem;
            color: #4b5563;
        }
        .stat-item strong {
            display: block;
            font-size: 1.125rem;
            color: #1f2937;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .report-table th {
            background-color: #0A749B;
            color: white;
            padding: 0.75rem;
            font-size: 0.875rem;
            text-align: center;
            border: 1px solid #0A749B;
        }
        .report-table td {
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-operasi { background-color: #dcfce7; color: #166534; }
        .status-standby { background-color: #dbeafe; color: #1e40af; }
        .status-gangguan { background-color: #fee2e2; color: #991b1b; }
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
        .priority-high { background-color: #fee2e2; color: #991b1b; }
        .priority-medium { background-color: #fef3c7; color: #92400e; }
        .priority-low { background-color: #dcfce7; color: #166534; }
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

        /* Update style untuk thead */
        th { 
            background-color: #0A749B;
            color: white;
            font-size: 15px;
            font-weight: bold;
            padding: 12px 14px;
            text-align: left;
            border: 1px solid #0A749B;
        }

        /* Style untuk tabel spesifik */
        .report-table th,
        .sr-table th,
        .wo-table th,
        .attendance-table th { 
            background-color: #0A749B;
            color: white;
            font-size: 12px;
            padding: 10px;
            border: 1px solid #0A749B;
            text-align: left;
        }

        /* Pastikan border tetap terlihat */
        .report-table td,
        .sr-table td,
        .wo-table td,
        .attendance-table td {
            border: 1px solid #000;
        }

        /* Style untuk status badge */
        .status-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
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
                        'mysql_wua_wua' => 'Wua-Wua',  // Sesuaikan dengan format nama yang dikirim dari controller
                        'mysql_bau_bau' => 'Bau-Bau',  // Sesuaikan dengan format nama yang dikirim dari controller
                        'mysql_poasia' => 'Poasia',    // Sesuaikan dengan format nama yang dikirim dari controller
                        'mysql_kolaka' => 'Kolaka'     // Sesuaikan dengan format nama yang dikirim dari controller
                    ];
                    
                    // Debug information
                    \Log::info('Print View Debug:', [
                        'currentSession' => $currentSession,
                        'unitName' => $unitName,
                        'mappedUnit' => $unitMapping[$currentSession] ?? null
                    ]);
                    
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

                        <!-- Baris Ketentuan dengan spacing yang lebih besar -->
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
        <div class="error">
            Tidak ada data score card yang tersedia untuk tanggal ini
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
                    <th style="width: 5%">No</th>
                    <th style="width: 25%">Nama</th>
                    <th style="width: 20%">Jabatan</th>
                    <th style="width: 20%">Divisi</th>
                    <th style="width: 15%">Waktu</th>
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
                        $plantLogs = $logs->filter(function($log) use ($powerPlant) {
                            return $log->machine->power_plant_id === $powerPlant->id;
                        });

                        $totalDMP = $plantLogs->sum('dmp');
                        $totalDMN = $plantLogs->sum('dmn');
                        $totalBeban = $plantLogs->where('status', 'Operasi')->sum('load_value');
                    @endphp
                    
                    <div class="stat-item">
                        <span>DMP Total:</span>
                        <strong>{{ number_format($totalDMP, 1) }} MW</strong>
                    </div>
                    <div class="stat-item">
                        <span>DMN Total:</span>
                        <strong>{{ number_format($totalDMN, 1) }} MW</strong>
                    </div>
                    <div class="stat-item">
                        <span>Total Beban:</span>
                        <strong>{{ number_format($totalBeban, 1) }} MW</strong>
                    </div>
                </div>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mesin</th>
                            <th>DMN</th>
                            <th>DMP</th>
                            <th>Beban</th>
                            <th>Status</th>
                            <th>Component</th>
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
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $machine->name }}</td>
                                <td>{{ $machineLog?->dmn ?? '-' }}</td>
                                <td>{{ $machineLog?->dmp ?? '-' }}</td>
                                <td>{{ $machineLog?->load_value ?? '-' }}</td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>{{ $machineLog?->component ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <!-- Halaman keempat - Service Request Table -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>DAFTAR SERVICE REQUEST</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="sr-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">ID SR</th>
                    <th style="width: 30%">Deskripsi</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 10%">Downtime</th>
                    <th style="width: 15%">Tipe SR</th>
                    <th style="width: 15%">Prioritas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($serviceRequests as $index => $sr)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $sr->id }}</td>
                        <td>{{ $sr->description }}</td>
                        <td>
                            <span class="status-badge {{ 
                                $sr->status === 'Completed' ? 'status-operasi' : 
                                ($sr->status === 'In Progress' ? 'status-standby' : 
                                'status-gangguan') 
                            }}">
                                {{ $sr->status }}
                            </span>
                        </td>
                        <td>{{ $sr->downtime }}</td>
                        <td>{{ $sr->tipe_sr }}</td>
                        <td>
                            <span class="priority-badge priority-{{ strtolower($sr->priority) }}">
                                {{ $sr->priority }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada Service Request untuk tanggal ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Halaman Work Orders -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>DAFTAR WORK ORDER</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="wo-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">No WO</th>
                    <th style="width: 30%">Deskripsi</th>
                    <th style="width: 10%">Tipe</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 15%">Prioritas</th>
                    <th style="width: 15%">Jadwal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $index => $wo)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $wo->id }}</td>
                        <td>{{ $wo->description }}</td>
                        <td>{{ $wo->type }}</td>
                        <td>
                            <span class="status-badge {{ 
                                $wo->status === 'Completed' ? 'status-operasi' : 
                                ($wo->status === 'In Progress' ? 'status-standby' : 
                                'status-gangguan') 
                            }}">
                                {{ $wo->status }}
                            </span>
                        </td>
                        <td>
                            <span class="priority-badge priority-{{ strtolower($wo->priority) }}">
                                {{ $wo->priority }}
                            </span>
                        </td>
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

    <!-- Halaman WO Backlog -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>DAFTAR WORK ORDER BACKLOG</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="wo-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">No WO</th>
                    <th style="width: 35%">Deskripsi</th>
                    <th style="width: 15%">Tanggal Backlog</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 15%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($woBacklogs as $index => $wo)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $wo->no_wo }}</td>
                        <td>{{ $wo->deskripsi }}</td>
                        <td>{{ \Carbon\Carbon::parse($wo->tanggal_backlog)->format('d/m/Y') }}</td>
                        <td>
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
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">No Pembahasan</th>
                    <th style="width: 40%">Topik</th>
                    <th style="width: 25%">PIC</th>
                    <th style="width: 15%">Status</th>
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
                        <td colspan="5" style="padding: 10px; background-color: #f9f9f9;">
                            <div style="font-size: 10px; margin-bottom: 5px;">
                                <strong>Unit:</strong> {{ $discussion->unit }} |
                                <strong>No SR:</strong> {{ $discussion->sr_number ?? '-' }} |
                                <strong>Target:</strong> {{ $discussion->target }} |
                                <strong>Deadline:</strong> {{ $discussion->deadline ? \Carbon\Carbon::parse($discussion->deadline)->format('d/m/Y') : '-' }}
                            </div>
                            @if($discussion->commitments && $discussion->commitments->count() > 0)
                                <div style="margin-top: 8px;">
                                    <strong style="font-size: 10px;">Commitments:</strong>
                                    <ul style="list-style: none; padding-left: 0; margin: 5px 0;">
                                        @foreach($discussion->commitments as $commitment)
                                            <li style="margin-bottom: 5px; font-size: 10px; padding-left: 10px;">
                                                • {{ $commitment->description }} 
                                                <div style="padding-left: 12px; color: #666;">
                                                    PIC: {{ $commitment->pic }} | 
                                                    Deadline: {{ \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') }} | 
                                                    Status: {{ $commitment->status }}
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
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
            if (!document.querySelector('.error')) {
                window.print();
            }
        }
    </script>
</body>
</html>
