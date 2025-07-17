<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notulen Rapat' }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            }
            
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .notulen-container {
            width: 100%;
            margin: 20px auto;
            padding: 20px;
            background: white;
        }

        /* Header table layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }

        .logo-cell {
            width: 120px;
            text-align: center;
        }

        .logo-cell img {
            width: 100px;
            height: auto;
        }

        .title-cell {
            width: 40%;
            text-align: center;
            font-size: 8pt;
        }

        .number-cell {
            font-size: 8pt;
            padding-left: 10px;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        /* Info table layout */
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .info-table td {
            font-size: 8pt;
            padding: 3px 0;
            vertical-align: top;
        }

        .info-label {
            width: 120px;
        }

        /* Content layout */
        .content-wrapper {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
        }

        .content-section {
            margin-bottom: 15px;
        }

        .content-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .content-body {
            font-size: 8pt;
            padding-left: 15px;
        }

        /* Signature table layout */
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 8pt;
            vertical-align: top;
        }

        .signature-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin: 50px auto 5px;
        }

        /* Documentation table layout */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .doc-table th,
        .doc-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 8pt;
        }

        .doc-table th {
            background-color: #f5f5f5;
        }

        /* Utility classes */
        .page-break {
            page-break-before: always;
        }

        .text-center {
            text-align: center;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="notulen-container">
        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo">
                </td>
                <td class="title-cell">
                    <div class="border-bottom">PT PLN NUSANTARA POWER</div>
                    <div class="border-bottom">INTEGRATED MANAGEMENT SYSTEM</div>
                    <div style="font-weight: bold">FORMULIR NOTULEN RAPAT</div>
                </td>
                <td class="number-cell">
                    <div class="border-bottom">Nomor Dokumen : FMKP - 145 - 13.3.4.a.a.i - 001</div>
                    <div class="border-bottom">Tanggal Terbit : {{ $notulen->tanggal ? $notulen->tanggal->format('d-m-Y') : '-' }}</div>
                    <div>Halaman : 1 dari 1</div>
                </td>
            </tr>
        </table>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td class="info-label">Nomor Notulen</td>
                <td>: {{ $notulen->format_nomor ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Agenda</td>
                <td>: {{ $notulen->agenda ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tempat</td>
                <td>: {{ $notulen->tempat ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Peserta</td>
                <td>: {{ $notulen->peserta ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Waktu</td>
                <td>: {{ $notulen->waktu_mulai ? \Carbon\Carbon::parse($notulen->waktu_mulai)->format('H:i') : '-' }} - 
                    {{ $notulen->waktu_selesai ? \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i') : '-' }} WIB</td>
            </tr>
            <tr>
                <td class="info-label">Hari/Tanggal</td>
                <td>: {{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</td>
            </tr>
        </table>

        <!-- Content Section -->
        <div class="content-wrapper">
            <div class="content-section">
                <div class="content-title">A. Pembahasan</div>
                <div class="content-body">{!! $notulen->pembahasan !!}</div>
            </div>

            <div class="content-section">
                <div class="content-title">B. Tindak Lanjut</div>
                <div class="content-body">{!! $notulen->tindak_lanjut !!}</div>
            </div>
        </div>

        <!-- Signature Section -->
        <table class="signature-table">
            <tr>
                <td>
                    <div>Mengetahui,</div>
                    <div style="margin-top: 10px;">Pimpinan Rapat</div>
                    <div class="signature-line"></div>
                    <div>{{ $notulen->pimpinan_rapat_nama ?? '-' }}</div>
                </td>
                <td>
                    <div>Bau-Bau , {{ $notulen->tanggal_tanda_tangan ? $notulen->tanggal_tanda_tangan->format('d F Y') : '-' }}</div>
                    <div style="margin-top: 10px;">Notulis</div>
                    <div class="signature-line"></div>
                    <div>{{ $notulen->notulis_nama ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Attendance Section -->
    <div class="page-break">
        <div class="notulen-container">
            <div class="text-center mb-10">
                <div style="font-size: 14pt; font-weight: bold;" class="mb-5">DAFTAR HADIR RAPAT</div>
                <div style="font-size: 11pt; color: #666;" class="mb-5">{{ $notulen->agenda }}</div>
                <div style="font-size: 11pt; color: #666;">{{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</div>
            </div>

            <table class="doc-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama</th>
                        <th style="width: 25%;">Jabatan</th>
                        <th style="width: 20%;">Divisi</th>
                        <th style="width: 25%;">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notulen->attendances as $index => $attendance)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $attendance->name }}</td>
                            <td>{{ $attendance->position }}</td>
                            <td>{{ $attendance->division }}</td>
                            <td class="text-center">
                                <img src="{{ $attendance->signature }}" alt="Tanda Tangan" style="max-width: 100px; max-height: 50px;">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data absensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Documentation Section -->
    <div class="page-break">
        <div class="notulen-container">
            <div class="text-center mb-10">
                <div style="font-size: 14pt; font-weight: bold;" class="mb-5">DOKUMENTASI RAPAT</div>
                <div style="font-size: 11pt; color: #666;" class="mb-5">{{ $notulen->agenda }}</div>
                <div style="font-size: 11pt; color: #666;">{{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</div>
            </div>

            <table class="doc-table">
                <tr>
                    @forelse($notulen->documentations as $index => $documentation)
                        @if($index % 2 == 0)
                            </tr><tr>
                        @endif
                        <td style="width: 50%; padding: 15px; text-align: center;">
                            <img src="{{ public_path('storage/' . $documentation->image_path) }}" 
                                 alt="Dokumentasi" 
                                 style="max-width: 300px; max-height: 200px; margin-bottom: 10px;">
                            @if($documentation->caption)
                                <div style="font-size: 9pt;">{{ $documentation->caption }}</div>
                            @endif
                        </td>
                    @empty
                        <td class="text-center" style="padding: 30px;">Tidak ada dokumentasi rapat</td>
                    @endforelse
                </tr>
            </table>
        </div>
    </div>

    <!-- Attachment List -->
    @if($notulen->files && count($notulen->files))
    <div class="page-break">
        <div class="notulen-container">
            <div class="text-center mb-10">
                <div style="font-size: 14pt; font-weight: bold;" class="mb-5">DAFTAR LAMPIRAN DOKUMEN</div>
                <div style="font-size: 11pt; color: #666;" class="mb-5">{{ $notulen->agenda }}</div>
                <div style="font-size: 11pt; color: #666;">{{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</div>
            </div>

            <table class="doc-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Dokumen</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notulen->files as $index => $file)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $file->file_name }}</td>
                            <td>{{ $file->caption ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 15px; font-style: italic; font-size: 9pt;">
                * Dokumen lampiran terlampir pada halaman berikutnya
            </div>
        </div>
    </div>
    @endif

    @if($notulen->revision_count > 0)
    <div style="margin: 20px 0; padding: 10px 0; border-top: 1px solid #000; font-size: 9pt; font-style: italic; color: #666;">
        Dokumen ini telah direvisi sebanyak {{ $notulen->revision_count }} kali.
        Revisi terakhir pada {{ $notulen->revisions()->latest()->first()->created_at->format('d/m/Y H:i') }}
        oleh {{ $notulen->revisions()->latest()->first()->user->name }}.
    </div>
    @endif
</body>
</html>