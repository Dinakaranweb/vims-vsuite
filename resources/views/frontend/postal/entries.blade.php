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
                    <h2 class="section-title">Postal Entries</h2>
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
                                                <th>Delivered to</th>
                                                <th>Delivered by</th>
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
                                                    
                                                    <td>
                                                        <div class="btn-group">
                                                            <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            {{ $post->status }}
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                @if(Auth::user()->department == 'Postal')
                                                                    <a class="dropdown-item" href="{{ route('change.postal.status' ,['postal_status' => 'Dispatched', 'postal_id' => $post->id]) }}">Dispatched</a>
                                                                @endif
                                                                <!-- @if($post->staff_name || $post->sent_to == 'DDE Admission & Finance')
                                                                    <a class="dropdown-item" href="{{ route('change.postal.status' ,['postal_status' => 'Delivered', 'postal_id' => $post->id]) }}">Delivered</a>
                                                                @else
                                                                    @if(Auth::user()->department == 'Registrar Office')
                                                                        <a class="dropdown-item" href="{{ route('change.postal.status' ,['postal_status' => 'Forwarded', 'postal_id' => $post->id]) }}">Forwarded</a>
                                                                    @endif
                                                                    @if(Auth::user()->department == 'Postal')
                                                                        <a class="dropdown-item" href="{{ route('change.postal.status' ,['postal_status' => 'Sent to Registrar', 'postal_id' => $post->id]) }}">Sent to Registrar</a>
                                                                    @endif
                                                                @endif -->
                                                                <!-- <button class="btn btn-primary" id="modal-5">Login</button> -->
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        {{ $post->dispatched_to }}
                                                    </td>
                                                    @php
                                                        if($post->delivered_by){
                                                            $delivered_by = App\Models\User::find($post->delivered_by)->name;
                                                        }else{
                                                            $delivered_by = null;
                                                        }
                                                            
                                                    @endphp
                                                    @if($delivered_by)
                                                        <td><div class="font-weight-600">{{ $delivered_by }} <br>( to {{ $post->dispatched_to }} )</div></td>
                                                    @else
                                                        <td><div class="font-weight-600"></div></td>
                                                    @endif
                                                    <td>
                                                        @if(Auth::user()->department == 'Registrar Office')
                                                            <a href="{{ route('postal_edit_entry' ,['postal_id' => $post->id]) }}" class="btn btn-primary btn-action" data-toggle="tooltip" title="Edit"><i class="fas fa-solid fa-edit"></i></a>
                                                            
                                                        @else
                                                            <a href="{{ route('postal_edit_entry' ,['postal_id' => $post->id]) }}" class="btn btn-primary btn-action" data-toggle="tooltip" title="Edit"><i class="fas fa-solid fa-edit"></i></a>
                                                            <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $post->id }})"><i class="fas fa-trash"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <script>
                                                function handleConfirmYes(postId) {
                                                    // Redirect to the specified route with the correct ticket ID
                                                    window.location.href = "{{ url('/delete/post') }}/" + postId;
                                                }
                                            </script>
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
