<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jobcard {{ $jobcard['wonum'] ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; }
        .meta { font-size: 11px; color: #374151; text-align: right; }
        .box { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; margin-bottom: 10px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 6px 8px; vertical-align: top; }
        .label { width: 160px; color: #374151; }
        .value { font-weight: 600; }
        .desc { white-space: pre-wrap; }
        .section-title { font-size: 13px; font-weight: 700; margin: 0 0 6px 0; }
        .footer { margin-top: 14px; font-size: 10px; color: #6b7280; }
        .sign { margin-top: 18px; width: 100%; border-collapse: collapse; }
        .sign td { width: 33.33%; padding: 10px; text-align: center; }
        .line { margin-top: 40px; border-top: 1px solid #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">JOB CARD WORK ORDER</div>
        <div class="meta">
            <div><strong>Generated:</strong> {{ $jobcard['generated_at'] ?? '-' }}</div>
            <div><strong>Site:</strong> {{ $jobcard['siteid'] ?? '-' }}</div>
        </div>
    </div>

    <div class="box">
        <div class="section-title">Informasi Work Order</div>
        <table class="grid">
            <tr>
                <td class="label">WONUM</td>
                <td class="value">{{ $jobcard['wonum'] ?? '-' }}</td>
                <td class="label">PARENT</td>
                <td class="value">{{ $jobcard['parent'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">STATUS</td>
                <td class="value">{{ $jobcard['status'] ?? '-' }}</td>
                <td class="label">STATUS DATE</td>
                <td class="value">{{ $jobcard['statusdate'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">WORK TYPE</td>
                <td class="value">{{ $jobcard['worktype'] ?? '-' }}</td>
                <td class="label">PRIORITY</td>
                <td class="value">{{ $jobcard['priority'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">ASSET</td>
                <td class="value">{{ $jobcard['assetnum'] ?? '-' }}</td>
                <td class="label">LOCATION</td>
                <td class="value">{{ $jobcard['location'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">SCHED START</td>
                <td class="value">{{ $jobcard['schedstart'] ?? '-' }}</td>
                <td class="label">SCHED FINISH</td>
                <td class="value">{{ $jobcard['schedfinish'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">REPORT DATE</td>
                <td class="value">{{ $jobcard['reportdate'] ?? '-' }}</td>
                <td class="label">DOWNTIME</td>
                <td class="value">{{ $jobcard['downtime'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="section-title">Deskripsi</div>
        <div class="desc">{{ $jobcard['description'] ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="section-title">Catatan Pelaksanaan</div>
        <table class="grid">
            <tr>
                <td class="label">Kendala</td>
                <td class="value" style="font-weight:400;">............................................................</td>
            </tr>
            <tr>
                <td class="label">Tindak Lanjut</td>
                <td class="value" style="font-weight:400;">............................................................</td>
            </tr>
        </table>
    </div>

    <table class="sign">
        <tr>
            <td>
                Mengetahui<br>Supervisor
                <div class="line"></div>
            </td>
            <td>
                Pelaksana<br>Teknisi
                <div class="line"></div>
            </td>
            <td>
                Operator / Pemohon
                <div class="line"></div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis dari data Maximo. Perubahan/annotasi dapat dilakukan melalui sistem pemeliharaan (PDF Editor).
    </div>
</body>
</html>

