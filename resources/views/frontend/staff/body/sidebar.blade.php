<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href=""><img src="{{ asset('assets/img/vm/logo.jpg') }}" height="60px"> &nbsp;</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="">VMRF</a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header" style="margin-top: 15px;">Dashboard</li>
        <li class="{{ $activeMenu == 'Dashboard' ? 'active' : '' }}">
          <a href="{{ route('staff_dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
        </li>
        <li class="menu-header">Postal</li>
            <li class="dropdown {{ $activeMenu == 'postal' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i> <span>Posts Received</span></a>
              <ul class="dropdown-menu">
                <!-- <li class="{{ $activeDropdown == 'create_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_entry') }}">Create Post</a></li> -->
                <!-- <li class="{{ $activeDropdown == 'dept_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_dept_post') }}">{{ Auth::user()->department }} Posts</a></li> -->
                <li class="{{ $activeDropdown == 'dispatched_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('dispatched_posts') }}">Dispatched</a></li>
                <li class="{{ $activeDropdown == 'personal_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('staff_personal_post') }}">My Posts</a></li>
              </ul>
            </li>
        <li class="menu-header">Ticketing</li>
        <li class="dropdown {{ $activeMenu == 'tickets_received' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i></i> <span>Tickets Received</span></a>
          <ul class="dropdown-menu">

            <li class="{{ $activeDropdown == 'open_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $open_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('staff_open_tickets') }}">Open Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'tickets_in_progress' ? 'active' : '' }}">
              <a class="nav-link {{ $in_progress_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('staff_tickets_in_progress') }}">Tickets in Progress</a>
            </li>

            <li class="{{ $activeDropdown == 'hold_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $hold_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('staff_hold_tickets') }}">Tickets on Hold</a>
            </li>

            <li class="{{ $activeDropdown == 'completed_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $completed_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('staff_completed_tickets') }}">Completed Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'closed_tickets' ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('staff_closed_tickets') }}">Closed Tickets</a>
            </li>

          </ul>
        </li>
        <li class="dropdown {{ $activeMenu == 'tickets' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-regular fa-folder-open"></i> <span>Tickets Raised</span></a>
          <ul class="dropdown-menu">

            <li class="{{ $activeDropdown == 'unapproved_tickets' ? 'active' : '' }}">
             <a class="nav-link {{ $unapproved_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('staff_unapproved_tickets') }}">Unapproved Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'my_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $my_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('staff_my_tickets') }}">My Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'create_ticket' ? 'active' : '' }}">
             <a class="nav-link" href="{{ route('staff_create_ticket') }}">Create a Ticket</a>
            </li>

          </ul>
        
        <li class="{{ $activeMenu == 'forwarded_tickets' ? 'active' : '' }}">
          <a href="{{ route('staff-forwarded-report') }}" class="nav-link {{ $forwarded_tickets_count > 0? 'beep beep-sidebar' : '' }}">
            <i class="fas fa-columns"></i><span>Forwarded Tickets</span>
          </a>
        </li>

        <li class="{{ $activeMenu == 'reports' ? 'active' : '' }}">
          <a href="{{ route('staff-ticket-report') }}" class="nav-link">
            <i class="far fa-file-alt"></i><span>Report</span>
          </a>
        </li>
        
        </li>

        <li class="menu-header" style="margin-top: 15px;">Notifications</li>
        <li class="{{ $activeMenu == 'notifications' ? 'active' : '' }}">
          <a href="{{ route('notifications') }}" class="nav-link"><i class="fas fa-bell"></i><span>Notifications</span></a>
        </li>
        
        <li class="menu-header" style="margin-top: 15px;">Pings</li>
        <li class="{{ $activeMenu == 'pings' ? 'active' : '' }}">
          <a href="{{ route('pings') }}" class="nav-link"><i class="fas fa-exclamation"></i><span>Pings</span></a>
        </li>
      </ul>

      <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="{{ route('user-manual') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
          <i class="fas fa-rocket"></i> User Guide
        </a>
      </div>

    </aside>
  </div>