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
            <h1>Forwarded Tickets</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Forwarded Tickets</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Forwarded Tickets</h2>
            <p class="section-lead">
              Forwarded tickets that require multiple deparment inputs 
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Forwarded Tickets</h4>
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($forwards as $forward)

                                    @php
                                        $ticket = App\Models\Ticket::find($forward->ticket_id);   
                                    @endphp
                                    
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
                                            <a class="font-weight-600">{{ $ticket->ticket_to }}</a>
                                        </td>
                                        <td>
                                            <a class="font-weight-600">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</a>
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
                                            <a href="{{ url('superadmin/view/ticket/'.$ticket->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="View"><i class="fas fa-solid fa-eye"></i></a>
                                            <div class="btn-group">
                                              <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <i class=" fas fa-solid fa-bell"></i>
                                              </button>
                                              
                                              @include('frontend.ping.ping-alert')

                                            </div>
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