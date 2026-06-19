@php
    $staffWithAssignments = [];
    foreach($staffMembers as $staff) {
        $stats = $staffStatistics[$staff->id] ?? [];
        $counts = $stats['counts'] ?? [];
        $totalAssigned = $counts['assigned'] ?? 0;
        if($totalAssigned > 0) $staffWithAssignments[] = $staff;
    }
@endphp

<!-- Staff Tabs 3 -->

<div class="glassmorphism-container mb-4">
    <!-- Animated Background -->
    <div class="glassmorphism-background">
        <div class="bg-blur-circle circle-1"></div>
        <div class="bg-blur-circle circle-2"></div>
        <div class="bg-blur-circle circle-3"></div>
    </div>

    <!-- Header with Stats and Accordion Toggle -->
    <div class="glass-header-section">
        <div class="glass-main-header">
            <div class="glass-title-wrapper">
                <h1 class="glass-main-title">
                    <span class="glass-title-gradient">Team Performance</span>
                </h1>
                <p class="glass-main-subtitle">
                    
                    <span class="glass-update-time">
                        <i class="fas fa-clock"></i>
                        Updated {{ now()->format('g:i A') }}
                    </span>
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="glass-quick-stats">
                <div class="glass-stat-card">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #667eea20, #764ba220)">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-count="{{ count($staffWithAssignments) }}">0</div>
                        <div class="stat-label">Active Staff</div>
                    </div>
                </div>
                
                @php
                    $totalAssigned = 0;
                    $totalCompleted = 0;
                    foreach($staffWithAssignments as $staff) {
                        $stats = $staffStatistics[$staff->id] ?? [];
                        $counts = $stats['counts'] ?? [];
                        $totalAssigned += $counts['assigned'] ?? 0;
                        $totalCompleted += $counts['completed'] ?? 0;
                    }
                    $overallProgress = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;
                @endphp
                
                <div class="glass-stat-card">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #10b98120, #3b82f620)">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-count="{{ $totalAssigned }}">0</div>
                        <div class="stat-label">Total Assignments</div>
                    </div>
                </div>
                
                <div class="glass-stat-card">
                    <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #f59e0b20, #f9731620)">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-count="{{ $overallProgress }}">0<small>%</small></div>
                        <div class="stat-label">Team Efficiency</div>
                    </div>
                </div>
                
                <!-- Accordion Toggle Button -->
                <button class="glass-accordion-toggle" id="accordionToggle">
                    <i class="fas fa-chevron-down"></i>
                    <span>Collapse</span>
                </button>
            </div>
        </div>

        <!-- Search Only -->
        <div class="glass-view-controls">
            <div class="glass-search">
                <i class="fas fa-search"></i>
                <input type="text" class="glass-search-input" placeholder="Search staff by name or email...">
            </div>
        </div>
    </div>

    <!-- Accordion Content -->
    <div class="glass-accordion-content" id="accordionContent">
        <!-- Horizontal Scroll Container with Drag Scroll -->
        <div class="horizontal-scroll-container" id="scrollContainer">
            <div class="scroll-hint">
                <i class="fas fa-arrows-alt-h"></i>
                Drag to scroll
            </div>
            <div class="glass-performance-grid" id="performanceGrid">
                @if(count($staffWithAssignments) > 0)
                    @foreach($staffWithAssignments as $staff)
                    @php
                        $stats = $staffStatistics[$staff->id] ?? [];
                        $counts = $stats['counts'] ?? [];
                        $totalAssigned = $counts['assigned'] ?? 0;
                        $totalCompleted = $counts['completed'] ?? 0;
                        $inProgress = $counts['payment_started'] ?? 0;
                        $onHold = $counts['hold'] ?? 0;
                        $progress = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;
                        
                        $performanceLevel = match(true) {
                            $progress >= 90 => 'excellent',
                            $progress >= 75 => 'good',
                            $progress >= 50 => 'average',
                            default => 'needs_attention'
                        };
                        
                        $performanceConfig = [
                            'excellent' => ['color' => '#10b981', 'icon' => 'fa-trophy', 'label' => 'Top Performer'],
                            'good' => ['color' => '#3b82f6', 'icon' => 'fa-star', 'label' => 'Reliable'],
                            'average' => ['color' => '#f59e0b', 'icon' => 'fa-chart-line', 'label' => 'On Track'],
                            'needs_attention' => ['color' => '#ef4444', 'icon' => 'fa-exclamation', 'label' => 'Needs Review']
                        ];
                        
                        $config = $performanceConfig[$performanceLevel];
                        
                        // Calculate payment type percentages
                        $paymentTypes = $counts['payment_types'] ?? [];
                        $fullPayment = $paymentTypes['Full Payment'] ?? 0;
                        $partialPayment = $paymentTypes['Partial Payment'] ?? 0;
                        $totalPayments = $fullPayment + $partialPayment;
                        $fullPercentage = $totalPayments > 0 ? round(($fullPayment / $totalPayments) * 100) : 0;
                        $partialPercentage = $totalPayments > 0 ? round(($partialPayment / $totalPayments) * 100) : 0;
                    @endphp
                    
                    <div class="glass-performance-card" data-performance="{{ $performanceLevel }}">
                        <!-- Card Header -->
                        <div class="glass-card-header">
                            <div class="staff-identity">
                                <div class="staff-avatar" style="background: linear-gradient(135deg, {{ $config['color'] }}40, {{ $config['color'] }}20)">
                                    <span class="avatar-initial">{{ substr($staff->name, 0, 1) }}</span>
                                    <div class="avatar-status" style="background-color: {{ $config['color'] }}"></div>
                                </div>
                                <div class="staff-info">
                                    <h3 class="staff-name">{{ $staff->name }}</h3>
                                    <p class="staff-email">{{ $staff->email }}</p>
                                    <div class="staff-tags">
                                        <span class="staff-tag" style="background: {{ $config['color'] }}20; color: {{ $config['color'] }}">
                                            <i class="fas {{ $config['icon'] }}"></i>
                                            {{ $config['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Efficiency Score -->
                        <div class="efficiency-score">
                            <div class="score-header">
                                <span>Efficiency Score</span>
                                <span class="score-value" style="color: {{ $config['color'] }}">{{ $progress }}%</span>
                            </div>
                            <div class="score-meter">
                                <div class="meter-fill" style="width: {{ $progress }}%; background: linear-gradient(90deg, {{ $config['color'] }}, {{ $config['color'] }}80);"></div>
                                <div class="meter-marks">
                                    <span class="meter-mark" style="left: 25%">Low</span>
                                    <span class="meter-mark" style="left: 50%">Avg</span>
                                    <span class="meter-mark" style="left: 75%">Good</span>
                                    <span class="meter-mark" style="left: 100%">Excel</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon-circle" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="{{ $totalAssigned }}">0</div>
                                    <div class="stat-label">Assigned</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon-circle" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="{{ $totalCompleted }}">0</div>
                                    <div class="stat-label">Completed</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon-circle" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="{{ $inProgress }}">0</div>
                                    <div class="stat-label">In Progress</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon-circle" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-pause-circle"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="{{ $onHold }}">0</div>
                                    <div class="stat-label">On Hold</div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Breakdown with Progress Bars -->
                        @if(false)
                        <div class="payment-breakdown">
                            <div class="breakdown-header">
                                <i class="fas fa-credit-card"></i>
                                <span>Payment Breakdown</span>
                            </div>
                            <div class="payment-bars">
                                <!-- Full Payment Bar -->
                                <div class="payment-bar-item">
                                    <div class="payment-bar-label">
                                        <i class="fas fa-money-check-alt" style="color: #10b981;"></i>
                                        <span>Full Payment</span>
                                    </div>
                                    <div class="payment-bar-container">
                                        <div class="payment-bar-fill" data-width="{{ $fullPercentage }}" style="background: linear-gradient(90deg, #10b981, #34d399);"></div>
                                    </div>
                                    <div class="payment-bar-value">
                                        <span class="payment-count">{{ $fullPayment }}</span>
                                        <span class="payment-percentage">{{ $fullPercentage }}%</span>
                                    </div>
                                </div>
                                
                                <!-- Partial Payment Bar -->
                                <div class="payment-bar-item">
                                    <div class="payment-bar-label">
                                        <i class="fas fa-money-bill-wave" style="color: #3b82f6;"></i>
                                        <span>Advance Payment</span>
                                    </div>
                                    <div class="payment-bar-container">
                                        <div class="payment-bar-fill" data-width="{{ $partialPercentage }}" style="background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
                                    </div>
                                    <div class="payment-bar-value">
                                        <span class="payment-count">{{ $partialPayment }}</span>
                                        <span class="payment-percentage">{{ $partialPercentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Detailed Report Button -->
                        <div class="glass-card-footer">
                            <a href="{{ route('fo.staff.report', $staff->id) }}" class="detail-report-btn">
                                <i class="fas fa-chart-bar"></i>
                                View Detailed Report
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="glass-empty-state">
                        <div class="empty-illustration">
                            <i class="fas fa-user-clock"></i>
                            <div class="empty-particles">
                                <div class="particle"></div>
                                <div class="particle"></div>
                                <div class="particle"></div>
                            </div>
                        </div>
                        <h3>No Active Assignments</h3>
                        <p>Assign tasks to staff members to track their performance metrics here.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="glass-footer">
            <div class="footer-info">
                <span class="footer-text">
                    <i class="fas fa-users"></i>
                    {{ count($staffWithAssignments) }} active staff members
                </span>
                <span class="footer-text">
                    <i class="fas fa-chart-line"></i>
                    Overall efficiency: {{ $overallProgress }}%
                </span>
                <span class="footer-text">
                    <i class="fas fa-tasks"></i>
                    {{ $totalAssigned }} total assignments
                </span>
            </div>
        </div>
    </div>
</div>

<style>
    .glassmorphism-container {
        position: relative;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border-radius: 32px;
        padding: 32px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 20px 40px rgba(31, 38, 135, 0.1),
            0 8px 32px rgba(31, 38, 135, 0.05),
            inset 0 0 0 1px rgba(255, 255, 255, 0.3);
        overflow: hidden;
    }

    .glassmorphism-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: -1;
        overflow: hidden;
    }

    /* Accordion Styles */
    .glass-accordion-toggle {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        margin-left: auto;
    }

    .glass-accordion-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .glass-accordion-toggle i {
        transition: transform 0.3s ease;
    }

    .glass-accordion-toggle.collapsed i {
        transform: rotate(180deg);
    }

    .glass-accordion-content {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .glass-accordion-content.collapsed {
        max-height: 0 !important;
        opacity: 0;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden;
    }

    /* Detail Report Button */
    .detail-report-btn {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #3b82f6;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        margin-top: 20px;
        width: 100%;
        justify-content: center;
    }

    .detail-report-btn:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }

    .detail-report-btn i {
        font-size: 12px;
    }

    /* Add a section for the button in each card */
    .glass-card-footer {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid rgba(229, 231, 235, 0.5);
    }

    .bg-blur-circle {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.15;
    }

    .circle-1 {
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        top: -100px;
        right: -100px;
        animation: float 20s infinite ease-in-out;
    }

    .circle-2 {
        width: 400px;
        height: 400px;
        background: linear-gradient(135deg, #10b981, #3b82f6);
        bottom: -200px;
        left: -100px;
        animation: float 25s infinite ease-in-out reverse;
    }

    .circle-3 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, #f59e0b, #ef4444);
        top: 50%;
        left: 60%;
        animation: float 15s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }

    .glass-header-section {
        margin-bottom: 32px;
    }

    .glass-main-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .glass-title-wrapper {
        flex: 1;
    }

    .glass-main-title {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 8px;
    }

    .glass-title-gradient {
        font-size: 32px;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea, #764ba2, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .glass-main-subtitle {
        color: rgba(107, 114, 128, 0.8);
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .glass-update-time {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .glass-quick-stats {
        display: flex;
        gap: 16px;
    }

    .glass-stat-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        min-width: 140px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }

    .glass-stat-card:hover {
        transform: translateY(-4px);
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 12px 24px rgba(31, 38, 135, 0.1);
    }

    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(31, 41, 55, 0.8);
        font-size: 24px;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 12px;
        color: rgba(107, 114, 128, 0.8);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .glass-view-controls {
        display: flex;
        justify-content: flex-end;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 12px 20px;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .glass-search {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.8);
        padding: 8px 16px;
        border-radius: 12px;
        border: 1px solid rgba(229, 231, 235, 0.5);
        width: 300px;
    }

    .glass-search i {
        color: #9ca3af;
    }

    .glass-search-input {
        border: none;
        background: transparent;
        outline: none;
        color: #1f2937;
        font-size: 14px;
        width: 100%;
    }

    .glass-search-input::placeholder {
        color: #9ca3af;
    }

    /* Horizontal Scroll Container with Drag */
    .horizontal-scroll-container {
        position: relative;
        overflow-x: auto;
        padding-bottom: 20px;
        margin: 0 -32px;
        padding: 0 32px 20px;
        cursor: grab;
        -webkit-overflow-scrolling: touch;
        user-select: none;
    }

    .horizontal-scroll-container.dragging {
        cursor: grabbing;
        user-select: none;
    }

    .scroll-hint {
        position: absolute;
        top: 10px;
        right: 40px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(229, 231, 235, 0.6);
        z-index: 10;
        animation: fadeOutHint 8s forwards;
        animation-delay: 3s;
    }

    @keyframes fadeOutHint {
        0% { opacity: 1; }
        100% { opacity: 0; visibility: hidden; }
    }

    .horizontal-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .horizontal-scroll-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.3);
        border-radius: 3px;
        transition: background 0.3s ease;
    }

    .horizontal-scroll-container::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.5);
    }

    .glass-performance-grid {
        display: flex;
        gap: 30px;
        padding-bottom: 10px;
        min-width: min-content;
    }

    .glass-performance-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        padding: 28px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        min-width: 440px;
        max-width: 450px;
        flex: 0 0 auto;
        cursor: default;
    }

    .glass-performance-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--performance-color, #3b82f6), transparent);
        opacity: 0.5;
    }

    .glass-performance-card:hover {
        transform: translateY(-8px);
        border-color: rgba(59, 130, 246, 0.5);
        box-shadow: 
            0 20px 40px rgba(31, 38, 135, 0.1),
            0 8px 32px rgba(31, 38, 135, 0.05);
    }

    .glass-performance-card[data-performance="excellent"] {
        --performance-color: #10b981;
    }

    .glass-performance-card[data-performance="good"] {
        --performance-color: #3b82f6;
    }

    .glass-performance-card[data-performance="average"] {
        --performance-color: #f59e0b;
    }

    .glass-performance-card[data-performance="needs_attention"] {
        --performance-color: #ef4444;
    }

    .glass-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .staff-identity {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
    }

    .staff-avatar {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
    }

    .avatar-initial {
        font-size: 28px;
        font-weight: 700;
        color: rgba(31, 41, 55, 0.8);
    }

    .avatar-status {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .staff-info {
        flex: 1;
        min-width: 0;
    }

    .staff-name {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .staff-email {
        font-size: 13px;
        color: rgba(107, 114, 128, 0.8);
        margin-bottom: 12px;
        word-break: break-all;
    }

    .staff-tags {
        display: flex;
        gap: 8px;
    }

    .staff-tag {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    /* Efficiency Score */
    .efficiency-score {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .score-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #4b5563;
    }

    .score-value {
        font-size: 18px;
        font-weight: 700;
    }

    .score-meter {
        height: 10px;
        background: rgba(229, 231, 235, 0.5);
        border-radius: 5px;
        position: relative;
        overflow: hidden;
    }

    .meter-fill {
        height: 100%;
        border-radius: 5px;
        transition: width 1s ease;
    }

    .meter-marks {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .meter-mark {
        position: absolute;
        top: -22px;
        font-size: 11px;
        color: rgba(107, 114, 128, 0.6);
        transform: translateX(-50%);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 14px;
        background: rgba(255, 255, 255, 0.8);
        padding: 16px;
        border-radius: 14px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .stat-icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
        min-width: 0;
    }

    .stat-number {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 12px;
        color: rgba(107, 114, 128, 0.8);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Payment Breakdown with Bars */
    .payment-breakdown {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .breakdown-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        color: #4b5563;
        font-size: 14px;
        font-weight: 600;
    }

    .payment-bars {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .payment-bar-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .payment-bar-label {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
    }

    .payment-bar-label i {
        font-size: 16px;
        width: 24px;
    }

    .payment-bar-label span {
        font-size: 13px;
        color: #4b5563;
        font-weight: 500;
    }

    .payment-bar-container {
        flex: 1;
        height: 12px;
        background: rgba(229, 231, 235, 0.5);
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }

    .payment-bar-fill {
        height: 100%;
        border-radius: 6px;
        width: 0%;
        transition: width 1s ease;
    }

    .payment-bar-value {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 60px;
        justify-content: flex-end;
    }

    .payment-count {
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
    }

    .payment-percentage {
        font-size: 12px;
        font-weight: 600;
        color: rgba(107, 114, 128, 0.8);
        min-width: 40px;
        text-align: right;
    }

    /* Empty State */
    .glass-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        text-align: center;
        padding: 40px;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        border: 2px dashed rgba(255, 255, 255, 0.4);
        min-width: 100%;
    }

    .empty-illustration {
        position: relative;
        width: 120px;
        height: 120px;
        margin-bottom: 32px;
    }

    .empty-illustration i {
        font-size: 64px;
        color: rgba(156, 163, 175, 0.5);
        position: relative;
        z-index: 1;
    }

    .empty-particles {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .particle {
        position: absolute;
        width: 40px;
        height: 40px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 50%;
        animation: pulse 2s infinite ease-in-out;
    }

    .particle:nth-child(1) {
        top: 0;
        left: 0;
        animation-delay: 0s;
    }

    .particle:nth-child(2) {
        top: 0;
        right: 0;
        animation-delay: 0.5s;
    }

    .particle:nth-child(3) {
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        animation-delay: 1s;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .glass-empty-state h3 {
        font-size: 24px;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .glass-empty-state p {
        color: rgba(107, 114, 128, 0.8);
        max-width: 400px;
        margin: 0 auto 24px;
    }

    /* Footer */
    .glass-footer {
        display: flex;
        justify-content: center;
        padding-top: 24px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .footer-info {
        display: flex;
        gap: 32px;
    }

    .footer-text {
        font-size: 14px;
        color: rgba(107, 114, 128, 0.8);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .footer-text i {
        color: rgba(59, 130, 246, 0.8);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .glass-main-header {
            flex-direction: column;
            gap: 24px;
        }
        
        .glass-quick-stats {
            width: 100%;
        }
        
        .glass-stat-card {
            flex: 1;
            min-width: 0;
        }
        
        .footer-info {
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        
        .glass-performance-card {
            min-width: 380px;
        }
    }

    @media (max-width: 768px) {
        .glassmorphism-container {
            padding: 20px;
            border-radius: 24px;
        }
        
        .glass-view-controls {
            justify-content: center;
        }
        
        .glass-search {
            width: 100%;
        }
        
        .horizontal-scroll-container {
            margin: 0 -20px;
            padding: 0 20px 20px;
        }
        
        .glass-performance-card {
            min-width: 340px;
            padding: 24px;
        }
        
        .staff-identity {
            gap: 16px;
        }
        
        .staff-avatar {
            width: 60px;
            height: 60px;
        }
        
        .avatar-initial {
            font-size: 24px;
        }
        
        .stats-grid {
            gap: 12px;
        }
        
        .stat-item {
            padding: 14px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate number counters
        document.querySelectorAll('[data-count]').forEach(counter => {
            const finalValue = parseInt(counter.getAttribute('data-count'));
            const duration = 1000;
            const increment = finalValue / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                if (current < finalValue) {
                    current += increment;
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = finalValue;
                }
            };
            
            setTimeout(updateCounter, 300);
        });
        
        // Animate meter fill
        document.querySelectorAll('.meter-fill').forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0%';
            setTimeout(() => {
                fill.style.width = width;
            }, 500);
        });
        
        // Animate payment bars
        document.querySelectorAll('.payment-bar-fill').forEach(bar => {
            const width = bar.getAttribute('data-width') + '%';
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 800);
        });
        
        // Search functionality
        const searchInput = document.querySelector('.glass-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.glass-performance-card');
                
                cards.forEach(card => {
                    const name = card.querySelector('.staff-name').textContent.toLowerCase();
                    const email = card.querySelector('.staff-email').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        card.style.display = 'flex';
                        card.style.flexDirection = 'column';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
        
        // Drag Scroll Functionality
        const scrollContainer = document.getElementById('scrollContainer');
        if (scrollContainer) {
            const grid = document.getElementById('performanceGrid');
            let isDragging = false;
            let startX;
            let scrollLeft;
            
            // Mouse drag
            scrollContainer.addEventListener('mousedown', (e) => {
                isDragging = true;
                scrollContainer.classList.add('dragging');
                startX = e.pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
                
                // Prevent text selection while dragging
                e.preventDefault();
            });
            
            scrollContainer.addEventListener('mouseleave', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });
            
            scrollContainer.addEventListener('mouseup', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });
            
            scrollContainer.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2; // Multiply for faster scrolling
                scrollContainer.scrollLeft = scrollLeft - walk;
            });
            
            // Touch drag for mobile
            scrollContainer.addEventListener('touchstart', (e) => {
                isDragging = true;
                scrollContainer.classList.add('dragging');
                startX = e.touches[0].pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });
            
            scrollContainer.addEventListener('touchend', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });
            
            scrollContainer.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const x = e.touches[0].pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2;
                scrollContainer.scrollLeft = scrollLeft - walk;
            });
            
            // Add momentum scrolling
            let scrollVelocity = 0;
            let lastScrollPos = 0;
            let lastTime = 0;
            
            scrollContainer.addEventListener('scroll', () => {
                const currentTime = Date.now();
                const currentScroll = scrollContainer.scrollLeft;
                
                if (lastTime !== 0) {
                    const deltaTime = currentTime - lastTime;
                    const deltaScroll = currentScroll - lastScrollPos;
                    scrollVelocity = deltaScroll / deltaTime;
                }
                
                lastScrollPos = currentScroll;
                lastTime = currentTime;
            });
            
            // Momentum scroll on mouse up/touch end
            scrollContainer.addEventListener('mouseup', applyMomentum);
            scrollContainer.addEventListener('touchend', applyMomentum);
            
            function applyMomentum() {
                if (Math.abs(scrollVelocity) > 0.1) {
                    const momentum = scrollVelocity * 300; // Adjust multiplier for momentum strength
                    const targetScroll = scrollContainer.scrollLeft + momentum;
                    
                    scrollContainer.scrollTo({
                        left: targetScroll,
                        behavior: 'smooth'
                    });
                }
            }
        }
        
        // Create floating particles for background
        const container = document.querySelector('.glassmorphism-container');
        if (container) {
            const createParticle = () => {
                const particle = document.createElement('div');
                particle.className = 'floating-particle';
                particle.style.cssText = `
                    position: absolute;
                    width: ${Math.random() * 3 + 1}px;
                    height: ${Math.random() * 3 + 1}px;
                    background: rgba(255, 255, 255, ${Math.random() * 0.2 + 0.05});
                    border-radius: 50%;
                    top: ${Math.random() * 100}%;
                    left: ${Math.random() * 100}%;
                    pointer-events: none;
                    z-index: 0;
                `;
                container.appendChild(particle);
                
                const duration = Math.random() * 10000 + 5000;
                particle.animate([
                    { transform: 'translate(0, 0)', opacity: 0 },
                    { transform: `translate(${Math.random() * 80 - 40}px, ${Math.random() * 80 - 40}px)`, opacity: 0.3 },
                    { transform: `translate(${Math.random() * 80 - 40}px, ${Math.random() * 80 - 40}px)`, opacity: 0 }
                ], {
                    duration: duration,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    iterations: Infinity
                });
                
                setTimeout(() => particle.remove(), duration);
            };
            
            // Create initial particles
            for (let i = 0; i < 15; i++) {
                setTimeout(createParticle, i * 200);
            }
            
            // Continuous particle generation
            setInterval(createParticle, 3000);
        }
        
        // Hover effect enhancement
        const cards = document.querySelectorAll('.glass-performance-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                const color = card.style.getPropertyValue('--performance-color') || '#3b82f6';
                card.style.boxShadow = `
                    0 20px 40px rgba(31, 38, 135, 0.15),
                    0 8px 32px rgba(31, 38, 135, 0.1),
                    inset 0 0 0 1px rgba(255, 255, 255, 0.3),
                    0 0 40px ${color}20
                `;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.boxShadow = `
                    0 20px 40px rgba(31, 38, 135, 0.1),
                    0 8px 32px rgba(31, 38, 135, 0.05),
                    inset 0 0 0 1px rgba(255, 255, 255, 0.3)
                `;
            });
        });

        // Accordion Functionality
        const accordionToggle = document.getElementById('accordionToggle');
        const accordionContent = document.getElementById('accordionContent');
        
        if (accordionToggle && accordionContent) {
            const toggleIcon = accordionToggle.querySelector('i');
            const toggleText = accordionToggle.querySelector('span');

            // IMPORTANT CHANGE: Start with accordion collapsed
            // Add 'collapsed' class to content and toggle initially
            accordionContent.classList.add('collapsed');
            accordionToggle.classList.add('collapsed');
            
            // Set max-height to 0 initially (collapsed state)
            accordionContent.style.maxHeight = '0px';
            
            // Set toggle text to "Expand" since it's collapsed
            if (toggleText) {
                toggleText.textContent = 'Expand';
            }

            accordionToggle.addEventListener('click', function() {
                const isCollapsed = accordionContent.classList.toggle('collapsed');
                accordionToggle.classList.toggle('collapsed', isCollapsed);
                
                if (isCollapsed) {
                    // Collapse: Set height to 0
                    if (toggleText) toggleText.textContent = 'Expand';
                    accordionContent.style.maxHeight = '0px';
                } else {
                    // Expand: Set to scroll height
                    if (toggleText) toggleText.textContent = 'Collapse';
                    accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px';
                }
            });

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (!accordionContent.classList.contains('collapsed')) {
                        // Only update if accordion is expanded
                        const newHeight = accordionContent.scrollHeight;
                        accordionContent.style.maxHeight = newHeight + 'px';
                    }
                }, 250);
            });
        }

        // Update route generation for detail report button
        // If you're using Laravel, make sure you have a route named 'staff.detail.report'
        // Example route definition (add this to your web.php):
        // Route::get('/staff/{id}/detail-report', [StaffController::class, 'detailReport'])->name('staff.detail.report');

        // Alternative: If you don't have routes, use a JavaScript approach
        // Note: You already have DOMContentLoaded, so remove this duplicate listener
        // document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers for detail report buttons
            document.querySelectorAll('.detail-report-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    // You can add custom behavior here if needed
                    // For example, analytics tracking or loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    // The actual navigation will happen via href attribute
                });
            });
        // });
    });
</script>