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
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .approval-status-item {
        text-align: center;
        min-width: 80px;
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
    
    .approval-status-icon.current {
        color: #ffc107;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.1); }
        100% { opacity: 0.6; transform: scale(1); }
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
    
    .approval-status-label.current {
        color: #ffc107;
        font-weight: bold;
    }
    
    .approval-status-label.pending {
        color: #999;
    }
    
    .approval-arrow {
        color: #ccc;
        font-size: 14px;
        margin: 0 2px;
    }
    
    .report-table th, .report-table td {
        vertical-align: middle;
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
            <h1>Report</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Report</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Report</h2>
            <p class="section-lead">
              Document Approval Report
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>Document Report</h4>
                                <div>
                                    <a href="{{ route('download_report_pdf') }}" class="btn btn-danger btn-sm mr-2">
                                        <i class="fas fa-file-pdf"></i> Download PDF
                                    </a>
                                    <a href="{{ route('download_report_excel') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Download Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Search Form -->
                            <div class="mb-4">
                                <form method="GET" action="{{ route('report_documents') }}" class="form-row align-items-end">
                                    <div class="form-group col-md-3">
                                        <label>Document ID</label>
                                        <input type="text" name="doc_id" class="form-control" placeholder="Search by Document ID" value="{{ request('doc_id') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Title</label>
                                        <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>From Department</label>
                                        <select name="section" class="form-control">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $section)
                                                <option value="{{ $section->dept_label }}" {{ request('section') == $section->dept_label ? 'selected' : '' }}>{{ $section->dept_label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>From Date</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>To Date</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <a href="{{ route('report_documents') }}" class="btn btn-secondary btn-block">Reset</a>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped report-table" id="report-table" style="text-align: center;">
                                    <thead>
                                         <tr>
                                            <th style="width:5%">S.No</th>
                                            <th style="width:12%">DOC ID</th>
                                            <th style="width:20%">Title</th>
                                            <th style="width:10%">From</th>
                                            <th style="width:8%">Priority</th>
                                            <th style="width:10%">Amount</th>
                                            <th style="width:25%">Approval Progress</th>
                                            <th style="width:10%">Created</th>
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
                                                    ->where(function($query) {
                                                        $query->where('status', 'like', 'Approved by%')
                                                              ->orWhere('status', 'like', 'Noted by%')
                                                              ->orWhere('status', 'like', 'Completed by%')
                                                              ->orWhere('status', 'like', 'Closed by%');
                                                    })
                                                    ->pluck('status')
                                                    ->toArray();
                                                
                                                // Determine which approvers have completed
                                                $completedApprovers = [];
                                                foreach ($completedLogs as $log) {
                                                    // Extract department name from status
                                                    if (preg_match('/Approved by (.+?)(?:\s|$)/', $log, $matches)) {
                                                        $completedApprovers[] = trim($matches[1]);
                                                    } elseif (preg_match('/Noted by (.+?)(?:\s|$)/', $log, $matches)) {
                                                        $completedApprovers[] = trim($matches[1]);
                                                    } elseif (preg_match('/Completed by (.+?)(?:\s|$)/', $log, $matches)) {
                                                        $completedApprovers[] = trim($matches[1]);
                                                    } elseif (preg_match('/Closed by (.+?)(?:\s|$)/', $log, $matches)) {
                                                        $completedApprovers[] = trim($matches[1]);
                                                    }
                                                }
                                                
                                                $currentApprover = isset($approvalSequence[$currentIndex]) ? $approvalSequence[$currentIndex] : null;
                                                
                                                // Calculate completion percentage
                                                $totalSteps = count($approvalSequence);
                                                $completedSteps = 0;
                                                foreach ($approvalSequence as $index => $approver) {
                                                    if (in_array($approver, $completedApprovers) || $index < $currentIndex) {
                                                        $completedSteps++;
                                                    }
                                                }
                                                $completionPercent = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $sno++ }}</td>
                                                <td>{{ $doc->doc_id }}</td>
                                                <td style="text-align: left;">
                                                    <a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">
                                                        {{ \Illuminate\Support\Str::limit($doc->title, 50) }}
                                                    </a>
                                                </td>
                                                <td>{{ $doc->from }}</td>
                                                <td>
                                                    @if($doc->priority == 'High')
                                                        <span class="badge badge-danger">{{ $doc->priority }}</span>
                                                    @elseif($doc->priority == 'Medium')
                                                        <span class="badge badge-warning">{{ $doc->priority }}</span>
                                                    @else
                                                        <span class="badge badge-info">{{ $doc->priority }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($doc->amount)
                                                        {{ isset($doc->currency) ? $doc->currency : '₹' }} {{ number_format($doc->amount, 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($approvalSequence))
                                                        <div class="approval-status-container">
                                                            @foreach($approvalSequence as $index => $approver)
                                                                @php
                                                                    $isCompleted = in_array($approver, $completedApprovers) || $index < $currentIndex;
                                                                    $isCurrent = $index == $currentIndex && !$isCompleted && $doc->status != 'Completed' && $doc->status != 'Closed';
                                                                    $iconClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                                                                    $iconSymbol = $isCompleted ? '✓' : ($isCurrent ? '▶' : '○');
                                                                    $labelClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                                                                    
                                                                    // Shorten department names for display
                                                                    $shortName = $approver;
                                                                    if(strlen($approver) > 15) {
                                                                        $shortName = substr($approver, 0, 12) . '...';
                                                                    }
                                                                @endphp
                                                                <div class="approval-status-item">
                                                                    <div class="approval-status-icon {{ $iconClass }}">
                                                                        {{ $iconSymbol }}
                                                                    </div>
                                                                    <span class="approval-status-label {{ $labelClass }}" title="{{ $approver }}">
                                                                        {{ $shortName }}
                                                                    </span>
                                                                </div>
                                                                @if(!$loop->last)
                                                                    <span class="approval-arrow">→</span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-1">
                                                            <div class="progress" style="height: 5px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercent }}%;" aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <small class="text-muted">{{ $completionPercent }}% Complete</small>
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
                                                            <span class="approval-arrow">→</span>
                                                            <div class="approval-status-item">
                                                                <div class="approval-status-icon {{ $flagProVC ? 'completed' : 'pending' }}">
                                                                    {!! $flagProVC ? '✓' : '○' !!}
                                                                </div>
                                                                <span class="approval-status-label">Pro-VC</span>
                                                            </div>
                                                            <span class="approval-arrow">→</span>
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
                                            </tr>
                                        @endforeach
                                        
                                        @if(count($docs) == 0)
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-5">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p>No documents found</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                {!! $docs->links('frontend.pagination.custom') !!}
                            </div>
                            
                            <div class="mt-3 text-muted">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    <span style="color: #28a745;">✓</span> Completed &nbsp;&nbsp;
                                    <span style="color: #ffc107;">▶</span> Current Step &nbsp;&nbsp;
                                    <span style="color: #ccc;">○</span> Pending
                                </small>
                            </div>
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
  <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
        
        // Add print functionality
        function printReport() {
            window.print();
        }
  </script>
@endsection