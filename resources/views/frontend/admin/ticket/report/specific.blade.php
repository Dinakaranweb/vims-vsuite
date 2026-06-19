@extends('frontend.frontend_master')

@section('content')

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @include('frontend.admin.body.header')
      
            @include('frontend.admin.body.sidebar')

        <!-- Main Content -->
        <div class="main-content">
          <section class="section">
            <div class="section-header">
              <h1>Specific Reports</h1>
              <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Specific Report</div>
              </div>
            </div>

            <div class="section-body">
              <h2 class="section-title">Specific Report</h2>
              <p class="section-lead">Select the duration below for specific reports</p>

              <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                  <div class="card">
                    <div class="card-body">
                      <form method="POST" action="{{ route('specific_report') }}" class="needs-validation" novalidate="">
                      @csrf  
                        <div class="col-lg-5 col-md-5 form-group" style="display: inline-block;">
                          <label for="from">From</label>
                          <input id="from" type="text" name="from" class="form-control datepicker" value="{{ $from }}">
                        </div>
                        <div class="col-lg-5 col-md-5 form-group" style="display: inline-block;">
                          <label for="to">To</label>
                          <input id="to" type="text" name="to" class="form-control datepicker">
                        </div>
                        <div class="col-lg-2 col-md-2 form-group row" style="display: inline-block; align-content: right;">
                            <button class="btn btn-primary">Create</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                      <div class="card-header bg-primary text-white" style="border-bottom-color: #2a6d98; display: flex; justify-content: center;">
                        <h5 class="mb-0">Ticket Received - From {{ \Carbon\Carbon::parse($from)->format('d-m-Y') }} To {{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}</h5>
                      </div>
                    </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-header">
                          <h4>Tickets Received</h4>
                        </div>
                        <div class="card-body">
                          <canvas id="myChart3"></canvas>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-body">
                          <ul class="list-group">
                            @foreach($departmentData as $department)
                              @if ($department['count'] != 0)
                                <li class="list-group-item" style="font-weight: 600">
                                    {{ $department['name'] }} - {{ $department['count'] }}
                                    @if($department['pending'] > 0)
                                        <div class="bullet"></div>
                                        <span class="text-danger font-weight-600">
                                            Pending - {{ $department['pending'] }}
                                        </span>
                                    @endif
                                </li>
                              @endif
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
 
            <div class="row">
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="table table-sm">
                        <table class="table table-bordered">
                          <tbody>
                            <tr>
                              <td>Total Tickets</td>
                              <td>{{ $total_tickets }}</td>
                            </tr>
                            <tr>
                              <td>Open Tickets</td>
                              <td>{{ $open_tickets }}</td>
                            </tr>
                            <tr>
                              <td>Tickets on Hold</td>
                              <td>{{ $tickets_on_hold }}</td>
                            </tr>
                            <tr>
                              <td>Tickets in Progress</td>
                              <td>{{ $tickets_in_progress }}</td>
                            </tr>
                            <tr>
                              <td>Completed Tickets</td>
                              <td>{{ $completed_tickets }}</td>
                            </tr>
                            <tr>
                              <td>Closed Tickets</td>
                              <td>{{ $closed_tickets }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <h4>Tickets by Status</h4>
                    </div>
                    <div class="card-body">
                      <canvas id="myChart4"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            
              <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Tickets</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Title</th>
                                    <th>From</th>
                                    <th>Assigned</th>
                                    <th>Created</th>
                                    <th>Due</th>
                                    <th>Status</th>
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
                                            <a href="{{ url('admin/view/ticket/'.$ticket->id) }}" style="color: #1e1e1e">
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
                                        @php
                                            $user = App\Models\User::find($ticket->assigned_to);
                                        @endphp
                                        @if ($ticket->assigned_to == Null)
                                            <td>
                                                <a class="font-weight-600">Not yet assigned</a>
                                            </td>

                                        @else

                                            <td>
                                                <a class="font-weight-600">{{ $user->name }}</a>
                                            </td>
                                        @endif

                                        <td>
                                            {{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}
                                        </td>
                                        @if($ticket->status == 'Closed')
                                        <td>
                                            -
                                        </td>
                                        @else
                                        <td>
                                            {{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}
                                        </td>
                                        @endif
                                        
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
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

              <div class="row" style="margin: 0 auto">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                      <div class="card-header bg-primary text-white" style="border-bottom-color: #2a6d98; display: flex; justify-content: center;">
                        <h5 class="mb-0">Ticket Raised</h5>

                      </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <h4>Tickets Raised</h4>
                    </div>
                    <div class="card-body">
                      <canvas id="myChart9"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6">
                  <div class="card">
                    <div class="card-body">
                        <ul class="list-group">
                          @foreach($ticketData as $department)
                          @if ($department['count'] != 0)
                            <li class="list-group-item" style="font-weight: 600">
                                {{ $department['name'] }} - {{ $department['count'] }}
                                @if($department['pending'] > 0)
                                    <div class="bullet"></div>
                                    <span class="text-danger font-weight-600">
                                        Pending - {{ $department['pending'] }}
                                    </span>
                                @endif
                            </li>
                          @endif
                          @endforeach
                        </ul>
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
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Tickets Raised</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Title</th>
                                    <th>By</th>
                                    <th>To</th>
                                    <th>Created</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($raised_tickets as $ticket)   
                                    <tr data-id="{{ $ticket->id }}">
                                        <td>
                                            {{ $i++ }}
                                        </td>
                                        <td>
                                            {{ $ticket->title }}
                                            <div class="table-links">
                                            in <a href="#">{{ $ticket->ticket_to }}</a>
                                            <div class="bullet"></div>
                                            <a href="#" class="log-link">Log</a>
                                            </div>
                                        </td>
                                        <td>
                                            <a class="font-weight-600">{{ App\Models\User::find($ticket->ticket_by)->name }}</a>
                                        </td>
                                        <td>
                                            {{ $ticket->ticket_to }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}
                                        </td>
                                        @if($ticket->status == 'Closed')
                                        <td>
                                            -
                                        </td>
                                        @else
                                        <td>
                                            {{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}
                                        </td>
                                        @endif
                                        
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
        <!-- <a href="{{ url('/chart-template') }}"><button class="btn btn-primary">Download</button></a> -->

         <form id="chart-form" method="POST" action="{{ route('generate_specific_report') }}">
            @csrf
            <input type="hidden" id="departments-chart-img" name="departments_chart_img">
            <input type="hidden" id="status-chart-img" name="status_chart_img">
            <input type="hidden" id="ticket-raised-img" name="ticket_raised_img">
            <input type="hidden" id="from" name="from" value="{{ $from }}">
            <input type="hidden" id="to" name="to" value="{{ $to }}">
            <input type="submit" class="btn btn-primary" value="Download">
        </form>

        </div>



        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                
                // Convert charts to images and submit the form
                function convertChartsToImages() {

                    var departmentsChartImg = myChart.toBase64Image();
                    var statusChartImg = myChart2.toBase64Image();
                    var ticketRaisedImg = myChart3.toBase64Image();

                    document.getElementById('departments-chart-img').value = departmentsChartImg;
                    document.getElementById('status-chart-img').value = statusChartImg;
                    document.getElementById('ticket-raised-img').value = ticketRaisedImg;

                    //document.getElementById('chart-form').submit();
                }

                // Delay conversion to ensure charts are rendered
                setTimeout(convertChartsToImages, 2000);
            });
        </script>

        @include('frontend.body.footer')

        </div>
  </div>
@endsection