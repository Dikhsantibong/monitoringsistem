<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jobcard - {{ $wo['wonum'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding-right: 10px;
        }
        .info-value {
            display: table-cell;
            width: 70%;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .description-box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 100px;
            margin-top: 10px;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 20px 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>JOBCARD</h1>
        <h2>Work Order: {{ $wo['wonum'] }}</h2>
    </div>

    <div class="info-section">
        <div class="section-title">Informasi Work Order</div>
        <div class="info-row">
            <div class="info-label">Work Order Number:</div>
            <div class="info-value">{{ $wo['wonum'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Parent:</div>
            <div class="info-value">{{ $wo['parent'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ $wo['status'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Work Type:</div>
            <div class="info-value">{{ $wo['worktype'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Priority:</div>
            <div class="info-value">{{ $wo['wopriority'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status Date:</div>
            <div class="info-value">{{ $wo['statusdate'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Report Date:</div>
            <div class="info-value">{{ $wo['reportdate'] }}</div>
        </div>
    </div>

    <div class="info-section">
        <div class="section-title">Informasi Lokasi & Asset</div>
        <div class="info-row">
            <div class="info-label">Site ID:</div>
            <div class="info-value">{{ $wo['siteid'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value">{{ $wo['location'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Asset Number:</div>
            <div class="info-value">{{ $wo['assetnum'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Downtime:</div>
            <div class="info-value">{{ $wo['downtime'] }}</div>
        </div>
    </div>

    <div class="info-section">
        <div class="section-title">Jadwal</div>
        <div class="info-row">
            <div class="info-label">Schedule Start:</div>
            <div class="info-value">{{ $wo['schedstart'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Schedule Finish:</div>
            <div class="info-value">{{ $wo['schedfinish'] }}</div>
        </div>
    </div>

    <div class="info-section">
        <div class="section-title">Description</div>
        <div class="description-box">
            {{ $wo['description'] }}
        </div>
    </div>

    <div class="info-section">
        <div class="section-title">Catatan & Tindakan</div>
        <div class="description-box" style="min-height: 150px;">
            <br><br><br>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>Dibuat Oleh</strong><br>
                <br><br>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Disetujui Oleh</strong><br>
                <br><br>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Diketahui Oleh</strong><br>
                <br><br>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini di-generate pada: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
