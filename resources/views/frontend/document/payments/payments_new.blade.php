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
            <h1>New Documents (Payments)</h1>
          </div>

          @include('frontend.document.payments.payment_stats')

          <div class="section-body">
            
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                            <h4 class="mb-2 mb-md-0">Latest Documents</h4>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('download_report_forwarded_doc', request()->query()) }}" class="btn btn-success mr-2">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                                &nbsp;
                                <a href="{{ route('download.forwarded.doc.excel', request()->query()) }}" class="btn btn-primary">
                                    <i class="fas fa-file-excel"></i> Excel
                                </a>
                            </div>
                        </div>
                        <div class="p-3 border-top">
                            <form method="GET" action="{{ route('search_forwarded_documents') }}" class="form-row align-items-end">
                                <div class="form-group col-md-2">
                                    <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="doc_id" class="form-control" placeholder="Search by Document ID" value="{{ request('doc_id') }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <select name="approval_status" class="form-control">
                                        <option value="">Search by Status</option>
                                        <option value="Approved" {{ request('approval_status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Pending" {{ request('approval_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Rejected" {{ request('approval_status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="form-group col-md-2 d-flex">
                                    <button type="submit" class="btn btn-primary mr-2">Search</button>
                                    <a href="{{ route('forwarded_documents') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
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
                                            <th style="width:10%">
                                                EXP ID
                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'expenditure_id', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'expenditure_id' ? 'desc' : 'asc']) }}">
                                                    {!! request('sort_by') == 'expenditure_id' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
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
                                            <th style="width:20%">
                                                Payment Status
                                                <a href="{{ request()->fullUrlWithQuery([
                                                    'sort_by' => 'payment_status',
                                                    'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'payment_status' ? 'desc' : 'asc'
                                                ]) }}">
                                                    {!! request('sort_by') == 'payment_status' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                </a>
                                            </th>
                                            <th style="width:15%">Forwarded at</th>
                                            <th style="width:10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = ($docs->currentPage() - 1) * $docs->perPage() + 1;
                                        @endphp
                                        @foreach ($docs as $doc_id)

                                            @php
                                                $doc = App\Models\DocumentApproval::FindorFail($doc_id->doc_id);
                                            @endphp
                                            <tr data-id="{{ $doc->id }}">
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $doc->doc_id }}</td>
                                                <td>{{ $doc_id->expenditure_id }}</td>
                                                <td><a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">{{ $doc->title }}</a></td>
                                                <td>{{ $doc->priority }}</td>
                                                <td>{{ $doc->from }}</td>
                                                <td>{{ $doc_id->payment_status }}</td>
                                                <td>{{ \Carbon\Carbon::parse($doc_id->created_at)->format('d-m-Y') }}</td>
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
                                        @endforeach
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
                // The page was restored from the bfcache, so reload it
                window.location.reload();
            }
        });
  </script>
@endsection