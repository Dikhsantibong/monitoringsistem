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
            width: 210mm !important;
            min-height: 297mm !important;
            margin: 0 auto !important;
            padding: 15mm 20mm !important;
            page-break-after: always !important;
            position: relative !important;
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
                /* top, right, bottom, left (kanan lebih tebal, kiri lebih tipis) */
                margin: 15mm 25mm 15mm 10mm;
            }
            body {
                background: #ffffff;
                margin: 0;
                padding: 0;
            }
            .page {
                /* Saat render PDF, margin ditangani oleh @page agar pasti ter-apply */
                margin: 0 !important;
                padding: 0 !important;
                width: auto !important;
                min-height: auto !important;
                box-shadow: none;
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
    </div>
    
    <div class="doc-title">JOB CARD</div>

    <!-- Service Request Information -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><b>No. WO</b> : {{ $wo['wonum'] ?? '-' }}</div>
            <div class="info-cell"><b>{{ $wo['description'] ?? '-' }}</b></div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Job Plan</b> : -</div>
            <div class="info-cell"><b>{{ $wo['description'] ?? '-' }}</b></div>
        </div>
    </div>

    <h3 class="section-title">Service Request Information</h3>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><b>Task</b> : -</div>
            <div class="info-cell"></div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Site</b> : {{ $wo['siteid'] ?? '-' }}</div>
            <div class="info-cell"><b>Sched Start</b> : {{ $wo['schedstart'] ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Status</b> : {{ $wo['status'] ?? '-' }}</div>
            <div class="info-cell"><b>Target Start</b> : {{ $wo['schedstart'] ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Parent</b> : {{ $wo['parent'] ?? '-' }}</div>
            <div class="info-cell"><b>Actual Start</b> : -</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Work Type</b> : {{ $wo['worktype'] ?? '-' }}</div>
            <div class="info-cell"><b>Report Date</b> : {{ $wo['reportdate'] ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Assign</b> : -</div>
            <div class="info-cell"><b>Failure Class</b> : -</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Priority</b> : {{ $wo['wopriority'] ?? '-' }}</div>
            <div class="info-cell"><b>Person Group</b> : -</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Asset</b> : {{ $wo['assetnum'] ?? '-' }}</div>
            <div class="info-cell"><b>{{ $wo['location'] ?? '-' }}</b></div>
        </div>
        <div class="info-row">
            <div class="info-cell"><b>Location</b> : {{ $wo['location'] ?? '-' }}</div>
            <div class="info-cell"><b>-</b></div>
        </div>
    </div>

    <div class="task-title">Task : {{ $wo['description'] ?? '-' }}</div>

    <!-- A. SAFETY INDUCTION -->
    <h4 class="subsection-title">A. SAFETY INDUCTION :</h4>
    <ol>
        <li>PASTIKAN KELENGKAPAN APD (ALAT PELINDUNG DIRI)
            <ul style="list-style-type: lower-alpha;">
                <li>SAFETY HELMET</li>
                <li>SAFETY SHOES</li>
                <li>SARUNG TANGAN</li>
            </ul>
        </li>
    </ol>

    <div style="margin-left: 20px; margin-top: 10px;">
        <div>2. SIAPKAN TOOLS DAN MATERIAL YANG DIBUTUHKAN</div>
        <div style="margin-left: 15px;">
            <div>I. TOOLS</div>
            <div>KUNCI PAS RING : 1 SET</div>
            <div>KUNCI SHOCK : 1 SET</div>
            <div>OBENG</div>
            <div>TANG</div>
            <div>II. MATERIAL</div>
            <div>MAJUN : 0.25 KG</div>
            <div>SIKAT KUNINGAN : 1 BUAH</div>
            <div>WD 40 : 1 KALENG</div>
        </div>
        <div style="margin-top: 10px;">3. LAKUKAN LOG OUT PADA PANEL</div>
    </div>

    <!-- B. LANGKAH KERJA -->
    <h4 class="subsection-title">B. LANGKAH KERJA :</h4>
    <ol>
        <li>LAKUKAN INSPEKSI PADA SALURAN PELUMAS, JIKA DITEMUKAN KARAT BERSIHKAN DENGAN WD 40</li>
        <li>LAKUKAN INSPEKSI KEKENCANGAN PADA SAMBUNGAN PIPA PELUMAS</li>
        <li>LAKUKAN INSPEKSI PADA SAMBUNGAN</li>
        <li>LAKUKAN PEMBERSIHAN PADA VALVE, PASTIKAN VALVE MUDAH UNTUK DI BUKA ATAU TUTUP</li>
        <li>LAKUKAN PEMERIKSAAN PADA TANGKI PELUMAS</li>
    </ol>

    <!-- C. POST MAINTENANCE TEST -->
    <h4 class="subsection-title">C. POST MAINTENANCE TEST :</h4>
    <ol>
        <li>Pastikan peralatan terlibat bersih</li>
        <li>LAKUKAN TAG OUT PADA POIN 3</li>
    </ol>

    <!-- Planned & Actual Labor Table -->
    <h3 class="section-title">Planned & Actual Labor</h3>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Craft</th>
                <th>Skill Level</th>
                <th>Labor</th>
                <th>Planned Quantity</th>
                <th>Planned Hours</th>
                <th>Actual Quantity</th>
                <th>Actual Hours</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
                <td>1</td>
                <td>1</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px; font-size: 10pt;">Halaman : 1</div>
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
            <div class="logo-placeholder">PLN</div>
        </div>
        <div class="header-title">
            <h2>PLN Nusantara Power</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
    </div>

    <!-- JSA Analysis Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Tahapan Kerja</th>
                <th style="width: 20%;">Risk</th>
                <th style="width: 30%;">Pengendalian Bahaya (pre caution)</th>
                <th style="width: 15%;">PIC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <div><b>A. SAFETY INDUCTION :</b></div>
                    <div>1. PASTIKAN KELENGKAPAN APD (ALAT PELINDUNG DIRI)</div>
                    <div style="margin-left: 10px;">a. SAFETY HELMET</div>
                    <div style="margin-left: 10px;">b. SAFETY SHOES</div>
                    <div style="margin-left: 10px;">c. SARUNG TANGAN</div>
                    <br>
                    <div>2. SIAPKAN TOOLS DAN MATERIAL YANG DIBUTUHKAN</div>
                    <div>I. TOOLS</div>
                    <div>KUNCI PAS RING : 1 SET</div>
                    <div>KUNCI SHOCK : 1 SET</div>
                    <div>OBENG</div>
                    <div>TANG</div>
                    <div>II. MATERIAL</div>
                    <div>MAJUN : 0.25 KG</div>
                    <div>SIKAT KUNINGAN : 1 BUAH</div>
                    <div>WD 40 : 1 KALENG</div>
                    <br>
                    <div>3. LAKUKAN LOG OUT PADA PANEL</div>
                    <br>
                    <div><b>B. LANGKAH KERJA :</b></div>
                    <div>1. LAKUKAN INSPEKSI PADA SALURAN PELUMAS, JIKA DITEMUKAN KARAT BERSIHKAN DENGAN WD 40</div>
                    <div>2. LAKUKAN INSPEKSI KEKENCANGAN PADA SAMBUNGAN PIPA PELUMAS</div>
                    <div>3. LAKUKAN INSPEKSI PADA SAMBUNGAN</div>
                    <div>4. LAKUKAN PEMBERSIHAN PADA VALVE, PASTIKAN VALVE MUDAH UNTUK DI BUKA ATAU TUTUP</div>
                    <div>5. LAKUKAN PEMERIKSAAN PADA TANGKI PELUMAS</div>
                    <br>
                    <div><b>C. POST MAINTENANCE TEST :</b></div>
                    <div>1. Pastikan peralatan terlibat bersih</div>
                    <div>2. LAKUKAN TAG OUT PADA POIN 3</div>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

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
                <td>1.</td>
                <td>1.</td>
                <td>1. DILARANG merokok di area kerja.</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>2.</td>
                <td>2.</td>
                <td>2. Jagalah kebersihan lingkungan di area kerja.</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>3.</td>
                <td>3.</td>
                <td>3. Jika ada kondisi bahaya tidak aman di sekitar, segera lapor kepada atasan langsung area.</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>4.</td>
                <td>4.</td>
                <td>4. Semua pekerja harus memastikan area yang tidak tercantum dalam izin kerja tanpa seizin dari pengawas area.</td>
            </tr>
            <tr>
                <td>5.</td>
                <td>5.</td>
                <td>5.</td>
                <td>5. Dilarang menyentuh peralatan dan tombol emergency di sekitar area tanpa seizin.</td>
            </tr>
            <tr>
                <td>6.</td>
                <td>6.</td>
                <td>6.</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px; font-size: 10pt;">Halaman : 4</div>
</div>

</body>
</html>