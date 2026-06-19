@extends('frontend.frontend_master')

@section('content')
<div id="app">
    <div class="main-wrapper main-wrapper-1">

        @if(Auth::user()->role == 'SuperAdmin')
            @include('frontend.superadmin.body.header')
            @include('frontend.superadmin.body.sidebar')
        @else
            @include('frontend.admin.body.header')
            @include('frontend.admin.body.sidebar')
        @endif

        <div class="main-content">
            <section class="section">

                {{-- ── Hero Banner ─────────────────────────────────────── --}}
                <div class="chairman-hero mb-4">
                    <div class="chairman-hero-inner">
                        <div class="d-flex align-items-center">
                            <div class="chairman-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="ml-4">
                                <div class="chairman-greeting">Welcome back,</div>
                                <div class="chairman-name">{{ Auth::user()->name }}</div>
                                <div class="chairman-sub">
                                    <i class="fas fa-building mr-1"></i>Chairman's Office
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-calendar-alt mr-1"></i>{{ now()->format('l, d F Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="chairman-hero-actions">
                            <a href="{{ route('view_all_document') }}" class="btn chairman-btn-outline">
                                <i class="fas fa-file-alt mr-2"></i>All Documents
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── Stat Cards ───────────────────────────────────────── --}}
                <div class="row mb-4" id="stat-cards">

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-pending">
                            <div class="stat-card-body">
                                <div class="stat-info">
                                    <div class="stat-label">Pending Approval</div>
                                    <div class="stat-value">{{ $pendingDocs }}</div>
                                    <div class="stat-sub">Awaiting your decision</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>
                            <div class="stat-bar stat-bar-pending"></div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-approved">
                            <div class="stat-card-body">
                                <div class="stat-info">
                                    <div class="stat-label">Approved by You</div>
                                    <div class="stat-value">{{ $approvedDocs }}</div>
                                    <div class="stat-sub">All-time approvals</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="stat-bar stat-bar-approved"></div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-completed">
                            <div class="stat-card-body">
                                <div class="stat-info">
                                    <div class="stat-label">Completed</div>
                                    <div class="stat-value">{{ $completedDocs }}</div>
                                    <div class="stat-sub">Fully processed</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                            </div>
                            <div class="stat-bar stat-bar-completed"></div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card stat-total">
                            <div class="stat-card-body">
                                <div class="stat-info">
                                    <div class="stat-label">Total Documents</div>
                                    <div class="stat-value">{{ $totalDocs }}</div>
                                    <div class="stat-sub">Through Chairman stage</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                            <div class="stat-bar stat-bar-total"></div>
                        </div>
                    </div>

                </div>

                {{-- ── Charts Row 1: Donut + Bar ─────────────────────────── --}}
                <div class="row mb-4">

                    <div class="col-xl-5 col-lg-6 mb-3">
                        <div class="chart-card h-100">
                            <div class="chart-card-header">
                                <div class="chart-card-title">
                                    <i class="fas fa-chart-pie mr-2"></i>Document Status Mix
                                </div>
                                <span class="chart-badge">All Time</span>
                            </div>
                            <div class="chart-card-body d-flex align-items-center justify-content-center">
                                <div style="position:relative; width:100%; max-width:320px;">
                                    <canvas id="statusDonutChart" height="280"></canvas>
                                </div>
                            </div>
                            <div class="chart-legend" id="donut-legend"></div>
                        </div>
                    </div>

                    <div class="col-xl-7 col-lg-6 mb-3">
                        <div class="chart-card h-100">
                            <div class="chart-card-header">
                                <div class="chart-card-title">
                                    <i class="fas fa-chart-bar mr-2"></i>Monthly Approvals
                                </div>
                                <span class="chart-badge">Last 6 Months</span>
                            </div>
                            <div class="chart-card-body">
                                <canvas id="monthlyBarChart" height="240"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Charts Row 2: Line Trend + Amount ────────────────── --}}
                <div class="row mb-4">

                    <div class="col-xl-7 col-lg-6 mb-3">
                        <div class="chart-card h-100">
                            <div class="chart-card-header">
                                <div class="chart-card-title">
                                    <i class="fas fa-chart-line mr-2"></i>Approval Trend
                                </div>
                                <span class="chart-badge">Last 12 Months</span>
                            </div>
                            <div class="chart-card-body">
                                <canvas id="trendLineChart" height="240"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5 col-lg-6 mb-3">
                        <div class="chart-card h-100">
                            <div class="chart-card-header">
                                <div class="chart-card-title">
                                    <i class="fas fa-rupee-sign mr-2"></i>Sanctioned Amount
                                </div>
                                <span class="chart-badge">Last 6 Months</span>
                            </div>
                            <div class="chart-card-body">
                                <canvas id="amountBarChart" height="240"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Pending Documents Table ───────────────────────────── --}}
                <div class="row">
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-card-header">
                                <div class="chart-card-title">
                                    <i class="fas fa-clock mr-2"></i>Pending Documents
                                </div>
                                <a href="{{ route('view_all_document') }}" class="btn btn-sm chairman-btn-outline-sm">
                                    View All <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <div class="chart-card-body p-0">
                                @if($pendingDocuments->isEmpty())
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                        <p class="text-muted mb-0">No documents currently pending your approval.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table chairman-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Doc ID</th>
                                                    <th>Title</th>
                                                    <th>From</th>
                                                    <th>Amount</th>
                                                    <th>Priority</th>
                                                    <th>Days Pending</th>
                                                    <th>Created By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pendingDocuments as $i => $doc)
                                                <tr class="{{ $doc->days_pending >= 7 ? 'row-urgent' : '' }}">
                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                    <td>
                                                        <span class="doc-id-badge">{{ $doc->doc_id ?? '—' }}</span>
                                                    </td>
                                                    <td class="doc-title">{{ Str::limit($doc->title, 45) }}</td>
                                                    <td><span class="dept-badge">{{ $doc->from }}</span></td>
                                                    <td>
                                                        @if($doc->amount)
                                                            <span class="amount-val">₹{{ number_format($doc->amount, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $pClass = match(strtolower($doc->priority ?? '')) {
                                                                'high', 'urgent' => 'priority-high',
                                                                'medium', 'normal' => 'priority-medium',
                                                                default => 'priority-low',
                                                            };
                                                        @endphp
                                                        <span class="priority-badge {{ $pClass }}">
                                                            {{ $doc->priority ?? 'Normal' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($doc->days_pending >= 7)
                                                            <span class="days-badge days-urgent">{{ $doc->days_pending }}d</span>
                                                        @elseif($doc->days_pending >= 3)
                                                            <span class="days-badge days-warning">{{ $doc->days_pending }}d</span>
                                                        @else
                                                            <span class="days-badge days-ok">{{ $doc->days_pending }}d</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted small">{{ $doc->created_by ?? '—' }}</td>
                                                    <td>
                                                        <a href="{{ route('view_single_document', $doc->id) }}"
                                                           class="btn btn-sm btn-chairman-view">
                                                            <i class="fas fa-eye mr-1"></i>Review
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>
</div>

{{-- ── Styles ──────────────────────────────────────────────────────────── --}}
<style>
/* ── Hero ─────────────────────────────────────────────────────── */
.chairman-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(15,52,96,.45);
}
.chairman-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.chairman-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 30%;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,.03);
}
.chairman-hero-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
    z-index: 1;
}
.chairman-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
    border: 2px solid rgba(255,255,255,.2);
    flex-shrink: 0;
}
.chairman-greeting { font-size: 13px; opacity: .7; letter-spacing: 1px; text-transform: uppercase; }
.chairman-name    { font-size: 22px; font-weight: 700; line-height: 1.2; }
.chairman-sub     { font-size: 13px; opacity: .65; margin-top: 4px; }
.chairman-btn-outline {
    border: 1.5px solid rgba(255,255,255,.4);
    color: #fff;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 13px;
    background: transparent;
    transition: all .2s;
}
.chairman-btn-outline:hover { background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.7); text-decoration: none; }

/* ── Stat Cards ───────────────────────────────────────────────── */
.stat-card {
    border-radius: 14px;
    padding: 20px 20px 0;
    overflow: hidden;
    position: relative;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
    background: #fff;
    border: none;
    height: 100%;
}
.stat-card-body { display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; }
.stat-label   { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; opacity: .65; }
.stat-value   { font-size: 38px; font-weight: 800; line-height: 1.1; margin: 4px 0; }
.stat-sub     { font-size: 12px; opacity: .55; }
.stat-icon    { font-size: 36px; opacity: .18; }
.stat-bar { height: 5px; border-radius: 0 0 14px 14px; }

.stat-pending .stat-value { color: #e67e22; }
.stat-pending .stat-icon  { color: #e67e22; }
.stat-bar-pending         { background: linear-gradient(90deg,#e67e22,#f39c12); }

.stat-approved .stat-value { color: #27ae60; }
.stat-approved .stat-icon  { color: #27ae60; }
.stat-bar-approved         { background: linear-gradient(90deg,#27ae60,#2ecc71); }

.stat-completed .stat-value { color: #2980b9; }
.stat-completed .stat-icon  { color: #2980b9; }
.stat-bar-completed         { background: linear-gradient(90deg,#2980b9,#3498db); }

.stat-total .stat-value { color: #8e44ad; }
.stat-total .stat-icon  { color: #8e44ad; }
.stat-bar-total         { background: linear-gradient(90deg,#8e44ad,#9b59b6); }

/* ── Chart Cards ──────────────────────────────────────────────── */
.chart-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    overflow: hidden;
}
.chart-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid #f0f0f0;
}
.chart-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
}
.chart-card-title i { color: #6c5ecf; }
.chart-badge {
    font-size: 11px;
    background: #f0edff;
    color: #6c5ecf;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 600;
}
.chart-card-body { padding: 16px 22px; }
.chart-legend { display: flex; flex-wrap: wrap; gap: 8px; padding: 8px 22px 16px; }
.chart-legend-item { display: flex; align-items: center; font-size: 12px; }
.chart-legend-dot  { width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; flex-shrink: 0; }

/* ── Table ────────────────────────────────────────────────────── */
.chairman-table thead th {
    background: #f8f9fc;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: #6c757d;
    font-weight: 700;
    padding: 12px 16px;
    border-top: none;
    border-bottom: 2px solid #eee;
}
.chairman-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f4f4f4;
    font-size: 13.5px;
}
.chairman-table tbody tr:last-child td { border-bottom: none; }
.chairman-table tbody tr:hover { background: #fafbff; }
.row-urgent td { background: #fff9f0 !important; }

.doc-id-badge   { background: #e8f4fd; color: #2980b9; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.doc-title      { font-weight: 500; color: #2c3e50; }
.dept-badge     { background: #f0f0f0; color: #555; padding: 2px 8px; border-radius: 6px; font-size: 12px; }
.amount-val     { font-weight: 600; color: #27ae60; font-size: 13px; }

.priority-badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.priority-high   { background: #ffeaea; color: #c0392b; }
.priority-medium { background: #fff8e1; color: #e67e22; }
.priority-low    { background: #e8f8f0; color: #27ae60; }

.days-badge { padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.days-urgent  { background: #ffeaea; color: #c0392b; }
.days-warning { background: #fff3cd; color: #856404; }
.days-ok      { background: #d4edda; color: #155724; }

.btn-chairman-view {
    background: linear-gradient(135deg,#6c5ecf,#4834d4);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    padding: 4px 12px;
    font-weight: 600;
    transition: opacity .2s;
}
.btn-chairman-view:hover { opacity: .85; color: #fff; }

.chairman-btn-outline-sm {
    border: 1.5px solid #6c5ecf;
    color: #6c5ecf;
    border-radius: 8px;
    padding: 4px 14px;
    font-size: 12px;
    background: transparent;
    font-weight: 600;
    transition: all .2s;
}
.chairman-btn-outline-sm:hover { background: #6c5ecf; color: #fff; text-decoration: none; }

.empty-state { text-align: center; padding: 48px; }
</style>

{{-- ── Chart Scripts ───────────────────────────────────────────────────── --}}
@push('scripts')
<script>
(function () {
    // ── Data from PHP ─────────────────────────────────────────────────
    const statusLabels = @json(array_keys($statusDistribution->toArray()));
    const statusValues = @json(array_values($statusDistribution->toArray()));

    const barLabels    = @json($monthlyInflow->pluck('month')->values()->toArray());
    const barValues    = @json($monthlyInflow->pluck('count')->values()->toArray());

    const trendLabels  = @json($approvalTrend->pluck('month')->values()->toArray());
    const trendValues  = @json($approvalTrend->pluck('count')->values()->toArray());

    const amtLabels    = @json($monthlyAmounts->pluck('month')->values()->toArray());
    const amtValues    = @json($monthlyAmounts->pluck('total')->map(fn($v) => (float)$v ?: 0)->values()->toArray());

    // ── Colour palette ────────────────────────────────────────────────
    const palette = [
        '#6c5ecf','#3498db','#27ae60','#e67e22',
        '#e74c3c','#1abc9c','#9b59b6','#f39c12',
        '#2980b9','#16a085','#8e44ad','#c0392b',
    ];

    // ── 1. Status Donut ───────────────────────────────────────────────
    const donutCtx = document.getElementById('statusDonutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: palette.slice(0, statusLabels.length),
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} docs`
                    }
                }
            },
        }
    });

    // Build custom legend
    const legendEl = document.getElementById('donut-legend');
    statusLabels.forEach((label, i) => {
        legendEl.innerHTML += `
            <div class="chart-legend-item">
                <div class="chart-legend-dot" style="background:${palette[i] || '#999'}"></div>
                <span>${label} (${statusValues[i]})</span>
            </div>`;
    });

    // ── 2. Monthly Bar ────────────────────────────────────────────────
    const barCtx = document.getElementById('monthlyBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barLabels.length ? barLabels : ['No Data'],
            datasets: [{
                label: 'Approvals',
                data: barValues.length ? barValues : [0],
                backgroundColor: 'rgba(108,94,207,.75)',
                borderColor: '#6c5ecf',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} approval(s)` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#888' },
                    grid: { color: 'rgba(0,0,0,.04)' }
                },
                x: { ticks: { color: '#888' }, grid: { display: false } }
            }
        }
    });

    // ── 3. Trend Line ─────────────────────────────────────────────────
    const lineCtx = document.getElementById('trendLineChart').getContext('2d');
    const lineGrad = lineCtx.createLinearGradient(0, 0, 0, 240);
    lineGrad.addColorStop(0, 'rgba(108,94,207,.3)');
    lineGrad.addColorStop(1, 'rgba(108,94,207,.0)');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: trendLabels.length ? trendLabels : ['No Data'],
            datasets: [{
                label: 'Approvals',
                data: trendValues.length ? trendValues : [0],
                borderColor: '#6c5ecf',
                backgroundColor: lineGrad,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6c5ecf',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} approval(s)` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#888' },
                    grid: { color: 'rgba(0,0,0,.04)' }
                },
                x: { ticks: { color: '#888' }, grid: { display: false } }
            }
        }
    });

    // ── 4. Amount Bar ─────────────────────────────────────────────────
    const amtCtx = document.getElementById('amountBarChart').getContext('2d');
    new Chart(amtCtx, {
        type: 'bar',
        data: {
            labels: amtLabels.length ? amtLabels : ['No Data'],
            datasets: [{
                label: 'Sanctioned (₹)',
                data: amtValues.length ? amtValues : [0],
                backgroundColor: 'rgba(39,174,96,.72)',
                borderColor: '#27ae60',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ₹${ctx.parsed.y.toLocaleString('en-IN')}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#888',
                        callback: v => '₹' + (v >= 100000 ? (v / 100000).toFixed(1) + 'L' : v.toLocaleString('en-IN'))
                    },
                    grid: { color: 'rgba(0,0,0,.04)' }
                },
                x: { ticks: { color: '#888' }, grid: { display: false } }
            }
        }
    });

})();
</script>
@endpush

@endsection
