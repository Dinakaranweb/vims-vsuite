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
            <h1>Postals</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Postals</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Received</h2>
            <p class="section-lead">Posts recieved for this month</p>
            
            <div class="row">
              @foreach($posts as $post)
                <div class="col-12 col-md-4 col-lg-4">
                  <article class="article article-style-c {{ $post->is_read == false ? 'unread' : '' }}" style="box-shadow:0 4px 8px rgb(0 0 0 / 43%)">
                    <div class="article-details">
                      <div class="article-category">{{ $post->type }} <div class="bullet"></div> {{ $post->created_at->diffForHumans() }}</div>
                      <div class="article-title">
                        <h2><a href="{{ route('view-post' ,['postal_id' => $post->id]) }}">{!! $post->subject !!}</a></h2>
                      </div>
                      <div class="article-user">
                        <div class="article-user-details">
                          <div class="user-detail-name">
                            {{ $post->sent_by }}
                          </div>
                          <div class="text-job">{!! $post->post_from_address !!}</div>
                        </div>
                      </div>
                    </div>
                  </article>
                </div>
              @endforeach
            </div>
          </div>
        </section>
      </div>
      @include('frontend.body.footer')
    </div>
  </div>
@endsection