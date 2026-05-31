<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #edf2f7;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .kpi-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #f8fafc;
    }

    .kpi-title-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .kpi-code {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .kpi-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.4;
    }

    .level-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.125rem;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .level-1 { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .level-2 { background: #ffedd5; color: #f97316; border: 1px solid #fed7aa; }
    .level-3 { background: #fef3c7; color: #f59e0b; border: 1px solid #fde68a; }
    .level-4 { background: #e0f2fe; color: #0ea5e9; border: 1px solid #bae6fd; }
    .level-5 { background: #dcfce7; color: #22c55e; border: 1px solid #86efac; }

    .kpi-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .kpi-value-section {
        text-align: center;
        margin-bottom: 1rem;
    }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .kpi-unit {
        font-size: 1rem;
        color: #64748b;
        font-weight: 500;
    }

    .kpi-desc {
        text-align: center;
        font-size: 0.875rem;
        color: #475569;
        font-weight: 500;
        background: #f1f5f9;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        display: inline-block;
        margin: 0 auto;
    }
    
    .evaluation-text {
        font-size: 0.8125rem;
        color: #64748b;
        text-align: center;
        margin-top: 0.5rem;
        font-style: italic;
    }

    .kpi-footer {
        padding: 1rem 1.25rem;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .stat-label {
        font-size: 0.6875rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
    }

    .stat-val {
        font-size: 0.875rem;
        color: #334155;
        font-weight: 600;
    }

    .w-full { grid-column: span 2; }

    .kpi-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background: #1e293b;
        color: white;
        padding: 1rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        line-height: 1.6;
        width: 320px;
        max-width: 90vw;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s, transform 0.2s;
        z-index: 100;
        pointer-events: none;
    }

    .kpi-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1e293b;
    }

    .kpi-card:hover .kpi-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .tooltip-title {
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #f1f5f9;
        border-bottom: 1px solid #334155;
        padding-bottom: 0.5rem;
    }

    .tooltip-section {
        margin-bottom: 0.75rem;
    }

    .tooltip-section:last-child {
        margin-bottom: 0;
    }

    .tooltip-label {
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 0.25rem;
    }

    .tooltip-content {
        color: #94a3b8;
    }

    .tooltip-list {
        margin: 0.25rem 0;
        padding-left: 1.25rem;
        color: #94a3b8;
    }

    .tooltip-list li {
        margin-bottom: 0.25rem;
    }
</style>

<div class="mt-5 px-4 fade-in">
    <!-- Filter Section -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('kinerja.pemeliharaan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <input type="hidden" name="tab" value="kpi-tab"> 
            
            <div class="flex flex-col gap-1 w-full md:w-auto">
                <label for="start_date" class="text-xs font-semibold text-gray-600">Start Schedule</label>
                <input type="date" id="start_date" name="start_date" 
                       value="{{ $filterStartDate ?? date('Y-m-d', strtotime('-6 months')) }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex flex-col gap-1 w-full md:w-auto">
                <label for="end_date" class="text-xs font-semibold text-gray-600">End Schedule</label>
                <input type="date" id="end_date" name="end_date" 
                       value="{{ $filterEndDate ?? date('Y-m-d') }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700 transition h-[38px]">
                <i class="fas fa-filter mr-2"></i> Filter Date
            </button>
            
            @if(request('start_date') || request('end_date'))
                <a href="{{ route('kinerja.pemeliharaan') }}?tab=kpi-tab" class="text-gray-500 text-sm hover:text-gray-700 underline mb-2">Reset</a>
            @endif
        </form>
    </div>

    <div class="kpi-grid">
        
        <!-- I6.6 PM Compliance -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.6.1 – PM Compliance</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Tingkat kepatuhan pelaksanaan PM yang selesai sesuai jadwal (actual finish dalam rentang schedule start dan schedule finish).</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kriteria Pengukuran:</div>
                    <ul class="tooltip-list">
                        <li>Seluruh pekerjaan PM yang telah closed</li>
                        <li>Data: Actual completion date, Completion comment, Realisasi man-hour</li>
                        <li>Nilai = (PM tepat waktu / Total PM closed) × 100%</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>≤ 70% → Skor 1</li>
                        <li>> 70% → Skor 2</li>
                        <li>> 80% → Skor 3</li>
                        <li>> 90% → Skor 4</li>
                        <li>= 100% → Skor 5</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.6 - Maintenance Execution</span>
                    <h3 class="kpi-title">PM Compliance</h3>
                </div>
                <div class="level-badge level-{{ $pmCompliance['level'] }}">{{ $pmCompliance['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $pmCompliance['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $pmCompliance['description'] }}</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Compliant PM</span>
                    <span class="stat-val">{{ number_format($pmCompliance['compliant']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total PM Closed</span>
                    <span class="stat-val">{{ number_format($pmCompliance['total']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.7 WO Planned Backlog -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.7.2 – WO Planned Backlog</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Indikator untuk mengukur jumlah pekerjaan yang telah direncanakan dibandingkan dengan kapasitas tenaga kerja yang tersedia.</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Definisi:</div>
                    <ul class="tooltip-list">
                        <li><strong>Planned Work:</strong> Pekerjaan non-OH dalam identifikasi kebutuhan labor, material, tools, safety</li>
                        <li><strong>Ready Work:</strong> Pekerjaan siap dieksekusi (perencanaan selesai)</li>
                        <li><strong>Crew Capacity:</strong> Total labor hour tersedia per minggu</li>
                        <li><strong>Planned Backlog:</strong> (Man-hour Planned + Ready) / Crew Capacity</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>Tidak terukur / tidak ada data → Skor 1</li>
                        <li>Backlog ≥ 8 minggu → Skor 2</li>
                        <li>Backlog 6 – < 8 minggu → Skor 3</li>
                        <li>Backlog 4 – < 6 minggu → Skor 4</li>
                        <li>Backlog < 4 minggu → Skor 5</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.7 - Maintenance Planning</span>
                    <h3 class="kpi-title">WO Planned Backlog</h3>
                </div>
                <div class="level-badge level-{{ $plannedBacklog['level'] }}">{{ $plannedBacklog['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $plannedBacklog['weeks'] }}<span class="kpi-unit"> Weeks</span></div>
                    <div class="kpi-desc">{{ $plannedBacklog['description'] }}</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Total Manhours</span>
                    <span class="stat-val">{{ number_format($plannedBacklog['total_manhours']) }} hrs</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Planned + Ready</span>
                    <span class="stat-val">{{ number_format($plannedBacklog['planned_hours']) }} + {{ number_format($plannedBacklog['ready_hours']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.8 Schedule Compliance -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.8.1 – Schedule Compliance (WO Non Tactical)</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Tingkat kepatuhan penyelesaian pekerjaan Non Tactical terhadap jadwal yang telah ditetapkan.</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Jenis Pekerjaan:</div>
                    <ul class="tooltip-list">
                        <li>Corrective Maintenance (CR)</li>
                        <li>Emergency Maintenance (EM)</li>
                        <li>Inspection (EI)</li>
                        <li>Normal Maintenance (NM)</li>
                        <li>Shutdown/Service Function (SF)</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kriteria Pengukuran:</div>
                    <ul class="tooltip-list">
                        <li>Comply: selesai tepat waktu berdasarkan actual finish, schedule start, schedule finish</li>
                        <li>Data: Completion comment, Realisasi man-hour</li>
                        <li>Nilai = (Non Tactical tepat waktu / Total Non Tactical selesai) × 100%</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>0% – 30% → Skor 1</li>
                        <li>> 30% – ≤ 50% → Skor 2</li>
                        <li>> 50% – ≤ 70% → Skor 3</li>
                        <li>> 70% – ≤ 80% → Skor 4</li>
                        <li>> 80% → Skor 5</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.8 - Maintenance Scheduling</span>
                    <h3 class="kpi-title">Schedule Compliance (Non Tactical)</h3>
                </div>
                <div class="level-badge level-{{ $scheduleCompliance['level'] }}">{{ $scheduleCompliance['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $scheduleCompliance['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $scheduleCompliance['description'] }}</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Compliant WO</span>
                    <span class="stat-val">{{ number_format($scheduleCompliance['compliant']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Non-Tactical</span>
                    <span class="stat-val">{{ number_format($scheduleCompliance['total']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.9 Rework -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.9.2 – Jaminan Kualitas Hasil Pekerjaan (Rework)</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Mengukur kualitas hasil pekerjaan pemeliharaan berdasarkan jumlah pekerjaan yang harus diulang (rework) akibat kerusakan yang sama pada aset/peralatan yang sama dalam periode satu bulan.</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kriteria Pengukuran:</div>
                    <ul class="tooltip-list">
                        <li>Rasio WO berulang pada aset/peralatan yang sama</li>
                        <li>Kerusakan harus jenis yang sama dengan pekerjaan sebelumnya</li>
                        <li>Periode pengukuran: 1 bulan</li>
                        <li>Nilai = (WO berulang / Total WO CR + EM) × 100%</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Tujuan:</div>
                    <ul class="tooltip-list">
                        <li>Menilai efektivitas dan kualitas pekerjaan perbaikan</li>
                        <li>Mengurangi pekerjaan berulang akibat perbaikan tidak tuntas</li>
                        <li>Meningkatkan reliability aset dan kualitas maintenance</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>> 20% atau tidak terukur/tidak ada data → Skor 1</li>
                        <li>15% – < 20% → Skor 2</li>
                        <li>10% – < 15% → Skor 3</li>
                        <li>5% – < 10% → Skor 4</li>
                        <li>≤ 5% → Skor 5</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.9 - Maintenance Execution</span>
                    <h3 class="kpi-title">Jaminan Kualitas (Rework)</h3>
                </div>
                <div class="level-badge level-{{ $rework['level'] }}">{{ $rework['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $rework['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $rework['description'] }}</div>
                    <div class="evaluation-text">Persentase WO Non-Tactical berulang</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Rework Count</span>
                    <span class="stat-val">{{ number_format($rework['rework']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total CR/EM</span>
                    <span class="stat-val">{{ number_format($rework['total']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.10.1 Reactive Work -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.10.1 – Reactive Work</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Mengukur proporsi pekerjaan pemeliharaan yang bersifat reaktif (tidak direncanakan) dibandingkan dengan total pekerjaan pemeliharaan yang dilaksanakan.</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kriteria Pengukuran:</div>
                    <ul class="tooltip-list">
                        <li>Rasio WO Non Tactical terhadap total seluruh WO</li>
                        <li>Total WO = WO Tactical + WO Non Tactical</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">WO Tactical (closed):</div>
                    <ul class="tooltip-list">
                        <li>Preventive Maintenance (PM)</li>
                        <li>Predictive Maintenance (PdM)</li>
                        <li>Engineering Inspection (EI)</li>
                        <li>Operational Health (OH)</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">WO Non Tactical (issued):</div>
                    <ul class="tooltip-list">
                        <li>Corrective Maintenance (CR)</li>
                        <li>Emergency Maintenance (EM)</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Tujuan:</div>
                    <ul class="tooltip-list">
                        <li>Mengukur tingkat perencanaan maintenance</li>
                        <li>Menurunkan pekerjaan bersifat reaktif</li>
                        <li>Meningkatkan proporsi pekerjaan terencana</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>> 20% atau tidak terukur/tidak ada data → Skor 1</li>
                        <li>15% – < 20% → Skor 2</li>
                        <li>10% – < 15% → Skor 3</li>
                        <li>5% – < 10% → Skor 4 (+ tren penurunan vs semester sebelumnya)</li>
                        <li>≤ 5% → Skor 5 (+ tren penurunan vs semester sebelumnya)</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.10.1 - Maintenance Control</span>
                    <h3 class="kpi-title">Reactive Work</h3>
                </div>
                <div class="level-badge level-{{ $reactiveWork['level'] }}">{{ $reactiveWork['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $reactiveWork['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $reactiveWork['description'] }}</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Non-Tactical Terbit (CM/EM)</span>
                    <span class="stat-val">{{ number_format($reactiveWork['non_tactical']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Tactical Close (PM/PDM/EJ/OH)</span>
                    <span class="stat-val">{{ number_format($reactiveWork['tactical_closed']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.10.2 WR/SR Open/Queued -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.10.2 – Work Request / Service Request Status Open/Queued</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Mengukur jumlah WR/SR yang masih berstatus Open atau Queued melebihi batas waktu penyelesaian yang ditetapkan.</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kriteria Pengukuran:</div>
                    <ul class="tooltip-list">
                        <li>Jumlah WR/SR belum diproses/ditindaklanjuti dalam batas waktu</li>
                        <li>Pengukuran sejak tanggal WR/SR diterbitkan hingga evaluasi</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Standar Batas Waktu:</div>
                    <ul class="tooltip-list">
                        <li>Service Request Normal: ≥ 30 hari</li>
                        <li>Service Request Urgent: ≥ 7 hari</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Rumus Perhitungan:</div>
                    <div class="tooltip-content">(Jumlah SR Open/Queued (Normal + Urgent) ÷ Total SR) × 100%</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Tujuan:</div>
                    <ul class="tooltip-list">
                        <li>Memastikan permintaan layanan ditindaklanjuti tepat waktu</li>
                        <li>Mengurangi backlog permintaan layanan</li>
                        <li>Meningkatkan kualitas pelayanan dan respons</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>≥ 5% Open/Queued (Normal ≥ 30 hari, Urgent ≥ 7 hari) → Skor 1</li>
                        <li>< 5% Open/Queued (Normal ≥ 30 hari, Urgent ≥ 7 hari) → Skor 2</li>
                        <li>< 2% Open/Queued (Normal ≥ 30 hari, Urgent ≥ 7 hari) → Skor 3</li>
                        <li>< 1% Open/Queued (Normal ≥ 30 hari, Urgent ≥ 7 hari) → Skor 4</li>
                        <li>0% Open/Queued (Normal ≥ 30 hari, Urgent ≥ 7 hari) → Skor 5</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.10.2 - Maintenance Control</span>
                    <h3 class="kpi-title">WR/SR Open/Queued</h3>
                </div>
                <div class="level-badge level-{{ $wrSrOpen['level'] }}">{{ $wrSrOpen['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $wrSrOpen['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $wrSrOpen['description'] }}</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Overdue Tickets</span>
                    <span class="stat-val">{{ number_format($wrSrOpen['overdue']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total WR/SR</span>
                    <span class="stat-val">{{ number_format($wrSrOpen['total']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.10.3 WO Ageing -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.10.3 – WO Ageing</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Mengukur jumlah WO yang masih berstatus aktif (open) dan telah berumur lebih dari 365 hari, kecuali WO type OH (Operational Health).</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kriteria Pengukuran:</div>
                    <ul class="tooltip-list">
                        <li>Umur WO = WO Creation Date hingga Today's Date</li>
                        <li>Satuan pengukuran: jumlah hari</li>
                        <li>Berlaku untuk seluruh jenis maintenance selain OH</li>
                        <li>Fokus: WO masih berstatus open/aktif</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Rumus Perhitungan:</div>
                    <div class="tooltip-content">(Jumlah WO Open umur > 365 hari ÷ Total WO Status Open) × 100%</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Tujuan:</div>
                    <ul class="tooltip-list">
                        <li>Mengurangi WO tertunda jangka panjang</li>
                        <li>Memastikan penyelesaian backlog maintenance</li>
                        <li>Meningkatkan efektivitas pengelolaan WO</li>
                        <li>Menghindari akumulasi pekerjaan belum ditindaklanjuti</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>≥ 20% WO umur > 365 hari → Skor 1</li>
                        <li>15% – < 20% → Skor 2</li>
                        <li>10% – < 15% → Skor 3</li>
                        <li>5% – < 10% → Skor 4</li>
                        <li>≤ 5% → Skor 5</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.10.3 - Maintenance Control</span>
                    <h3 class="kpi-title">WO Ageing (> 365 Days)</h3>
                </div>
                <div class="level-badge level-{{ $woAgeing['level'] }}">{{ $woAgeing['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $woAgeing['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $woAgeing['description'] }}</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Old Open WOs</span>
                    <span class="stat-val">{{ number_format($woAgeing['old_open']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Open WOs</span>
                    <span class="stat-val">{{ number_format($woAgeing['total_open']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.10.4 Post Impl Review -->
        <div class="kpi-card" style="position: relative;">
            <div class="kpi-tooltip">
                <div class="tooltip-title">I6.10.4 – Post Implementation Review</div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Deskripsi:</div>
                    <div class="tooltip-content">Evaluasi setelah program pemeliharaan, Asset Improvement (AI), atau proyek selesai untuk memastikan hasil sesuai tujuan yang direncanakan.</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Aspek Dievaluasi:</div>
                    <ul class="tooltip-list">
                        <li>Scope, Time, Cost/Biaya, Quality, Risk, Benefit</li>
                        <li>Perbandingan rencana vs realisasi</li>
                        <li>Hasil pengukuran dan pencapaian target</li>
                        <li>Analisis GAP target vs realisasi</li>
                        <li>Lesson Learned dan rekomendasi perbaikan</li>
                        <li>Evidence dan dokumentasi hasil evaluasi</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Periode Evaluasi:</div>
                    <ul class="tooltip-list">
                        <li>Setiap tahun</li>
                        <li>Seluruh program AI dan proyek selesai dieksekusi</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Referensi:</div>
                    <div class="tooltip-content">ISO 55001 – Asset Management Standard, Klausul 9.3 – Management Review</div>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Tujuan:</div>
                    <ul class="tooltip-list">
                        <li>Mengukur efektivitas program dan proyek</li>
                        <li>Mengetahui kesesuaian rencana vs hasil aktual</li>
                        <li>Mengidentifikasi peluang perbaikan berkelanjutan</li>
                        <li>Dasar pengambilan keputusan program berikutnya</li>
                    </ul>
                </div>
                <div class="tooltip-section">
                    <div class="tooltip-label">Kategori Penilaian:</div>
                    <ul class="tooltip-list">
                        <li>< 50% program RKAU dilakukan PIR → Skor 1</li>
                        <li>50% – < 75% program dilakukan PIR → Skor 2</li>
                        <li>75% – < 100% program dilakukan PIR → Skor 3</li>
                        <li>100% program dilakukan PIR → Skor 4</li>
                        <li>Skor 5: Skor 4 + hasil aktual sesuai/melebihi target</li>
                    </ul>
                </div>
            </div>
            <div class="kpi-header">
                <div class="kpi-title-group">
                    <span class="kpi-code">I6.10.4 - Maintenance Control</span>
                    <h3 class="kpi-title">Post Implementation Review</h3>
                </div>
                <div class="level-badge level-{{ $postImplReview['level'] }}">{{ $postImplReview['level'] }}</div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value-section">
                    <div class="kpi-value">{{ $postImplReview['percentage'] }}<span class="kpi-unit">%</span></div>
                    <div class="kpi-desc">{{ $postImplReview['description'] }}</div>
                    <div class="evaluation-text">Evaluasi tahunan program AI & Project</div>
                </div>
            </div>
            <div class="kpi-footer">
                <div class="stat-item">
                    <span class="stat-label">Reviewed Programs</span>
                    <span class="stat-val">{{ number_format($postImplReview['reviewed']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Programs</span>
                    <span class="stat-val">{{ number_format($postImplReview['total']) }}</span>
                </div>
            </div>
        </div>
        
    </div>
</div>
