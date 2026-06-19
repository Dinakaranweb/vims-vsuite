<div class="navbar-bg"></div>
  <nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
      <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
      </ul>
      <!-- VIMS-VSuite Title -->
      <span class="navbar-brand mb-0 h1 vsuite-navbar-title">
        <i class="fas fa-gem text-warning" style="margin-right: 6px;"></i>
        VIMS-VSuite
      </span>
    </form>
    <ul class="navbar-nav navbar-right">
      @if($unread > 0)
      <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
      @else
      <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i class="far fa-bell"></i></a>
      @endif
        <div class="dropdown-menu dropdown-list dropdown-menu-right" style="overflow-y: auto;">
          <div class="dropdown-header">Notifications
            <div class="float-right">
              <a href="{{ route('notification-mark-all_read') }}">Mark All As Read</a>
            </div>
          </div>
          <div class="dropdown-list-content dropdown-list-icons">
            @foreach($notifications as $notification)
              @if($notification->is_read)
              <a href="{{ route('notification-redirect', ['task_type' => $notification->task_type, 'task_id' => $notification->task_id, 'notification_id' => $notification->id]) }}" class="dropdown-item dropdown-item-read" style="cursor:pointer">
              @else
              <a href="{{ route('notification-redirect', ['task_type' => $notification->task_type, 'task_id' => $notification->task_id, 'notification_id' => $notification->id]) }}" class="dropdown-item dropdown-item-unread" style="cursor:pointer">
              @endif
                <div class="dropdown-item-icon bg-primary text-white">
                  <i class="fas fa-bell"></i>
                </div>
                <div class="dropdown-item-desc">
                  {!! $notification->message !!}
                  <div class="time text-primary">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
              </a>
            @endforeach
          </div>
          <div class="dropdown-footer text-center">
            <a href="{{ route('notifications') }}">View All <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </li>
      <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <!-- <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1"> -->
        <div class="d-sm-none d-lg-inline-block">Welcome, {{ Auth::user()->name }}</div></a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-title">Logged in 5 min ago</div>
          <a href="{{ route('user-profile') }}" class="dropdown-item has-icon">
            <i class="far fa-user"></i> Profile
          </a>
          <div class="dropdown-divider"></div>
          <a href="{{ route('auth_logout') }}" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>