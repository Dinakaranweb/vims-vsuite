@extends('frontend.frontend_master')

@section('content')

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @include('frontend.staff.body.header')
      
            @include('frontend.staff.body.sidebar')

        <!-- Main Content -->
        <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Closed Tickets</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('staff_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Closed Tickets</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Closed Tickets</h2>
            <p class="section-lead">
              Thank you for completing the ticket.
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
                                    <th>Assigned</th>
                                    <th>Rating</th>
                                    <th>Closed by</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $j = 1;
                                @endphp
                                @foreach ($tickets as $ticket)   
                                    <tr data-id="{{ $ticket->id }}">
                                        <td>
                                            {{ $j++ }}
                                        </td>
                                        <td><a href="{{ url('staff/view/ticket/'.$ticket->id) }}" style="color: #1e1e1e">
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
                                            $user = App\Models\User::find($ticket->closed_by);
                                        @endphp
                                        <td>
                                            <a class="font-weight-600">{{ App\Models\User::find($ticket->assigned_to)->name }}</a>
                                        </td>
                                        <td>
                                            <div>
                                              <style>
                                                .star-rating .star {
                                                    display: inline-block;
                                                    font-size: 1.5rem;
                                                    color: #ccc;
                                                    transition: color 0.2s;
                                                  }

                                                  .star-rating .star.filled {
                                                    color: #f00;
                                                  }
                                              </style>
                                              <div class="star-rating" style="text-align: center;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                  <span class="star{{ $i <= $ticket->rating ? ' filled' : '' }}" data-rating="{{ $i }}">&#9733;</span>
                                                @endfor
                                              </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a class="font-weight-600">{{ $user->name }}</a>
                                        </td>
                                        <td><div class="badge badge-success">{{ $ticket->status }}</div></td>
                                        
                                        <td>
                                            <a href="{{ url('staff/view/ticket/'.$ticket->id) }}" class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="View"><i class="fas fa-solid fa-eye"></i></a>
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