@extends('frontend.frontend_master')

@section('content')
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      
      @if(Auth::user()->role == 'HOD')

        @include('frontend.admin.body.header')
        @include('frontend.admin.body.sidebar')

      @elseif(Auth::user()->role == 'SuperAdmin')

        @include('frontend.superadmin.body.header')
        @include('frontend.superadmin.body.sidebar')

      @else

        @include('frontend.staff.body.header')
        @include('frontend.staff.body.sidebar')

      @endif

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
              @if(Auth::user()->role == 'HOD')
                <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              @elseif(Auth::user()->role == 'SuperAdmin')
                <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
              @else
                <div class="breadcrumb-item active"><a href="{{ route('staff_dashboard') }}">Dashboard</a></div>
              @endif
              <div class="breadcrumb-item">Profile</div>
            </div>
          </div>
          <div class="section-body">
            <h2 class="section-title">Hi, {{ Auth::user()->name }}!</h2>
            <p class="section-lead">
              Change information about yourself on this page.
            </p>
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="POST" action="{{ route('update-profile') }}" class="needs-validation" novalidate="">
                    @csrf
                    <div class="card-header">
                      <h4>Edit Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">                               
                          <div class="form-group col-md-6 col-12">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required="">
                            <div class="invalid-feedback">
                              Please fill in the Name
                            </div>
                          </div>
                          <div class="form-group col-md-6 col-12">
                            <label>Employee ID</label>
                            <input type="text" class="form-control" name="emp_id" value="{{ Auth::user()->emp_id }}" required="">
                            <div class="invalid-feedback">
                              Please fill in your Emp ID
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-6 col-12">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required="">
                            <div class="invalid-feedback">
                              Please fill in the email
                            </div>
                          </div>
                          <div class="form-group col-md-6 col-12">
                            <label>Phone</label>
                            <input type="tel" class="form-control" name="phone" value="{{ Auth::user()->phone }}" required="">
                            <div class="invalid-feedback">
                              Please fill in your mobile
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-6 col-12">
                            <label>Department</label>
                            <input type="text" class="form-control" name="department" value="{{ Auth::user()->department }}" required="" disabled>
                            <div class="invalid-feedback">
                              Please fill in the email
                            </div>
                          </div>
                          <div class="form-group col-md-6 col-12">
                            <label>Role</label>
                            <input type="text" class="form-control" name="role" value="{{ Auth::user()->role }}" required="" disabled>
                            <div class="invalid-feedback">
                              Please fill in your mobile
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-6 col-12">
                            <label>Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                            <div class="invalid-feedback">
                              Please fill in your last name
                            </div>
                          </div>
                          <div class="form-group col-md-6 col-12">
                            <label>Confirm Password</label>
                            <input type="password" id="password2" name="password-confirm" class="form-control">
                            <div class="invalid-feedback">
                              Password mismatch
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                      <input type="submit" class="btn btn-primary" value="Update Profile">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      @include('frontend.body.footer')
    </div>
  </div>

  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const password = document.querySelector('#password');
          const passwordConfirm = document.querySelector('#password2');
          const form = document.querySelector('form');

          passwordConfirm.addEventListener('input', function() {
              const passwordValue = password.value;
              const passwordConfirmValue = this.value;

              if (passwordValue !== passwordConfirmValue) {
                  passwordConfirm.setCustomValidity('Passwords do not match.');
              } else {
                  passwordConfirm.setCustomValidity('');
              }
          });

          form.addEventListener('submit', function(event) {
              if (!form.checkValidity()) {
                  event.preventDefault();
                  event.stopPropagation();
              }
              form.classList.add('was-validated');
          });
      });
  </script>



@endsection