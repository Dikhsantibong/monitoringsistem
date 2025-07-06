<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notulen Rapat - {{ $notulen->agenda }}</title>
    <style>
        @page {
            margin: 2.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .notulen-container {
            width: 100%;
            background: white;
        }

        .notulen-header {
            border: 1px solid #000;
            display: flex;
            margin-bottom: 2rem;
            width: 100%;
        }

        .header-logo {
            display: flex;
            align-items: center;
            border-right: 1px solid #000;
            justify-content: space-between;
            padding: 10px;
            width: 15%;
        }

        .header-logo img {
            height: 60px;
            width: auto;
        }

        .header-text {
            text-align: center;
            justify-content: center;
            font-size: 12px;
            border-right: 1px solid #000;
            width: 50%;
            padding: 10px 0;
        }

        .header-number {
            padding-left: 0.5rem;
            font-size: 12px;
            width: 35%;
        }

        .header-number .border-bottom {
            margin-left: -0.5rem;
            padding-left: 0.5rem;
            border-bottom: 1px solid #000;
            padding: 5px;
        }

        .header-info {
            margin: 2rem 0;
        }

        .header-info-item {
            margin-bottom: 0.5rem;
        }

        .header-info-label {
            display: inline-block;
            width: 120px;
        }

        .content-section {
            margin-bottom: 2rem;
        }

        .content-title {
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .content-body {
            margin-left: 1rem;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
        }

        .signature-section {
            text-align: center;
        }

        .signature-line {
            margin-top: 5rem;
            border-bottom: 1px solid #000;
            width: 200px;
            display: inline-block;
        }

        /* Ensure page breaks don't occur within sections */
        .content-section, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="notulen-container">
        <div class="notulen-header">
            <div class="header-logo">
                <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo">
            </div>
            <div class="header-text">
                <div class="border-bottom">PT PLN NUSANTARA POWER</div>
                <div class="border-bottom">INTEGRATED MANAGEMENT SYSTEM</div>
                <div style="font-weight: bold">FORMULIR NOTULEN RAPAT</div>
            </div>
            <div class="header-number">
                <div class="border-bottom">Nomor Dokumen : FMKP - 145 - 13.3.4.a.a.i - 001</div>
                <div class="border-bottom">Tanggal Terbit : {{ $notulen->tanggal ? $notulen->tanggal->format('d-m-Y') : '-' }}</div>
                <div>Halaman : 1 dari 1</div>
            </div>
        </div>

        <div class="header-info">
            <div class="header-info-item">
                <span class="header-info-label">Agenda</span>
                <span class="header-info-value">: {{ $notulen->agenda ?? '-' }}</span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Tempat</span>
                <span class="header-info-value">: {{ $notulen->tempat ?? '-' }}</span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Peserta</span>
                <span class="header-info-value">: {{ $notulen->peserta ?? '-' }}</span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Waktu</span>
                <span class="header-info-value">:
                    {{ $notulen->waktu_mulai ? \Carbon\Carbon::parse($notulen->waktu_mulai)->format('H:i') : '-' }} -
                    {{ $notulen->waktu_selesai ? \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i') : '-' }}
                    WIB
                </span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Hari/Tanggal</span>
                <span class="header-info-value">: {{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</span>
            </div>
        </div>

        <div class="content-section">
            <div class="content-title">A. Pembahasan</div>
            <div class="content-body">{!! $notulen->pembahasan ?? '-' !!}</div>
        </div>

        <div class="content-section">
            <div class="content-title">B. Tindak Lanjut</div>
            <div class="content-body">{!! $notulen->tindak_lanjut ?? '-' !!}</div>
        </div>

        <!-- Daftar Hadir -->
        <div class="content-section">
            <div class="content-title">C. Daftar Hadir</div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f3f4f6;">No</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f3f4f6;">Nama</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f3f4f6;">Jabatan</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f3f4f6;">Divisi</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f3f4f6;">Waktu</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f3f4f6;">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $index => $attendance)
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $attendance->name }}</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $attendance->position }}</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $attendance->division }}</td>
                        <td style="border: 1px solid #000; padding: 8px;">
                            {{ \Carbon\Carbon::parse($attendance->time)->format('d/m/Y H:i') }}
                        </td>
                        <td style="border: 1px solid #000; padding: 8px;">
                            <img src="{{ $attendance->signature }}" alt="Tanda tangan" style="height: 40px;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Dokumentasi -->
        @if($notulen->documentation_images)
        <div class="content-section" style="page-break-before: always;">
            <div class="content-title">D. Dokumentasi</div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 1rem;">
                @foreach(json_decode($notulen->documentation_images) as $image)
                <div>
                    <img src="{{ storage_path('app/public/' . $image) }}"
                         alt="Dokumentasi rapat"
                         style="width: 100%; height: auto; object-fit: cover; border-radius: 4px;">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="footer">
            <div class="signature-section">
                <div>Mengetahui,</div>
                <div style="margin-top: 1rem;">Pimpinan Rapat</div>
                <div class="signature-line"></div>
                <div style="margin-top: 0.5rem;">{{ $notulen->pimpinan_rapat_nama ?? '-' }}</div>
            </div>

            <div class="signature-section">
                <div>Kendari, {{ $notulen->tanggal_tanda_tangan ? $notulen->tanggal_tanda_tangan->format('d F Y') : '-' }}</div>
                <div style="margin-top: 1rem;">Notulis</div>
                <div class="signature-line"></div>
                <div style="margin-top: 0.5rem;">{{ $notulen->notulis_nama ?? '-' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
