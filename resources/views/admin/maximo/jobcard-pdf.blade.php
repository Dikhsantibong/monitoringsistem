<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>JOB CARD & JSA - PLN Nusantara Power</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #eaeaea;
            margin: 0;
            padding: 20px;
        }

        .page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 0 6px rgba(0,0,0,0.25);
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-logo img {
            height: 60px;
            margin-bottom: 8px;
        }

        .header h1 {
            margin: 4px 0;
            font-size: 20px;
            font-weight: bold;
        }

        .header h2 {
            margin: 2px 0;
            font-size: 15px;
        }

        .header h3 {
            margin: 2px 0;
            font-size: 13px;
        }

        h3 {
            font-size: 14px;
            margin-top: 16px;
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
        }

        h4 {
            font-size: 13px;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 15px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        ol, ul {
            font-size: 12px;
            margin-left: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 8px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 5px;
        }

        table th {
            background: #f2f2f2;
            text-align: center;
        }

        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            text-align: center;
        }

        .signature div {
            width: 30%;
        }

        .note {
            font-size: 11px;
            margin-top: 10px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

<!-- ===================================================== -->
<!-- PAGE 1 : JOB CARD                                     -->
<!-- ===================================================== -->
<div class="page">

    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo">
        </div>
        <h2>PLN NUSANTARA POWER</h2>
        <h3>Unit Pembangkitan Kendari</h3>
        <h1>JOB CARD</h1>
    </div>

    <h3>Service Request Information</h3>
    <div class="info-grid">
        <div><b>No. Work Order</b> : {{ $wo['wonum'] ?? 'WO0938' }}</div>
        <div><b>Status</b> : {{ $wo['status'] ?? 'APPR' }}</div>
        <div><b>Job Plan</b> : {{ $wo['parent'] ?? 'RAHA-TD-JP-PMM' }}</div>
        <div><b>Priority</b> : {{ $wo['wopriority'] ?? 'Medium' }}</div>
        <div><b>Task</b> : {{ $wo['parent'] ?? 'WT13454' }}</div>
        <div><b>Work Type</b> : {{ $wo['worktype'] ?? 'PM' }}</div>
        <div><b>Site</b> : {{ $wo['siteid'] ?? 'KD' }}</div>
        <div><b>Location</b> : {{ $wo['location'] ?? 'PLTD RAHA UNIT 4' }}</div>
        <div><b>Asset</b> : {{ $wo['assetnum'] ?? 'RAHATD004MJV' }}</div>
        <div><b>Report Date</b> : {{ $wo['reportdate'] ?? '2026-01-14' }}</div>
        <div><b>Schedule Start</b> : {{ $wo['schedstart'] ?? '14:00' }}</div>
        <div><b>Schedule Finish</b> : {{ $wo['schedfinish'] ?? '15:00' }}</div>
    </div>

    <h3>Task : Pemeriksaan Lub Oil System</h3>

    <h4>A. Safety Induction</h4>
    <ol>
        <li>Pastikan penggunaan APD lengkap:
            <ul>
                <li>Safety Helmet</li>
                <li>Safety Shoes</li>
                <li>Sarung Tangan</li>
            </ul>
        </li>
        <li>Siapkan tools dan material kerja</li>
        <li>Lakukan Lock Out Tag Out (LOTO)</li>
    </ol>

    <h4>B. Langkah Kerja</h4>
    <ol>
        <li>Inspeksi sistem pelumasan</li>
        <li>Periksa kebocoran pada pipa dan sambungan</li>
        <li>Bersihkan valve dan komponen terkait</li>
        <li>Pastikan sistem berfungsi normal</li>
    </ol>

    <h4>C. Post Maintenance Test</h4>
    <ol>
        <li>Pastikan peralatan terpasang kembali</li>
        <li>Area kerja bersih dan aman</li>
    </ol>

    <h3>Planned & Actual Labor</h3>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Craft</th>
                <th>Skill Level</th>
                <th>Planned Qty</th>
                <th>Planned Hours</th>
                <th>Actual Qty</th>
                <th>Actual Hours</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>WT13454</td>
                <td>MECH</td>
                <td>Junior</td>
                <td>1</td>
                <td>1</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div>
            Diminta Oleh<br><br><br>
            Supervisor Pemeliharaan
        </div>
        <div>
            Verifikasi<br><br><br>
            Supervisor Operasi
        </div>
        <div>
            Pelepasan Sistem<br><br><br>
            Supervisor KLK3
        </div>
    </div>

</div>

<!-- ===================================================== -->
<!-- PAGE 2 : JOB SAFETY ANALYSIS                           -->
<!-- ===================================================== -->
<div class="page">

    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo">
        </div>
        <h2>PT PLN NUSANTARA POWER</h2>
        <h3>INTEGRATED MANAGEMENT SYSTEM</h3>
        <h1>FORM JOB SAFETY ANALYSIS</h1>
    </div>

    <div class="info-grid">
        <div><b>Nama Pekerjaan</b> : Pemeriksaan Lub Oil System</div>
        <div><b>Lokasi</b> : PLTD RAHA UNIT 4</div>
        <div><b>WO / Task</b> : WO0938 / WT13454</div>
        <div><b>Pelaksana</b> : MECHD</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahapan Kerja</th>
                <th>Potensi Bahaya</th>
                <th>Pengendalian Risiko</th>
                <th>PIC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Safety Induction</td>
                <td>Cedera ringan</td>
                <td>Briefing K3 & APD lengkap</td>
                <td>Supervisor</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Pekerjaan Pemeliharaan</td>
                <td>Listrik, terpeleset</td>
                <td>LOTO, SOP, APD</td>
                <td>Teknisi</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Post Maintenance Test</td>
                <td>Salah operasi</td>
                <td>Uji sesuai prosedur</td>
                <td>Teknisi</td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div>
            Disusun Oleh<br><br><br>
            Supervisor Pemeliharaan
        </div>
        <div>
            Diverifikasi Oleh<br><br><br>
            Supervisor Operasi
        </div>
        <div>
            Disetujui Oleh<br><br><br>
            Supervisor KLK3
        </div>
    </div>

</div>

</body>
</html>
