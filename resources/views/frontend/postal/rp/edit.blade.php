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
                    <h1>Edit Post</h1>
                    <div class="section-header-breadcrumb">
                      <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                      <div class="breadcrumb-item"><a href="{{ route('admin_tickets_summary') }}">Post</a></div>
                      <div class="breadcrumb-item">Edit</div>
                    </div>
                  </div>

                  <div class="section-body">
                    <h2 class="section-title">Edit Post</h2>
                    <div class="row">
                      <div class="col-lg-8 col-md-12">
                        <div class="card">
                          <div class="card-header">
                            <h4>Post Generation</h4>
                          </div>
                          <div class="card-body">
                            <form action="{{ route('rp_add_track_id') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tracking ID</label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control" id="tracking_id" name="tracking_id" placeholder="Enter Tracking ID" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Vendor</label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Enter Vendor name" autocomplete="off">
                                    </div>
                                </div>
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                <input type="hidden" name="status" value="Received">
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                  <div class="col-sm-12 col-md-7">
                                    <button class="btn btn-primary">Update</button>
                                  </div>
                                </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-12">
                          <div class="row">
                            <div class="card col-12">
                              <div class="card-header">
                                <h4>Post Details</h4>
                              </div>
                                @php
                                    $by = App\Models\User::find($post->reply_by)->name;
                                @endphp
                                
                              <div class="card-body">
                                <b>
                                    <p> Post ID : {{ $post->post_id }} </p>
                                    <p> Reply From : {{ $by }}</p>
                                    <p> Reply To :{{ $post->reply_to }}</p>
                                    <p> Reply To Address :{!! $post->reply_to_address !!}</p>
                                    <p> Status : {{ $post->status }}</p>
                                </b>
                              </div>
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