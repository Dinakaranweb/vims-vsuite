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
              <h1>Reports</h1>
              <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Report</div>
              </div>
            </div>

            <div class="section-body">
              <h2 class="section-title">Report</h2>
              <p class="section-lead">Select the duration below for specific reports</p>

              <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                  <div class="card">
                    <div class="card-body">
                      <form method="POST" action="{{ route('specific_report') }}" class="needs-validation" novalidate="">
                      @csrf  
                        <div class="col-lg-5 col-md-5 form-group" style="display: inline-block;">
                          <label for="from">From</label>
                          <input id="from" type="text" name="from" class="form-control datepicker">
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

              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Total Report</h4>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="card border-0">
                              <div class="card-body">
                                <h5 class="card-title">Ticket Summary</h5>
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                      </tr>
                                    </thead>
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
                                        <td>Closed Tickets</td>
                                        <td>{{ $closed_tickets }}</td>
                                      </tr>
                                      <tr>
                                        <td>Raised Tickets</td>
                                        <td>{{ $tickets_raised }}</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="card border-0">
                              <div class="card-body">
                                <h5 class="card-title">Tickets by Department</h5>
                                <ul class="list-group">
                                  @foreach($departmentData as $department)
                                  @if ($department['count'] != 0)
                                  <li class="list-group-item">{{ $department['name'] }} - {{ $department['count'] }}</li>
                                  @endif
                                  @endforeach
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

 
              <div class="row">
                <div class="col-12 col-md-6 col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <h4>Tickets by Department</h4>
                    </div>
                    <div class="card-body">
                      <canvas id="myChart3"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6">
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
              <script>
                  var departmentData = <?php echo json_encode($departmentData); ?>;
                  var open_tickets = <?php echo json_encode($open_tickets); ?>;
                  var closed_tickets = <?php echo json_encode($closed_tickets); ?>;
                  var tickets_on_hold = <?php echo json_encode($tickets_on_hold); ?>;
                  var tickets_in_progress = <?php echo json_encode($tickets_in_progress); ?>;
              </script>
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
                                    <th>Ticket ID</th>
                                    <th>Title</th>
                                    <th>From</th>
                                    <th>Assigned to</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody> 

                                @foreach ($tickets as $ticket)   
                                    <tr data-id="{{ $ticket->id }}">
                                        <td>
                                            {{ $ticket->ticket_id }}
                                        </td>
                                        <td>
                                            {{ $ticket->title }}
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
                                        @elseif($ticket->assigned_to == Auth::id())

                                            <td>
                                                <a class="font-weight-600">$user->name</a>
                                            </td>

                                        @else

                                            <td>
                                                <a href="#" class="font-weight-600"><img src="{{ asset('assets/img/avatar/avatar-1.png') }}" alt="avatar" width="30" class="rounded-circle mr-1">{{ $user->name }}</a>
                                            </td>
                                        @endif

                                        <td>
                                            {{ $ticket->due_date }}
                                        </td>
                                        
                                        @if ($ticket->status  == "Open")
                                            <td><div class="badge badge-info">{{ $ticket->status }}</div></td>
                                        @elseif ($ticket->status == "In Progress")
                                            <td><div class="badge badge-warning">{{ $ticket->status }}</div></td>
                                        @elseif ($ticket->status == "Hold")
                                            <td><div class="badge badge-danger">{{ $ticket->status }}</div></td>
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

         <form id="chart-form" method="POST" action="{{ route('generate_report') }}">
            @csrf
            <input type="hidden" id="departments-chart-img" name="departments_chart_img">
            <input type="hidden" id="status-chart-img" name="status_chart_img">
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

                    document.getElementById('departments-chart-img').value = departmentsChartImg;
                    document.getElementById('status-chart-img').value = statusChartImg;

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