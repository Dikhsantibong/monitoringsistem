@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; padding-top: 64px; background: #f0f4f8; }

        /* Page Header */
        .mm-header {
            background: linear-gradient(135deg, #0a2540 0%, #0d4f6e 50%, #0095B7 100%);
            padding: 2.5rem 1.5rem 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .mm-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(168,214,0,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .mm-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        .mm-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 0.25rem;
        }
        .mm-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.75);
            font-weight: 400;
        }
        .mm-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 1.25rem;
        }
        .mm-date-input {
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.12);
            color: white;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            backdrop-filter: blur(6px);
            outline: none;
            transition: all 0.3s;
        }
        .mm-date-input:focus {
            border-color: #A8D600;
            background: rgba(255,255,255,0.2);
        }
        .mm-btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Inter', sans-serif;
        }
        .mm-btn-primary {
            background: #A8D600;
            color: #0a2540;
        }
        .mm-btn-primary:hover { background: #bde630; transform: translateY(-1px); }
        .mm-btn-secondary {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .mm-btn-secondary:hover { background: rgba(255,255,255,0.25); }
        .mm-btn-secondary.active { background: rgba(168,214,0,0.3); border-color: #A8D600; color: #A8D600; }
        .mm-live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #A8D600;
            animation: mm-pulse 1.5s infinite;
            display: inline-block;
        }
        @keyframes mm-pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(168,214,0,0.5); }
            50% { opacity: 0.7; box-shadow: 0 0 0 6px rgba(168,214,0,0); }
        }

        /* Summary Cards */
        .mm-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            max-width: 1400px;
            margin: -28px auto 0;
            padding: 0 1.5rem;
            position: relative;
            z-index: 3;
        }
        .mm-card {
            background: white;
            border-radius: 14px;
            padding: 18px 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.25s, box-shadow 0.25s;
            border-bottom: 3px solid transparent;
        }
        .mm-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .mm-card-icon {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-bottom: 10px;
        }
        .mm-card-value { font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .mm-card-label { font-size: 0.75rem; color: #64748b; font-weight: 500; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .mm-card.total { border-bottom-color: #0095B7; }
        .mm-card.total .mm-card-icon { background: #e0f7fa; color: #0095B7; }
        .mm-card.total .mm-card-value { color: #0095B7; }
        .mm-card.operasi { border-bottom-color: #22c55e; }
        .mm-card.operasi .mm-card-icon { background: #dcfce7; color: #22c55e; }
        .mm-card.operasi .mm-card-value { color: #22c55e; }
        .mm-card.standby { border-bottom-color: #3b82f6; }
        .mm-card.standby .mm-card-icon { background: #dbeafe; color: #3b82f6; }
        .mm-card.standby .mm-card-value { color: #3b82f6; }
        .mm-card.gangguan { border-bottom-color: #ef4444; }
        .mm-card.gangguan .mm-card-icon { background: #fee2e2; color: #ef4444; }
        .mm-card.gangguan .mm-card-value { color: #ef4444; }
        .mm-card.har { border-bottom-color: #f59e0b; }
        .mm-card.har .mm-card-icon { background: #fef3c7; color: #f59e0b; }
        .mm-card.har .mm-card-value { color: #f59e0b; }
        .mm-card.mo { border-bottom-color: #8b5cf6; }
        .mm-card.mo .mm-card-icon { background: #ede9fe; color: #8b5cf6; }
        .mm-card.mo .mm-card-value { color: #8b5cf6; }

        /* Tab Navigation */
        .mm-tabs-wrap {
            max-width: 1400px;
            margin: 28px auto 0;
            padding: 0 1.5rem;
        }
        .mm-tabs {
            display: flex;
            background: white;
            border-radius: 14px;
            padding: 5px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            gap: 4px;
        }
        .mm-tab {
            flex: 1;
            padding: 12px 20px;
            border-radius: 10px;
            border: none;
            background: transparent;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s;
            color: #64748b;
            font-family: 'Inter', sans-serif;
        }
        .mm-tab.active {
            background: linear-gradient(135deg, #0095B7, #0d4f6e);
            color: white;
            box-shadow: 0 4px 12px rgba(0,149,183,0.3);
        }
        .mm-tab:hover:not(.active) { background: #f1f5f9; color: #0a2540; }

        /* Content Area */
        .mm-content {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 1.5rem 2rem;
        }
        .mm-panel { display: none; animation: mmFadeIn 0.4s ease; }
        .mm-panel.active { display: block; }
        @keyframes mmFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Plant Group */
        .mm-plant-group {
            background: white;
            border-radius: 14px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .mm-plant-header {
            background: linear-gradient(135deg, #f8fafc, #eef2f7);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }
        .mm-plant-header:hover { background: linear-gradient(135deg, #eef6f9, #e8f0f5); }
        .mm-plant-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #0a2540;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mm-plant-name i { color: #0095B7; }
        .mm-plant-badge {
            background: #0095B7;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .mm-plant-body { padding: 0; }
        .mm-plant-body.collapsed { display: none; }

        /* Table Styles */
        .mm-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .mm-table th {
            background: #f8fafc;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .mm-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .mm-table tr:last-child td { border-bottom: none; }
        .mm-table tr:hover td { background: #f8fafc; }

        /* Status Badge */
        .mm-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            white-space: nowrap;
        }
        .mm-status-dot { width: 7px; height: 7px; border-radius: 50%; }
        .mm-status-noh { background: #dcfce7; color: #166534; }
        .mm-status-noh .mm-status-dot { background: #22c55e; }
        .mm-status-rsh, .mm-status-soh { background: #dbeafe; color: #1e40af; }
        .mm-status-rsh .mm-status-dot, .mm-status-soh .mm-status-dot { background: #3b82f6; }
        .mm-status-foh { background: #fee2e2; color: #991b1b; }
        .mm-status-foh .mm-status-dot { background: #ef4444; }
        .mm-status-poh, .mm-status-moh { background: #fef3c7; color: #92400e; }
        .mm-status-poh .mm-status-dot, .mm-status-moh .mm-status-dot { background: #f59e0b; }
        .mm-status-moth { background: #ede9fe; color: #5b21b6; }
        .mm-status-moth .mm-status-dot { background: #8b5cf6; }
        .mm-status-default { background: #f1f5f9; color: #475569; }
        .mm-status-default .mm-status-dot { background: #94a3b8; }

        /* Loading & Error */
        .mm-loading {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .mm-spinner {
            width: 40px; height: 40px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #0095B7;
            border-radius: 50%;
            animation: mmSpin 0.8s linear infinite;
            margin: 0 auto 16px;
        }
        @keyframes mmSpin { to { transform: rotate(360deg); } }
        .mm-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .mm-empty {
            text-align: center;
            padding: 50px 20px;
            color: #94a3b8;
        }
        .mm-empty i { font-size: 2.5rem; margin-bottom: 12px; display: block; }

        /* MW value highlight */
        .mm-mw-val {
            font-weight: 700;
            color: #0095B7;
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mm-header { padding: 1.5rem 1rem 1.5rem; }
            .mm-title { font-size: 1.4rem; }
            .mm-summary { grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 0 1rem; margin-top: -20px; }
            .mm-card { padding: 12px 8px; }
            .mm-card-value { font-size: 1.3rem; }
            .mm-card-icon { width: 32px; height: 32px; font-size: 1rem; }
            .mm-tabs-wrap, .mm-content { padding: 0 1rem; }
            .mm-table { font-size: 0.78rem; }
            .mm-table th, .mm-table td { padding: 8px 10px; }
            .mm-plant-group { overflow-x: auto; }
        }
        @media (max-width: 480px) {
            .mm-summary { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
@endsection

@section('content')
    @include('components.navbar')

    <!-- Header -->
    <div class="mm-header">
        <div class="mm-header-inner">
            <h1 class="mm-title"><i class="fas fa-cogs"></i> MONITORING MESIN</h1>
            <p class="mm-subtitle">UP Kendari — Real-time Machine Monitoring System (Navitas)</p>
            <div class="mm-controls">
                <input type="date" id="mm-date" class="mm-date-input">
                <button class="mm-btn mm-btn-primary" onclick="fetchAllData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="mm-btn mm-btn-secondary" id="mm-auto-btn" onclick="toggleAutoRefresh()">
                    <span class="mm-live-dot"></span> Auto Refresh: <span id="mm-auto-label">OFF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mm-summary" id="mm-summary">
        <div class="mm-card total"><div class="mm-card-icon"><i class="fas fa-industry"></i></div><div class="mm-card-value" id="sum-total">-</div><div class="mm-card-label">Total Mesin</div></div>
        <div class="mm-card operasi"><div class="mm-card-icon"><i class="fas fa-check-circle"></i></div><div class="mm-card-value" id="sum-operasi">-</div><div class="mm-card-label">Operasi</div></div>
        <div class="mm-card standby"><div class="mm-card-icon"><i class="fas fa-pause-circle"></i></div><div class="mm-card-value" id="sum-standby">-</div><div class="mm-card-label">Standby</div></div>
        <div class="mm-card gangguan"><div class="mm-card-icon"><i class="fas fa-exclamation-triangle"></i></div><div class="mm-card-value" id="sum-gangguan">-</div><div class="mm-card-label">Gangguan</div></div>
        <div class="mm-card har"><div class="mm-card-icon"><i class="fas fa-wrench"></i></div><div class="mm-card-value" id="sum-har">-</div><div class="mm-card-label">Pemeliharaan</div></div>
        <div class="mm-card mo"><div class="mm-card-icon"><i class="fas fa-ban"></i></div><div class="mm-card-value" id="sum-mo">-</div><div class="mm-card-label">Mothball/Lainnya</div></div>
    </div>

    <!-- Tabs -->
    <div class="mm-tabs-wrap">
        <div class="mm-tabs">
            <button class="mm-tab active" data-tab="beban" onclick="switchTab('beban')"><i class="fas fa-bolt"></i> Beban (MW / MVAR)</button>
            <button class="mm-tab" data-tab="status" onclick="switchTab('status')"><i class="fas fa-heartbeat"></i> Status Kinerja</button>
        </div>
    </div>

    <!-- Content -->
    <div class="mm-content">
        <div class="mm-panel active" id="panel-beban">
            <div class="mm-loading" id="loading-beban"><div class="mm-spinner"></div>Memuat data beban...</div>
            <div id="content-beban"></div>
        </div>
        <div class="mm-panel" id="panel-status">
            <div class="mm-loading" id="loading-status"><div class="mm-spinner"></div>Memuat status kinerja...</div>
            <div id="content-status"></div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
(function() {
    // === MAPPING DATA ===
    const RKUNIT_NAMES = {
        'KDN':'PLTU NII TANASA','KDR':'PLTM RONGI','KDB':'PLTD BAU-BAU','KDM':'PLTM MIKUASI',
        'KDI':'PLTM WINNING','KDS':'PLTM SABILAMBO','KDW':'PLTD WANGI-WANGI','KDE':'PLTD EREKE',
        'KDK':'PLTD KOLAKA','KDH':'PLTD RAHA','KDL':'PLTD LADUMPI','KDA':'PLTD LANIPA NIPA',
        'KDP':'PLTD POASIA','KDU':'PLTD WUA-WUA','KDG':'PLTD LANGARA','Z':'COMMON UPDK KENDARI'
    };

    const RUNIT_NAMES = {
        'KDA-02':'PLTD LANIPA NIPA #02 (DEUTZ)','KDA-03':'PLTD LANIPA NIPA #03 (DEUTZ)',
        'KDA-04':'PLTD LANIPA NIPA #04 (DEUTZ)','KDA-09':'PLTD LANIPA NIPA #09 (MAN)',
        'KDB-04':'PLTD BAU-BAU #04 (DAIHATSU)','KDB-05':'PLTD BAU-BAU #05 (DAIHATSU)',
        'KDB-07':'PLTD BAU-BAU #07 (DEUTZ)','KDB-08':'PLTD BAU-BAU #08 (DEUTZ)',
        'KDB-13':'PLTD BAU-BAU #13 (DEUTZ)','KDB-16':'PLTD BAU-BAU #16 (CUMMINS)',
        'KDB-17':'PLTD BAU-BAU #17 (CUMMINS)','KDE-04':'PLTD EREKE #04 (DAIHATSU)',
        'KDE-06':'PLTD EREKE #06 (DAIHATSU)','KDE-07':'PLTD EREKE #07 (DAIHATSU)',
        'KDE-09':'PLTD EREKE #09 (DAIHATSU)','KDE-10':'PLTD EREKE #10 (CUMMINS)',
        'KDE-11':'PLTD EREKE #11 (CUMMINS)','KDE-18':'PLTD EREKE EX BAU-BAU #18 (CUMMINS)',
        'KDG-08':'PLTD LANGARA #08 (MAN)','KDG-09':'PLTD LANGARA #15 EX LADUMPI #09 (MITSUBISHI)',
        'KDG-10':'PLTD LANGARA #10 (CATERPILLAR)','KDG-11':'PLTD LANGARA #11 (CUMMINS)',
        'KDG-12':'PLTD LANGARA #12 (CUMMINS)','KDG-13':'PLTD LANGARA #13 (MAN)',
        'KDG-14':'PLTD LANGARA #14 (MITSUBISHI) EX LAMBUYA #09',
        'KDH-04':'PLTD RAHA #04 (DAIHATSU)','KDH-05':'PLTD RAHA #05 (MIRRLEES)',
        'KDH-06':'PLTD RAHA #06 (DEUTZ)','KDH-07':'PLTD RAHA #07 (CUMMINS)',
        'KDH-08':'PLTD RAHA #08 (CUMMINS)','KDH-09':'PLTD RAHA #09 (CUMMINS)',
        'KDH-10':'PLTD RAHA #10 (CUMMINS)','KDH-11':'PLTD RAHA #11 (CUMMINS)',
        'KDH-12':'PLTD RAHA #12 (MITSUBISHI)','KDH-13':'PLTD RAHA #13 (MITSUBISHI)',
        'KDH-14':'PLTD RAHA #14 (MITSUBISHI)','KDH-15':'PLTD RAHA #15 (Mitsubishi) EX BAU-BAU #21',
        'KDI-01':'PLTM WINNING #01','KDI-02':'PLTM WINNING #02',
        'KDK-03':'PLTD KOLAKA #03 (DAIHATSU)','KDK-04':'PLTD KOLAKA #04 (DAIHATSU)',
        'KDK-05':'PLTD KOLAKA #05 (DAIHATSU)','KDK-07':'PLTD KOLAKA #07 (NIIGATA)',
        'KDK-08':'PLTD KOLAKA #08 (MAK)','KDK-09':'PLTD KOLAKA #09 (MAK)',
        'KDL-01':'PLTD LADUMPI #01 (YANMAR)','KDL-02':'PLTD LADUMPI #02 (YANMAR)',
        'KDL-06':'PLTD LADUMPI #06 (CUMMINS)',
        'KDM-01':'PLTM MIKUASI #01','KDN-01':'PLTU NII TANASA #01','KDN-02':'PLTU NII TANASA #02',
        'KDP-01':'PLTD POASIA #01 (MIRRLEES)','KDP-02':'PLTD POASIA #02 (MIRRLEES)',
        'KDP-04':'PLTD POASIA #04 (MIRRLEES)','KDP-05':'PLTD POASIA #05 (MIRRLEES)',
        'KDP-06':'PLTD POASIA #06 EX BAU-BAU #14 (CUMMINS)',
        'KDP-07':'PLTD POASIA #07 EX BAU-BAU #15 (CUMMINS)',
        'KDP-08':'PLTD POASIA #08 EX BAU-BAU #19 (CUMMINS)',
        'KDR-01':'PLTM RONGI #01','KDR-02':'PLTM RONGI #02',
        'KDS-01':'PLTM SABILAMBO #01','KDS-02':'PLTM SABILAMBO #02',
        'KDU-01':'PLTD WUA-WUA #01 (MAK)','KDU-02':'PLTD WUA-WUA #02 (MAK)',
        'KDU-03':'PLTD WUA-WUA #03 (MAK)','KDU-04':'PLTD WUA-WUA #04 (MAK)',
        'KDU-05':'PLTD WUA-WUA #05 (MAK)',
        'KDW-01':'PLTD WANGI-WANGI #01 (DAIHATSU)','KDW-02':'PLTD WANGI-WANGI #02 (DAIHATSU)',
        'KDW-03':'PLTD WANGI-WANGI #03 (SWD)','KDW-04':'PLTD WANGI-WANGI #04 (SWD)',
        'KDW-05':'PLTD WANGI-WANGI #05 (SWD)','KDW-07':'PLTD WANGI-WANGI #07 (CUMMINS)',
        'KDW-08':'PLTD WANGI-WANGI #08 (MITSUBISHI) (EX GI TELLO)',
        'KDW-09':'PLTD WANGI-WANGI #09 (MITSUBISHI) (EX GI TELLO)',
        'KDW-10':'PLTD WANGI-WANGI #10 (Mitsubishi) EX BAU-BAU #23'
    };

    function getUnitName(rkunit, runit) {
        const key = rkunit + '-' + runit;
        return RUNIT_NAMES[key] || (RKUNIT_NAMES[rkunit] || rkunit) + ' #' + runit;
    }
    function getPlantName(rkunit) {
        return RKUNIT_NAMES[rkunit] || rkunit;
    }

    // === STATUS HELPERS ===
    const STATUS_MAP = {
        'NOH': { label:'Operasi Normal', cls:'noh', group:'operasi' },
        'FOH': { label:'Forced Outage', cls:'foh', group:'gangguan' },
        'MOH': { label:'Maintenance Outage', cls:'moh', group:'har' },
        'POH': { label:'Planned Outage', cls:'poh', group:'har' },
        'RSH': { label:'Reserve Shutdown', cls:'rsh', group:'standby' },
        'SOH': { label:'Standby', cls:'soh', group:'standby' },
        'MOTH': { label:'Mothballed', cls:'moth', group:'mo' },
        'MB':  { label:'Mothballed', cls:'moth', group:'mo' }
    };
    function getStatusInfo(code) {
        return STATUS_MAP[code] || { label: code || '-', cls:'default', group:'mo' };
    }
    function makeStatusBadge(code, desc) {
        const info = getStatusInfo(code);
        const lbl = desc || info.label;
        return `<span class="mm-status mm-status-${info.cls}"><span class="mm-status-dot"></span>${lbl}</span>`;
    }

    // === DATE ===
    const dateInput = document.getElementById('mm-date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;
    dateInput.addEventListener('change', fetchAllData);

    // === TAB SWITCHING ===
    window.switchTab = function(tab) {
        document.querySelectorAll('.mm-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
        document.querySelectorAll('.mm-panel').forEach(p => p.classList.toggle('active', p.id === 'panel-' + tab));
    };

    // === AUTO REFRESH ===
    let autoInterval = null;
    window.toggleAutoRefresh = function() {
        const btn = document.getElementById('mm-auto-btn');
        const label = document.getElementById('mm-auto-label');
        if (autoInterval) {
            clearInterval(autoInterval);
            autoInterval = null;
            label.textContent = 'OFF';
            btn.classList.remove('active');
        } else {
            autoInterval = setInterval(fetchAllData, 60000);
            label.textContent = 'ON (60s)';
            btn.classList.add('active');
        }
    };

    // === DATA FETCHING ===
    window.fetchAllData = function() {
        fetchBeban();
        fetchStatus();
    };

    function fetchBeban() {
        const tanggal = dateInput.value;
        const loadEl = document.getElementById('loading-beban');
        const contentEl = document.getElementById('content-beban');
        loadEl.style.display = 'block';
        contentEl.innerHTML = '';

        fetch(`/api/monitoring-mesin/navitas-beban?tanggal=${tanggal}`)
            .then(r => r.json())
            .then(data => {
                loadEl.style.display = 'none';
                if (data.error) { contentEl.innerHTML = `<div class="mm-error"><i class="fas fa-exclamation-circle"></i> ${data.error}</div>`; return; }
                const entries = data.entry || [];
                if (!entries.length) { contentEl.innerHTML = '<div class="mm-empty"><i class="fas fa-database"></i>Tidak ada data beban untuk tanggal ini.</div>'; return; }
                renderBeban(entries, contentEl);
            })
            .catch(err => {
                loadEl.style.display = 'none';
                contentEl.innerHTML = `<div class="mm-error"><i class="fas fa-exclamation-circle"></i> Gagal memuat data: ${err.message}</div>`;
            });
    }

    function fetchStatus() {
        const tanggal = dateInput.value;
        const loadEl = document.getElementById('loading-status');
        const contentEl = document.getElementById('content-status');
        loadEl.style.display = 'block';
        contentEl.innerHTML = '';

        fetch(`/api/monitoring-mesin/navitas-status?tanggal=${tanggal}`)
            .then(r => r.json())
            .then(data => {
                loadEl.style.display = 'none';
                if (data.error) { contentEl.innerHTML = `<div class="mm-error"><i class="fas fa-exclamation-circle"></i> ${data.error}</div>`; return; }
                const entries = data.entry || [];
                if (!entries.length) { contentEl.innerHTML = '<div class="mm-empty"><i class="fas fa-database"></i>Tidak ada data status untuk tanggal ini.</div>'; return; }
                renderStatus(entries, contentEl);
                updateSummary(entries);
            })
            .catch(err => {
                loadEl.style.display = 'none';
                contentEl.innerHTML = `<div class="mm-error"><i class="fas fa-exclamation-circle"></i> Gagal memuat data: ${err.message}</div>`;
            });
    }

    // === RENDER BEBAN ===
    function renderBeban(entries, container) {
        const grouped = groupBy(entries, 'RKUNIT_KODE');
        let html = '';
        for (const [rkunit, items] of Object.entries(grouped)) {
            html += `<div class="mm-plant-group">
                <div class="mm-plant-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                    <div class="mm-plant-name"><i class="fas fa-industry"></i> ${getPlantName(rkunit)}</div>
                    <span class="mm-plant-badge">${items.length} unit</span>
                </div>
                <div class="mm-plant-body">
                    <table class="mm-table">
                        <thead><tr>
                            <th>Unit</th><th>Tanggal</th><th>Jam</th>
                            <th>MW</th><th>MVAR</th>
                            <th>Free Gov</th><th>AGC</th><th>LFC</th>
                        </tr></thead>
                        <tbody>`;
            items.forEach(e => {
                const name = getUnitName(e.RKUNIT_KODE, e.RUNIT_KODE);
                html += `<tr>
                    <td><strong>${name}</strong></td>
                    <td>${e.TUBEBAN_TGL || '-'}</td>
                    <td>${e.TUBEBAN_JAM || '-'}</td>
                    <td class="mm-mw-val">${e.TUBEBAN_MW !== null ? e.TUBEBAN_MW : '-'}</td>
                    <td>${e.TUBEBAN_MVAR !== null ? e.TUBEBAN_MVAR : '-'}</td>
                    <td>${e.FREEGOV || '-'}</td>
                    <td>${e.AGC || '-'}</td>
                    <td>${e.LFC || '-'}</td>
                </tr>`;
            });
            html += '</tbody></table></div></div>';
        }
        container.innerHTML = html;
    }

    // === RENDER STATUS ===
    function renderStatus(entries, container) {
        const grouped = groupBy(entries, 'RKUNIT_KODE');
        let html = '';
        for (const [rkunit, items] of Object.entries(grouped)) {
            html += `<div class="mm-plant-group">
                <div class="mm-plant-header" onclick="this.nextElementSibling.classList.toggle('collapsed')">
                    <div class="mm-plant-name"><i class="fas fa-industry"></i> ${getPlantName(rkunit)}</div>
                    <span class="mm-plant-badge">${items.length} unit</span>
                </div>
                <div class="mm-plant-body">
                    <table class="mm-table">
                        <thead><tr>
                            <th>Unit</th><th>Tanggal</th><th>Jam</th>
                            <th>Status</th><th>Penyebab</th>
                            <th>Derate (MW)</th><th>SDOF</th>
                            <th>Est. Selesai</th>
                        </tr></thead>
                        <tbody>`;
            items.forEach(e => {
                const name = getUnitName(e.RKUNIT_KODE, e.RUNIT_KODE);
                const estEnd = (e.TUKIN_EST_END_DATE && e.TUKIN_EST_END_TIME)
                    ? e.TUKIN_EST_END_DATE + ' ' + e.TUKIN_EST_END_TIME
                    : (e.TUKIN_EST_END_DATE || '-');
                html += `<tr>
                    <td><strong>${name}</strong></td>
                    <td>${e.TUKIN_TGL || '-'}</td>
                    <td>${e.TUKIN_JAM || '-'}</td>
                    <td>${makeStatusBadge(e.KODE_STATUS, e.DESC_STATUS)}</td>
                    <td>${e.CAUSE_DESC || '-'}</td>
                    <td>${e.TUKIN_DERATE !== null ? e.TUKIN_DERATE : '-'}</td>
                    <td>${e.SDOF || '-'}</td>
                    <td>${estEnd}</td>
                </tr>`;
            });
            html += '</tbody></table></div></div>';
        }
        container.innerHTML = html;
    }

    // === UPDATE SUMMARY CARDS ===
    function updateSummary(entries) {
        let counts = { total:0, operasi:0, standby:0, gangguan:0, har:0, mo:0 };
        entries.forEach(e => {
            counts.total++;
            const info = getStatusInfo(e.KODE_STATUS);
            if (counts[info.group] !== undefined) counts[info.group]++;
        });
        document.getElementById('sum-total').textContent = counts.total;
        document.getElementById('sum-operasi').textContent = counts.operasi;
        document.getElementById('sum-standby').textContent = counts.standby;
        document.getElementById('sum-gangguan').textContent = counts.gangguan;
        document.getElementById('sum-har').textContent = counts.har;
        document.getElementById('sum-mo').textContent = counts.mo;
    }

    // === UTILS ===
    function groupBy(arr, key) {
        const map = {};
        arr.forEach(item => {
            const k = item[key] || 'UNKNOWN';
            if (!map[k]) map[k] = [];
            map[k].push(item);
        });
        // Sort by plant name
        return Object.fromEntries(Object.entries(map).sort((a,b) => getPlantName(a[0]).localeCompare(getPlantName(b[0]))));
    }

    // === INIT ===
    document.addEventListener('DOMContentLoaded', fetchAllData);
})();
</script>
@endsection
