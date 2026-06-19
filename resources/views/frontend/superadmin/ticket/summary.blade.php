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
                <h1>Ticket Dashboard</h1>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('super_admin_recieved_open_tickets') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-user"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Open Tickets</h4>
                            </div>
                            <div class="card-body">
                                {{ $assignedTickets }}
                            </div>
                        </div>
                    </div>
                </a>
                </div>
                  
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href = "{{ route('super_admin_recieved_tickets_in_progress') }}">
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
                    <a href = "{{ route('super_admin_recieved_tickets_on_hold') }}">
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
                    <a href = "{{ route('super_admin_recieved_completed_tickets') }}">
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
                    <a href = "{{ route('super_admin_recieved_closed_tickets') }}">
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
                    <a href = "{{ route('super_admin_recieved_total_tickets') }}">
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
                                    <th>Assigned</th>
                                    <th>Created</th>
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
                                        @php
                                            $user = App\Models\User::find($ticket->assigned_to);
                                        @endphp
                                        @if ($ticket->assigned_to == Null)
                                            <td>
                                                <a class="font-weight-600">Not yet assigned</a>
                                            </td>
                                        @elseif($ticket->assigned_to == Auth::id())

                                            <td>
                                                <a class="font-weight-600">Self</a>
                                            </td>

                                        @else

                                            <td>
                                                <a class="font-weight-600">{{ $user->name }}</a>
                                            </td>
                                        @endif

                                        <td>
                                            {{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}
                                        </td>

                                        <td>
                                            {{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}
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
                                            <a href="{{ url('superadmin/view/ticket/'.$ticket->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="View"><i class="fas fa-solid fa-eye"></i></a>
                                            <div class="btn-group">
                                              <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <i class=" fas fa-solid fa-bell"></i>
                                              </button>
                                              
                                              @include('frontend.ping.ping-alert')

                                            </div>
                                            <!-- <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="alert('Deleted')"><i class="fas fa-trash"></i></a> -->
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
@endsection