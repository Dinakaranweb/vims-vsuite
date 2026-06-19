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
        0% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.1); }
        100% { opacity: 0.6; transform: scale(1); }
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
    
    .approval-arrow {
        color: #ccc;
        font-size: 14px;
        margin: 0 2px;
    }
    
    .progress-bar-container {
        width: 100%;
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .progress-bar-fill {
        height: 6px;
        background-color: #28a745;
        width: 0%;
        transition: width 0.3s ease;
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
            <h1>In Progress Documents</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">In Progress Documents</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">In Progress Documents</h2>
            <p class="section-lead">
              Documents that are currently in the approval process
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Search Documents</h4>
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#searchCard" aria-expanded="false" aria-controls="searchCard">
                                Toggle Search
                            </button>
                        </div>
                        <div id="searchCard" class="collapse">
                            <div class="card-body">
                                @if(Auth::user()->role == 'SuperAdmin')
                                    <form method="GET" action="{{ route('sa_search_documents') }}" class="form-inline" style="margin-top: 10px;">
                                @else
                                    <form method="GET" action="{{ route('search_documents') }}" class="form-inline" style="margin-top: 10px;">
                                @endif
                                
                                    <input type="hidden" name="type" value="inProgress">
                                    <div class="form-group mr-2">
                                        <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="text" name="doc_id" class="form-control" placeholder="Search by Document ID" value="{{ request('doc_id') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="section" class="form-control">
                                            <option value="">Search by Section</option>
                                            @foreach($departments as $section)
                                                <option value="{{ $section->dept_label }}" {{ request('section') == $section->dept_label ? 'selected' : '' }}>{{ $section->dept_label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="approval_status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="Sent to Medical Director" {{ request('approval_status') == 'Sent to Medical Director' ? 'selected' : '' }}>Sent to Medical Director</option>
                                            <option value="Sent to General Manager" {{ request('approval_status') == 'Sent to General Manager' ? 'selected' : '' }}>Sent to General Manager</option>
                                            <option value="Sent to Purchase Head" {{ request('approval_status') == 'Sent to Purchase Head' ? 'selected' : '' }}>Sent to Purchase Head</option>
                                            <option value="Sent to Finance Head Salem" {{ request('approval_status') == 'Sent to Finance Head Salem' ? 'selected' : '' }}>Sent to Finance Head Salem</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="date" name="date_from" class="form-control" placeholder="Date From" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="date" name="date_to" class="form-control" placeholder="Date To" value="{{ request('date_to') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('inProgress_documents') }}" class="btn btn-secondary ml-2">Reset</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Latest Documents</h4>
                            <a href="{{ route('download_report_doc') }}" class="btn btn-success" target="_blank" style="margin-left:auto;">
                                <i class="fas fa-download"></i> Download as PDF
                            </a>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1" style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th style="width:5%">S.No</th>
                                        <th style="width:12%">
                                            DOC ID
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'doc_id', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'doc_id' ? 'desc' : 'asc']) }}">
                                                {!! request('sort_by') == 'doc_id' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                            </a>
                                        </th>
                                        <th style="width:18%">
                                            Title
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'title' ? 'desc' : 'asc']) }}">
                                                {!! request('sort_by') == 'title' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                            </a>
                                        </th>
                                        <th style="width:8%">
                                            Priority
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'priority', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'priority' ? 'desc' : 'asc']) }}">
                                                {!! request('sort_by') == 'priority' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                            </a>
                                        </th>
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
                                            $completedApprovers = [];
                                            
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
                                                          ->orWhere('status', 'like', 'Forwarded to%by%');
                                                })
                                                ->pluck('status')
                                                ->toArray();
                                            
                                            // Determine which approvers have completed
                                            foreach ($completedLogs as $log) {
                                                if (preg_match('/Approved by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                } 
                                                elseif (preg_match('/Noted by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                }
                                                elseif (preg_match('/Completed by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                }
                                                elseif (preg_match('/Forwarded to\s+(.+?)\s+by/', $log, $matches)) {
                                                    $completedApprovers[] = trim($matches[1]);
                                                }
                                            }
                                            
                                            $completedApprovers = array_unique($completedApprovers);
                                            
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
                                        
                                        <tr data-id="{{ $doc->id }}">
                                            <td>{{ $sno++ }}</td>
                                            <td>{{ $doc->doc_id }}</td>
                                            <td style="text-align: left;">
                                                <a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">
                                                    {{ \Illuminate\Support\Str::limit($doc->title, 50) }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($doc->priority == 'High')
                                                    <span class="badge badge-danger">{{ $doc->priority }}</span>
                                                @elseif($doc->priority == 'Medium')
                                                    <span class="badge badge-warning">{{ $doc->priority }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ $doc->priority }}</span>
                                                @endif
                                            </td>
                                            @if(Auth::user()->role == 'SuperAdmin')
                                                <td>{{ $doc->from }}</td>
                                            @else
                                                <td>
                                                    <span class="badge badge-primary">{{ $doc->status }}</span>
                                                </td>
                                            @endif                                        
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
                                                                if(strlen($approver) > 12) {
                                                                    $shortName = substr($approver, 0, 10) . '...';
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
                                                    <div class="progress-bar-container">
                                                        <div class="progress-bar-fill" style="width: {{ $completionPercent }}%;"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $completionPercent }}% Complete</small>
                                                    @if($nextApprover = $doc->next_approver)
                                                        <div class="mt-1">
                                                            <small class="text-warning">
                                                                <i class="fas fa-clock"></i> Pending at: {{ $nextApprover }}
                                                            </small>
                                                        </div>
                                                    @endif
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
                                            <td colspan="8" style="padding: 20px;">
                                                <div class="accordion-details" style="text-align: left">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><b>Document ID :</b> {{ $doc->doc_id }}</p>
                                                            <p><b>Sent by :</b> {{ App\Models\User::find($doc->by)->name }}, {{ App\Models\User::find($doc->by)->department }}</p>
                                                            <p><b>From :</b> {{ $doc->from }}</p>
                                                            <p><b>Subject :</b> {!! strip_tags($doc->subject, '<span><b><i><u>') !!}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><b>Priority :</b> {{ $doc->priority }}</p>
                                                            @if($doc->amount)
                                                                <p><b>Amount :</b> {{ isset($doc->currency) ? $doc->currency : '₹' }} {{ number_format($doc->amount, 2) }}</p>
                                                            @endif
                                                            <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
                                                            <p><b>Current Status :</b> {{ $doc->status }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <p><b>Description :</b> {!! strip_tags($doc->description, '<span><b><i><u>') !!}</p>
                                                            @if($doc->justification)
                                                                <p><b>Justification :</b> {!! strip_tags($doc->justification, '<span><b><i><u>') !!}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if(!empty($approvalSequence))
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="approval-flowchart-container">
                                                                <div class="approval-flowchart">
                                                                    @foreach($approvalSequence as $index => $step)
                                                                        @php
                                                                            $isCompleted = in_array($step, $completedApprovers) || $index < $currentIndex;
                                                                            $isCurrent = $index == $currentIndex && !$isCompleted;
                                                                            $stepClass = $isCompleted ? 'path-step-completed' : ($isCurrent ? 'path-step-current' : 'path-step-pending');
                                                                            $icon = $isCompleted ? '✓' : ($isCurrent ? '▶' : '○');
                                                                        @endphp
                                                                        <div class="step">
                                                                            <div class="icon">&#x1F4A1;</div>
                                                                            <div>
                                                                                <p>{{ $step }}</p>
                                                                                <hr>
                                                                                <p>{{ $icon }} {{ $isCompleted ? 'Completed' : ($isCurrent ? 'In Progress' : 'Pending') }}</p>
                                                                            </div>
                                                                        </div>
                                                                        @if($index < count($approvalSequence) - 1)
                                                                            <div class="connector">&#x2190;</div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                            
                            function handleConfirmYes(docId) {
                                if(confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                                    window.location.href = "{{ url('/delete/document') }}/" + docId;
                                }
                            }
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
  <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
  </script>
@endsection