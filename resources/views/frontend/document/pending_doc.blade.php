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

    .step .approved {
        color: #28a745;
    }

    .step .pending {
        color: #ffc107;
    }

    .step .rejected {
        color: #dc3545;
    }

    .step .in-progress {
        color: #17a2b8;
    }

    .step .completed {
        color: #28a745;
        font-weight: bold;
    }

    .step .current {
        background-color: #e3f2fd;
        border: 2px solid #2196F3;
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

    /* Status badge styles */
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
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

    .status-completed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    @media (max-width: 768px) {
        .approval-flowchart {
            gap: 10px;
        }
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
            <h1>Pending Requests</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active">Dashboard</div>
              <div class="breadcrumb-item">Pending Requests</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Pending Requests</h2>
            <p class="section-lead">
              Following are the list of requests placed at your table
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
                                <form method="GET" action="{{ route('sa_search_documents') }}" class="form-inline" style="margin-top: 10px;">
                                    <input type="hidden" name="type" value="received">
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
                                        <input type="date" name="date_from" class="form-control" placeholder="Date From" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="date" name="date_to" class="form-control" placeholder="Date To" value="{{ request('date_to') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('received_documents') }}" class="btn btn-secondary ml-2">Reset</a>
                                </form>
                            </div>
                        </div>
                    </div>
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
                                        <th style="width:20">DOC ID</th>
                                        <th style="width:20%">Title</th>
                                        <th style="width:5%">Priority</th>
                                        @if(Auth::user()->role == 'SuperAdmin')
                                            <th style="width:10%">From</th>
                                        @else
                                            <th style="width:10%">Status</th>
                                        @endif
                                        <th style="width:20%">Approval Progress</th>
                                        <th style="width:15%">Created</th>
                                        <th style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sno = ($docs->currentPage() - 1) * $docs->perPage() + 1;
                                    @endphp
                                    @foreach ($docs as $doc)
                                        
                                        @php
                                            // Get the approval sequence from the document
                                            $approvalSequence = json_decode($doc->approval_sequence, true) ?? [];
                                            $currentIndex = $doc->current_sequence_index ?? 0;
                                            $totalSteps = count($approvalSequence);
                                            
                                            // Get all approval logs grouped by approver
                                            $approvalLogs = DB::table('approval_log')
                                                ->where('doc_id', $doc->id)
                                                ->get()
                                                ->keyBy('status');
                                            
                                            // Determine approval status for each step in sequence
                                            $stepStatuses = [];
                                            foreach ($approvalSequence as $index => $approver) {
                                                $logExists = DB::table('approval_log')
                                                    ->where('doc_id', $doc->id)
                                                    ->where('status', 'like', "Approved by $approver%")
                                                    ->orWhere('status', 'like', "$approver Approved%")
                                                    ->exists();
                                                
                                                if ($logExists) {
                                                    $stepStatuses[$approver] = 'approved';
                                                } elseif ($index == $currentIndex && $doc->status != 'Completed' && $doc->status != 'Rejected') {
                                                    $stepStatuses[$approver] = 'current';
                                                } elseif ($index < $currentIndex) {
                                                    $stepStatuses[$approver] = 'completed';
                                                } else {
                                                    $stepStatuses[$approver] = 'pending';
                                                }
                                            }
                                            
                                            // If document is fully approved
                                            if ($doc->status == 'Completed' || ($currentIndex >= $totalSteps && $totalSteps > 0)) {
                                                $progressStatus = 'Completed';
                                                $progressPercent = 100;
                                                $progressClass = 'status-completed';
                                            } elseif ($doc->status == 'Rejected') {
                                                $progressStatus = 'Rejected';
                                                $progressPercent = 0;
                                                $progressClass = 'status-rejected';
                                            } else {
                                                $progressPercent = $totalSteps > 0 ? round(($currentIndex / $totalSteps) * 100) : 0;
                                                $progressStatus = "Step {$currentIndex} of {$totalSteps}";
                                                $progressClass = 'status-pending';
                                            }
                                            
                                            $nextApprover = $approvalSequence[$currentIndex] ?? 'None';
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
                                                <div style="min-width: 150px;">
                                                    <div class="progress mb-2" style="height: 8px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <span class="status-badge {{ $progressClass }}">
                                                        {{ $progressStatus }}
                                                    </span>
                                                    @if($doc->status != 'Completed' && $doc->status != 'Rejected' && $nextApprover != 'None')
                                                        <br><small class="text-muted">Next: {{ $nextApprover }}</small>
                                                    @endif
                                                </div>
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
                                        <!-- Accordion row with dynamic approval flowchart -->
                                        <tr id="accordion-{{ $doc->id }}" class="accordion-content" style="display: none;">
                                            <td colspan="4" style="border-right: none">
                                                
                                                <div class="accordion-details" style="text-align: left">
                                                    <p><b>Document ID :</b> {{ $doc->doc_id }}</p>
                                                    <p><b>Sent by :</b> {{ App\Models\User::find($doc->by)->name ?? 'Unknown' }}, {{ App\Models\User::find($doc->by)->department ?? 'Unknown' }}</p>
                                                    <p><b>From :</b> {{ $doc->from }}</p>
                                                    <p><b>Subject :</b> {!! strip_tags($doc->subject, '<span><b><i><u>') !!}</p>
                                                    @if($doc->amount)
                                                        <p><b>Amount :</b> {{ $doc->currency ?? 'INR' }} {{ number_format($doc->amount, 2) }}</p>
                                                    @endif
                                                    <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
                                                </div>
                                            </td>
                                            <td colspan="4">
                                                <div class="approval-flowchart-container">
                                                    <div class="approval-flowchart">
                                                        @php
                                                            // Build dynamic flowchart based on approval sequence
                                                            $displaySteps = [];
                                                            
                                                            // Add sequence steps
                                                            foreach ($approvalSequence as $index => $approver) {
                                                                $status = $stepStatuses[$approver] ?? 'pending';
                                                                
                                                                // Get the actual approval log for this step
                                                                $logEntry = DB::table('approval_log')
                                                                    ->where('doc_id', $doc->id)
                                                                    ->where(function($query) use ($approver) {
                                                                        $query->where('status', 'like', "Approved by $approver%")
                                                                              ->orWhere('status', 'like', "$approver Approved%");
                                                                    })
                                                                    ->first();
                                                                
                                                                $displaySteps[] = [
                                                                    'approver' => $approver,
                                                                    'status' => $status,
                                                                    'log' => $logEntry,
                                                                    'isCurrent' => ($status == 'current'),
                                                                    'index' => $index
                                                                ];
                                                            }
                                                        @endphp
                                                        
                                                        @foreach($displaySteps as $step)
                                                            @php
                                                                $stepClass = '';
                                                                if ($step['status'] == 'approved') {
                                                                    $stepClass = 'approved';
                                                                } elseif ($step['status'] == 'current') {
                                                                    $stepClass = 'in-progress current';
                                                                } elseif ($step['status'] == 'completed') {
                                                                    $stepClass = 'completed';
                                                                } else {
                                                                    $stepClass = 'pending';
                                                                }
                                                                
                                                                $icon = $step['status'] == 'approved' ? '✓' : ($step['status'] == 'current' ? '⟳' : '○');
                                                                $statusText = ucfirst($step['status']);
                                                                $message = $step['log']->message ?? ($step['status'] == 'current' ? 'Awaiting approval from ' . $step['approver'] : 'Pending');
                                                                $createdAt = $step['log'] ? date('d/m/Y h:i A', strtotime($step['log']->created_at)) : '';
                                                            @endphp
                                                            
                                                            <div class="step {{ $step['status'] == 'current' ? 'current' : '' }}">
                                                                <div class="icon">
                                                                    @if($step['approver'] == 'Medical Director')
                                                                        👨‍⚕️
                                                                    @elseif($step['approver'] == 'General Manager')
                                                                        📊
                                                                    @elseif($step['approver'] == 'STB Office')
                                                                        🏛️
                                                                    @elseif($step['approver'] == 'Chairman')
                                                                        👔
                                                                    @elseif(str_contains($step['approver'], 'Purchase'))
                                                                        🛒
                                                                    @elseif(str_contains($step['approver'], 'Finance'))
                                                                        💰
                                                                    @else
                                                                        📋
                                                                    @endif
                                                                </div>
                                                                <div class="{{ $stepClass }}">
                                                                    <p><strong>{{ $step['approver'] }}</strong></p>
                                                                    <p><span class="status-badge status-{{ $step['status'] == 'current' ? 'pending' : $step['status'] }}">
                                                                        @if($step['status'] == 'approved')
                                                                            ✅ Approved
                                                                        @elseif($step['status'] == 'current')
                                                                            🔄 In Progress
                                                                        @elseif($step['status'] == 'completed')
                                                                            ✔️ Completed
                                                                        @else
                                                                            ⏳ Pending
                                                                        @endif
                                                                    </span></p>
                                                                    @if($step['log'])
                                                                        <hr>
                                                                        <span class="description" style="font-size: 12px;">{{ Str::limit($step['log']->message, 100) }}</span>
                                                                        <hr>
                                                                        <p style="font-size: 11px;">{{ $createdAt }}</p>
                                                                    @elseif($step['status'] == 'current')
                                                                        <hr>
                                                                        <span class="description" style="font-size: 12px; color: #17a2b8;">Awaiting action</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            @if(!$loop->last)
                                                                <div class="connector">
                                                                    @if($step['status'] == 'approved' || $step['status'] == 'completed')
                                                                        → 
                                                                    @else
                                                                        → 
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        
                                                        @if(empty($displaySteps))
                                                            <div class="step">
                                                                <div class="icon">📄</div>
                                                                <div class="pending">
                                                                    <p>No approval sequence defined</p>
                                                                    <span class="description">Document is in draft or pending submission</span>
                                                                </div>
                                                            </div>
                                                        @endif
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
                                        
                                        // Toggle visibility
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
  <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
  </script>
@endsection