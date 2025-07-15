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
            height: 35px;
            width: auto;

        }

        .header-text {
            text-align: center;
            justify-content: center;
            font-size: 8pt;
            border-right: 1px solid #000;
            width: 50%;
            padding: 10px 0;
        }

        .header-number {
            padding-left: 0.5rem;
            font-size: 8pt;
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
            font-size: 10pt;
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
            font-size: 10pt;
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

        /* Attendance table styles */
        .page-break {
            page-break-before: always;
        }

        .attendance-section {
            margin: 2rem 0;
            page-break-inside: avoid;
        }

        .attendance-header {
            margin-bottom: 2rem;
            text-align: center;
            font-size: 10pt;
        }

        .attendance-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 8pt;
        }

        .attendance-subtitle {
            font-size: 11pt;
            color: #666;
            margin-bottom: 4pt;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        .attendance-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .attendance-signature {
            max-width: 100px;
            max-height: 50px;
            display: block;
            margin: 0 auto;
        }

        /* Documentation styles */
        .documentation-section {
            margin: 2rem 0;
            page-break-inside: avoid;
        }

        .documentation-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .documentation-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 8pt;
        }

        .documentation-subtitle {
            font-size: 11pt;
            color: #666;
            margin-bottom: 4pt;
        }

        .documentation-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .documentation-item {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            break-inside: avoid;
        }

        .documentation-image {
            max-width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .documentation-caption {
            font-size: 10pt;
            color: #333;
            text-align: center;
            padding-top: 4px;
            border-top: 1px solid #eee;
        }

        .no-documentation {
            text-align: center;
            padding: 2rem;
            color: #666;
            border: 1px dashed #000;
            grid-column: 1 / -1;
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
                <span class="header-info-label">Nomor Notulen</span>
                <span class="header-info-value">: {{ $notulen->format_nomor ?? '-' }}</span>
            </div>
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

        <div class="content-wrapper" style="border: 1px solid #000; padding: 1rem; margin-bottom: 2rem;">
            <div class="content-section">
                <div class="content-title">A. Pembahasan</div>
                <div class="content-body">{!! $notulen->pembahasan ?? '-' !!}</div>
            </div>

            <div class="content-section">
                <div class="content-title">B. Tindak Lanjut</div>
                <div class="content-body">{!! $notulen->tindak_lanjut ?? '-' !!}</div>
            </div>
        </div>

        @if($notulen->revision_count > 0)
        <div style="margin: 20px 0; padding: 10px 0; border-top: 1px solid #000; font-size: 9pt; font-style: italic; color: #666;">
            Dokumen ini telah direvisi sebanyak {{ $notulen->revision_count }} kali.
            Revisi terakhir pada {{ $notulen->revisions()->latest()->first()->created_at->format('d/m/Y H:i') }}
            oleh {{ $notulen->revisions()->latest()->first()->user->name }}.
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

    <!-- Attendance on new page -->
    <div class="page-break">
        <div class="notulen-container">
            <div class="attendance-section">
                <div class="attendance-header">
                    <div class="attendance-title">DAFTAR HADIR RAPAT</div>
                    <div class="attendance-subtitle">{{ $notulen->agenda }}</div>
                    <div class="attendance-subtitle">{{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</div>
                </div>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th class="text-center" style="width: 25%;">Nama</th>
                            <th class="text-center" style="width: 25%;">Jabatan</th>
                            <th class="text-center" style="width: 20%;">Divisi</th>
                            <th class="text-center" style="width: 25%;">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notulen->attendances as $index => $attendance)
                            <tr>
                                <td class="text-center" style="text-align: center;">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $attendance->name }}</td>
                                <td class="text-center">{{ $attendance->position }}</td>
                                <td class="text-center">{{ $attendance->division }}</td>
                                <td class="text-center" style="text-align: center;">
                                    <img src="{{ $attendance->signature }}" alt="Tanda Tangan" class="attendance-signature">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Tidak ada data absensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Documentation on new page -->
    <div class="page-break">
        <div class="notulen-container">
            <div class="documentation-section">
                <div class="documentation-header">
                    <div class="documentation-title">DOKUMENTASI RAPAT</div>
                    <div class="documentation-subtitle">{{ $notulen->agenda }}</div>
                    <div class="documentation-subtitle">{{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</div>
                </div>
                <div class="documentation-grid">
                    @forelse($notulen->documentations as $documentation)
                        <div class="documentation-item">
                            <img src="{{ $documentation->image_url }}" alt="Dokumentasi Rapat" class="documentation-image">
                            @if($documentation->caption)
                                <div class="documentation-caption">{{ $documentation->caption }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="no-documentation">Tidak ada dokumentasi rapat</div>
                    @endforelse
                </div>
                @if($notulen->files && count($notulen->files))
                <div style="margin-top:2rem;">
                    <div class="documentation-title" style="margin-bottom:1rem;">Lampiran Dokumen (Word/PDF)</div>
                    <div class="documentation-grid">
                        @foreach($notulen->files as $file)
                            <div class="documentation-item" style="text-align:center;">
                                <div style="font-size:2rem;">
                                    @if(Str::contains($file->file_type, 'pdf')) 📰
                                    @elseif(Str::contains($file->file_type, 'word')) 📝
                                    @else 📄 @endif
                                </div>
                                <div style="font-weight:bold;word-break:break-all;">{{ $file->file_name }}</div>
                                @if($file->caption)
                                    <div class="documentation-caption">{{ $file->caption }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
