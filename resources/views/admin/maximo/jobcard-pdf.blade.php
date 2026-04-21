<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOB CARD & JSA - PLN Nusantara Power</title>
    <style>
        * {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        body {
            font-family: Arial, sans-serif !important;
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 10pt !important;
            line-height: 1.3 !important;
            margin: 0 !important;
        }

        .page {
            background: #ffffff !important;
            /* Width dikurangi padding: 210mm - 10mm (kiri) - 25mm (kanan) = 175mm untuk konten */
            width: 175mm !important;
            min-height: 267mm !important; /* 297mm - 15mm (atas) - 15mm (bawah) */
            margin: 15mm 25mm 15mm 10mm !important; /* top, right, bottom, left (kanan lebih tebal, kiri lebih tipis) */
            padding: 0 !important;
            page-break-after: always !important;
            position: relative !important;
            box-sizing: border-box !important;
        }

        .page:last-child {
            page-break-after: auto !important;
        }

        /* Header */
        .header {
            position: relative !important;
            margin-bottom: 15px !important;
            padding-bottom: 10px !important;
            border-bottom: 2px solid #000 !important;
            min-height: 70px !important;
        }

        .header-logo {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
        }

        .logo-placeholder {
            width: 120px !important;
            height: 58px !important;
            background: #FFD700 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: bold !important;
            font-size: 18px !important;
            color: #000 !important;
        }

        .header-title {
            text-align: center !important;
            padding-top: 5px !important;
        }

        .header-title h2 {
            font-size: 14pt !important;
            font-weight: bold !important;
            margin-bottom: 3px !important;
        }

        .header-title h3 {
            font-size: 11pt !important;
            font-weight: normal !important;
        }

        .doc-title {
            text-align: left !important;
            font-size: 18pt !important;
            font-weight: bold !important;
            margin-top: 10px !important;
            text-transform: uppercase !important;
        }

        .doc-title.center {
            text-align: center !important;
        }

        /* Info Grid */
        .info-grid {
            margin-bottom: 12px !important;
            font-size: 10pt !important;
        }

        .info-row {
            display: block !important;
            margin-bottom: 4px !important;
        }

        .info-cell {
            display: inline-block !important;
            width: 48% !important;
            padding-right: 10px !important;
            vertical-align: top !important;
        }

        .info-cell b {
            font-weight: bold !important;
        }

        /* Section Title */
        h3.section-title {
            font-size: 12pt !important;
            font-weight: bold !important;
            margin-top: 15px !important;
            margin-bottom: 8px !important;
            padding-bottom: 4px !important;
            border-bottom: 1px solid #000 !important;
        }

        h4.subsection-title {
            font-size: 11pt !important;
            font-weight: bold !important;
            margin-top: 12px !important;
            margin-bottom: 6px !important;
            text-transform: uppercase !important;
        }

        /* Task Title */
        .task-title {
            font-size: 11pt !important;
            font-weight: bold !important;
            margin: 10px 0 !important;
        }

        /* Lists */
        ol, ul {
            margin-left: 20px !important;
            margin-top: 5px !important;
            margin-bottom: 8px !important;
            font-size: 10pt !important;
        }

        ol li, ul li {
            margin-bottom: 4px !important;
            line-height: 1.4 !important;
        }

        ul ul {
            margin-top: 3px !important;
        }

        /* Tables */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 10px 0 !important;
            font-size: 9pt !important;
        }

        table th {
            border: 1px solid #000 !important;
            padding: 6px 4px !important;
            background-color: #e8e8e8 !important;
            text-align: center !important;
            font-weight: bold !important;
        }

        table td {
            border: 1px solid #000 !important;
            padding: 5px !important;
            vertical-align: top !important;
        }

        table td:first-child {
            text-align: center !important;
        }

        /* Signature Area */
        .signature {
            margin-top: 40px !important;
            display: block !important;
            width: 100% !important;
        }

        .signature-cell {
            display: inline-block !important;
            width: 32% !important;
            text-align: center !important;
            vertical-align: top !important;
        }

        .signature-line {
            border-top: 1px solid #000 !important;
            margin-top: 60px !important;
            padding-top: 5px !important;
        }

        /* Page 2 specific */
        .failure-section {
            margin-top: 30px !important;
        }

        .failure-section h4 {
            font-weight: bold !important;
            margin-bottom: 5px !important;
        }

        .failure-field {
            margin-left: 20px !important;
            margin-bottom: 3px !important;
        }

        /* Page 3 specific */
        .info-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 15px !important;
        }

        .info-table td {
            border: 1px solid #000 !important;
            padding: 8px !important;
        }

        .info-table .label-cell {
            background-color: #e8e8e8 !important;
            font-weight: bold !important;
            width: 30% !important;
        }

        .checkbox-group {
            display: block !important;
            margin-top: 10px !important;
        }

        .checkbox-item {
            display: inline-block !important;
            margin-right: 15px !important;
            margin-bottom: 5px !important;
        }

        .checkbox {
            width: 15px !important;
            height: 15px !important;
            border: 1px solid #000 !important;
            display: inline-block !important;
            vertical-align: middle !important;
            margin-right: 5px !important;
        }

        .checkbox.checked::after {
            content: '✓' !important;
            display: block !important;
            text-align: center !important;
            line-height: 15px !important;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4;
                margin: 0 !important; /* Margin dikontrol oleh .page */
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .page {
                /* Margin asimetris: kanan lebih tebal (25mm), kiri lebih tipis (10mm) */
                margin: 15mm 25mm 15mm 10mm !important;
                padding: 0 !important;
                width: 175mm !important; /* 210mm - 10mm - 25mm */
                min-height: 267mm !important; /* 297mm - 15mm - 15mm */
                box-shadow: none !important;
                box-sizing: border-box !important;
            }
        }
    </style>
</head>
<body>

<!-- ===================================================== -->
<!-- PAGE 1: JOB CARD - Main Information -->
<!-- ===================================================== -->
<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo" style="width: 120px; height: auto;">
        </div>
        <div class="header-title">
            <h2>PLN Nusantara Power</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
        <div class="doc-title">JOB CARD</div>
    </div>
    

    <!-- No. WO & Description -->
    <table style="border: none; margin: 8px 0 5px 0; width: 100%;">
        <tr>
            <td style="border: none; padding: 3px 5px; width: 25%; font-size: 10pt;"><b>No. WO</b> : {{ $wo['wonum'] ?? '-' }}</td>
            <td style="border: none; padding: 3px 5px; font-size: 10pt;">[{{ $wo['description'] ?? '-' }}]</td>
        </tr>
        <tr>
            <td style="border: none; padding: 3px 5px; font-size: 10pt;"><b>Job Plan</b> :</td>
            <td style="border: none; padding: 3px 5px; font-size: 10pt;"></td>
        </tr>
    </table>

    <!-- Service Request Information (Line 2) -->
    <div style="border-top: 1px solid #000; padding: 8px 0; margin-top: 5px;">
        <div style="font-weight: bold; font-size: 10pt; margin-bottom: 5px;">Service Request Information</div>
        
        <table style="border: none; margin: 0; width: 100%;">
            <tr>
                <td style="border: none; padding: 2px 5px; font-size: 9pt; width: 10%;"><b>No. SR :</b></td>
                <td style="border: none; padding: 2px 5px; font-size: 9pt; width: 10%;">699183</td>
                <td style="border: none; padding: 2px 5px; font-size: 9pt; width: 40%;">[{{ $wo['description'] ?? '-' }}]</td>
                <td style="border: none; padding: 2px 5px; font-size: 9pt; width: 15%;"><b>Reported By :</b></td>
                <td style="border: none; padding: 2px 5px; font-size: 9pt; width: 25%;">9615038FY &nbsp;&nbsp; ASFAR ADRIN ASLI</td>
            </tr>
        </table>

        <!-- Detil SR -->
        <div style="font-weight: bold; font-size: 9pt; margin-top: 8px;">Detil SR</div>
        
        <div style="font-size: 9pt; margin-top: 4px;">
            <div>Gejala :</div>
            <div style="padding-left: 5px;">
                - PKI 17:50 Unit operasi normal<br>
                - Pkl 17:56 Unit trip ( gangguan jaringan 20 Kva)<br>
                - Pkl 20:10 Unit running<br>
                - PKI 20:20 Unit Stop normal, indikasi bunyi abnormal pada turbin
            </div>
        </div>

        <div style="font-size: 9pt; margin-top: 8px;">
            <div>Dampak : Unit tidak dapat beroperasi</div>
        </div>

        <div style="font-size: 9pt; margin-top: 8px;">
            <div>Resiko : Daya mapu berkurang</div>
        </div>

        <div style="font-size: 9pt; margin-top: 8px;">
            <div>Deviasi :</div>
        </div>

        <div style="font-size: 9pt; margin-top: 15px;">
            <div>Tindakan :</div>
            <div style="padding-left: 5px;">
                - dilakukan pemeriksaan pada turbin<br>
                - dilakukan pemeriksaan runner turbin<br>
                - pengecekan runner cop
            </div>
        </div>
    </div>

    <!-- Task Section (Line 3 & 4) -->
    <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 15px 0; margin-top: 10px;">
        <div style="font-size: 11pt; font-weight: bold; margin-bottom: 10px; margin-left: 100px;">Task : {{ $wo['wonum'] ?? '-' }}</div>

        <!-- 3-column info grid -->
        <table style="border: none; margin: 0; font-size: 9pt; width: 100%;">
            <tr>
                <td style="border: none; padding: 2px 5px; width: 33%;"><b>Site :</b> {{ $wo['siteid'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px; width: 34%;"><b>Sched Start :</b> {{ $wo['schedstart'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px; width: 33%;"><b>Sched Finish :</b> {{ $wo['schedfinish'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Status :</b> {{ $wo['status'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px;"><b>Target Start :</b> {{ $wo['schedstart'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px;"><b>Target Finish :</b> {{ $wo['schedfinish'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Parent :</b> {{ $wo['parent'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px;"><b>Actual Start :</b> -</td>
                <td style="border: none; padding: 2px 5px;"><b>Actual Finish :</b></td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Work Type :</b> {{ $wo['worktype'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px;"><b>Report Date :</b> {{ $wo['reportdate'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px;"><b>Reported By :</b> 9615038FY</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Assign :</b> 9213024FY</td>
                <td style="border: none; padding: 2px 5px;"><b>Failure Class :</b></td>
                <td style="border: none; padding: 2px 5px;"><b>GL Account :</b> A-KD-21-377-001-01-22</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Priority :</b> {{ $wo['wopriority'] ?? '-' }}</td>
                <td style="border: none; padding: 2px 5px;"><b>Person Group :</b> MECHD</td>
                <td style="border: none; padding: 2px 5px;"></td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Asset :</b> {{ $wo['assetnum'] ?? '-' }}</td>
                <td colspan="2" style="border: none; padding: 2px 5px;">PLTM WINNING RUNNER TURBINE UNIT 1</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 5px;"><b>Location :</b> {{ $wo['location'] ?? '-' }}</td>
                <td colspan="2" style="border: none; padding: 2px 5px;">AREA ENGINE SYSTEM UNIT 1 PLTM WINNING</td>
            </tr>
        </table>

        <div style="font-size: 11pt; font-weight: bold; margin-top: 10px; margin-left: 100px;">Task : <i>{{ $wo['description'] ?? '-' }}</i></div>
    </div>

    <div style="text-align: left; margin-top: auto; font-size: 10pt; position: absolute; bottom: 0; left: 0;">Halaman : <span style="margin-left: 40px;">1</span></div>
</div>

<!-- ===================================================== -->
<!-- PAGE 2: Isolasi dan Perhatian Keselamatan Kerja -->
<!-- ===================================================== -->
<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo" style="width: 120px; height: auto;">
        </div>
        <div class="header-title">
            <h2>PLN Nusantara Power</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
    </div>

    <h3 class="section-title">Isolasi dan Perhatian Keselamatan Kerja</h3>

    <div style="margin: 30px 0;">
        <div style="text-align: center; margin-bottom: 80px;">
            Diminta Oleh
            <div class="signature-line">Supervisor Pemeliharaan</div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 80px;">
            <div style="flex: 1; text-align: center;">
                Verifikasi
                <div class="signature-line">Supervisor Operasi</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="text-align: right; margin-right: 50px;">Pelepasan Sistem</div>
                <div class="signature-line" style="margin-right: 50px;"></div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 80px;">
            Verifikasi
            <div class="signature-line">Supervisor KLK3</div>
        </div>
    </div>

    <!-- Failure Reporting Section -->
    <div class="failure-section">
        <h3 class="section-title">Failure Reporting</h3>
        <div class="failure-field">Problems : _________________________________________________________________</div>
        <div class="failure-field">Cause : _____________________________________________________________________</div>
        <div class="failure-field">Remedy : ___________________________________________________________________</div>
    </div>

    <!-- Work Order Release Section -->
    <h3 class="section-title" style="margin-top: 40px;">Work Order Release</h3>
    
    <div style="margin: 30px 0;">
        <div style="text-align: center; margin-bottom: 80px;">
            Diminta Oleh
            <div class="signature-line">Supervisor Pemeliharaan</div>
        </div>

        <div style="text-align: center; margin-bottom: 80px;">
            Verifikasi
            <div class="signature-line">Supervisor Operasi</div>
        </div>
    </div>

    <div style="text-align: right; margin-top: 40px; font-size: 10pt;">Halaman : 2</div>
</div>

<!-- ===================================================== -->
<!-- PAGE 3: FORM JOB SAFETY ANALYSIS - Header -->
<!-- ===================================================== -->
<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo" style="width: 120px; height: auto;">
        </div>
        <div class="header-title">
            <h2>PLN Nusantara Power</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
    </div>

    <!-- JSA Header Table -->
    <table class="info-table">
        <tr>
            <td colspan="2" rowspan="2" style="text-align: center; font-weight: bold; font-size: 12pt;">
                PT PLN NUSANTARA POWER<br>
                INTEGRATED MANAGEMENT SYSTEM<br>
                FORM JOB SAFETY ANALYSIS
            </td>
            <td class="label-cell">No Dokumen :</td>
            <td>FMZ 08.2.3.4</td>
        </tr>
        <tr>
            <td class="label-cell">Tgl Terbit :</td>
            <td>02-02-2017</td>
        </tr>
        <tr>
            <td colspan="2" rowspan="2" style="text-align: center;">
                <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo" style="width: 80px; height: auto; margin: 5px auto; display: block;">
            </td>
            <td class="label-cell">Revisi :</td>
            <td>1</td>
        </tr>
        <tr>
            <td class="label-cell">Halaman :</td>
            <td>Page 1 of 1</td>
        </tr>
    </table>

    <!-- Job Information -->
    <table class="info-table">
        <tr>
            <td class="label-cell">NAMA PEKERJAAN (Sesuai No WT)</td>
            <td colspan="3">{{ $wo['description'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">DASAR PERENCANAAN KERJA (Task)</td>
            <td colspan="3">{{ $wo['wonum'] ?? '-' }} - {{ $wo['worktype'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">LOKASI</td>
            <td colspan="3">{{ $wo['location'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">PELAKSANA PEKERJAAN</td>
            <td>-</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="label-cell">Tgl Hari Kerja</td>
            <td>: Tgl ................ sd Tgl ................</td>
            <td><b>Waktu Kerja Per Hari</b></td>
            <td>: Pkl .................. sd Pkl ..................</td>
        </tr>
    </table>

    <!-- Jenis Pekerjaan Checkboxes -->
    <div style="margin: 15px 0;">
        <div style="font-weight: bold; margin-bottom: 10px;">
            <span class="checkbox checked"></span> BERI TANDA : UJI PEKERJAAN YANG HARUS DILENGKAPI
        </div>
        
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>HOT WORK</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>CONFINED SPACE</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>WORKING AT HEIGHT</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>ISOLASI</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>DIGGING</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>VICINITY</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>NEAR & UNDERWATER</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>NOTHING</span>
            </div>
        </div>
    </div>

    <div style="text-align: right; margin-top: 40px; font-size: 10pt;">Halaman : 3</div>
</div>

<!-- ===================================================== -->
<!-- PAGE 4: JSA Analysis Table -->
<!-- ===================================================== -->
<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlogo.png') }}" alt="PLN Logo" style="width: 120px; height: auto;">
        </div>
        <div class="header-title">
            <h2>PLN Nusantara Power</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
    </div>

    <!-- JSA Analysis Table -->
    <!-- Table removed as requested -->

    <!-- Pekerja Table -->
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th style="width: 10%;">Nama Pekerja</th>
                <th style="width: 10%;">Skill / Posisi</th>
                <th style="width: 35%;">Peralatan Kerja yang Digunakan</th>
                <th style="width: 45%;">Keterangan :</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.</td>
                <td></td>
                <td></td>
                <td>1.</td>
            </tr>
            <tr>
                <td>2.</td>
                <td></td>
                <td></td>
                <td>2.</td>
            </tr>
            <tr>
                <td>3.</td>
                <td></td>
                <td></td>
                <td>3.</td>
            </tr>
            <tr>
                <td>4.</td>
                <td></td>
                <td></td>
                <td>4.</td>
            </tr>
            <tr>
                <td>5.</td>
                <td></td>
                <td></td>
                <td>5.</td>
            </tr>
            <tr>
                <td>6.</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px; font-size: 10pt;">Halaman : 4</div>
</div>

</body>
</html>