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
            <h1>Pings</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('staff_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Pings</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Pings</h2>
            <p class="section-lead">
              Please Work on the pinged tasks at priority.
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Latest Pings</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Pings</th>
                                    <th>Total Pings</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $sno = 1;
                                @endphp
                                @foreach ($pings as $ping)   
                                    <tr data-id="{{ $ping->id }}">
                                        <td>
                                            {{ $sno++ }}
                                        </td>
                                        <td><a href="{{ url('staff/view/'.$ping->task_type.'/'.$ping->task_id) }}">
                                            {{ App\Models\Ticket::find($ping->task_id)->title }} from {{ App\Models\Ticket::find($ping->task_id)->ticket_from }}</a>
                                        </td>
                                        <td>
                                            {{ $ping->ping_count }}
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