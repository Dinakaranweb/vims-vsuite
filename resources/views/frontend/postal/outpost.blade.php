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
            <h1>Create Out Post</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin_tickets_summary') }}">Post</a></div>
              <div class="breadcrumb-item">Create</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Create Post</h2>
            <!-- <p class="section-lead">WYSIWYG editor and code editor.</p> -->

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Post Generation</h4>
                  </div>
                  <div class="card-body">
                    <form action="{{ route('postal_store_out_entry') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">To</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="to" class="form-control" required>
                          </div>
                        </div>
                          
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">To address</label>
                          <div class="col-sm-12 col-md-7">
                            <textarea name="to_address" class="summernote-simple" required> 
                            </textarea>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">By</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="by" class="form-control" value="{{ Auth::user()->name }}" required>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">From address</label>
                          <div class="col-sm-12 col-md-7">
                            <textarea name="from_address" class="summernote-simple" required>
                                <b>Vinayaka Missions Reseach Foundation</b>, <br>NH 47, Sankari Main Road,<br> Salem, Tamil Nadu 636308
                            </textarea>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Subject</label>
                          <div class="col-sm-12 col-md-7">
                            <textarea name="subject" class="summernote-simple" required>
                                <b>Sub :</b> 
                            </textarea>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Date</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="received_date" class="form-control datepicker" required>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Choose file</label>
                          <div class="col-sm-12 col-md-7">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="fileInput">
                                    <label class="custom-file-label" for="fileInput">Choose file</label>
                                </div>
                           </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Preferred Post Type</label>
                          <div class="col-sm-12 col-md-7">
                            <select class="form-control selectric" name="type" required>
                                <option value="Post">Post</option>
                                <option value="Courier">Courier</option>
                                <option value="Speed Post">Speed Post</option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                          <div class="col-sm-12 col-md-7">
                            <button class="btn btn-primary">Create</button>
                          </div>
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
@include('frontend.postal.script')