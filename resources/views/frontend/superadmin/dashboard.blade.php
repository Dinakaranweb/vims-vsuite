@extends('frontend.frontend_master')

@section('content')
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      
      @include('frontend.superadmin.body.header')
      
      @include('frontend.superadmin.body.sidebar')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
              <a href="{{ route('super_admin_tickets_summary') }}">
                <div class="card card-statistic-2">
                  <div class="card-stats">
                    <div class="card-stats-title">Tickets Statistics 
                    </div>
                    <div class="card-stats-items">
                      <div class="card-stats-item">
                        <a href="{{ route('super_admin_total_tickets') }}">
                           @if(Auth::user()->department == 'Chancellor' || Auth::user()->department == 'Chancellor Office')
                              <div class="card-stats-item-count">0</div>
                            @else
                              <div class="card-stats-item-count">{{ $total_tickets }}</div>
                            @endif
                          <div class="card-stats-item-label">Total</div>
                        </a>
                      </div>
                      <div class="card-stats-item">
                        <a href="{{ route('super_admin_pending_tickets') }}">
                            @if(Auth::user()->department == 'Chancellor' || Auth::user()->department == 'Chancellor Office')
                              <div class="card-stats-item-count">0</div>
                            @else
                              <div class="card-stats-item-count">{{ $pending_tickets }}</div>
                            @endif
                          <div class="card-stats-item-label"><span style="color:red">Pending</span></div>
                        </a>
                      </div>
                      <div class="card-stats-item">
                        <a href="{{ route('super_admin_closed_tickets') }}">
                            @if(Auth::user()->department == 'Chancellor' || Auth::user()->department == 'Chancellor Office')
                              <div class="card-stats-item-count">0</div>
                            @else
                              <div class="card-stats-item-count">{{ $closed_tickets }}</div>
                            @endif
                          <div class="card-stats-item-label">closed</div>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-archive"></i>
                  </div>
                  <div class="card-wrap">
                    <a href="{{ route('super_admin_unassigned_tickets') }}">
                      <div class="card-header">
                        <h4>New Tickets</h4>
                      </div>
                      <div class="card-body">
                        {{ $new_tickets }}
                      </div>
                    </a>
                  </div>
                </div>
              </a>
            </div>
            <!--<div class="col-lg-4 col-md-4 col-sm-12">-->
            <!--  <div class="card card-statistic-2">-->
            <!--    <div class="card-stats">-->
            <!--      <div class="card-stats-title">Postal Statistics -->
            <!--      </div>-->
            <!--      <div class="card-stats-items">-->
            <!--        <div class="card-stats-item">-->
            <!--          <div class="card-stats-item-count">13</div>-->
            <!--          <div class="card-stats-item-label">Total</div>-->
            <!--        </div>-->
            <!--        <div class="card-stats-item">-->
            <!--          <div class="card-stats-item-count">3</div>-->
            <!--          <div class="card-stats-item-label"><span style="color:red">Pending</span></div>-->
            <!--        </div>-->
            <!--        <div class="card-stats-item">-->
            <!--          <div class="card-stats-item-count">10</div>-->
            <!--          <div class="card-stats-item-label">Closed</div>-->
            <!--        </div>-->
            <!--      </div>-->
            <!--    </div>-->
            <!--    <div class="card-icon shadow-primary bg-primary">-->
            <!--      <i class="fas fa-archive"></i>-->
            <!--    </div>-->
            <!--    <div class="card-wrap">-->
            <!--      <div class="card-header">-->
            <!--        <h4>New Posts</h4>-->
            <!--      </div>-->
            <!--      <div class="card-body">-->
            <!--        2-->
            <!--      </div>-->
            <!--    </div>-->
            <!--  </div>-->
            <!--</div>-->
            <div class="col-lg-4 col-md-4 col-sm-12">
              <div class="card card-statistic-2">
                    <div class="card-stats">
                      <div class="card-stats-title">Postal Statistics
                      </div>
                      <div class="card-stats-items">
                        <div class="card-stats-item">
                          <a href="{{ route('dept-posts', ['total' => true]) }}">
                            <div class="card-stats-item-count">{{ $total_posts }}</div>
                            <div class="card-stats-item-label">Received</div>
                          </a>
                        </div>
                        <div class="card-stats-item">
                          <a href="{{ route('dept-posts', ['status' => 'pending']) }}">
                            <div class="card-stats-item-count">{{ $pending_posts }}</div>
                            <div class="card-stats-item-label"><span style="color:red">Pending</span></div>
                          </a>
                        </div>
                        <div class="card-stats-item">
                          <a href="{{ route('admin_forwarded_post') }}">
                            <div class="card-stats-item-count">{{ $forwarded }}</div>
                            <div class="card-stats-item-label">Forwarded to you</div>
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                      <i class="fas fa-archive"></i>
                    </div>
                    <div class="card-wrap">
                      <a href="{{ route('dept-posts', ['unread' => true]) }}">
                        <div class="card-header">
                          <h4>Unread</h4>
                        </div>
                        <div class="card-body">
                          {{ $new_posts }}
                        </div>
                      </a>
                    </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
              <div class="card card-statistic-2">
                <div class="card-stats">
                    <div class="card-stats-title">
                        Summary
                    </div>
                  <div class="card-stats-items">
                    <div class="card-stats-item">
                      <a href="{{ route('total_documents') }}">
                        @if(Auth::user()->department == 'Chancellor' || Auth::user()->department == 'Chancellor Office')
                          <div class="card-stats-item-count">0</div>
                        @else
                          <div class="card-stats-item-count">{{ $total_docs }}</div>
                        @endif
                        <div class="card-stats-item-label">Total</div>
                      </a>
                    </div>
                    <div class="card-stats-item">
                      <a href="{{ route('pending_documents') }}">
                        <div class="card-stats-item-count">{{ $pending_docs }}</div>
                        <div class="card-stats-item-label"><span style="color:red">Pending</span></div>
                      </a>
                    </div>
                    @if(Auth::user()->department == 'Registrar')
                    <div class="card-stats-item">
                      <a href="{{ route('approved_documents') }}">
                        <div class="card-stats-item-count">{{ $approved_docs }}</div>
                        <div class="card-stats-item-label">VC Approved</div>
                      </a>
                    </div>
                    @else
                    <div class="card-stats-item">
                      <a href="{{ route('closed_documents') }}">
                        @if(Auth::user()->department == 'Chancellor')
                          <div class="card-stats-item-count">0</div>
                        @else
                          <div class="card-stats-item-count">{{ $closed_docs }}</div>
                        @endif
                        <div class="card-stats-item-label">Closed</div>
                      </a>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="card-icon shadow-primary bg-primary">
                  <i class="fas fa-archive"></i>
                </div>
                <div class="card-wrap">
                  <a href="{{ route('forwarded_documents') }}">
                    <div class="card-header">
                      <h4>Forwarded to you</h4>
                    </div>
                    <div class="card-body">
                      {{ $forwarded_to_you }}
                    </div>
                  </a>
                </div>
              </div>
            </div>
            
          </div>
          
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h4>High Priority Tasks</h4>
                  <div class="card-header-action">
                    <a href="{{ route('superadmin-high-priority-tasks') }}" class="btn btn-danger">View More <i class="fas fa-chevron-right"></i></a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped" id="table-1" style="text-align: center;">
                      <tr>
                        <th>S.No</th>
                        <th>Task Type</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Due</th>
                        <th>Action</th>
                      </tr>
                      @php
                        $i = 1;
                      @endphp
                      @foreach($highPriorityTasks as $task)
                      <tr>
                        <td>{{ $i++ }}</td>
                        <td class="font-weight-600">{{ ucfirst($task->task_type) }}</td>
                        <td class="font-weight-600"><a href="{{ url('superadmin/view/'.$task->task_type.'/'.$task->id) }}">{{ $task->task_title }}</a></td>                        
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
                        <td>{{ floor(\Carbon\Carbon::parse($task->task_due)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</td>
                        <td>
                          <a href="{{ url('superadmin/view/'.$task->task_type.'/'.$task->id) }}" class="btn btn-primary">Detail</a>
                        </td>
                      </tr>
                      @endforeach
                      
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-hero">
                <div class="card-header">
                  <div class="card-icon">
                    <i class="far fa-solid fa-bell"></i>
                  </div>
                  <h4>{{ $total_pings }}</h4>
                  <div class="card-description">New Pings</div>
                </div>
                <div class="card-body p-0">
                  <div class="tickets-list">
                    
                    @foreach($pings as $ping)
                      
                      @php
                        $pingController = app()->make('App\Http\Controllers\PingController');
                        $task_details = $pingController->getTaskDetails($ping->task_type, $ping->task_id)
                      @endphp

                      <a href="{{ $task_details['task_url'] }}" class="ticket-item">
                        <div class="ticket-title">
                          <h4>{{ $task_details['task_title'] }}</h4>
                        </div>
                        <div class="ticket-info">
                          <div>{{ $task_details['task_from'] }}</div>
                          <div class="bullet"></div>
                          <div>{{ $task_details['task_created_at']->diffForHumans() }}</div>
                          <div class="bullet"></div>
                          <div class="text-primary"><b>{{ $ping->ping_count }} - Pings</b></div>
                        </div>
                      </a>
                    @endforeach
                    
                    <a href="{{ route('pings') }}" class="ticket-item ticket-more">
                      View All <i class="fas fa-chevron-right"></i>
                    </a>
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