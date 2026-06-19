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
                    <h1>Todays Post</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item">Postals Sents</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">Posts sent</h2>
                    <p class="section-lead">
                        You have forwarded the following posts.
                    </p>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                <h4>Forwarded Posts</h4>
                                </div>
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-1" style="font-size: 13px; text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg ID</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Dispatched by</th>
                                                <th>Collected by</th>
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
                                                        $postal = App\Models\Postal::find($post->post_id);
                                                        $reg_id = $postal ? $postal->registrar_id : null;
                                                        $sent_by = $postal ? $postal->sent_by : null;
                                                    @endphp
                                                    <td>
                                                        <a href="" style="color: #1e1e1e">
                                                        {{ $reg_id }}</a>
                                                    </td>
                                                    <td>
                                                        <a href="" style="color: #1e1e1e">
                                                        {{ $sent_by }}</a>
                                                    </td>
                                                    <td>
                                                        <a class="font-weight-600">{{ $post->forwarded_to }}</a>
                                                    </td>
                                                    
                                                    @php
                                                        if($post->dispatched_by){
                                                            $dispatched_by = App\Models\User::find($post->dispatched_by)->name;
                                                        }else{
                                                            $dispatched_by = null;
                                                        }
                                                            
                                                    @endphp
                                                    @if($dispatched_by)
                                                        <td class="postal-dispatched-by"><div class="font-weight-600">{{ $dispatched_by }} </div></td>
                                                    @else
                                                        <td  class="postal-dispatched-by"><div class="font-weight-600"></div></td>
                                                    @endif
                                                    @php
                                                        if($post->collected_by){
                                                            $collected_by = App\Models\User::find($post->collected_by)->name;
                                                        }else{
                                                            $collected_by = null;
                                                        }
                                                    @endphp
                                                    @if($collected_by)
                                                        <td class="postal-collected-by"><div class="font-weight-600">{{ $collected_by }} </div></td>
                                                    @else
                                                        <td class="postal-collected-by"><div class="font-weight-600"></div></td>
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
