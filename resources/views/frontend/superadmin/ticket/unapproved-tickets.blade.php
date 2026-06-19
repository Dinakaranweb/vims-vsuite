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
            <h1>Un-Approved Tickets</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('super_admin_tickets_summary') }}">Unapproved Tickets</a></div>
              <div class="breadcrumb-item">Unapproved Tickets</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Unapproved Tickets</h2>
            <p class="section-lead">
              Tickets require approval to be displayed in the destined dashboard for resolution
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
                                    <th>By</th>
                                    <th>To</th>
                                    <th>Created</th>
                                    <th>Due</th>
                                    <th>Priority</th>
                                    <th>Action</th>
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
                                            {{ \App\Models\User::find($ticket->ticket_by)->name }}
                                        </td>
                                        
                                        <td>
                                            <a class="font-weight-600">{{ $ticket->ticket_to }}</a>
                                        </td>
                                        <td>
                                            <a class="font-weight-600">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</a>
                                        </td>
                                        <td>
                                            <a class="font-weight-600">{{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</a>
                                        </td>
                                        
                                        @if ($ticket->priority  == "Low")
                                            <td><div class="badge badge-info">{{ $ticket->priority }}</div></td>
                                        @elseif ($ticket->priority == "Medium")
                                            <td><div class="badge badge-warning">{{ $ticket->priority }}</div></td>
                                        @elseif ($ticket->priority == "High")
                                            <td><div class="badge badge-danger">{{ $ticket->priority }}</div></td>
                                        @endif
                                        
                                        <td>
                                            <a href="{{ url('/approve/ticket/'.$ticket->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="Approve"><i class="fas fa-exclamation-triangle"></i></a>
                                            <a href="{{ url('/edit/ticket/'.$ticket->id) }}" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit"><i class="far fa-edit"></i></a>
                                            <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $ticket->id }})"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                <script>
                                    function handleConfirmYes(ticketId) {
                                        // Redirect to the specified route with the correct ticket ID
                                        window.location.href = "{{ url('/delete/ticket') }}/" + ticketId;
                                    }
                                </script>
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