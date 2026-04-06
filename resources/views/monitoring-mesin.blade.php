@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        body { padding-top: 0; background: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        .mm-page { max-width: 1300px; margin: 0 auto; padding: 20px 16px 40px; padding-top: 90px; }

        /* Header */
        .mm-header {
            background: #fff;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .mm-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mm-header h1 i { color: #0095B7; }
        .mm-header-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .mm-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.875rem;
            background: #fff;
            color: #333;
            outline: none;
        }
        .mm-input:focus { border-color: #0095B7; }
        .mm-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: #fff;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #333;
            transition: background 0.15s;
        }
        .mm-btn:hover { background: #f0f0f0; }
        .mm-btn-primary { background: #0095B7; color: #fff; border-color: #0095B7; }
        .mm-btn-primary:hover { background: #007a96; }
        .mm-status-msg {
            font-size: 0.8rem;
            color: #888;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .mm-status-msg .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #22c55e;
            animation: blink 1.5s infinite;
        }
        @keyframes blink { 50% { opacity: 0.3; } }

        /* Stats Row */
        .mm-stats {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .mm-stat {
            background: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            flex: 1;
            min-width: 100px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            text-align: center;
            border-left: 3px solid #e5e5e5;
        }
        .mm-stat-val { font-size: 1.5rem; font-weight: 700; line-height: 1; }
        .mm-stat-label { font-size: 0.7rem; color: #888; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 4px; }
        .mm-stat.s-total { border-left-color: #0095B7; }
        .mm-stat.s-total .mm-stat-val { color: #0095B7; }
        .mm-stat.s-op { border-left-color: #22c55e; }
        .mm-stat.s-op .mm-stat-val { color: #22c55e; }
        .mm-stat.s-sb { border-left-color: #3b82f6; }
        .mm-stat.s-sb .mm-stat-val { color: #3b82f6; }
        .mm-stat.s-fo { border-left-color: #ef4444; }
        .mm-stat.s-fo .mm-stat-val { color: #ef4444; }
        .mm-stat.s-mo { border-left-color: #f59e0b; }
        .mm-stat.s-mo .mm-stat-val { color: #f59e0b; }

        /* Table Container */
        .mm-table-wrap {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 16px;
        }
        .mm-table-title {
            padding: 14px 20px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1a1a1a;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mm-table-title i { color: #0095B7; font-size: 0.9rem; }
        .mm-table-scroll { overflow-x: auto; }

        /* Table */
        .mm-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }
        .mm-table th {
            background: #fafafa;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            color: #666;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
        }
        .mm-table td {
            padding: 9px 14px;
            border-bottom: 1px solid #f3f3f3;
            color: #333;
            white-space: nowrap;
        }
        .mm-table tbody tr:hover td { background: #f9fafb; }
        .mm-table tbody tr:last-child td { border-bottom: none; }

        /* Plant group header row */
        .mm-plant-row td {
            background: #f0f7fa;
            font-weight: 700;
            color: #0095B7;
            font-size: 0.82rem;
            padding: 8px 14px;
            border-bottom: 1px solid #d9eef4;
        }

        /* Status badge */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .badge-status .dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-noh { background: #ecfdf5; color: #166534; }
        .badge-noh .dot { background: #22c55e; }
        .badge-foh { background: #fef2f2; color: #991b1b; }
        .badge-foh .dot { background: #ef4444; }
        .badge-poh, .badge-moh { background: #fffbeb; color: #92400e; }
        .badge-poh .dot, .badge-moh .dot { background: #f59e0b; }
        .badge-rsh, .badge-soh { background: #eff6ff; color: #1e40af; }
        .badge-rsh .dot, .badge-soh .dot { background: #3b82f6; }
        .badge-moth, .badge-mb { background: #f5f3ff; color: #5b21b6; }
        .badge-moth .dot, .badge-mb .dot { background: #8b5cf6; }
        .badge-default { background: #f3f4f6; color: #555; }
        .badge-default .dot { background: #999; }

        /* MW highlight */
        .mw-val { font-weight: 700; color: #0095B7; }

        /* Loading / Error / Empty */
        .mm-msg {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 0.9rem;
        }
        .mm-msg i { font-size: 1.5rem; display: block; margin-bottom: 8px; }
        .mm-msg.error { color: #dc2626; background: #fef2f2; border-radius: 8px; margin: 12px 16px; }
        .mm-spinner {
            width: 28px; height: 28px;
            border: 3px solid #e5e5e5;
            border-top-color: #0095B7;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Responsive */
        @media (max-width: 768px) {
            .mm-page { padding: 12px 10px 30px; }
            .mm-header { padding: 14px 16px; }
            .mm-header h1 { font-size: 1.1rem; }
            .mm-stats { gap: 6px; }
            .mm-stat { padding: 10px 8px; min-width: 70px; }
            .mm-stat-val { font-size: 1.2rem; }
            .mm-table th, .mm-table td { padding: 7px 10px; }
        }
        @media (max-width: 480px) {
            .mm-stats { flex-wrap: wrap; }
            .mm-stat { flex: 0 0 calc(33.33% - 4px); }
        }
    </style>
@endsection

@section('content')
    @include('components.navbar')

    <div class="mm-page">
        <!-- Header -->
        <div class="mm-header">
            <h1><i class="fas fa-cogs"></i> Monitoring Mesin</h1>
            <div class="mm-header-controls">
                <input type="date" id="mm-date" class="mm-input" title="Pilih tanggal">
                <button class="mm-btn mm-btn-primary" onclick="loadData()">
                    <i class="fas fa-sync-alt"></i> Muat Data
                </button>
                <span class="mm-status-msg" id="mm-status-msg"></span>
            </div>
        </div>

        <!-- Stats -->
        <div class="mm-stats" id="mm-stats">
            <div class="mm-stat s-total"><div class="mm-stat-val" id="s-total">-</div><div class="mm-stat-label">Total</div></div>
            <div class="mm-stat s-op"><div class="mm-stat-val" id="s-op">-</div><div class="mm-stat-label">Operasi</div></div>
            <div class="mm-stat s-sb"><div class="mm-stat-val" id="s-sb">-</div><div class="mm-stat-label">Standby</div></div>
            <div class="mm-stat s-fo"><div class="mm-stat-val" id="s-fo">-</div><div class="mm-stat-label">Gangguan</div></div>
            <div class="mm-stat s-mo"><div class="mm-stat-val" id="s-mo">-</div><div class="mm-stat-label">Har / Lainnya</div></div>
        </div>

        <!-- Status Kinerja Table -->
        <div class="mm-table-wrap">
            <div class="mm-table-title"><i class="fas fa-heartbeat"></i> Status Kinerja Mesin</div>
            <div id="status-content">
                <div class="mm-msg"><div class="mm-spinner"></div>Pilih tanggal lalu klik "Muat Data"</div>
            </div>
        </div>

        <!-- Beban (Load) Table -->
        <div class="mm-table-wrap">
            <div class="mm-table-title"><i class="fas fa-bolt"></i> Data Beban (MW / MVAR)</div>
            <div id="beban-content">
                <div class="mm-msg"><div class="mm-spinner"></div>Pilih tanggal lalu klik "Muat Data"</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function() {
    const RKUNIT = {
        'KDN':'PLTU NII TANASA','KDR':'PLTM RONGI','KDB':'PLTD BAU-BAU','KDM':'PLTM MIKUASI',
        'KDI':'PLTM WINNING','KDS':'PLTM SABILAMBO','KDW':'PLTD WANGI-WANGI','KDE':'PLTD EREKE',
        'KDK':'PLTD KOLAKA','KDH':'PLTD RAHA','KDL':'PLTD LADUMPI','KDA':'PLTD LANIPA NIPA',
        'KDP':'PLTD POASIA','KDU':'PLTD WUA-WUA','KDG':'PLTD LANGARA','Z':'COMMON UPDK KENDARI'
    };
    const RUNIT = {
        'KDA-02':'LANIPA NIPA #02 (DEUTZ)','KDA-03':'LANIPA NIPA #03 (DEUTZ)','KDA-04':'LANIPA NIPA #04 (DEUTZ)','KDA-09':'LANIPA NIPA #09 (MAN)',
        'KDB-04':'BAU-BAU #04 (DAIHATSU)','KDB-05':'BAU-BAU #05 (DAIHATSU)','KDB-07':'BAU-BAU #07 (DEUTZ)','KDB-08':'BAU-BAU #08 (DEUTZ)',
        'KDB-13':'BAU-BAU #13 (DEUTZ)','KDB-16':'BAU-BAU #16 (CUMMINS)','KDB-17':'BAU-BAU #17 (CUMMINS)',
        'KDE-04':'EREKE #04 (DAIHATSU)','KDE-06':'EREKE #06 (DAIHATSU)','KDE-07':'EREKE #07 (DAIHATSU)',
        'KDE-09':'EREKE #09 (DAIHATSU)','KDE-10':'EREKE #10 (CUMMINS)','KDE-11':'EREKE #11 (CUMMINS)','KDE-18':'EREKE #18 (CUMMINS)',
        'KDG-08':'LANGARA #08 (MAN)','KDG-09':'LANGARA #15 (MITSUBISHI)','KDG-10':'LANGARA #10 (CATERPILLAR)',
        'KDG-11':'LANGARA #11 (CUMMINS)','KDG-12':'LANGARA #12 (CUMMINS)','KDG-13':'LANGARA #13 (MAN)','KDG-14':'LANGARA #14 (MITSUBISHI)',
        'KDH-04':'RAHA #04 (DAIHATSU)','KDH-05':'RAHA #05 (MIRRLEES)','KDH-06':'RAHA #06 (DEUTZ)',
        'KDH-07':'RAHA #07 (CUMMINS)','KDH-08':'RAHA #08 (CUMMINS)','KDH-09':'RAHA #09 (CUMMINS)',
        'KDH-10':'RAHA #10 (CUMMINS)','KDH-11':'RAHA #11 (CUMMINS)','KDH-12':'RAHA #12 (MITSUBISHI)',
        'KDH-13':'RAHA #13 (MITSUBISHI)','KDH-14':'RAHA #14 (MITSUBISHI)','KDH-15':'RAHA #15 (MITSUBISHI)',
        'KDI-01':'WINNING #01','KDI-02':'WINNING #02',
        'KDK-03':'KOLAKA #03 (DAIHATSU)','KDK-04':'KOLAKA #04 (DAIHATSU)','KDK-05':'KOLAKA #05 (DAIHATSU)',
        'KDK-07':'KOLAKA #07 (NIIGATA)','KDK-08':'KOLAKA #08 (MAK)','KDK-09':'KOLAKA #09 (MAK)',
        'KDL-01':'LADUMPI #01 (YANMAR)','KDL-02':'LADUMPI #02 (YANMAR)','KDL-06':'LADUMPI #06 (CUMMINS)',
        'KDM-01':'MIKUASI #01','KDN-01':'NII TANASA #01','KDN-02':'NII TANASA #02',
        'KDP-01':'POASIA #01 (MIRRLEES)','KDP-02':'POASIA #02 (MIRRLEES)','KDP-04':'POASIA #04 (MIRRLEES)',
        'KDP-05':'POASIA #05 (MIRRLEES)','KDP-06':'POASIA #06 (CUMMINS)','KDP-07':'POASIA #07 (CUMMINS)','KDP-08':'POASIA #08 (CUMMINS)',
        'KDR-01':'RONGI #01','KDR-02':'RONGI #02','KDS-01':'SABILAMBO #01','KDS-02':'SABILAMBO #02',
        'KDU-01':'WUA-WUA #01 (MAK)','KDU-02':'WUA-WUA #02 (MAK)','KDU-03':'WUA-WUA #03 (MAK)',
        'KDU-04':'WUA-WUA #04 (MAK)','KDU-05':'WUA-WUA #05 (MAK)',
        'KDW-01':'WANGI-WANGI #01 (DAIHATSU)','KDW-02':'WANGI-WANGI #02 (DAIHATSU)',
        'KDW-03':'WANGI-WANGI #03 (SWD)','KDW-04':'WANGI-WANGI #04 (SWD)','KDW-05':'WANGI-WANGI #05 (SWD)',
        'KDW-07':'WANGI-WANGI #07 (CUMMINS)','KDW-08':'WANGI-WANGI #08 (MITSUBISHI)',
        'KDW-09':'WANGI-WANGI #09 (MITSUBISHI)','KDW-10':'WANGI-WANGI #10 (MITSUBISHI)'
    };

    function unitName(rk, ru) { return RUNIT[rk+'-'+ru] || (RKUNIT[rk]||rk)+' #'+ru; }
    function plantName(rk) { return RKUNIT[rk] || rk; }

    // Status mapping
    const ST = {
        'NOH':{ cls:'noh', grp:'op' }, 'FOH':{ cls:'foh', grp:'fo' },
        'MOH':{ cls:'moh', grp:'mo' }, 'POH':{ cls:'poh', grp:'mo' },
        'RSH':{ cls:'rsh', grp:'sb' }, 'SOH':{ cls:'soh', grp:'sb' },
        'MOTH':{ cls:'moth', grp:'mo' }, 'MB':{ cls:'mb', grp:'mo' }
    };
    function statusBadge(code, desc) {
        const s = ST[code] || { cls:'default', grp:'mo' };
        return '<span class="badge-status badge-'+s.cls+'"><span class="dot"></span>'+(desc||code||'-')+'</span>';
    }
    function statusGroup(code) { return (ST[code]||{grp:'mo'}).grp; }

    // Date
    const dateEl = document.getElementById('mm-date');
    dateEl.value = new Date().toISOString().split('T')[0];

    // Group entries by RKUNIT_KODE
    function group(arr) {
        const m = {};
        arr.forEach(e => {
            const k = e.RKUNIT_KODE || '?';
            (m[k] = m[k] || []).push(e);
        });
        return Object.entries(m).sort((a,b) => plantName(a[0]).localeCompare(plantName(b[0])));
    }

    function setMsg(id, html) { document.getElementById(id).innerHTML = html; }
    function showLoading(id) { setMsg(id, '<div class="mm-msg"><div class="mm-spinner"></div>Memuat data...</div>'); }
    function showError(id, msg) { setMsg(id, '<div class="mm-msg error"><i class="fas fa-exclamation-circle"></i>'+msg+'</div>'); }
    function showEmpty(id) { setMsg(id, '<div class="mm-msg"><i class="fas fa-inbox"></i>Tidak ada data untuk tanggal ini.</div>'); }

    window.loadData = function() {
        const tgl = dateEl.value;
        if (!tgl) { alert('Pilih tanggal terlebih dahulu'); return; }

        showLoading('status-content');
        showLoading('beban-content');
        document.getElementById('mm-status-msg').innerHTML = '<span class="dot"></span> Memuat...';

        const url = '/api/monitoring-mesin/navitas-status?tanggal=' + tgl;

        fetch(url)
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                if (data.error) { throw new Error(data.error); }
                const entries = data.entry || [];
                if (!entries.length) {
                    showEmpty('status-content');
                    showEmpty('beban-content');
                    resetStats();
                    document.getElementById('mm-status-msg').innerHTML = 'Tidak ada data';
                    return;
                }

                // Separate: entries with TUKIN fields → status, entries with TUBEBAN fields → beban
                const statusEntries = entries.filter(e => e.KODE_STATUS !== undefined || e.TUKIN_TGL !== undefined);
                const bebanEntries = entries.filter(e => e.TUBEBAN_MW !== undefined || e.TUBEBAN_TGL !== undefined);

                if (statusEntries.length) renderStatus(statusEntries);
                else showEmpty('status-content');

                if (bebanEntries.length) renderBeban(bebanEntries);
                else showEmpty('beban-content');

                updateStats(statusEntries);
                document.getElementById('mm-status-msg').innerHTML =
                    '<span class="dot"></span> ' + entries.length + ' data · ' + new Date().toLocaleTimeString('id-ID');
            })
            .catch(err => {
                showError('status-content', 'Gagal memuat: ' + err.message);
                showError('beban-content', 'Gagal memuat: ' + err.message);
                document.getElementById('mm-status-msg').innerHTML = '<span style="color:#dc2626">⚠ Error</span>';
            });
    };

    function renderStatus(entries) {
        const groups = group(entries);
        let h = '<div class="mm-table-scroll"><table class="mm-table"><thead><tr>';
        h += '<th>No</th><th>Unit</th><th>Jam</th><th>Status</th><th>Penyebab</th><th>Derate</th><th>SDOF</th><th>Est. Selesai</th>';
        h += '</tr></thead><tbody>';
        let n = 0;
        groups.forEach(([rk, items]) => {
            h += '<tr class="mm-plant-row"><td colspan="8"><i class="fas fa-industry" style="margin-right:6px"></i>' + plantName(rk) + ' (' + items.length + ' unit)</td></tr>';
            items.forEach(e => {
                n++;
                const est = e.TUKIN_EST_END_DATE ? (e.TUKIN_EST_END_DATE + (e.TUKIN_EST_END_TIME ? ' '+e.TUKIN_EST_END_TIME : '')) : '-';
                h += '<tr>';
                h += '<td>'+n+'</td>';
                h += '<td><strong>'+unitName(e.RKUNIT_KODE, e.RUNIT_KODE)+'</strong></td>';
                h += '<td>'+(e.TUKIN_JAM||'-')+'</td>';
                h += '<td>'+statusBadge(e.KODE_STATUS, e.DESC_STATUS)+'</td>';
                h += '<td>'+(e.CAUSE_DESC||'-')+'</td>';
                h += '<td>'+(e.TUKIN_DERATE!=null?e.TUKIN_DERATE:'-')+'</td>';
                h += '<td>'+(e.SDOF||'-')+'</td>';
                h += '<td>'+est+'</td>';
                h += '</tr>';
            });
        });
        h += '</tbody></table></div>';
        setMsg('status-content', h);
    }

    function renderBeban(entries) {
        const groups = group(entries);
        let h = '<div class="mm-table-scroll"><table class="mm-table"><thead><tr>';
        h += '<th>No</th><th>Unit</th><th>Jam</th><th>MW</th><th>MVAR</th><th>Free Gov</th><th>AGC</th><th>LFC</th>';
        h += '</tr></thead><tbody>';
        let n = 0;
        groups.forEach(([rk, items]) => {
            h += '<tr class="mm-plant-row"><td colspan="8"><i class="fas fa-industry" style="margin-right:6px"></i>' + plantName(rk) + ' (' + items.length + ' unit)</td></tr>';
            items.forEach(e => {
                n++;
                h += '<tr>';
                h += '<td>'+n+'</td>';
                h += '<td><strong>'+unitName(e.RKUNIT_KODE, e.RUNIT_KODE)+'</strong></td>';
                h += '<td>'+(e.TUBEBAN_JAM||'-')+'</td>';
                h += '<td class="mw-val">'+(e.TUBEBAN_MW!=null?e.TUBEBAN_MW:'-')+'</td>';
                h += '<td>'+(e.TUBEBAN_MVAR!=null?e.TUBEBAN_MVAR:'-')+'</td>';
                h += '<td>'+(e.FREEGOV||'-')+'</td>';
                h += '<td>'+(e.AGC||'-')+'</td>';
                h += '<td>'+(e.LFC||'-')+'</td>';
                h += '</tr>';
            });
        });
        h += '</tbody></table></div>';
        setMsg('beban-content', h);
    }

    function updateStats(entries) {
        let c = { total:0, op:0, sb:0, fo:0, mo:0 };
        entries.forEach(e => {
            c.total++;
            const g = statusGroup(e.KODE_STATUS);
            if (c[g] !== undefined) c[g]++;
        });
        document.getElementById('s-total').textContent = c.total;
        document.getElementById('s-op').textContent = c.op;
        document.getElementById('s-sb').textContent = c.sb;
        document.getElementById('s-fo').textContent = c.fo;
        document.getElementById('s-mo').textContent = c.mo;
    }
    function resetStats() {
        ['s-total','s-op','s-sb','s-fo','s-mo'].forEach(id => document.getElementById(id).textContent = '-');
    }

    // Auto-load on page ready
    document.addEventListener('DOMContentLoaded', function() {
        loadData();
    });
})();
</script>
@endsection
