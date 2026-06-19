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

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .col-summary {
        min-width: 0;
    }
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
        }
    }

    .status-filter-bar {
        margin-bottom: 20px;
    }

    .status-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .status-filter-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 15px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        min-width: 80px;
        position: relative;
        overflow: hidden;
    }

    .status-filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
    }

    /* Color classes for each status */
    .status-filter-btn.new {
        border-left-color: #6c757d;
    }
    .status-filter-btn.new.active {
        background-color: #6c757d;
        color: white;
        border-color: #6c757d;
    }

    .status-filter-btn.approved {
        border-left-color: #28a745;
    }
    .status-filter-btn.approved.active {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .status-filter-btn.approved-principle {
        border-left-color: #007bff;
    }
    .status-filter-btn.approved-principle.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .status-filter-btn.discussion {
        border-left-color: #17a2b8;
    }
    .status-filter-btn.discussion.active {
        background-color: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }

    .status-filter-btn.forwarded {
        border-left-color: #6f42c1;
    }
    .status-filter-btn.forwarded.active {
        background-color: #6f42c1;
        color: white;
        border-color: #6f42c1;
    }

    .status-filter-btn.commented {
        border-left-color: #fd7e14;
    }
    .status-filter-btn.commented.active {
        background-color: #fd7e14;
        color: white;
        border-color: #fd7e14;
    }

    .status-filter-btn.no-action {
        border-left-color: #20c997;
    }
    .status-filter-btn.no-action.active {
        background-color: #20c997;
        color: white;
        border-color: #20c997;
    }

    .status-filter-btn.hold {
        border-left-color: #343a40;
    }
    .status-filter-btn.hold.active {
        background-color: #343a40;
        color: white;
        border-color: #343a40;
    }

    .status-filter-btn.rejected {
        border-left-color: #dc3545;
    }
    .status-filter-btn.rejected.active {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .status-filter-btn.pending {
        border-left-color: #ffc107;
    }
    .status-filter-btn.pending.active {
        background-color: #ffc107;
        color: #212529;
        border-color: #ffc107;
    }

    .status-filter-btn.total {
        background: #2d3748;
        color: white;
        border-color: #2d3748;
        border-left-color: #1a202c;
    }

    /* Active state styles */
    .status-filter-btn.active {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: 600;
    }

    .filter-count {
        font-size: 1.125rem;
        font-weight: bold;
        margin-bottom: 2px;
    }

    .filter-label {
        font-size: 0.75rem;
        opacity: 0.9;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .status-filters {
            gap: 6px;
        }
        
        .status-filter-btn {
            padding: 8px 12px;
            min-width: 70px;
        }
        
        .filter-count {
            font-size: 1rem;
        }
        
        .filter-label {
            font-size: 0.7rem;
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
            <h1>Discussion Documents</h1>
          </div>
          @if(Auth::user()->department == 'VC')
            <div class="status-filter-bar">
                <div class="status-filters">
                    <a href="{{ route('summary_documents') }}" class="status-filter-btn new {{ request()->routeIs('summary_documents') ? 'active' : '' }}">
                        <span class="filter-count">{{ $yetToApproveCount }}</span>
                        <span class="filter-label">New</span>
                    </a>
                    
                    <a href="{{ route('summary_approved') }}" class="status-filter-btn approved {{ request()->routeIs('summary_approved') ? 'active' : '' }}">
                        <span class="filter-count">{{ $approvedCount }}</span>
                        <span class="filter-label">Approved</span>
                    </a>
                    
                    <a href="{{ route('summary_approved_in_principle') }}" class="status-filter-btn approved-principle {{ request()->routeIs('summary_approved_in_principle') ? 'active' : '' }}">
                        <span class="filter-count">{{ $approvedInPrincipleCount }}</span>
                        <span class="filter-label">In Principle</span>
                    </a>
                    
                    <a href="{{ route('summary_discussion') }}" class="status-filter-btn discussion {{ request()->routeIs('summary_discussion') ? 'active' : '' }}">
                        <span class="filter-count">{{ $discussionCount }}</span>
                        <span class="filter-label">Discussion</span>
                    </a>
                    
                    <a href="{{ route('summary_forwarded') }}" class="status-filter-btn forwarded {{ request()->routeIs('summary_forwarded') ? 'active' : '' }}">
                        <span class="filter-count">{{ $forwardedCount }}</span>
                        <span class="filter-label">Forwarded</span>
                    </a>
                    
                    <a href="{{ route('summary_commented') }}" class="status-filter-btn commented {{ request()->routeIs('summary_commented') ? 'active' : '' }}">
                        <span class="filter-count">{{ $commentedCount }}</span>
                        <span class="filter-label">Commented</span>
                    </a>
                    
                    <a href="{{ route('summary_nat') }}" class="status-filter-btn no-action {{ request()->routeIs('summary_nat') ? 'active' : '' }}">
                        <span class="filter-count">{{ $noActionCount }}</span>
                        <span class="filter-label">No Action</span>
                    </a>
                    
                    <a href="{{ route('summary_hold') }}" class="status-filter-btn hold {{ request()->routeIs('summary_hold') ? 'active' : '' }}">
                        <span class="filter-count">{{ $holdCount }}</span>
                        <span class="filter-label">Hold</span>
                    </a>
                    
                    <a href="{{ route('summary_rejected') }}" class="status-filter-btn rejected {{ request()->routeIs('summary_rejected') ? 'active' : '' }}">
                        <span class="filter-count">{{ $rejectCount }}</span>
                        <span class="filter-label">Rejected</span>
                    </a>
                    
                    <a href="{{ route('summary_pending') }}" class="status-filter-btn pending {{ request()->routeIs('summary_pending') ? 'active' : '' }}">
                        <span class="filter-count">{{ $pendingCount }}</span>
                        <span class="filter-label">Pending</span>
                    </a>
                    
                    <div class="status-filter-btn total">
                        <span class="filter-count">{{ $totalDocumentsCount }}</span>
                        <span class="filter-label">Total</span>
                    </div>
                </div>
            </div>
          @endif
          <div class="section-body">
            <h2 class="section-title">Discussion</h2>
            <p class="section-lead">
              Following Documents were marked as "Discussion" by you.
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1" style="text-align: center;">
                                    
                                    <thead>
                                        <tr>
                                            <th style="width:5%">
                                                S.No
                                            </th>
                                            <th style="width:15%">
                                                DOC ID
                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'doc_id', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'doc_id' ? 'desc' : 'asc']) }}">
                                                    {!! request('sort_by') == 'doc_id' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            <th style="width:20%">
                                                Title
                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'title' ? 'desc' : 'asc']) }}">
                                                    {!! request('sort_by') == 'title' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            <th style="width:10%">
                                                Priority
                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'priority', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'priority' ? 'desc' : 'asc']) }}">
                                                    {!! request('sort_by') == 'priority' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            <th style="width:10%">
                                                From
                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'from', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'from' ? 'desc' : 'asc']) }}">
                                                    {!! request('sort_by') == 'from' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            @if(Auth::user()->department == 'Students Welfare')
                                            <th style="width:20%">
                                                Payment Status
                                                <a href="{{ request()->fullUrlWithQuery([
                                                    'sort_by' => 'payment_status',
                                                    'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'payment_status' ? 'desc' : 'asc'
                                                ]) }}">
                                                    {!! request('sort_by') == 'payment_status' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            @else
                                            <th style="width:20%">
                                                Status
                                                <a href="{{ request()->fullUrlWithQuery([
                                                    'sort_by' => 'payment_status',
                                                    'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'payment_status' ? 'desc' : 'asc'
                                                ]) }}">
                                                    {!! request('sort_by') == 'payment_status' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            @endif
                                            <th style="width:15%">Created at</th>
                                            <th style="width:10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                                        @endphp
                                        @foreach ($documents as $doc)

                                            @if($doc)
                                                <tr data-id="{{ $doc->id }}">
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $doc->doc_id }}</td>
                                                    <td><a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">{{ $doc->title }}</a></td>
                                                    <td>{{ $doc->priority }}</td>
                                                    <td>{{ $doc->from }}</td>
                                                    <td>{{ $doc->approval_status }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
                                                    <td>
                                                        <a href="javascript:void(0);" class="btn btn-primary btn-action mr-1 toggle-accordion" data-toggle="tooltip" title="View" data-target="#accordion-{{ $doc->id }}">
                                                            <i class="fas fa-solid fa-eye"></i>
                                                        </a>                                               
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
                                                            <p><b>Justification :</b> {!! strip_tags($doc->justification, '<span><b><i><u>') !!}</p>
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
                                                                            $role = \App\Models\User::FindorFail($log->by)->department;
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
                                                                        
                                                                        @if (!$loop->last) <!-- Check if it's not the last iteration -->
                                                                            <div class="connector">&#x2190;</div>
                                                                        @endif
                                                                
                                                                    @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                {!! $documents->links('frontend.pagination.custom') !!}
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
                // The page was restored from the bfcache, so reload it
                window.location.reload();
            }
        });
  </script>
@endsection