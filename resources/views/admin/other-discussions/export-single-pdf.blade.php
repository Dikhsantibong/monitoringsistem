<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pembahasan {{ $discussion->no_pembahasan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 240px;
            height: auto;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 0.5px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f4f4f4;
            padding: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .status-open {
            background-color: #ffecec;
            color: #dc3545;
        }
        .status-closed {
            background-color: #e8f5e9;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo/navlog1.png') }}" alt="Logo PLN" class="logo">
        <h2>PT PLN NP UP KENDARI</h2>
        <div class="title">Detail Pembahasan</div>
        <div class="subtitle">No. Pembahasan: {{ $discussion->no_pembahasan }}</div>
    </div>

    <div class="section">
        <div class="section-title">Informasi Umum</div>
        <div class="info-grid">
            <div class="label">No SR:</div>
            <div class="value">{{ $discussion->sr_number }}</div>

            <div class="label">Unit:</div>
            <div class="value">{{ $discussion->unit }}</div>

            <div class="label">Topik:</div> 
            <div class="value">{{ $discussion->topic }}</div>

            <div class="label">Status:</div>
            <div class="value">
                <span class="status-badge {{ $discussion->status === 'Open' ? 'status-open' : 'status-closed' }}">
                    {{ $discussion->status }}
                </span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detail Sasaran</div>
        <div class="info-grid">
            <div class="label">Sasaran:</div>
            <div class="value">{{ $discussion->target }}</div>

            <div class="label">PIC:</div>
            <div class="value">{{ $discussion->pic }}</div>

            <div class="label">Tingkat Resiko:</div>
            <div class="value">{{ $discussion->risk_level }}</div>

            <div class="label">Tingkat Prioritas:</div>
            <div class="value">{{ $discussion->priority_level }}</div>

            <div class="label">Deadline Sasaran:</div>
            <div class="value">
                {{ $discussion->target_deadline ? \Carbon\Carbon::parse($discussion->target_deadline)->format('d/m/Y') : '-' }}
            </div>
        </div>
    </div>

    @if($discussion->commitments && $discussion->commitments->count() > 0)
    <div class="section">
        <div class="section-title">Komitmen</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Deskripsi</th>
                    <th width="20%">PIC</th>
                    <th width="15%">Deadline</th>
                    <th width="20%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discussion->commitments as $index => $commitment)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $commitment->description }}</td>
                    <td>{{ $commitment->pic }}</td>
                    <td align="center">{{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}</td>
                    <td align="center">
                        <span class="status-badge {{ $commitment->status === 'Open' ? 'status-open' : 'status-closed' }}">
                            {{ $commitment->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistem Monitoring Pembahasan</p>
    </div>
</body>
</html>