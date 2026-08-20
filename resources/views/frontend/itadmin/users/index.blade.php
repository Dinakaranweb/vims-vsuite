@extends('frontend.frontend_master')
@section('content')

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        @include('frontend.superadmin.body.header')
        @include('frontend.superadmin.body.sidebar')

        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>User Management</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('itadmin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item">Users</div>
                    </div>
                </div>

                <div class="section-body">
                    @if(session('message'))
                        <div class="alert alert-{{ session('alert-type') == 'success' ? 'success' : 'danger' }}">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                        <h4>Users</h4>
                                        <a href="{{ route('itadmin.users.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus"></i> Add User
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('itadmin.users.index') }}" class="form-row align-items-end mb-4">
                                        <div class="form-group col-md-4">
                                            <label>Search</label>
                                            <input type="text" name="search" class="form-control" placeholder="Name, email, or department" value="{{ request('search') }}">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Role</label>
                                            <select name="role" class="form-control">
                                                <option value="">All Roles</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-primary btn-block">Search</button>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <a href="{{ route('itadmin.users.index') }}" class="btn btn-secondary btn-block">Reset</a>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Department</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>API Access</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($users as $user)
                                                    <tr>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->department }}</td>
                                                        <td><span class="badge badge-info">{{ $user->role }}</span></td>
                                                        <td>
                                                            @if($user->is_active)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($user->can_use_api)
                                                                <span class="badge badge-success">Enabled</span>
                                                            @else
                                                                <span class="badge badge-secondary">Disabled</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('itadmin.users.edit', $user->id) }}" class="btn btn-icon btn-info" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if($user->id !== auth()->id())
                                                                <form method="POST" action="{{ route('itadmin.users.destroy', $user->id) }}" class="d-inline"
                                                                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-icon btn-danger" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-center">No users found.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {!! $users->links('frontend.pagination.custom') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

@endsection
