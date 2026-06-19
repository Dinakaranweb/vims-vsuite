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
                <h1>Tickets</h1>
                <div class="section-header-breadcrumb">
                  <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                  <div class="breadcrumb-item">Tickets</div>
                </div>
              </div>
    
              <div class="section-body">
                <h2 class="section-title">{{ $ticket->title }}</h2>
                <p class="section-lead">
                  {{ $ticket->priority }} &nbsp;Priority Task.
                </p>
    
                <div class="row">
                  <div class="col-lg-8 col-md-12">
                    
                    <div class="tickets" style="display: block;">
                      <div class="ticket-content" style="width:100%">
                        <div class="card">
                          <div class="card-body">
                            <div class="ticket-header">
                              <!-- <div class="ticket-sender-picture img-shadow">
                                <img src="assets/img/avatar/avatar-5.png" alt="image">
                              </div> -->
                              <div class="ticket-detail">
                                <div class="ticket-title">
                                  <h4>{{ $ticket->title }}</h4>
                                </div>
                                <div class="ticket-info">
                                  <div class="font-weight-600">{{ \App\Models\User::find($ticket->ticket_by)->name }} - {{ \App\Models\User::find($ticket->ticket_by)->role }} from {{ $ticket->ticket_from }} Department</div>
                                  <div class="bullet"></div>
                                  <div class="text-primary font-weight-600">{{ $ticket->created_at->diffForHumans() }}</div>
                                </div>
                              </div>
                            </div>
                            <div class="ticket-description" style="overflow-x: auto;">
                              <div>
                                {!! $ticket->description !!}
                              </div>
                              @if($ticket->file)
                              <div>
                                Attachment : <a href="{{ Storage::url($ticket->file) }}" target="__blank">{{ basename($ticket->file) }}</a>
                              </div>
                              @endif
                            </div>
                          </div>
                        </div>
                        @foreach($conversation as $convo)
                          @if($convo->title == 'Forward')
                          <div class="card">
                            <div class="card-body">
                              <div class="ticket-header">
                                <!-- <div class="ticket-sender-picture img-shadow">
                                  <img src="assets/img/avatar/avatar-5.png" alt="image">
                                </div> -->
                                <div class="ticket-detail">
                                  <div class="ticket-title">
                                    <h4 style="color:red">{{ $convo->title }}ed</h4>
                                  </div>
                                  <div class="ticket-info">
                                    <!-- <div class="font-weight-600">{{ $convo->by }}</div> -->
                                    <div class="bullet"></div>
                                    <div class="text-primary font-weight-600">{{ $convo->created_at->diffForHumans() }}</div>
                                  </div>
                                </div>
                              </div>
                              <div class="ticket-description" style="overflow-x: auto;">
                                <div>
                                  {!! $convo->description !!}
                                </div>
                              </div>
                            </div>
                          </div>
                          @else
                          <div class="card">
                            <div class="card-body">
                              <div class="ticket-header">
                                <!-- <div class="ticket-sender-picture img-shadow">
                                  <img src="assets/img/avatar/avatar-5.png" alt="image">
                                </div> -->
                                <div class="ticket-detail">
                                  <div class="ticket-title">
                                    <h4>{{ $convo->by }}</h4>
                                  </div>
                                  <div class="ticket-info">
                                    <!-- <div class="font-weight-600">{{ $convo->by }}</div> -->
                                    <div class="bullet"></div>
                                    <div class="text-primary font-weight-600">{{ $convo->created_at->diffForHumans() }}</div>
                                  </div>
                                </div>
                              </div>
                              <div class="ticket-description" style="overflow-x: auto;">
                                <div>
                                  {!! $convo->description !!}
                                </div>
                                @if($convo->file)
                                <div>
                                  Attachment : <a href="{{ Storage::url($convo->file) }}" target="__blank">{{ basename($convo->file) }}</a>
                                </div>
                                @endif
                              </div>
                            </div>
                          </div>
                          @endif
                        @endforeach
                        @if($ticket->status == 'Closed')
                          <div class="card">
                            <div class="card-body">
                              <div class="ticket-header">
                                <!-- <div class="ticket-sender-picture img-shadow">
                                  <img src="assets/img/avatar/avatar-5.png" alt="image">
                                </div> -->
                                <div class="ticket-detail">
                                  <div class="ticket-title">
                                    <h4>Ticket Closed by {{ App\Models\User::find($ticket->closed_by)->name }}</h4>
                                  </div>
                                  <div class="ticket-info">
                                    <!-- <div class="font-weight-600">{{ $convo->by }}</div> -->
                                    <div class="bullet"></div>
                                    <div class="text-primary font-weight-600">{{ $ticket->updated_at->diffForHumans() }}</div>
                                  </div>
                                </div>
                              </div>
                              <div class="ticket-description" style="overflow-x: auto;">
                                <div>
                                  <style>
                                    .star-rating .star {
                                        display: inline-block;
                                        font-size: 2rem;
                                        color: #ccc;
                                        transition: color 0.2s;
                                      }

                                      .star-rating .star.filled {
                                        color: #f00;
                                      }
                                  </style>
                                  <div class="star-rating" style="text-align: center;">
                                    <p>Performance rating for the ticket</p>
                                    @for ($i = 1; $i <= 5; $i++)
                                      <span class="star{{ $i <= $ticket->rating ? ' filled' : '' }}" data-rating="{{ $i }}">&#9733;</span>
                                    @endfor
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                        @if($ticket->status != "Closed" && $ticket->assigned_to != Null && $ticket->status != 'Hold')
                        <div class="card">
                          <div class="card-body">
                            <div class="ticket-form">
                              <form action="{{ route('ticket-respond') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                  <textarea name="description" class="summernote form-control" placeholder="Type a reply ..."></textarea>
                                </div>
                                <div class="form-group row mb-4">
                                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File</label>
                                      <div class="col-sm-12 col-md-7">
                                        <div class="custom-file">
                                          <input type="file" name="file" class="custom-file-input">
                                          <label class="custom-file-label">Choose file</label>
                                        </div>
                                      </div>
                                    </div>
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <div class="form-group text-right">
                                  <a href="{{ route('download-ticket', $ticket->id) }}" class="btn btn-warning btn-lg" style="color:#fff; margin-right: 20px;">
                                    Download as PDF
                                  </a>
                                  <a class="btn btn-danger btn-lg" style="color:#fff; margin-right: 20px;" id="modal-8">
                                    Forward
                                  </a>
                                  <button class="btn btn-primary btn-lg">
                                    Reply
                                  </button>
                                </div>
                                <script>
                                    $(document).ready(function () {
                                        $('.custom-file-input').on('change', function (event) {
                                            var inputFile = event.currentTarget;
                                            $(inputFile).parent()
                                                .find('.custom-file-label')
                                                .html(inputFile.files[0].name);
                                        });
                                    });
                                </script>
                              </form>
                              <form class="modal-part" id="modal-forward-part">
                                @csrf
                                <p>Forward the ticket to any of the following</p>
                                <div class="form-group">
                                  <label>Forward to</label>
                                  <div class="input-group">
                                    <div class="col-sm-12 col-md-12">
                                      <select class="form-control selectric" name="forwarded_to" required>
                                          <optgroup label="Sections">
                                              <option value="ICT">ICT Section</option>
                                              <option value="Property Management">Property Management Section</option>
                                              <option value="Vehicle">Vehicle Section</option>
                                          </optgroup>
                                          <optgroup label="Officers">
                                              @if(Auth::user()->department == 'Registrar')
                                              <option value="Pro-VC & VC">Pro-VC & VC</option>
                                              @elseif(Auth::user()->department == 'Pro-VC')
                                              <option value="VC">VC</option>
                                              @endif
                                          </optgroup>
                                      </select>
                                    </div>
                                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-12">
                    <div class="col-lg-12 col-md-12">
                      <div class="card">
                        <div class="card-header">
                          <h4>Ticket Summary</h4>
                          <div class="card-header-action">
                            <a data-collapse="#mycard-collapse1" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                          </div>
                        </div>
                        @php
                          $user_details = \App\Models\User::find($ticket->ticket_by);
                          if($user_details->role == 'SuperAdmin'){
                            $created_by = $user_details->department;
                          }else{
                            $created_by = $user_details->name.', '.$user_details->designation;
                          }
                        @endphp
                        <div class="collapse show" id="mycard-collapse1">
                          <div class="card-body">
                            <table border="1" style="width:100%">
                              <tr style="padding:10px">
                                <th style="padding:15px">Ticket ID</th>
                                <td style="padding:10px">{{ $ticket->ticket_id }}</td>
                              </tr>
                              <tr style="padding:10px">
                                <th style="padding:15px">Created by</th>
                                <td style="padding:10px">{{ $created_by }}</td>
                              </tr>
                              <tr style="padding:10px">
                                <th style="padding:15px">From</th>
                                <td style="padding:10px">{{ $ticket->ticket_from }} Dept</td>
                              </tr>
                              <tr style="padding:10px">
                                <th style="padding:15px">To</th>
                                <td style="padding:10px">{{ $ticket->ticket_to }} Dept</td>
                              </tr>
                              @php
                                $user = \App\Models\User::where('department', '=', $ticket->ticket_to)->where('role', '=', 'Staff')->get();
                                if($ticket->assigned_to != Null){
                                  $staff = \App\Models\User::find($ticket->assigned_to);
                                  
                                  if($staff->name == Auth::user()->name){
                                    $staff_name = "Self";
                                  }
                                  else{
                                    $staff_name = $staff->name;
                                  }
                                }
                                else{
                                  $staff_name = "Not yet Assigned";
                                }
                              @endphp
                              @if($ticket->ticket_by == Auth::id() || $ticket->ticket_from == Auth::user()->department || $ticket->ticket_to != Auth::user()->department)
                                <tr style="padding:10px">
                                  <th style="padding:15px">Assigned to</th>
                                  @if($ticket->assigned_to == Null)
                                  <td style="padding:10px">
                                    Not yet assigned
                                  </td>
                                  @else
                                  @if($staff->role == 'SuperAdmin')
                                    <td style="padding:10px">{{ $staff->department }}</td>
                                  @else
                                    <td style="padding:10px">{{ $staff->name }}, {{ $staff->designation }}</td>
                                  @endif
                                  @endif
                                </tr>
                              @elseif($ticket->ticket_to == Auth::user()->department)
                                <tr style="padding:10px">
                                  <th style="padding:15px">Assigned to</th>
                                  <td style="padding:10px">
                                    <div class="btn-group">
                                        <button class="btn btn-dark dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ $staff_name }}
                                        </button>
                                        <div class="dropdown-menu">
                                          @foreach($user as $emp)
                                            @if($emp->name == $staff_name)
                                              <a class="disabled-cus-link dropdown-item" href="{{ url('assign/ticket' ,['emp_id' => $emp->id, 'ticket_id' => $ticket->id]) }}">{{ $emp->name }}</a>
                                            @else
                                              <a class="dropdown-item" href="{{ url('assign/ticket' ,['emp_id' => $emp->id, 'ticket_id' => $ticket->id]) }}">{{ $emp->name }}</a>
                                            @endif
                                          @endforeach

                                              <div class="dropdown-divider"></div>
                                            @if($staff_name == 'Self')
                                              <a class="disabled-cus-link dropdown-item" href="{{ url('assign/ticket' ,['emp_id' => Auth::id(), 'ticket_id' => $ticket->id]) }}">Self</a>
                                            @else
                                              <a class="dropdown-item" href="{{ url('assign/ticket' ,['emp_id' => Auth::id(), 'ticket_id' => $ticket->id]) }}">Self</a>
                                            @endif
                                        </div>
                                      </div>
                                  </td>
                                </tr>
                              @endif
                              <tr style="padding:10px">
                                @if($ticket->ticket_to == Auth::user()->department || $ticket->ticket_from == Auth::user()->department)
                                  <th style="padding:15px">Priority</th>
                                    @if($ticket->priority == "Low")
                                    <td style="padding:10px">
                                      <div class="btn-group">
                                          <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              {{ $ticket->priority }}
                                          </button>
                                          <div class="dropdown-menu">
                                              <a class="dropdown-item" href="{{ url('change/priority/ticket' ,['ticket_id' => $ticket->id, 'priority' => 'Medium']) }}">Medium</a>
                                              <a class="dropdown-item" href="{{ url('change/priority/ticket' ,['ticket_id' => $ticket->id, 'priority' => 'High']) }}">High</a>
                                          </div>
                                      </div>
                                    </td>
                                  @elseif($ticket->priority == "Medium")
                                    <td style="padding:10px">
                                      <div class="btn-group">
                                          <button class="btn btn-warning dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              {{ $ticket->priority }}
                                          </button>
                                          <div class="dropdown-menu">
                                              <a class="dropdown-item" href="{{ url('change/priority/ticket' ,['ticket_id' => $ticket->id, 'priority' => 'Low']) }}">Low</a>
                                              <a class="dropdown-item" href="{{ url('change/priority/ticket' ,['ticket_id' => $ticket->id, 'priority' => 'High']) }}">High</a>
                                          </div>
                                      </div>
                                    </td>
                                  @else
                                    <td style="padding:10px">
                                      <div class="btn-group">
                                          <button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              {{ $ticket->priority }}
                                          </button>
                                          <div class="dropdown-menu">
                                              <a class="dropdown-item" href="{{ url('change/priority/ticket' ,['ticket_id' => $ticket->id, 'priority' => 'Low']) }}">Low</a>
                                              <a class="dropdown-item" href="{{ url('change/priority/ticket' ,['ticket_id' => $ticket->id, 'priority' => 'Medium']) }}">Medium</a>
                                          </div>
                                      </div>
                                    </td>
                                  @endif
                                @else
                                  <th style="padding:15px">Priority</th>
                                  @if($ticket->priority == "Low")
                                    <td style="padding:10px"><span class="badge badge-info">{{ $ticket->priority }}</span></td>
                                  @elseif($ticket->priority == "Medium")
                                    <td style="padding:10px"><span class="badge badge-warning">{{ $ticket->priority }}</span></td>
                                  @else
                                    <td style="padding:10px"><span class="badge badge-danger">{{ $ticket->priority }}</span></td>
                                  @endif
                                @endif
                              </tr>

                              @php
                                
                                $user = \App\Models\User::where('department', '=', $ticket->ticket_to)->where('role', '=', 'Staff')->get();

                                if($ticket->is_forwarded){

                                  $forwarded = App\Models\TicketForwarding::where('ticket_id',$ticket->id)->latest()->first();

                                  $forwarded_to = $forwarded->forwarded_to;
                                  $forwarded_by = $forwarded->forwarded_by;

                                  $forwarded_user = \App\Models\User::where('department', '=', $forwarded_to)->where('role', '=', 'Staff')->get();

                                  if($forwarded->assigned_to){
                                    $forwarded_to = App\Models\User::find($forwarded->assigned_to)->name;  
                                  }
                                
                                }else{

                                  $forwarded_to = "NIL";
                                  $forwarded_by = "NIL";
                                }

                              @endphp

                              @if($ticket->is_forwarded)
                                @if($forwarded->forwarded_to == Auth::user()->department)
                                <tr style="padding:10px">
                                    <th style="padding:15px">Forwarded to ({{ $forwarded->forwarded_to }})</th>
                                    <td style="padding:10px">
                                        <div class="btn-group">
                                            <button class="btn btn-dark dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ $forwarded_to }}
                                            </button>
                                            <div class="dropdown-menu">
                                                @foreach($forwarded_user as $emp)
                                                    @if($emp->name == $forwarded_to)
                                                        <a class="disabled-cus-link dropdown-item" href="{{ url('assign/forwarded/ticket' ,['emp_id' => $emp->id, 'forwarded_id' => $forwarded->id]) }}">{{ $emp->name }}</a>
                                                    @else
                                                        <a class="dropdown-item" href="{{ url('assign/forwarded/ticket' ,['emp_id' => $emp->id, 'forwarded_id' => $forwarded->id]) }}">{{ $emp->name }}</a>
                                                    @endif
                                                @endforeach
                                                <div class="dropdown-divider"></div>
                                                @if($forwarded_to == Auth::user()->department)
                                                    <a class="disabled-cus-link dropdown-item" href="{{ url('assign/forwarded/ticket' ,['emp_id' => Auth::id(), 'forwarded_id' => $forwarded->id]) }}">Self</a>
                                                @else
                                                    <a class="dropdown-item" href="{{ url('assign/forwarded/ticket' ,['emp_id' => Auth::id(), 'forwarded_id' => $forwarded->id]) }}">Self</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @else
                                <tr style="padding:10px">
                                  <th style="padding:15px">Forwarded</th>
                                  <td style="padding:10px">
                                      {{ $forwarded->forwarded_to }}
                                  </td>
                                </tr>
                                @endif
                              @endif
                              <tr style="padding:10px">
                                <th style="padding:15px">Pings</th>
                                <td style="padding:10px">
                                  @php
                                      $pings = App\Models\Ping::where('task_id', '=', $ticket->id)->get();
                                      $total = 0;
                                      foreach($pings as $ping){
                                          $total = $total + $ping->ping_count;
                                      }
                                  @endphp
                                  {{ $total }} - 
                                  <div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class=" fas fa-solid fa-bell"></i>
                                    </button>
                                    
                                    @include('frontend.ping.ping-alert')
                                    
                                  </div>
                                </td>
                              </tr>
                              @if($ticket->assigned_to != Null)
                              <tr style="padding:10px">
                                <th style="padding:15px">Status</th>
                                @if ($ticket->status == "Open")
                                  <td style="padding:10px">
                                    <div class="btn-group">
                                      <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Open
                                      </button>
                                      <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'In Progress', 'ticket_id' => $ticket->id]) }}">In Progress</a>
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Hold', 'ticket_id' => $ticket->id]) }}">Hold</a>
                                        @if($ticket->assigned_to == Auth::id())
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Completed', 'ticket_id' => $ticket->id]) }}">Completed</a>
                                        @endif
                                        @if($ticket->ticket_from == Auth::user()->department)
                                        <a class="dropdown-item" id="modal-5" style="cursor: pointer;">Close</a>
                                        @endif
                                        <!-- <button class="btn btn-primary" id="modal-5">Login</button> -->
                                      </div>
                                    </div>
                                  </td>
                                @elseif ($ticket->status == "In Progress")
                                  <td style="padding:10px">
                                    <div class="btn-group">
                                      <button class="btn btn-warning dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      In Progress
                                      </button>
                                      <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Hold', 'ticket_id' => $ticket->id]) }}">Hold</a>
                                        @if($ticket->assigned_to == Auth::id())
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Completed', 'ticket_id' => $ticket->id]) }}">Completed</a>
                                        @endif
                                        @if($ticket->ticket_from == Auth::user()->department)
                                        <a class="dropdown-item" id="modal-5" style="cursor: pointer;">Close</a>
                                        @endif

                                      </div>
                                    </div>
                                  </td>
                                @elseif ($ticket->status == "Hold")
                                  <td style="padding:10px">
                                    <div class="btn-group">
                                      <button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Hold
                                      </button>
                                      <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Reopen', 'ticket_id' => $ticket->id]) }}">Re-open</a>
                                        @if($ticket->assigned_to == Auth::id())
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Completed', 'ticket_id' => $ticket->id]) }}">Completed</a>
                                        @endif
                                        @if($ticket->ticket_from == Auth::user()->department)
                                        <a class="dropdown-item" id="modal-5" style="cursor: pointer;">Close</a>
                                        @endif
                                      </div>
                                    </div>
                                  </td>
                                @elseif ($ticket->status == "Completed")
                                  <td style="padding:10px">
                                    <div class="btn-group">
                                      <button class="btn btn-dark dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Completed
                                      </button>
                                      <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'In Progress', 'ticket_id' => $ticket->id]) }}">In Progress</a>
                                        @if($ticket->assigned_to == Auth::id())
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Completed', 'ticket_id' => $ticket->id]) }}">Completed</a>
                                        @endif
                                        @if($ticket->ticket_from == Auth::user()->department)
                                        <a class="dropdown-item" id="modal-5" style="cursor: pointer;">Close</a>
                                        @endif
                                      </div>
                                    </div>
                                  </td>
                                @else
                                  <td style="padding:10px">
                                    <div class="btn-group">
                                      <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000">
                                      Closed
                                      </button>
                                      <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('change.ticket.status' ,['ticket_status' => 'Reopen', 'ticket_id' => $ticket->id]) }}">Re-open</a>
                                        
                                      </div>
                                    </div>
                                  </td>
                                @endif
                                
                              </tr>
                              @endif
                            </table>
                            @if($ticket->assigned_to != Null)
                            <form class="modal-part" id="modal-login-part" method="POST" action="{{ route('close-ticket') }}">
                              @csrf
                              <div id="full-stars-example-two">
                                <p class="desc" style="font-family: sans-serif; font-size:0.9rem">Please rate {{ App\Models\User::find($ticket->assigned_to)->name }}'s Performance</p>
                                  <div class="feedback">
                                    <div class="rating">
                                      <input type="radio" name="rating3" value="5" id="rating-5">
                                      <label for="rating-5"></label>
                                      <input type="radio" name="rating3" value="4" id="rating-4">
                                      <label for="rating-4"></label>
                                      <input type="radio" name="rating3" value="3" id="rating-3">
                                      <label for="rating-3"></label>
                                      <input type="radio" name="rating3" value="2" id="rating-2">
                                      <label for="rating-2"></label>
                                      <input type="radio" name="rating3" value="1" id="rating-1">
                                      <label for="rating-1"></label>
                                      <div class="emoji-wrapper">
                                        <div class="emoji">
                                          <svg class="rating-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
                                            <path d="M512 256c0 141.44-114.64 256-256 256-80.48 0-152.32-37.12-199.28-95.28 43.92 35.52 99.84 56.72 160.72 56.72 141.36 0 256-114.56 256-256 0-60.88-21.2-116.8-56.72-160.72C474.8 103.68 512 175.52 512 256z" fill="#f4c534"/>
                                            <ellipse transform="scale(-1) rotate(31.21 715.433 -595.455)" cx="166.318" cy="199.829" rx="56.146" ry="56.13" fill="#fff"/>
                                            <ellipse transform="rotate(-148.804 180.87 175.82)" cx="180.871" cy="175.822" rx="28.048" ry="28.08" fill="#3e4347"/>
                                            <ellipse transform="rotate(-113.778 194.434 165.995)" cx="194.433" cy="165.993" rx="8.016" ry="5.296" fill="#5a5f63"/>
                                            <ellipse transform="scale(-1) rotate(31.21 715.397 -1237.664)" cx="345.695" cy="199.819" rx="56.146" ry="56.13" fill="#fff"/>
                                            <ellipse transform="rotate(-148.804 360.25 175.837)" cx="360.252" cy="175.84" rx="28.048" ry="28.08" fill="#3e4347"/>
                                            <ellipse transform="scale(-1) rotate(66.227 254.508 -573.138)" cx="373.794" cy="165.987" rx="8.016" ry="5.296" fill="#5a5f63"/>
                                            <path d="M370.56 344.4c0 7.696-6.224 13.92-13.92 13.92H155.36c-7.616 0-13.92-6.224-13.92-13.92s6.304-13.92 13.92-13.92h201.296c7.696.016 13.904 6.224 13.904 13.92z" fill="#3e4347"/>
                                          </svg>
                                          <svg class="rating-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
                                            <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
                                            <path d="M328.4 428a92.8 92.8 0 0 0-145-.1 6.8 6.8 0 0 1-12-5.8 86.6 86.6 0 0 1 84.5-69 86.6 86.6 0 0 1 84.7 69.8c1.3 6.9-7.7 10.6-12.2 5.1z" fill="#3e4347"/>
                                            <path d="M269.2 222.3c5.3 62.8 52 113.9 104.8 113.9 52.3 0 90.8-51.1 85.6-113.9-2-25-10.8-47.9-23.7-66.7-4.1-6.1-12.2-8-18.5-4.2a111.8 111.8 0 0 1-60.1 16.2c-22.8 0-42.1-5.6-57.8-14.8-6.8-4-15.4-1.5-18.9 5.4-9 18.2-13.2 40.3-11.4 64.1z" fill="#f4c534"/>
                                            <path d="M357 189.5c25.8 0 47-7.1 63.7-18.7 10 14.6 17 32.1 18.7 51.6 4 49.6-26.1 89.7-67.5 89.7-41.6 0-78.4-40.1-82.5-89.7A95 95 0 0 1 298 174c16 9.7 35.6 15.5 59 15.5z" fill="#fff"/>
                                            <path d="M396.2 246.1a38.5 38.5 0 0 1-38.7 38.6 38.5 38.5 0 0 1-38.6-38.6 38.6 38.6 0 1 1 77.3 0z" fill="#3e4347"/>
                                            <path d="M380.4 241.1c-3.2 3.2-9.9 1.7-14.9-3.2-4.8-4.8-6.2-11.5-3-14.7 3.3-3.4 10-2 14.9 2.9 4.9 5 6.4 11.7 3 15z" fill="#fff"/>
                                            <path d="M242.8 222.3c-5.3 62.8-52 113.9-104.8 113.9-52.3 0-90.8-51.1-85.6-113.9 2-25 10.8-47.9 23.7-66.7 4.1-6.1 12.2-8 18.5-4.2 16.2 10.1 36.2 16.2 60.1 16.2 22.8 0 42.1-5.6 57.8-14.8 6.8-4 15.4-1.5 18.9 5.4 9 18.2 13.2 40.3 11.4 64.1z" fill="#f4c534"/>
                                            <path d="M155 189.5c-25.8 0-47-7.1-63.7-18.7-10 14.6-17 32.1-18.7 51.6-4 49.6 26.1 89.7 67.5 89.7 41.6 0 78.4-40.1 82.5-89.7A95 95 0 0 0 214 174c-16 9.7-35.6 15.5-59 15.5z" fill="#fff"/>
                                            <path d="M115.8 246.1a38.5 38.5 0 0 0 38.7 38.6 38.5 38.5 0 0 0 38.6-38.6 38.6 38.6 0 1 0-77.3 0z" fill="#3e4347"/>
                                            <path d="M131.6 241.1c3.2 3.2 9.9 1.7 14.9-3.2 4.8-4.8 6.2-11.5 3-14.7-3.3-3.4-10-2-14.9 2.9-4.9 5-6.4 11.7-3 15z" fill="#fff"/>
                                          </svg>
                                          <svg class="rating-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
                                            <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
                                            <path d="M336.6 403.2c-6.5 8-16 10-25.5 5.2a117.6 117.6 0 0 0-110.2 0c-9.4 4.9-19 3.3-25.6-4.6-6.5-7.7-4.7-21.1 8.4-28 45.1-24 99.5-24 144.6 0 13 7 14.8 19.7 8.3 27.4z" fill="#3e4347"/>
                                            <path d="M276.6 244.3a79.3 79.3 0 1 1 158.8 0 79.5 79.5 0 1 1-158.8 0z" fill="#fff"/>
                                            <circle cx="340" cy="260.4" r="36.2" fill="#3e4347"/>
                                            <g fill="#fff">
                                              <ellipse transform="rotate(-135 326.4 246.6)" cx="326.4" cy="246.6" rx="6.5" ry="10"/>
                                              <path d="M231.9 244.3a79.3 79.3 0 1 0-158.8 0 79.5 79.5 0 1 0 158.8 0z"/>
                                            </g>
                                            <circle cx="168.5" cy="260.4" r="36.2" fill="#3e4347"/>
                                            <ellipse transform="rotate(-135 182.1 246.7)" cx="182.1" cy="246.7" rx="10" ry="6.5" fill="#fff"/>
                                          </svg>
                                          <svg class="rating-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
                                            <path d="M407.7 352.8a163.9 163.9 0 0 1-303.5 0c-2.3-5.5 1.5-12 7.5-13.2a780.8 780.8 0 0 1 288.4 0c6 1.2 9.9 7.7 7.6 13.2z" fill="#3e4347"/>
                                            <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
                                            <g fill="#fff">
                                              <path d="M115.3 339c18.2 29.6 75.1 32.8 143.1 32.8 67.1 0 124.2-3.2 143.2-31.6l-1.5-.6a780.6 780.6 0 0 0-284.8-.6z"/>
                                              <ellipse cx="356.4" cy="205.3" rx="81.1" ry="81"/>
                                            </g>
                                            <ellipse cx="356.4" cy="205.3" rx="44.2" ry="44.2" fill="#3e4347"/>
                                            <g fill="#fff">
                                              <ellipse transform="scale(-1) rotate(45 454 -906)" cx="375.3" cy="188.1" rx="12" ry="8.1"/>
                                              <ellipse cx="155.6" cy="205.3" rx="81.1" ry="81"/>
                                            </g>
                                            <ellipse cx="155.6" cy="205.3" rx="44.2" ry="44.2" fill="#3e4347"/>
                                            <ellipse transform="scale(-1) rotate(45 454 -421.3)" cx="174.5" cy="188" rx="12" ry="8.1" fill="#fff"/>
                                          </svg>
                                          <svg class="rating-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
                                            <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
                                            <path d="M232.3 201.3c0 49.2-74.3 94.2-74.3 94.2s-74.4-45-74.4-94.2a38 38 0 0 1 74.4-11.1 38 38 0 0 1 74.3 11.1z" fill="#e24b4b"/>
                                            <path d="M96.1 173.3a37.7 37.7 0 0 0-12.4 28c0 49.2 74.3 94.2 74.3 94.2C80.2 229.8 95.6 175.2 96 173.3z" fill="#d03f3f"/>
                                            <path d="M215.2 200c-3.6 3-9.8 1-13.8-4.1-4.2-5.2-4.6-11.5-1.2-14.1 3.6-2.8 9.7-.7 13.9 4.4 4 5.2 4.6 11.4 1.1 13.8z" fill="#fff"/>
                                            <path d="M428.4 201.3c0 49.2-74.4 94.2-74.4 94.2s-74.3-45-74.3-94.2a38 38 0 0 1 74.4-11.1 38 38 0 0 1 74.3 11.1z" fill="#e24b4b"/>
                                            <path d="M292.2 173.3a37.7 37.7 0 0 0-12.4 28c0 49.2 74.3 94.2 74.3 94.2-77.8-65.7-62.4-120.3-61.9-122.2z" fill="#d03f3f"/>
                                            <path d="M411.3 200c-3.6 3-9.8 1-13.8-4.1-4.2-5.2-4.6-11.5-1.2-14.1 3.6-2.8 9.7-.7 13.9 4.4 4 5.2 4.6 11.4 1.1 13.8z" fill="#fff"/>
                                            <path d="M381.7 374.1c-30.2 35.9-75.3 64.4-125.7 64.4s-95.4-28.5-125.8-64.2a17.6 17.6 0 0 1 16.5-28.7 627.7 627.7 0 0 0 218.7-.1c16.2-2.7 27 16.1 16.3 28.6z" fill="#3e4347"/>
                                            <path d="M256 438.5c25.7 0 50-7.5 71.7-19.5-9-33.7-40.7-43.3-62.6-31.7-29.7 15.8-62.8-4.7-75.6 34.3 20.3 10.4 42.8 17 66.5 17z" fill="#e24b4b"/>
                                          </svg>
                                          <svg class="rating-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <g fill="#ffd93b">
                                              <circle cx="256" cy="256" r="256"/>
                                              <path d="M512 256A256 256 0 0 1 56.8 416.7a256 256 0 0 0 360-360c58 47 95.2 118.8 95.2 199.3z"/>
                                            </g>
                                            <path d="M512 99.4v165.1c0 11-8.9 19.9-19.7 19.9h-187c-13 0-23.5-10.5-23.5-23.5v-21.3c0-12.9-8.9-24.8-21.6-26.7-16.2-2.5-30 10-30 25.5V261c0 13-10.5 23.5-23.5 23.5h-187A19.7 19.7 0 0 1 0 264.7V99.4c0-10.9 8.8-19.7 19.7-19.7h472.6c10.8 0 19.7 8.7 19.7 19.7z" fill="#e9eff4"/>
                                            <path d="M204.6 138v88.2a23 23 0 0 1-23 23H58.2a23 23 0 0 1-23-23v-88.3a23 23 0 0 1 23-23h123.4a23 23 0 0 1 23 23z" fill="#45cbea"/>
                                            <path d="M476.9 138v88.2a23 23 0 0 1-23 23H330.3a23 23 0 0 1-23-23v-88.3a23 23 0 0 1 23-23h123.4a23 23 0 0 1 23 23z" fill="#e84d88"/>
                                            <g fill="#38c0dc">
                                              <path d="M95.2 114.9l-60 60v15.2l75.2-75.2zM123.3 114.9L35.1 203v23.2c0 1.8.3 3.7.7 5.4l116.8-116.7h-29.3z"/>
                                            </g>
                                            <g fill="#d23f77">
                                              <path d="M373.3 114.9l-66 66V196l81.3-81.2zM401.5 114.9l-94.1 94v17.3c0 3.5.8 6.8 2.2 9.8l121.1-121.1h-29.2z"/>
                                            </g>
                                            <path d="M329.5 395.2c0 44.7-33 81-73.4 81-40.7 0-73.5-36.3-73.5-81s32.8-81 73.5-81c40.5 0 73.4 36.3 73.4 81z" fill="#3e4347"/>
                                            <path d="M256 476.2a70 70 0 0 0 53.3-25.5 34.6 34.6 0 0 0-58-25 34.4 34.4 0 0 0-47.8 26 69.9 69.9 0 0 0 52.6 24.5z" fill="#e24b4b"/>
                                            <path d="M290.3 434.8c-1 3.4-5.8 5.2-11 3.9s-8.4-5.1-7.4-8.7c.8-3.3 5.7-5 10.7-3.8 5.1 1.4 8.5 5.3 7.7 8.6z" fill="#fff" opacity=".2"/>
                                          </svg>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <input type="hidden" id="ticket_id" name="ticket_id" value="{{ $ticket->id }}"><br /><br />
                                  <div class="button-container" style="display: flex; justify-content: center;">
                                      <input type="submit" class="btn btn-primary" value="Close Ticket">
                                  </div>
                              </div>
                            </form>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="card">
                        <div class="card-header">
                          <h4>Ticket Log</h4>
                          <div class="card-header-action">
                            <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                          </div>
                        </div>
                        <div class="collapse show" id="mycard-collapse">
                          <div class="card-body">
                            <div class="col-12">
                              <div class="activities">
                                @foreach($log as $entry)
                                  <div class="activity">
                                    <div class="activity-icon bg-primary text-white shadow-primary">
                                      <i class="fas fa-comment-alt"></i>
                                    </div>
                                    <div class="activity-detail">
                                      <div class="mb-2">
                                        <span class="text-job text-primary">{{ $entry->created_at->diffForHumans() }}</span>
                                        <span class="bullet"></span>
                                      </div>
                                      <p>{!! $entry->description !!}</p>
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                          </div>
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
  <script>
    $(document).ready(function() {
        $('#modal-5').on('click', function(event) {
            event.preventDefault(); // Prevent the default anchor behavior
            $('#exampleModal').modal('show'); // Show the modal
        });
    });
  </script>
@endsection