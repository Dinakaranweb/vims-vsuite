@extends('frontend.frontend_master')

@section('content')

    <div id="app">
      <section class="section">
        <div class="container mt-5">
          <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
              <div class="login-brand">
                <img src="{{ asset('assets/img/vm/icon.jpg') }}" alt="logo" width="100" class="shadow-light rounded-circle">
              </div>

              <div class="card card-primary">
                <div class="card-header"><h4>Register</h4></div>

                <div class="card-body">
                  <form method="POST" action="{{ route('auth_register_user') }}">
                    @csrf
                    <div class="row">
                      <div class="form-group col-6">
                        <label for="name">Full Name</label>
                        <input id="name" type="text" class="form-control" name="name" autofocus>
                      </div>
                      <div class="form-group col-6">
                        <label for="username">Username</label>
                        <input id="username" type="text" class="form-control" name="username">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-6">
                        <label for="department">Department</label>
                        <select class="form-control selectric" name="department" required>
                          @foreach ($departments as $department)
                            <option value="{{ $department->dept_label }}">{{ $department->dept_label }}</option>
                          @endforeach
                        </select>
                        <!-- <input id="department" type="text" class="form-control" name="department" autofocus> -->
                      </div>
                      <div class="form-group col-6">
                        <label for="role">Role</label>
                        <select class="form-control selectric" name="role" required>
                            <option value="Staff">Staff</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="email">Email</label>
                      <input id="email" type="email" class="form-control" name="email">
                      <div class="invalid-feedback">
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-6">
                        <label for="password" class="d-block">Password</label>
                        <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password">
                        <div id="pwindicator" class="pwindicator">
                          <div class="bar"></div>
                          <div class="label"></div>
                        </div>
                      </div>
                      <div class="form-group col-6">
                        <label for="password2" class="d-block">Password Confirmation</label>
                        <input id="password2" type="password" class="form-control" name="password-confirm">
                        <div class="invalid-feedback">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Register
                      </button>
                    </div>
                  </form>
                </div>
              </div>
              <div class="simple-footer">
                Copyright &copy; VMRF(DU) IT Team 
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <script>
      const passwordConfirm = document.querySelector('#password2');
      passwordConfirm.addEventListener('input', function() {
        const password = document.querySelector('#password').value;
        const passwordConfirmValue = this.value;

        if (password !== passwordConfirmValue) {
          // Show an error message
          const errorMessage = this.nextElementSibling;
          errorMessage.textContent = 'Passwords do not match.';
          errorMessage.style.display = 'block';

          // Prevent form submission
          form.onsubmit = function(event) {
            event.preventDefault();
          };
        } else {
          // Hide the error message
          const errorMessage = this.nextElementSibling;
          errorMessage.textContent = '';
          errorMessage.style.display = 'none';

          // Allow form submission
          form.onsubmit = function() {
            return true;
          };
        }
      });

      const form = document.querySelector('form');
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        const password = document.querySelector('#password').value;
        const passwordConfirm = document.querySelector('#password2').value;

        if (password !== passwordConfirm) {
          // Show an error message
          const errorMessage = document.querySelector('#password2 + .invalid-feedback');
          errorMessage.textContent = 'Passwords do not match.';
          errorMessage.style.display = 'block';

          // Prevent form submission
          return false;
        } else {
          // Hide the error message
          const errorMessage = document.querySelector('#password2 + .invalid-feedback');
          errorMessage.textContent = '';
          errorMessage.style.display = 'none';
        }

        // Submit the form
        form.submit();
      });
    </script>

@endsection