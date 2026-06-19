@extends('frontend.frontend_master')
@section('content')

@php
/* ── Role theme config ───────────────────────────────────────────────────── */
$themes = [
    'hod' => [
        'label'  => 'Department Head',
        'icon'   => 'fas fa-building',
        'g1'     => '#0c1445',
        'g2'     => '#1d3a8a',
        'accent' => '#60a5fa',
        'ring'   => 'rgba(96,165,250,0.25)',
    ],
    'medical_director' => [
        'label'  => 'Medical Director',
        'icon'   => 'fas fa-user-md',
        'g1'     => '#0c1445',
        'g2'     => '#1e3a8a',
        'accent' => '#34d399',
        'ring'   => 'rgba(52,211,153,0.25)',
    ],
    'general_manager' => [
        'label'  => 'General Manager',
        'icon'   => 'fas fa-briefcase',
        'g1'     => '#0f1623',
        'g2'     => '#1a2d4a',
        'accent' => '#94a3b8',
        'ring'   => 'rgba(148,163,184,0.3)',
    ],
    'stb' => [
        'label'  => 'STB Office',
        'icon'   => 'fas fa-stamp',
        'g1'     => '#0d0f28',
        'g2'     => '#1e2060',
        'accent' => '#818cf8',
        'ring'   => 'rgba(129,140,248,0.25)',
    ],
    'chairman' => [
        'label'  => 'Chairman',
        'icon'   => 'fas fa-crown',
        'g1'     => '#0c1445',
        'g2'     => '#1e3a8a',
        'accent' => '#fbbf24',
        'ring'   => 'rgba(251,191,36,0.25)',
    ],
    'finance' => [
        'label'  => 'Finance Head',
        'icon'   => 'fas fa-landmark',
        'g1'     => '#0d1128',
        'g2'     => '#1e1f5e',
        'accent' => '#a78bfa',
        'ring'   => 'rgba(167,139,250,0.25)',
    ],
    'purchase' => [
        'label'  => 'Purchase Head',
        'icon'   => 'fas fa-shopping-cart',
        'g1'     => '#0c1445',
        'g2'     => '#1e3a8a',
        'accent' => '#fb923c',
        'ring'   => 'rgba(251,146,60,0.25)',
    ],
    'staff' => [
        'label'  => 'Staff',
        'icon'   => 'fas fa-user-circle',
        'g1'     => '#0c1445',
        'g2'     => '#1e3a8a',
        'accent' => '#2dd4bf',
        'ring'   => 'rgba(45,212,191,0.25)',
    ],
    'itadmin' => [
        'label'  => 'IT Administrator',
        'icon'   => 'fas fa-shield-alt',
        'g1'     => '#0d1117',
        'g2'     => '#1a2a45',
        'accent' => '#58a6ff',
        'ring'   => 'rgba(88,166,255,0.25)',
    ],
];
$t = $themes[$viewType] ?? $themes['staff'];

/* ── Route helpers per role ───────────────────────────────────────────────── */
$isSA   = $sidebarType === 'superadmin';
$isHOD  = $sidebarType === 'admin';
$docListRoute = $isSA ? 'total_documents'  : ($isHOD ? 'my_documents'       : '#');
$pendRoute    = $isSA ? 'received_documents': ($isHOD ? 'forwarded_documents' : '#');
$doneRoute    = $isSA ? 'approved_documents': ($isHOD ? 'completed_documents' : '#');
$rejRoute     = $isSA ? 'rejected_documents': '#';

/* ── Initials from name ───────────────────────────────────────────────────── */
$nameParts = explode(' ', $authUser->name ?? 'User');
$initials  = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . substr($nameParts[1] ?? '', 0, 1));

/* ── Amount formatter ─────────────────────────────────────────────────────── */
$fmtAmt = static function(float $v): string {
    if ($v >= 1_00_00_000) return '₹' . number_format($v / 1_00_00_000, 1) . ' Cr';
    if ($v >= 1_00_000)    return '₹' . number_format($v / 1_00_000, 1)    . ' L';
    if ($v >= 1_000)       return '₹' . number_format($v / 1_000, 1)       . ' K';
    return '₹' . number_format($v);
};

/* ── Stat card config per role ────────────────────────────────────────────── */
$cards = match($viewType) {
    'itadmin' => [
        ['icon'=>'fas fa-key',          'label'=>'Total API Tokens',  'value'=>$totalTokens ?? 0,  'color'=>'blue',   'route'=>'api.tokens.index', 'trend'=>null],
        ['icon'=>'fas fa-check-circle', 'label'=>'Active Tokens',     'value'=>$activeTokens ?? 0, 'color'=>'green',  'route'=>'api.tokens.index', 'trend'=>null],
        ['icon'=>'fas fa-file-alt',     'label'=>'Total Documents',   'value'=>$totalDocs ?? 0,    'color'=>'amber',  'route'=>'total_documents',  'trend'=>null],
        ['icon'=>'fas fa-users',        'label'=>'Active Users',      'value'=>$totalUsers ?? 0,   'color'=>'purple', 'route'=>'super-admin-view-staffs', 'trend'=>null],
    ],
    'hod', 'staff' => [
        ['icon'=>'fas fa-file-alt',      'label'=>'Total Submitted',  'value'=>$total ?? 0,      'color'=>'blue',   'route'=>$docListRoute,  'trend'=>null],
        ['icon'=>'fas fa-hourglass-half','label'=>'In Progress',      'value'=>$inProgress ?? 0, 'color'=>'amber',  'route'=>$pendRoute,     'trend'=>null],
        ['icon'=>'fas fa-check-circle',  'label'=>'Completed',        'value'=>$completed ?? 0,  'color'=>'green',  'route'=>$doneRoute,     'trend'=>null],
        ['icon'=>'fas fa-times-circle',  'label'=>'Rejected',         'value'=>$rejected ?? 0,   'color'=>'red',    'route'=>$rejRoute,      'trend'=>null],
    ],
    'chairman' => [
        ['icon'=>'fas fa-folder-open',   'label'=>'Total Documents',  'value'=>$total ?? 0,           'color'=>'amber',  'route'=>$docListRoute, 'trend'=>null],
        ['icon'=>'fas fa-inbox',         'label'=>'Awaiting Approval','value'=>$pending ?? 0,          'color'=>'red',    'route'=>$pendRoute,    'trend'=>null],
        ['icon'=>'fas fa-check-double',  'label'=>'Approved',         'value'=>$approved ?? 0,         'color'=>'green',  'route'=>$doneRoute,    'trend'=>null],
        ['icon'=>'fas fa-rupee-sign',    'label'=>'Total Sanctioned', 'value'=>$fmtAmt((float)($totalSanctioned??0)), 'color'=>'purple', 'route'=>'#', 'trend'=>null, 'small'=>true],
    ],
    'finance' => [
        ['icon'=>'fas fa-clock',         'label'=>'Pending Payment',  'value'=>$pending ?? 0,          'color'=>'amber',  'route'=>'payment.new',     'trend'=>null],
        ['icon'=>'fas fa-check-circle',  'label'=>'Full Payments',    'value'=>$fullPayments ?? 0,      'color'=>'green',  'route'=>'payment.full',    'trend'=>null],
        ['icon'=>'fas fa-percentage',    'label'=>'Advance Payments', 'value'=>$partialPayments ?? 0,   'color'=>'blue',   'route'=>'payment.advance', 'trend'=>null],
        ['icon'=>'fas fa-rupee-sign',    'label'=>'Total Processed',  'value'=>$fmtAmt((float)($totalAmount??0)), 'color'=>'purple', 'route'=>'#', 'trend'=>null, 'small'=>true],
    ],
    default => [
        ['icon'=>'fas fa-folder-open',   'label'=>'Total Handled',    'value'=>$total ?? 0,       'color'=>'blue',   'route'=>$docListRoute, 'trend'=>null],
        ['icon'=>'fas fa-inbox',         'label'=>'Pending Review',   'value'=>$pending ?? 0,      'color'=>'amber',  'route'=>$pendRoute,    'trend'=>null],
        ['icon'=>'fas fa-check-circle',  'label'=>'Approved',         'value'=>$approved ?? $acknowledged ?? 0, 'color'=>'green', 'route'=>$doneRoute, 'trend'=>null],
        ['icon'=>'fas fa-times-circle',  'label'=>'Rejected',         'value'=>$rejected ?? 0,     'color'=>'red',    'route'=>$rejRoute,     'trend'=>null],
    ],
};
@endphp

{{-- ── Sidebar / Header ───────────────────────────────────────────────────── --}}
@if($sidebarType === 'superadmin')
    @include('frontend.superadmin.body.header')
    @include('frontend.superadmin.body.sidebar')
@elseif($sidebarType === 'admin')
    @include('frontend.admin.body.header')
    @include('frontend.admin.body.sidebar')
@else
    @include('frontend.staff.body.header')
    @include('frontend.staff.body.sidebar')
@endif

<div class="main-content">
<section class="section">

{{-- ══════════════════════════════════════════════════════════════
     HERO BANNER
═══════════════════════════════════════════════════════════════ --}}
<div class="dd-hero" style="background:linear-gradient(135deg,{{ $t['g1'] }} 0%,{{ $t['g2'] }} 100%)">
    <div class="dd-hero-glow" style="background:radial-gradient(ellipse at 20% 50%,{{ $t['ring'] }} 0%,transparent 70%)"></div>
    <div class="dd-hero-inner">
        <div class="dd-hero-left">
            <div class="dd-avatar" style="--accent:{{ $t['accent'] }};box-shadow:0 0 0 3px {{ $t['ring'] }},0 0 28px {{ $t['ring'] }}">
                {{ $initials }}
            </div>
            <div class="dd-hero-text">
                <div class="dd-role-badge" style="border-color:{{ $t['accent'] }};color:{{ $t['accent'] }}">
                    <i class="{{ $t['icon'] }} me-1"></i>{{ $t['label'] }}
                </div>
                <h1 class="dd-user-name">{{ $authUser->name }}</h1>
                <p class="dd-user-meta">
                    {{ $authUser->department }}
                    @if($authUser->designation) &bull; {{ $authUser->designation }} @endif
                </p>
                @if($authUser->division)
                <span class="dd-division-pill {{ $authUser->division === 'Clinical' ? 'clinical' : 'nonclinical' }}">
                    <i class="fas fa-circle-dot me-1" style="font-size:7px;vertical-align:middle"></i>
                    {{ $authUser->division }}
                </span>
                @endif
            </div>
        </div>
        <div class="dd-hero-stats">
            <div class="dd-hs-item">
                <div class="dd-hs-num" style="color:{{ $t['accent'] }}">{{ $total ?? 0 }}</div>
                <div class="dd-hs-lbl">Total</div>
            </div>
            <div class="dd-hs-divider"></div>
            <div class="dd-hs-item">
                <div class="dd-hs-num" style="color:#f59e0b">{{ $pending ?? $inProgress ?? 0 }}</div>
                <div class="dd-hs-lbl">Pending</div>
            </div>
            <div class="dd-hs-divider"></div>
            <div class="dd-hs-item">
                <div class="dd-hs-num" style="color:#10b981">{{ $completed ?? $approved ?? $acknowledged ?? 0 }}</div>
                <div class="dd-hs-lbl">Done</div>
            </div>
        </div>
    </div>
    <div class="dd-hero-date">
        <i class="far fa-calendar-alt me-1"></i>
        {{ now()->format('l, d M Y') }}
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     STAT CARDS
═══════════════════════════════════════════════════════════════ --}}
<div class="dd-cards-grid">
    @foreach($cards as $card)
    @php
        $colorMap = [
            'blue'   => ['bg'=>'rgba(79,142,247,0.08)',  'border'=>'#4f8ef7',  'icon'=>'rgba(79,142,247,0.15)',  'text'=>'#4f8ef7'],
            'amber'  => ['bg'=>'rgba(245,158,11,0.08)', 'border'=>'#f59e0b', 'icon'=>'rgba(245,158,11,0.15)', 'text'=>'#f59e0b'],
            'green'  => ['bg'=>'rgba(16,185,129,0.08)', 'border'=>'#10b981', 'icon'=>'rgba(16,185,129,0.15)', 'text'=>'#10b981'],
            'red'    => ['bg'=>'rgba(239,68,68,0.08)',  'border'=>'#ef4444', 'icon'=>'rgba(239,68,68,0.15)',  'text'=>'#ef4444'],
            'purple' => ['bg'=>'rgba(167,139,250,0.08)','border'=>'#a78bfa', 'icon'=>'rgba(167,139,250,0.15)','text'=>'#a78bfa'],
        ];
        $cm = $colorMap[$card['color']] ?? $colorMap['blue'];
        $routeTarget = ($card['route'] !== '#' && \Illuminate\Support\Facades\Route::has($card['route'])) ? route($card['route']) : '#';
    @endphp
    <a href="{{ $routeTarget }}" class="dd-card-link">
        <div class="dd-stat-card" style="--card-border:{{ $cm['border'] }};--card-bg:{{ $cm['bg'] }};--icon-bg:{{ $cm['icon'] }};--text-color:{{ $cm['text'] }}">
            <div class="dd-sc-icon"><i class="{{ $card['icon'] }}"></i></div>
            <div class="dd-sc-body">
                <div class="dd-sc-val {{ isset($card['small']) ? 'small' : '' }}">{{ $card['value'] }}</div>
                <div class="dd-sc-lbl">{{ $card['label'] }}</div>
            </div>
            <div class="dd-sc-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="dd-sc-glow"></div>
        </div>
    </a>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════════════
     CHARTS ROW
═══════════════════════════════════════════════════════════════ --}}
<div class="row g-4 mb-4">

    {{-- Donut: Status/Action Breakdown --}}
    <div class="col-xl-4 col-lg-5">
        <div class="dd-chart-card h-100">
            <div class="dd-cc-header">
                <div>
                    <div class="dd-cc-title">Breakdown</div>
                    <div class="dd-cc-sub">Document status distribution</div>
                </div>
                <div class="dd-cc-icon" style="background:{{ $t['ring'] }};color:{{ $t['accent'] }}">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="dd-donut-wrap">
                <canvas id="donutChart"></canvas>
                <div class="dd-donut-center">
                    <div class="dd-dc-num" style="color:{{ $t['accent'] }}">{{ $total ?? 0 }}</div>
                    <div class="dd-dc-lbl">Total</div>
                </div>
            </div>
            <div class="dd-legend" id="donutLegend"></div>
        </div>
    </div>

    {{-- Bar / Area: Monthly Trend --}}
    <div class="col-xl-8 col-lg-7">
        <div class="dd-chart-card h-100">
            <div class="dd-cc-header">
                <div>
                    <div class="dd-cc-title">
                        @if($viewType === 'finance') Monthly Payment Volume (₹)
                        @else Monthly Document Activity @endif
                    </div>
                    <div class="dd-cc-sub">Last 6 months</div>
                </div>
                <div class="dd-cc-icon" style="background:{{ $t['ring'] }};color:{{ $t['accent'] }}">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
            <div class="dd-bar-wrap">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Chairman: Sanctioned Amount Trend --}}
@if($viewType === 'chairman' && isset($monthlySanctioned) && $monthlySanctioned->isNotEmpty())
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="dd-chart-card">
            <div class="dd-cc-header">
                <div>
                    <div class="dd-cc-title">Monthly Sanctioned Amount</div>
                    <div class="dd-cc-sub">₹ value of documents approved — last 6 months</div>
                </div>
                <div class="dd-cc-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b">
                    <i class="fas fa-rupee-sign"></i>
                </div>
            </div>
            <div style="position:relative;height:180px;"><canvas id="amountChart"></canvas></div>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════
     RECENT DOCUMENTS TABLE
═══════════════════════════════════════════════════════════════ --}}
<div class="dd-chart-card">
    <div class="dd-cc-header">
        <div>
            <div class="dd-cc-title">
                @if(in_array($viewType, ['hod','staff'])) Active Documents
                @else Pending for Action @endif
            </div>
            <div class="dd-cc-sub">Latest documents requiring attention</div>
        </div>
        @if($pendRoute !== '#')
        <a href="{{ route($pendRoute === '#' ? 'approval_flowchart' : $pendRoute) }}" class="dd-view-all" style="color:{{ $t['accent'] }}">
            View All <i class="fas fa-arrow-right ms-1"></i>
        </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="dd-table">
            <thead>
                <tr>
                    <th width="36">#</th>
                    <th>Document ID</th>
                    <th>Title / Subject</th>
                    <th>From Dept</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Age</th>
                    <th width="60">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDocs as $i => $doc)
                @php
                    $st  = $doc->status ?? 'New';
                    $stc = match(strtolower(str_replace(' ', '', $st))) {
                        'new'           => 'st-new',
                        'inprogress'    => 'st-progress',
                        'approved'      => 'st-approved',
                        'completed',
                        'closed'        => 'st-done',
                        'rejected'      => 'st-rejected',
                        'hold'          => 'st-hold',
                        default         => 'st-new',
                    };
                    $pr  = strtolower($doc->priority ?? 'normal');
                    $prc = match($pr) {
                        'high','urgent' => 'pr-high',
                        'medium'        => 'pr-med',
                        'low'           => 'pr-low',
                        default         => 'pr-normal',
                    };
                    $days = $doc->created_at ? (int) now()->diffInDays($doc->created_at) : 0;
                    $dayClass = $days > 7 ? 'age-over' : ($days > 3 ? 'age-warn' : 'age-ok');
                @endphp
                <tr>
                    <td class="dd-idx">{{ $i + 1 }}</td>
                    <td><span class="dd-docid">{{ $doc->doc_id ?? ('DOC-' . str_pad($doc->id, 4, '0', STR_PAD_LEFT)) }}</span></td>
                    <td>
                        <div class="dd-doc-title">{{ \Illuminate\Support\Str::limit($doc->title ?? $doc->subject ?? 'Untitled', 42) }}</div>
                        @if($doc->amount)
                        <div class="dd-doc-amount">₹{{ number_format($doc->amount) }}</div>
                        @endif
                    </td>
                    <td><span class="dd-dept-tag">{{ $doc->from }}</span></td>
                    <td><span class="dd-status {{ $stc }}">{{ $st }}</span></td>
                    <td><span class="dd-priority {{ $prc }}">{{ ucfirst($pr) }}</span></td>
                    <td><span class="dd-age {{ $dayClass }}">{{ $days }}d</span></td>
                    <td>
                        <a href="{{ url('/view/document/' . $doc->id) }}" class="dd-eye" style="color:{{ $t['accent'] }}" title="View Document">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="dd-empty">
                            <i class="fas fa-inbox"></i>
                            <p>No pending documents</p>
                            <span>All documents have been actioned</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</section>
</div>

{{-- ══════════════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════════════ --}}
<style>
:root {
    --dd-radius: 14px;
    --dd-card-bg: #ffffff;
    --dd-border: #e8edf5;
    --dd-shadow: 0 1px 4px rgba(15,23,42,0.06), 0 4px 16px rgba(15,23,42,0.04);
    --dd-shadow-hover: 0 4px 12px rgba(15,23,42,0.1), 0 12px 32px rgba(15,23,42,0.08);
    --dd-text: #0f172a;
    --dd-muted: #64748b;
    --dd-table-head: #f8fafc;
    --dd-page-bg: #f5f7fb;
}

/* ── Layout ─────────────────────────────────────────────────── */
.main-content { background: #ffffff; min-height: 100vh; }
.section { padding: 0 0 40px; }

/* ── Hero ───────────────────────────────────────────────────── */
.dd-hero {
    position: relative;
    overflow: hidden;
    padding: 36px 40px 28px;
    margin-bottom: 28px;
    border-radius: 0 0 28px 28px;
}
.dd-hero-glow {
    position: absolute; inset: 0; pointer-events: none;
}
.dd-hero-inner {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}
.dd-hero-left {
    display: flex;
    align-items: center;
    gap: 20px;
}
.dd-avatar {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    border: 2px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 700; color: #fff;
    letter-spacing: 1px;
    flex-shrink: 0;
    transition: transform 0.3s;
}
.dd-avatar:hover { transform: scale(1.06); }
.dd-hero-text {}
.dd-role-badge {
    display: inline-flex;
    align-items: center;
    border: 1px solid;
    border-radius: 30px;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(4px);
    margin-bottom: 6px;
}
.dd-user-name {
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 2px;
    line-height: 1.2;
}
.dd-user-meta {
    color: rgba(255,255,255,0.65);
    font-size: 13px;
    margin: 0 0 8px;
}
.dd-division-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.dd-division-pill.clinical { background: rgba(34,197,94,0.18); color: #4ade80; }
.dd-division-pill.nonclinical { background: rgba(100,116,139,0.25); color: #94a3b8; }
.dd-hero-stats {
    display: flex;
    align-items: center;
    gap: 0;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 14px 24px;
}
.dd-hs-item { text-align: center; padding: 0 20px; }
.dd-hs-num { font-size: 26px; font-weight: 800; line-height: 1; }
.dd-hs-lbl { font-size: 11px; color: rgba(255,255,255,0.55); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
.dd-hs-divider { width: 1px; height: 40px; background: rgba(255,255,255,0.12); }
.dd-hero-date {
    position: relative;
    margin-top: 16px;
    font-size: 12px;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.3px;
}

/* ── Stat Cards ─────────────────────────────────────────────── */
.dd-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    padding: 0 24px;
    margin-bottom: 28px;
}
@media(max-width:1200px){ .dd-cards-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:576px) { .dd-cards-grid { grid-template-columns: 1fr; } }

.dd-card-link { text-decoration: none; }
.dd-stat-card {
    background: var(--card-bg, rgba(255,255,255,0.9));
    border: 1px solid var(--card-border, #e2e8f0);
    border-left: 4px solid var(--card-border);
    border-radius: var(--dd-radius);
    padding: 24px 20px 20px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    box-shadow: var(--dd-shadow);
    transition: transform 0.22s cubic-bezier(.34,1.56,.64,1), box-shadow 0.22s;
    backdrop-filter: blur(2px);
}
.dd-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--dd-shadow-hover);
}
.dd-sc-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: var(--icon-bg);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    color: var(--text-color);
    flex-shrink: 0;
}
.dd-sc-body { flex: 1; }
.dd-sc-val {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-color);
    line-height: 1;
    margin-bottom: 4px;
}
.dd-sc-val.small { font-size: 22px; }
.dd-sc-lbl { font-size: 12px; color: var(--dd-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.4px; }
.dd-sc-arrow { color: var(--text-color); opacity: 0; font-size: 13px; transition: opacity 0.2s, transform 0.2s; margin-top: 4px; }
.dd-stat-card:hover .dd-sc-arrow { opacity: 0.7; transform: translateX(3px); }
.dd-sc-glow {
    position: absolute; top: -20px; right: -20px;
    width: 100px; height: 100px;
    border-radius: 50%;
    background: var(--icon-bg);
    filter: blur(24px);
    pointer-events: none;
}

/* ── Chart Cards ────────────────────────────────────────────── */
.dd-chart-card {
    background: var(--dd-card-bg);
    border: 1px solid var(--dd-border);
    border-radius: var(--dd-radius);
    padding: 24px;
    box-shadow: var(--dd-shadow);
    margin: 0 24px 24px;
}
.row.g-4 .dd-chart-card { margin: 0; }
.dd-cc-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 24px;
}
.dd-cc-title { font-size: 15px; font-weight: 700; color: var(--dd-text); }
.dd-cc-sub { font-size: 12px; color: var(--dd-muted); margin-top: 2px; }
.dd-cc-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.row.g-4 { padding: 0 24px; }

/* ── Donut ──────────────────────────────────────────────────── */
/* IMPORTANT: do NOT add display:flex or !important sizing on canvas —
   Chart.js reads the container dimensions and sets canvas attributes itself.
   Only the container needs position:relative + explicit height. */
.dd-donut-wrap {
    position: relative;
    height: 220px;
    margin-bottom: 20px;
}
.dd-donut-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
    z-index: 1;
}
.dd-dc-num { font-size: 30px; font-weight: 800; line-height: 1; }
.dd-dc-lbl { font-size: 11px; color: var(--dd-muted); text-transform: uppercase; letter-spacing: 0.4px; }

.dd-legend { display: flex; flex-direction: column; gap: 8px; }
.dd-legend-item {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 12px;
}
.dd-legend-left { display: flex; align-items: center; gap: 8px; }
.dd-legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.dd-legend-name { color: var(--dd-text); font-weight: 500; }
.dd-legend-val { display: flex; align-items: center; gap: 6px; }
.dd-legend-count { font-weight: 700; color: var(--dd-text); }
.dd-legend-pct { color: var(--dd-muted); font-size: 11px; }

/* ── Bar chart wrapper ──────────────────────────────────────── */
.dd-bar-wrap { position: relative; height: 240px; }

/* ── Table ──────────────────────────────────────────────────── */
.dd-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
}
.dd-table thead th {
    background: var(--dd-table-head);
    color: var(--dd-muted);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 14px;
    border-bottom: 1px solid var(--dd-border);
    white-space: nowrap;
}
.dd-table thead th:first-child { border-radius: 8px 0 0 8px; }
.dd-table thead th:last-child  { border-radius: 0 8px 8px 0; }
.dd-table tbody tr {
    border-bottom: 1px solid var(--dd-border);
    transition: background 0.15s;
}
.dd-table tbody tr:hover { background: #f8fafc; }
.dd-table tbody tr:last-child { border-bottom: none; }
.dd-table td { padding: 12px 14px; color: var(--dd-text); vertical-align: middle; }
.dd-idx { color: var(--dd-muted); font-weight: 600; }
.dd-docid {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 2px 7px;
    color: #475569;
    white-space: nowrap;
}
.dd-doc-title { font-weight: 500; color: var(--dd-text); line-height: 1.3; }
.dd-doc-amount { font-size: 11px; color: #10b981; font-weight: 600; margin-top: 2px; }
.dd-dept-tag {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    color: #475569;
    white-space: nowrap;
}

/* Status chips */
.dd-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.st-new      { background: rgba(99,102,241,0.1);  color: #6366f1; }
.st-progress { background: rgba(245,158,11,0.1);  color: #d97706; }
.st-approved { background: rgba(16,185,129,0.1);  color: #059669; }
.st-done     { background: rgba(100,116,139,0.1); color: #475569; }
.st-rejected { background: rgba(239,68,68,0.1);   color: #dc2626; }
.st-hold     { background: rgba(249,115,22,0.1);  color: #ea580c; }

/* Priority chips */
.dd-priority {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.pr-high   { background: rgba(239,68,68,0.1);   color: #dc2626; }
.pr-med    { background: rgba(245,158,11,0.1);  color: #d97706; }
.pr-normal { background: rgba(79,142,247,0.1);  color: #2563eb; }
.pr-low    { background: rgba(16,185,129,0.1);  color: #059669; }

/* Age badge */
.dd-age {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}
.age-ok   { background: rgba(16,185,129,0.08); color: #059669; }
.age-warn { background: rgba(245,158,11,0.1);  color: #d97706; }
.age-over { background: rgba(239,68,68,0.1);   color: #dc2626; }

/* Eye button */
.dd-eye {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    transition: background 0.2s, transform 0.15s;
    font-size: 13px;
}
.dd-eye:hover { background: #e2e8f0; transform: scale(1.1); }

/* Empty state */
.dd-empty {
    text-align: center;
    padding: 48px 20px;
    color: var(--dd-muted);
}
.dd-empty i { font-size: 40px; opacity: 0.2; margin-bottom: 12px; display: block; }
.dd-empty p { font-size: 14px; font-weight: 600; margin: 0 0 4px; }
.dd-empty span { font-size: 12px; }

/* View all link */
.dd-view-all {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    transition: opacity 0.2s;
}
.dd-view-all:hover { opacity: 0.75; }

/* ── Navbar fixes ───────────────────────────────────────────── */
/* Hamburger icon */
.main-navbar .nav-link-lg i,
.main-navbar [data-toggle="sidebar"] i {
    color: #495057 !important;
}
.main-navbar [data-toggle="sidebar"]:hover i { color: #1e293b !important; }
/* Username text — white text is invisible on a light navbar */
.main-navbar .nav-link-user .d-sm-none,
.main-navbar .nav-link-user div,
.navbar-nav .nav-link-user span {
    color: #1e293b !important;
}
.main-navbar .nav-link-user:hover .d-sm-none,
.main-navbar .nav-link-user:hover div {
    color: #2563eb !important;
}

/* ── Mobile responsive fixes ────────────────────────────────── */
@media (max-width: 768px) {
    .dd-hero { padding: 24px 20px 20px; border-radius: 0 0 20px 20px; }
    .dd-hero-inner { flex-direction: column; }
    .dd-hero-stats { width: 100%; justify-content: center; }
    .dd-hs-item { padding: 0 14px; }
    .dd-hs-num { font-size: 20px; }
    .dd-user-name { font-size: 18px; }
    .dd-cards-grid { padding: 0 16px; gap: 14px; }
    .dd-chart-card { margin-left: 16px; margin-right: 16px; }
    .row.g-4 { padding: 0 16px; }
    .dd-bar-wrap  { height: 200px; }
    .dd-donut-wrap{ height: 180px; }
    .dd-table { font-size: 12px; }
    .dd-table thead th,
    .dd-table td { padding: 8px 10px; }
    .dd-doc-title { max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
}
</style>

{{-- ══════════════════════════════════════════════════════════════
     CHART SCRIPTS
═══════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Data from PHP ────────────────────────────────────────── */
    const breakdownRaw = @json($breakdown instanceof \Illuminate\Support\Collection ? $breakdown->toArray() : (is_array($breakdown) ? $breakdown : []));
    const monthlyRaw   = @json($monthly ?? []);
    const accent       = '{{ $t['accent'] }}';
    /* Professional blue-forward semantic palette:
       Blue=New/Primary, Amber=Pending/Warning, Green=Done/Approved,
       Red=Rejected, Violet=Misc, Cyan=Other */
    const colors = ['#2563eb','#f59e0b','#10b981','#ef4444','#7c3aed','#0891b2'];

    @if($viewType === 'chairman' && isset($monthlySanctioned))
    const monthlySanctionedRaw = @json($monthlySanctioned ?? []);
    @endif

    /* ── Chart.js global defaults ─────────────────────────────── */
    Chart.defaults.font.family = "'Inter','Segoe UI',sans-serif";
    Chart.defaults.color       = '#94a3b8';

    /* ── Donut ────────────────────────────────────────────────── */
    (function buildDonut() {
        const labels = Object.keys(breakdownRaw);
        const data   = Object.values(breakdownRaw);
        const total  = data.reduce((a, b) => a + b, 0);

        const ctx = document.getElementById('donutChart');
        if (!ctx) return;

        if (!labels.length) {
            /* Show an empty-state ring when there's no data yet */
            new Chart(ctx, {
                type: 'doughnut',
                data: { labels: ['No Data'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0 }] },
                options: {
                    cutout: '72%', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });
            const leg = document.getElementById('donutLegend');
            if (leg) leg.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:12px;padding:8px 0;">No documents yet</div>';
            return;
        }

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors.slice(0, labels.length),
                    hoverBackgroundColor: colors.slice(0, labels.length).map(c => c + 'dd'),
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverBorderWidth: 4,
                    hoverOffset: 8,
                }]
            },
            options: {
                cutout: '72%',
                responsive: true,
                maintainAspectRatio: false,
                animation: { animateRotate: true, duration: 900, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => {
                                const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.parsed} docs (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        /* Custom HTML legend */
        const legendEl = document.getElementById('donutLegend');
        if (legendEl) {
            labels.forEach((lbl, i) => {
                const cnt = data[i] || 0;
                const pct = total > 0 ? ((cnt / total) * 100).toFixed(1) : 0;
                const item = document.createElement('div');
                item.className = 'dd-legend-item';
                item.innerHTML = `
                    <div class="dd-legend-left">
                        <div class="dd-legend-dot" style="background:${colors[i]}"></div>
                        <span class="dd-legend-name">${lbl}</span>
                    </div>
                    <div class="dd-legend-val">
                        <span class="dd-legend-count">${cnt}</span>
                        <span class="dd-legend-pct">${pct}%</span>
                    </div>`;
                legendEl.appendChild(item);
            });
        }
    })();

    /* ── Bar Chart ────────────────────────────────────────────── */
    (function buildBar() {
        const ctx = document.getElementById('barChart');
        if (!ctx) return;

        const labels = monthlyRaw.map(r => r.month);
        const values = monthlyRaw.map(r => parseFloat(r.value) || 0);
        const isAmt  = '{{ $viewType }}' === 'finance';

        /* Professional blue gradient for bars — consistent across all roles */
        const barBlue = '#2563eb';
        const gradFill = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
        gradFill.addColorStop(0, barBlue + 'dd');
        gradFill.addColorStop(1, barBlue + '22');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: isAmt ? 'Amount (₹)' : 'Documents',
                    data: values,
                    backgroundColor: gradFill,
                    borderColor: barBlue,
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false,
                    hoverBackgroundColor: barBlue + 'ff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 900, easing: 'easeInOutQuart' },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        border: { dash: [4,4], display: false },
                        grid: { color: 'rgba(100,116,139,0.1)' },
                        ticks: {
                            font: { size: 11 },
                            callback: v => isAmt ? '₹' + (v >= 100000 ? (v/100000).toFixed(1)+'L' : v.toLocaleString()) : v,
                            maxTicksLimit: 5,
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => isAmt
                                ? ` ₹${Number(ctx.parsed.y).toLocaleString()}`
                                : ` ${ctx.parsed.y} document${ctx.parsed.y !== 1 ? 's' : ''}`
                        }
                    }
                }
            }
        });
    })();

    @if($viewType === 'chairman' && isset($monthlySanctioned))
    /* ── Chairman: Sanctioned Amount Area Chart ───────────────── */
    (function buildAmountChart() {
        const ctx = document.getElementById('amountChart');
        if (!ctx) return;

        const labels = monthlySanctionedRaw.map(r => r.month);
        const values = monthlySanctionedRaw.map(r => parseFloat(r.value) || 0);

        const grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 180);
        grad.addColorStop(0, '#f59e0b88');
        grad.addColorStop(1, '#f59e0b08');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Sanctioned (₹)',
                    data: values,
                    borderColor: '#f59e0b',
                    borderWidth: 2.5,
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.42,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 900 },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        border: { dash: [4,4], display: false },
                        grid: { color: 'rgba(100,116,139,0.1)' },
                        ticks: {
                            callback: v => '₹' + (v >= 100000 ? (v/100000).toFixed(1)+'L' : v.toLocaleString()),
                            maxTicksLimit: 5,
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => ` ₹${Number(ctx.parsed.y).toLocaleString()}`
                        }
                    }
                }
            }
        });
    })();
    @endif

    /* ── Animate stat card numbers ────────────────────────────── */
    document.querySelectorAll('.dd-sc-val:not(.small)').forEach(el => {
        const raw = el.textContent.trim();
        const num = parseInt(raw.replace(/[^0-9]/g, ''), 10);
        if (isNaN(num) || num === 0) return;
        let start = 0;
        const step = Math.ceil(num / 40);
        const timer = setInterval(() => {
            start = Math.min(start + step, num);
            el.textContent = start.toLocaleString();
            if (start >= num) clearInterval(timer);
        }, 18);
    });

})();
</script>
@endpush

@endsection
