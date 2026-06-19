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
          <a href="{{ route('admin_dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
        </li>
        <li class="{{ $activeMenu == 'fo_dashboard' ? 'active' : '' }}">
          @if(Auth::user()->department == 'Students Welfare')
            <a href="{{ route('fo_dashboard') }}" class="nav-link"><i class="fas fa-rupee-sign"></i><span>Finance Dashboard</span></a>
          @endif
        </li>
        <li class="menu-header">Approval/Requests</li>
            <li class="dropdown {{ $activeMenu == 'document' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-tags"></i> <span>Document Sent</span></a>
              <ul class="dropdown-menu">
                <li class="{{ $activeDropdown == 'create_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('create_document') }}">Create</a></li>
                <!-- <li class="{{ $activeDropdown == 'my_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('my_documents') }}">My Documents</a></li> -->
                <li class="{{ $activeDropdown == 'draft_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('draft_documents') }}">Drafts</a></li>
                <li class="{{ $activeDropdown == 'new_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('new_documents') }}">New Documents</a></li>
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
                <li class="{{ $activeDropdown == 'completed_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('completed_documents') }}">Completed Doc</a></li>
                <li class="{{ $activeDropdown == 'received_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('received_documents') }}">Sent to you</a></li>
              </ul>
            </li>
            @if(Auth::user()->department == 'ICT')
            <li class="{{ $activeMenu == 'report-doc' ? 'active' : '' }}">
              <a href="{{ route('report-doc') }}" class="nav-link"><i class="fas fa-bible"></i><span>IT Report</span></a>
            </li>
            <!--<li class="{{ $activeMenu == 'payment_details' ? 'active' : '' }}">-->
            <!--  <a href="{{ route('payment.details') }}" class="nav-link"><i class="fas fa-bible"></i><span>Payment Details</span></a>-->
            <!--</li>-->
            @endif
            @if(Auth::user()->department == 'Students Welfare')
            <li class="{{ $activeMenu == 'payment_details' ? 'active' : '' }}">
              <a href="{{ route('payment.details') }}" class="nav-link"><i class="fas fa-bible"></i><span>Payment Details</span></a>
            </li>
            @endif
        <li class="menu-header">Postal</li>
        @if(Auth::user()->department == 'Postal')
            <li class="dropdown {{ $activeMenu == 'postal' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-teamspeak"></i> <span>Posts Received</span></a>
              <ul class="dropdown-menu">
                <li class="{{ $activeDropdown == 'create_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_entry') }}">Create Post</a></li>
                <li class="{{ $activeDropdown == 'received_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('received_posts') }}">Received</a></li>
                <li class="{{ $activeDropdown == 'delivered_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('delivered_posts') }}">Delivered</a></li>
                <!-- <li class="{{ $activeDropdown == 'entries' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_entries') }}">Postal Entries</a></li> -->
                <li class="{{ $activeDropdown == 'outgoing' ? 'active' : '' }}"><a class="nav-link" href="{{ route('outgoing_postal_entries') }}">Outgoing Posts</a></li>
                <li class="{{ $activeDropdown == 'incoming_report' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_report') }}">Report</a></li>
              </ul>
            </li>
        @elseif(Auth::user()->department == 'Registrar Office')
            <li class="dropdown {{ $activeMenu == 'postal' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-teamspeak"></i> <span>Posts Received</span></a>
              <ul class="dropdown-menu">
                <li class="{{ $activeDropdown == 'create_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_entry') }}">Create Post</a></li>
                <li class="{{ $activeDropdown == 'entries' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_entries') }}">Postal Entries</a></li>
              </ul>
            </li>
        @else
            <li class="dropdown {{ $activeMenu == 'postal' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-swatchbook"></i> <span>Posts</span></a>
              <ul class="dropdown-menu">
                <!-- <li class="{{ $activeDropdown == 'create_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_entry') }}">Create Post</a></li> -->
                <li class="{{ $activeDropdown == 'dispatched_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('dispatched_posts') }}">Dispatched</a></li>
                <li class="{{ $activeDropdown == 'dept_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('collected_posts') }}">Received</a></li>
                <li class="{{ $activeDropdown == 'search_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('search-posts') }}">Search Posts</a></li>
                @if(Auth::user()->department == 'DDE Examination' || Auth::user()->department == 'DDE Admission & Finance' || Auth::user()->role == 'SuperAdmin')
                <li class="{{ $activeDropdown == 'dde_details' ? 'active' : '' }}"><a class="nav-link" href="{{ route('dde_details.index') }}">DDE Details</a></li>
                @endif
                <li class="{{ $activeDropdown == 'personal_posts' ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_personal_post') }}">My Posts</a></li>
                
                <li class="{{ $activeDropdown == 'out_post' ? 'active' : '' }}"><a class="nav-link" href="{{ route('postal_add_out_entry') }}">Create Out Posts</a></li>
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
        @endif
        <li class="menu-header">Ticketing</li>
        @if(Auth::user()->department == 'ICT'|| Auth::user()->department == 'Property Management' || Auth::user()->department == 'Vehicle')
            <li class="dropdown {{ $activeMenu == 'tickets_received' ? 'active' : '' }}">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i> <span>Tickets Received</span></a>
              <ul class="dropdown-menu">
                
                <li class="{{ $activeDropdown == 'summary' ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_tickets_summary') }}">Summary</a></li>
                
                <li class="{{ $activeDropdown == 'unassigned_tickets' ? 'active' : '' }}">
                  <a class="nav-link {{ $unassigned_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_unassigned_tickets') }}">New Tickets</a>
                </li>
                
                <li class="{{ $activeDropdown == 'open_tickets' ? 'active' : '' }}">
                  <a class="nav-link {{ $open_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_open_tickets') }}">Open Tickets</a>
                </li>
                
                <li class="{{ $activeDropdown == 'in_progress_tickets' ? 'active' : '' }}">
                  <a class="nav-link {{ $in_progress_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_tickets_in_progress') }}">In Progress Tickets</a>
                </li>
                
                <li class="{{ $activeDropdown == 'hold_tickets' ? 'active' : '' }}">
                  <a class="nav-link {{ $hold_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_tickets_on_hold') }}">Hold Tickets</a>
                </li>
                
                <li class="{{ $activeDropdown == 'completed_tickets' ? 'active' : '' }}">
                  <a class="nav-link {{ $completed_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_completed_tickets') }}">Completed Tickets</a>
                </li>
                
                <li class="{{ $activeDropdown == 'self_tickets' ? 'active' : '' }}">
                  <a class="nav-link {{ $self_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_self_tickets') }}">Self Assigned Tickets</a>
                </li>
                
              </ul>
            </li>
        @endif
        <li class="dropdown {{ $activeMenu == 'tickets_raised' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-regular fa-folder-open"></i> <span>Ticket Raised</span></a>
          <ul class="dropdown-menu">
            
            <li class="{{ $activeDropdown == 'unapproved_tickets' ? 'active' : '' }}">
             <a class="nav-link {{ $unapproved_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_unapproved_tickets') }}">Un Approved Tickets</a>
            </li>
            
            <li class="{{ $activeDropdown == 'my_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $my_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_my_tickets') }}">My Tickets</a>
            </li>
            
            <li class="{{ $activeDropdown == 'staff_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $dept_staff_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin_dept_tickets') }}">Tickets by Dept Staffs</a>
            </li>
            
            <li class="{{ $activeDropdown == 'create_ticket' ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_create_ticket') }}">Create a Ticket</a></li>
          </ul>
        </li>
        @if(Auth::user()->department == 'ICT'|| Auth::user()->department == 'Property Management' || Auth::user()->department == 'Vehicle' || Auth::user()->department == 'Purchase')
            <li class="{{ $activeMenu == 'forwarded_tickets' ? 'active' : '' }}">
              <a class="nav-link {{ $forwarded_tickets_count > 0? 'beep beep-sidebar' : '' }}" href="{{ route('admin-forwarded-report') }}" >
                <i class="fas fa-columns"></i><span>Forwarded Tickets</span>
              </a>
            </li>
        @endif
        <li class="{{ $activeMenu == 'reports' ? 'active' : '' }}">
          <a href="{{ route('admin-ticket-report') }}" class="nav-link"><i class="far fa-file-alt"></i><span>Report</span></a>
        </li>
        <li class="{{ $activeMenu == 'approval-report' ? 'active' : '' }}">
          <a href="{{ route('approval-report') }}" class="nav-link"><i class="fas fa-file-signature"></i><span>Approval Report</span></a>
        </li>
        
        <li class="menu-header">Staff Master</li>
        <li class="dropdown {{ $activeMenu == 'staffs' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>{{ Auth::user()->department }} Staffs</span></a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'add_staffs' ? 'active' : '' }}"><a href="{{ route('add-staffs') }}">Add Staff</a></li> 
            <li class="{{ $activeDropdown == 'view_staffs' ? 'active' : '' }}"><a href="{{ route('view-staffs') }}">View Staffs</a></li>
            <li class="{{ $activeDropdown == 'ex_staffs' ? 'active' : '' }}"><a href="{{ route('ex-staffs') }}">Ex Staffs</a></li>  
          </ul>
        </li>

        @if(Auth::user()->department == 'ICT')
        <li class="menu-header">Department Master</li>
        <li class="dropdown {{ $activeMenu == 'staffs' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Departments</span></a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'add_depts' ? 'active' : '' }}"><a href="{{ route('add-depts') }}">Add Dept</a></li> 
            <li class="{{ $activeDropdown == 'view_depts' ? 'active' : '' }}"><a href="{{ route('view-depts') }}">View Depts</a></li>
            <li class="{{ $activeDropdown == 'ex_depts' ? 'active' : '' }}"><a href="{{ route('ex-depts') }}">Ex Dept</a></li>  
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

      <!-- <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="{{ route('signature-demo') }}" class="btn btn-warning btn-lg btn-block btn-icon-split">
          <i class="fas fa-rocket"></i> Signature Demo
        </a>
      </div> -->

      <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="{{ route('user-manual') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
          <i class="fas fa-rocket"></i> User Guide
        </a>
      </div>

    </aside>
</div>