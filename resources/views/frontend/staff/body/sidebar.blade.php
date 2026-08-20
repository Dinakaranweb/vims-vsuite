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
        <li class="menu-header">Approval/Requests</li>
        <li class="dropdown {{ $activeMenu == 'document' ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-tags"></i> <span>Document Sent</span></a>
          <ul class="dropdown-menu">
            <li class="{{ $activeDropdown == 'create_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('create_document') }}">Create</a></li>
            <li class="{{ $activeDropdown == 'draft_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('draft_documents') }}">Drafts</a></li>
            <li class="{{ $activeDropdown == 'new_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('new_documents') }}">New Documents</a></li>
            <li class="{{ $activeDropdown == 'inProgress_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('inProgress_documents') }}">In Progress</a></li>
            <li class="{{ $activeDropdown == 'approved_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('approved_documents') }}">Approved</a></li>
            <li class="{{ $activeDropdown == 'rejected_doc' ? 'active' : '' }}"><a class="nav-link" href="{{ route('rejected_documents') }}">Rejected</a></li>
          </ul>
        </li>
      </ul>

      <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="{{ route('user-manual') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
          <i class="fas fa-rocket"></i> User Guide
        </a>
      </div>

    </aside>
  </div>