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
            <h1>Postals</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Postals</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Forwarded</h2>
            <p class="section-lead">Posts recieved for this month</p>

            <!-- Buttons for Date Filters -->
            <div class="mb-4">
              <a href="{{ route('forwarded-posts', ['date' => now()->toDateString()]) }}" class="btn btn-primary">Today's Posts</a>
              <a href="{{ route('forwarded-posts', ['date' => now()->subDay()->toDateString()]) }}" class="btn btn-secondary">Yesterday's Posts</a>
              <a href="{{ route('forwarded-posts', ['month' => now()->format('Y-m')]) }}" class="btn btn-info">This Month's Posts</a>
            </div>

            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('forwarded-posts') }}" class="mb-4">
              <div class="form-row">
                <div class="col-md-4">
                  <label for="date_from">Date From</label>
                  <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4">
                  <label for="date_to">Date To</label>
                  <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 align-self-end">
                  <button type="submit" class="btn btn-primary">Filter</button>
                </div>
              </div>
            </form>
            
            <div class="row">
              @foreach($posts as $post_id)
              @php
                $post = App\Models\Postal::find($post_id->post_id);
              @endphp
                @if($post)
                <div class="col-12 col-md-4 col-lg-4">
                  <article class="article article-style-c {{ $post_id->is_read == false ? 'unread' : '' }}" style="box-shadow:0 4px 8px rgb(0 0 0 / 43%)">
                    <div class="article-details">
                      <div class="article-category">{{ $post->type }} <div class="bullet"></div> {{ $post->created_at->diffForHumans() }}</div>
                      <div class="article-title">
                        <h2><a href="{{ route('view-forward-post' ,['postal_id' => $post->id]) }}">{!! $post->subject !!}</a></h2>
                      </div>
                      <div class="article-user">
                        <div class="article-user-details">
                          <div class="user-detail-name">
                            <h5><a href="{{ route('view-forward-post' ,['postal_id' => $post->id]) }}">{{ $post->sent_by }}</a></h5>
                          </div>
                          <div class="text-job">{!! $post->post_from_address !!}</div>
                        </div>
                      </div>
                    </div>
                  </article>
                </div>
                @endif
              @endforeach
            </div>
          </div>
        </section>
      </div>
      @include('frontend.body.footer')
    </div>
  </div>
@endsection