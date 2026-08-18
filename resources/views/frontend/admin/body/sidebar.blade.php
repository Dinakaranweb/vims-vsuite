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