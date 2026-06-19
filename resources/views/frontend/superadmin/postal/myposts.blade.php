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
                    <h1>Todays Post</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item">My Posts</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">My posts</h2>
                    <p class="section-lead">
                        Make sure the all the posts are closed.
                    </p>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                <h4>Todays Posts</h4>
                                </div>
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-1" style="font-size: 13px; text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Status</th>
                                                <th>Vendor</th>
                                                <th>Tracking ID</th>
                                                <th>Created at</th>
                                                <th style="width:13%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            @php
                                                $i = 1;
                                            @endphp
                                            @foreach($postals as $post)
                                                <tr data-id="{{ $post->id }}">
                                                    <td>
                                                        {{ $i++ }}
                                                    </td>
                                                    @php
                                                        $by = App\Models\User::find($post->reply_by)->name .'-'.App\Models\User::find($post->reply_by)->department;
                                                    @endphp
                                                    
                                                    <td>
                                                        <a href="" style="color: #1e1e1e">
                                                        {{ $by }}</a>
                                                    </td>
                                                    <td>
                                                        <a class="font-weight-600">{{ $post->reply_to }}</a>
                                                    </td>
                                                    
                                                    <td>
                                                        {{ $post->status }}
                                                    </td>

                                                    <td>
                                                        {{ $post->vendor }}
                                                    </td>

                                                    <td>
                                                        {{ $post->tracking_id }}
                                                    </td>
                                                    <td>
                                                        {{ date('d/m/Y h:i A', strtotime($post->created_at)) }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('outPost_trackingID' ,['rp_id' => $post->id]) }}" class="btn btn-primary btn-action" data-toggle="tooltip" title="Edit"><i class="fas fa-solid fa-bell"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
