<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notulen Rapat' }}</title>
    
    <style>
        /* Hilangkan @media print dan @page, DomPDF tidak mendukung penuh */
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            line-height: 1.6;
            margin: 1.5cm;
            padding: 0;
            background: #fff;
        }
    
        .notulen-container {
            width: 100%;
            margin: 10px auto;
            padding: 10px;
            background: #fff;
            border: 0;
        }
    
        /* Header table layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
    
        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
    
        .logo-cell {
            width: 100px;
            text-align: center;
        }
    
        .logo-cell img {
            width: 80px;
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
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
    
        /* Info table layout */
        .info-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
    
        .info-table td {
            font-size: 8pt;
            padding: 2px 0;
            vertical-align: top;
        }
    
        .info-label {
            width: 120px;
        }
    
        /* Content layout */
        .content-wrapper {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
        }
    
        .content-section {
            margin-bottom: 10px;
        }
    
        .content-title {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 8pt;
        }
    
        .content-body {
            font-size: 8pt;
            padding-left: 10px;
        }
    
        /* List */
        .content-body ul,
        .content-body ol {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
    
        .content-body li {
            margin-bottom: 3px;
        }
    
        .content-body .pembahasan-point,
        .content-body .tindak-lanjut-point {
            margin-bottom: 4px;
        }
    
        .content-body .pembahasan-subpoints,
        .content-body .tindak-lanjut-subpoints {
            margin-left: 10px;
        }
    
        /* Gambar */
        .content-body img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 10px auto;
            border: 1px solid #ccc;
            /* ❌ Hapus box-shadow, DomPDF tidak dukung */
        }
    
        .content-body .image-wrapper {
            text-align: center;
            margin: 8px 0;
        }
    
        .content-body .image-wrapper img {
            display: inline-block;
            margin: 0;
            max-height: 250px;
        }
    
        /* Signature table layout */
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
    
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 8pt;
            vertical-align: top;
        }
    
        .signature-line {
            width: 150px;
            border-bottom: 1px solid #000;
            margin: 30px auto 5px;
        }
    
        /* Documentation table layout */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
    
        .doc-table th,
        .doc-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 8pt;
        }
    
        .doc-table th {
            background-color: #eaeaea;
        }
    
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
                    <div class="border-bottom">PT PLN NUSANTARA POWE</div>
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
                    {{ $notulen->waktu_selesai ? \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i') : '-' }} WIB
                </td>
            </tr>
            <tr>
                <td class="info-label">Hari/Tanggal</td>
                <td>: {{ $notulen->tanggal ? \App\Helpers\TextFormatter::hariIndonesia($notulen->tanggal) . ', ' . $notulen->tanggal->format('d F Y') : '-' }}</td>
            </tr>
        </table>

        <!-- Content Section -->
        <div class="content-wrapper">
            <div class="content-section">
                <div class="content-title">A. Pembahasan</div>
                <div class="content-body">
                    @php
                    function convertImageSrcToFile($html) {
                        return preg_replace_callback(
                            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
                            function($matches) {
                                $src = $matches[1];
                                // Jika src sudah file://, biarkan
                                if (strpos($src, 'file://') === 0) return $matches[0];
                                // Jika src mengandung /storage/ atau asset url
                                if (strpos($src, '/storage/') !== false) {
                                    $relative = $src;
                                    // Jika src berupa url penuh (http/https), ambil pathnya saja
                                    if (preg_match('#https?://[^/]+(/storage/.*)#', $src, $m)) {
                                        $relative = $m[1];
                                    }
                                    $localPath = public_path(parse_url($relative, PHP_URL_PATH));
                                    if (file_exists($localPath)) {
                                        return str_replace($src, 'file://' . $localPath, $matches[0]);
                                    }
                                }
                                // Jika src asset('storage/...')
                                if (preg_match('#/storage/.*#', $src, $m)) {
                                    $localPath = public_path($m[0]);
                                    if (file_exists($localPath)) {
                                        return str_replace($src, 'file://' . $localPath, $matches[0]);
                                    }
                                }
                                // Jika src relatif ke public
                                $localPath = public_path($src);
                                if (file_exists($localPath)) {
                                    return str_replace($src, 'file://' . $localPath, $matches[0]);
                                }
                                // Jika tidak ditemukan, biarkan src asli
                                return $matches[0];
                            },
                            $html
                        );
                    }
                    @endphp
                        {!! convertImageSrcToFile($notulen->pembahasan) !!}
                </div>
            </div>

            <div class="content-section">
                <div class="content-title">B. Tindak Lanjut</div>
                <div class="content-body">
                    @php
                    // Replace image paths to use public_path for PDF generation
                    $tindakLanjut = preg_replace_callback(
                    '/<img[^>]+src="([^"]+)"[^>]*>/',
                        function($matches) {
                        $src = $matches[1];
                        // If it's a storage URL, convert it to public_path
                        if (strpos($src, '/storage/') !== false) {
                        $storagePath = str_replace('/storage/', '', parse_url($src, PHP_URL_PATH));
                        return str_replace($src, asset('storage/' . $storagePath), $matches[0]);
                        }
                        return $matches[0];
                        },
                        $notulen->tindak_lanjut
                        );
                        @endphp
                        {!! convertImageSrcToFile($notulen->tindak_lanjut) !!}
                </div>
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
                    <div>Kendari, {{ $notulen->tanggal_tanda_tangan ? $notulen->tanggal_tanda_tangan->format('d F Y') : '-' }}</div>
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
                <div style="font-size: 11pt; color: #666;">{{ $notulen->tanggal ? \App\Helpers\TextFormatter::hariIndonesia($notulen->tanggal) . ', ' . $notulen->tanggal->format('d F Y') : '-' }}</div>
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
                <div style="font-size: 11pt; color: #666;">{{ $notulen->tanggal ? \App\Helpers\TextFormatter::hariIndonesia($notulen->tanggal) . ', ' . $notulen->tanggal->format('d F Y') : '-' }}</div>
            </div>

            <table class="doc-table">
                <tr>
                    @forelse($notulen->documentations as $index => $documentation)
                    @if($index % 2 == 0)
                </tr>
                <tr>
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
                <div style="font-size: 11pt; color: #666;">{{ $notulen->tanggal ? \App\Helpers\TextFormatter::hariIndonesia($notulen->tanggal) . ', ' . $notulen->tanggal->format('d F Y') : '-' }}</div>
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