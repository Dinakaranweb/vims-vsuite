@extends('frontend.frontend_master')

@section('content')

<style>

    .approval-flowchart-container {
        overflow-x: auto;
        padding: 10px;
        max-width:500px;
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        background-color: #fff;
        position: relative;
    }

    .step .vc{
        color: red;
    }

    .step .provc{
        color: green;
    }

    .step .registrar{
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

    /* Connector styling */
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
    
    /* Dynamic approval status styles */
    .approval-status-container {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .approval-status-item {
        text-align: center;
        min-width: 70px;
    }
    
    .approval-status-icon {
        font-size: 22px;
    }
    
    .approval-status-icon.completed {
        color: #28a745;
    }
    
    .approval-status-icon.pending {
        color: #ccc;
    }
    
    .approval-status-icon.current {
        color: #ffc107;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    
    .approval-status-label {
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
    
    .approval-status-label.completed {
        color: #28a745;
        font-weight: bold;
    }
    
    .approval-status-label.current {
        color: #ffc107;
        font-weight: bold;
    }
    
    .approval-status-label.pending {
        color: #999;
    }

</style>

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @if(Auth::user()->role == 'SuperAdmin')
            
                @include('frontend.superadmin.body.header')
               @include('frontend.superadmin.body.sidebar')
            
            @else
            
                @include('frontend.admin.body.header')
                @include('frontend.admin.body.sidebar')
            
            @endif

        <!-- Main Content -->
        <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>All Requests</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active">Dashboard</div>
              <div class="breadcrumb-item">All Requests</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">All Requests</h2>
            <p class="section-lead">
              Following are the list of requests placed
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    
                    <div class="card">
                        <div class="card-header">
                            <h4>All Requests</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1" style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th style="width:5%">S.No</th>
                                        <th style="width:15%">DOC ID</th>
                                        <th style="width:20%">Title</th>
                                        <th style="width:8%">Priority</th>
                                        @if(Auth::user()->role == 'SuperAdmin')
                                            <th style="width:10%">From</th>
                                        @else
                                            <th style="width:10%">Status</th>
                                        @endif
                                        <th style="width:25%">Approval Progress</th>
                                        <th style="width:12%">Created</th>
                                        <th style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sno = ($docs->currentPage() - 1) * $docs->perPage() + 1;
                                    @endphp
                                    @foreach ($docs as $doc)
                                        
                                        @php
                                            // Get approval sequence and current progress
                                            $approvalSequence = [];
                                            $currentIndex = 0;
                                            $completedApprovals = [];
                                            
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
                                            
                                            // Get completed approvals from approval_log
                                            $completedLogs = DB::table('approval_log')
                                                ->where('doc_id', $doc->id)
                                                ->where('status', 'like', 'Approved by%')
                                                ->orWhere('status', 'like', 'Noted by%')
                                                ->orWhere('status', 'like', 'Completed by%')
                                                ->pluck('status')
                                                ->toArray();
                                            
                                            // Determine which approvers have completed
                                            $completedApprovers = [];
                                            foreach ($completedLogs as $log) {
                                                // Extract department name from status (e.g., "Approved by Medical Director" -> "Medical Director")
                                                if (preg_match('/Approved by (.+)/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                } elseif (preg_match('/Noted by (.+)/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                } elseif (preg_match('/Completed by (.+)/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                }
                                            }
                                            
                                            $currentApprover = isset($approvalSequence[$currentIndex]) ? $approvalSequence[$currentIndex] : null;
                                        @endphp
                                        
                                        <tr data-id="{{ $doc->id }}">
                                            <td>{{ $sno++ }}</td>
                                            <td>{{ $doc->doc_id }}</td>
                                            <td><a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">{{ $doc->title }}</a></td>
                                            <td>{{ $doc->priority }}</td>
                                            @if(Auth::user()->role == 'SuperAdmin')
                                                <td>{{ $doc->from }}</td>
                                            @else
                                                <td>{{ $doc->status }}</td>
                                            @endif                                        
                                            <td>
                                                @if(!empty($approvalSequence))
                                                    <div class="approval-status-container">
                                                        @foreach($approvalSequence as $index => $approver)
                                                            @php
                                                                $isCompleted = in_array($approver, $completedApprovers) || $index < $currentIndex;
                                                                $isCurrent = $index == $currentIndex && !$isCompleted;
                                                                $iconClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                                                                $iconSymbol = $isCompleted ? '✓' : ($isCurrent ? '▶' : '○');
                                                                $labelClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                                                            @endphp
                                                            <div class="approval-status-item">
                                                                <div class="approval-status-icon {{ $iconClass }}">
                                                                    {{ $iconSymbol }}
                                                                </div>
                                                                <span class="approval-status-label {{ $labelClass }}">
                                                                    {{ $approver }}
                                                                </span>
                                                            </div>
                                                            @if(!$loop->last)
                                                                <span style="color: #ccc;">→</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <!-- Fallback for old documents without approval sequence -->
                                                    @php
                                                        $flagRegistrar = DB::table('approval_log')->where('doc_id', $doc->id)
                                                            ->whereIn('status', ['Approved by Registrar', 'Noted by Registrar'])->first();
                                                        
                                                        $flagProVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                            ->whereIn('status', ['Approved by Pro VC', 'Noted by Pro-VC'])->first();

                                                        $flagVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                            ->whereIn('status', ['Approved by VC', 'VC Approved in Principle', 'Noted by VC'])->first();
                                                    @endphp
                                                    <div class="approval-status-container">
                                                        <div class="approval-status-item">
                                                            <div class="approval-status-icon {{ $flagRegistrar ? 'completed' : 'pending' }}">
                                                                {!! $flagRegistrar ? '✓' : '○' !!}
                                                            </div>
                                                            <span class="approval-status-label">Registrar</span>
                                                        </div>
                                                        <span style="color: #ccc;">→</span>
                                                        <div class="approval-status-item">
                                                            <div class="approval-status-icon {{ $flagProVC ? 'completed' : 'pending' }}">
                                                                {!! $flagProVC ? '✓' : '○' !!}
                                                            </div>
                                                            <span class="approval-status-label">Pro-VC</span>
                                                        </div>
                                                        <span style="color: #ccc;">→</span>
                                                        <div class="approval-status-item">
                                                            <div class="approval-status-icon {{ $flagVC ? 'completed' : 'pending' }}">
                                                                {!! $flagVC ? '✓' : '○' !!}
                                                            </div>
                                                            <span class="approval-status-label">VC</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-primary btn-action mr-1 toggle-accordion" data-toggle="tooltip" title="View" data-target="#accordion-{{ $doc->id }}">
                                                    <i class="fas fa-solid fa-eye"></i>
                                                </a>
                                                @if($doc->status == 'Draft' && $doc->by == Auth::id())
                                                <a href="{{ route('edit_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-solid fa-pen"></i>
                                                </a>
                                                <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $doc->id }})"><i class="fas fa-trash"></i></a>
                                                @endif                                                
                                            </td>
                                        </tr>
                                        <!-- Accordion row -->
                                        <tr id="accordion-{{ $doc->id }}" class="accordion-content" style="display: none;">
                                            <td colspan="4" style="border-right: none">
                                                
                                                <div class="accordion-details" style="text-align: left">
                                                    <p><b>Document ID :</b> {{ $doc->doc_id }}</p>
                                                    <p><b>Sent by :</b> {{ App\Models\User::find($doc->by)->name }}, {{ App\Models\User::find($doc->by)->department }}</p>
                                                    <p><b>From :</b> {{ $doc->from }}</p>
                                                    <p><b>Subject :</b> {!! strip_tags($doc->subject, '<span><b><i><u>') !!}</p>
                                                    @if($doc->amount)
                                                        <p><b>Amount :</b> {{ $doc->amount }}</p>
                                                    @endif
                                                    <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
                                                </div>
                                            </td>
                                            <td colspan="4">
                                                <div class="approval-flowchart-container">
                                                    <div class="approval-flowchart">
                                                            @php
                                                                $approval_logs = DB::table('approval_log')->where('doc_id', $doc->id)->latest()->get();
                                                            @endphp
                                                            @foreach($approval_logs as $log)

                                                                @php
                                                                    $userRole = \App\Models\User::find($log->by);
                                                                    $role = $userRole ? $userRole->department : '';
                                                                    if($role == 'Pro-VC'){
                                                                        $style = 'provc';
                                                                    }elseif($role == 'VC'){
                                                                        $style = 'vc';
                                                                    }elseif($role == 'Registrar'){
                                                                        $style = 'registrar';
                                                                    }else{
                                                                        $style = '';
                                                                    } 
                                                                @endphp
                                                        
                                                                <div class="step">
                                                                    <div class="icon">&#x1F4A1;</div>
                                                                    <div class="{{ $style }}">
                                                                        <p>{!! $log->status !!}</p>
                                                                        <hr>
                                                                        <span class="description">{!! $log->message !!}</span>
                                                                        <hr>
                                                                        <p>{{ date('d/m/Y h:i A', strtotime($log->created_at)) }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                @if (!$loop->last)
                                                                    <div class="connector">&#x2190;</div>
                                                                @endif
                                                        
                                                            @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <script>
                                        function handleConfirmYes(docId) {
                                            window.location.href = "{{ url('/delete/document') }}/" + docId;
                                        }
                                    </script>
                                </tbody>
                            </table>
                            {!! $docs->links('frontend.pagination.custom') !!}
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                document.querySelectorAll(".toggle-accordion").forEach(button => {
                                    button.addEventListener("click", function () {
                                        const targetId = this.getAttribute("data-target");
                                        const targetRow = document.querySelector(targetId);
                                        
                                        if (targetRow.style.display === "none") {
                                            targetRow.style.display = "table-row";
                                        } else {
                                            targetRow.style.display = "none";
                                        }
                                    });
                                });
                            });
                        </script>

                        </div>
                    </div>
                </div>
            </div>
          </div>
        </section>
      </div>
        @include('frontend.body.footer')
        </div>
  </div>
@endsection