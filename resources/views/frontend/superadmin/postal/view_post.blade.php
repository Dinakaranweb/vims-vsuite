@extends('frontend.frontend_master')

@section('content')

    <div id="app">
        <div class="main-wrapper main-wrapper-1">            
                
                @include('frontend.superadmin.body.header')
        
                @include('frontend.superadmin.body.sidebar')

        <!-- Main Content -->
        <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{!! $post->subject !!}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin_dept_post') }}">Post</a></div>
              <div class="breadcrumb-item">View</div>
            </div>
          </div>

          <div class="section-body">
            <!-- <h2 class="section-title">{!! $post->subject !!}</h2> -->
            <!-- <p class="section-lead">WYSIWYG editor and code editor.</p> -->

            <div class="row">
              <div class="col-lg-8 col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Scanned Copy</h4>
                  </div>
                  @if($post->scanned_copy == null)
                    <div class="card-body">
                        Scanned Copy is not available yet
                    </div>
                  @else
                    <div class="card-body">
                        @php
                            $scanned_copy = 'https://vmrfdu.edu.in/img/VMC-header.jpg';
                        @endphp
                        <iframe src="{{ $scanned_copy }}" frameborder="0" height="980px" width="100%"></iframe>
                    </div>
                  @endif
                  
                </div>
              </div>
              <div class="col-lg-4 col-md-12">
                
                <div class="row">
                  <div class="card col-12">
                    <div class="card-header">
                      <h4>Post Details</h4>
                    </div>
                    <div class="card-body">
                    <div class="collapse show" id="mycard-collapse1">
                          <div class="card-body">
                            <p><b>Post ID :</b> {{ $post->post_id }}</p>
                            <p><b>Sent by :</b> {{ $post->sent_by }}</p>
                            <p><b>From :</b> {!! $post->post_from_address !!}</p>
                            <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($post->created_at)) }}</p>
                            <p><a href="{{ route('reply-post' ,['post_id' => $post->id]) }}"><button class="btn btn-primary">Reply ?</button></a></p>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
                @if($post->is_responded)
                @foreach($rps as $rp)
                  <div class="row">
                    <div class="card col-12">
                      <div class="card-header">
                        <h4>Reply Post</h4>
                      </div>
                      <div class="card-body">
                      <div class="collapse show" id="mycard-collapse1">
                            <div class="card-body">
                              <p><b>Post ID :</b> {{ $rp->post_id }}</p>
                              <p><b>Post :</b> {{ $rp->scanned_copy }}</p>
                              
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                @endforeach
                @endif
                <div class="row">
                  <div class="card col-12">
                    <div class="card-header">
                        <h4>Postal Log</h4>
                        <div class="card-header-action">
                        <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                        </div>
                    </div>
                    <div class="collapse show" id="mycard-collapse">
                        <div class="card-body">
                        <div class="col-12">
                            <div class="activities">
                            @foreach($logs as $entry)
                                <div class="activity">
                                  <div class="activity-icon bg-primary text-white shadow-primary">
                                      <i class="fas fa-comment-alt" style="margin-top:19px"></i>
                                  </div>
                                  <div class="activity-detail">
                                      <div class="mb-2">
                                      <span class="text-job text-primary">{{ $entry->created_at }}</span>
                                      <span class="bullet"></span>
                                      </div>
                                      <p>{!! $entry->description !!}</p>
                                  </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        </div>
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