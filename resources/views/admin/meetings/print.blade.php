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
    </style>
</head>
<body>
    <img src="{{ asset('logo/navlog1.png') }}" alt="PLN Logo" class="logo">

    @if(isset($data) && isset($date))
        <div class="header">
            <h2>SCORE CARD DAILY</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
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

    <script>
        window.onload = function() {
            if (!document.querySelector('.error')) {
                window.print();
            }
        }
    </script>
</body>
</html>
