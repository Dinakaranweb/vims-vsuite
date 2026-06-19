@php
    $dept = Auth::user()->department;
@endphp

<div class="status-filter-bar">
    <!-- Tabs Navigation -->
    @if($dept == 'VC')
        <div class="authority-tabs">
            <button class="tab-btn active" onclick="openTab('vc')">Vice Chancellor (VC)</button>
            <button class="tab-btn" onclick="openTab('pro')">Pro Vice Chancellor (Pro-VC)</button>
            <button class="tab-btn" onclick="openTab('registrar')">Registrar</button>
        </div>
    @elseif($dept == 'Registrar')
        <div class="authority-tabs">
            <button class="tab-btn active" onclick="openTab('registrar')">Registrar</button>
            <button class="tab-btn" onclick="openTab('pro')">Pro Vice Chancellor (Pro-VC)</button>
            <button class="tab-btn" onclick="openTab('vc')">Vice Chancellor (VC)</button>
        </div>
    @elseif($dept == 'Pro-VC')
        <div class="authority-tabs">
            <button class="tab-btn" onclick="openTab('registrar')">Registrar</button>
            <button class="tab-btn active" onclick="openTab('pro')">Pro Vice Chancellor (Pro-VC)</button>
            <button class="tab-btn" onclick="openTab('vc')">Vice Chancellor (VC)</button>
        </div>
    @endif

    <!-- VC Tab Content -->
    <div id="vc-tab" class="tab-content active">
        <div class="status-filters">
            
            
            @if($dept == 'VC')

                <a href="{{ route('summary_new', ['role' => 'VC']) }}" class="status-filter-btn new {{ request()->routeIs('summary_new') && request('role') == 'VC' ? 'active' : '' }}">
                    <span class="filter-count">{{ $vcNewCount }}</span>
                    <span class="filter-label">New</span>
                </a>

                <a href="{{ route('summary_documents', ['role' => 'VC']) }}" class="status-filter-btn yet-to-approve {{ request()->routeIs('summary_documents') && request('role') == 'VC' ? 'active' : '' }}">
                    <span class="filter-count">{{ $vcNotApprovedYetCount }}</span>
                    <span class="filter-label">Yet to Approve</span>
                </a>

            @endif
            
            <a href="{{ route('summary_approved', ['role' => 'VC']) }}" class="status-filter-btn approved {{ request()->routeIs('summary_approved') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $approvedCount }}</span>
                <span class="filter-label">Approved</span>
            </a>
            
            <a href="{{ route('summary_approved_in_principle', ['role' => 'VC']) }}" class="status-filter-btn approved-principle {{ request()->routeIs('summary_approved_in_principle') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $approvedInPrincipleCount }}</span>
                <span class="filter-label">In Principle</span>
            </a>
            
            <a href="{{ route('summary_discussion', ['role' => 'VC']) }}" class="status-filter-btn discussion {{ request()->routeIs('summary_discussion') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $discussionCount }}</span>
                <span class="filter-label">Discussion</span>
            </a>
            
            <a href="{{ route('summary_forwarded', ['role' => 'VC']) }}" class="status-filter-btn forwarded {{ request()->routeIs('summary_forwarded') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $forwardedCount }}</span>
                <span class="filter-label">Forwarded</span>
            </a>
            
            <a href="{{ route('summary_commented', ['role' => 'VC']) }}" class="status-filter-btn commented {{ request()->routeIs('summary_commented') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $commentedCount }}</span>
                <span class="filter-label">Commented</span>
            </a>
            
            <a href="{{ route('summary_nat', ['role' => 'VC']) }}" class="status-filter-btn no-action {{ request()->routeIs('summary_nat') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $noActionCount }}</span>
                <span class="filter-label">No Action</span>
            </a>
            
            <a href="{{ route('summary_hold', ['role' => 'VC']) }}" class="status-filter-btn hold {{ request()->routeIs('summary_hold') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $holdCount }}</span>
                <span class="filter-label">Hold</span>
            </a>
            
            <a href="{{ route('summary_rejected', ['role' => 'VC']) }}" class="status-filter-btn rejected {{ request()->routeIs('summary_rejected') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $rejectCount }}</span>
                <span class="filter-label">Rejected</span>
            </a>
            
            <a href="{{ route('summary_pending', ['role' => 'VC']) }}" class="status-filter-btn pending {{ request()->routeIs('summary_pending') && request('role') == 'VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $pendingCount }}</span>
                <span class="filter-label">Pending</span>
            </a>
            
            <div class="status-filter-btn total">
                <span class="filter-count">{{ $totalDocumentsCount }}</span>
                <span class="filter-label">Total</span>
            </div>
        </div>
    </div>

    <!-- Registrar Tab Content -->
    <div id="registrar-tab" class="tab-content">
        <div class="status-filters">
            
            <a href="{{ route('summary_new', ['role' => 'Registrar']) }}" class="status-filter-btn no-action {{ request()->routeIs('summary_new') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarNewCount }}</span>
                <span class="filter-label">New</span>
            </a>

            <a href="{{ route('summary_documents', ['role' => 'Registrar']) }}" class="status-filter-btn yet-to-approve {{ request()->routeIs('summary_documents') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarNotApprovedYetCount }}</span>
                <span class="filter-label">Yet to Approve</span>
            </a>
        
            <a href="{{ route('summary_approved', ['role' => 'Registrar']) }}" class="status-filter-btn approved {{ request()->routeIs('summary_approved') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarApprovedCount }}</span>
                <span class="filter-label">Approved</span>
            </a>
            
            <a href="{{ route('summary_hold', ['role' => 'Registrar']) }}" class="status-filter-btn hold {{ request()->routeIs('summary_hold') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarHoldCount }}</span>
                <span class="filter-label">Hold</span>
            </a>
            
            <a href="{{ route('summary_pending', ['role' => 'Registrar']) }}" class="status-filter-btn pending {{ request()->routeIs('summary_pending') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarPendingCount }}</span>
                <span class="filter-label">Pending</span>
            </a>

            <a href="{{ route('summary_discussion', ['role' => 'Registrar']) }}" class="status-filter-btn discussion {{ request()->routeIs('summary_discussion') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarDiscussionCount }}</span>
                <span class="filter-label">Discussion</span>
            </a>
            
            <a href="{{ route('summary_rejected', ['role' => 'Registrar']) }}" class="status-filter-btn rejected {{ request()->routeIs('summary_rejected') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarRejectCount }}</span>
                <span class="filter-label">Rejected</span>
            </a>
            
            <a href="{{ route('summary_commented', ['role' => 'Registrar']) }}" class="status-filter-btn commented {{ request()->routeIs('summary_commented') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarCommentedCount }}</span>
                <span class="filter-label">Commented</span>
            </a>
            
            <a href="{{ route('summary_forwarded', ['role' => 'Registrar']) }}" class="status-filter-btn forwarded {{ request()->routeIs('summary_forwarded') && request('role') == 'Registrar' ? 'active' : '' }}">
                <span class="filter-count">{{ $registrarForwardedCount }}</span>
                <span class="filter-label">Forwarded</span>
            </a>
            
            <div class="status-filter-btn total">
                <span class="filter-count">{{ $totalDocumentsCount }}</span>
                <span class="filter-label">Total</span>
            </div>
        </div>
    </div>

    <!-- pro Tab Content -->
    <div id="pro-tab" class="tab-content">
        <div class="status-filters">

            @if($dept == 'Pro-VC' || $dept == 'VC')
            
                <a href="{{ route('summary_new', ['role' => 'Pro-VC']) }}" class="status-filter-btn no-action {{ request()->routeIs('summary_new') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                    <span class="filter-count">{{ $proVcNewCount }}</span>
                    <span class="filter-label">New</span>
                </a>

                <a href="{{ route('summary_documents', ['role' => 'Pro-VC']) }}" class="status-filter-btn yet-to-approve {{ request()->routeIs('summary_documents') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                    <span class="filter-count">{{ $proVcNotApprovedYetCount }}</span>
                    <span class="filter-label">Yet to Approve</span>
                </a>

            @endif
        
            <a href="{{ route('summary_approved', ['role' => 'Pro-VC']) }}" class="status-filter-btn approved {{ request()->routeIs('summary_approved') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcApprovedCount }}</span>
                <span class="filter-label">Approved</span>
            </a>
            
            <a href="{{ route('summary_hold', ['role' => 'Pro-VC']) }}" class="status-filter-btn hold {{ request()->routeIs('summary_hold') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcHoldCount }}</span>
                <span class="filter-label">Hold</span>
            </a>
            
            <a href="{{ route('summary_discussion', ['role' => 'Pro-VC']) }}" class="status-filter-btn discussion {{ request()->routeIs('summary_discussion') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcDiscussionCount }}</span>
                <span class="filter-label">Discussion</span>
            </a>
            
            <a href="{{ route('summary_pending', ['role' => 'Pro-VC']) }}" class="status-filter-btn pending {{ request()->routeIs('summary_pending') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcPendingCount }}</span>
                <span class="filter-label">Pending</span>
            </a>
            
            <a href="{{ route('summary_rejected', ['role' => 'Pro-VC']) }}" class="status-filter-btn rejected {{ request()->routeIs('summary_rejected') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcRejectCount }}</span>
                <span class="filter-label">Rejected</span>
            </a>
            
            <a href="{{ route('summary_commented', ['role' => 'Pro-VC']) }}" class="status-filter-btn commented {{ request()->routeIs('summary_commented') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcCommentedCount }}</span>
                <span class="filter-label">Commented</span>
            </a>
            
            <a href="{{ route('summary_forwarded', ['role' => 'Pro-VC']) }}" class="status-filter-btn forwarded {{ request()->routeIs('summary_forwarded') && request('role') == 'Pro-VC' ? 'active' : '' }}">
                <span class="filter-count">{{ $proVcForwardedCount }}</span>
                <span class="filter-label">Forwarded</span>
            </a>
            
            <div class="status-filter-btn total">
                <span class="filter-count">{{ $totalDocumentsCount }}</span>
                <span class="filter-label">Total</span>
            </div>
        </div>
    </div>
</div>

<style>
    .authority-tabs {
        display: flex;
        margin-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
    }

    .tab-btn {
        padding: 10px 20px;
        border: none;
        background: #f8f9fa;
        cursor: pointer;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        transition: all 0.3s ease;
    }

    .tab-btn:hover {
        background: #e9ecef;
    }

    .tab-btn.active {
        background: #2a6d98;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
</style>

<script>

    document.addEventListener("DOMContentLoaded", function() {
        // Get role from URL or default to VC
        let activeRole = "{{ request('role') ?? 'VC' }}";

        // Normalize tab name (for element IDs)
        let tabId = activeRole.toLowerCase().replace(/\s+/g, '-');
        if (tabId === 'pro-vc') tabId = 'pro';

        // Remove all active classes
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

        // Activate matching tab content
        const activeTab = document.getElementById(`${tabId}-tab`);
        if (activeTab) activeTab.classList.add('active');

        // Activate matching tab button
        document.querySelectorAll('.tab-btn').forEach(btn => {
            const btnText = btn.textContent.toLowerCase();
            if (
                (activeRole.toLowerCase() === 'vc' && btnText.includes('vice chancellor') && !btnText.includes('pro')) ||
                (activeRole.toLowerCase() === 'pro-vc' && btnText.includes('pro vice chancellor')) ||
                (activeRole.toLowerCase() === 'registrar' && btnText.includes('registrar'))
            ) {
                btn.classList.add('active');
            }
        });
    });

    function openTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        // Show selected tab
        document.getElementById(`${tabName}-tab`).classList.add('active');
        // Mark button active
        event.currentTarget.classList.add('active');
    }
</script>
