@extends('frontend.frontend_master')

@section('content')

    <div id="app">
      <section class="section">
        <div class="container mt-5">
          <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
              <div class="login-brand mb-4 text-center">
                  <img src="{{ asset('assets/img/vm/icon.jpg') }}" alt="logo" width="100" class="shadow-light rounded-circle mb-3">
                  <h2 class="vsuite-title mb-2">
                      <i class="fas fa-gem text-primary"></i>
                      V-Suite
                  </h2>
                  <div class="vsuite-divider mx-auto mb-2"></div>
                  <div class="vsuite-tagline text-muted mb-1">
                      VMRF-DU
                  </div>
              </div>

              <style>
                .vsuite-title {
                    font-family: 'Montserrat', 'Poppins', Arial, sans-serif;
                    font-weight: 800;
                    font-size: 2.2rem;
                    background: linear-gradient(90deg, #005bea 20%, #00c6fb 80%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    /* Remove or darken the text shadow for better visibility */
                    text-shadow: 0 2px 8px rgba(0,0,0,0.15);
                    letter-spacing: 2.5px;
                    display: inline-block;
                    /* Fallback color for unsupported browsers */
                    color: #005bea;
                }

                .vsuite-divider {
                    width: 80px;
                    height: 4px;
                    background: linear-gradient(90deg, #005bea, #00c6fb, #005bea);
                    border-radius: 2px;
                    box-shadow: 0 2px 8px rgba(0,123,255,0.10);
                }

                .vsuite-tagline {
                    font-size: 1.05rem;
                    letter-spacing: 1px;
                    font-family: 'Poppins', Arial, sans-serif;
                }
              </style>

              <div class="card card-primary">
                <div class="card-header"><h4>Login</h4></div>

                <div class="card-body">
                  <form method="POST" action="{{ route('auth_login') }}" class="needs-validation" novalidate="">
                    @csrf
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                      <div class="invalid-feedback">
                        Please fill in your email
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="d-block">
                        <label for="password" class="control-label">Password</label>
                        <div class="float-right">
                          <a href="{{ route('forget.password.get') }}" class="text-small">
                            Forgot Password?
                          </a>
                        </div>
                      </div>
                      <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                      <div class="invalid-feedback">
                        please fill in your password
                      </div>
                    </div>

                    <!-- <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                        <label class="custom-control-label" for="remember-me">Remember Me</label>
                      </div>
                    </div> -->

                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        Login
                      </button>
                    </div>
                  </form>
                </div>
              </div>
              <!--<div class="mt-5 text-muted text-center">-->
              <!-- For Documentation <a href="https://app.presentations.ai/view/gwM9ln" target="_blank">Click Here!</a>-->
              <!--</div>-->
              <div class="simple-footer">
                Copyright &copy; VMRF(DU) IT Team 
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
@endsection