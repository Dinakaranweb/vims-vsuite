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

    @if(Auth::user()->department == 'Students Welfare')
        <div id="app">
            <div class="main-wrapper main-wrapper-1">
                
                @include('frontend.staff.body.header')
        
                @include('frontend.staff.body.sidebar')

                <!-- Main Content -->
                <div class="main-content">
                    <section class="section">
                        <div class="section-header">
                            <h1>In Progress Documents</h1>
                        </div>
                        
                        @include('frontend.staff.document.payment_stats')
                        
                        <div class="section-body">
                            <h2 class="section-title">In Progress Documents</h2>
                            <p class="section-lead">
                            Following Documents require action
                            </p>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                                            <h4 class="mb-2 mb-md-0">Latest Documents</h4>
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
                                                            <th style="width:15%">
                                                                Expenditure ID
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'expenditure_id', 'sort_dir' => request('expenditure_id') == 'asc' && request('sort_by') == 'expenditure_id' ? 'desc' : 'asc']) }}">
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
                                                                From
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'from', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'from' ? 'desc' : 'asc']) }}">
                                                                    {!! request('sort_by') == 'from' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                                </a>
                                                            </th>
                                                            <th style="width:15%">Payment Status</th>
                                                            <th style="width:15%">Received at</th>
                                                            <th style="width:10%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $i = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                                                        @endphp
                                                        @foreach ($documents as $finance)

                                                            @php
                                                                $doc = App\Models\DocumentApproval::FindorFail($finance->doc_id);
                                                            @endphp
                                                            <tr data-id="{{ $doc->id }}">
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ $doc->doc_id }}</td>
                                                                <td>{{ $finance->expenditure_id ?? '-' }}</td>
                                                                <td><a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">{{ $doc->title }}</a></td>
                                                                <td>{{ $doc->from }}</td>
                                                                <td>{{ $finance->status }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($finance->created_at)->format('d-m-Y') }}</td>
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
    @else
        <div id="app">
            <div class="main-wrapper main-wrapper-1">
                
                @include('frontend.staff.body.header')
        
                @include('frontend.staff.body.sidebar')

                <!-- Main Content -->
                <div class="main-content">
                    <section class="section">
                    <div class="section-header">
                        <h1>Ticket Dashboard</h1>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <a href="{{ route('staff_open_tickets') }}">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="far fa-user"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Open Tickets</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $open_tickets }}
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <a href="{{ route('staff_tickets_in_progress') }}">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-warning">
                                    <i class="far fa-newspaper"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>In Progress</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $tickets_in_progress }}
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <a href="{{ route('staff_completed_tickets') }}">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-danger">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Hold Tickets</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $tickets_on_hold }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div> 
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <a href="{{ route('staff_completed_tickets') }}">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-dark">
                                    <i class="far fa-file"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Completed Tickets</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $completed_tickets }}
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                            <a href = "{{ route('staff_closed_tickets') }}">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-success">
                                    <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Closed</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $closed_tickets }}
                                    </div>
                                    </div>
                                </div>
                            </a>
                        </div> 
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                            <a href = "{{ route('staff_total_tickets') }}">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-info">
                                    <i class="far fa-file"></i>
                                    </div>
                                    <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Total</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $total_tickets }}
                                    </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="card">
                            <div class="card-header">
                                <h4>Tickets Received</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="myChart4"></canvas>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                        <div class="card card-hero">
                            <div class="card-header">
                            <div class="card-icon">
                                <i class="far fa-solid fa-bell"></i>
                            </div>
                            <h4>{{ $total_pings }}</h4>
                            <div class="card-description">New Pings</div>
                            </div>
                            <div class="card-body p-0">
                            <div class="tickets-list">
                                
                                @foreach($pings as $ping)
                                
                                @php
                                    $pingController = app()->make('App\Http\Controllers\PingController');
                                    $task_details = $pingController->getTaskDetails($ping->task_type, $ping->task_id)
                                @endphp

                                <a href="{{ $task_details['task_url'] }}" class="ticket-item">
                                    <div class="ticket-title">
                                    <h4>{{ $task_details['task_title'] }}</h4>
                                    </div>
                                    <div class="ticket-info">
                                    <div>{{ $task_details['task_from'] }}</div>
                                    <div class="bullet"></div>
                                    <div>{{ $task_details['task_created_at']->diffForHumans() }}</div>
                                    <div class="bullet"></div>
                                    <div class="text-primary"><b>{{ $ping->ping_count }} - Pings</b></div>
                                    </div>
                                </a>
                                @endforeach
                                
                                <a href="{{ route('pings') }}" class="ticket-item ticket-more">
                                View All <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <script>
                        var departmentData = <?php echo json_encode($departmentData); ?>;
                        var ticketData = <?php echo json_encode($ticketData); ?>;
                        var open_tickets = <?php echo json_encode($open_tickets); ?>;
                        var closed_tickets = <?php echo json_encode($closed_tickets); ?>;
                        var completed_tickets = <?php echo json_encode($completed_tickets); ?>;
                        var tickets_on_hold = <?php echo json_encode($tickets_on_hold); ?>;
                        var tickets_in_progress = <?php echo json_encode($tickets_in_progress); ?>;
                    </script>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                <h4>Latest Tickets</h4>
                                </div>
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-1" style="text-align: center;">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Title</th>
                                            <th>From</th>
                                            <th>Due</th>
                                            <th>Status</th>
                                            <th style="width:13%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($tickets as $ticket)   
                                            <tr data-id="{{ $ticket->id }}">
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td>
                                                    <a href="{{ url('staff/view/ticket/'.$ticket->id) }}" style="color: #1e1e1e">
                                                    {{ $ticket->title }}</a>
                                                    <div class="table-links">
                                                    in <a href="#">{{ $ticket->ticket_from }}</a>
                                                    <div class="bullet"></div>
                                                    <a href="#" class="log-link">Log</a>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $ticket->ticket_from }}
                                                </td>
                                                <td>
                                                    <a class="font-weight-600">{{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</a>
                                                </td>
                                                
                                                @if ($ticket->status  == "Open")
                                                    <td><div class="badge badge-info">{{ $ticket->status }}</div></td>
                                                @elseif ($ticket->status == "In Progress")
                                                    <td><div class="badge badge-warning">{{ $ticket->status }}</div></td>
                                                @elseif ($ticket->status == "Hold")
                                                    <td><div class="badge badge-danger">{{ $ticket->status }}</div></td>
                                                @elseif ($ticket->status == "Completed")
                                                    <td><div class="badge badge-dark">{{ $ticket->status }}</div></td>
                                                @else
                                                    <td><div class="badge badge-success">{{ $ticket->status }}</div></td>
                                                @endif
                                                
                                                <td>
                                                    <a href="{{ url('staff/view/ticket/'.$ticket->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="View">Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
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
    @endif
@endsection