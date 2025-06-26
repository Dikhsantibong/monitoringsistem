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
        margin-bottom: 2rem;
    }

    .header-logo {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .header-logo img {
        height: 60px;
    }

    .header-text {
        text-align: center;
        font-size: 12px;
        font-weight: bold;
    }

    .header-number {
        font-size: 12px;
        text-align: right;
    }

    .header-info {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.5rem;
        margin-top: 1rem;
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
    }

    .content-title {
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .content-body {
        margin-left: 1rem;
        white-space: pre-line;
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

        .print-button {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="notulen-container">
    <div class="notulen-header">
        <div class="header-logo">
            <img src="{{ asset('logo/navlogo.png') }}" alt="PLN Logo">
            <div class="header-text">
                <div >PT PLN NUSANTARA POWER</div>
                <div>INTEGRATED MANAGEMENT SYSTEM</div>
                <div>FORMULIR NOTULEN RAPAT</div>
            </div>
            <div class="header-number">
                <div>
                    <div>Nomor Dokumen : {{ $notulen->format_nomor }}</div>
                    <div>Tanggal Terbit : {{ $notulen->tanggal ? $notulen->tanggal->format('d-m-Y') : '-' }}</div>
                    <div>Halaman : 1 dari 1</div>
                </div>
            </div>
        </div>
    </div>

    <div class="header-info">
        <div class="header-info-item">
            <span class="header-info-label">Pimpinan Rapat</span>
            <span class="header-info-value">: {{ $notulen->pimpinan_rapat ?? '-' }}</span>
        </div>
        <div class="header-info-item">
            <span class="header-info-label">Hari/Tanggal</span>
            <span class="header-info-value">: {{ $notulen->tanggal ? $notulen->tanggal->format('l, d F Y') : '-' }}</span>
        </div>
        <div class="header-info-item">
            <span class="header-info-label">Tempat</span>
            <span class="header-info-value">: {{ $notulen->tempat ?? '-' }}</span>
        </div>
        <div class="header-info-item">
            <span class="header-info-label">Waktu</span>
            <span class="header-info-value">: {{ $notulen->waktu_mulai ? \Carbon\Carbon::parse($notulen->waktu_mulai)->format('H:i') : '-' }} - {{ $notulen->waktu_selesai ? \Carbon\Carbon::parse($notulen->waktu_selesai)->format('H:i') : '-' }} WIB</span>
        </div>
        <div class="header-info-item">
            <span class="header-info-label">Agenda</span>
            <span class="header-info-value">: {{ $notulen->agenda ?? '-' }}</span>
        </div>
        <div class="header-info-item">
            <span class="header-info-label">Peserta</span>
            <span class="header-info-value">: {{ $notulen->peserta ?? '-' }}</span>
        </div>
    </div>

    <div class="content-section">
        <div class="content-title">A. Pembahasan</div>
        <div class="content-body">{{ $notulen->pembahasan ?? '-' }}</div>
    </div>

    <div class="content-section">
        <div class="content-title">B. Tindak Lanjut</div>
        <div class="content-body">{{ $notulen->tindak_lanjut ?? '-' }}</div>
    </div>

    <div class="footer">
        <div class="signature-section">
            <div>Mengetahui,</div>
            <div style="margin-top: 1rem;">Pimpinan Rapat</div>
            <div class="signature-line"></div>
            <div style="margin-top: 0.5rem;">{{ $notulen->pimpinan_rapat_nama ?? '-' }}</div>
        </div>

        <div class="signature-section">
            <div>Surabaya, {{ $notulen->tanggal_tanda_tangan ? $notulen->tanggal_tanda_tangan->format('d F Y') : '-' }}</div>
            <div style="margin-top: 1rem;">Notulis</div>
            <div class="signature-line"></div>
            <div style="margin-top: 0.5rem;">{{ $notulen->notulis_nama ?? '-' }}</div>
        </div>
    </div>
</div>

<!-- Print Button -->
<button onclick="window.print()" class="print-button">
    <i class="fas fa-print"></i>
    Cetak Notulen
</button>

@endsection
