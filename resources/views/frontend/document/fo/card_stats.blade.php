<div class="row">                   
    <!-- New Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('fo.documents.new') }}">
            <div class="card card-statistic-1 
                {{ $activeDropdown=='new' ? 'bg-info text-white' : '' }}">
                
                <div class="card-icon 
                    {{ $activeDropdown=='new' ? 'bg-white' : 'bg-info' }}">
                    <i class="fas fa-inbox"
                       style="{{ $activeDropdown=='new' ? 'color:#2a6d98;' : 'color:#fff;' }}"></i>
                </div>
                
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $activeDropdown=='new' ? 'color:#fff;' : '' }}">New</h4>
                    </div>
                    <div class="card-body" style="{{ $activeDropdown=='new' ? 'color:#fff;' : '' }}">
                        {{ $newCount }}
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Assigned for Payment Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('fo.documents.assigned') }}">
            <div class="card card-statistic-1 
                {{ $activeDropdown=='assigned' ? 'bg-secondary text-white' : '' }}">
                
                <div class="card-icon 
                    {{ $activeDropdown=='assigned' ? 'bg-white' : 'bg-secondary' }}">
                    <i class="fas fa-tasks"
                       style="{{ $activeDropdown=='assigned' ? 'color:#8a6d1c;' : 'color:#fff;' }}"></i>
                </div>
                
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $activeDropdown=='assigned' ? 'color:#fff;' : '' }}">Assigned for Payment</h4>
                    </div>
                    <div class="card-body" style="{{ $activeDropdown=='assigned' ? 'color:#fff;' : '' }}">
                        {{ $assginedDocsCount }}
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Completed Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('fo.documents.completed') }}">
            <div class="card card-statistic-1 
                {{ $activeDropdown=='completed' ? 'bg-success text-white' : '' }}">
                
                <div class="card-icon 
                    {{ $activeDropdown=='completed' ? 'bg-white' : 'bg-success' }}">
                    <i class="fas fa-check-circle"
                       style="{{ $activeDropdown=='completed' ? 'color:#155724;' : 'color:#fff;' }}"></i>
                </div>
                
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $activeDropdown=='completed' ? 'color:#fff;' : '' }}">Completed</h4>
                    </div>
                    <div class="card-body" style="{{ $activeDropdown=='completed' ? 'color:#fff;' : '' }}">
                        {{ $paymentCompletedCount }}
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Forwarded Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('fo.documents.total') }}">
            <div class="card card-statistic-1 
                {{ $activeDropdown=='total' ? 'bg-primary text-white' : '' }}">
                
                <div class="card-icon 
                    {{ $activeDropdown=='total' ? 'bg-white' : 'bg-primary' }}">
                    <i class="fas fa-share-square"
                       style="{{ $activeDropdown=='total' ? 'color:#000;' : 'color:#fff;' }}"></i>
                </div>
                
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $activeDropdown=='total' ? 'color:#fff;' : '' }}">Total</h4>
                    </div>
                    <div class="card-body" style="{{ $activeDropdown=='total' ? 'color:#fff;' : '' }}">
                        {{ $totalForwardedCount }}
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <!-- Payment Started Card - Accordion -->
    <div class="col-12">
        <div class="card">
            <div class="card-header" id="paymentStartedHeading">
                <h4 class="mb-0">
                    <button class="btn btn-link w-100 text-left d-flex justify-content-between align-items-center" 
                            type="button" 
                            data-toggle="collapse" 
                            data-target="#paymentStartedCollapse" 
                            aria-expanded="true" 
                            aria-controls="paymentStartedCollapse">
                        <span>
                            <i class="fas fa-play-circle text-warning mr-2"></i>
                            Payment Stat Analysis
                        </span>
                        <span class="accordion-arrow">
                            <i class="fas fa-chevron-down ml-2"></i>
                        </span>
                    </button>
                </h4>
            </div>
            
            <div id="paymentStartedCollapse" class="collapse" aria-labelledby="paymentStartedHeading">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Total Count -->
                        <div class="col-lg-2 col-md-3 text-center mb-md-0 mb-4">
                            <div class="total-payment-started">
                                <div class="display-4 font-weight-bold text-warning">{{ $totalForwardedCount }}</div>
                                <div class="text-muted">Total</div>
                            </div>
                        </div>
                        
                        <!-- Progress Visualization -->
                        <div class="col-lg-10 col-md-9">
                            <div class="payment-progress-container">
                                <!-- Full Payment Progress -->
                                <div class="progress-item mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <span class="font-weight-bold">Full Payments</span>
                                            <span class="badge badge-success ml-2">{{ $fullCount }}</span>
                                        </div>
                                        <div class="text-success font-weight-bold">
                                            @php
                                                $fullPercentage = $totalForwardedCount > 0 ? round(($fullCount / $totalForwardedCount) * 100) : 0;
                                            @endphp
                                            <span class="percentage-display">{{ $fullPercentage }}%</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                            data-width="{{ $fullPercentage }}" 
                                            aria-valuenow="{{ $fullPercentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Advance Payment Progress -->
                                <div class="progress-item">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <span class="font-weight-bold">Advance Payments</span>
                                            <span class="badge badge-primary ml-2">{{ $advanceCount }}</span>
                                        </div>
                                        <div class="text-primary font-weight-bold">
                                            @php
                                                $advancePercentage = $totalForwardedCount > 0 ? round(($advanceCount / $totalForwardedCount) * 100) : 0;
                                            @endphp
                                            <span class="percentage-display">{{ $advancePercentage }}%</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                            data-width="{{ $advancePercentage }}" 
                                            aria-valuenow="{{ $advancePercentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Row -->
                    <div class="row mt-4 pt-3 border-top">
                        <!-- Total Card -->
                        <div class="col-md-4 text-center">
                            <a href="{{ route('fo.documents.total') }}">
                                <div class="stat-box 
                                    {{ $activeDropdown=='total' ? 'bg-dark text-white rounded p-3' : '' }}">
                                    <div class="stat-value display-4 font-weight-bold 
                                        {{ $activeDropdown=='total' ? 'text-white' : 'text-warning' }}">
                                        {{ $totalForwardedCount }}
                                    </div>
                                    <div class="stat-label 
                                        {{ $activeDropdown=='total' ? 'text-white' : '' }}">
                                        Total
                                    </div>
                                    <div class="stat-icon mt-2">
                                        <i class="fas fa-play-circle fa-2x 
                                            {{ $activeDropdown=='total' ? 'text-white' : 'text-warning' }}"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Full Payments Card -->
                        <div class="col-md-4 text-center">
                            <a href="{{ route('fo.documents.fullPayment') }}">
                                <div class="stat-box 
                                    {{ $activeDropdown=='fullPayment' ? 'bg-success text-white rounded p-3' : '' }}">
                                    <div class="stat-value display-4 font-weight-bold 
                                        {{ $activeDropdown=='fullPayment' ? 'text-white' : 'text-success' }}">
                                        {{ $fullCount }}
                                    </div>
                                    <div class="stat-label 
                                        {{ $activeDropdown=='fullPayment' ? 'text-white' : '' }}">
                                        Full Payments
                                    </div>
                                    <div class="stat-icon mt-2">
                                        <i class="fas fa-check-circle fa-2x 
                                            {{ $activeDropdown=='fullPayment' ? 'text-white' : 'text-success' }}"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Advance Payments Card -->
                        <div class="col-md-4 text-center">
                            <a href="{{ route('fo.documents.advancePayment') }}">
                                <div class="stat-box 
                                    {{ $activeDropdown=='advancePayment' ? 'bg-info text-white rounded p-3' : '' }}">
                                    <div class="stat-value display-4 font-weight-bold 
                                        {{ $activeDropdown=='advancePayment' ? 'text-white' : 'text-primary' }}">
                                        {{ $advanceCount }}
                                    </div>
                                    <div class="stat-label 
                                        {{ $activeDropdown=='advancePayment' ? 'text-white' : '' }}">
                                        Advance Payments
                                    </div>
                                    <div class="stat-icon mt-2">
                                        <i class="fas fa-clock fa-2x 
                                            {{ $activeDropdown=='advancePayment' ? 'text-white' : 'text-primary' }}"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Accordion Header */
    #paymentStartedHeading .btn-link {
        text-decoration: none !important;
        color: #343a40 !important;
        padding: 1rem 1.25rem;
        font-size: 1.1rem;
        font-weight: 600;
        background: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
        transition: all 0.3s;
    }
    
    #paymentStartedHeading .btn-link:hover {
        background: #e9ecef;
        color: #2a6d98 !important;
    }
    
    #paymentStartedHeading .btn-link:focus {
        box-shadow: none;
    }
    
    #paymentStartedHeading .btn-link.collapsed .accordion-arrow i {
        transform: rotate(0deg);
    }
    
    .accordion-arrow i {
        transition: transform 0.3s ease;
        transform: rotate(180deg);
    }
    
    /* Payment Progress Container */
    .payment-progress-container {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }
    
    .progress-item {
        position: relative;
    }
    
    .progress-item .progress {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        background-color: #e9ecef;
    }
    
    .progress-item .progress-bar {
        position: relative;
        border-radius: 10px;
        width: 0%; /* Start at 0 for animation */
        transition: width 1.5s ease-in-out;
    }
    
    .progress-item .progress-bar.bg-success:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }
    
    .progress-item .progress-bar.bg-primary:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }
    
    /* Total Payment Started */
    .total-payment-started {
        padding: 20px;
        background: linear-gradient(135deg, rgba(255,193,7,0.1) 0%, rgba(255,193,7,0.05) 100%);
        border-radius: 10px;
        border: 2px solid #ffc107;
    }
    
    /* Stat Boxes */
    .stat-box {
        padding: 20px;
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        background: #f8f9fa;
    }
    
    .stat-value {
        font-size: 3rem;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 14px;
        margin-top: 10px;
    }
    
    .stat-icon {
        opacity: 0.7;
    }
    
    /* Percentage Animation */
    .percentage-display {
        display: inline-block;
        min-width: 40px;
    }
    
    /* Collapse Animation */
    .collapse.show {
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .stat-value {
            font-size: 2.5rem;
        }
        
        .payment-progress-container {
            padding: 15px;
        }
        
        .total-payment-started {
            padding: 15px;
        }
        
        .progress-item .progress {
            height: 15px;
        }
        
        #paymentStartedHeading .btn-link {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .stat-value {
            font-size: 2rem;
        }
        
        .display-4 {
            font-size: 2.5rem;
        }
        
        .total-payment-started .display-4 {
            font-size: 2rem;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Animate progress bars when accordion is shown
        function animateProgressBars() {
            $('.progress-bar').each(function(index) {
                var $this = $(this);
                var width = $this.data('width') + '%';
                
                // Reset to 0 for animation
                $this.css('width', '0%');
                
                // Animate after a short delay for sequential effect
                setTimeout(function() {
                    $this.animate({
                        width: width
                    }, 1500, 'easeOutQuart');
                    
                    // Animate percentage counter
                    var $percentage = $this.closest('.progress-item').find('.percentage-display');
                    var targetPercent = parseInt($this.data('width'));
                    var currentPercent = 0;
                    
                    var counter = setInterval(function() {
                        if (currentPercent >= targetPercent) {
                            clearInterval(counter);
                            return;
                        }
                        currentPercent++;
                        $percentage.text(currentPercent + '%');
                    }, 1500 / targetPercent);
                    
                }, index * 300); // Stagger the animations
            });
        }
        
        // Initialize progress bars when page loads (accordion is open by default)
        if ($('#paymentStartedCollapse').hasClass('show')) {
            animateProgressBars();
        }
        
        // Re-animate when accordion is opened
        $('#paymentStartedCollapse').on('shown.bs.collapse', function() {
            animateProgressBars();
        });
        
        // Reset animation when accordion is closed (optional)
        $('#paymentStartedCollapse').on('hidden.bs.collapse', function() {
            $('.progress-bar').css('width', '0%');
            $('.percentage-display').each(function() {
                var targetPercent = $(this).closest('.progress-item').find('.progress-bar').data('width');
                $(this).text(targetPercent + '%');
            });
        });
    });
</script>