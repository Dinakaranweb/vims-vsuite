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
                  <div class="card-header"><h4>Forgot Password</h4></div>

                  <div class="card-body">
                    <p class="text-muted">We will send a link to reset your password</p>
                    <form method="POST" action="{{ route('forget.password.post') }}">
                        @csrf
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>

                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                          Forgot Password
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="mt-5 text-muted text-center">
                    Login in to your account? <a href="{{ route('login') }}">Click here!</a>
                  </div>
                  <div class="simple-footer">
                    Copyright &copy; VMRF(DU) IT Team 
                  </div>
              </div>
            </div>
          </div>
        </section>
    </div>
@endsection