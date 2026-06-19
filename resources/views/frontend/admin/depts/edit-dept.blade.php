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
            <h1>Edit Department</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Edit Department</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Edit Department</h2>
            
            <div class="row">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Edit {{ $emp->dept_label }}</h4>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('update-dept') }}">
                      @csrf
                      <div class="row">
                          <div class="form-group col-12 col-md-12 col-lg-6">
                              <label for="dept_name">Department Name</label>
                              <input type="text" class="form-control @error('dept_name') is-invalid @enderror" id="dept_name" name="dept_name" value="{{ old('dept_name', $emp->dept_name) }}">
                              @error('dept_name')
                                  <span class="invalid-feedback">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="form-group col-12 col-md-12 col-lg-6">
                              <label for="dept_label">Department Label</label>
                              <input type="text" class="form-control @error('dept_label') is-invalid @enderror" id="dept_label" name="dept_label" value="{{ old('dept_label', $emp->dept_label) }}">
                              @error('dept_label')
                                  <span class="invalid-feedback">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="form-group col-12 col-md-12 col-lg-6">
                              <label for="dept_id">Department ID</label>
                              <input type="text" class="form-control @error('dept_id') is-invalid @enderror" id="dept_id" name="dept_id" value="{{ old('dept_id', $emp->dept_id) }}">
                              @error('dept_id')
                                  <span class="invalid-feedback">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <input type="hidden" name="id" id="id" value="{{ $emp->id }}"> <!-- Presumably you want to keep the role as is -->
                      <div class="form-group col-12 col-md-6 col-lg-4" style="margin: 0 auto">
                          <button type="submit" class="btn btn-primary btn-lg btn-block">
                              Update Staff
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