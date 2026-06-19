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
        <li class="{{ $activeMenu == 'dashboard' ? 'active' : '' }}">
          <a href="{{ route('super_admin_dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
        </li>
        <li class="menu-header">Approval/Requests</li>
            <li class="dropdown {{ $activeMenu == 'document' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-tags"></i> <span>Document Sent</span></a>
              <ul class="dropdown-menu">
                <li class="{{ $activeDropdown == 'create_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('create_document') }}">Create</a></li>
                <!-- <li class="{{ $activeDropdown == 'my_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('my_documents') }}">My Documents</a></li> -->
                <li class="{{ $activeDropdown == 'draft_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('draft_documents') }}">Drafts</a></li>
                <li class="{{ $activeDropdown == 'new_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('my_documents') }}">My Documents</a></li>
                <li class="{{ $activeDropdown == 'inProgress_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('inProgress_documents') }}">In Progress</a></li>
                <li class="{{ $activeDropdown == 'approved_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('approved_documents') }}">Approved</a></li>
                <li class="{{ $activeDropdown == 'rejected_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('rejected_documents') }}">Rejected</a></li>
                <!-- <li class="{{ $activeDropdown == 'closed_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('closed_documents') }}">Closed</a></li> -->
              </ul>
            </li>
            <li class="dropdown {{ $activeMenu == 'document_received' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-tasks"></i> <span>Document Received</span></a>
              <ul class="dropdown-menu">
                <li class="{{ $activeDropdown == 'forwarded_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('forwarded_documents') }}">Forwarded to you</a></li>
                <li class="{{ $activeDropdown == 'received_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('received_documents') }}">Sent to you</a></li>
              </ul>
            </li>
            <li class="{{ $activeMenu == 'deleted_doc' ? 'active' : '' }}">
              <a href="{{ route('deleted_documents') }}" class="nav-link">
                <i class="fas fa-trash"></i><span>Deleted Documents</span>
              </a>
            </li>
        <!--<li class="menu-header">Document Approval</li>-->
        <!--    <li class="dropdown {{ $activeMenu == 'document' ? 'active' : '' }}">-->
        <!--      <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i> <span>Document Approval</span></a>-->
        <!--      <ul class="dropdown-menu">-->
        <!--        <li class="{{ $activeDropdown == 'received_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('received_documents') }}">New Documents</a></li>-->
        <!--        <li class="{{ $activeDropdown == 'inProgress_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('inProgress_documents') }}">In Progress</a></li>-->
        <!--        <li class="{{ $activeDropdown == 'rejected_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('rejected_documents') }}">Rejected</a></li>-->
        <!--        <li class="{{ $activeDropdown == 'approved_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('approved_documents') }}">Approved</a></li>-->
        <!--        <li class="{{ $activeDropdown == 'closed_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('closed_documents') }}">Closed</a></li>-->
        <!--      </ul>-->
        <!--    </li>-->
        <li class="menu-header">Postal</li>
            <li class="dropdown {{ $activeMenu == 'postal' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i> <span>Posts Received</span></a>
              <ul class="dropdown-menu">
                <!-- <li class="{{ $activeDropdown == 'create_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_entry') }}">Create Post</a></li> -->
                <!-- <li class="{{ $activeDropdown == 'dept_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('superadmin_dept_post') }}">{{ Auth::user()->department }} Posts</a></li>
                <li class="{{ $activeDropdown == 'personal_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('superadmin_personal_post') }}">My Posts</a></li> -->
                <li class="{{ $activeDropdown == 'dispatched_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('dispatched_posts') }}">Dispatched</a></li>
                <li class="{{ $activeDropdown == 'collected_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('collected_posts') }}">Received</a></li>
                <li class="{{ $activeDropdown == 'search_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('search-posts') }}">Search Posts</a></li>
                @if(Auth::user()->department == 'DDE Examination' || Auth::user()->department == 'DDE Admission & Finance' || Auth::user()->role == 'SuperAdmin')
                <li class="{{ $activeDropdown == 'dde_details' ? 'active' : '' }}"><a class="nav-link" href="{{ route('dde_details.index') }}">DDE Details</a></li>
                @endif
                <li class="{{ $activeDropdown == 'personal_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('superadmin_personal_post') }}">My Posts</a></li>
                
                <li class="{{ $activeDropdown == 'personal_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_out_entry') }}">Create Out Posts</a></li>
                <li class="{{ $activeDropdown == 'in_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_in_entry') }}">Create In Posts</a></li>
              </ul>
            </li>

            <li class="dropdown {{ $activeMenu == 'postal_forward' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-cloud-sun-rain"></i> <span>Posts Forwarded</span></a>
              <ul class="dropdown-menu">
                <li class="{{ $activeDropdown == 'to_be_dispatched_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('to_be_dispatched_posts') }}">To be Dispatched</a></li>
                <li class="{{ $activeDropdown == 'to_be_collected_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('to_be_collected_posts') }}">To be Collected</a></li>
                
                <li class="{{ $activeDropdown == 'forwarded_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_forwarded_post') }}">Forwarded Posts</a></li>
                <li class="{{ $activeDropdown == 'sent_forwarded_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_sent_forwarded_post') }}">Sent</a></li>

              </ul>
            </li>
      
        <li class="menu-header">Ticketing</li>
        <li class="dropdown {{ $activeMenu == 'tickets' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i> <span>Tickets Recieved </span></a>
          <ul class="dropdown-menu">
            
            <li class="{{ $activeDropdown == 'summary' ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('super_admin_recieved_tickets_summary') }}">Summary</a>
            </li>
            
            <li class="{{ $activeDropdown == 'unassigned_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $unassigned_received_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_recieved_unassigned_tickets') }}">New Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'open_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $open_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_recieved_open_tickets') }}">Open Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'in_progress_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $in_progress_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_recieved_tickets_in_progress') }}">In Progress Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'hold_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $hold_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_recieved_tickets_on_hold') }}">Hold Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'completed_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $completed_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_recieved_completed_tickets') }}">Completed Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'self_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $self_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_recieved_self_tickets') }}">Self Assigned Tickets</a>
            </li>

          </ul>
        </li>

        <li class="dropdown {{ $activeMenu == 'tickets_raised' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-regular fa-folder-open"></i> <span>Tickets Raised </span></a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'unapproved_tickets' ? 'active' : '' }}">
             <a class="nav-link {{ $unapproved_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_unapproved_tickets') }}">Unapproved Tickets</a>
            </li>

            <li class="{{ $activeDropdown == 'my_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $my_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_my_tickets') }}">My Tickets</a>
            </li>
            
            <li class="{{ $activeDropdown == 'staff_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $dept_staff_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('super_admin_dept_tickets') }}">Tickets by Dept Staffs</a>
            </li>

            <li class="{{ $activeDropdown == 'create_ticket' ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('super_admin_create_ticket') }}">Create a Ticket</a>
            </li>

          </ul>

        <li class="{{ $activeMenu == 'forwarded_tickets' ? 'active' : '' }}">
          <a href="{{ route('superadmin-forwarded-ticket') }}" class="nav-link {{ $forwarded_tickets_count > 0? 'beep beep-sidebar' : '' }}">
            <i class="fas fa-columns"></i><span>Forwarded Tickets</span>
          </a>
        </li>

        <li class="{{ $activeMenu == 'reports' ? 'active' : '' }}">
          <a href="{{ route('super-admin-dept-ticket-report') }}" class="nav-link"><i class="far fa-file-alt"></i><span>Report</span></a>
        </li>
        <li class="{{ $activeMenu == 'approval-report' ? 'active' : '' }}">
          <a href="{{ route('approval-report') }}" class="nav-link"><i class="fas fa-file-signature"></i><span>Approval Report</span></a>
        </li>

        </li>

        <li class="menu-header">Inter-Dept Tickets</li>
        <li class="dropdown {{ $activeMenu == 'sa_tickets' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-pencil-ruler"></i> <span>Inter-Dept Tickets</span></a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'sa_summary' ? 'active' : '' }}"><a class="nav-link" href="{{ route('super_admin_tickets_summary') }}">Summary</a></li>
            @if($unassigned_tickets_count > 0)
            <li class="{{ $activeDropdown == 'sa_unassigned_tickets' ? 'active' : '' }}"><a class="nav-link beep beep-sidebar" href="{{ route('super_admin_unassigned_tickets') }}">New Tickets</a></li>
            @else
            <li class="{{ $activeDropdown == 'sa_unassigned_tickets' ? 'active' : '' }}"><a class="nav-link" href="{{ route('super_admin_unassigned_tickets') }}">New Tickets</a></li>
            @endif
            <li class="{{ $activeDropdown == 'sa_reports' ? 'active' : '' }}"><a class="nav-link" href="{{ route('super-admin-ticket-report') }}">Report</a></li>
          </ul>
        </li>
        
        <li class="menu-header">Staff Master</li>
        <li class="dropdown {{ $activeMenu == 'staffs' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>{{ Auth::user()->department }} Staffs</span></a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'add_staffs' ? 'active' : '' }}"><a href="{{ route('super-admin-add-staffs') }}">Add Staff</a></li> 
            <li class="{{ $activeDropdown == 'view_staffs' ? 'active' : '' }}"><a href="{{ route('super-admin-view-staffs') }}">View Staffs</a></li>
            <li class="{{ $activeDropdown == 'ex_staffs' ? 'active' : '' }}"><a href="{{ route('super-admin-ex-staffs') }}">Ex Staffs</a></li>  
          </ul>
        </li>

        @if(in_array(Auth::user()->role ?? '', ['ITAdmin', 'SuperAdmin']))
        <li class="menu-header" style="margin-top: 15px;">IT Administration</li>
        <li class="dropdown {{ $activeMenu == 'api' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
            <i class="fas fa-key"></i> <span>API Management</span>
          </a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'api_tokens' ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('api.tokens.index') }}">API Tokens</a>
            </li>
            <li class="{{ $activeDropdown == 'api_docs' ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('api.tokens.index') }}#tab-auth">Instructions</a>
            </li>
          </ul>
        </li>
        @endif

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