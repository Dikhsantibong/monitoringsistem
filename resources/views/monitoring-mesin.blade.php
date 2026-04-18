@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #f3f6f9; --card: #ffffff; --border: #e2e8f0;
            --text-main: #1e293b; --text-muted: #64748b;
            --primary: #0ea5e9; --green: #10b981; --red: #ef4444; --orange: #f59e0b;
            --blue: #3b82f6; --purple: #8b5cf6;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        body { background-color: var(--bg) !important; color: var(--text-main); font-family: 'Inter', sans-serif; margin: 0; padding: 0; }

        /* Top Bar */
        .top-control-bar {
            background: var(--card); border-bottom: 1px solid var(--border);
            padding: 12px 24px; display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }
        .top-title { font-size: 1.1rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
        .top-title i { color: var(--primary); }
        .controls { display: flex; align-items: center; gap: 12px; }
        .date-input { border: 1px solid var(--border); border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; outline: none; }
        .btn-load { background: var(--primary); color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-load:hover { background: #0284c7; }

        .dashboard-container { max-width: 1600px; margin: 0 auto; padding: 0 20px 40px; }

        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .kpi-card { background: var(--card); border-radius: 12px; padding: 16px; box-shadow: var(--shadow); border: 1px solid var(--border); border-bottom: 3px solid var(--border); }
        .kpi-card.b-blue { border-bottom-color: var(--blue); } .kpi-card.b-green { border-bottom-color: var(--green); } .kpi-card.b-red { border-bottom-color: var(--red); } .kpi-card.b-orange { border-bottom-color: var(--orange); }
        .kpi-title { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
        .kpi-value { font-size: 1.6rem; font-weight: 800; color: var(--text-main); line-height: 1; }

        /* Charts Grid */
        .charts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        @media(max-width: 1200px) { .charts-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 768px) { .charts-grid { grid-template-columns: 1fr; } }
        .chart-card { background: var(--card); border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border); padding: 20px; display: flex; flex-direction: column; min-height: 320px; }
        .chart-header { font-size: 0.9rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
        .chart-header i { color: var(--primary); }
        .chart-body { flex: 1; position: relative; width: 100%; min-height: 240px; }
        .chart-body canvas { max-height: 100%; margin: auto; display: block; }

        /* AI Insights Card Graphical Layout */
        .ai-card { grid-column: span 3; background: #ffffff; border: 1px solid #bae6fd; border-left: 4px solid #0ea5e9; border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; display: flex; gap: 24px; flex-wrap: wrap; }
        @media(max-width: 1200px) { .ai-card { grid-column: span 2; } } @media(max-width: 768px) { .ai-card { grid-column: span 1; } }
        .ai-left { flex: 0 0 250px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid #e2e8f0; padding-right: 24px; }
        .ai-right { flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center; }
        
        .ai-title { font-size: 1.1rem; font-weight: 800; color: #0369a1; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        
        .health-ring { position: relative; width: 150px; height: 150px; }
        .health-score { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; color: #0f172a; line-height: 1; }
        .health-label { font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px; }
        
        .ai-metric-bar { width: 100%; background: #e2e8f0; height: 8px; border-radius: 4px; margin-top: 8px; overflow: hidden; }
        .ai-metric-fill { height: 100%; border-radius: 4px; transition: width 1s ease-out; }
        .ai-metric-fill.bg-green { background: var(--green); } .ai-metric-fill.bg-orange { background: var(--orange); } .ai-metric-fill.bg-red { background: var(--red); }

        .ai-insight-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 12px; transition: all 0.2s; }
        .ai-insight-box:hover { background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-color: #cbd5e1; }
        .ai-insight-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .ai-insight-title { font-weight: 700; font-size: 0.85rem; color: #1e293b; display: flex; align-items: center; gap: 8px;}
        .ai-insight-badge { font-size: 0.65rem; padding: 4px 10px; border-radius: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .bg-crit { background: #fee2e2; color: #991b1b; } .bg-warn { background: #fef3c7; color: #92400e; } .bg-safe { background: #dcfce7; color: #166534; }
        .ai-insight-text { font-size: 0.8rem; color: #475569; line-height: 1.5; }
        .text-blue { color: var(--blue); } .text-orange { color: var(--orange); }



        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 0.72rem; }
        .badge::before { content: ''; display: block; width: 6px; height: 6px; border-radius: 50%; }
        .bg-op { background: #dcfce7; color: #166534; } .bg-op::before { background: #10b981; }
        .bg-fo { background: #fee2e2; color: #991b1b; } .bg-fo::before { background: #ef4444; }
        .bg-sb { background: #dbeafe; color: #1e40af; } .bg-sb::before { background: #3b82f6; }
        .bg-mo { background: #fef3c7; color: #92400e; } .bg-mo::before { background: #f59e0b; }
        .bg-oth { background: #f3e8ff; color: #6b21a8; } .bg-oth::before { background: #a855f7; }
        .bg-def { background: #f1f5f9; color: #475569; } .bg-def::before { background: #94a3b8; }
        
        .badge-patrol { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 0.7rem; color: #fff; }
        .badge-patrol.alarm { background: var(--red); } .badge-patrol.normal { background: var(--green); }

        .text-green { color: var(--green); font-weight: 700; } .text-red { color: var(--red); font-weight: 700; }
        
        .loading-msg { text-align: center; padding: 40px; color: var(--text-muted); font-weight: 500; font-size: 0.85rem;}
        .spinner { width: 24px; height: 24px; border: 3px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 10px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
@endsection

@section('content')
    <div style="padding-top: 20px;"></div>

    <div class="top-control-bar">
        <div class="top-title"><i class="fas fa-chart-pie"></i> ENTERPRISE MONITORING DASHBOARD</div>
        <div class="controls">
            <input type="date" id="dash-date" class="date-input">
            <button class="btn-load" onclick="loadDashboard()"><i class="fas fa-sync-alt"></i> Load Data</button>
        </div>
    </div>

    <div class="dashboard-container">

        <!-- KPI Strip -->
        <div class="kpi-grid">
            <div class="kpi-card b-blue"><div class="kpi-title">Total Unit</div><div class="kpi-value" id="k-total">0</div></div>
            <div class="kpi-card b-green"><div class="kpi-title">Beban Total (MW)</div><div class="kpi-value" id="k-mw">0.00</div></div>
            <div class="kpi-card b-green"><div class="kpi-title">Operasi</div><div class="kpi-value" id="k-op">0</div></div>
            <div class="kpi-card b-blue"><div class="kpi-title">Standby</div><div class="kpi-value" id="k-sb">0</div></div>
            <div class="kpi-card b-red"><div class="kpi-title">Gangguan (FOH)</div><div class="kpi-value" id="k-fo">0</div></div>
            <div class="kpi-card b-orange"><div class="kpi-title">Pemeliharaan</div><div class="kpi-value" id="k-mo">0</div></div>
            <div class="kpi-card b-red"><div class="kpi-title">Patrol Alarm</div><div class="kpi-value" id="k-alarm">0</div></div>
            <div class="kpi-card b-green"><div class="kpi-title">Patrol Normal</div><div class="kpi-value" id="k-normal">0</div></div>
        </div>

        <!-- AI Insights Card (Graphical Layout) -->
        <div class="ai-card" id="ai-section" style="display: none;">
            <div class="ai-left">
                <div class="ai-title" style="margin-bottom: 20px; text-align: center;"><i class="fas fa-robot"></i> AI Health Index</div>
                <div class="health-ring">
                    <canvas id="health-canvas"></canvas>
                    <div class="health-score"><span id="ai-score-txt">0</span><span class="health-label">System Score</span></div>
                </div>
            </div>
            <div class="ai-right">
                <div class="ai-title" style="margin-bottom: 16px;"><i class="fas fa-brain"></i> AI Predictive Metrics & Analysis</div>
                <div id="ai-list-wrap"></div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <div class="chart-card" style="grid-column: span 2;">
                <div class="chart-header"><i class="fas fa-bolt"></i> Total Beban Aktual (MW) per Pembangkit</div>
                <div class="chart-body" id="cb-beban"><canvas id="chart-beban" style="display:none;"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-heartbeat"></i> Distribusi Status Unit Utama</div>
                <div class="chart-body" id="cb-status"><canvas id="chart-status" style="display:none;"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-exclamation-triangle"></i> Top 5 Plant/Cabang Alarm (OMAMO)</div>
                <div class="chart-body" id="cb-topalarm"><canvas id="chart-topalarm" style="display:none;"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-chart-bar"></i> Distribusi Alarm OMAMO Per Area (Top 5)</div>
                <div class="chart-body" id="cb-omamo-area"><canvas id="chart-omamo-area" style="display:none;"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-clipboard-check"></i> Rasio Keseluruhan Temuan Patrol</div>
                <div class="chart-body" id="cb-patrol"><canvas id="chart-patrol" style="display:none;"></canvas></div>
            </div>
            
            <!-- Ekstra Charts Tambahan -->
            <div class="chart-card" style="grid-column: span 2;">
                <div class="chart-header"><i class="fas fa-bolt"></i> Total MVAR Aktual per Pembangkit</div>
                <div class="chart-body" id="cb-mvar"><canvas id="chart-mvar" style="display:none;"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-microchip"></i> Unit dengan Freegov / AGC / LFC</div>
                <div class="chart-body" id="cb-nav-feat"><canvas id="chart-nav-feat" style="display:none;"></canvas></div>
            </div>
            <div class="chart-card" style="grid-column: span 3;">
                <div class="chart-header"><i class="fas fa-wrench"></i> Top 10 Instrumen Pemicu Alarm OMAMO (Akar Masalah)</div>
                <div class="chart-body" id="cb-instr-alarm"><canvas id="chart-instr-alarm" style="display:none;"></canvas></div>
            </div>

        </div>



    </div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e2e8f0';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Referensi Chart.js
    let cStatus=null, cPatrol=null, cNavFeat=null;
    let cBeban=null, cTopAlarm=null, cAreaAlarm=null, cMvar=null, cInstrAlarm=null;

    const RK = { 'KDN':'NII TANASA','KDR':'RONGI','KDB':'BAU-BAU','KDM':'MIKUASI','KDI':'WINNING','KDS':'SABILAMBO','KDW':'WANGI-WANGI','KDE':'EREKE','KDK':'KOLAKA','KDH':'RAHA','KDL':'LADUMPI','KDA':'LANIPA NIPA','KDP':'POASIA','KDU':'WUA-WUA','KDG':'LANGARA','Z':'COMMON UPDK' };
    const RU = {
        'KDA-02':'LANIPA #02','KDA-03':'LANIPA #03','KDA-04':'LANIPA #04','KDA-09':'LANIPA #09',
        'KDB-04':'BAU-BAU #04','KDB-05':'BAU-BAU #05','KDB-07':'BAU-BAU #07','KDB-08':'BAU-BAU #08','KDB-13':'BAU-BAU #13','KDB-16':'BAU-BAU #16','KDB-17':'BAU-BAU #17',
        'KDE-04':'EREKE #04','KDE-06':'EREKE #06','KDE-07':'EREKE #07','KDE-09':'EREKE #09','KDE-10':'EREKE #10','KDE-11':'EREKE #11','KDE-18':'EREKE #18',
        'KDG-08':'LANGARA #08','KDG-09':'LANGARA #15','KDG-10':'LANGARA #10','KDG-11':'LANGARA #11','KDG-12':'LANGARA #12','KDG-13':'LANGARA #13','KDG-14':'LANGARA #14',
        'KDH-04':'RAHA #04','KDH-05':'RAHA #05','KDH-06':'RAHA #06','KDH-07':'RAHA #07','KDH-08':'RAHA #08','KDH-09':'RAHA #09','KDH-10':'RAHA #10','KDH-11':'RAHA #11','KDH-12':'RAHA #12','KDH-13':'RAHA #13','KDH-14':'RAHA #14','KDH-15':'RAHA #15',
        'KDI-01':'WINNING #01','KDI-02':'WINNING #02',
        'KDK-03':'KOLAKA #03','KDK-04':'KOLAKA #04','KDK-05':'KOLAKA #05','KDK-07':'KOLAKA #07','KDK-08':'KOLAKA #08','KDK-09':'KOLAKA #09',
        'KDL-01':'LADUMPI #01','KDL-02':'LADUMPI #02','KDL-06':'LADUMPI #06',
        'KDM-01':'MIKUASI #01','KDN-01':'NII TANASA #01','KDN-02':'NII TANASA #02',
        'KDP-01':'POASIA #01','KDP-02':'POASIA #02','KDP-04':'POASIA #04','KDP-05':'POASIA #05','KDP-06':'POASIA #06','KDP-07':'POASIA #07','KDP-08':'POASIA #08',
        'KDR-01':'RONGI #01','KDR-02':'RONGI #02','KDS-01':'SABILAMBO #01','KDS-02':'SABILAMBO #02',
        'KDU-01':'WUA-WUA #01','KDU-02':'WUA-WUA #02','KDU-03':'WUA-WUA #03','KDU-04':'WUA-WUA #04','KDU-05':'WUA-WUA #05',
        'KDW-01':'WANGI² #01','KDW-02':'WANGI² #02','KDW-03':'WANGI² #03','KDW-04':'WANGI² #04','KDW-05':'WANGI² #05','KDW-07':'WANGI² #07','KDW-08':'WANGI² #08','KDW-09':'WANGI² #09','KDW-10':'WANGI² #10'
    };
    function uName(rk,ru){ return RU[rk+'-'+ru]||(RK[rk]||rk)+' #'+ru; }
    function pName(rk){ return RK[rk]||rk; }

    const ST={NOH:{c:'op'},FOH:{c:'fo'},MOH:{c:'mo'},POH:{c:'mo'},RSH:{c:'sb'},SOH:{c:'sb'},MOTH:{c:'oth'},MB:{c:'oth'}};
    function sCls(cd){return 'bg-'+(ST[cd]||{c:'def'}).c;}
    const colors = { op:'#10b981', fo:'#ef4444', sb:'#3b82f6', mo:'#f59e0b', oth:'#8b5cf6', patAl:'#ef4444', patNm:'#10b981' };

    const dateEl = document.getElementById('dash-date');
    dateEl.value = new Date().toISOString().split('T')[0];


    function showLoading(id){ document.getElementById(id).innerHTML = `<div class="loading-msg"><div class="spinner"></div></div>`; }

    window.loadDashboard = async function(){
        const tgl = dateEl.value;
        if(!tgl) return alert('Pilih tanggal');

        ['cb-status','cb-beban','cb-patrol','cb-topalarm','cb-omamo-area', 'cb-mvar', 'cb-nav-feat', 'cb-instr-alarm'].forEach(id => showLoading(id));
        document.getElementById('ai-section').style.display = 'none';

        const navStatusUrl = '/api/monitoring-mesin/navitas-status?tanggal=' + tgl;
        const navBebanUrl  = '/api/monitoring-mesin/navitas-beban?tanggal=' + tgl;
        const patUrl       = '/api/monitoring-mesin/patrol?tanggal=' + tgl;

        let statusData = [], bebanData = [], patData = [];

        // 1. Trigger all fetches in parallel
        const fStatus = fetch(navStatusUrl).then(r => r.ok ? r.json() : null).catch(() => null);
        const fBeban  = fetch(navBebanUrl).then(r => r.ok ? r.json() : null).catch(() => null);
        const fPatrol = fetch(patUrl).then(r => r.ok ? r.json() : null).catch(() => null);

        // 2. Process Navitas (Faster) as soon as ready
        Promise.all([fStatus, fBeban]).then(([s, b]) => {
            statusData = s ? (s.entry || (Array.isArray(s) ? s : [])) : [];
            bebanData = b ? (b.entry || (Array.isArray(b) ? b : [])) : [];
            processNavitas(statusData, bebanData);
            if (patData.length > 0 || patFetched) generateAIInsights(patData, statusData);
        });

        // 3. Process OMAMO (Slower) as soon as ready
        let patFetched = false;
        fPatrol.then(p => {
            patFetched = true;
            if (p) {
                if (Array.isArray(p)) patData = p;
                else if (p.status !== false) patData = p.data || [];
                else patData = [];
            } else {
                patData = [];
            }
            processPatrolData(patData);
            if (statusData.length > 0) generateAIInsights(patData, statusData);
        }).catch(e => {
            patFetched = true;
            console.error("OMAMO Fetch Error:", e);
        });
    };

    function processNavitas(statusData, bebanData){
        if(!statusData.length && !bebanData.length){
            ['k-total','k-mw','k-op','k-sb','k-fo','k-mo'].forEach(id=>document.getElementById(id).textContent='0');
            ['cb-status','cb-beban','cb-mvar','cb-nav-feat'].forEach(id => {
                document.getElementById(id).innerHTML = '<div class="loading-msg">Tidak ada data.</div>';
            });
            return;
        }

        let map = {};
        statusData.forEach(e => {
            if(!e.RKUNIT_KODE || !e.RUNIT_KODE) return;
            const rk = e.RKUNIT_KODE; const key = rk + '-' + e.RUNIT_KODE;
            if(!map[key]) map[key] = { name: uName(rk, e.RUNIT_KODE), plant: pName(rk), rk: rk, stat: 'UKN', stDesc: '-', cause: '-', derate: '-', sdof: '-', est: '-', mw: null, mvar: '-', freegov: '-', agc: '-', lfc: '-', wo: '-' };
            map[key].stat = e.KODE_STATUS || 'UKN'; map[key].stDesc = e.DESC_STATUS || '-'; map[key].cause = e.CAUSE_DESC || '-'; map[key].derate = e.TUKIN_DERATE != null ? e.TUKIN_DERATE : '-'; map[key].sdof = e.SDOF || '0'; map[key].wo = e.WORK_ORDER || '-'; map[key].est = e.TUKIN_EST_END_DATE ? (e.TUKIN_EST_END_DATE + (e.TUKIN_EST_END_TIME?' '+e.TUKIN_EST_END_TIME:'')) : '-';
        });

        bebanData.forEach(e => {
            if(!e.RKUNIT_KODE || !e.RUNIT_KODE) return;
            const rk = e.RKUNIT_KODE; const key = rk + '-' + e.RUNIT_KODE;
            if(!map[key]) map[key] = { name: uName(rk, e.RUNIT_KODE), plant: pName(rk), rk: rk, stat: 'UKN', stDesc: '-', cause: '-', derate: '-', sdof: '-', est: '-', mw: null, mvar: '-', freegov: '-', agc: '-', lfc: '-', wo: '-' };
            map[key].mw = e.TUBEBAN_MW !== null ? e.TUBEBAN_MW : null; map[key].mvar = e.TUBEBAN_MVAR != null ? e.TUBEBAN_MVAR : '-'; map[key].freegov = e.FREEGOV || '-'; map[key].agc = e.AGC || '-'; map[key].lfc = e.LFC || '-';
        });

        let c={tot:0,op:0,sb:0,fo:0,mo:0,mw:0};
        let pBeban = {};
        let pMvar = {};
        let featCount = { Freegov: 0, AGC: 0, LFC: 0 };
        let statusCounts = {};

        window.masterNavitas = Object.values(map).map(u => {
            c.tot++; let w = parseFloat(u.mw) || 0; c.mw += w; pBeban[u.rk] = (pBeban[u.rk] || 0) + w;
            
            let m = parseFloat(u.mvar); if(!isNaN(m)) pMvar[u.rk] = (pMvar[u.rk] || 0) + m;
            let fg = (u.freegov||'').toString().toUpperCase(); if(fg !== '-' && fg !== '0' && fg !== '' && fg !== 'FALSE') featCount.Freegov++;
            let ag = (u.agc||'').toString().toUpperCase(); if(ag !== '-' && ag !== '0' && ag !== '' && ag !== 'FALSE') featCount.AGC++;
            let lf = (u.lfc||'').toString().toUpperCase(); if(lf !== '-' && lf !== '0' && lf !== '' && lf !== 'FALSE') featCount.LFC++;

            const st = ST[u.stat]?.c; if(st==='op') c.op++; else if(st==='sb') c.sb++; else if(st==='fo') c.fo++; else if(st==='mo') c.mo++;
            const gK = u.stat + " - " + u.stDesc; statusCounts[gK] = (statusCounts[gK] || 0) + 1;
            
            // Search string indexer
            u._search = (u.name + ' ' + u.plant + ' ' + u.stat + ' ' + u.stDesc + ' ' + u.cause).toLowerCase();
            return u;
        }).sort((a,b)=>a.plant.localeCompare(b.plant) || a.name.localeCompare(b.name));

        document.getElementById('k-total').textContent = c.tot; document.getElementById('k-mw').textContent = c.mw.toFixed(2);
        document.getElementById('k-op').textContent = c.op; document.getElementById('k-sb').textContent = c.sb;
        document.getElementById('k-fo').textContent = c.fo; document.getElementById('k-mo').textContent = c.mo;

        // Pengecekan & Pembersihan Chart Lama
        if(cStatus) cStatus.destroy();
        if(cBeban) cBeban.destroy();

        // Chart.js untuk Donut Status
        document.getElementById('cb-status').innerHTML = '<canvas id="chart-status" style="max-height:240px; display:block; margin:auto;"></canvas>';
        const sL=Object.keys(statusCounts), sD=sL.map(k=>statusCounts[k]), sC=sL.map(l=>colors[ST[l.split(' - ')[0]]?.c||'oth']);
        cStatus = new Chart(document.getElementById('chart-status'), { type: 'doughnut', data: { labels: sL, datasets: [{ data: sD, backgroundColor: sC, borderWidth: 2 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } } } });

        // Chart.js untuk Beban MW per Pembangkit (Bar Chart Gradient/Solid)
        const cbBebanContainer = document.getElementById('cb-beban');
        cbBebanContainer.innerHTML = '';
        const pK = Object.keys(pBeban).sort((a,b)=>pBeban[b]-pBeban[a]);
        
        let labelsBeban = [];
        let dataBeban = [];
        pK.forEach((r, i) => {
            if(pBeban[r] > 0) {
                labelsBeban.push(pName(r));
                dataBeban.push(pBeban[r]);
            }
        });

        if(dataBeban.length > 0) {
            cbBebanContainer.innerHTML = '<canvas id="chart-beban" style="max-height:240px; width:100%; display:block; margin:auto;"></canvas>';
            cBeban = new Chart(document.getElementById('chart-beban'), {
                type: 'bar',
                data: {
                    labels: labelsBeban,
                    datasets: [{
                        label: 'Total Beban (MW)',
                        data: dataBeban,
                        backgroundColor: 'rgba(14, 165, 233, 0.8)',
                        hoverBackgroundColor: 'rgba(2, 132, 199, 1)',
                        borderColor: '#0ea5e9',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, grid: { color: '#e2e8f0' } }, x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } } },
                    plugins: { legend: { display: false } }
                }
            });
        } else {
            cbBebanContainer.innerHTML = '<div class="loading-msg">Tidak ada beban mesin saat ini (0 MW).</div>';
        }

        // --- NEW: Chart.js MVAR Aktual Per Pembangkit (Bar Chart) ---
        if(cMvar) cMvar.destroy();
        const cbMvarContainer = document.getElementById('cb-mvar');
        cbMvarContainer.innerHTML = '';
        const pKmvar = Object.keys(pMvar).sort((a,b)=>Math.abs(pMvar[b])-Math.abs(pMvar[a]));
        
        let labelsMvar = [];
        let dataMvar = [];
        let bgColorsMvar = [];
        pKmvar.forEach((r, i) => {
            labelsMvar.push(pName(r));
            dataMvar.push(pMvar[r]);
            bgColorsMvar.push(pMvar[r] < 0 ? 'rgba(239, 68, 68, 0.8)' : 'rgba(139, 92, 246, 0.8)');
        });

        if(dataMvar.length > 0) {
            cbMvarContainer.innerHTML = '<canvas id="chart-mvar" style="max-height:240px; width:100%; display:block; margin:auto;"></canvas>';
            cMvar = new Chart(document.getElementById('chart-mvar'), {
                type: 'bar',
                data: {
                    labels: labelsMvar,
                    datasets: [{
                        label: 'MVAR',
                        data: dataMvar,
                        backgroundColor: bgColorsMvar,
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, grid: { color: '#e2e8f0' } }, x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } } },
                    plugins: { legend: { display: false } }
                }
            });
        } else {
            cbMvarContainer.innerHTML = '<div class="loading-msg">Tidak ada data MVAR saat ini.</div>';
        }

        // --- NEW: Chart.js untuk FREEGOV / AGC / LFC (Pie) ---
        if(cNavFeat) cNavFeat.destroy();
        document.getElementById('cb-nav-feat').innerHTML = '<canvas id="chart-nav-feat" style="max-height:240px; display:block; margin:auto;"></canvas>';
        cNavFeat = new Chart(document.getElementById('chart-nav-feat'), { 
            type: 'pie', 
            data: { 
                labels: ['Freegov', 'AGC', 'LFC'], 
                datasets: [{ 
                    data: [featCount.Freegov, featCount.AGC, featCount.LFC], 
                    backgroundColor: [colors.sb, colors.op, colors.mo], 
                    borderWidth: 2 
                }] 
            }, 
            options: { 
                responsive: true, maintainAspectRatio: false, 
                plugins: { legend: { position: 'bottom' } } 
            } 
        });

    }

    function processPatrolData(items){
        if(!items.length){
            document.getElementById('k-alarm').textContent = 0; document.getElementById('k-normal').textContent = 0;
            ['cb-patrol','cb-topalarm','cb-omamo-area','cb-instr-alarm'].forEach(id => {
                document.getElementById(id).innerHTML = '<div class="loading-msg">Data tidak tersedia / Kosong.</div>';
            });
            return;
        }

        let al=0, nm=0;
        let alarmByCabang = {}, alarmByArea = {}, alarmByInstr = {};
        
        window.masterPatrol = items.map(e => {
            if(e.status==='ALARM') {
                al++;
                alarmByCabang[e.cabang] = (alarmByCabang[e.cabang]||0)+1;
                alarmByArea[e.area] = (alarmByArea[e.area]||0)+1;
                alarmByInstr[e.instrument] = (alarmByInstr[e.instrument]||0)+1;
            } else { nm++; }
            e._search = (e.cabang+' '+e.unit+' '+e.area+' '+e.instrument+' '+e.komentar+' '+e.status).toLowerCase();
            return e;
        }).sort((a,b)=>(a.status==='ALARM'?-1:1)); // Alarm on top always

        document.getElementById('k-alarm').textContent = al; document.getElementById('k-normal').textContent = nm;

        // Bersihkan Chart Lama
        if(cPatrol) cPatrol.destroy();
        if(cTopAlarm) cTopAlarm.destroy();
        if(cAreaAlarm) cAreaAlarm.destroy();
        if(cInstrAlarm) cInstrAlarm.destroy();

        // Chart OMAMO Doughnut (Polar Area dipertahankan dari ChartJS)
        document.getElementById('cb-patrol').innerHTML = '<canvas id="chart-patrol" style="max-height:240px; display:block; margin:auto;"></canvas>';
        cPatrol = new Chart(document.getElementById('chart-patrol'), { type: 'polarArea', data: { labels: ['Alarm', 'Normal'], datasets: [{ data: [al, nm], backgroundColor: [colors.patAl, colors.patNm], borderWidth: 2 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } } });

        // Chart OMAMO Alarm Cabang - Chart.js
        const cbTopAlarmContainer = document.getElementById('cb-topalarm');
        cbTopAlarmContainer.innerHTML = '';
        const cbLabels = Object.keys(alarmByCabang).sort((a,b)=>alarmByCabang[b]-alarmByCabang[a]).slice(0,5);
        
        if(cbLabels.length > 0) {
            cbTopAlarmContainer.innerHTML = '<canvas id="chart-topalarm" style="max-height:240px; width:100%; display:block; margin:auto;"></canvas>';
            cTopAlarm = new Chart(document.getElementById('chart-topalarm'), {
                type: 'bar',
                data: {
                    labels: cbLabels,
                    datasets: [{
                        label: 'Kasus Alarm',
                        data: cbLabels.map(l => alarmByCabang[l]),
                        backgroundColor: colors.fo,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: '#e2e8f0' } }, x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } } }
                }
            });
        } else {
            cbTopAlarmContainer.innerHTML = '<div class="loading-msg">Aman, tidak ada Alarm.</div>';
        }

        // Chart OMAMO Alarm Area - Chart.js Horizontal Bar
        const cbAreaAlarmContainer = document.getElementById('cb-omamo-area');
        cbAreaAlarmContainer.innerHTML = '';
        const arLabels = Object.keys(alarmByArea).sort((a,b)=>alarmByArea[b]-alarmByArea[a]).slice(0,5);
        
        if(arLabels.length > 0) {
            cbAreaAlarmContainer.innerHTML = '<canvas id="chart-omamo-area" style="max-height:240px; width:100%; display:block; margin:auto;"></canvas>';
            
            cAreaAlarm = new Chart(document.getElementById('chart-omamo-area'), {
                type: 'bar',
                data: {
                    labels: arLabels.map(l => l.length > 20 ? l.substring(0,20)+'..' : l),
                    datasets: [{
                        label: 'Alarm Lokasi',
                        data: arLabels.map(l => alarmByArea[l]),
                        backgroundColor: colors.orange,
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, grid: { color: '#e2e8f0' } }, y: { grid: { display: false } } }
                }
            });
        } else {
            cbAreaAlarmContainer.innerHTML = '<div class="loading-msg">Aman, tidak ada temuan Alarm Area.</div>';
        }

        // --- NEW: Top 10 Instrumen Alarm - Chart.js Horizontal Bar ---
        const cbInstrContainer = document.getElementById('cb-instr-alarm');
        cbInstrContainer.innerHTML = '';
        const inLabels = Object.keys(alarmByInstr).sort((a,b)=>alarmByInstr[b]-alarmByInstr[a]).slice(0, 10);
        
        if(inLabels.length > 0) {
            cbInstrContainer.innerHTML = '<canvas id="chart-instr-alarm" style="max-height:240px; width:100%; display:block; margin:auto;"></canvas>';
            
            cInstrAlarm = new Chart(document.getElementById('chart-instr-alarm'), {
                type: 'bar',
                data: {
                    labels: inLabels.map(l => l.length > 35 ? l.substring(0,35)+'..' : l),
                    datasets: [{
                        label: 'Total Freq Error',
                        data: inLabels.map(l => alarmByInstr[l]),
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, grid: { color: '#e2e8f0' } }, y: { grid: { display: false }, ticks: { font: { size: 10 } } } }
                }
            });
        } else {
            cbInstrContainer.innerHTML = '<div class="loading-msg">Aman, tidak ada temuan sensor bermasalah.</div>';
        }
    }

    function generateAIInsights(patData, statusData) {
        document.getElementById('ai-section').style.display = 'flex';
        const list = document.getElementById('ai-list-wrap');
        list.innerHTML = '';
        if(!statusData.length && !patData.length) {
            list.innerHTML = '<div class="loading-msg">Data kosong, AI tidak dapat menganalisa secara prediktif.</div>';
            return;
        }

        let totalUnits = statusData.length || 1;
        let fohCount = statusData.filter(s => s.KODE_STATUS === 'FOH').length;
        let derateCount = statusData.filter(s => s.TUKIN_DERATE && parseFloat(s.TUKIN_DERATE) > 0).length;
        
        let alarms = patData.filter(i => i.status === 'ALARM');
        let totalPatrol = patData.length || 1;

        // --- 1. Kalkulasi Kesehatan Sistem AI ---
        // Basis Skor 100, turun berdasarkan deviasi dan masalah di lapangan
        let fohPenalty = (fohCount / totalUnits) * 60; // FOH Penalty sangat berbobot
        let deratePenalty = (derateCount / totalUnits) * 20; 
        let alarmPenalty = (alarms.length / totalPatrol) * 40;
        
        let score = 100 - fohPenalty - deratePenalty - alarmPenalty;
        if(score < 0) score = 0; if(score > 100) score = 100;
        let colorScore = score >= 80 ? colors.op : (score >= 50 ? colors.mo : colors.fo);

        // Render Circular ChartJS untuk Score Gauge
        if(window.aiHealthChart) window.aiHealthChart.destroy();
        window.aiHealthChart = new Chart(document.getElementById('health-canvas'), {
            type: 'doughnut',
            data: { datasets: [{ data: [score, 100-score], backgroundColor: [colorScore, '#f1f5f9'], borderWidth: 0, borderRadius: 8 }] },
            options: { cutout: '82%', responsive: true, maintainAspectRatio: false, animation: { animateScale: true }, plugins: { tooltip: {enabled: false}, legend: {display:false} } }
        });
        document.getElementById('ai-score-txt').textContent = Math.round(score);
        document.getElementById('ai-score-txt').style.color = colorScore;

        // --- 2. Menyematkan Metric Bar ke Panel Kanan ---
        let htmlList = '';

        // Insight A: Indeks Keandalan Mesin Utama (Reliability)
        let fohRatio = (fohCount / totalUnits) * 100;
        let relState = fohCount > 0 ? 'Kritis (FOH)' : 'Optimal';
        let relBg = fohCount > 0 ? 'bg-crit' : 'bg-safe';
        let relFill = fohCount > 0 ? 'bg-red' : 'bg-green';
        htmlList += `<div class="ai-insight-box">
            <div class="ai-insight-top">
                <span class="ai-insight-title"><i class="fas fa-heartbeat text-blue"></i> Indeks Keandalan Mesin (Reliability Rate)</span>
                <span class="ai-insight-badge ${relBg}">${relState}</span>
            </div>
            <div class="ai-insight-text">Sistem AI mengidentifikasi adanya <strong>${fohCount} interupsi mesin utama (FOH)</strong> melawan total pasokan. Tingkat ketersediaan sistem jaringan diperkirakan <strong>${Math.round(100 - fohRatio)}%</strong> dari total ${totalUnits} pembangkit.</div>
            <div class="ai-metric-bar"><div class="ai-metric-fill ${relFill}" style="width: ${100 - fohRatio}%"></div></div>
        </div>`;

        // Insight B: Prediksi Deviasi Alarm Sensor OMAMO
        let patRatio = (alarms.length / totalPatrol) * 100;
        let patState = patRatio > 10 ? 'Risiko Kesalahan' : (patRatio > 0 ? 'Investigasi Rutin' : 'Integrasi Normal');
        let patBg = patRatio > 10 ? 'bg-crit' : (patRatio > 0 ? 'bg-warn' : 'bg-safe');
        let patFill = patRatio > 10 ? 'bg-red' : (patRatio > 0 ? 'bg-orange' : 'bg-green');
        htmlList += `<div class="ai-insight-box">
            <div class="ai-insight-top">
                <span class="ai-insight-title"><i class="fas fa-shield-alt text-orange"></i> Analisis Deviasi Kesalahan Instrumen (OMAMO)</span>
                <span class="ai-insight-badge ${patBg}">${patState}</span>
            </div>
            <div class="ai-insight-text">Mesin kalkulasi menemukan <strong>${alarms.length} persentase kerusakan titik sensor lokal</strong>. Rasio keparahan cacat komponen bernilai ${patRatio.toFixed(1)}%. Sangat direkomendasikan pengiriman teknisi pada puncak histogram sebelah kiri.</div>
            <div class="ai-metric-bar"><div class="ai-metric-fill ${patFill}" style="width: ${100 - patRatio}%"></div></div>
        </div>`;

        // Insight C: Kinerja Parsial (Derating)
        let derateBg = derateCount > 3 ? 'bg-crit' : (derateCount > 0 ? 'bg-warn' : 'bg-safe');
        let derateFill = derateCount > 3 ? 'bg-red' : (derateCount > 0 ? 'bg-orange' : 'bg-green');
        let derateRatio = (derateCount / totalUnits) * 100;
        htmlList += `<div class="ai-insight-box">
            <div class="ai-insight-top">
                <span class="ai-insight-title"><i class="fas fa-arrow-down" style="color: var(--red);"></i> Kerentanan Penurunan Daya (Derating)</span>
                <span class="ai-insight-badge ${derateBg}">${derateCount>3?'Penurunan Mayor':(derateCount>0?'Waspada Rantai Beban':'Kapasitas Murni')}</span>
            </div>
            <div class="ai-insight-text">Algoritma pencegahan AI mendeteksi <strong>${derateCount} unit mesin kehilangan daya output maksimalnya</strong>. Disarankan memeriksa siklus kalori batubara, filter intake, maupun sistem injeksi bahan bakar untuk menghindari eskalasi trip.</div>
            <div class="ai-metric-bar"><div class="ai-metric-fill ${derateFill}" style="width: ${100 - derateRatio}%"></div></div>
        </div>`;

        list.innerHTML = htmlList;
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
@endsection