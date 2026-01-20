<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOB CARD & JSA - PLN Nusantara Power</title>
    <style type="text/css">
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif !important;
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #000000 !important;
            font-size: 12px !important;
        }

        /* Page Container - A4 Portrait */
        .page {
            background: #ffffff !important;
            width: 210mm !important;
            min-height: 297mm !important;
            margin: 0 auto !important;
            padding: 20mm !important;
            page-break-after: always;
            position: relative;
        }

        /* Header Section */
        .header {
            position: relative;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000000 !important;
        }

        .header-container {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .header-logo {
            position: absolute;
            left: 0;
            top: 0;
        }

        .header-logo img {
            height: 58px !important;
            width: auto !important;
            max-width: 120px;
        }

        .header-title {
            text-align: center;
            width: 100%;
            padding-left: 0;
        }

        .header-title h2 {
            font-size: 15px !important;
            font-weight: bold !important;
            margin: 0 0 2px 0 !important;
            color: #000000 !important;
        }

        .header-title h3 {
            font-size: 13px !important;
            font-weight: normal !important;
            margin: 0 !important;
            color: #000000 !important;
        }

        .doc-title {
            text-align: center;
            font-size: 20px !important;
            font-weight: bold !important;
            margin: 10px 0 0 0 !important;
            color: #000000 !important;
            text-transform: uppercase;
        }

        /* Section Titles */
        h3.section-title {
            font-size: 14px !important;
            font-weight: bold !important;
            margin-top: 16px !important;
            margin-bottom: 8px !important;
            padding-bottom: 4px;
            border-bottom: 1px solid #000000 !important;
            color: #000000 !important;
        }

        h4.subsection-title {
            font-size: 13px !important;
            font-weight: bold !important;
            margin-top: 12px !important;
            margin-bottom: 6px !important;
            color: #000000 !important;
            text-transform: uppercase;
        }

        /* Info Grid - 2 Columns */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            font-size: 12px !important;
        }

        .info-grid-row {
            display: table-row;
        }

        .info-grid-cell {
            display: table-cell;
            width: 50%;
            padding: 3px 8px 3px 0;
            vertical-align: top;
            font-size: 12px !important;
        }

        .info-grid-cell b {
            font-weight: bold !important;
        }

        /* Task Title */
        .task-title {
            font-size: 13px !important;
            font-weight: bold !important;
            margin: 12px 0 8px 0 !important;
            color: #000000 !important;
        }

        /* Lists */
        ol, ul {
            font-size: 12px !important;
            margin-left: 20px !important;
            margin-top: 5px !important;
            margin-bottom: 8px !important;
            color: #000000 !important;
        }

        ol li, ul li {
            margin-bottom: 4px !important;
            line-height: 1.4 !important;
        }

        ul {
            list-style-type: disc !important;
        }

        ol {
            list-style-type: decimal !important;
        }

        /* Tables */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 8px !important;
            margin-bottom: 12px !important;
            font-size: 12px !important;
        }

        table th {
            border: 1px solid #000000 !important;
            padding: 6px 5px !important;
            background-color: #f2f2f2 !important;
            text-align: center !important;
            font-weight: bold !important;
            color: #000000 !important;
        }

        table td {
            border: 1px solid #000000 !important;
            padding: 5px !important;
            text-align: left !important;
            color: #000000 !important;
        }

        table td:first-child {
            text-align: center !important;
        }

        /* Signature Section */
        .signature {
            margin-top: 30px !important;
            display: table;
            width: 100%;
            font-size: 12px !important;
        }

        .signature-row {
            display: table-row;
        }

        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center !important;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-cell div {
            margin-bottom: 50px;
            color: #000000 !important;
        }

        .signature-line {
            border-top: 1px solid #000000 !important;
            margin-top: 50px;
            padding-top: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .page {
                margin: 0 !important;
                padding: 20mm !important;
                box-shadow: none !important;
                page-break-after: always;
            }
            .page:last-child {
                page-break-after: auto;
            }
        }

        /* Ensure all text is black */
        p, div, span, td, th, li, h1, h2, h3, h4 {
            color: #000000 !important;
        }
    </style>
</head>
<body>

<!-- ===================================================== -->
<!-- PAGE 1 : JOB CARD                                     -->
<!-- ===================================================== -->
<div class="page" style="background: #ffffff !important; width: 210mm !important; min-height: 297mm !important; padding: 20mm !important; margin: 0 auto !important;">

    <!-- Header -->
    <div class="header" style="border-bottom: 3px solid #000000 !important; padding-bottom: 10px !important; margin-bottom: 15px !important;">
        <div class="header-logo" style="position: absolute !important; left: 0 !important; top: 0 !important;">
            <img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="height: 58px !important; width: auto !important; max-width: 120px !important;">
        </div>
        <div class="header-title" style="text-align: center !important; width: 100% !important;">
            <h2 style="font-size: 15px !important; font-weight: bold !important; margin: 0 0 2px 0 !important; color: #000000 !important;">PLN NUSANTARA POWER</h2>
            <h3 style="font-size: 13px !important; font-weight: normal !important; margin: 0 !important; color: #000000 !important;">Unit Pembangkitan Kendari</h3>
        </div>
        <div class="doc-title" style="text-align: center !important; font-size: 20px !important; font-weight: bold !important; margin: 10px 0 0 0 !important; color: #000000 !important; text-transform: uppercase !important;">
            JOB CARD
        </div>
    </div>

    <!-- Service Request Information -->
    <h3 class="section-title" style="font-size: 14px !important; font-weight: bold !important; margin-top: 16px !important; margin-bottom: 8px !important; padding-bottom: 4px !important; border-bottom: 1px solid #000000 !important; color: #000000 !important;">Service Request Information</h3>
    
    <div class="info-grid" style="display: table !important; width: 100% !important; margin-bottom: 12px !important; font-size: 12px !important;">
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>No. Work Order</b> : {{ $wo['wonum'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Status</b> : {{ $wo['status'] ?? '-' }}</div>
        </div>
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Job Plan</b> : {{ $wo['parent'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Priority</b> : {{ $wo['wopriority'] ?? '-' }}</div>
        </div>
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Task</b> : {{ $wo['parent'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Site</b> : {{ $wo['siteid'] ?? '-' }}</div>
        </div>
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Work Type</b> : {{ $wo['worktype'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Report Date</b> : {{ $wo['reportdate'] ?? '-' }}</div>
        </div>
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Asset</b> : {{ $wo['assetnum'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Schedule Start</b> : {{ $wo['schedstart'] ?? '-' }}</div>
        </div>
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Location</b> : {{ $wo['location'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Schedule Finish</b> : {{ $wo['schedfinish'] ?? '-' }}</div>
        </div>
    </div>

    <!-- Task Title -->
    <div class="task-title" style="font-size: 13px !important; font-weight: bold !important; margin: 12px 0 8px 0 !important; color: #000000 !important;">
        Task : {{ $wo['description'] ? (strlen($wo['description']) > 60 ? substr($wo['description'], 0, 60) . '...' : $wo['description']) : '-' }}
    </div>

    <!-- A. SAFETY INDUCTION -->
    <h4 class="subsection-title" style="font-size: 13px !important; font-weight: bold !important; margin-top: 12px !important; margin-bottom: 6px !important; color: #000000 !important; text-transform: uppercase !important;">A. SAFETY INDUCTION</h4>
    <ol style="font-size: 12px !important; margin-left: 20px !important; margin-top: 5px !important; margin-bottom: 8px !important; color: #000000 !important;">
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Pastikan seluruh personel menggunakan APD lengkap
            <ul style="font-size: 12px !important; margin-left: 20px !important; margin-top: 4px !important; list-style-type: disc !important; color: #000000 !important;">
                <li style="margin-bottom: 3px !important; color: #000000 !important;">Safety Helmet</li>
                <li style="margin-bottom: 3px !important; color: #000000 !important;">Safety Shoes</li>
                <li style="margin-bottom: 3px !important; color: #000000 !important;">Sarung Tangan</li>
            </ul>
        </li>
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Siapkan tools dan material kerja</li>
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Lakukan Lock Out & Tag Out (LOTO)</li>
    </ol>

    <!-- B. LANGKAH KERJA -->
    <h4 class="subsection-title" style="font-size: 13px !important; font-weight: bold !important; margin-top: 12px !important; margin-bottom: 6px !important; color: #000000 !important; text-transform: uppercase !important;">B. LANGKAH KERJA</h4>
    <ol style="font-size: 12px !important; margin-left: 20px !important; margin-top: 5px !important; margin-bottom: 8px !important; color: #000000 !important;">
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Inspeksi visual saluran pelumasan</li>
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Periksa kebocoran pada pipa dan sambungan</li>
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Membersihkan area sekitar valve</li>
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Pastikan level oli sesuai standar</li>
    </ol>

    <!-- C. POST MAINTENANCE TEST -->
    <h4 class="subsection-title" style="font-size: 13px !important; font-weight: bold !important; margin-top: 12px !important; margin-bottom: 6px !important; color: #000000 !important; text-transform: uppercase !important;">C. POST MAINTENANCE TEST</h4>
    <ol style="font-size: 12px !important; margin-left: 20px !important; margin-top: 5px !important; margin-bottom: 8px !important; color: #000000 !important;">
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Pastikan seluruh peralatan terpasang kembali</li>
        <li style="margin-bottom: 4px !important; line-height: 1.4 !important; color: #000000 !important;">Area kerja dalam kondisi bersih dan aman</li>
    </ol>

    <!-- Planned & Actual Labor Table -->
    <h3 class="section-title" style="font-size: 14px !important; font-weight: bold !important; margin-top: 16px !important; margin-bottom: 8px !important; padding-bottom: 4px !important; border-bottom: 1px solid #000000 !important; color: #000000 !important;">Planned & Actual Labor</h3>
    <table style="width: 100% !important; border-collapse: collapse !important; margin-top: 8px !important; margin-bottom: 12px !important; font-size: 12px !important;">
        <thead>
            <tr>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Task ID</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Craft</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Skill Level</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Planned Quantity</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Planned Hours</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Actual Quantity</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Actual Hours</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: center !important; color: #000000 !important;">{{ $wo['parent'] ?? '-' }}</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">-</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">-</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">-</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">-</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">-</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">-</td>
            </tr>
        </tbody>
    </table>

    <!-- Signature Area -->
    <div class="signature" style="margin-top: 30px !important; display: table !important; width: 100% !important; font-size: 12px !important;">
        <div class="signature-row" style="display: table-row !important;">
            <div class="signature-cell" style="display: table-cell !important; width: 33.33% !important; text-align: center !important; vertical-align: top !important; padding: 0 10px !important;">
                <div style="margin-bottom: 50px !important; color: #000000 !important;">
                    Diminta Oleh<br><br><br>
                    <div class="signature-line" style="border-top: 1px solid #000000 !important; margin-top: 50px !important; padding-top: 5px !important; color: #000000 !important;">Supervisor Pemeliharaan</div>
                </div>
            </div>
            <div class="signature-cell" style="display: table-cell !important; width: 33.33% !important; text-align: center !important; vertical-align: top !important; padding: 0 10px !important;">
                <div style="margin-bottom: 50px !important; color: #000000 !important;">
                    Verifikasi<br><br><br>
                    <div class="signature-line" style="border-top: 1px solid #000000 !important; margin-top: 50px !important; padding-top: 5px !important; color: #000000 !important;">Supervisor Operasi</div>
                </div>
            </div>
            <div class="signature-cell" style="display: table-cell !important; width: 33.33% !important; text-align: center !important; vertical-align: top !important; padding: 0 10px !important;">
                <div style="margin-bottom: 50px !important; color: #000000 !important;">
                    Pelepasan Sistem<br><br><br>
                    <div class="signature-line" style="border-top: 1px solid #000000 !important; margin-top: 50px !important; padding-top: 5px !important; color: #000000 !important;">Supervisor KLK3</div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===================================================== -->
<!-- PAGE 2 : JOB SAFETY ANALYSIS                           -->
<!-- ===================================================== -->
<div class="page" style="background: #ffffff !important; width: 210mm !important; min-height: 297mm !important; padding: 20mm !important; margin: 0 auto !important;">

    <!-- Header -->
    <div class="header" style="border-bottom: 3px solid #000000 !important; padding-bottom: 10px !important; margin-bottom: 15px !important;">
        <div class="header-logo" style="position: absolute !important; left: 0 !important; top: 0 !important;">
            <img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="height: 58px !important; width: auto !important; max-width: 120px !important;">
        </div>
        <div class="header-title" style="text-align: center !important; width: 100% !important;">
            <h2 style="font-size: 15px !important; font-weight: bold !important; margin: 0 0 2px 0 !important; color: #000000 !important;">PT PLN NUSANTARA POWER</h2>
            <h3 style="font-size: 13px !important; font-weight: normal !important; margin: 0 !important; color: #000000 !important;">INTEGRATED MANAGEMENT SYSTEM</h3>
        </div>
        <div class="doc-title" style="text-align: center !important; font-size: 20px !important; font-weight: bold !important; margin: 10px 0 0 0 !important; color: #000000 !important; text-transform: uppercase !important;">
            FORM JOB SAFETY ANALYSIS
        </div>
    </div>

    <!-- Informasi Umum JSA -->
    <div class="info-grid" style="display: table !important; width: 100% !important; margin-bottom: 12px !important; font-size: 12px !important;">
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Nama Pekerjaan</b> : {{ $wo['description'] ? (strlen($wo['description']) > 50 ? substr($wo['description'], 0, 50) . '...' : $wo['description']) : '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Lokasi</b> : {{ $wo['location'] ?? '-' }}</div>
        </div>
        <div class="info-grid-row" style="display: table-row !important;">
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>No. WO / Task</b> : {{ $wo['wonum'] ?? '-' }} / {{ $wo['parent'] ?? '-' }}</div>
            <div class="info-grid-cell" style="display: table-cell !important; width: 50% !important; padding: 3px 8px 3px 0 !important; vertical-align: top !important; font-size: 12px !important; color: #000000 !important;"><b>Pelaksana</b> : -</div>
        </div>
    </div>

    <!-- Tabel Analisis Keselamatan -->
    <table style="width: 100% !important; border-collapse: collapse !important; margin-top: 8px !important; margin-bottom: 12px !important; font-size: 12px !important;">
        <thead>
            <tr>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">No</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Tahapan Kerja</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Potensi Bahaya</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">Pengendalian Risiko</th>
                <th style="border: 1px solid #000000 !important; padding: 6px 5px !important; background-color: #f2f2f2 !important; text-align: center !important; font-weight: bold !important; color: #000000 !important;">PIC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: center !important; color: #000000 !important;">1</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Safety Induction</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Cedera ringan</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Penggunaan APD lengkap</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Supervisor</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: center !important; color: #000000 !important;">2</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Pemeriksaan & Pemeliharaan</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Tersengat listrik, terjatuh</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Lakukan LOTO, gunakan APD lengkap</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Teknisi</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: center !important; color: #000000 !important;">3</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Post Maintenance Test</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Kerusakan peralatan</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Lakukan test sesuai prosedur</td>
                <td style="border: 1px solid #000000 !important; padding: 5px !important; text-align: left !important; color: #000000 !important;">Teknisi</td>
            </tr>
        </tbody>
    </table>

    <!-- Signature Area JSA -->
    <div class="signature" style="margin-top: 30px !important; display: table !important; width: 100% !important; font-size: 12px !important;">
        <div class="signature-row" style="display: table-row !important;">
            <div class="signature-cell" style="display: table-cell !important; width: 33.33% !important; text-align: center !important; vertical-align: top !important; padding: 0 10px !important;">
                <div style="margin-bottom: 50px !important; color: #000000 !important;">
                    Disusun Oleh<br><br><br>
                    <div class="signature-line" style="border-top: 1px solid #000000 !important; margin-top: 50px !important; padding-top: 5px !important; color: #000000 !important;">Supervisor Pemeliharaan</div>
                </div>
            </div>
            <div class="signature-cell" style="display: table-cell !important; width: 33.33% !important; text-align: center !important; vertical-align: top !important; padding: 0 10px !important;">
                <div style="margin-bottom: 50px !important; color: #000000 !important;">
                    Diverifikasi Oleh<br><br><br>
                    <div class="signature-line" style="border-top: 1px solid #000000 !important; margin-top: 50px !important; padding-top: 5px !important; color: #000000 !important;">Supervisor Operasi</div>
                </div>
            </div>
            <div class="signature-cell" style="display: table-cell !important; width: 33.33% !important; text-align: center !important; vertical-align: top !important; padding: 0 10px !important;">
                <div style="margin-bottom: 50px !important; color: #000000 !important;">
                    Disetujui Oleh<br><br><br>
                    <div class="signature-line" style="border-top: 1px solid #000000 !important; margin-top: 50px !important; padding-top: 5px !important; color: #000000 !important;">Supervisor KLK3</div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
