<!-- Staff Performance with Horizontal Tabs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Staff Performance</h4>
            </div>
            <div class="card-body">
                @php
                    $staffWithAssignments = [];
                    foreach($staffMembers as $staff) {
                        $stats = $staffStatistics[$staff->id] ?? [];
                        $counts = $stats['counts'] ?? [];
                        $totalAssigned = $counts['assigned'] ?? 0;
                        if($totalAssigned > 0) $staffWithAssignments[] = $staff;
                    }
                @endphp
                
                @if(count($staffWithAssignments) > 0)
                    <div class="row">
                        @foreach($staffWithAssignments as $staff)
                            @php
                                $stats = $staffStatistics[$staff->id] ?? [];
                                $counts = $stats['counts'] ?? [];
                                $totalAssigned = $counts['assigned'] ?? 0;
                                $totalCompleted = $counts['completed'] ?? 0;
                                $progress = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;
                            @endphp
                            
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="staff-profile-card">
                                    <!-- Header -->
                                    <div class="profile-header">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="profile-icon">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h6 class="mb-0">{{ $staff->name }}</h6>
                                                <small class="text-muted">{{ $staff->email }}</small>
                                            </div>
                                        </div>
                                        <div class="total-assigned">
                                            <span class="badge badge-primary">{{ $totalAssigned }} Assigned</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress -->
                                    <div class="progress-container mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Progress</small>
                                            <small class="font-weight-bold">{{ $progress }}%</small>
                                        </div>
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-success" data-width="{{ $progress }}" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Stats -->
                                    <div class="stats-grid mb-3">
                                        <div class="stat">
                                            <div class="stat-value">{{ $counts['assigned'] ?? 0 }}</div>
                                            <div class="stat-label">Assigned</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-value">{{ $counts['payment_started'] ?? 0 }}</div>
                                            <div class="stat-label">Started</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-value">{{ $counts['completed'] ?? 0 }}</div>
                                            <div class="stat-label">Completed</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-value">{{ $counts['hold'] ?? 0 }}</div>
                                            <div class="stat-label">Hold</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Types -->
                                    @if(isset($counts['payment_types']) && count($counts['payment_types']) > 0)
                                        <div class="payment-tags">
                                            @foreach($counts['payment_types'] as $type => $count)
                                                @if($count > 0)
                                                    <span class="tag {{ $type == 'Partial Payment' ? 'tag-primary' : 'tag-success' }}">
                                                        {{ $type == 'Partial Payment' ? 'Advance' : 'Full' }}: {{ $count }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-data text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                        <h5 class="text-muted">No Staff Assignments</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .staff-profile-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e9ecef;
        height: 100%;
        transition: all 0.3s;
    }
    
    .staff-profile-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .profile-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #2a6d98, #3b82f6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    
    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    
    .stat {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .stat-value {
        font-size: 20px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .payment-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }
    
    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .tag-primary {
        background: rgba(42, 109, 152, 0.1);
        color: #2a6d98;
        border: 1px solid rgba(42, 109, 152, 0.2);
    }
    
    .tag-success {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.2);
    }
    
    .no-data i {
        opacity: 0.3;
    }
    
    @media (max-width: 768px) {
        .staff-profile-card {
            padding: 15px;
        }
        
        .profile-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
        
        .stat-value {
            font-size: 18px;
        }
    }
</style>

<script>
    $(document).ready(function() {
        $('.progress-bar[data-width]').each(function(index) {
            var $this = $(this);
            var width = $this.data('width') + '%';
            setTimeout(() => {
                $this.css('width', width);
            }, index * 300);
        });
    });
</script>

<!-- Staff Tabs 2-->

<div class="glass-container">
    <div class="glass-header">
        <div>
            <h2 class="glass-title">Staff Performance</h2>
            <p class="glass-subtitle">Assignment completion overview</p>
        </div>
        <div class="glass-stats">
            <div class="glass-stat">
                <span class="glass-stat-value">{{ count($staffWithAssignments) }}</span>
                <span class="glass-stat-label">Active Staff</span>
            </div>
        </div>
    </div>

    @if(count($staffWithAssignments) > 0)
        <div class="glass-grid">
            @foreach($staffWithAssignments as $staff)
            @php
                $stats = $staffStatistics[$staff->id] ?? [];
                $counts = $stats['counts'] ?? [];
                $totalAssigned = $counts['assigned'] ?? 0;
                $totalCompleted = $counts['completed'] ?? 0;
                $progress = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;
                
                $efficiency = match(true) {
                    $progress >= 90 => 'Excellent',
                    $progress >= 70 => 'Good',
                    $progress >= 50 => 'Average',
                    default => 'Needs Improvement'
                };
                
                $efficiencyColor = match($efficiency) {
                    'Excellent' => '#10b981',
                    'Good' => '#3b82f6',
                    'Average' => '#f59e0b',
                    default => '#ef4444'
                };
            @endphp
            
            <div class="glass-card">
                <div class="glass-card-content">
                    <div class="glass-card-header">
                        <div class="glass-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="glass-user">
                            <h3 class="glass-user-name">{{ $staff->name }}</h3>
                            <p class="glass-user-email">{{ $staff->email }}</p>
                        </div>
                        <div class="glass-rating" style="--rating-color: {{ $efficiencyColor }}">
                            {{ $efficiency }}
                        </div>
                    </div>

                    <div class="glass-progress">
                        <div class="glass-progress-info">
                            <span class="glass-progress-label">Completion Rate</span>
                            <span class="glass-progress-value">{{ $progress }}%</span>
                        </div>
                        <div class="glass-progress-bar">
                            <div class="glass-progress-fill" data-width="{{ $progress }}" style="width: 0%; background: linear-gradient(90deg, {{ $efficiencyColor }}, {{ $efficiencyColor }}80)"></div>
                        </div>
                    </div>

                    <div class="glass-metrics">
                        <div class="glass-metric">
                            <div class="glass-metric-icon" style="background: rgba(59, 130, 246, 0.1)">
                                <i class="fas fa-tasks" style="color: #3b82f6"></i>
                            </div>
                            <div>
                                <div class="glass-metric-value">{{ $totalAssigned }}</div>
                                <div class="glass-metric-label">Assigned</div>
                            </div>
                        </div>
                        <div class="glass-metric">
                            <div class="glass-metric-icon" style="background: rgba(16, 185, 129, 0.1)">
                                <i class="fas fa-check-circle" style="color: #10b981"></i>
                            </div>
                            <div>
                                <div class="glass-metric-value">{{ $counts['completed'] ?? 0 }}</div>
                                <div class="glass-metric-label">Completed</div>
                            </div>
                        </div>
                        <div class="glass-metric">
                            <div class="glass-metric-icon" style="background: rgba(245, 158, 11, 0.1)">
                                <i class="fas fa-spinner" style="color: #f59e0b"></i>
                            </div>
                            <div>
                                <div class="glass-metric-value">{{ $counts['payment_started'] ?? 0 }}</div>
                                <div class="glass-metric-label">In Progress</div>
                            </div>
                        </div>
                        <div class="glass-metric">
                            <div class="glass-metric-icon" style="background: rgba(239, 68, 68, 0.1)">
                                <i class="fas fa-pause-circle" style="color: #ef4444"></i>
                            </div>
                            <div>
                                <div class="glass-metric-value">{{ $counts['hold'] ?? 0 }}</div>
                                <div class="glass-metric-label">On Hold</div>
                            </div>
                        </div>
                    </div>

                    @if(isset($counts['payment_types']))
                    <div class="glass-tags">
                        <div class="glass-tags-title">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment Breakdown</span>
                        </div>
                        <div class="glass-tags-list">
                            @foreach($counts['payment_types'] as $type => $count)
                                @if($count > 0)
                                <span class="glass-tag {{ $type == 'Partial Payment' ? 'glass-tag-blue' : 'glass-tag-green' }}">
                                    {{ $type == 'Partial Payment' ? 'Advance' : 'Full' }}
                                    <span class="glass-tag-count">{{ $count }}</span>
                                </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="glass-empty">
            <div class="glass-empty-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <h3>No Active Assignments</h3>
            <p>Staff members will appear here once assignments are created.</p>
        </div>
    @endif
</div>

<style>
    .glass-container {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        padding: 32px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 8px 32px rgba(31, 38, 135, 0.07),
            inset 0 0 0 1px rgba(255, 255, 255, 0.6);
    }

    .glass-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .glass-title {
        font-size: 28px;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 4px;
    }

    .glass-subtitle {
        color: #6b7280;
        font-size: 14px;
    }

    .glass-stats {
        display: flex;
        gap: 16px;
    }

    .glass-stat {
        text-align: center;
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .glass-stat-value {
        display: block;
        font-size: 32px;
        font-weight: 700;
        color: #3b82f6;
        line-height: 1;
    }

    .glass-stat-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .glass-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 20px;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.6);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(8px);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 
            0 12px 24px rgba(31, 38, 135, 0.1),
            inset 0 0 0 1px rgba(255, 255, 255, 0.8);
    }

    .glass-card-content {
        padding: 24px;
    }

    .glass-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
    }

    .glass-avatar {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        color: white;
        font-size: 24px;
    }

    .glass-user {
        flex: 1;
    }

    .glass-user-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 2px;
        color: #1f2937;
    }

    .glass-user-email {
        font-size: 12px;
        color: #6b7280;
    }

    .glass-rating {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: var(--rating-color);
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .glass-progress {
        margin-bottom: 24px;
    }

    .glass-progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .glass-progress-label {
        font-size: 13px;
        color: #6b7280;
    }

    .glass-progress-value {
        font-weight: 700;
        font-size: 18px;
        color: #1f2937;
    }

    .glass-progress-bar {
        height: 8px;
        background: rgba(229, 231, 235, 0.6);
        border-radius: 4px;
        overflow: hidden;
    }

    .glass-progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease;
    }

    .glass-metrics {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .glass-metric {
        display: flex;
        align-items: center;
        padding: 12px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .glass-metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 18px;
    }

    .glass-metric-value {
        font-size: 20px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 2px;
        color: #1f2937;
    }

    .glass-metric-label {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .glass-tags {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        padding: 16px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .glass-tags-title {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        font-size: 13px;
        font-weight: 600;
        color: #4b5563;
    }

    .glass-tags-title i {
        margin-right: 8px;
        color: #6b7280;
    }

    .glass-tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .glass-tag {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .glass-tag-blue {
        background: rgba(59, 130, 246, 0.1);
        color: #1e40af;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .glass-tag-green {
        background: rgba(16, 185, 129, 0.1);
        color: #065f46;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .glass-tag-count {
        background: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
    }

    .glass-empty {
        text-align: center;
        padding: 80px 20px;
    }

    .glass-empty-icon {
        font-size: 64px;
        color: #9ca3af;
        margin-bottom: 24px;
        opacity: 0.5;
    }

    .glass-empty h3 {
        font-size: 20px;
        color: #4b5563;
        margin-bottom: 8px;
    }

    .glass-empty p {
        color: #9ca3af;
        max-width: 400px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .glass-container {
            padding: 20px;
            border-radius: 16px;
        }
        
        .glass-header {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }
        
        .glass-grid {
            grid-template-columns: 1fr;
        }
        
        .glass-metrics {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress bars
        document.querySelectorAll('.glass-progress-fill').forEach((bar, index) => {
            setTimeout(() => {
                const width = bar.dataset.width + '%';
                bar.style.width = width;
            }, index * 300);
        });
    });
</script>