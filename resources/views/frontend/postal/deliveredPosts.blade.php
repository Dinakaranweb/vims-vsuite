@extends('frontend.frontend_master')

@section('content')

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @if(Auth::user()->role == 'HOD')

                @include('frontend.admin.body.header')
                @include('frontend.admin.body.sidebar')

            @else

                @include('frontend.staff.body.header')
                @include('frontend.staff.body.sidebar')

            @endif

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                <div class="section-header">
                    <h1>Todays Post</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item">Postal Entries</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">Delivered Posts</h2>
                    <p class="section-lead">
                        Make sure the all the posts are Delivered.
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
                                                <th>Post ID</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Status</th>
                                                <th>Delivered to</th>
                                                <th>Received by</th>
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
                                                    <td>
                                                        {{ $post->post_id }}
                                                    </td>
                                                    <td>
                                                        <a href="" style="color: #1e1e1e">
                                                        {{ $post->sent_by }}</a>
                                                    </td>
                                                    @php
                                                        if($post->staff_name){
                                                            $to = App\Models\User::find($post->staff_name)->name .'-'.App\Models\User::find($post->staff_name)->department;
                                                        }else{
                                                            $to = $post->sent_to;
                                                        }
                                                    @endphp
                                                    <td>
                                                        <a class="font-weight-600">{{ $to }}</a>
                                                    </td>
                                                    
                                                    <td class="postal-status">
                                                        {{ $post->status }}
                                                    </td>
                                                    
                                                    <td class="postal-dispatched-to">
                                                        {{ $post->dispatched_to }}
                                                    </td>
                                                    @php
                                                        if($post->collected_by){
                                                            $collected_by = App\Models\User::find($post->collected_by)->name;
                                                        }else{
                                                            $collected_by = null;
                                                        }
                                                            
                                                    @endphp
                                                    @if($collected_by)
                                                        <td class="postal-dispatched-to"><div class="font-weight-600">{{ $collected_by }} </div></td>
                                                    @else
                                                        <td  class="postal-dispatched-by"><div class="font-weight-600"></div></td>
                                                    @endif
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
