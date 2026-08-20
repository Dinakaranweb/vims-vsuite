@extends('frontend.frontend_master')
@section('content')

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        @include('frontend.superadmin.body.header')
        @include('frontend.superadmin.body.sidebar')

        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Add User</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('itadmin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item"><a href="{{ route('itadmin.users.index') }}">Users</a></div>
                        <div class="breadcrumb-item">Add</div>
                    </div>
                </div>

                <div class="section-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-8 col-md-12">
                            <div class="card">
                                <div class="card-header"><h4>User Details</h4></div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('itadmin.users.store') }}">
                                        @csrf
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Name *</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Username *</label>
                                                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Email *</label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Department *</label>
                                                <div class="input-group">
                                                    <select name="department" id="department-select" class="form-control" required>
                                                        <option value="">-- Select Department --</option>
                                                        @foreach($departments as $dept)
                                                            <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary" id="add-department-btn" title="Add a new department not yet in the list">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">Departments are drawn from existing users. Use + to add one that isn't listed yet.</small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Role *</label>
                                                <select name="role" class="form-control" required>
                                                    <option value="">-- Select Role --</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Designation</label>
                                                <input type="text" name="designation" class="form-control" value="{{ old('designation') }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Division</label>
                                                <select name="division" class="form-control">
                                                    <option value="">-- Select Division --</option>
                                                    <option value="Clinical" {{ old('division') == 'Clinical' ? 'selected' : '' }}>Clinical</option>
                                                    <option value="Non Clinical" {{ old('division') == 'Non Clinical' ? 'selected' : '' }}>Non Clinical</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Employee ID</label>
                                                <input type="text" name="emp_id" class="form-control" value="{{ old('emp_id') }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Password *</label>
                                                <input type="password" name="password" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                                <label class="custom-control-label" for="is_active">Active</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="can_use_api" name="can_use_api" value="1">
                                                <label class="custom-control-label" for="can_use_api">Enable API integration for this user (mobile app / cross-system login)</label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Create User</button>
                                        <a href="{{ route('itadmin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-department-btn').addEventListener('click', function () {
        var name = prompt('New department name:');
        if (!name) return;
        name = name.trim();
        if (!name) return;

        var select = document.getElementById('department-select');
        var exists = Array.prototype.some.call(select.options, function (opt) {
            return opt.value.toLowerCase() === name.toLowerCase();
        });

        if (!exists) {
            var option = document.createElement('option');
            option.value = name;
            option.text = name;
            select.add(option);
        }

        select.value = name;
    });
</script>

@endsection
