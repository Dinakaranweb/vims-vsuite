@extends('frontend.frontend_master')
@section('content')

@include('frontend.superadmin.body.header')
@include('frontend.superadmin.body.sidebar')

<div class="main-content">
<section class="section">

{{-- Page Header --}}
<div class="api-page-header">
    <div class="api-ph-left">
        <div class="api-ph-icon"><i class="fas fa-key"></i></div>
        <div>
            <h1>API Management</h1>
            <p>Create and manage API tokens for cross-system integration</p>
        </div>
    </div>
    <a href="#create-section" class="api-btn-primary">
        <i class="fas fa-plus me-2"></i> New Token
    </a>
</div>

{{-- Alerts --}}
@if(session('new_api_token'))
<div class="api-token-reveal">
    <div class="api-tr-header">
        <i class="fas fa-shield-check me-2"></i>
        Token Created — Copy it now. This will <strong>never be shown again.</strong>
    </div>
    <div class="api-tr-body">
        <code id="newTokenVal">{{ session('new_api_token') }}</code>
        <button onclick="copyToken()" class="api-copy-btn"><i class="fas fa-copy me-1"></i>Copy</button>
    </div>
</div>
@endif

@if(session('success') && !session('new_api_token'))
<div class="api-alert success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
@endif

{{-- Stats Row --}}
<div class="api-stats-row">
    @foreach([
        ['Total Tokens',  $stats['total'],   'fas fa-key',          'blue'],
        ['Active',        $stats['active'],  'fas fa-check-circle', 'green'],
        ['Expiring Soon', $stats['expiring'],'fas fa-clock',        'amber'],
        ['Expired',       $stats['expired'], 'fas fa-ban',          'red'],
    ] as $s)
    <div class="api-stat api-stat-{{ $s[3] }}">
        <div class="api-stat-icon"><i class="{{ $s[2] }}"></i></div>
        <div class="api-stat-num">{{ $s[1] }}</div>
        <div class="api-stat-lbl">{{ $s[0] }}</div>
    </div>
    @endforeach
</div>

{{-- Main Grid --}}
<div class="api-grid">

    {{-- LEFT: Create Form + Existing Tokens --}}
    <div class="api-left">

        {{-- Create Token --}}
        <div class="api-card" id="create-section">
            <div class="api-card-hd">
                <i class="fas fa-plus-circle me-2"></i> Create New Token
            </div>
            <form method="POST" action="{{ route('api.tokens.store') }}">
                @csrf
                <div class="api-form-group">
                    <label>Token Name / Label</label>
                    <input type="text" name="name" class="api-input" placeholder="e.g. VSuite-Branch-Chairman" required>
                    <span class="api-hint">A descriptive name to identify where this token is used</span>
                </div>
                <div class="api-form-group">
                    <label>Assign to User</label>
                    <select name="user_id" class="api-input" required>
                        <option value="">— Select User —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->department }} · {{ $u->role }})</option>
                        @endforeach
                    </select>
                    <span class="api-hint">The user whose identity this token represents</span>
                </div>
                <div class="api-form-group">
                    <label>Validity</label>
                    <select name="expires_days" class="api-input">
                        <option value="7">7 days</option>
                        <option value="30" selected>30 days</option>
                        <option value="90">90 days</option>
                        <option value="180">180 days</option>
                        <option value="365">1 year</option>
                    </select>
                </div>
                <button type="submit" class="api-btn-primary w-100">
                    <i class="fas fa-key me-2"></i> Generate Token
                </button>
            </form>
        </div>

        {{-- Token List --}}
        <div class="api-card">
            <div class="api-card-hd">
                <i class="fas fa-list me-2"></i> Existing Tokens
            </div>
            @forelse($tokens as $token)
            @php $expired = $token->isExpired(); @endphp
            <div class="api-token-row {{ $expired ? 'expired' : '' }}">
                <div class="api-tr-info">
                    <div class="api-tr-name">{{ $token->name }}</div>
                    <div class="api-tr-meta">
                        <span><i class="fas fa-user me-1"></i>{{ $token->user->name ?? '?' }}</span>
                        <span><i class="fas fa-building me-1"></i>{{ $token->user->department ?? '—' }}</span>
                        <span><i class="far fa-calendar me-1"></i>
                            Expires {{ $token->expires_at ? $token->expires_at->format('d M Y') : 'Never' }}
                        </span>
                        @if($token->last_used_at)
                        <span><i class="fas fa-activity me-1"></i>Last used {{ $token->last_used_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                <div class="api-tr-actions">
                    <span class="api-badge {{ $expired ? 'badge-red' : 'badge-green' }}">
                        {{ $expired ? 'Expired' : 'Active' }}
                    </span>
                    <form method="POST" action="{{ route('api.tokens.destroy', $token->id) }}" onsubmit="return confirm('Revoke this token?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="api-btn-revoke" title="Revoke">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="api-empty">
                <i class="fas fa-key"></i>
                <p>No tokens yet</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- RIGHT: Instructions --}}
    <div class="api-right">

        {{-- Tab Nav --}}
        <div class="api-tabs">
            <button class="api-tab active" onclick="showTab('auth')">Authentication</button>
            <button class="api-tab" onclick="showTab('endpoints')">Endpoints</button>
            <button class="api-tab" onclick="showTab('crossvs')">Cross-VSuite</button>
            <button class="api-tab" onclick="showTab('examples')">Code</button>
        </div>

        {{-- Auth Tab --}}
        <div id="tab-auth" class="api-tab-panel active">
            <div class="api-card">
                <div class="api-card-hd"><i class="fas fa-lock me-2"></i>Authentication</div>
                <p class="api-desc">All protected API endpoints require a Bearer token in the request header.</p>

                <div class="api-info-block">
                    <div class="api-ib-label">Base URL</div>
                    <code>{{ url('/api/v1') }}</code>
                </div>

                <div class="api-ib-label mt-3">Required Header</div>
                <div class="api-code-block">
Authorization: Bearer &lt;your-token&gt;
Content-Type: application/json</div>

                <div class="api-ib-label mt-3">Step 1 — Get a token (cross-VSuite only)</div>
                <div class="api-code-block">POST {{ url('/api/v1') }}/cross-auth/chairman

{
  "email": "chairman@hospital.com",
  "source_app": "VSuite-Branch"
}</div>

                <div class="api-ib-label mt-3">Response</div>
                <div class="api-code-block">{
  "success": true,
  "data": {
    "token": "abc...48chars",
    "token_type": "Bearer",
    "expires_at": "2026-07-14T00:00:00Z",
    "user": { "name": "Dr. Vijayakumar", "department": "Chairman" }
  }
}</div>
                <p class="api-tip"><i class="fas fa-info-circle me-1"></i>
                    Store this token in your app's <code>.env</code> file and reuse it until expiry.
                    Re-authenticate only when the token expires (30-day default).
                </p>
            </div>
        </div>

        {{-- Endpoints Tab --}}
        <div id="tab-endpoints" class="api-tab-panel">
            <div class="api-card">
                <div class="api-card-hd"><i class="fas fa-plug me-2"></i>API Endpoints</div>

                <div class="api-endpoint">
                    <span class="ep-method post">POST</span>
                    <span class="ep-path">/cross-auth/chairman</span>
                    <span class="ep-badge public">Public</span>
                    <p class="ep-desc">Verify Chairman email and receive a Bearer token. Used by remote VSuite instances.</p>
                </div>
                <div class="api-endpoint">
                    <span class="ep-method post">POST</span>
                    <span class="ep-path">/documents/{id}/chairman-approve</span>
                    <span class="ep-badge auth">Auth Required</span>
                    <p class="ep-desc">Approve a document as Chairman. Optionally choose the Finance Head to forward to.</p>
                    <div class="ep-params">
                        <div><code>remarks</code> <em>string, optional</em> — Approval notes</div>
                        <div><code>finance_head</code> <em>string, optional</em> — Override Finance Head department</div>
                    </div>
                </div>
                <div class="api-endpoint">
                    <span class="ep-method get">GET</span>
                    <span class="ep-path">/auth/login</span>
                    <span class="ep-badge public">Public</span>
                    <p class="ep-desc">Standard login — returns Bearer token for regular API access.</p>
                </div>
            </div>
        </div>

        {{-- Cross-VSuite Tab --}}
        <div id="tab-crossvs" class="api-tab-panel">
            <div class="api-card">
                <div class="api-card-hd"><i class="fas fa-exchange-alt me-2"></i>Cross-VSuite Integration</div>
                <p class="api-desc">Connect a second V-Suite application to this instance so the Chairman can approve documents from either system.</p>

                <div class="api-step">
                    <div class="api-step-num">1</div>
                    <div>
                        <strong>Add to the second app's <code>.env</code></strong>
                        <div class="api-code-block">VSUITE_API_URL={{ url('/api/v1') }}
VSUITE_API_TOKEN=   # paste token here after generating</div>
                    </div>
                </div>
                <div class="api-step">
                    <div class="api-step-num">2</div>
                    <div>
                        <strong>Add to <code>config/services.php</code></strong>
                        <div class="api-code-block">'vsuite' => [
    'url'   => env('VSUITE_API_URL'),
    'token' => env('VSUITE_API_TOKEN'),
],</div>
                    </div>
                </div>
                <div class="api-step">
                    <div class="api-step-num">3</div>
                    <div>
                        <strong>Trust anchor</strong> — both apps must have a user with <strong>the same Chairman email</strong>. The email match is the identity verification.
                    </div>
                </div>
                <div class="api-step">
                    <div class="api-step-num">4</div>
                    <div>
                        <strong>Token lifecycle</strong>
                        <ul class="api-list">
                            <li>30-day validity by default (configurable above)</li>
                            <li>Re-authenticate before expiry</li>
                            <li><code>401</code> response → revoke old + generate new token</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Code Examples Tab --}}
        <div id="tab-examples" class="api-tab-panel">
            <div class="api-card">
                <div class="api-card-hd"><i class="fas fa-code me-2"></i>Code Examples</div>

                <div class="api-ib-label">PHP (Laravel Http client)</div>
                <div class="api-code-block">use Illuminate\Support\Facades\Http;

// Authenticate
$res = Http::post('{{ url('/api/v1') }}/cross-auth/chairman', [
    'email'      => 'chairman@hospital.com',
    'source_app' => 'VSuite-Branch',
]);
$token = $res->json('data.token');

// Approve a document
Http::withToken($token)
    ->post('{{ url('/api/v1') }}/documents/42/chairman-approve', [
        'remarks'      => 'Approved as per committee',
        'finance_head' => 'Finance Head Salem', // optional
    ]);</div>

                <div class="api-ib-label mt-3">cURL</div>
                <div class="api-code-block"># Step 1: Get token
curl -X POST {{ url('/api/v1') }}/cross-auth/chairman \
  -H "Content-Type: application/json" \
  -d '{"email":"chairman@hospital.com","source_app":"Branch"}'

# Step 2: Approve
curl -X POST {{ url('/api/v1') }}/documents/42/chairman-approve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"remarks":"Approved"}'</div>

                <div class="api-ib-label mt-3">JavaScript (fetch)</div>
                <div class="api-code-block">const BASE = '{{ url('/api/v1') }}';

const { data } = await fetch(`${BASE}/cross-auth/chairman`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email: 'chairman@hospital.com' }),
}).then(r => r.json());

await fetch(`${BASE}/documents/42/chairman-approve`, {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${data.token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ remarks: 'Approved' }),
});</div>
            </div>
        </div>

    </div>{{-- /api-right --}}
</div>{{-- /api-grid --}}

</section>
</div>

<style>
:root {
    --api-radius: 14px;
    --api-bg: #f1f5f9;
    --api-card: #fff;
    --api-border: rgba(0,0,0,0.07);
    --api-shadow: 0 2px 16px rgba(0,0,0,0.06);
    --api-text: #1e293b;
    --api-muted: #64748b;
    --api-blue: #3b82f6;
    --api-green: #10b981;
    --api-amber: #f59e0b;
    --api-red: #ef4444;
    --api-purple: #8b5cf6;
}

.main-content { background: var(--api-bg); min-height: 100vh; }
.section { padding: 0 0 40px; }

/* ── Page Header ─────────────────────────────────────────────── */
.api-page-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 28px 32px 20px;
    flex-wrap: wrap; gap: 16px;
}
.api-ph-left { display: flex; align-items: center; gap: 16px; }
.api-ph-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg,#3b82f6,#6366f1);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff;
    box-shadow: 0 4px 14px rgba(59,130,246,0.3);
}
.api-page-header h1 { font-size: 22px; font-weight: 800; color: var(--api-text); margin: 0 0 2px; }
.api-page-header p { font-size: 13px; color: var(--api-muted); margin: 0; }

/* ── Alerts ──────────────────────────────────────────────────── */
.api-token-reveal {
    margin: 0 32px 20px;
    border-radius: var(--api-radius);
    background: #0f172a;
    border: 1px solid #22c55e44;
    overflow: hidden;
}
.api-tr-header {
    background: rgba(34,197,94,0.12);
    padding: 10px 18px;
    font-size: 13px; font-weight: 600; color: #4ade80;
    border-bottom: 1px solid #22c55e22;
}
.api-tr-body {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px;
}
.api-tr-body code {
    flex: 1; font-size: 13px; color: #f1fa8c;
    word-break: break-all; font-family: 'Courier New', monospace;
}
.api-copy-btn {
    background: #22c55e; color: #fff; border: none;
    padding: 7px 16px; border-radius: 8px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    white-space: nowrap; transition: background 0.2s;
}
.api-copy-btn:hover { background: #16a34a; }
.api-alert {
    margin: 0 32px 16px;
    padding: 12px 18px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
}
.api-alert.success { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }

/* ── Stats Row ───────────────────────────────────────────────── */
.api-stats-row {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 16px; padding: 0 32px 24px;
}
@media(max-width:900px){ .api-stats-row { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px){ .api-stats-row { grid-template-columns: 1fr; } }

.api-stat {
    background: var(--api-card);
    border: 1px solid var(--api-border);
    border-radius: var(--api-radius);
    padding: 20px;
    display: flex; flex-direction: column; align-items: flex-start;
    gap: 6px;
    box-shadow: var(--api-shadow);
    border-left: 4px solid;
    transition: transform 0.2s;
}
.api-stat:hover { transform: translateY(-3px); }
.api-stat-blue   { border-left-color: var(--api-blue); }
.api-stat-green  { border-left-color: var(--api-green); }
.api-stat-amber  { border-left-color: var(--api-amber); }
.api-stat-red    { border-left-color: var(--api-red); }
.api-stat-icon { font-size: 18px; opacity: 0.5; }
.api-stat-blue   .api-stat-icon { color: var(--api-blue); }
.api-stat-green  .api-stat-icon { color: var(--api-green); }
.api-stat-amber  .api-stat-icon { color: var(--api-amber); }
.api-stat-red    .api-stat-icon { color: var(--api-red); }
.api-stat-num { font-size: 30px; font-weight: 800; color: var(--api-text); line-height: 1; }
.api-stat-lbl { font-size: 12px; color: var(--api-muted); text-transform: uppercase; letter-spacing: 0.4px; }

/* ── Main Grid ───────────────────────────────────────────────── */
.api-grid {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 24px;
    padding: 0 32px;
}
@media(max-width:1100px){ .api-grid { grid-template-columns: 1fr; } }

/* ── Cards ───────────────────────────────────────────────────── */
.api-card {
    background: var(--api-card);
    border: 1px solid var(--api-border);
    border-radius: var(--api-radius);
    padding: 24px;
    box-shadow: var(--api-shadow);
    margin-bottom: 20px;
}
.api-card-hd {
    font-size: 14px; font-weight: 700; color: var(--api-text);
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--api-border);
}

/* ── Form ────────────────────────────────────────────────────── */
.api-form-group { margin-bottom: 16px; }
.api-form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--api-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; }
.api-input {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px; color: var(--api-text);
    background: #f8fafc;
    transition: border-color 0.2s;
    outline: none;
    appearance: none;
}
.api-input:focus { border-color: var(--api-blue); background: #fff; }
.api-hint { font-size: 11px; color: var(--api-muted); margin-top: 4px; display: block; }

.api-btn-primary {
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg,#3b82f6,#6366f1);
    color: #fff; border: none;
    padding: 11px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: opacity 0.2s, transform 0.15s;
    box-shadow: 0 3px 12px rgba(59,130,246,0.3);
}
.api-btn-primary:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
.api-btn-primary.w-100 { width: 100%; }

/* ── Token List ──────────────────────────────────────────────── */
.api-token-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid var(--api-border);
    gap: 12px;
}
.api-token-row:last-child { border-bottom: none; }
.api-token-row.expired { opacity: 0.55; }
.api-tr-name { font-size: 13px; font-weight: 600; color: var(--api-text); }
.api-tr-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; font-size: 11px; color: var(--api-muted); }
.api-tr-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.api-badge {
    display: inline-block; padding: 3px 10px;
    border-radius: 20px; font-size: 11px; font-weight: 600;
}
.badge-green { background: rgba(16,185,129,0.1); color: #059669; }
.badge-red   { background: rgba(239,68,68,0.1);  color: #dc2626; }
.api-btn-revoke {
    background: rgba(239,68,68,0.08); color: var(--api-red);
    border: 1px solid rgba(239,68,68,0.2);
    width: 30px; height: 30px; border-radius: 8px;
    cursor: pointer; font-size: 12px;
    transition: background 0.2s;
    display: flex; align-items: center; justify-content: center;
}
.api-btn-revoke:hover { background: rgba(239,68,68,0.15); }

.api-empty { text-align: center; padding: 32px; color: var(--api-muted); }
.api-empty i { font-size: 32px; opacity: 0.2; display: block; margin-bottom: 8px; }

/* ── Tabs ────────────────────────────────────────────────────── */
.api-tabs {
    display: flex; gap: 4px;
    background: var(--api-card);
    padding: 6px;
    border-radius: var(--api-radius);
    border: 1px solid var(--api-border);
    margin-bottom: 16px;
    box-shadow: var(--api-shadow);
    flex-wrap: wrap;
}
.api-tab {
    flex: 1; padding: 9px 12px;
    border: none; background: transparent;
    border-radius: 10px;
    font-size: 12px; font-weight: 600; color: var(--api-muted);
    cursor: pointer; transition: all 0.2s;
    white-space: nowrap;
}
.api-tab.active {
    background: linear-gradient(135deg,#3b82f6,#6366f1);
    color: #fff;
    box-shadow: 0 2px 8px rgba(59,130,246,0.3);
}
.api-tab-panel { display: none; }
.api-tab-panel.active { display: block; }

/* ── Instructions Content ────────────────────────────────────── */
.api-desc { font-size: 13px; color: var(--api-muted); margin-bottom: 16px; line-height: 1.6; }
.api-info-block { background: #f8fafc; border: 1px solid var(--api-border); border-radius: 10px; padding: 12px 16px; }
.api-info-block code { font-size: 13px; color: #3b82f6; font-family: 'Courier New', monospace; }
.api-ib-label { font-size: 11px; font-weight: 700; color: var(--api-muted); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; display: block; }
.mt-3 { margin-top: 16px; }

.api-code-block {
    background: #0f172a; color: #e2e8f0;
    border-radius: 10px; padding: 14px 16px;
    font-family: 'Courier New', monospace;
    font-size: 12px; line-height: 1.7;
    white-space: pre-wrap; word-break: break-all;
    border: 1px solid rgba(255,255,255,0.06);
}

.api-tip {
    background: rgba(59,130,246,0.06);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 8px; padding: 10px 14px;
    font-size: 12px; color: #2563eb;
    margin-top: 14px;
}
.api-tip code { background: rgba(59,130,246,0.1); padding: 1px 5px; border-radius: 4px; font-size: 11px; }

/* ── Endpoints ───────────────────────────────────────────────── */
.api-endpoint {
    padding: 14px 0;
    border-bottom: 1px solid var(--api-border);
}
.api-endpoint:last-child { border-bottom: none; }
.ep-method {
    display: inline-block; padding: 3px 8px;
    border-radius: 5px; font-size: 11px; font-weight: 700;
    font-family: monospace; margin-right: 6px;
}
.ep-method.post { background: rgba(16,185,129,0.12); color: #059669; }
.ep-method.get  { background: rgba(59,130,246,0.12); color: #2563eb; }
.ep-path { font-size: 13px; font-family: monospace; font-weight: 600; color: var(--api-text); }
.ep-badge {
    display: inline-block; padding: 2px 8px; border-radius: 20px;
    font-size: 10px; font-weight: 600; margin-left: 8px; vertical-align: middle;
}
.ep-badge.public { background: rgba(100,116,139,0.1); color: var(--api-muted); }
.ep-badge.auth   { background: rgba(245,158,11,0.1);  color: #d97706; }
.ep-desc { font-size: 12px; color: var(--api-muted); margin: 6px 0 0 0; }
.ep-params { margin-top: 8px; display: flex; flex-direction: column; gap: 4px; }
.ep-params div { font-size: 12px; color: var(--api-muted); }
.ep-params code { background: #f1f5f9; padding: 1px 6px; border-radius: 4px; color: #475569; font-size: 11px; }
.ep-params em { font-size: 11px; color: #94a3b8; }

/* ── Steps ───────────────────────────────────────────────────── */
.api-step {
    display: flex; gap: 14px; align-items: flex-start;
    margin-bottom: 18px;
}
.api-step-num {
    width: 26px; height: 26px;
    background: linear-gradient(135deg,#3b82f6,#6366f1);
    color: #fff; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.api-step strong { font-size: 13px; color: var(--api-text); display: block; margin-bottom: 8px; }
.api-step .api-code-block { margin-top: 6px; }
.api-list { font-size: 13px; color: var(--api-muted); padding-left: 18px; margin: 6px 0 0; }
.api-list li { margin-bottom: 4px; }
.api-list code { background: #f1f5f9; padding: 1px 5px; border-radius: 4px; font-size: 11px; color: #475569; }
</style>

<script>
function showTab(name) {
    document.querySelectorAll('.api-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.api-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    event.currentTarget.classList.add('active');
}

function copyToken() {
    const val = document.getElementById('newTokenVal').textContent.trim();
    navigator.clipboard.writeText(val).then(() => {
        const btn = document.querySelector('.api-copy-btn');
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.style.background = '#059669';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
            btn.style.background = '';
        }, 2000);
    });
}
</script>

@endsection
