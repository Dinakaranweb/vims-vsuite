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
          @if(Auth::user()->role === 'ITAdmin')
            <a href="{{ route('itadmin_dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
          @else
            <a href="{{ route('super_admin_dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
          @endif
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

      </ul>

      <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="{{ route('user-manual') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
          <i class="fas fa-rocket"></i> User Guide
        </a>
      </div>

    </aside>
  </div>