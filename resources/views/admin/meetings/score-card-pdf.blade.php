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
        .status-gangguan { background-color: #dc3545; color: white; }
        .status-pemeliharaan { background-color: #17a2b8; color: white; }
        .status-overhaul { background-color: #6c757d; color: white; }
        .status-default { background-color: #6c757d; color: white; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        /* Update and add these styles */
        .report-table th, .sr-table th, .wo-table th, .attendance-table th { 
            background-color: #0A749B;
            color: white;
            font-size: 12px;
            padding: 10px;
            border: 1px solid #0A749B;
            text-align: left;
        }

        .report-table td, .sr-table td, .wo-table td, .attendance-table td {
            border: 1px solid #000;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
        }

        .priority-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .priority-high { background-color: #fee2e2; color: #991b1b; }
        .priority-medium { background-color: #fef3c7; color: #92400e; }
        .priority-low { background-color: #dcfce7; color: #166534; }

        .status-operasi { background-color: #dcfce7; color: #166534; }
        .status-standby { background-color: #dbeafe; color: #1e40af; }
        .status-gangguan { background-color: #fee2e2; color: #991b1b; }
        .status-pemeliharaan { background-color: #ffedd5; color: #9a3412; }
        .status-overhaul { background-color: #ede9fe; color: #5b21b6; }
        .status-default { background-color: #f3f4f6; color: #374151; }

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
        .unit-section {
            margin-bottom: 2rem;
            page-break-inside: avoid;
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
    </style>
</head>
<body>
    <!-- Halaman Score Card -->
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
                    
                    // Debug information
                    \Log::info('PDF View Debug:', [
                        'currentSession' => $currentSession,
                        'unitName' => $unitName,
                        'data' => $data
                    ]);
                    
                    $shouldDisplay = ($currentSession && isset($unitMapping[$currentSession]) && $unitName === $unitMapping[$currentSession]);
                }
            @endphp

            @if($shouldDisplay || $currentSession === 'mysql')
                @if($loop->iteration > 1)
                    <div class="page-break"></div>
                @endif
                
                <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
                
                <div class="header">
                    <h2>SCORE CARD DAILY</h2>
                    <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
                    <p>Lokasi: {{ $unitName }}</p>
                    <p>Waktu: {{ \Carbon\Carbon::parse($data['waktu_mulai'])->format('H:i') }} - 
                             {{ \Carbon\Carbon::parse($data['waktu_selesai'])->format('H:i') }}</p>
                </div>

                <table class="report-table">
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
                        @foreach($data['peserta'] as $peserta)
                            <tr>
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                <td>{{ $peserta['jabatan'] }}</td>
                                <td style="text-align: center;">{{ $peserta['skor'] == 50 ? 0 : '' }}</td>
                                <td style="text-align: center;">{{ $peserta['skor'] == 100 ? 1 : '' }}</td>
                                <td style="text-align: center;">{{ $peserta['skor'] }}</td>
                                <td class="keterangan">{{ $peserta['keterangan'] }}</td>
                            </tr>
                        @endforeach

                        <!-- Ketentuan Rapat -->
                        @php
                            $ketentuanFields = [
                                ['label' => 'Ketepatan waktu mulai', 'value' => 'skor_waktu_mulai', 'start' => 'Start', 'end' => $data['waktu_mulai'], 'keterangan' => '-10 per 3 menit keterlambatan'],
                                ['label' => 'Kesiapan Panitia', 'value' => 'kesiapan_panitia', 'keterangan' => '-20 per peralatan tidak siap'],
                                ['label' => 'Kesiapan Bahan', 'value' => 'kesiapan_bahan', 'keterangan' => '-20 per bahan tidak siap'],
                                ['label' => 'Aktivitas Luar', 'value' => 'aktivitas_luar', 'keterangan' => '-10 per gangguan'],
                                ['label' => 'Gangguan Diskusi', 'value' => 'gangguan_diskusi', 'keterangan' => '-10 per gangguan'],
                                ['label' => 'Gangguan Keluar Masuk', 'value' => 'gangguan_keluar_masuk', 'keterangan' => '-10 per gangguan'],
                                ['label' => 'Gangguan Interupsi', 'value' => 'gangguan_interupsi', 'keterangan' => '-10 per gangguan'],
                                ['label' => 'Ketegasan Moderator', 'value' => 'ketegasan_moderator', 'keterangan' => 'Obyektif'],
                                ['label' => 'Kelengkapan SR', 'value' => 'kelengkapan_sr', 'keterangan' => 'Kaidah, Dokumentasi, CMMS']
                            ];
                        @endphp

                        @foreach($ketentuanFields as $index => $field)
                            <tr>
                                <td style="text-align: center;">{{ count($data['peserta']) + $loop->iteration }}</td>
                                <td>{{ $field['label'] }}</td>
                                <td style="text-align: center;">{{ $field['start'] ?? '' }}</td>
                                <td style="text-align: center;">
                                    @if(isset($field['end']))
                                        {{ \Carbon\Carbon::parse($field['end'])->format('H:i') }}
                                    @endif
                                </td>
                                <td style="text-align: center;">{{ $data[$field['value']] }}</td>
                                <td class="keterangan">{{ $field['keterangan'] }}</td>
                            </tr>
                        @endforeach

                        <!-- Total Score -->
                        @php
                            try {
                                // Debug log
                                \Log::info("Processing unit: {$unitName}", [
                                    'data' => $data
                                ]);
                                
                                // Pastikan data peserta ada dan valid
                                if (!isset($data['peserta']) || !is_array($data['peserta'])) {
                                    throw new Exception('Data peserta tidak valid');
                                }

                                // Hitung total score peserta
                                $totalScorePeserta = 0;
                                foreach ($data['peserta'] as $peserta) {
                                    if (isset($peserta['skor'])) {
                                        $totalScorePeserta += (int)$peserta['skor'];
                                    }
                                }

                                // Hitung total score ketentuan
                                $totalScoreKetentuan = 
                                    (int)($data['kesiapan_panitia'] ?? 0) +
                                    (int)($data['kesiapan_bahan'] ?? 0) +
                                    (int)($data['aktivitas_luar'] ?? 0) +
                                    (int)($data['gangguan_diskusi'] ?? 0) +
                                    (int)($data['gangguan_keluar_masuk'] ?? 0) +
                                    (int)($data['gangguan_interupsi'] ?? 0) +
                                    (int)($data['ketegasan_moderator'] ?? 0) +
                                    (int)($data['kelengkapan_sr'] ?? 0) +
                                    (int)($data['skor_waktu_mulai'] ?? 0);

                                // Hitung skor maksimum
                                $maxScorePeserta = count($data['peserta']) * 100;
                                $maxScoreKetentuan = 900; // 9 ketentuan x 100
                                $maxPossibleScore = $maxScorePeserta + $maxScoreKetentuan;

                                // Hitung persentase
                                $actualTotalScore = $totalScorePeserta + $totalScoreKetentuan;
                                $percentageScore = $maxPossibleScore > 0 ? ($actualTotalScore / $maxPossibleScore) * 100 : 0;

                                \Log::info("Score calculation for {$unitName}", [
                                    'totalScorePeserta' => $totalScorePeserta,
                                    'totalScoreKetentuan' => $totalScoreKetentuan,
                                    'maxPossibleScore' => $maxPossibleScore,
                                    'actualTotalScore' => $actualTotalScore,
                                    'percentageScore' => $percentageScore
                                ]);

                            } catch (\Exception $e) {
                                \Log::error("Error calculating score for {$unitName}: " . $e->getMessage());
                                $percentageScore = 0;
                            }
                        @endphp
                        
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;"><strong>Total Score:</strong></td>
                            <td style="text-align: center;"><strong>{{ number_format($percentageScore, 2) }}%</strong></td>
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

     <!-- Halaman Status Mesin -->
     @foreach($powerPlants as $powerPlant)
     <div class="page-break">
         <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
         <div class="header">
             <h3 class="text-lg font-semibold uppercase mb-2">STATUS MESIN - {{ $powerPlant->name }}</h3>
             <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
         </div>

         <!-- Statistik Unit -->
         <div class="stats-grid">
             @php
                 $plantLogs = $logs->filter(function($log) use ($powerPlant) {
                     return $log->machine->power_plant_id === $powerPlant->id;
                 });

                 $totalDMP = $plantLogs->sum('dmp');
                 $totalDMN = $plantLogs->sum('dmn'); 
                 $totalBeban = $plantLogs->where('status', 'OPERASI')->sum('load_value');
             @endphp
             
             <div class="stat-item">
                 <span >DMP Total:</span>
                 <strong style="display: block; font-size: 16px; color: #2d3748;">{{ number_format($totalDMP, 1) }} MW</strong>
             </div>
             <div class="stat-item">
                 <span>DMN Total:</span>
                 <strong style="display: block; font-size: 16px; color: #2d3748;">{{ number_format($totalDMN, 1) }} MW</strong>
             </div>
             <div class="stat-item">
                 <span >Total Beban:</span>
                 <strong style="display: block; font-size: 16px; color: #2d3748;">{{ number_format($totalBeban, 1) }} MW</strong>
             </div>     
         </div>

         <table class="report-table">
             <thead>
                 <tr>
                     <th style="width: 5%; text-align: center;">No</th>
                     <th style="width: 20%;">Mesin</th>
                     <th style="width: 12%; text-align: center;">DMN</th>
                     <th style="width: 12%; text-align: center;">DMP</th>
                     <th style="width: 12%; text-align: center;">Beban</th>
                     <th style="width: 15%; text-align: center;">Status</th>
                     <th style="width: 24%;">Component</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($powerPlant->machines as $index => $machine)
                     @php
                         $machineLog = $logs->where('machine_id', $machine->id)->first();
                         $status = $machineLog?->status ?? '-';
                         
                         $statusClass = match($status) {
                             'OPERASI' => 'status-operasi',
                             'STANDBY' => 'status-standby',
                             'GANGGUAN' => 'status-gangguan',
                             'PEMELIHARAAN' => 'status-pemeliharaan',
                             'OVERHAUL' => 'status-overhaul',
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
                         <td>{{ $machineLog?->component ?? '-' }}</td>
                     </tr>
                 @endforeach
             </tbody>
         </table>
     </div>
 @endforeach
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
                        <td>{{ $sr->number ?? $sr->id }}</td>
                        <td>{{ optional($sr->powerPlant)->name ?? '-' }}</td>
                        <td>{{ $sr->description ?? '-' }}</td>
                        <td style="text-align: center;">{{ $sr->status ?? '-' }}</td>
                        <td style="text-align: center;">{{ $sr->priority ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data service request</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Halaman Work Orders -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        <div class="header">
            <h2>DAFTAR WORK ORDER</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Nomor WO</th>
                    <th style="width: 15%">Unit</th>
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
                        <td>{{ $wo->number ?? $wo->id }}</td>
                        <td>{{ optional($wo->powerPlant)->name ?? '-' }}</td>
                        <td>{{ $wo->description ?? '-' }}</td>
                        <td>{{ $wo->type ?? '-' }}</td>
                        <td style="text-align: center;">{{ $wo->status ?? '-' }}</td>
                        <td style="text-align: center;">{{ $wo->priority ?? '-' }}</td>
                        <td style="text-align: center;">
                            {{ $wo->target_date ? \Carbon\Carbon::parse($wo->target_date)->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">Tidak ada data work order</td>
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
                    <th style="width: 15%">Unit</th>
                    <th style="width: 35%">Deskripsi</th>
                    <th style="width: 15%">Tanggal Backlog</th>
                    <th style="width: 15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($woBacklogs as $index => $wo)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $wo->no_wo ?? '-' }}</td>
                        <td>{{ optional($wo->powerPlant)->name ?? '-' }}</td>
                        <td>{{ $wo->deskripsi ?? $wo->description ?? '-' }}</td>
                        <td style="text-align: center;">
                            {{ $wo->backlog_date ? \Carbon\Carbon::parse($wo->backlog_date)->format('d/m/Y') : '-' }}
                        </td>
                        <td style="text-align: center;">{{ $wo->status ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data work order backlog</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Halaman Pembahasan Lain-lain -->
    <div class="page-break">
        <img src="{{ $logoSrc }}" alt="PLN Logo" class="logo">
        
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
        
        <div style="margin-top: 100px; text-align: center;">
            <p style="font-weight: bold; margin-bottom: 100px;">MANAJER UP</p>
            <div style="width: 200px; border-top: 1px solid black; margin: 0 auto;"></div>
        </div>
    </div>

   
</body>
</html>