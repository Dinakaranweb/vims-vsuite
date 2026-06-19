@extends('frontend.frontend_master')

@section('content')

    <div id="app">
      <section class="section">
        <div class="container mt-5">
          <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
              <div class="login-brand">
                <img src="{{ asset('assets/img/vm/icon.jpg') }}" alt="logo" width="100" class="shadow-light rounded-circle">
              </div>

              <div class="card card-primary">
                  <div class="card-header"><h4>Reset Password</h4></div>

                  <div class="card-body">
                    <p class="text-muted">Please type in your new password</p>
                    <form method="POST" action="{{ route('reset.password.post') }}">
                        @csrf
                      <input type="hidden" name="token" value="{{ $token }}">
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                      </div>

                      <div class="form-group">
                        <label for="password">New Password</label>
                        <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" tabindex="2" required>
                        <div id="pwindicator" class="pwindicator">
                          <div class="bar"></div>
                          <div class="label"></div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="password2">Confirm Password</label>
                        <input id="password2" type="password" class="form-control" name="password2" tabindex="2" required>
                        <div class="invalid-feedback">
                        </div>
                      </div>

                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                          Reset Password
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