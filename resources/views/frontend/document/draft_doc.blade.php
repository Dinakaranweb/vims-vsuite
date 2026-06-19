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
    
    .approval-status-icon.draft {
        color: #6c757d;
    }
    
    .approval-status-icon.retracted {
        color: #dc3545;
    }
    
    .approval-status-label {
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
    
    .approval-status-label.draft {
        color: #6c757d;
    }
    
    .approval-status-label.retracted {
        color: #dc3545;
    }
    
    .draft-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #6c757d;
        color: #fff;
    }
    
    .retracted-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #dc3545;
        color: #fff;
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
            <h1>Draft/Retracted Documents</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Draft/Retracted Documents</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Draft/Retracted Documents</h2>
            <p class="section-lead">
              Documents saved as draft or retracted from approval process
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
                                <form method="GET" action="{{ route('sa_search_documents') }}" class="form-inline" style="margin-top: 10px; flex-wrap: wrap;">
                                    <input type="hidden" name="type" value="received">
                                    <div class="form-group mr-2 mb-2">
                                        <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
                                    </div>
                                    <div class="form-group mr-2 mb-2">
                                        <input type="text" name="doc_id" class="form-control" placeholder="Search by Document ID" value="{{ request('doc_id') }}">
                                    </div>
                                    <div class="form-group mr-2 mb-2">
                                        <select name="section" class="form-control">
                                            <option value="">Search by Section</option>
                                            @foreach($departments as $section)
                                                <option value="{{ $section->dept_label }}" {{ request('section') == $section->dept_label ? 'selected' : '' }}>{{ $section->dept_label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-2 mb-2">
                                        <select name="doc_type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="Draft" {{ request('doc_type') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="Retracted" {{ request('doc_type') == 'Retracted' ? 'selected' : '' }}>Retracted</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2 mb-2">
                                        <input type="date" name="date_from" class="form-control" placeholder="Date From" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="form-group mr-2 mb-2">
                                        <input type="date" name="date_to" class="form-control" placeholder="Date To" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="form-group mr-2 mb-2">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                    <div class="form-group mb-2">
                                        <a href="{{ route('draft_documents') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Latest Drafts</h4>
                            <a href="{{ route('create_document') }}" class="btn btn-primary" style="margin-left:auto;">
                                <i class="fas fa-plus"></i> Create New Document
                            </a>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1" style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th style="width:5%">S.No</th>
                                        <th style="width:12%">DOC ID</th>
                                        <th style="width:18%">Title</th>
                                        <th style="width:8%">Priority</th>
                                        @if(Auth::user()->role == 'SuperAdmin')
                                            <th style="width:10%">From</th>
                                        @else
                                            <th style="width:10%">Status</th>
                                        @endif
                                        <th style="width:15%">Document Type</th>
                                        <th style="width:12%">Created</th>
                                        <th style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($docs as $doc)
                                    
                                        @php
                                            // Get approval sequence for retracted documents (if any approvals happened)
                                            $approvalSequence = [];
                                            $completedApprovers = [];
                                            $hasApprovals = false;
                                            
                                            if ($doc->status == 'Retracted' && !empty($doc->approval_sequence)) {
                                                try {
                                                    $approvalSequence = json_decode($doc->approval_sequence, true);
                                                    if (!is_array($approvalSequence)) {
                                                        $approvalSequence = [];
                                                    }
                                                } catch (\Exception $e) {
                                                    $approvalSequence = [];
                                                }
                                                
                                                // Get completed approvals from approval_log
                                                $completedLogs = DB::table('approval_log')
                                                    ->where('doc_id', $doc->id)
                                                    ->where(function($query) {
                                                        $query->where('status', 'like', 'Approved by%')
                                                              ->orWhere('status', 'like', 'Noted by%');
                                                    })
                                                    ->pluck('status')
                                                    ->toArray();
                                                
                                                foreach ($completedLogs as $log) {
                                                    if (preg_match('/Approved by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                                                        $completedApprovers[] = trim($matches[1]);
                                                    } 
                                                    elseif (preg_match('/Noted by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                                                        $completedApprovers[] = trim($matches[1]);
                                                    }
                                                }
                                                
                                                $hasApprovals = count($completedApprovers) > 0;
                                            }
                                            
                                            $isDraft = $doc->status == 'Draft';
                                            $isRetracted = $doc->status == 'Retracted';
                                        @endphp
                                        
                                        <tr data-id="{{ $doc->id }}">
                                            <td>{{ $i++ }}</td>
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
                                                <td>{{ $doc->status }}</td>
                                            @endif                                        
                                            <td>
                                                @if($isDraft)
                                                    <span class="draft-badge">
                                                        <i class="fas fa-pen"></i> Draft
                                                    </span>
                                                @elseif($isRetracted)
                                                    <span class="retracted-badge">
                                                        <i class="fas fa-undo-alt"></i> Retracted
                                                    </span>
                                                @else
                                                    <span class="draft-badge">{{ $doc->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-primary btn-action mr-1 toggle-accordion" data-toggle="tooltip" title="View Details" data-target="#accordion-{{ $doc->id }}">
                                                    <i class="fas fa-solid fa-eye"></i>
                                                </a>
                                                @if(($doc->status == 'Draft' || $doc->status == 'Retracted') && $doc->by == Auth::id())
                                                <a href="{{ route('edit_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-solid fa-pen"></i>
                                                </a>
                                                <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $doc->id }})"><i class="fas fa-trash"></i></a>
                                                @endif                                                
                                            </td>
                                        </tr>
                                        
                                        <!-- Accordion row with full details -->
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
                                                            <p><b>Status :</b> {{ $doc->status }}</p>
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
                                                    @if($isRetracted && !empty($approvalSequence) && $hasApprovals)
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <h6>Approval Progress Before Retraction</h6>
                                                            <div class="approval-flowchart-container">
                                                                <div class="approval-flowchart">
                                                                    @foreach($approvalSequence as $index => $step)
                                                                        @php
                                                                            $isCompleted = in_array($step, $completedApprovers);
                                                                            $stepClass = $isCompleted ? 'path-step-completed' : 'path-step-pending';
                                                                            $icon = $isCompleted ? '✓' : '○';
                                                                            $statusText = $isCompleted ? 'Completed' : 'Not Reached';
                                                                        @endphp
                                                                        <div class="step">
                                                                            <div class="icon">&#x1F4A1;</div>
                                                                            <div>
                                                                                <p>{{ $step }}</p>
                                                                                <hr>
                                                                                <p class="{{ $isCompleted ? 'text-success' : 'text-muted' }}">
                                                                                    {{ $icon }} {{ $statusText }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        @if($index < count($approvalSequence) - 1)
                                                                            <div class="connector">&#x2190;</div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="alert alert-warning mt-3">
                                                                <i class="fas fa-exclamation-triangle"></i> 
                                                                This document was retracted from the approval process.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @elseif($isDraft)
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle"></i> 
                                                                This is a draft document. Please complete and submit for approval.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <script>
                                        function handleConfirmYes(docId) {
                                            if(confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                                                window.location.href = "{{ url('/delete/document') }}/" + docId;
                                            }
                                        }
                                    </script>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 text-muted">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                <span style="color: #6c757d;">📄 Draft</span> - Not yet submitted &nbsp;&nbsp;
                                <span style="color: #dc3545;">↩️ Retracted</span> - Withdrawn from approval process
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