@extends('frontend.frontend_master')

@section('content')
@php
    $user = Auth::user();
    $role = $user->role;
    $dept = $user->department;
@endphp

<div id="app">
<div class="main-wrapper main-wrapper-1">

    @if($role === 'Staff')
        @include('frontend.staff.body.header')
        @include('frontend.staff.body.sidebar')
    @elseif($role === 'SuperAdmin')
        @include('frontend.superadmin.body.header')
        @include('frontend.superadmin.body.sidebar')
    @else
        @include('frontend.admin.body.header')
        @include('frontend.admin.body.sidebar')
    @endif

    <div class="main-content ud-wrap">
    <section class="section">

        {{-- ══════════════════════════════════════════════════════
             HERO GREETING BAR
        ══════════════════════════════════════════════════════ --}}
        <div class="ud-hero">
            <div class="ud-hero-left">
                <div class="ud-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="ud-greet">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},</div>
                    <div class="ud-name">{{ $user->name }}</div>
                    <div class="ud-meta">
                        <span><i class="fas fa-building"></i> {{ $dept }}</span>
                        <span class="ud-sep">·</span>
                        <span><i class="fas fa-id-badge"></i> {{ $role }}</span>
                        <span class="ud-sep">·</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="ud-hero-right">
                <div class="ud-quick-stat">
                    <div class="ud-qs-val">{{ $docs_pending }}</div>
                    <div class="ud-qs-lbl">Pending<br>Approvals</div>
                </div>
                <div class="ud-quick-stat">
                    <div class="ud-qs-val">{{ $tkt_open }}</div>
                    <div class="ud-qs-lbl">Open<br>Tickets</div>
                </div>
                <div class="ud-quick-stat">
                    <div class="ud-qs-val">{{ $post_unread }}</div>
                    <div class="ud-qs-lbl">Unread<br>Post</div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SECTION 1 — DOCUMENT APPROVALS / REQUESTS
        ══════════════════════════════════════════════════════ --}}
        <div class="ud-section-label">
            <span class="ud-section-dot" style="background:#6c5ecf"></span>
            Document Approvals &amp; Requests
        </div>

        <div class="row ud-cards-row">

            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route($docRoute) }}" class="ud-card-link">
                    <div class="ud-card" style="--accent:#6c5ecf">
                        <div class="ud-card-icon"><i class="fas fa-layer-group"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $docs_total }}</div>
                            <div class="ud-card-lbl">Total Requests</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#e67e22">
                    <div class="ud-card-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $docs_pending }}</div>
                        <div class="ud-card-lbl">Pending</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#27ae60">
                    <div class="ud-card-icon"><i class="fas fa-check-double"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $docs_completed }}</div>
                        <div class="ud-card-lbl">Completed</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#e74c3c">
                    <div class="ud-card-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $docs_rejected }}</div>
                        <div class="ud-card-lbl">Rejected</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

        </div>

        {{-- Approvals chart --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="ud-chart-card">
                    <div class="ud-chart-hdr">
                        <div class="ud-chart-title">
                            <i class="fas fa-chart-bar" style="color:#6c5ecf"></i>
                            Monthly Document Requests
                        </div>
                        <span class="ud-badge" style="background:#f0edff;color:#6c5ecf">Last 6 Months</span>
                    </div>
                    <div class="ud-chart-body">
                        <canvas id="docMonthlyChart" height="110"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SECTION 2 — TICKETS
        ══════════════════════════════════════════════════════ --}}
        <div class="ud-section-label">
            <span class="ud-section-dot" style="background:#3498db"></span>
            Tickets
        </div>

        <div class="row ud-cards-row">

            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <a href="{{ route($tktRoutes['total']) }}" class="ud-card-link">
                    <div class="ud-card ud-card-sm" style="--accent:#3498db">
                        <div class="ud-card-icon small"><i class="fas fa-ticket-alt"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $tkt_total }}</div>
                            <div class="ud-card-lbl">Total</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <a href="{{ route($tktRoutes['open']) }}" class="ud-card-link">
                    <div class="ud-card ud-card-sm" style="--accent:#e67e22">
                        <div class="ud-card-icon small"><i class="fas fa-folder-open"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $tkt_open }}</div>
                            <div class="ud-card-lbl">Open</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <a href="{{ route($tktRoutes['inprogress']) }}" class="ud-card-link">
                    <div class="ud-card ud-card-sm" style="--accent:#9b59b6">
                        <div class="ud-card-icon small"><i class="fas fa-spinner"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $tkt_inprogress }}</div>
                            <div class="ud-card-lbl">In Progress</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <a href="{{ route($tktRoutes['hold']) }}" class="ud-card-link">
                    <div class="ud-card ud-card-sm" style="--accent:#f39c12">
                        <div class="ud-card-icon small"><i class="fas fa-pause-circle"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $tkt_hold }}</div>
                            <div class="ud-card-lbl">On Hold</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <a href="{{ route($tktRoutes['completed']) }}" class="ud-card-link">
                    <div class="ud-card ud-card-sm" style="--accent:#27ae60">
                        <div class="ud-card-icon small"><i class="fas fa-check-circle"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $tkt_completed }}</div>
                            <div class="ud-card-lbl">Completed</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <a href="{{ route($tktRoutes['closed']) }}" class="ud-card-link">
                    <div class="ud-card ud-card-sm" style="--accent:#7f8c8d">
                        <div class="ud-card-icon small"><i class="fas fa-lock"></i></div>
                        <div class="ud-card-info">
                            <div class="ud-card-num">{{ $tkt_closed }}</div>
                            <div class="ud-card-lbl">Closed</div>
                        </div>
                        <div class="ud-card-bar"></div>
                    </div>
                </a>
            </div>

        </div>

        {{-- Tickets chart + recent table --}}
        <div class="row mb-4">

            <div class="col-xl-5 col-lg-5 mb-3">
                <div class="ud-chart-card h-100">
                    <div class="ud-chart-hdr">
                        <div class="ud-chart-title">
                            <i class="fas fa-chart-pie" style="color:#3498db"></i>
                            Ticket Status Breakdown
                        </div>
                        <span class="ud-badge" style="background:#ebf5fb;color:#2980b9">All Time</span>
                    </div>
                    <div class="ud-chart-body d-flex align-items-center justify-content-center">
                        <div style="max-width:260px;width:100%">
                            <canvas id="tktDonutChart" height="260"></canvas>
                        </div>
                    </div>
                    <div class="ud-legend" id="tkt-legend"></div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-7 mb-3">
                <div class="ud-chart-card h-100">
                    <div class="ud-chart-hdr">
                        <div class="ud-chart-title">
                            <i class="fas fa-chart-bar" style="color:#3498db"></i>
                            Monthly Ticket Volume
                        </div>
                        <span class="ud-badge" style="background:#ebf5fb;color:#2980b9">Last 6 Months</span>
                    </div>
                    <div class="ud-chart-body">
                        <canvas id="tktBarChart" height="230"></canvas>
                    </div>
                </div>
            </div>

        </div>

        {{-- Recent tickets table --}}
        @if($recentTickets->isNotEmpty())
        <div class="row mb-4">
            <div class="col-12">
                <div class="ud-chart-card">
                    <div class="ud-chart-hdr">
                        <div class="ud-chart-title">
                            <i class="fas fa-clock" style="color:#3498db"></i>
                            Recent Open Tickets
                        </div>
                    </div>
                    <div class="ud-chart-body p-0">
                        <div class="table-responsive">
                            <table class="table ud-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Title</th>
                                        <th>From</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTickets as $t)
                                    <tr>
                                        <td><span class="ud-tid">{{ $t->ticket_id ?? '—' }}</span></td>
                                        <td class="ud-ttitle">{{ Str::limit($t->title, 50) }}</td>
                                        <td class="text-muted small">{{ $t->from }}</td>
                                        <td>
                                            <span class="ud-priority {{ strtolower($t->priority ?? '') === 'high' ? 'ud-p-high' : 'ud-p-normal' }}">
                                                {{ ucfirst($t->priority ?? 'Normal') }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $sc = ['Open'=>'#e67e22','In Progress'=>'#9b59b6','Hold'=>'#f39c12','Completed'=>'#27ae60'];
                                                $clr = $sc[$t->status] ?? '#95a5a6';
                                            @endphp
                                            <span class="ud-status-dot" style="background:{{ $clr }}20;color:{{ $clr }}">
                                                {{ $t->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════
             SECTION 3 — POSTAL
        ══════════════════════════════════════════════════════ --}}
        <div class="ud-section-label">
            <span class="ud-section-dot" style="background:#27ae60"></span>
            Post &amp; Correspondence
        </div>

        <div class="row ud-cards-row mb-5">

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#27ae60">
                    <div class="ud-card-icon"><i class="fas fa-envelope"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $post_total }}</div>
                        <div class="ud-card-lbl">Total Post</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#e67e22">
                    <div class="ud-card-icon"><i class="fas fa-envelope-open-text"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $post_pending }}</div>
                        <div class="ud-card-lbl">Pending</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#e74c3c">
                    <div class="ud-card-icon"><i class="fas fa-bell"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $post_unread }}</div>
                        <div class="ud-card-lbl">Unread</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="ud-card" style="--accent:#2980b9">
                    <div class="ud-card-icon"><i class="fas fa-share-square"></i></div>
                    <div class="ud-card-info">
                        <div class="ud-card-num">{{ $post_forwarded }}</div>
                        <div class="ud-card-lbl">Forwarded</div>
                    </div>
                    <div class="ud-card-bar"></div>
                </div>
            </div>

        </div>

        {{-- Post + Docs combined trend --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="ud-chart-card">
                    <div class="ud-chart-hdr">
                        <div class="ud-chart-title">
                            <i class="fas fa-chart-line" style="color:#27ae60"></i>
                            Combined Activity Trend
                        </div>
                        <span class="ud-badge" style="background:#eafaf1;color:#27ae60">Tickets + Requests — Last 6 Months</span>
                    </div>
                    <div class="ud-chart-body">
                        <canvas id="combinedTrendChart" height="110"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </section>
    </div><!-- /.main-content -->
</div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════════════════ --}}
<style>
/* ── Wrap ──────────────────────────────────────────────────────── */
.ud-wrap { background: #f5f6fa; }
.section  { padding-bottom: 32px; }

/* ── Hero ──────────────────────────────────────────────────────── */
.ud-hero {
    background: linear-gradient(135deg,#1c1c3a 0%,#2c3e7a 50%,#1a5276 100%);
    border-radius: 18px;
    padding: 26px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(28,28,58,.35);
}
.ud-hero::after {
    content: '';
    position: absolute; top: -80px; right: -80px;
    width: 240px; height: 240px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
    pointer-events: none;
}
.ud-hero-left { display: flex; align-items: center; gap: 18px; }
.ud-avatar {
    width: 58px; height: 58px; border-radius: 50%;
    background: rgba(255,255,255,.15);
    font-size: 22px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid rgba(255,255,255,.25);
    flex-shrink: 0;
}
.ud-greet { font-size: 12px; opacity: .65; letter-spacing: 1px; text-transform: uppercase; }
.ud-name  { font-size: 20px; font-weight: 700; line-height: 1.2; }
.ud-meta  { font-size: 12px; opacity: .6; margin-top: 5px; }
.ud-meta span { margin-right: 2px; }
.ud-meta i    { margin-right: 4px; }
.ud-sep       { margin: 0 6px; opacity: .4; }
.ud-hero-right { display: flex; gap: 20px; }
.ud-quick-stat {
    text-align: center;
    background: rgba(255,255,255,.1);
    border-radius: 10px;
    padding: 12px 18px;
    min-width: 70px;
}
.ud-qs-val  { font-size: 24px; font-weight: 800; line-height: 1; }
.ud-qs-lbl  { font-size: 11px; opacity: .65; margin-top: 4px; line-height: 1.3; }

/* ── Section Labels ────────────────────────────────────────────── */
.ud-section-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #555;
    margin-bottom: 14px;
    margin-top: 8px;
}
.ud-section-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Stat Cards ────────────────────────────────────────────────── */
.ud-cards-row { margin-bottom: 0; }
.ud-card-link { text-decoration: none; color: inherit; display: block; }
.ud-card-link:hover { text-decoration: none; }
.ud-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px 18px 0;
    box-shadow: 0 3px 16px rgba(0,0,0,.07);
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform .15s, box-shadow .15s;
    overflow: hidden;
    position: relative;
}
.ud-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.ud-card-icon {
    font-size: 28px;
    color: var(--accent);
    opacity: .25;
    margin-bottom: 10px;
    align-self: flex-end;
}
.ud-card-icon.small { font-size: 20px; }
.ud-card-info { margin-bottom: 14px; }
.ud-card-num  { font-size: 34px; font-weight: 800; color: #2c3e50; line-height: 1; }
.ud-card-lbl  { font-size: 12px; color: #888; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.ud-card-bar  { height: 4px; background: var(--accent); border-radius: 0 0 14px 14px; }
.ud-card.ud-card-sm .ud-card-num { font-size: 26px; }

/* ── Chart Cards ───────────────────────────────────────────────── */
.ud-chart-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 3px 16px rgba(0,0,0,.07);
    overflow: hidden;
}
.ud-chart-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f0f0;
}
.ud-chart-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ud-badge {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 600;
}
.ud-chart-body { padding: 16px 20px; }
.ud-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 6px 20px 14px;
}
.ud-legend-item { display: flex; align-items: center; font-size: 11.5px; gap: 6px; }
.ud-legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* ── Table ─────────────────────────────────────────────────────── */
.ud-table thead th {
    background: #f8f9fc;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #6c757d;
    font-weight: 700;
    padding: 11px 16px;
    border-top: none;
    border-bottom: 2px solid #eee;
}
.ud-table tbody td {
    padding: 11px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f4f4f4;
    font-size: 13px;
}
.ud-table tbody tr:last-child td { border-bottom: none; }
.ud-table tbody tr:hover         { background: #fafbff; }
.ud-tid    { background: #ebf5fb; color: #2980b9; padding: 2px 8px; border-radius: 5px; font-size: 12px; font-weight: 600; }
.ud-ttitle { font-weight: 500; color: #2c3e50; }
.ud-priority { padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.ud-p-high   { background: #fdecea; color: #c0392b; }
.ud-p-normal { background: #eafaf1; color: #27ae60; }
.ud-status-dot {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}
</style>

{{-- ═══════════════════════════════════════════════════════════════════
     CHART SCRIPTS
═══════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
(function () {

    // ── PHP data ────────────────────────────────────────────────────
    const docMonths  = @json($docMonthly->pluck('month')->values());
    const docCounts  = @json($docMonthly->pluck('count')->values());

    const tktMonths  = @json($tktMonthly->pluck('month')->values());
    const tktCounts  = @json($tktMonthly->pluck('count')->values());

    const tktStatus  = {
        Open:        {{ $tkt_open }},
        'In Progress': {{ $tkt_inprogress }},
        Hold:        {{ $tkt_hold }},
        Completed:   {{ $tkt_completed }},
        Closed:      {{ $tkt_closed }},
    };
    const tktColors = ['#e67e22','#9b59b6','#f39c12','#27ae60','#7f8c8d'];

    // Helper: gradient fill
    function lineGrad(ctx, color) {
        const g = ctx.createLinearGradient(0, 0, 0, 200);
        g.addColorStop(0, color + '55');
        g.addColorStop(1, color + '00');
        return g;
    }

    // ── 1. Document monthly bar chart ─────────────────────────────
    new Chart(document.getElementById('docMonthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: docMonths.length ? docMonths : ['No data'],
            datasets: [{
                label: 'Requests',
                data: docCounts.length ? docCounts : [0],
                backgroundColor: 'rgba(108,94,207,.7)',
                borderColor: '#6c5ecf',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#999' }, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { ticks: { color: '#999' }, grid: { display: false } }
            }
        }
    });

    // ── 2. Ticket Donut ───────────────────────────────────────────
    const tktLabels = Object.keys(tktStatus);
    const tktVals   = Object.values(tktStatus);
    const donutCtx  = document.getElementById('tktDonutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: tktLabels,
            datasets: [{
                data: tktVals,
                backgroundColor: tktColors,
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '62%',
            plugins: { legend: { display: false } }
        }
    });

    // Custom legend
    const leg = document.getElementById('tkt-legend');
    tktLabels.forEach((lbl, i) => {
        leg.innerHTML += `<div class="ud-legend-item">
            <div class="ud-legend-dot" style="background:${tktColors[i]}"></div>
            <span>${lbl} <b>(${tktVals[i]})</b></span></div>`;
    });

    // ── 3. Ticket monthly bar ─────────────────────────────────────
    new Chart(document.getElementById('tktBarChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: tktMonths.length ? tktMonths : ['No data'],
            datasets: [{
                label: 'Tickets',
                data: tktCounts.length ? tktCounts : [0],
                backgroundColor: 'rgba(52,152,219,.7)',
                borderColor: '#3498db',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#999' }, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { ticks: { color: '#999' }, grid: { display: false } }
            }
        }
    });

    // ── 4. Combined trend line ─────────────────────────────────────
    // Merge labels from both series
    const allMonths = [...new Set([...tktMonths, ...docMonths])].sort();
    const tktMap  = Object.fromEntries(tktMonths.map((m, i) => [m, tktCounts[i]]));
    const docMap  = Object.fromEntries(docMonths.map((m, i) => [m, docCounts[i]]));
    const cTkt = allMonths.map(m => tktMap[m] ?? 0);
    const cDoc = allMonths.map(m => docMap[m] ?? 0);

    const ctx4 = document.getElementById('combinedTrendChart').getContext('2d');
    new Chart(ctx4, {
        type: 'line',
        data: {
            labels: allMonths.length ? allMonths : ['No data'],
            datasets: [
                {
                    label: 'Tickets',
                    data: cTkt,
                    borderColor: '#3498db',
                    backgroundColor: lineGrad(ctx4, '#3498db'),
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3498db',
                    pointRadius: 4,
                },
                {
                    label: 'Requests',
                    data: cDoc,
                    borderColor: '#6c5ecf',
                    backgroundColor: lineGrad(ctx4, '#6c5ecf'),
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6c5ecf',
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    labels: { boxWidth: 12, font: { size: 12 } }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#999' }, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { ticks: { color: '#999' }, grid: { display: false } }
            }
        }
    });

})();
</script>
@endpush

@endsection
