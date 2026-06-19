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
            <h1>Add Staffs</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Add Staff</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Add Staff</h2>
            <p class="section-lead" style="font-weight: 600; color:red">Default password for the all new user is 12345678</p>

            <div class="row">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Add User</h4>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('store-staffs') }}">
                      @csrf
                      <div class="row">
                        <div class="form-group col-12 col-md-12 col-lg-6">
                          <label for="full_name">Full Name</label>
                          <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}">
                          @error('full_name')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                        </div>
                        <div class="form-group col-12 col-md-12 col-lg-6">
                          <label for="user_name">User Name</label>
                          <input type="text" class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" value="{{ old('user_name') }}">
                          @error('user_name')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-12 col-md-12 col-lg-6">
                          <label for="emp_id">Employee ID</label>
                          <input type="text" class="form-control @error('emp_id') is-invalid @enderror" id="emp_id" name="emp_id" value="{{ old('emp_id') }}">
                          @error('emp_id')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                        </div>
                        <div class="form-group col-12 col-md-12 col-lg-6">
                          <label for="designation">Designation</label>
                          <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation') }}">
                          @error('designation')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-12 col-md-12 col-lg-6">
                          <label for="phone">Phone</label>
                          <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                          @error('phone')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                        </div>
                        <div class="form-group col-12 col-md-12 col-lg-6">
                          <label for="email">Email</label>
                          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                          @error('email')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                        </div>
                      </div>
                      <input type="hidden" name="department" id="department" value="{{ Auth::user()->department }}">
                      <input type="hidden" name="role" id="role" value="Staff">
                      <div class="form-group col-12 col-md-6 col-lg-4" style="margin: 0 auto">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                          Add Staff
                        </button>
                      </div>
                    </form>
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