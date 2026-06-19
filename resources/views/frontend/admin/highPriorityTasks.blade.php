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
            <h1>High Priority Tasks</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="#">High Priority Tasks</a></div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">High Priority Tasks</h2>
            <p class="section-lead">
              Make sure the High Priority tasks are handled immediately.
            </p>

            <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h4>High Priority Tasks</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped" id="table-1" style="text-align: center;">
                      <tr>
                        <th>S.No</th>
                        <th>Task Type</th>
                        <th>Task</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Due</th>
                        <th>Action</th>
                      </tr>
                      @php
                        $i=1;
                      @endphp
                      @foreach($highPriorityTasksPaginated as $task)
                      <tr data-id="{{ $i }}">
                        <td><a href="{{ url('admin/view/'.$task->task_type.'/'.$task->id) }}">{{ $i++ }}</a></td>
                        <td class="font-weight-600">{{ ucfirst($task->task_type) }}</td>
                        <td class="font-weight-600">{{ $task->task_title }}</td>                        
                        <td class="font-weight-600">{{ $task->task_from }}</td>                        
                        @if ($task->task_status  == "Open")
                            <td><div class="badge badge-info">{{ $task->task_status }}</div></td>
                        @elseif ($task->task_status == "In Progress")
                            <td><div class="badge badge-warning">{{ $task->task_status }}</div></td>
                        @elseif ($task->task_status == "Hold")
                            <td><div class="badge badge-danger">{{ $task->task_status }}</div></td>
                        @elseif ($task->task_status == "Completed")
                            <td><div class="badge badge-dark">{{ $task->task_status }}</div></td>
                        @else
                            <td><div class="badge badge-success">{{ $task->task_status }}</div></td>
                        @endif
                        <td>{{ \Carbon\Carbon::parse($task->task_created_at)->format('d-m-Y') }}</td>
                        <td>{{ floor(\Carbon\Carbon::parse($task->task_created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</td>
                        <td>
                          <a href="{{ url('admin/view/'.$task->task_type.'/'.$task->id) }}" class="btn btn-primary">Detail</a>
                        </td>
                      </tr>
                      @endforeach
                      
                    </table>
                    {!! $highPriorityTasksPaginated->links('frontend.pagination.custom') !!}
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