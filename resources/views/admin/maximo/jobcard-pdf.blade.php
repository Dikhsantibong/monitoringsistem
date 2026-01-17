<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Jobcard - {{ $jobcard['wonum'] }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #009BBF;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            height: 60px;
            width: auto;
            margin-right: 15px;
        }
        
        .header-content {
            flex-grow: 1;
            text-align: center;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #009BBF;
            margin: 5px 0;
        }
        
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-top: 5px;
        }
        
        .jobcard-info {
            text-align: right;
            font-size: 12px;
            color: #888;
        }
        
        .content {
            margin-top: 20px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-label {
            width: 200px;
            font-weight: bold;
            color: #555;
            font-size: 13px;
        }
        
        .info-value {
            flex: 1;
            color: #333;
            font-size: 13px;
        }
        
        .description-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
            margin-top: 10px;
            min-height: 80px;
        }
        
        .description-text {
            color: #333;
            font-size: 13px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #888;
            padding: 10px;
            border-top: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-approved {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-progress {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-default {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            @if(file_exists(public_path('logo/navlog1.png')))
                <img src="{{ public_path('logo/navlog1.png') }}" alt="Logo" class="logo">
            @endif
            <div>
                <div class="title">JOB CARD</div>
                <div class="subtitle">PLN Nusantara Power</div>
            </div>
        </div>
        <div class="jobcard-info">
            <div>Generated: {{ $jobcard['generated_at'] }}</div>
        </div>
    </div>

    <div class="content">
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Work Order Number:</div>
                <div class="info-value"><strong>{{ $jobcard['wonum'] }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Parent WO:</div>
                <div class="info-value">{{ $jobcard['parent'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @php
                        $status = strtoupper($jobcard['status']);
                        $badgeClass = 'status-default';
                        if (in_array($status, ['COMP', 'CLOSE'])) {
                            $badgeClass = 'status-completed';
                        } elseif (in_array($status, ['WAPPR', 'APPR'])) {
                            $badgeClass = 'status-approved';
                        } elseif (in_array($status, ['INPRG', 'IN PROGRESS'])) {
                            $badgeClass = 'status-progress';
                        }
                    @endphp
                    <span class="status-badge {{ $badgeClass }}">{{ $jobcard['status'] }}</span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Work Type:</div>
                <div class="info-value">{{ $jobcard['worktype'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Priority:</div>
                <div class="info-value">{{ $jobcard['wopriority'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $jobcard['location'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Asset Number:</div>
                <div class="info-value">{{ $jobcard['assetnum'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Site ID:</div>
                <div class="info-value">{{ $jobcard['siteid'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Report Date:</div>
                <div class="info-value">{{ $jobcard['reportdate'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status Date:</div>
                <div class="info-value">{{ $jobcard['statusdate'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Scheduled Start:</div>
                <div class="info-value">{{ $jobcard['schedstart'] }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Scheduled Finish:</div>
                <div class="info-value">{{ $jobcard['schedfinish'] }}</div>
            </div>
            
            @if($jobcard['downtime'] && $jobcard['downtime'] !== '-')
            <div class="info-row">
                <div class="info-label">Downtime:</div>
                <div class="info-value">{{ $jobcard['downtime'] }}</div>
            </div>
            @endif
            
            <div class="info-row" style="border-bottom: none; padding-top: 15px;">
                <div class="info-label">Description:</div>
                <div class="info-value"></div>
            </div>
            <div class="description-box">
                <div class="description-text">{{ $jobcard['description'] }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>Jobcard untuk Work Order: {{ $jobcard['wonum'] }} | Generated by Maximo Monitoring System</div>
    </div>
</body>
</html>
