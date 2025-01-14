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
            font-size: 11px;
            padding: 8px;
            border: 1px solid #000;
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
            border: 1px solid #000;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
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
    </style>
</head>
<body>
    <!-- Halaman pertama - Score Card -->
    <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">

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
                @endphp
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Score:</td>
                    <td style="text-align: center;">{{ $grandTotal }}</td>
                    <td>Total score peserta dan ketentuan</td>
                </tr>
            </tbody>
        </table>
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

    <!-- Halaman ketiga - Report Table (dipindah ke akhir) -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>LAPORAN STATUS PEMBANGKIT</h2>
            <p>Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '-' }} 
               s/d {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '-' }}</p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Unit</th>
                    <th>Mesin</th>
                    <th>DMN</th>
                    <th>DMP</th>
                    <th>Beban</th>
                    <th>Status</th>
                    <th>Comp</th>
                
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $log->machine->powerPlant->name }}</td>
                        <td>{{ $log->machine->name }}</td>
                        <td>{{ $log->dmn }}</td>
                        <td>{{ $log->dmp }}</td>
                        <td>{{ $log->load_value }}</td>
                        <td>
                            <span class="status-badge {{ 
                                $log->status === 'Operasi' ? 'status-operasi' : 
                                ($log->status === 'Gangguan' ? 'status-gangguan' : 'status-standby') 
                            }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td>{{ $log->component }}</td>
                      
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" style="text-align: center;">Tidak ada data untuk ditampilkan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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

    <!-- Halaman kelima - WO Backlog Table -->
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
                    <th style="width: 15%">Tanggal</th>
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

    <!-- Halaman Notes -->
    <div class="page-break">
        <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">
        
        <div class="header">
            <h2>CATATAN RAPAT</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <table class="notes-table">
            <thead>
                <tr>
                    <th style="width: 3%">No</th>
                    <th style="width: 7%">No SR</th>
                    <th style="width: 7%">No WO</th>
                    <th style="width: 10%">Unit</th>
                    <th style="width: 15%">Topik</th>
                    <th style="width: 15%">Target</th>
                    <th style="width: 7%">Risk Level</th>
                    <th style="width: 7%">Priority</th>
                    <th style="width: 10%">Previous</th>
                    <th style="width: 10%">Next</th>
                    <th style="width: 5%">PIC</th>
                    <th style="width: 7%">Status</th>
                    <th style="width: 7%">Deadline</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $discussions = collect()
                        ->concat(App\Models\OtherDiscussion::whereDate('created_at', $date)->get())
                        ->concat(App\Models\ClosedDiscussion::whereDate('created_at', $date)->get())
                        ->concat(App\Models\OverdueDiscussion::whereDate('created_at', $date)->get())
                        ->sortBy('created_at');
                    $no = 1;
                @endphp

                @forelse($discussions as $discussion)
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $discussion->sr_number ?? '-' }}</td>
                        <td>{{ $discussion->wo_number ?? '-' }}</td>
                        <td>{{ $discussion->unit }}</td>
                        <td>{{ $discussion->topic }}</td>
                        <td>{{ $discussion->target }}</td>
                        <td>{{ $discussion->risk_level }}</td>
                        <td>{{ $discussion->priority_level }}</td>
                        <td>{{ $discussion->previous_commitment }}</td>
                        <td>{{ $discussion->next_commitment }}</td>
                        <td>{{ $discussion->pic }}</td>
                        <td>{{ $discussion->status }}</td>
                        <td>{{ $discussion->deadline ? $discussion->deadline->format('d/m/Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" style="text-align: center; height: 50px;">Tidak ada catatan untuk tanggal ini</td>
                    </tr>
                @endforelse

                @if($discussions->count() < 10)
                    @for($i = $discussions->count() + 1; $i <= 10; $i++)
                        <tr>
                            <td style="text-align: center;">{{ $i }}</td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                            <td style="height: 50px;"></td>
                        </tr>
                    @endfor
                @endif
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

        <div class="signatures-grid">
            <div class="signature-box">
                <p class="title">ASMAN OPERASI</p>
                @if(isset($signatures['Operasi']))
                    <img src="{{ $signatures['Operasi'] }}" alt="Tanda Tangan ASMAN OPERASI" class="signature-image">
                @endif
                <div class="signature-line"></div>
            </div>

            <div class="signature-box">
                <p class="title">ASMAN PEMELIHARAAN</p>
                @if(isset($signatures['Pemeliharaan']))
                    <img src="{{ $signatures['Pemeliharaan'] }}" alt="Tanda Tangan ASMAN PEMELIHARAAN" class="signature-image">
                @endif
                <div class="signature-line"></div>
            </div>

            <div class="signature-box">
                <p class="title">ASMAN ENJINIRING</p>
                @if(isset($signatures['Enjiniring']))
                    <img src="{{ $signatures['Enjiniring'] }}" alt="Tanda Tangan ASMAN ENJINIRING" class="signature-image">
                @endif
                <div class="signature-line"></div>
            </div>

            <div class="signature-box">
                <p class="title">MANAJER UP</p>
                @if(isset($signatures['Manajer']))
                    <img src="{{ $signatures['Manajer'] }}" alt="Tanda Tangan MANAJER UP" class="signature-image">
                @endif
                <div class="signature-line"></div>
            </div>
        </div>
    </div>

    <style>
    .signatures-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
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
    </style>

    <script>
        window.onload = function() {
            if (!document.querySelector('.error')) {
                window.print();
            }
        }
    </script>
</body>
</html>
