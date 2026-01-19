<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>JOB CARD & JSA - PLN Nusantara Power</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eaeaea;
            margin: 0;
            padding: 20px;
        }

        .page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 6px rgba(0,0,0,0.25);
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-logo {
            margin-bottom: 10px;
        }

        .header-logo img {
            height: 60px;
            width: auto;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 20px;
        }

        .header h2, .header h3 {
            margin: 2px 0;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 15px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        h3 {
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        h4 {
            font-size: 13px;
            margin-top: 10px;
        }

        ol, ul {
            font-size: 12px;
            margin-left: 20px;
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
            text-align: left;
        }

        table th {
            background: #f2f2f2;
        }

        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .signature div {
            width: 30%;
            text-align: center;
        }

        .note {
            font-size: 11px;
            margin-top: 10px;
        }

        .description-box {
            font-size: 12px;
            padding: 10px;
            margin-top: 5px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>

<!-- ================= PAGE 1 : JOB CARD ================= -->
<div class="page">

    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Nusantara Power Logo">
        </div>
        <h2>PLN NUSANTARA POWER</h2>
        <h3>Unit Pembangkitan Kendari</h3>
        <h1>JOB CARD</h1>
    </div>

    <div class="info-grid">
        <div><b>No. Work Order</b> : {{ $wo['wonum'] ?? '-' }}</div>
        <div><b>Status</b> : {{ $wo['status'] ?? '-' }}</div>
        <div><b>Job Plan</b> : {{ $wo['parent'] ?? '-' }}</div>
        <div><b>Priority</b> : {{ $wo['wopriority'] ?? '-' }}</div>
        <div><b>Task</b> : {{ $wo['parent'] ?? '-' }}</div>
        <div><b>Site</b> : {{ $wo['siteid'] ?? '-' }}</div>
        <div><b>Asset</b> : {{ $wo['assetnum'] ?? '-' }}</div>
        <div><b>Location</b> : {{ $wo['location'] ?? '-' }}</div>
        <div><b>Work Type</b> : {{ $wo['worktype'] ?? '-' }}</div>
        <div><b>Status Date</b> : {{ $wo['statusdate'] ?? '-' }}</div>
        <div><b>Report Date</b> : {{ $wo['reportdate'] ?? '-' }}</div>
        <div><b>Schedule Start</b> : {{ $wo['schedstart'] ?? '-' }}</div>
        <div><b>Schedule Finish</b> : {{ $wo['schedfinish'] ?? '-' }}</div>
        <div><b>Downtime</b> : {{ $wo['downtime'] ?? '-' }}</div>
    </div>

    <h3>Deskripsi Pekerjaan</h3>
    <div class="description-box">
        {{ $wo['description'] ?? '-' }}
    </div>

    <h4>A. Safety Induction</h4>
    <ol>
        <li>Pastikan seluruh personel menggunakan APD lengkap
            <ul>
                <li>Safety Helmet</li>
                <li>Safety Shoes</li>
                <li>Sarung Tangan</li>
            </ul>
        </li>
        <li>Siapkan tools dan material kerja</li>
        <li>Lakukan Lock Out & Tag Out (LOTO)</li>
    </ol>

    <h4>B. Langkah Kerja</h4>
    <ol>
        <li>Inspeksi visual peralatan dan area kerja</li>
        <li>Periksa kondisi komponen sesuai dengan deskripsi pekerjaan</li>
        <li>Lakukan pemeliharaan sesuai prosedur</li>
        <li>Pastikan seluruh peralatan dalam kondisi baik</li>
    </ol>

    <h4>C. Post Maintenance Test</h4>
    <ol>
        <li>Pastikan seluruh peralatan terpasang kembali</li>
        <li>Area kerja dalam kondisi bersih dan aman</li>
    </ol>

    <h3>Planned & Actual Labor</h3>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Craft</th>
                <th>Skill</th>
                <th>Planned Qty</th>
                <th>Planned Hours</th>
                <th>Actual Qty</th>
                <th>Actual Hours</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $wo['parent'] ?? '-' }}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div>
            Dibuat Oleh<br><br><br>
            (_____________)
        </div>
        <div>
            Disetujui Oleh<br><br><br>
            (_____________)
        </div>
        <div>
            Diketahui Oleh<br><br><br>
            (_____________)
        </div>
    </div>

</div>

<!-- ================= PAGE 2 : JSA ================= -->
<div class="page">

    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Nusantara Power Logo">
        </div>
        <h1>FORM JOB SAFETY ANALYSIS (JSA)</h1>
    </div>

    <div class="info-grid">
        <div><b>Nama Pekerjaan</b> : {{ $wo['description'] ? substr($wo['description'], 0, 50) . (strlen($wo['description']) > 50 ? '...' : '') : '-' }}</div>
        <div><b>Lokasi</b> : {{ $wo['location'] ?? '-' }}</div>
        <div><b>No. WO / Task</b> : {{ $wo['wonum'] ?? '-' }} / {{ $wo['parent'] ?? '-' }}</div>
        <div><b>Pelaksana</b> : -</div>
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
                <td>Penggunaan APD lengkap</td>
                <td>Supervisor</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Pemeriksaan & Pemeliharaan</td>
                <td>Tersengat listrik, terjatuh</td>
                <td>Lakukan LOTO, gunakan APD lengkap</td>
                <td>Teknisi</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Post Maintenance Test</td>
                <td>Kerusakan peralatan</td>
                <td>Lakukan test sesuai prosedur</td>
                <td>Teknisi</td>
            </tr>
        </tbody>
    </table>

    <div class="note">
        <b>Catatan Keselamatan:</b>
        <ul>
            <li>Dilarang merokok di area kerja</li>
            <li>Laporkan kondisi tidak aman kepada atasan</li>
            <li>Hentikan pekerjaan jika terjadi kondisi berbahaya</li>
            <li>Pastikan semua peralatan dalam kondisi baik sebelum digunakan</li>
        </ul>
    </div>

    <div class="signature">
        <div>
            Disusun Oleh<br><br><br>
            (_____________)
        </div>
        <div>
            Diperiksa Oleh<br><br><br>
            (_____________)
        </div>
        <div>
            Disetujui Oleh<br><br><br>
            (_____________)
        </div>
    </div>

</div>

</body>
</html>
