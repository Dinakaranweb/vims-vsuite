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
            <h1>Notifications</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Notifications</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Notifications</h2>
            <p class="section-lead">
              Carefully read all the notifications.
            </p>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Latest Notifications</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Notification</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $sno = 1;
                                @endphp
                                @foreach ($notifications as $notification)   
                                
                                @php
                                
                                    if(Auth::user()->role == 'Staff'){
                                        $role = 'staff';
                                    }elseif(Auth::user()->role == 'HOD'){
                                        $role = 'admin';
                                    }elseif(Auth::user()->role == 'SuperAdmin'){
                                        $role = 'superadmin';
                                    }
                                
                                    $link = url($role.'/view/'.$notification->task_type.'/'.$notification->task_id);

                                    if($notification->task_type == 'postal'){
                                        $link = url('post/view/'.$notification->task_id);
                                    }
                            
                                    if($notification->task_type == 'document'){
                                        $link = url('view/document/'.$notification->task_id);
                                    }
                                
                                @endphp
                                    <tr data-id="{{ $notification->id }}">
                                        <td>
                                            {{ $sno++ }}
                                        </td>
                                        <td><a href="{{ $link }}">
                                            {!! $notification->message !!}</a>
                                        </td>
                                        <td>
                                            @if($notification->is_read == True)
                                                <a href="{{ route('notifications-mark', ['notification_id' => $notification->id]) }}" class="btn btn-outline-primary">Mark as Unread</a>
                                            @else
                                                <a href="{{ route('notifications-mark', ['notification_id' => $notification->id]) }}" class="btn btn-primary">Mark as Read</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
                            {!! $notifications->links('frontend.pagination.custom') !!}
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