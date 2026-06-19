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
              <div class="breadcrumb-item">Manual</div>
            </div>
          </div>
          <div class="section-body">
            <h2 class="section-title">Hi, {{ Auth::user()->name }}!</h2>
            <p class="section-lead">
              Here is your user guide manual for this application
            </p>
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  @if(Auth::user()->role == 'HOD')
                  <iframe src="https://docs.google.com/viewer?url=https://drive.google.com/uc?id=1KaVo14UtwhNvqQxBenwdYPW-_8ZkBEkR&embedded=true" frameborder="0" height="500px" width="100%"></iframe>
                  @elseif(Auth::user()->role == 'Staff')
                  <iframe src="https://docs.google.com/viewer?url=https://drive.google.com/uc?id=1C4qU5PBCypOSxh5s9qKj0NJn-tegQpxz&embedded=true" frameborder="0" height="500px" width="100%"></iframe>
                  @else
                  <iframe src="https://docs.google.com/viewer?url=https://drive.google.com/uc?id=1n7Hz_5vN6Z_Zrq5IJ1uoeXAvEwwObRYs&embedded=true" frameborder="0" height="500px" width="100%"></iframe>
                  @endif
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