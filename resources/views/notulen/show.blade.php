@extends('layouts.app')

@section('styles')
    <style>
        .notulen-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            font-family: Arial, sans-serif;
        }

        .notulen-header {
            border: 1px solid #000;
            display: flex;
            margin-bottom: 2rem;
        }

        .header-logo {
            display: flex;
            align-items: center;
            border-right: 1px solid #000;
            justify-content: space-between;

        }

        .header-logo img {
            height: 40px;
        }

        .header-text {
            text-align: center;
            justify-content: center;
            font-size: 8pt;
            border-right: 1px solid #000;
            width: 50%;

        }

        .header-number {
            padding-left: 0.5rem;
            font-size: 8pt;
            width: 60%;
        }

        .header-number .border-bottom {
            margin-left: -0.5rem;
            padding-left: 0.5rem;
            border-bottom: 1px solid #000;
        }

        .header-info {
            display: grid;
            grid-template-columns: auto 1fr;
            font-size: 8pt;
            gap: 0.5rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .header-info-item {
            display: contents;
        }

        .header-info-label {
            font-weight: normal;
        }

        .header-info-value {
            margin-left: 0.5rem;
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
            line-height: 1.5;
        }

        .content-body p {
            margin-bottom: 1rem;
        }

        .content-body .pembahasan-point {
            margin-bottom: 0.5rem;
            font-size: 10pt;
        }

        .content-body .pembahasan-point strong {
            font-weight: bold;
        }

        .content-body .pembahasan-detail {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
            font-size: 10pt;
        }

        .content-body .pembahasan-subpoints {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .content-body ul, 
        .content-body ol {
            margin: 0.5rem 0;
            
        }

        .content-body li {
            margin-bottom: 0.5rem;
            font-size: 10pt;
        }

        .content-body ol[style*="lower-alpha"] {
            list-style-type: lower-alpha;
        }

        @media print {
            .content-body .pembahasan-detail,
            .content-body .pembahasan-subpoints {
                margin-left: 1.2rem;
            }

            .content-body ul,
            .content-body ol {
                padding-left: 1.5rem;
            }
        }

        /* Adjust point spacing */
        .content-body br + br {
            display: none;
        }

        .content-body br {
            line-height: 0.3;
        }

        .content-body p {
            margin-bottom: 0.5rem;
        }
        

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            font-size: 10pt;
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

        /* Print button styles */
        .print-button {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background-color: #0095B7;
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background-color: #007a94;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            background-color: #6B7280;
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: #4B5563;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .print-button i {
            font-size: 1.2rem;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .notulen-container {
                margin: 0;
                padding: 1rem;
                max-width: none;
            }

            .print-button, .back-button {
                display: none;
            }
        }

        /* Attendance table styles */
        .page-break {
            page-break-before: always;
            margin-top: 3rem;
        }

        .attendance-section {
            margin: 2rem 0;
        }

        .attendance-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .attendance-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .attendance-subtitle {
            font-size: 1rem;
            color: #666;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: left;
        }

        .attendance-table th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #1a202c;
        }

        .attendance-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .attendance-table tr:hover {
            background-color: #f3f4f6;
        }

        .attendance-signature {
            max-width: 150px;
            max-height: 75px;
            display: block;
            margin: 0 auto;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        /* Documentation styles */
        .documentation-section {
            margin: 2rem 0;
        }

        .documentation-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .documentation-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .documentation-subtitle {
            font-size: 1rem;
            color: #666;
        }

        .documentation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .documentation-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .documentation-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .documentation-caption {
            padding: 1rem;
            font-size: 0.9rem;
            color: #4a5568;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .no-documentation {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            color: #666;
            background: #f8fafc;
            border: 1px dashed #e2e8f0;
            border-radius: 8px;
        }

        @media print {
            .documentation-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .documentation-image {
                height: 150px;
            }
        }

        /* Timeline styles */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #17a2b8;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #17a2b8;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 7px;
            top: 15px;
            height: 100%;
            width: 2px;
            background-color: #17a2b8;
        }

        .timeline-item:last-child:before {
            display: none;
        }

        .timeline-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }

        .timeline-title {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .revision-details {
            background-color: #fff;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .old-value, .new-value {
            margin-bottom: 5px;
        }

        .revision-reason {
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
    </style>
@endsection

@section('content')
    <div class="notulen-container">
        <div class="notulen-header">
            <div class="header-logo">
                <img src="{{ asset('logo/navlogo.png') }}" alt="PLN Logo">
            </div>
            <div class="header-text">
                <div class="border-bottom border-black">PT PLN NUSANTARA POWER</div>
                <div class="border-bottom border-black">INTEGRATED MANAGEMENT SYSTEM</div>
                <div style="font-weight: bold ">FORMULIR NOTULEN RAPAT</div>
            </div>
            <div class="header-number">
                <div class="border-bottom border-black">Nomor Dokumen : FMKP - 145 - 13.3.4.a.a.i - 001</div>
                <div class="border-bottom border-black">Tanggal Terbit :
                    {{ $notulen->tanggal ? $notulen->tanggal->format('d-m-Y') : '-' }}</div>
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
                    WIB</span>
            </div>
            <div class="header-info-item">
                <span class="header-info-label">Hari/Tanggal</span>
                <span class="header-info-value">:
                    {{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</span>
            </div>
        </div>

        <div class="content-wrapper" style="border: 1px solid #000; padding: 1rem; margin-bottom: 2rem;">
            <div class="content-section">
                <div class="content-title">A. Pembahasan</div>
                <div class="content-body">{!! $notulen->pembahasan !!}</div>
            </div>

            <div class="content-section">
                <div class="content-title">B. Tindak Lanjut</div>
                <div class="content-body">{!! $notulen->tindak_lanjut !!}</div>
            </div>
        </div>

        <div class="footer">
            <div class="signature-section">
                <div>Mengetahui,</div>
                <div style="margin-top: 1rem;">Pimpinan Rapat</div>
                <div class="signature-line"></div>
                <div style="margin-top: 0.5rem;">{{ $notulen->pimpinan_rapat_nama ?? '-' }}</div>
            </div>

            <div class="signature-section">
                <div>Kendari, {{ $notulen->tanggal_tanda_tangan ? $notulen->tanggal_tanda_tangan->format('d F Y') : '-' }}
                </div>
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
                            <th style="width: 5%; text-align: center;">No</th>
                            <th style="width: 25%;">Nama</th>
                            <th style="width: 25%;">Jabatan</th>
                            <th style="width: 20%;">Divisi</th>
                            <th style="width: 25%; text-align: center;">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notulen->attendances as $index => $attendance)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $attendance->name }}</td>
                                <td>{{ $attendance->position }}</td>
                                <td>{{ $attendance->division }}</td>
                                <td style="text-align: center;">
                                    <img src="{{ $attendance->signature }}" alt="Tanda Tangan" class="attendance-signature">
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
                            <img src="{{ asset('storage/' . $documentation->image_path) }}" alt="Dokumentasi Rapat" class="documentation-image">
                            @if($documentation->caption)
                                <div class="documentation-caption">{{ $documentation->caption }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="no-documentation">Tidak ada dokumentasi rapat</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Lampiran Dokumen pada halaman terpisah -->
    @if($notulen->files && count($notulen->files))
    <div class="page-break">
        <div class="notulen-container">
            <div class="documentation-section">
                <div class="documentation-header">
                    <div class="documentation-title">LAMPIRAN DOKUMEN (Word/PDF)</div>
                    <div class="documentation-subtitle">{{ $notulen->agenda }}</div>
                    <div class="documentation-subtitle">{{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</div>
                </div>
                <ul style="list-style:none;padding:0;">
                    @foreach($notulen->files as $file)
                        <li style="margin-bottom: 1.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:1rem;">
                            <span style="font-size:1.5rem;vertical-align:middle;">
                                @if(Str::contains($file->file_type, 'pdf')) 📰
                                @elseif(Str::contains($file->file_type, 'word')) 📝
                                @else 📄 @endif
                            </span>
                            <span style="font-weight:bold; font-size:1.1rem; margin-left:8px;vertical-align:middle;">{{ $file->file_name }}</span>
                            @if($file->caption)
                                <div style="font-size:0.95rem; color:#666; margin-left:2.2rem; margin-top:2px;">{{ $file->caption }}</div>
                            @endif
                            <div style="margin-left:2.2rem; margin-top:6px;">
                                <a href="{{ asset('storage/' . $file->file_path) }}" download style="color:#0095B7;text-decoration:underline;">
                                    Download dokumen
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if($notulen->revision_count > 0)
    <div class="page-break">
        <div class="notulen-container">
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> Riwayat Revisi
                        <span class="badge bg-info">{{ $notulen->revision_count }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($notulen->getRevisionHistory() as $revision)
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">
                                    {{ $revision->user->name }} mengubah {{ $revision->getFormattedFieldName() }}
                                    <small class="text-muted">{{ $revision->created_at->diffForHumans() }}</small>
                                </h6>
                                <div class="timeline-body">
                                    <div class="revision-details">
                                        <div class="old-value">
                                            <strong>Sebelum:</strong>
                                            <span>{{ $revision->old_value ?: '-' }}</span>
                                        </div>
                                        <div class="new-value">
                                            <strong>Sesudah:</strong>
                                            <span>{{ $revision->new_value ?: '-' }}</span>
                                        </div>
                                        @if($revision->revision_reason)
                                        <div class="revision-reason mt-2">
                                            <strong>Alasan:</strong>
                                            <span>{{ $revision->revision_reason }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Print Button -->
    <button onclick="window.open('{{ route('notulen.print-pdf', $notulen->id) }}', '_blank').onload = function(){ this.print(); }" class="print-button" style="padding: 6px 16px; font-size: 0.95rem;">
        <i class="fas fa-print"></i>
        Cetak Notulen
    </button>
    <!-- Download ZIP Button -->
    <a href="{{ route('notulen.download-zip', $notulen->id) }}" class="print-button" style="right: 220px; background-color: #38b6ff; padding: 6px 16px; font-size: 0.95rem;">
        <i class="fas fa-file-archive"></i>
        Download ZIP (Notulen + Lampiran)
    </a>

    <!-- Back Button -->
    <a href="{{ route('notulen.form') }}" class="back-button" style="padding: 6px 16px; font-size: 0.95rem;">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </a>
@endsection
