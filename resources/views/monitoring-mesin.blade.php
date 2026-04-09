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

        /* AI Insights Card */
        .ai-card { grid-column: span 3; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 12px; padding: 20px; box-shadow: var(--shadow); margin-bottom: 24px; }
        @media(max-width: 1200px) { .ai-card { grid-column: span 2; } } @media(max-width: 768px) { .ai-card { grid-column: span 1; } }
        .ai-title { font-size: 1.1rem; font-weight: 800; color: #0369a1; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        .ai-list { margin: 0; padding: 0; list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px; }
        .ai-item { background: #ffffff; border: 1px solid #e0f2fe; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; color: #0f172a; display: flex; gap: 10px; align-items: flex-start; box-shadow: 0 1px 2px rgba(0,0,0,0.02); line-height: 1.4; }
        .ai-normal { color: var(--green); } .ai-warn { color: var(--orange); } .ai-danger { color: var(--red); } .ai-info { color: var(--blue); }

        /* Data Tables & Toolbar */
        .data-card { background: var(--card); border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; display: flex; flex-direction: column; margin-bottom: 24px; }
        .toolbar { padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; background: #fdfdfd; border-bottom: 1px solid var(--border); gap: 10px; flex-wrap: wrap; }
        .search-box { position: relative; width: 260px; }
        .search-box i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem; }
        .search-input { width: 100%; padding: 8px 10px 8px 30px; border: 1px solid var(--border); border-radius: 6px; font-size: 0.8rem; outline: none; transition: border 0.2s; }
        .search-input:focus { border-color: var(--primary); }
        .pagination { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; }
        .page-btn { padding: 4px 10px; border: 1px solid var(--border); background: #fff; border-radius: 4px; cursor: pointer; color: var(--text-main); }
        .page-btn:hover:not(:disabled) { background: #f1f5f9; }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .table-wrap { overflow-x: auto; max-height: 500px; }
        table.dt { width: 100%; border-collapse: collapse; font-size: 0.75rem; text-align: left; }
        table.dt th { background: #f8fafc; padding: 10px 14px; font-weight: 700; color: var(--text-muted); border-bottom: 2px solid var(--border); white-space: nowrap; position: sticky; top: 0; z-index: 10; }
        table.dt td { padding: 8px 14px; border-bottom: 1px solid var(--border); white-space: nowrap; color: var(--text-main); }
        table.dt tbody tr:hover td { background: #f1f5f9; }

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

        <!-- AI Insights -->
        <div class="ai-card" id="ai-section" style="display: none;">
            <div class="ai-title"><i class="fas fa-robot"></i> AI Smart Analysis & Insights</div>
            <ul class="ai-list" id="ai-list"></ul>
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
        </div>

        <!-- Navitas Table -->
        <div class="data-card">
            <div class="chart-header" style="margin: 0; padding: 16px 20px;">
                <i class="fas fa-cogs"></i> Tabel Master Data Parameter Navitas
            </div>
            <div class="toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-nav" class="search-input" placeholder="Cari plant, unit, status..." oninput="handleSearchNav()">
                </div>
                <div class="pagination" id="pag-nav">
                    <span id="info-nav">Data: 0</span>
                    <button class="page-btn" onclick="navitasPage(-1)" id="btn-prev-nav"><i class="fas fa-chevron-left"></i></button>
                    <span id="page-txt-nav">1 / 1</span>
                    <button class="page-btn" onclick="navitasPage(1)" id="btn-next-nav"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Cabang Pembangkit</th>
                            <th>Nama Unit</th>
                            <th>Status Kinerja</th>
                            <th>Beban (MW)</th>
                            <th>MVAR</th>
                            <th>Freegov</th>
                            <th>AGC</th>
                            <th>LFC</th>
                            <th>Penyebab</th>
                            <th>WO Status</th>
                            <th>Derate</th>
                            <th>SDOF</th>
                            <th>Est. Selesai</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body">
                        <tr><td colspan="13" class="loading-msg"><div class="spinner"></div> Menunggu data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Omamo Patrol Table -->
        <div class="data-card">
            <div class="chart-header" style="margin: 0; padding: 16px 20px;">
                <i class="fas fa-shield-alt"></i> Tabel Lengkap Historis Patrol Pekerja (OMAMO)
            </div>
            <div class="toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-patrol" class="search-input" placeholder="Cari instrumen, area, komentar..." oninput="handleSearchPatrol()">
                </div>
                <div class="pagination" id="pag-patrol">
                    <span id="info-patrol">Data: 0</span>
                    <button class="page-btn" onclick="patrolPage(-1)" id="btn-prev-patrol"><i class="fas fa-chevron-left"></i></button>
                    <span id="page-txt-patrol">1 / 1</span>
                    <button class="page-btn" onclick="patrolPage(1)" id="btn-next-patrol"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Cabang / Pembangkit</th>
                            <th>Unit</th>
                            <th>Area Cek</th>
                            <th>Instrumen</th>
                            <th>Aturan Info</th>
                            <th>Nilai</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Komentar Temuan</th>
                            <th>Jam Patroli</th>
                        </tr>
                    </thead>
                    <tbody id="dt-patrol-body">
                        <tr><td colspan="10" class="loading-msg"><div class="spinner"></div> Menunggu data OMAMO...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<!-- Chart.js untuk Donut/Pie (karena lightweight-charts tidak support Donut/Pie) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- TradingView Lightweight Charts -->
<script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>
<script>
(function(){
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e2e8f0';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Referensi Chart.js (Donut/Pie)
    let cStatus=null, cPatrol=null;
    // Referensi Lightweight Charts
    let lwBeban=null, lwTopAlarm=null, lwAreaAlarm=null;

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

    function createDummyTime(index) {
        const d = new Date(2024, 0, index + 1);
        return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    }
    function formatTimeLabel(time, map) {
        if(!time) return '';
        let key = '';
        if(typeof time === 'string') key = time;
        else if(time.year) key = time.year + '-' + String(time.month).padStart(2,'0') + '-' + String(time.day).padStart(2,'0');
        else if(typeof time === 'number') {
            const d = new Date(time*1000);
            key = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
        }
        return map[key] || key;
    }

    // Globals for Navitas Pagination
    window.masterNavitas = [];
    window.filteredNavitas = [];
    window.currNavPage = 1;
    const itemsPerPageNav = 15;

    // Globals for Patrol OMAMO Pagination
    window.masterPatrol = [];
    window.filteredPatrol = [];
    window.currPatPage = 1;
    const itemsPerPagePat = 15;

    function showLoading(id){ document.getElementById(id).innerHTML = `<div class="loading-msg"><div class="spinner"></div></div>`; }

    window.loadDashboard = async function(){
        const tgl = dateEl.value;
        if(!tgl) return alert('Pilih tanggal');

        ['cb-status','cb-beban','cb-patrol','cb-topalarm','cb-omamo-area'].forEach(id => showLoading(id));
        document.getElementById('dt-body').innerHTML = `<tr><td colspan="13" class="loading-msg"><div class="spinner"></div> Memuat Navitas...</td></tr>`;
        document.getElementById('dt-patrol-body').innerHTML = `<tr><td colspan="10" class="loading-msg"><div class="spinner"></div> Memuat OMAMO...</td></tr>`;
        document.getElementById('ai-section').style.display = 'none';

        const navStatusUrl = '/api/monitoring-mesin/navitas-status?tanggal=' + tgl;
        const navBebanUrl  = '/api/monitoring-mesin/navitas-beban?tanggal=' + tgl;
        const patUrl       = '/api/monitoring-mesin/patrol?tanggal=' + tgl;

        let statusData = [], bebanData = [], patData = [];

        try { const r1 = await fetch(navStatusUrl); if(r1.ok) statusData = (await r1.json()).entry || []; } catch(e){}
        try { const r2 = await fetch(navBebanUrl); if(r2.ok) bebanData = (await r2.json()).entry || []; } catch(e){}
        try { const r3 = await fetch(patUrl); if(r3.ok) { const j3 = await r3.json(); if(j3.status!==false) patData = j3.data || []; } } catch(e){}

        processNavitas(statusData, bebanData);
        processPatrolData(patData);
        generateAIInsights(patData, statusData);
    };

    function processNavitas(statusData, bebanData){
        if(!statusData.length && !bebanData.length){
            document.getElementById('dt-body').innerHTML = `<tr><td colspan="13" class="loading-msg">Data kinerja & beban kosong.</td></tr>`;
            ['k-total','k-mw','k-op','k-sb','k-fo','k-mo'].forEach(id=>document.getElementById(id).textContent='0');
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
        let statusCounts = {};

        window.masterNavitas = Object.values(map).map(u => {
            c.tot++; let w = parseFloat(u.mw) || 0; c.mw += w; pBeban[u.rk] = (pBeban[u.rk] || 0) + w;
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
        if(lwBeban) { lwBeban.remove(); lwBeban = null; }

        // Chart.js untuk Donut Status
        document.getElementById('cb-status').innerHTML = '<canvas id="chart-status" style="max-height:240px; display:block; margin:auto;"></canvas>';
        const sL=Object.keys(statusCounts), sD=sL.map(k=>statusCounts[k]), sC=sL.map(l=>colors[ST[l.split(' - ')[0]]?.c||'oth']);
        cStatus = new Chart(document.getElementById('chart-status'), { type: 'doughnut', data: { labels: sL, datasets: [{ data: sD, backgroundColor: sC, borderWidth: 2 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } } } });

        // Lightweight Chart (Tampilan Trading) untuk Beban MW per Pembangkit (Area Series)
        const cbBebanContainer = document.getElementById('cb-beban');
        cbBebanContainer.innerHTML = '';
        const pK = Object.keys(pBeban).sort((a,b)=>pBeban[b]-pBeban[a]);
        
        let catMapBeban = {};
        let dataBebanLW = [];
        pK.forEach((r, i) => {
            if(pBeban[r] > 0) {
                const tStr = createDummyTime(dataBebanLW.length);
                catMapBeban[tStr] = pName(r);
                dataBebanLW.push({ time: tStr, value: pBeban[r] });
            }
        });

        if(dataBebanLW.length > 0) {
            lwBeban = LightweightCharts.createChart(cbBebanContainer, {
                autoSize: true, // Otomatis menyesuaikan ukuran
                layout: { background: { type: 'solid', color: 'transparent' }, textColor: '#64748b', fontFamily: "'Inter', sans-serif" },
                grid: { vertLines: { visible: false }, horzLines: { color: '#e2e8f0', style: 1 } },
                localization: { timeFormatter: t => formatTimeLabel(t, catMapBeban) },
                timeScale: { tickMarkFormatter: t => formatTimeLabel(t, catMapBeban), fixLeftEdge: true, fixRightEdge: true },
                crosshair: { mode: LightweightCharts.CrosshairMode.Normal }
            });
            const areaSeries = lwBeban.addAreaSeries({ 
                title: 'Beban (MW)',
                lineColor: '#0ea5e9', topColor: 'rgba(14, 165, 233, 0.4)', bottomColor: 'rgba(14, 165, 233, 0.0)', lineWidth: 2 
            });
            areaSeries.setData(dataBebanLW);
            lwBeban.timeScale().fitContent();
        } else {
            cbBebanContainer.innerHTML = '<div class="loading-msg">Tidak ada beban mesin saat ini (0 MW).</div>';
        }

        // Init Data Table
        document.getElementById('search-nav').value = '';
        window.filteredNavitas = [...window.masterNavitas];
        navitasPage(0, true);
    }

    function processPatrolData(items){
        if(!items.length){
            document.getElementById('dt-patrol-body').innerHTML = '<tr><td colspan="10" class="loading-msg">Tidak ada temuan patroli OMAMO</td></tr>';
            document.getElementById('k-alarm').textContent = 0; document.getElementById('k-normal').textContent = 0;
            return;
        }

        let al=0, nm=0;
        let alarmByCabang = {}, alarmByArea = {};
        
        window.masterPatrol = items.map(e => {
            if(e.status==='ALARM') {
                al++;
                alarmByCabang[e.cabang] = (alarmByCabang[e.cabang]||0)+1;
                alarmByArea[e.area] = (alarmByArea[e.area]||0)+1;
            } else { nm++; }
            e._search = (e.cabang+' '+e.unit+' '+e.area+' '+e.instrument+' '+e.komentar+' '+e.status).toLowerCase();
            return e;
        }).sort((a,b)=>(a.status==='ALARM'?-1:1)); // Alarm on top always

        document.getElementById('k-alarm').textContent = al; document.getElementById('k-normal').textContent = nm;

        // Bersihkan Chart Lama
        if(cPatrol) cPatrol.destroy();
        if(lwTopAlarm) { lwTopAlarm.remove(); lwTopAlarm = null; }
        if(lwAreaAlarm) { lwAreaAlarm.remove(); lwAreaAlarm = null; }

        // Chart OMAMO Doughnut (Polar Area dipertahankan dari ChartJS)
        document.getElementById('cb-patrol').innerHTML = '<canvas id="chart-patrol" style="max-height:240px; display:block; margin:auto;"></canvas>';
        cPatrol = new Chart(document.getElementById('chart-patrol'), { type: 'polarArea', data: { labels: ['Alarm', 'Normal'], datasets: [{ data: [al, nm], backgroundColor: [colors.patAl, colors.patNm], borderWidth: 2 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } } });

        // Chart OMAMO Alarm Cabang - Lightweight Chart Histogram
        const cbTopAlarmContainer = document.getElementById('cb-topalarm');
        cbTopAlarmContainer.innerHTML = '';
        const cbLabels = Object.keys(alarmByCabang).sort((a,b)=>alarmByCabang[b]-alarmByCabang[a]).slice(0,5);
        
        let catMapTA = {};
        let taDataLW = [];
        cbLabels.forEach((l, i) => {
            const tStr = createDummyTime(i);
            catMapTA[tStr] = l;
            taDataLW.push({ time: tStr, value: alarmByCabang[l], color: colors.fo });
        });

        if(taDataLW.length > 0) {
            lwTopAlarm = LightweightCharts.createChart(cbTopAlarmContainer, {
                autoSize: true, // Otomatis menyesuaikan dengan container
                layout: { background: { type: 'solid', color: 'transparent' }, textColor: '#64748b', fontFamily: "'Inter', sans-serif" },
                grid: { vertLines: { visible: false }, horzLines: { color: '#e2e8f0', style: 1 } },
                localization: { timeFormatter: t => formatTimeLabel(t, catMapTA) },
                timeScale: { tickMarkFormatter: t => formatTimeLabel(t, catMapTA), fixLeftEdge: true, fixRightEdge: true },
                crosshair: { mode: LightweightCharts.CrosshairMode.Normal }
            });
            const taSeries = lwTopAlarm.addHistogramSeries({ title: 'Kasus Alarm' });
            taSeries.setData(taDataLW);
            lwTopAlarm.timeScale().fitContent();
        } else {
            cbTopAlarmContainer.innerHTML = '<div class="loading-msg">Aman, tidak ada Alarm.</div>';
        }

        // Chart OMAMO Alarm Area - Lightweight Chart Histogram
        const cbAreaAlarmContainer = document.getElementById('cb-omamo-area');
        cbAreaAlarmContainer.innerHTML = '';
        const arLabels = Object.keys(alarmByArea).sort((a,b)=>alarmByArea[b]-alarmByArea[a]).slice(0,5);
        
        let catMapAA = {};
        let aaDataLW = [];
        arLabels.forEach((l, i) => {
            const tStr = createDummyTime(i);
            const title = l.length > 20 ? l.substring(0,20)+'..' : l;
            catMapAA[tStr] = title;
            aaDataLW.push({ time: tStr, value: alarmByArea[l], color: colors.orange });
        });

        if(aaDataLW.length > 0) {
            lwAreaAlarm = LightweightCharts.createChart(cbAreaAlarmContainer, {
                autoSize: true,
                layout: { background: { type: 'solid', color: 'transparent' }, textColor: '#64748b', fontFamily: "'Inter', sans-serif" },
                grid: { vertLines: { visible: false }, horzLines: { color: '#e2e8f0', style: 1 } },
                localization: { timeFormatter: t => formatTimeLabel(t, catMapAA) },
                timeScale: { tickMarkFormatter: t => formatTimeLabel(t, catMapAA), fixLeftEdge: true, fixRightEdge: true },
                crosshair: { mode: LightweightCharts.CrosshairMode.Normal }
            });
            const aaSeries = lwAreaAlarm.addHistogramSeries({ title: 'Alarm Lokasi' });
            aaSeries.setData(aaDataLW);
            lwAreaAlarm.timeScale().fitContent();
        } else {
            cbAreaAlarmContainer.innerHTML = '<div class="loading-msg">Aman, tidak ada temuan Alarm Area.</div>';
        }

        document.getElementById('search-patrol').value = '';
        window.filteredPatrol = [...window.masterPatrol];
        patrolPage(0, true);
    }

    /* ==== PAGINATION LOGIC NAVITAS ==== */
    window.handleSearchNav = function(){
        const v = document.getElementById('search-nav').value.toLowerCase();
        if(!v) window.filteredNavitas = [...window.masterNavitas];
        else window.filteredNavitas = window.masterNavitas.filter(u => u._search.includes(v));
        navitasPage(0, true);
    };

    window.navitasPage = function(step, reset=false){
        if(reset) window.currNavPage = 1;
        else window.currNavPage += step;
        
        const total = window.filteredNavitas.length;
        const totalPages = Math.ceil(total / itemsPerPageNav) || 1;
        if(window.currNavPage < 1) window.currNavPage = 1;
        if(window.currNavPage > totalPages) window.currNavPage = totalPages;

        document.getElementById('info-nav').textContent = `Total: ${total}`;
        document.getElementById('page-txt-nav').textContent = `${window.currNavPage} / ${totalPages}`;
        document.getElementById('btn-prev-nav').disabled = (window.currNavPage === 1);
        document.getElementById('btn-next-nav').disabled = (window.currNavPage === totalPages);

        const start = (window.currNavPage - 1) * itemsPerPageNav;
        const pageData = window.filteredNavitas.slice(start, start + itemsPerPageNav);
        
        let html = '';
        if(!pageData.length) html = '<tr><td colspan="13" style="text-align:center;padding:20px;color:var(--text-muted)">Pencarian tidak ditemukan</td></tr>';
        
        pageData.forEach(u => {
            const detailStatus = u.stat !== 'UKN' ? (u.stat + ' - ' + u.stDesc) : '-';
            const bg = `<span class="badge ${sCls(u.stat)}">${detailStatus}</span>`;
            const mwv = u.mw !== null ? `<span class="text-green">${parseFloat(u.mw).toFixed(2)}</span>` : '-';
            const foh = u.cause && u.cause !== '-' ? `<span class="text-red">${u.cause}</span>` : '-';
            html += `<tr>
                <td style="font-weight:600;color:var(--text-muted);">${u.plant}</td>
                <td style="font-weight:700;">${u.name}</td>
                <td>${bg}</td>
                <td>${mwv}</td>
                <td>${u.mvar}</td>
                <td>${u.freegov}</td>
                <td>${u.agc}</td>
                <td>${u.lfc}</td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis" title="${foh}">${foh}</td>
                <td title="${u.wo}">${u.wo !== '-' && u.wo.length > 15 ? u.wo.substring(0,15)+'...' : u.wo}</td>
                <td class="text-orange">${u.derate !== '-' ? u.derate : '-'}</td>
                <td>${u.sdof}</td>
                <td>${u.est}</td>
            </tr>`;
        });
        document.getElementById('dt-body').innerHTML = html;
    };

    /* ==== PAGINATION LOGIC PATROL ==== */
    window.handleSearchPatrol = function(){
        const v = document.getElementById('search-patrol').value.toLowerCase();
        if(!v) window.filteredPatrol = [...window.masterPatrol];
        else window.filteredPatrol = window.masterPatrol.filter(u => u._search.includes(v));
        patrolPage(0, true);
    };

    window.patrolPage = function(step, reset=false){
        if(reset) window.currPatPage = 1;
        else window.currPatPage += step;
        
        const total = window.filteredPatrol.length;
        const totalPages = Math.ceil(total / itemsPerPagePat) || 1;
        if(window.currPatPage < 1) window.currPatPage = 1;
        if(window.currPatPage > totalPages) window.currPatPage = totalPages;

        document.getElementById('info-patrol').textContent = `Total: ${total}`;
        document.getElementById('page-txt-patrol').textContent = `${window.currPatPage} / ${totalPages}`;
        document.getElementById('btn-prev-patrol').disabled = (window.currPatPage === 1);
        document.getElementById('btn-next-patrol').disabled = (window.currPatPage === totalPages);

        const start = (window.currPatPage - 1) * itemsPerPagePat;
        const pageData = window.filteredPatrol.slice(start, start + itemsPerPagePat);
        
        let html = '';
        if(!pageData.length) html = '<tr><td colspan="10" style="text-align:center;padding:20px;color:var(--text-muted)">Pencarian tidak ditemukan</td></tr>';
        
        pageData.forEach(e => {
            const bCls = e.status==='ALARM' ? 'alarm' : 'normal';
            html += `<tr>
                <td style="font-weight:600;color:var(--text-muted)">${e.cabang || '-'}</td>
                <td style="font-weight:700;">${e.unit || '-'}</td>
                <td title="${e.area}">${e.area && e.area.length>25 ? e.area.substring(0,25)+'...' : (e.area||'-')}</td>
                <td>${e.instrument || '-'}</td>
                <td title="${e.instrument_info}" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;">${e.instrument_info || '-'}</td>
                <td class="${e.status==='ALARM'?'text-red':'text-green'}">${e.nilai || '0'}</td>
                <td>${e.instrument_unit || '-'}</td>
                <td><span class="badge-patrol ${bCls}">${e.status}</span></td>
                <td>${e.komentar || '-'}</td>
                <td>${e.created_date ? e.created_date.substring(11,19) : '-'}</td>
            </tr>`;
        });
        document.getElementById('dt-patrol-body').innerHTML = html;
    };

    function generateAIInsights(patData, statusData) {
        document.getElementById('ai-section').style.display = 'block';
        const list = document.getElementById('ai-list');
        list.innerHTML = '';
        if(!statusData.length && !patData.length) return list.innerHTML = '<li>Data kosong</li>';

        let alarms = patData.filter(i => i.status === 'ALARM');
        let fohCount = statusData.filter(s => s.KODE_STATUS==='FOH').length;

        // Insight Navitas FOH
        if(fohCount > 0) list.innerHTML += `<li class="ai-item ai-danger"><i class="fas fa-exclamation-circle"></i> Terdapat <strong>${fohCount} mesin mengalami gangguan berat (FOH)</strong> hari ini pada seluruh area kendari.</li>`;
        else list.innerHTML += `<li class="ai-item ai-normal"><i class="fas fa-check-circle"></i> Sangat baik: <strong>Tidak ada mesin FOH (0 Kasus)</strong>. Seluruh permesinan dalam status siap / beroperasi.</li>`;

        // Insight OMAMO
        if(alarms.length > 0) {
            // Group alarms
            let byCabang = {}; alarms.forEach(a=>{ byCabang[a.cabang]=(byCabang[a.cabang]||0)+1; });
            const topCabang = Object.keys(byCabang).sort((a,b)=>byCabang[b]-byCabang[a])[0];
            list.innerHTML += `<li class="ai-item ai-warn"><i class="fas fa-bell"></i> Patrol mencatat <strong>${alarms.length} Laporan Alarm</strong> di seluruh cabang. Area yang paling kritis dan harus segera dievaluasi pimpinannya hari ini adalah <strong>${topCabang} (${byCabang[topCabang]} Alarm)</strong>.</li>`;
            
            let byInstr = {}; alarms.forEach(a=>{ byInstr[a.instrument]=(byInstr[a.instrument]||0)+1; });
            const topInstr = Object.keys(byInstr).sort((a,b)=>byInstr[b]-byInstr[a])[0];
            list.innerHTML += `<li class="ai-item ai-warn"><i class="fas fa-exclamation-triangle"></i> Instrumen penyebab alarm paling banyak saat patroli berfokus pada <strong>${topInstr}</strong>. Teknisi perlu mempertimbangkan investigasi preventif pada sensor/area ini.</li>`;
        } else {
            list.innerHTML += `<li class="ai-item ai-normal"><i class="fas fa-shield-alt"></i> Luar biasa: Laporan patroli pekerja OMAMO menunjukkan <strong>tidak ada instrumen yang menyimpang di luar standar (0 Alarm)</strong>.</li>`;
        }

        list.innerHTML += `<li class="ai-item ai-info"><i class="fas fa-lightbulb"></i> Gunakan kotak pencarian (search box) di setiap tabel untuk melakukan filter kilat terhadap parameter yang Anda inginkan.</li>`;
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
@endsection