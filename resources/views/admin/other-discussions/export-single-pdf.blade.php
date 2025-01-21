<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pembahasan {{ $discussion->no_pembahasan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            background-color: #f3f4f6;
            padding: 5px 10px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 150px auto;
            gap: 10px;
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #374151;
        }
        .value {
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .status-open {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-closed {
            background-color: #d1fae5;
            color: #065f46;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
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
                    <th>No</th>
                    <th>Deskripsi</th>
                    <th>PIC</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discussion->commitments as $index => $commitment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $commitment->description }}</td>
                    <td>{{ $commitment->pic }}</td>
                    <td>{{ $commitment->deadline ? \Carbon\Carbon::parse($commitment->deadline)->format('d/m/Y') : '-' }}</td>
                    <td>
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