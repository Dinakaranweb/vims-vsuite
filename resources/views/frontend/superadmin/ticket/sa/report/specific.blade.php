@extends('frontend.frontend_master')

@section('content')

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @include('frontend.superadmin.body.header')
      
            @include('frontend.superadmin.body.sidebar')

        <!-- Main Content -->
        <div class="main-content">
          <section class="section">
            <div class="section-header">
              <h1>Specific Reports</h1>
              <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
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
                      <form method="POST" action="{{ route('super_admin_specific_report') }}" class="needs-validation" novalidate="">
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
                    <div class="card-header bg-primary text-white">
                      <h4 class="mb-0">Report From {{ \Carbon\Carbon::parse($from)->format('d-m-Y') }} To {{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}</h4>
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
                </div>
              </div>
              @if($reportData != Null)
              <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Tickets</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">

                          <table class="table table-striped" id="table-3" style="text-align: center;">
                            </thead>
                            <tbody>
                                <tr>
                                    <th></th>
                                    <th colspan="{{ count($departments) + 2 }}">
                                        Ticket Received (R - Received, <span class="text-danger"> P - Pending</span>)
                                    </th>
                                </tr>
                                <tr>
                                    <th rowspan="{{ count($departments) + 3 }}" style="writing-mode: vertical-lr; text-orientation: mixed; transform: rotate(180deg);">
                                        Ticket Raised (R - Raised, <span class="text-danger"> P - Pending</span>)
                                    </th>
                                </tr>
                                <tr>
                                    <th style="min-width: 32px;"></th>
                                    @foreach ($departments as $department)
                                        <th style="min-width: 32px;">{{ $department->dept_label }}</th>
                                    @endforeach
                                    <th style="min-width: 32px;">Total Raised</th>
                                </tr>
                                @foreach ($departments as $fromDepartment)
                                    <tr>
                                        <th>{{ $fromDepartment->dept_label }}</th>
                                        @php
                                            $rowRTotal = 0; // Initialize row R-total
                                            $rowPTotal = 0; // Initialize row P-total
                                        @endphp
                                        @foreach ($departments as $toDepartment)
                                            @php
                                                $found = false;
                                                $count = 0;
                                                $pending = 0;
                                                foreach ($reportData as $data) {
                                                    if ($data['name'] == $fromDepartment->dept_label && $data['to'] == $toDepartment->dept_label) {
                                                        $count = $data['count'];
                                                        $pending = $data['pending'];
                                                        $found = true;
                                                        break;
                                                    }
                                                }
                                                if ($found) {
                                                    echo "<td>R - $count <br/><span class='text-danger'>P - $pending</span></td>";
                                                    $rowRTotal += $count; // Add to row R-total
                                                    $rowPTotal += $pending; // Add to row P-total
                                                } else {
                                                    echo "<td>-</td>";
                                                }
                                            @endphp
                                        @endforeach
                                        <td>R - {{ $rowRTotal }} <br/><span class='text-danger'>P - {{ $rowPTotal }}</span></td> <!-- Display row total -->
                                    </tr>
                                @endforeach
                                <tr>
                                    <th>Total Received</th> <!-- Grand total row header -->
                                    @php
                                        $grandRTotal = 0; // Initialize grand R-total
                                        $grandPTotal = 0; // Initialize grand P-total
                                    @endphp
                                    @foreach ($departments as $department)
                                        @php
                                            $colRTotal = 0; // Initialize column R-total
                                            $colPTotal = 0; // Initialize column P-total
                                            foreach ($reportData as $data) {
                                                if ($data['to'] == $department->dept_label) {
                                                    $colRTotal += $data['count'];
                                                    $colPTotal += $data['pending'];
                                                }
                                            }
                                            $grandRTotal += $colRTotal; // Add to grand R-total
                                            $grandPTotal += $colPTotal; // Add to grand P-total
                                        @endphp
                                        <td>R - {{ $colRTotal }} <br/><span class='text-danger'>P - {{ $colPTotal }}</span></td> <!-- Display column total -->
                                    @endforeach
                                    <td>R - {{ $grandRTotal }} <br/><span class='text-danger'>P - {{ $grandPTotal }}</span></td> <!-- Display grand total -->
                                </tr>
                            </tbody>
                          </table>
                        </div>
                        </div>
                    </div>
                </div>
              </div>
              @endif

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
                                @foreach ($tickets as $ticket)   
                                    <tr data-id="{{ $ticket->id }}">
                                        <td>
                                            {{ $i++ }}
                                        </td>
                                        <td>
                                            <a href="{{ url('superadmin/view/ticket/'.$ticket->id) }}" style="color: #1e1e1e">
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
            
          </section>
        <!-- <a href="{{ url('/chart-template') }}"><button class="btn btn-primary">Download</button></a> -->

         <form id="chart-form" method="POST" action="{{ route('super_admin_generate_specific_report') }}">
            @csrf
            <input type="hidden" id="status-chart-img" name="status_chart_img">
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

                    var statusChartImg = myChart2.toBase64Image();
                    
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