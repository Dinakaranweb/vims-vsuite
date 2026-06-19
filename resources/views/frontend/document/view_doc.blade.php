@extends('frontend.frontend_master')

@section('content')

<style>
    /* Your existing styles remain the same */
    .approval-flowchart-container {
        overflow-x: auto;
        padding: 10px;
    }

    .approval-flowchart {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    .step {
        text-align: center;
        min-width: 250px;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        background-color: #f3f3f3;
        position: relative;
    }

    .step .vc {
        color: red;
    }

    .step .provc {
        color: green;
    }

    .step .registrar {
        color: #19b91f;
    }

    .icon {
        font-size: 24px;
        color: #ff7a00;
    }

    p {
        font-weight: bold;
        margin: 5px 0;
    }

    .connector {
        font-size: 24px;
        color: #ff7a00;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .approval-flowchart {
            gap: 10px;
        }
    }

    .step .description {
        max-height: 150px;
        overflow-y: auto;
        display: block;
        padding: 5px;
        word-break: break-word;
        text-align: center;
        line-height: 1.5;
        scroll-behavior: smooth;
    }

    .step .description::-webkit-scrollbar {
        width: 6px;
    }

    .step .description::-webkit-scrollbar-track {
        background: transparent;
    }

    .step .description::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .step .description::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.4);
    }

    .step .description {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }
    
    /* Status badge styles */
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-hold {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    .status-completed {
        background-color: #cce5ff;
        color: #004085;
    }
    
    .status-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .approval-path-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .path-step-current {
        background: #ffc107;
        color: #000;
        border-radius: 20px;
        padding: 5px 15px;
        font-weight: bold;
    }
    
    .path-step-completed {
        background: #28a745;
        color: white;
        border-radius: 20px;
        padding: 5px 15px;
    }
    
    .path-step-pending {
        background: #6c757d;
        color: white;
        border-radius: 20px;
        padding: 5px 15px;
        opacity: 0.7;
    }
    
    .path-blocked {
        background: #dc3545;
        color: white;
        border-radius: 20px;
        padding: 5px 15px;
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .approval-status-container {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .approval-status-item {
        text-align: center;
        min-width: 70px;
    }
    
    .approval-status-icon {
        font-size: 20px;
    }
    
    .approval-status-icon.completed {
        color: #28a745;
    }
    
    .approval-status-icon.pending {
        color: #ccc;
    }
    
    .approval-status-label {
        font-size: 11px;
        margin-top: 5px;
        display: block;
        word-break: break-word;
    }
    
    .approval-status-label.completed {
        color: #28a745;
        font-weight: bold;
    }
    
    .approval-arrow {
        color: #ccc;
        font-size: 14px;
        margin: 0 2px;
    }
    
    .buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    
    .buttons .btn {
        margin-bottom: 5px;
    }
    
    .approver-name {
        font-weight: normal;
        font-size: 11px;
        display: block;
        color: #666;
    }
    
    .path-step-completed .approver-name {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .path-step-current .approver-name {
        color: rgba(0, 0, 0, 0.7);
    }
    
    .action-btn-group {
        margin-bottom: 10px;
    }
    
    .btn-purchase {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        border: none;
    }
    
    .btn-work {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border: none;
    }
    
    .btn-sanction {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
        border: none;
    }
    
    .btn-payment {
        background: linear-gradient(135deg, #007bff, #0069d9);
        color: white;
        border: none;
    }
    
    .btn-stb {
        background: linear-gradient(135deg, #fd7e14, #dc3545);
        color: white;
        border: none;
    }
    
    .btn-chairman {
        background: linear-gradient(135deg, #6f42c1, #461a7a);
        color: white;
        border: none;
    }
    
    .readonly-view {
        background-color: #f8f9fa;
        border-left: 4px solid #28a745;
    }
    
    .view-status-badge {
        background-color: #28a745;
        color: white;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .info-box-info {
        background-color: #d1ecf1;
        border-left: 4px solid #17a2b8;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .info-box-warning {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        @if(Auth::user()->role == 'HOD')
            @include('frontend.admin.body.header')
            @include('frontend.admin.body.sidebar')
        @elseif(Auth::user()->role == 'SuperAdmin')
            @include('frontend.superadmin.body.header')
            @include('frontend.superadmin.body.sidebar')
        @else
            @include('frontend.staff.body.header')
            @include('frontend.staff.body.sidebar')
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>{!! $doc->subject !!}</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="">Dashboard</a></div>
                        <div class="breadcrumb-item"><a href="">Document</a></div>
                        <div class="breadcrumb-item">View</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">{!! $doc->subject !!}</h2>
                    
                    @php
                        $totalPaid = $pay->sum('paid_amount');
                        $total_tds = $pay->sum('tds_amount');
                        $user = Auth::user();
                        
                        // Safely decode approval sequence
                        $approvalSequence = [];
                        $currentIndex = 0;
                        $nextApprover = null;
                        $isComplete = false;
                        $completedApprovers = [];
                        $approverDetails = [];
                        
                        if (!empty($doc->approval_sequence)) {
                            try {
                                $approvalSequence = json_decode($doc->approval_sequence, true);
                                if (!is_array($approvalSequence)) {
                                    $approvalSequence = [];
                                }
                            } catch (\Exception $e) {
                                $approvalSequence = [];
                            }
                        }
                        
                        $currentIndex = $doc->current_sequence_index ?? 0;
                        $isComplete = $currentIndex >= count($approvalSequence);
                        $nextApprover = isset($approvalSequence[$currentIndex]) ? $approvalSequence[$currentIndex] : null;
                        
                        // IMPORTANT: Check if user is the CURRENT approver (sequential)
                        $isCurrentApprover = ($user->department == $nextApprover);
                        
                        // Check for STB Office approval - only if STB Office is the current approver
                        $isSTBApprover = ($user->department == 'STB Office' && $nextApprover == 'STB Office');
                        
                        // Check for Chairman approval - only if Chairman is the current approver
                        $isChairmanApprover = ($user->department == 'Chairman' && $nextApprover == 'Chairman');
                        
                        // Check for Medical Director - only if Medical Director is the current approver
                        $isMedicalDirector = ($user->department == 'Medical Director' && $nextApprover == 'Medical Director');
                        
                        // Check for General Manager - only if General Manager is the current approver
                        $isGeneralManager = ($user->department == 'General Manager' && $nextApprover == 'General Manager');
                        
                        // Check for Purchase Head roles - only if they are the current approver
                        $isPurchaseHeadSalem = ($user->department == 'Purchase Head' && $nextApprover == 'Purchase Head');
                        $isPurchaseHeadChennai = ($user->department == 'Purchase Head Chennai' && $nextApprover == 'Purchase Head Chennai');
                        
                        // Check for Finance Head - only if they are the current approver
                        $isFinanceHead = in_array($user->department, ['Finance Head Salem', 'Finance Head Karaikal', 'Finance Head Chennai', 'Finance Head Pondy']) 
                                         && $nextApprover == $user->department;
                        
                        // Check for PA to Chairman - only if current approver
                        $isPAtoChairman = ($user->department == 'PA to Chairman' && $nextApprover == 'PA to Chairman');
                        $isPAtoGM = ($user->department == 'PA to GM' && $nextApprover == 'PA to GM');
                        
                        // Determine if user can view this document
                        $userPosition = array_search($user->department, $approvalSequence);
                        $canViewDocument = false;
                        $canTakeAction = false;
                        $blockingMessage = null;
                        
                        // Document creator can always view
                        if ($doc->by == $user->id) {
                            $canViewDocument = true;
                        }
                        
                        // SuperAdmin can always view
                        if ($user->role == 'SuperAdmin') {
                            $canViewDocument = true;
                        }
                        
                        // Check if user's department is in the approval sequence
                        if ($userPosition !== false) {
                            $previousStepsCompleted = true;
                            for ($i = 0; $i < $userPosition; $i++) {
                                $stepApprover = $approvalSequence[$i];
                                $stepCompleted = DB::table('approval_log')
                                    ->where('doc_id', $doc->id)
                                    ->where('status', 'like', "Approved by $stepApprover%")
                                    ->orWhere('status', 'like', "Acknowledged by $stepApprover%")
                                    ->exists();

                                // If sequence index has advanced past this step, treat it as done
                                if (!$stepCompleted && $currentIndex > $i) {
                                    $stepCompleted = true;
                                }

                                if (!$stepCompleted) {
                                    $previousStepsCompleted = false;
                                    $blockingMessage = "This document is pending approval at: " . $stepApprover . ". You will be able to view it once all previous approvers have completed their approval.";
                                    break;
                                }
                            }
                            
                            if ($previousStepsCompleted) {
                                $canViewDocument = true;
                                $canTakeAction = ($userPosition == $currentIndex);
                            }
                        }
                        
                        // Check if user is a previous approver
                        $isPreviousApprover = false;
                        for ($i = 0; $i < $currentIndex; $i++) {
                            if ($approvalSequence[$i] == $user->department) {
                                $isPreviousApprover = true;
                                $canViewDocument = true;
                                break;
                            }
                        }
                        
                        // Record view
                        $hasUserViewed = DB::table('document_views')->where('doc_id', $doc->id)->where('user_id', $user->id)->exists();
                        
                        if ($canViewDocument && !$hasUserViewed && $doc->status != 'Draft') {
                            DB::table('document_views')->insert([
                                'doc_id' => $doc->id,
                                'user_id' => $user->id,
                                'viewed_at' => now()
                            ]);
                            $hasUserViewed = true;
                        }
                        
                        // Get completed approvals
                        $completedLogs = DB::table('approval_log')
                            ->where('doc_id', $doc->id)
                            ->where(function($query) {
                                $query->where('status', 'like', 'Approved by%')
                                      ->orWhere('status', 'like', 'Noted by%')
                                      ->orWhere('status', 'like', 'Completed by%')
                                      ->orWhere('status', 'like', 'Closed by%')
                                      ->orWhere('status', 'like', 'Forwarded to%by%')
                                      ->orWhere('status', 'like', 'Acknowledged by%');
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();
                        
                        foreach ($completedLogs as $log) {
                            if (preg_match('/(?:Approved|Noted|Completed|Closed|Acknowledged) by\s+(.+?)(?:\s|$)/', $log->status, $matches)) {
                                $deptName = trim($matches[1]);
                                $completedApprovers[] = $deptName;
                                $approver = App\Models\User::find($log->by);
                                $approverDetails[$deptName] = [
                                    'name' => $approver ? $approver->name : 'Unknown',
                                    'department' => $deptName,
                                    'date' => $log->created_at
                                ];
                            }
                            elseif (preg_match('/Forwarded to\s+(.+?)\s+by\s+(.+)/', $log->status, $matches)) {
                                // The department that did the forwarding is "done"; add them as completed
                                $forwardedBy = trim($matches[2]);
                                $completedApprovers[] = $forwardedBy;
                                $approver = App\Models\User::find($log->by);
                                $approverDetails[$forwardedBy] = [
                                    'name' => $approver ? $approver->name : 'Unknown',
                                    'department' => $forwardedBy,
                                    'date' => $log->created_at
                                ];
                            }
                        }
                        
                        $completedApprovers = array_unique($completedApprovers);
                        $totalSteps = count($approvalSequence);
                        $completedSteps = 0;
                        foreach ($approvalSequence as $index => $approver) {
                            if (in_array($approver, $completedApprovers) || $index < $currentIndex) {
                                $completedSteps++;
                            }
                        }
                        $completionPercent = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                        
                        // Check if payment is already added
                        $hasPaymentDetails = DB::table('payment_details')->where('doc_id', $doc->id)->exists();
                        $isPaymentCompleted = DB::table('payment_details')
                            ->where('doc_id', $doc->id)
                            ->where('payment_type', 'Full Payment')
                            ->exists()
                            || in_array($doc->status, ['Completed', 'Closed']);
                        
                        $process_payment = DB::table('payment_processing')->where('doc_id', $doc->id)->first();
                        
                        // Determine user's current status
                        $isCreator = ($doc->by == $user->id);
                        $hasApproved = in_array($user->department, $completedApprovers);
                        $isWaitingForPrevious = (!$canViewDocument && !$isCreator && $user->role != 'SuperAdmin');
                    @endphp
                    
                    <!-- Sequential Approval Flow Info -->
                    @if(!empty($approvalSequence) && !$isCreator && $user->role != 'SuperAdmin')
                        <div class="info-box-info">
                            <i class="fas fa-info-circle"></i> <strong>Sequential Approval Flow</strong><br>
                            This document follows a sequential approval process. Each approver can only view and act on the document after the previous approver has completed their action.
                            <div class="mt-2">
                                <strong>Current Status:</strong> 
                                @if($isComplete)
                                    <span class="status-badge status-completed">All approvals completed</span>
                                @elseif($isCurrentApprover && $canTakeAction)
                                    <span class="status-badge status-approved">Your turn to act</span>
                                @elseif($isPreviousApprover)
                                    <span class="status-badge status-completed">You have already approved this document</span>
                                @elseif($isWaitingForPrevious)
                                    <span class="status-badge status-warning">Waiting for previous approvers</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <!-- Blocking Message for users who cannot view yet -->
                    @if(!$canViewDocument && !$isCreator && $user->role != 'SuperAdmin' && $doc->status != 'Draft')
                        <div class="info-box-warning">
                            <i class="fas fa-lock"></i> <strong>Access Restricted</strong><br>
                            {{ $blockingMessage ?? 'You do not have permission to view this document at this stage of the approval process.' }}
                            <div class="mt-3">
                                <a href="{{ url()->previous() }}" class="btn btn-warning btn-sm">Go Back</a>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Approval Path Status Card -->
                    @if(!empty($approvalSequence) && $canViewDocument)
                    <div class="approval-path-card mb-4">
                        <h5><i class="fas fa-code-branch"></i> Approval Progress</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            @foreach($approvalSequence as $index => $step)
                                @php
                                    $isCompleted = in_array($step, $completedApprovers) || $index < $currentIndex;
                                    $isCurrent = $index == $currentIndex && !$isCompleted && $doc->status != 'Completed' && $doc->status != 'Closed' && $doc->status != 'Rejected';
                                    $isPending = !$isCompleted && !$isCurrent && $index >= $currentIndex;
                                    $stepClass = $isCompleted ? 'path-step-completed' : ($isCurrent ? 'path-step-current' : ($isPending ? 'path-step-pending' : 'path-step-pending'));
                                    $icon = $isCompleted ? '✓' : ($isCurrent ? '▶' : '○');
                                    $approverName = isset($approverDetails[$step]['name']) ? $approverDetails[$step]['name'] : '';
                                @endphp
                                <div class="text-center">
                                    <span class="{{ $stepClass }}" title="{{ $step }}">
                                        {{ $icon }} {{ $step }}
                                    </span>
                                    @if($approverName)
                                        <span class="approver-name">({{ $approverName }})</span>
                                    @endif
                                </div>
                                @if($index < count($approvalSequence) - 1)
                                    <span class="text-white">→</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercent }}%;" aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-white-50">{{ $completionPercent }}% Complete</small>
                        </div>
                        <div class="mt-3 small">
                            @if($isComplete)
                                <span class="status-badge status-completed">✓ All approvals completed</span>
                            @elseif($nextApprover)
                                <span class="status-badge status-pending">⏳ Pending at: {{ $nextApprover }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($canViewDocument || $isCreator || $user->role == 'SuperAdmin')
                    <div class="row">
                        <div class="col-lg-8 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Document Details</h4>
                                    @if($hasUserViewed && !$isCreator)
                                        <div class="ml-auto">
                                            <span class="view-status-badge"><i class="fas fa-check-circle"></i> Viewed</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body" style="overflow-x:auto;">
                                    @if($doc->ticket_id)
                                        @php
                                            $ticket = App\Models\Ticket::find($doc->ticket_id);
                                        @endphp
                                        @if(Auth::user()->role == 'SuperAdmin')
                                            <p><b>Ticket ID :</b> <a href="{{ url('superadmin/view/ticket/'.$ticket->id) }}" target="_blank">{{ $ticket->ticket_id }}</a></p>
                                        @else
                                            <p><b>Ticket ID :</b> <a href="{{ url('admin/view/ticket/'.$ticket->id) }}" target="_blank">{{ $ticket->ticket_id }}</a></p>
                                        @endif
                                        <p><b>Ticket Title :</b> {{ $ticket->title }}</p>
                                    @endif
                                    <p><b>Document ID :</b> {{ $doc->doc_id }}</p>
                                    @php
                                        $creator = App\Models\User::find($doc->by);
                                    @endphp
                                    <p><b>Sent by :</b> {{ $creator ? $creator->name : 'Unknown' }}, {{ $creator ? $creator->department : 'Unknown' }}</p>
                                    <p><b>From :</b> {{ $doc->from }}</p>
                                    <p><b>Subject :</b> {!! strip_tags($doc->subject, '<span><b><i><u>') !!}</p>
                                    <p><b>Priority :</b> {{ $doc->priority }}</p>
                                    <p><b>Purchase Request :</b> {{ $doc->is_purchase == 'Y' ? 'Yes' : 'No' }}</p>
                                    <p><b>Payment Involved :</b> {{ $doc->is_payment_involved ?? 'N' }}</p>
                                    
                                    @if($doc->amount)
                                        <div style="border:2px solid #2a6d98; border-radius:8px; padding:16px; margin-bottom:16px; background:#f8fafc;">
                                            <p><b>Requested Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.'  }} {{ number_format($doc->amount, 2) }}/-</p>
                                            <p><b>Recommended Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($doc->recommended_amount, 2) }}/-</p>
                                            <p><b>Sanctioned Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($doc->sanctioned_amount, 2) }}/-</p>
                                            <p><b>Paid Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($totalPaid, 2) }}/-</p>
                                            @if($doc->payment_mode)
                                                <p><b>Payment Mode :</b>
                                                    @if($doc->payment_mode == 'cash') Cash
                                                    @elseif($doc->payment_mode == 'annexure') Refer Annexure
                                                    @elseif($doc->payment_mode == 'cheque') Cheque
                                                    @elseif($doc->payment_mode == 'bank') NEFT/RTGS
                                                    @elseif($doc->payment_mode == 'upi') UPI
                                                    @else {{ ucfirst($doc->payment_mode) }}
                                                    @endif
                                                </p>
                                                <p><b>Payment Details :</b>
                                                    @if($doc->payment_mode == 'cash') NA
                                                    @elseif($doc->payment_mode == 'cheque') In favour of: {{ $doc->cash_in_favour ?? '-' }}
                                                    @elseif($doc->payment_mode == 'bank')
                                                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Name:</b> {{ $doc->account_holder ?? '-' }}
                                                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Account Number:</b> {{ $doc->account_number ?? '-' }}
                                                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>IFSC:</b> {{ $doc->ifsc_code ?? '-' }}
                                                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Branch:</b> {{ $doc->account_branch ?? '-' }}
                                                    @elseif($doc->payment_mode == 'upi') UPI ID: {{ $doc->upi_id ?? '-' }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                    <p><b>Description :</b> {!! $doc->description !!}</p>
                                    @if($doc->justification) <p><b>Justification :</b> {!! $doc->justification !!}</p> @endif
                                    @if($doc->purchase_committee_report)
                                        <p><b>Purchase Committee Report :</b> <a href="{{ Storage::url($doc->purchase_committee_report) }}" target="_blank">{{ basename($doc->purchase_committee_report) }}</a></p>
                                    @endif
                                    @if($doc->purchase_order)
                                        <p><b>Purchase Order :</b> <a href="{{ Storage::url($doc->purchase_order) }}" target="_blank">{{ basename($doc->purchase_order) }}</a></p>
                                    @endif
                                    @if($doc->work_order)
                                        <p><b>Work Order :</b> <a href="{{ Storage::url($doc->work_order) }}" target="_blank">{{ basename($doc->work_order) }}</a></p>
                                    @endif
                                    <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
                                </div>
                            </div>
                            
                            @include('frontend.document.partials.approval_status')

                            @if($doc->purchase_order)
                                <div class="card mb-4">
                                    <div class="card-header"><h4>Purchase Order</h4></div>
                                    <div class="card-body">
                                        <div class="text-center mb-2">
                                            <button onclick="zoomOut('purchase_order')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                            <button onclick="zoomIn('purchase_order')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                        </div>
                                        <div id="viewer-purchase_order" class="pdf-viewer" data-url="{{ Storage::url($doc->purchase_order) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                                    </div>
                                </div>
                            @endif

                            @if($doc->work_order)
                                <div class="card mb-4">
                                    <div class="card-header"><h4>Work Order</h4></div>
                                    <div class="card-body">
                                        <div class="text-center mb-2">
                                            <button onclick="zoomOut('work_order')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                            <button onclick="zoomIn('work_order')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                        </div>
                                        <div id="viewer-work_order" class="pdf-viewer" data-url="{{ Storage::url($doc->work_order) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($doc->purchase_committee_report)
                                <div class="card mb-4">
                                    <div class="card-header"><h4>Purchase Committee Report</h4></div>
                                    <div class="card-body">
                                        <div class="text-center mb-2">
                                            <button onclick="zoomOut('purchase_committee_report')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                            <button onclick="zoomIn('purchase_committee_report')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                        </div>
                                        <div id="viewer-purchase_committee_report" class="pdf-viewer" data-url="{{ Storage::url($doc->purchase_committee_report) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                                    </div>
                                </div>
                            @endif
                            
                            @php $no = 1; @endphp
                            @foreach($annexures as $attachment)
                                <div class="card">
                                    <div class="card-header"><h4>{{ $no }} - <a href="{{ Storage::url($attachment->annexure) }}" target="_blank">{{ basename($attachment->annexure) }}</a></h4></div>
                                    <div class="card-body">
                                        <div class="text-center mb-2">
                                            <button onclick="zoomOut('annexure-{{ $no }}')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                            <button onclick="zoomIn('annexure-{{ $no }}')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                        </div>
                                        <div id="viewer-annexure-{{ $no }}" class="pdf-viewer" data-url="{{ Storage::url($attachment->annexure) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                                    </div>
                                </div>
                                @php $no++; @endphp
                            @endforeach
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="row">
                                <!-- Action Buttons Section - Sequential Role Based -->
                                @if($doc->status != 'Draft' && $doc->status != 'Retracted' && $doc->status != 'Closed' && $doc->status != 'Rejected' && $canTakeAction && $hasUserViewed)
                                    <div style="position:sticky; top: 30px; z-index: 2;">
                                        <div class="card col-12">
                                            <div class="card-header">
                                                <h4>Action Required</h4>
                                                <div class="card-header-action">
                                                    <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                                                </div>
                                            </div>
                                            
                                            <div class="collapse show" id="mycard-collapse">
                                                <div class="card-body">
                                                    <div class="alert alert-success">
                                                        <i class="fas fa-check-circle"></i> You are the current approver. Please review the document and take appropriate action.
                                                    </div>
                                                    
                                                    <div class="buttons">
                                                        <!-- Retract Button (Only for document creator) -->
                                                        @php
                                                            $status_check = DB::table('approval_log')->where('doc_id', $doc->id)->where('status', '!=', 'Draft')->count();
                                                        @endphp
                                                        @if($doc->by == $user->id && $status_check == 0)
                                                            <a class="btn btn-primary w-100 mb-2" id="modal-retract" style="color:#fff">Retract</a>
                                                        @endif

                                                        <!-- STB Office Actions (Acknowledge and Forward) -->
                                                        @if($isSTBApprover)
                                                            <div class="action-btn-group w-100">
                                                                <button type="button" class="btn btn-stb w-100 mb-2" data-toggle="modal" data-target="#acknowledgeStbModal">
                                                                    <i class="fas fa-check-circle"></i> Acknowledge & Forward
                                                                </button>
                                                            </div>
                                                        @endif

                                                        <!-- Chairman Actions (Approve) -->
                                                        @if($isChairmanApprover)
                                                            <div class="action-btn-group w-100">
                                                                <button type="button" class="btn btn-chairman w-100 mb-2" data-toggle="modal" data-target="#chairmanApprovalModal">
                                                                    <i class="fas fa-check-double"></i> Approve
                                                                </button>
                                                            </div>
                                                        @endif

                                                        <!-- Medical Director / General Manager Actions (Approve) -->
                                                        @if($isMedicalDirector || $isGeneralManager)
                                                            <div class="action-btn-group w-100">
                                                                <a href="#" class="btn btn-success w-100 mb-2" id="modal-approve">
                                                                    <i class="fas fa-thumbs-up"></i> Approve
                                                                </a>
                                                            </div>
                                                        @endif

                                                        <!-- PA to Chairman / PA to GM (Select Finance Head) -->
                                                        @if($isPAtoChairman || $isPAtoGM)
                                                            <div class="action-btn-group w-100">
                                                                <button type="button" class="btn btn-info w-100 mb-2" data-toggle="modal" data-target="#selectFinanceHeadModal">
                                                                    <i class="fas fa-user-tie"></i> Select Finance Head
                                                                </button>
                                                            </div>
                                                        @endif

                                                        <!-- Purchase Head Actions (Create PO/Work Order) -->
                                                        @if($isPurchaseHeadSalem || $isPurchaseHeadChennai)
                                                            <div class="action-btn-group w-100">
                                                                @if(!$doc->purchase_order)
                                                                    <button type="button" class="btn btn-purchase w-100 mb-2" data-toggle="modal" data-target="#purchaseOrderModal">
                                                                        <i class="fas fa-file-pdf"></i> Create Purchase Order
                                                                    </button>
                                                                @endif
                                                                @if(!$doc->work_order)
                                                                    <button type="button" class="btn btn-work w-100 mb-2" data-toggle="modal" data-target="#workOrderModal">
                                                                        <i class="fas fa-file-alt"></i> Create Work Order
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- Finance Head Actions (Create Finance Details + Forward to another location) -->
                                                        @if($isFinanceHead)
                                                            <div class="action-btn-group w-100">
                                                                @if(!$isPaymentCompleted)
                                                                    <button type="button" class="btn btn-payment w-100 mb-2" data-toggle="modal" data-target="#financeDetailsModal">
                                                                        <i class="fas fa-rupee-sign"></i> Create Finance Details
                                                                    </button>
                                                                @else
                                                                    <div class="alert alert-success py-2 text-center">
                                                                        <i class="fas fa-check-circle"></i> Finance details submitted
                                                                    </div>
                                                                @endif
                                                                <button type="button" class="btn btn-outline-info w-100 mb-2" data-toggle="modal" data-target="#financeForwardModal">
                                                                    <i class="fas fa-exchange-alt"></i> Forward to Another Finance Location
                                                                </button>
                                                            </div>
                                                        @endif

                                                        <!-- Regular Approve Button for other approvers -->
                                                        @if(($isCurrentApprover && !$isSTBApprover && !$isChairmanApprover && !$isMedicalDirector && !$isGeneralManager && !$isPurchaseHeadSalem && !$isPurchaseHeadChennai && !$isFinanceHead && !$isPAtoChairman && !$isPAtoGM))
                                                            <a href="#" class="btn btn-success w-100 mb-2" id="modal-approve">Approve</a>
                                                        @endif
                                                        
                                                        @php
                                                            $isTerminalRole = $isSTBApprover || $isPurchaseHeadSalem || $isPurchaseHeadChennai || $isFinanceHead || $isPAtoChairman || $isPAtoGM;
                                                        @endphp

                                                        @if(!$isTerminalRole)
                                                        <!-- Common Action Buttons (not shown for STB / Purchase Head / Finance Head / PA roles) -->
                                                        <a href="#" class="btn btn-danger w-100 mb-2" id="modal-reject">Reject</a>
                                                        <a href="#" class="btn btn-dark w-100 mb-2" id="modal-hold">Hold</a>
                                                        <a href="#" class="btn btn-warning w-100 mb-2" id="modal-pending">Pending</a>
                                                        <a href="#" class="btn btn-light w-100 mb-2" id="modal-discuss">Discuss</a>
                                                        <a href="#" class="btn btn-info w-100 mb-2" id="modal-noted">Noted</a>

                                                        <!-- Forward Button -->
                                                        <a href="#" class="btn btn-outline-dark w-100 mb-2" id="modal-forward-doc">Forward to Another Department</a>
                                                        @endif

                                                        <!-- Comment & Download — always visible to all users in action mode -->
                                                        <a href="#" class="btn btn-outline-primary w-100 mb-2" id="modal-comment">Add Comment</a>
                                                        <a href="{{ route('download_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning w-100 mb-2">Download PDF</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(!$canTakeAction && !in_array($doc->status, ['Draft', 'Retracted']))
                                    <!-- View / Comment / Download — all users who are not current actor, at all stages -->
                                    <div style="position:sticky; top: 30px; z-index: 2;">
                                        <div class="card col-12">
                                            <div class="card-header">
                                                <h4>
                                                    @if($doc->status == 'Closed')
                                                        <i class="fas fa-lock"></i> Document Closed
                                                    @elseif($doc->status == 'Rejected')
                                                        <i class="fas fa-times-circle text-danger"></i> Document Rejected
                                                    @else
                                                        <i class="fas fa-eye"></i> View Only
                                                    @endif
                                                </h4>
                                            </div>
                                            <div class="card-body">
                                                @if($doc->status == 'Closed')
                                                    <div class="alert alert-secondary">
                                                        <i class="fas fa-lock"></i> This document has been <strong>Closed</strong>. You may still add a comment or download a copy.
                                                    </div>
                                                @elseif($doc->status == 'Rejected')
                                                    <div class="alert alert-danger">
                                                        <i class="fas fa-times-circle"></i> This document has been <strong>Rejected</strong>. You may still add a comment or download a copy.
                                                    </div>
                                                @elseif($isPreviousApprover)
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-check-circle"></i> You have already acted on this document.
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-eye"></i> You are viewing this document for reference.
                                                        @if($nextApprover)
                                                            Currently pending at: <strong>{{ $nextApprover }}</strong>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="buttons">
                                                    <a href="#" class="btn btn-outline-primary w-100 mb-2" id="modal-comment">Add Comment</a>
                                                    <a href="{{ route('download_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning w-100 mb-2">Download PDF</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Complete Document Process button — visible only to creator once all approvals are done --}}
                                @if($isCreator && $isComplete && !in_array($doc->status, ['Completed', 'Closed', 'Rejected', 'Draft', 'Retracted']))
                                    <div style="position:sticky; top: 30px; z-index: 2;" class="mt-2">
                                        <div class="card col-12" style="border: 2px solid #28a745;">
                                            <div class="card-header" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white;">
                                                <h4><i class="fas fa-check-circle"></i> All Approvals Completed</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-success">
                                                    <i class="fas fa-check-double"></i> All <strong>{{ $totalSteps }}</strong> approval step(s) have been completed. As the document creator, you can now finalize the document process.
                                                </div>
                                                <div class="buttons">
                                                    <a href="#" class="btn btn-success w-100 mb-2" id="modal-completed">
                                                        <i class="fas fa-flag-checkered"></i> Complete Document Process
                                                    </a>
                                                    <a href="{{ route('download_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning w-100 mb-2">
                                                        <i class="fas fa-download"></i> Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($process_payment && $user->department == 'Students Welfare')
                                    <div class="col-12 mb-4">
                                        <div class="card shadow" style="border: 2px solid #ff7e5f; border-radius: 10px;">
                                            <div class="card-header text-white" style="background: linear-gradient(135deg, #ff7e5f, #feb47b); border-radius: 8px 8px 0 0;">
                                                <h4 class="mb-0">Payment Processing</h4>
                                            </div>
                                            <div class="card-body" style="font-size: 14px; line-height: 2.0;">
                                                <div class="row mb-2"><div class="col-md-5 font-weight-bold">Exp ID</div><div class="col-md-7">: {{ $process_payment->expenditure_id }}</div></div>
                                                <div class="row mb-2"><div class="col-md-5 font-weight-bold">Assigned to</div><div class="col-md-7">: {{ App\Models\User::find($process_payment->assigned_to)->name }}</div></div>
                                                <div class="row mb-2"><div class="col-md-5 font-weight-bold">Assigned on</div><div class="col-md-7">: {{ date('d/m/Y h:i A', strtotime($process_payment->updated_at ?? $process_payment->created_at)) }}</div></div>
                                                <div class="row align-items-center">
                                                    <div class="col-md-5 font-weight-bold">Status</div>
                                                    <div class="col-md-7 d-flex align-items-center">
                                                        : 
                                                        <div class="btn-group ml-2">
                                                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown">{{ ($process_payment->status == 'Payment In Progress') ? 'Assigned' : $process_payment->status }}</button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="{{ route('change.finance.status', ['status' => 'In Progress', 'finance_id' => $process_payment->id]) }}">In Progress</a>
                                                                <a class="dropdown-item" href="{{ route('change.finance.status', ['status' => 'Hold', 'finance_id' => $process_payment->id]) }}">Hold</a>
                                                                <a class="dropdown-item" href="{{ route('change.finance.status', ['status' => 'Completed', 'finance_id' => $process_payment->id]) }}">Completed</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($totalPaid)
                                    <div class="card col-12" style="background-color: #2a6d98; color: #fff;">
                                        <div class="card-header" style="background-color: #2a6d98; color: #fff; border-bottom: 1px solid #fff;"><h4>Total Paid Amount</h4></div>
                                        <div class="card-body">
                                            <h6 style="color: #fff;"><b>Total Paid:</b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($totalPaid, 2) }}</h6>
                                            <h6 style="color: #fff;"><b>Total TDS:</b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($total_tds, 2) }}</h6>
                                            <hr style="border-bottom: 1px solid #fff;">
                                            <h6 style="color: #fff;"><b>Grand Total:</b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format(($total_tds + $totalPaid), 2) }}</h6>
                                        </div>
                                    </div>
                                @endif
                                
                                @foreach($pay as $paymentDetails)
                                <div class="col-12 mb-4">
                                    <div class="card payment-card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div><h5 class="mb-0 fw-semibold"><i class="fas fa-receipt mr-2"></i>Payment</h5><small class="text-muted">#{{ str_pad($paymentDetails->id, 6, '0', STR_PAD_LEFT) }}</small></div>
                                            @if(Auth::user()->department == 'Students Welfare')
                                            <div class="action-btns">
                                                <a href="{{ route('edit-payment-details', ['id' => $paymentDetails->id]) }}" class="btn btn-light btn-sm" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="{{ route('delete-payment-details', ['id' => $paymentDetails->id]) }}" class="btn btn-light btn-sm text-danger" onclick="return confirm('Delete this payment record?')" title="Delete"><i class="fas fa-trash"></i></a>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div class="amount-box mb-4">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="chip chip-primary">{{ $paymentDetails->payment_type === 'Partial Payment' ? 'Advance Payment' : ($paymentDetails->payment_type === 'Full Payment' ? 'Full Payment' : 'N/A') }}</span>
                                                    <span class="chip chip-mode">{{ $paymentDetails->mode ?? 'N/A' }}</span>
                                                </div>
                                                <div class="amount">₹ {{ number_format((($paymentDetails->paid_amount ?? 0) + ($paymentDetails->tds_amount ?? 0)), 2) }}</div>
                                                @if($paymentDetails->tds_amount > 0)<small class="text-danger">TDS − ₹ {{ number_format($paymentDetails->tds_amount, 2) }}</small>@endif
                                            </div>
                                            <div class="row g-3 mb-3">
                                                @if($paymentDetails->payment_reference_no)<div class="col-md-6"><label>Reference No</label><div class="value">{{ $paymentDetails->payment_reference_no }}</div></div>@endif
                                                @if($paymentDetails->payment_date)<div class="col-md-6"><label>Date</label><div class="value">{{ date('d M Y', strtotime($paymentDetails->payment_date)) }}</div></div>@endif
                                            </div>
                                            <div class="row g-3 mb-3">
                                                @if($paymentDetails->expenditure_id)<div class="col-md-6"><label>Expenditure ID</label><div class="value">{{ $paymentDetails->expenditure_id }}</div></div>@endif
                                                @if($paymentDetails->expenditure_category)<div class="col-md-6"><label>Category</label><div class="value">{{ $paymentDetails->expenditure_category }}</div></div>@endif
                                            </div>
                                            @if($paymentDetails->mode == 'Cheque')
                                            <div class="section mt-4 mb-3">
                                                <h6>Cheque Details</h6>
                                                <div class="row">
                                                    @if($paymentDetails->cheque_issue_date)<div class="col-6"><label>Issued</label><div class="value">{{ date('d M Y', strtotime($paymentDetails->cheque_issue_date)) }}</div></div>@endif
                                                    @if($paymentDetails->cheque_cleared_date)<div class="col-6"><label>Cleared</label><div class="value">{{ date('d M Y', strtotime($paymentDetails->cheque_cleared_date)) }}</div></div>@endif
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row g-3 mb-3">
                                                @if(!empty($paymentDetails->bill_amount) || $paymentDetails->bill_amount === '0' || $paymentDetails->bill_amount === 0)<div class="col-md-6"><label>Bill Amount</label><div class="value">{{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($paymentDetails->bill_amount, 2) }}</div></div>@endif
                                                @if($paymentDetails->bill_submission_date)<div class="col-md-6"><label>Bill Submission Date</label><div class="value">{{ date('d M Y', strtotime($paymentDetails->bill_submission_date)) }}</div></div>@endif
                                            </div>
                                            <div class="row g-3 mb-3">
                                                @if(!empty($paymentDetails->refund_amount) || $paymentDetails->refund_amount === '0' || $paymentDetails->refund_amount === 0)<div class="col-md-6"><label>Refund Amount</label><div class="value">{{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($paymentDetails->refund_amount, 2) }}</div></div>@endif
                                                @if($paymentDetails->refund_date)<div class="col-md-6"><label>Refund Date</label><div class="value">{{ date('d M Y', strtotime($paymentDetails->refund_date)) }}</div></div>@endif
                                            </div>
                                            @if($paymentDetails->remarks)<div class="section mt-4"><h6>Remarks</h6><p class="text-dark mb-0">{{ $paymentDetails->remarks }}</p></div>@endif
                                        </div>
                                        <div class="card-footer d-flex justify-content-between text-muted small">
                                            <span><i class="far fa-clock me-1"></i>{{ date('d M Y', strtotime($paymentDetails->created_at)) }}</span>
                                            @if($paymentDetails->updated_at != $paymentDetails->created_at)<span><i class="fas fa-rotate me-1"></i>{{ date('d M', strtotime($paymentDetails->updated_at)) }}</span>@endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <style>
                                    .payment-card{border:0;border-radius:14px;box-shadow:0 6px 18px rgba(0,0,0,.06);transition:.25s}
                                    .payment-card:hover{transform:translateY(-2px);box-shadow:0 10px 26px rgba(0,0,0,.1)}
                                    .card-header{background:#f7f9fc;border-bottom:0}
                                    .amount-box{background:linear-gradient(135deg,#eef4ff,#f8fbff);border-radius:12px;padding:16px;text-align:center}
                                    .amount{font-size:1.9rem;font-weight:700;color:#1b5ed8}
                                    label{font-size:.75rem;color:#6c757d;text-transform:uppercase}
                                    .value{font-weight:500;color:#000;font-size:1rem}
                                    .section h6{font-weight:600;margin-bottom:.5rem}
                                    .chip{padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600}
                                    .chip-primary{background:#1b5ed8;color:#fff}
                                    .chip-mode{background:#e9ecef;color:#333}
                                    .action-btns .btn{border-radius:50%}
                                </style>

                                <div class="card col-12">
                                    <div class="card-header"><h4>Document Log</h4></div>
                                    <div class="collapse show" id="mycard-collapse">
                                        <div class="card-body">
                                            <div class="col-12">
                                                <div class="activities">
                                                    @foreach($document_logs as $entry)
                                                    <div class="activity">
                                                        <div class="activity-icon bg-primary text-white shadow-primary"><i class="fas fa-comment-alt" style="margin-top:19px"></i></div>
                                                        <div class="activity-detail">
                                                            <div class="mb-2"><span class="text-job text-primary">{{ $entry->created_at }}</span><span class="bullet"></span></div>
                                                            <p>{!! $entry->description !!}</p>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
            @include('frontend.document.partials.modal')
        </div>
        @include('frontend.body.footer')
    </div>
</div>

<script>
    // PDF.js initialization
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    const pdfDocs = {};
    document.querySelectorAll('.pdf-viewer').forEach(div => {
        const id = div.id.replace('viewer-', '');
        const url = div.dataset.url;
        pdfDocs[id] = { url, containerId: div.id, scale: 1.0, pdf: null };
        pdfjsLib.getDocument(url).promise.then(pdf => { pdfDocs[id].pdf = pdf; renderAllPages(id); });
    });
    
    function renderAllPages(id) {
        const { pdf, containerId, scale } = pdfDocs[id];
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        for (let i = 1; i <= pdf.numPages; i++) renderPage(id, i);
    }
    
    function renderPage(id, pageNum) {
        const { pdf, containerId, scale } = pdfDocs[id];
        const container = document.getElementById(containerId);
        if (!container) return;
        pdf.getPage(pageNum).then(page => {
            const unscaledViewport = page.getViewport({ scale: 1.0 });
            const dpr = window.devicePixelRatio || 1;
            const desiredWidth = container.clientWidth;
            const adjustedScale = (desiredWidth / unscaledViewport.width) * scale;
            const viewport = page.getViewport({ scale: adjustedScale });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = viewport.width * dpr;
            canvas.height = viewport.height * dpr;
            canvas.style.width = `${viewport.width}px`;
            canvas.style.height = `${viewport.height}px`;
            context.setTransform(dpr, 0, 0, dpr, 0, 0);
            page.render({ canvasContext: context, viewport });
            canvas.style.marginBottom = "20px";
            container.appendChild(canvas);
        });
    }
    
    function zoomIn(id) { 
        if (pdfDocs[id]) {
            pdfDocs[id].scale += 0.2; 
            renderAllPages(id); 
        }
    }
    
    function zoomOut(id) { 
        if (pdfDocs[id]) {
            pdfDocs[id].scale = Math.max(0.4, pdfDocs[id].scale - 0.2); 
            renderAllPages(id); 
        }
    }
    
</script>

<script>
$(document).ready(function() {
    $('#acknowledgeStbForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find('.btn-stb-ack');

        // Flush summernote editor content into the hidden textarea before serializing
        $form.find('.summernote').each(function() {
            try { $(this).val($(this).summernote('code')); } catch(ex) {}
        });

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        $('#acknowledgeStbModal .modal-body .alert').remove();

        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: $form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#acknowledgeStbModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#acknowledgeStbModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('Acknowledge &amp; Forward');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred. Please try again.';
                try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
                $('#acknowledgeStbModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('Acknowledge &amp; Forward');
            }
        });
    });

    // Reset button state when modal is closed without submitting
    $('#acknowledgeStbModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-stb-ack').prop('disabled', false).html('Acknowledge &amp; Forward');
    });

    // ---- Purchase Order submit ----
    $('#purchaseOrderForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn   = $form.find('.btn-po-submit');
        var fileInput = $form.find('input[name="purchase_order"]')[0];

        if (!fileInput || !fileInput.files.length) {
            $form.find('.po-alert').remove();
            $form.prepend('<div class="alert alert-warning po-alert">Please select a file to upload.</div>');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        $('#purchaseOrderModal .modal-body .alert').remove();

        var formData = new FormData($form[0]);
        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    $('#purchaseOrderModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#purchaseOrderModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('Upload Purchase Order');
                }
            },
            error: function(xhr) {
                var msg = 'Upload failed. Please try again.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || (r.errors ? Object.values(r.errors).flat().join(' ') : msg);
                } catch(ex) {}
                $('#purchaseOrderModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('Upload Purchase Order');
            }
        });
    });

    $('#purchaseOrderModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-po-submit').prop('disabled', false).html('Upload Purchase Order');
        $('#purchaseOrderForm')[0].reset();
    });

    // ---- Work Order submit ----
    $('#workOrderForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn   = $form.find('.btn-wo-submit');
        var fileInput = $form.find('input[name="work_order"]')[0];

        if (!fileInput || !fileInput.files.length) {
            $form.find('.wo-alert').remove();
            $form.prepend('<div class="alert alert-warning wo-alert">Please select a file to upload.</div>');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        $('#workOrderModal .modal-body .alert').remove();

        var formData = new FormData($form[0]);
        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    $('#workOrderModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#workOrderModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('Upload Work Order');
                }
            },
            error: function(xhr) {
                var msg = 'Upload failed. Please try again.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || (r.errors ? Object.values(r.errors).flat().join(' ') : msg);
                } catch(ex) {}
                $('#workOrderModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('Upload Work Order');
            }
        });
    });

    $('#workOrderModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-wo-submit').prop('disabled', false).html('Upload Work Order');
        $('#workOrderForm')[0].reset();
    });

    // ---- Select Finance Head submit (PA to Chairman / PA to GM) ----
    $('#selectFinanceHeadForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find('.btn-fh-submit');
        if (!$form.find('select[name="finance_head"]').val()) {
            $form.find('.fh-alert').remove();
            $form.prepend('<div class="alert alert-warning fh-alert">Please select a Finance Head.</div>');
            return;
        }
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Forwarding...');
        $('#selectFinanceHeadModal .modal-body .alert').remove();
        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: $form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#selectFinanceHeadModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#selectFinanceHeadModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('Forward to Finance Head');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred. Please try again.';
                try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
                $('#selectFinanceHeadModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('Forward to Finance Head');
            }
        });
    });

    $('#selectFinanceHeadModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-fh-submit').prop('disabled', false).html('Forward to Finance Head');
        $('#selectFinanceHeadForm')[0].reset();
    });

    // ---- Finance Head: Create Finance Details ----
    $('#financeDetailsForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find('.btn-fd-submit');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        $('#financeDetailsModal .modal-body .alert').remove();
        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: $form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#financeDetailsModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#financeDetailsModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('Submit Finance Details');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred. Please try again.';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || (r.errors ? Object.values(r.errors).flat().join(' ') : msg);
                } catch(ex) {}
                $('#financeDetailsModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('Submit Finance Details');
            }
        });
    });

    $('#financeDetailsModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-fd-submit').prop('disabled', false).html('Submit Finance Details');
        $('#financeDetailsForm')[0].reset();
    });

    // ---- Finance Head: Forward to another Finance location ----
    $('#financeForwardForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find('.btn-fhf-submit');
        if (!$form.find('select[name="forward_to"]').val()) {
            $form.find('.fhf-alert').remove();
            $form.prepend('<div class="alert alert-warning fhf-alert">Please select a Finance location.</div>');
            return;
        }
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Forwarding...');
        $('#financeForwardModal .modal-body .alert').remove();
        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: $form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#financeForwardModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#financeForwardModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Forward');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred. Please try again.';
                try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
                $('#financeForwardModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Forward');
            }
        });
    });

    $('#financeForwardModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-fhf-submit').prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Forward');
        $('#financeForwardForm')[0].reset();
    });

    // ---- Chairman Approval ----
    $('#chairmanApprovalForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find('.btn-chairman-submit');

        $form.find('.summernote').each(function() {
            try { $(this).val($(this).summernote('code')); } catch(ex) {}
        });

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');
        $('#chairmanApprovalModal .modal-body .alert').remove();

        $.ajax({
            type: 'POST',
            url: '/change/document/status',
            data: $form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#chairmanApprovalModal .modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    $('#chairmanApprovalModal .modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
                    btn.prop('disabled', false).html('<i class="fas fa-check-double"></i> Approve');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred. Please try again.';
                try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
                $('#chairmanApprovalModal .modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
                btn.prop('disabled', false).html('<i class="fas fa-check-double"></i> Approve');
            }
        });
    });

    $('#chairmanApprovalModal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body .alert').remove();
        $(this).find('.btn-chairman-submit').prop('disabled', false).html('<i class="fas fa-check-double"></i> Approve');
        $('#chairmanApprovalForm')[0].reset();
        try { $('#chairmanApprovalForm .summernote').summernote('code', ''); } catch(ex) {}
    });
});
</script>

@endsection
@include('frontend.postal.script')