<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>JOB CARD & JSA</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:12px;
    color:#000;
}
.page{
    width:210mm;
    height:297mm;
    padding:20mm;
    page-break-after:always;
}
.header{
    border-bottom:3px solid #000;
    padding-bottom:10px;
    margin-bottom:10px;
}
.logo{
    position:absolute;
}
.logo img{height:55px;}
.title{
    text-align:center;
}
.title h2{font-size:15px;font-weight:bold;}
.title h3{font-size:13px;font-weight:normal;}
.doc-title{
    text-align:center;
    font-size:18px;
    font-weight:bold;
    margin-top:10px;
}
.section{
    margin-top:15px;
}
.section-title{
    font-weight:bold;
    border-bottom:1px solid #000;
    margin-bottom:5px;
}
.table{
    width:100%;
    border-collapse:collapse;
}
.table th,.table td{
    border:1px solid #000;
    padding:5px;
}
.table th{
    background:#eee;
    text-align:center;
}
.signature{
    margin-top:30px;
}
.signature td{
    text-align:center;
    padding-top:40px;
}
.line{
    border-top:1px solid #000;
    margin-top:40px;
}
ol{margin-left:20px;}
ul{margin-left:20px;}
</style>
</head>

<body>

<!-- ================================================= -->
<!-- PAGE 1 : JOB CARD                                 -->
<!-- ================================================= -->
<div class="page">
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('logo/navlog1.png') }}">
        </div>
        <div class="title">
            <h2>PLN NUSANTARA POWER</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
        <div class="doc-title">JOB CARD</div>
    </div>

    <div class="section">
        <div class="section-title">Service Request Information</div>
        <table class="table">
            <tr><td>No WO</td><td>{{ $wo['wonum'] ?? '-' }}</td><td>Status</td><td>{{ $wo['status'] ?? '-' }}</td></tr>
            <tr><td>Job Plan</td><td>{{ $wo['jpnum'] ?? '-' }}</td><td>Priority</td><td>{{ $wo['wopriority'] ?? '-' }}</td></tr>
            <tr><td>Site</td><td>{{ $wo['siteid'] ?? '-' }}</td><td>Work Type</td><td>{{ $wo['worktype'] ?? '-' }}</td></tr>
            <tr><td>Asset</td><td>{{ $wo['assetnum'] ?? '-' }}</td><td>Location</td><td>{{ $wo['location'] ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <b>Task : Pemeriksaan Lub Oil System</b>

        <p><b>A. SAFETY INDUCTION</b></p>
        <ol>
            <li>APD lengkap (Helmet, Shoes, Sarung Tangan)</li>
            <li>Siapkan tools & material</li>
            <li>Lock Out Tag Out</li>
        </ol>

        <p><b>B. LANGKAH KERJA</b></p>
        <ol>
            <li>Inspeksi saluran pelumas</li>
            <li>Periksa kebocoran pipa</li>
            <li>Periksa valve & tangki</li>
        </ol>

        <p><b>C. POST MAINTENANCE TEST</b></p>
        <ol>
            <li>Peralatan bersih</li>
            <li>Tag out dilepas</li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">Planned & Actual Labor</div>
        <table class="table">
            <tr>
                <th>Task ID</th><th>Craft</th><th>Skill</th>
                <th>Plan Qty</th><th>Plan Hrs</th>
                <th>Act Qty</th><th>Act Hrs</th>
            </tr>
            <tr>
                <td>{{ $wo['parent'] ?? '-' }}</td><td>MECH</td><td>JUNIOR</td>
                <td>1</td><td>1</td><td></td><td></td>
            </tr>
        </table>
    </div>
</div>

<!-- ================================================= -->
<!-- PAGE 2 : ISOLASI & FAILURE REPORT                  -->
<!-- ================================================= -->
<div class="page">
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('logo/navlog1.png') }}">
        </div>
        <div class="title">
            <h2>PLN NUSANTARA POWER</h2>
            <h3>Unit Pembangkitan Kendari</h3>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Isolasi dan Perhatian Keselamatan Kerja</div>
        <div style="height:100px;border:1px solid #000;"></div>
    </div>

    <div class="section">
        <div class="section-title">Failure Reporting</div>
        <table class="table">
            <tr><td>Problem</td><td style="height:40px;"></td></tr>
            <tr><td>Cause</td><td style="height:40px;"></td></tr>
            <tr><td>Remedy</td><td style="height:40px;"></td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Work Order Release</div>
        <table class="table signature">
            <tr>
                <td>Supervisor Pemeliharaan<div class="line"></div></td>
                <td>Supervisor Operasi<div class="line"></div></td>
            </tr>
        </table>
    </div>
</div>

<!-- ================================================= -->
<!-- PAGE 3 : FORM JSA IDENTITAS                         -->
<!-- ================================================= -->
<div class="page">
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('logo/navlog1.png') }}">
        </div>
        <div class="title">
            <h2>PT PLN NUSANTARA POWER</h2>
            <h3>INTEGRATED MANAGEMENT SYSTEM</h3>
        </div>
        <div class="doc-title">FORM JOB SAFETY ANALYSIS</div>
    </div>

    <table class="table">
        <tr><td>Nama Pekerjaan</td><td colspan="3">{{ $wo['description'] ?? '-' }}</td></tr>
        <tr><td>Dasar Pekerjaan</td><td colspan="3">WO {{ $wo['wonum'] ?? '-' }}</td></tr>
        <tr><td>Lokasi</td><td colspan="3">{{ $wo['location'] ?? '-' }}</td></tr>
        <tr><td>Pelaksana</td><td colspan="3">MECHD</td></tr>
    </table>
</div>

<!-- ================================================= -->
<!-- PAGE 4 : JSA DETAIL                                 -->
<!-- ================================================= -->
<div class="page">
    <table class="table">
        <tr>
            <th>No</th><th>Tahapan Kerja</th><th>Risiko</th>
            <th>Pengendalian Bahaya</th><th>PIC</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Safety Induction</td>
            <td>Cedera</td>
            <td>APD Lengkap</td>
            <td>Supervisor</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Pemeriksaan</td>
            <td>Kebocoran</td>
            <td>LOTO</td>
            <td>Teknisi</td>
        </tr>
    </table>

    <br>

    <table class="table">
        <tr>
            <th>Nama Pekerja</th>
            <th>Skill</th>
            <th>Peralatan</th>
        </tr>
        <tr><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td></tr>
    </table>
</div>

</body>
</html>
