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
</style>

<div class="dashboard-container py-5 mt-5 px-4 fade-in">
    <div class="kpi-grid">
        
        <!-- I6.6 PM Compliance -->
        <div class="kpi-card">
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
        <div class="kpi-card">
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
        <div class="kpi-card">
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
        <div class="kpi-card">
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
        <div class="kpi-card">
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
                    <span class="stat-label">Non-Tactical (Created)</span>
                    <span class="stat-val">{{ number_format($reactiveWork['non_tactical']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Tactical (Closed)</span>
                    <span class="stat-val">{{ number_format($reactiveWork['tactical_closed']) }}</span>
                </div>
            </div>
        </div>

        <!-- I6.10.2 WR/SR Open/Queued -->
        <div class="kpi-card">
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
        <div class="kpi-card">
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
        <div class="kpi-card">
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
