@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <!-- Load FontAwesome if not already globally loaded -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #f3f6f9;
            --card: #ffffff;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --primary: #0ea5e9;
            --green: #10b981;
            --red: #ef4444;
            --orange: #f59e0b;
            --blue: #3b82f6;
            --purple: #8b5cf6;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }

        body {
            background-color: var(--bg) !important;
            color: var(--text-main);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Top Bar override */
        .top-control-bar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            margin-bottom: 20px;
        }
        .top-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .top-title i { color: var(--primary); }
        .controls { display: flex; align-items: center; gap: 12px; }
        .date-input {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 8px 12px;
            color: var(--text-main);
            font-size: 0.85rem;
            outline: none;
            transition: border 0.2s;
        }
        .date-input:focus { border-color: var(--primary); }
        .btn-load {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);
            transition: all 0.2s;
        }
        .btn-load:hover { background: #0284c7; transform: translateY(-1px); }

        .dashboard-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: var(--card);
            border-radius: 12px;
            padding: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            border-bottom: 3px solid var(--border);
            display: flex;
            flex-direction: column;
        }
        .kpi-card.b-blue { border-bottom-color: var(--blue); }
        .kpi-card.b-green { border-bottom-color: var(--green); }
        .kpi-card.b-red { border-bottom-color: var(--red); }
        .kpi-card.b-orange { border-bottom-color: var(--orange); }
        .kpi-title { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .kpi-value { font-size: 1.8rem; font-weight: 800; color: var(--text-main); line-height: 1; }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        @media(max-width: 1200px) { .charts-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 768px) { .charts-grid { grid-template-columns: 1fr; } }
        
        .chart-card {
            background: var(--card);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 20px;
            display: flex;
            flex-direction: column;
            min-height: 320px;
        }
        .chart-header {
            font-size: 0.9rem; font-weight: 700; color: var(--text-main);
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }
        .chart-header i { color: var(--primary); }
        .chart-body {
            flex: 1; position: relative; width: 100%; min-height: 240px; display: flex; align-items: center; justify-content: center;
        }

        /* AI Insights Card */
        .ai-card {
            grid-column: span 3;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }
        @media(max-width: 1200px) { .ai-card { grid-column: span 2; } }
        @media(max-width: 768px) { .ai-card { grid-column: span 1; } }
        
        .ai-title {
            font-size: 1.1rem; font-weight: 800; color: #0369a1;
            margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
        }
        .ai-title i { color: #0284c7; }
        .ai-list {
            margin: 0; padding: 0; list-style: none;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px;
        }
        .ai-item {
            background: #ffffff; border: 1px solid #e0f2fe; border-radius: 8px; padding: 12px 16px;
            font-size: 0.85rem; color: #0f172a; display: flex; gap: 10px; align-items: flex-start;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            line-height: 1.4;
        }
        .ai-item i { margin-top: 2px; }
        .ai-normal { color: var(--green); }
        .ai-warn { color: var(--orange); }
        .ai-danger { color: var(--red); }
        .ai-info { color: var(--blue); }

        /* Data Table (Screener) */
        .data-card {
            background: var(--card);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .table-wrap { overflow-x: auto; }
        table.dt {
            width: 100%; border-collapse: collapse; font-size: 0.75rem; text-align: left;
        }
        table.dt th {
            background: #f8fafc; padding: 12px 16px;
            font-weight: 700; color: var(--text-muted);
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        table.dt td {
            padding: 10px 16px; border-bottom: 1px solid var(--border);
            white-space: nowrap; color: var(--text-main);
        }
        table.dt tbody tr:hover { background: #f8fafc; }
        .dt-group-row td {
            background: #f1f5f9;
            font-weight: 700;
            color: #334155;
            padding: 8px 16px;
            border-bottom: 1px solid var(--border);
        }

        /* Status Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 0.72rem;
        }
        .badge::before { content: ''; display: block; width: 6px; height: 6px; border-radius: 50%; }
        
        .bg-op { background: #dcfce7; color: #166534; } .bg-op::before { background: #10b981; }
        .bg-fo { background: #fee2e2; color: #991b1b; } .bg-fo::before { background: #ef4444; }
        .bg-sb { background: #dbeafe; color: #1e40af; } .bg-sb::before { background: #3b82f6; }
        .bg-mo { background: #fef3c7; color: #92400e; } .bg-mo::before { background: #f59e0b; }
        .bg-oth { background: #f3e8ff; color: #6b21a8; } .bg-oth::before { background: #a855f7; }
        .bg-def { background: #f1f5f9; color: #475569; } .bg-def::before { background: #94a3b8; }

        .text-green { color: var(--green); font-weight: 700; }
        .text-red { color: var(--red); font-weight: 700; }
        
        /* Loading state */
        .loading-msg { text-align: center; padding: 40px; color: var(--text-muted); font-weight: 500; }
        .spinner { width: 24px; height: 24px; border: 3px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 10px; }
        @keyframes spin { to { transform: rotate(360deg); } }

    </style>
@endsection

@section('content')
    <!-- Navbar overrides layout pushing content down, so we add padding here instead of body -->
    <div style="padding-top: 20px;"></div>

    <div class="top-control-bar">
        <div class="top-title">
            <i class="fas fa-chart-pie"></i> PERFORMANCE DASHBOARD MESIN
        </div>
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
            <div class="ai-title"><i class="fas fa-robot"></i> AI Diagnostic Insights</div>
            <ul class="ai-list" id="ai-list">
                <!-- Insights pushed via JS -->
            </ul>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Beban Bar Chart -->
            <div class="chart-card" style="grid-column: span 2;">
                <div class="chart-header"><i class="fas fa-bolt"></i> Total Beban (MW) per Pembangkit</div>
                <div class="chart-body" id="cb-beban">
                    <canvas id="chart-beban" style="display:none;"></canvas>
                </div>
            </div>

            <!-- Status Doughnut -->
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-heartbeat"></i> Distribusi Status Unit</div>
                <div class="chart-body" id="cb-status">
                    <canvas id="chart-status" style="display:none;"></canvas>
                </div>
            </div>

            <!-- Top 5 Mesin Bar Chart -->
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-arrow-up"></i> Top 5 Unit Beban Tertinggi (MW)</div>
                <div class="chart-body" id="cb-top5">
                    <canvas id="chart-top5" style="display:none;"></canvas>
                </div>
            </div>

            <!-- Penyebab Gangguan / MO -->
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-wrench"></i> Start Berhasil vs Gagal (Akumulasi)</div>
                <div class="chart-body" id="cb-starts">
                    <canvas id="chart-starts" style="display:none;"></canvas>
                </div>
            </div>

            <!-- Patrol Doughnut -->
            <div class="chart-card">
                <div class="chart-header"><i class="fas fa-clipboard-check"></i> Rasio Temuan Patrol</div>
                <div class="chart-body" id="cb-patrol">
                    <canvas id="chart-patrol" style="display:none;"></canvas>
                </div>
            </div>
        </div>

        <!-- Screener Super Table -->
        <div class="data-card">
            <div class="chart-header" style="margin: 0; padding: 16px 20px; border-bottom: 1px solid var(--border); background: #fdfdfd;">
                <i class="fas fa-table"></i> Data Lengkap Parameter Mesin
            </div>
            <div class="table-wrap">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Unit Pembangkit</th>
                            <th>Status Kinerja</th>
                            <th>Beban (MW)</th>
                            <th>MVAR</th>
                            <th>Freegov</th>
                            <th>AGC</th>
                            <th>LFC</th>
                            <th>Penyebab</th>
                            <th>Start B.</th>
                            <th>Start G.</th>
                            <th>WO Status</th>
                            <th>Derate</th>
                            <th>SDOF</th>
                            <th>Est. Selesai</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body">
                        <tr>
                            <td colspan="15" class="loading-msg"><div class="spinner"></div> Pilih tanggal dan tekan Load Data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
    // Setup Chart Defaults for white theme
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#f1f5f9';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Chart instances
    let cStatus=null, cBeban=null, cPatrol=null, cTop5=null, cStarts=null;

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

    // Date init
    const dateEl = document.getElementById('dash-date');
    dateEl.value = new Date().toISOString().split('T')[0];

    function showLoading(id){
        document.getElementById(id).innerHTML = `<div class="loading-msg"><div class="spinner"></div></div>`;
    }

    window.loadDashboard = async function(){
        const tgl = dateEl.value;
        if(!tgl) return alert('Pilih tanggal');

        ['cb-status','cb-beban','cb-patrol','cb-top5','cb-starts'].forEach(id => showLoading(id));
        document.getElementById('dt-body').innerHTML = `<tr><td colspan="15" class="loading-msg"><div class="spinner"></div> Memuat data parameter...</td></tr>`;
        document.getElementById('ai-section').style.display = 'none';

        const navUrl = 'http://192.168.1.203:8080/monday/navitas_status?tanggal=' + tgl;
        const patUrl = 'https://omamo.plnnusantarapower.co.id/api/transaksi_patrol/monday?apikey=rYqzzcNVg5qM3Cer4l2eEvk5JrsLM8Th&tanggal=' + tgl;

        let navData = [], patData = [];

        try {
            const r1 = await fetch(navUrl);
            if(r1.ok) navData = (await r1.json()).entry || [];
        } catch(e) { console.error('Navitas error', e); }

        try {
            const r2 = await fetch(patUrl);
            if(r2.ok) {
                const j2 = await r2.json();
                if(j2.status!==false) patData = j2.data || [];
            }
        } catch(e) { console.error('Patrol error', e); }

        processNavitas(navData);
        processPatrol(patData);
        generateAIInsights(navData, patData);
    };

    function processNavitas(entries){
        if(!entries.length){
            document.getElementById('dt-body').innerHTML = `<tr><td colspan="15" class="loading-msg">Data tidak tersedia untuk tanggal ini.</td></tr>`;
            return;
        }

        let c={tot:0,op:0,sb:0,fo:0,mo:0,mw:0, sGagal:0, sHasil:0};
        let pBeban = {};
        let screenMap = {}; 
        
        entries.forEach(e=>{
            if(!e.RKUNIT_KODE || !e.RUNIT_KODE) return;
            const rk = e.RKUNIT_KODE;
            const key = rk + '-' + e.RUNIT_KODE;
            
            if(!screenMap[key]) {
                screenMap[key] = { 
                    name: uName(rk, e.RUNIT_KODE), plant: pName(rk), rk: rk,
                    stat: 'UKN', stDesc: '-', cause: '-', derate: '-', sdof: '-', est: '-',
                    mw: null, mvar: '-', freegov: '-', agc: '-', lfc: '-',
                    sBerhasil: '-', sGagal: '-', wo: '-'
                };
            }
            // Status data
            if(e.KODE_STATUS !== undefined || e.TUKIN_TGL !== undefined){
                screenMap[key].stat = e.KODE_STATUS || 'UKN';
                screenMap[key].stDesc = e.DESC_STATUS || '-';
                screenMap[key].cause = e.CAUSE_DESC || '-';
                screenMap[key].derate = e.TUKIN_DERATE != null ? e.TUKIN_DERATE : '-';
                screenMap[key].sdof = e.SDOF || '0';
                screenMap[key].sBerhasil = e.START_BERHASIL != null ? e.START_BERHASIL : '-';
                screenMap[key].sGagal = e.START_GAGAL != null ? e.START_GAGAL : '-';
                screenMap[key].wo = e.WORK_ORDER || '-';
                screenMap[key].est = e.TUKIN_EST_END_DATE ? (e.TUKIN_EST_END_DATE + (e.TUKIN_EST_END_TIME?' '+e.TUKIN_EST_END_TIME:'')) : '-';
            }
            // Beban data
            if(e.TUBEBAN_MW !== undefined || e.TUBEBAN_TGL !== undefined){
                screenMap[key].mw = e.TUBEBAN_MW;
                screenMap[key].mvar = e.TUBEBAN_MVAR != null ? e.TUBEBAN_MVAR : '-';
                screenMap[key].freegov = e.FREEGOV || '-';
                screenMap[key].agc = e.AGC || '-';
                screenMap[key].lfc = e.LFC || '-';
            }
        });

        let gScreen = {};
        Object.values(screenMap).forEach(u => {
            if(!gScreen[u.rk]) gScreen[u.rk] = [];
            gScreen[u.rk].push(u);
            
            c.tot++;
            let w = parseFloat(u.mw) || 0;
            c.mw += w;
            pBeban[u.rk] = (pBeban[u.rk] || 0) + w;

            const st = ST[u.stat]?.c;
            if(st==='op') c.op++; else if(st==='sb') c.sb++; else if(st==='fo') c.fo++; else if(st==='mo') c.mo++;

            c.sHasil += parseInt(u.sBerhasil) || 0;
            c.sGagal += parseInt(u.sGagal) || 0;
        });

        // KPI
        document.getElementById('k-total').textContent = c.tot;
        document.getElementById('k-mw').textContent = c.mw.toFixed(2);
        document.getElementById('k-op').textContent = c.op;
        document.getElementById('k-sb').textContent = c.sb;
        document.getElementById('k-fo').textContent = c.fo;
        document.getElementById('k-mo').textContent = c.mo;

        // TABLE
        let dtHtml = '';
        Object.keys(gScreen).sort((a,b)=>pName(a).localeCompare(pName(b))).forEach(rk => {
            const list = gScreen[rk];
            dtHtml += `<tr class="dt-group-row"><td colspan="15"><i class="fas fa-industry"></i> ${pName(rk)} (${list.length} Unit)</td></tr>`;
            list.sort((a,b)=>a.name.localeCompare(b.name)).forEach(u => {
                const bCls = sCls(u.stat);
                const bg = `<span class="badge ${bCls}">${u.stDesc || u.stat}</span>`;
                const mwv = u.mw !== null ? `<span class="text-green">${parseFloat(u.mw).toFixed(2)}</span>` : '-';
                const foh = u.cause && u.cause !== '-' ? `<span class="text-red">${u.cause}</span>` : '-';
                
                dtHtml += `<tr>
                    <td style="font-weight:600;">${u.name}</td>
                    <td>${bg}</td>
                    <td>${mwv}</td>
                    <td>${u.mvar}</td>
                    <td>${u.freegov}</td>
                    <td>${u.agc}</td>
                    <td>${u.lfc}</td>
                    <td>${foh}</td>
                    <td>${u.sBerhasil}</td>
                    <td class="${parseInt(u.sGagal)>0?'text-red':''}">${u.sGagal}</td>
                    <td>${u.wo}</td>
                    <td class="text-orange">${u.derate !== '-' ? u.derate : '-'}</td>
                    <td>${u.sdof}</td>
                    <td>${u.est}</td>
                </tr>`;
            });
        });
        document.getElementById('dt-body').innerHTML = dtHtml;

        // DRAW STATUS DOUGHNUT
        document.getElementById('cb-status').innerHTML = '<canvas id="chart-status"></canvas>';
        cStatus = new Chart(document.getElementById('chart-status'), {
            type: 'doughnut',
            data: {
                labels: ['Operasi', 'Standby', 'Gangguan', 'Pemeliharaan'],
                datasets: [{ data: [c.op, c.sb, c.fo, c.mo], backgroundColor: [colors.op, colors.sb, colors.fo, colors.mo], borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });

        // DRAW BEBAN BAR
        document.getElementById('cb-beban').innerHTML = '<canvas id="chart-beban"></canvas>';
        const pKeys = Object.keys(pBeban).sort((a,b)=>pBeban[b]-pBeban[a]);
        cBeban = new Chart(document.getElementById('chart-beban'), {
            type: 'bar',
            data: {
                labels: pKeys.map(r=>pName(r)),
                datasets: [{ label:' MW', data: pKeys.map(r=>pBeban[r]), backgroundColor: colors.op, borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } } }
            }
        });

        // DRAW TOP 5 MESIN (Horizontal JS)
        document.getElementById('cb-top5').innerHTML = '<canvas id="chart-top5"></canvas>';
        const sortedU = Object.values(screenMap).filter(u=>parseFloat(u.mw)>0).sort((a,b)=>parseFloat(b.mw)-parseFloat(a.mw)).slice(0,5);
        cTop5 = new Chart(document.getElementById('chart-top5'), {
            type: 'bar',
            data: {
                labels: sortedU.map(u=>u.name),
                datasets: [{ label:' MW', data: sortedU.map(u=>parseFloat(u.mw)), backgroundColor: '#0ea5e9', borderRadius: 4 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } } } }
        });

        // DRAW STARTS vs FAILS
        document.getElementById('cb-starts').innerHTML = '<canvas id="chart-starts"></canvas>';
        cStarts = new Chart(document.getElementById('chart-starts'), {
            type: 'pie',
            data: {
                labels: ['Start Berhasil', 'Start Gagal'],
                datasets: [{ data: [c.sHasil, c.sGagal], backgroundColor: [colors.op, colors.fo], borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    function processPatrol(items){
        if(!items.length){
            document.getElementById('cb-patrol').innerHTML = '<div class="loading-msg">Tidak ada data patroli</div>';
            return;
        }

        let al=0, nm=0;
        items.forEach(i => { if(i.status==='ALARM') al++; else nm++; });
        document.getElementById('k-alarm').textContent = al;
        document.getElementById('k-normal').textContent = nm;

        document.getElementById('cb-patrol').innerHTML = '<canvas id="chart-patrol"></canvas>';
        cPatrol = new Chart(document.getElementById('chart-patrol'), {
            type: 'doughnut',
            data: {
                labels: ['Alarm', 'Normal'],
                datasets: [{ data: [al, nm], backgroundColor: [colors.patAl, colors.patNm], borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });
    }

    function generateAIInsights(navData, patData) {
        document.getElementById('ai-section').style.display = 'block';
        const list = document.getElementById('ai-list');
        list.innerHTML = '';

        if(!navData.length) {
            list.innerHTML = '<li class="ai-item ai-warn"><i class="fas fa-exclamation-triangle"></i> Data mesin belum tersedia untuk ditarik insight.</li>';
            return;
        }

        let fohCount = 0, fohNames = [], totalMW = 0, topUnit = null;
        let alarms = patData.filter(i => i.status === 'ALARM');

        navData.forEach(e => {
            if(e.KODE_STATUS === 'FOH') {
                fohCount++;
                if(fohNames.length < 3) fohNames.push(uName(e.RKUNIT_KODE, e.RUNIT_KODE));
            }
            let mw = parseFloat(e.TUBEBAN_MW) || 0;
            totalMW += mw;
            if(mw > 0 && (!topUnit || mw > parseFloat(topUnit.TUBEBAN_MW))) topUnit = e;
        });

        // Insight 1: Peak Loader
        if(topUnit) {
            list.innerHTML += `<li class="ai-item ai-info"><i class="fas fa-arrow-trend-up"></i> Unit tulang punggung saat ini adalah <strong>${uName(topUnit.RKUNIT_KODE, topUnit.RUNIT_KODE)}</strong> dengan suppai beban tertinggi mencapai <strong>${parseFloat(topUnit.TUBEBAN_MW).toFixed(2)} MW</strong>.</li>`;
        } else {
            list.innerHTML += `<li class="ai-item ai-warn"><i class="fas fa-power-off"></i> Menunjukkan indikasi black-out atau seluruh unit sedang tidak memikul beban.</li>`;
        }

        // Insight 2: Breakdown status
        if(fohCount > 0) {
            let n = fohNames.join(', ') + (fohCount > 3 ? ` dan ${fohCount-3} lainnya` : '');
            list.innerHTML += `<li class="ai-item ai-danger"><i class="fas fa-exclamation-circle"></i> Terdapat <strong>${fohCount} mesin mengalami gangguan (FOH)</strong> hari ini, di antaranya: ${n}. Perhatian khusus diperlukan pada WO perbaikan.</li>`;
        } else {
            list.innerHTML += `<li class="ai-item ai-normal"><i class="fas fa-check-circle"></i> Sangat baik: <strong>Tidak ada mesin yang FOH</strong>. Ketersediaan unit sedang optimal.</li>`;
        }

        // Insight 3: Patrol Alarm
        if(alarms.length > 0) {
            let uniqAreas = [...new Set(alarms.map(a => a.cabang))];
            list.innerHTML += `<li class="ai-item ai-warn"><i class="fas fa-bell"></i> Patrol (OMAMO) mendeteksi <strong>${alarms.length} parameter menyimpang (ALARM)</strong>. Prioritas pengecekan pada pembangkit: ${uniqAreas.join(', ')}.</li>`;
        } else {
            list.innerHTML += `<li class="ai-item ai-normal"><i class="fas fa-shield-alt"></i> Data Patrol OMAMO menunjukkan <strong>tidak ada anomali (0 ALARM)</strong> selama shift berjalan. Parameter dalam keadaan aman.</li>`;
        }

        // Insight 4: Operational capacity
        list.innerHTML += `<li class="ai-item ai-info"><i class="fas fa-industry"></i> Total energi yang dibangkitkan sistem mencapai <strong>${totalMW.toFixed(2)} MW</strong>. Status ini mencerminkan kapasitas suplai riil UP Kendari hari ini.</li>`;
    }

    // Run on boot
    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
@endsection
