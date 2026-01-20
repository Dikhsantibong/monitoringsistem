<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOB CARD & JSA - PLN Nusantara Power</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #000000;
            font-size: 11pt;
        }

        .page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm;
            page-break-after: always;
            position: relative;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* Header */
        .header {
            position: relative;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            min-height: 70px;
        }

        .header-logo {
            position: absolute;
            left: 0;
            top: 0;
        }

        .logo-placeholder {
            width: 120px;
            height: 58px;
            background: #FFD700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            color: #000;
        }

        .header-title {
            text-align: center;
            padding-top: 5px;
        }

        .header-title h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header-title h3 {
            font-size: 11pt;
            font-weight: normal;
        }

        .doc-title {
            text-align: left;
            font-size: 18pt;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .doc-title.center {
            text-align: center;
        }

        /* Info Grid */
        .info-grid {
            margin-bottom: 12px;
            font-size: 10pt;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
        }

        .info-cell {
            flex: 1;
            padding-right: 15px;
        }

        .info-cell b {
            font-weight: bold;
        }

        /* Section Title */
        h3.section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }

        h4.subsection-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* Task Title */
        .task-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0;
        }

        /* Lists */
        ol, ul {
            margin-left: 20px;
            margin-top: 5px;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        ol li, ul li {
            margin-bottom: 4px;
            line-height: 1.4;
        }

        ul ul {
            margin-top: 3px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9pt;
        }

        table th {
            border: 1px solid #000;
            padding: 6px 4px;
            background-color: #e8e8e8;
            text-align: center;
            font-weight: bold;
        }

        table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        table td:first-child {
            text-align: center;
        }

        /* Signature Area */
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .signature-cell {
            flex: 1;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }

        /* Page 2 specific */
        .failure-section {
            margin-top: 30px;
        }

        .failure-section h4 {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .failure-field {
            margin-left: 20px;
            margin-bottom: 3px;
        }

        /* Page 3 specific */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .info-table .label-cell {
            background-color: #e8e8e8;
            font-weight: bold;
            width: 30%;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .checkbox {
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            display: inline-block;
        }

        .checkbox.checked::after {
            content: '✓';
            display: block;
            text-align: center;
            line-height: 15px;
        }

        /* Print Styles */
        @media print {
            body {
                background: #ffffff;
                margin: 0;
                padding: 0;
            }
            .page {
                margin: 0;
                padding: 15mm 20mm;
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
            <div class="logo-placeholder">PLN</div>
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
            <div class="logo-placeholder">PLN</div>
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
            <div class="logo-placeholder">PLN</div>
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
                <div class="logo-placeholder" style="width: 80px; height: 40px; margin: 5px auto; font-size: 14px;">PLN</div>
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