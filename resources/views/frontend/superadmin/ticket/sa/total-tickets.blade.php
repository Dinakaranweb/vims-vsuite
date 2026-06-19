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
            <h1>Total Tickets</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('super_admin_tickets_summary') }}">Tickets Summary</a></div>
              <div class="breadcrumb-item">Total Tickets</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Total Tickets</h2>
            <p class="section-lead">
              Make sure the High Priority tasks are handled immediately.
            </p>

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
                                    <th>To</th>
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
                                        <td>
                                            {{ $ticket->ticket_to }}
                                        </td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}
                                        </td>
                                        @if($ticket->status == "Closed")
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
                                        
                                        <td>
                                            <a href="{{ url('superadmin/view/ticket/'.$ticket->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="View"><i class="fas fa-solid fa-eye"></i></a>
                                            <div class="btn-group">
                                              <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <i class=" fas fa-solid fa-bell"></i>
                                              </button>
                                              <div class="dropdown-menu">
                                                <div class="dropdown-title">Ping!</div>
                                                @if($ticket->ticket_from != Auth::user()->department)
                                                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_from]) }}">{{ $ticket->ticket_from }} - Head</a>
                                                @endif
                                                @if($ticket->ticket_to != Auth::user()->department)
                                                <a class="dropdown-item" href="{{ route('ping-alert' ,['task_type' => 'ticket', 'task_id' => $ticket->id, 'ping_to' => $ticket->ticket_to]) }}">{{ $ticket->ticket_to }} - Head</a>
                                                @endif
                                              </div>
                                            </div>
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
          </div>
        </section>
      </div>
        @include('frontend.body.footer')
        </div>
  </div>
@endsection