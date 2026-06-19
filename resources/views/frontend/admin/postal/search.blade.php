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
                    <h1>Search Posts</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                        <div class="breadcrumb-item">Search Posts</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">Search Posts</h2>
                    <p class="section-lead">Search for posts by subject, sender, post ID, received date, or category.</p>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('search-posts') }}" class="mb-4">
                        <div class="form-row">
                            <div class="col-md-3">
                                <input type="text" name="subject" class="form-control" placeholder="Subject" value="{{ request('subject') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="sent_by" class="form-control" placeholder="Sent By" value="{{ request('sent_by') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="post_id" class="form-control" placeholder="Post ID" value="{{ request('post_id') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="received_date" class="form-control" value="{{ request('received_date') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="category" class="form-control" placeholder="Category" value="{{ request('category') }}">
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-md-3">
                                <input type="text" name="registrar_id" class="form-control" placeholder="Registrar ID" value="{{ request('registrar_id') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>

                    <!-- Search Results -->
                    <div class="row">
                        @forelse($posts as $post)
                            <div class="col-12 col-md-4 col-lg-4">
                                <article class="article article-style-c" style="box-shadow:0 4px 8px rgb(0 0 0 / 43%)">
                                    <div class="article-details">
                                        <div class="article-category">{{ $post->type }} <div class="bullet"></div> {{ $post->created_at->diffForHumans() }}</div>
                                        <div class="article-title">
                                            <h2><a href="{{ route('view-post', ['postal_id' => $post->id]) }}">{!! $post->subject !!}</a></h2>
                                        </div>
                                        <div class="article-user">
                                            <div class="article-user-details">
                                                <div class="user-detail-name">
                                                    <h5><a href="{{ route('view-post', ['postal_id' => $post->id]) }}">{{ $post->sent_by }}</a></h5>
                                                </div>
                                                <div class="text-job">{!! $post->post_from_address !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No posts found matching the search criteria.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
        @include('frontend.body.footer')
    </div>
</div>
@endsection