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
                        <div class="breadcrumb-item">Postal Entries</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">Received Posts</h2>
                    <p class="section-lead">
                        Make sure the all the posts are dispatched.
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
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Dispatch</th>
                                                <th>Dispatched to</th>
                                                <!--<th>Dispatched by</th>-->
                                                <th style="width:15%">Action</th>
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
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($post->created_at)->format('Y-m-d') }}
                                                    </td>
                                                    
                                                    <td class="postal-status">
                                                        {{ $post->status }}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button 
                                                                class="btn btn-info dispatch-button" 
                                                                data-id="{{ $post->id }}" 
                                                                data-status="{{ $post->status }}">
                                                                {{ $post->status == 'Dispatched' ? 'Undo' : 'Dispatch' }}
                                                            </button>
                                                        </div>
                                                    </td>

                                                    <td class="postal-dispatched-to">
                                                        {{ $post->dispatched_to }}
                                                    </td>
                                                    @php
                                                        if($post->dispatched_by){
                                                            $dispatched_by = App\Models\User::find($post->dispatched_by)->name;
                                                        }else{
                                                            $dispatched_by = null;
                                                        }
                                                            
                                                    @endphp
                                                    <!--@if($dispatched_by)-->
                                                    <!--    <td class="postal-dispatched-by"><div class="font-weight-600">{{ $dispatched_by }} </div></td>-->
                                                    <!--@else-->
                                                    <!--    <td  class="postal-dispatched-by"><div class="font-weight-600"></div></td>-->
                                                    <!--@endif-->
                                                    <td>
                                                        @if(Auth::user()->department == 'Registrar Office')
                                                            <a href="{{ route('postal_edit_entry' ,['postal_id' => $post->id]) }}" class="btn btn-primary btn-action" data-toggle="tooltip" title="Edit"><i class="fas fa-solid fa-edit"></i></a>
                                                        @else
                                                            <!-- <a href="{{ route('postal_edit_entry' ,['postal_id' => $post->id]) }}" class="btn btn-primary btn-action" data-toggle="tooltip" title="Edit"><i class="fas fa-solid fa-edit"></i></a> -->
                                                            <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $post->id }})"><i class="fas fa-trash"></i></a>
                                                            <a href="{{ route('postal_reEdit_entry', ['postal_id' => $post->id]) }}" class="btn btn-warning btn-action" data-toggle="tooltip" title="Edit" ><i class="fas fa-pen"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <script>
                                                $(document).on('click', '.dispatch-button', function () {
                                                    const button = $(this);
                                                    const postId = button.data('id');
                                                    const currentStatus = button.data('status');
                                                    const url = currentStatus === 'Dispatched' 
                                                        ? `/postal/undo/${postId}` 
                                                        : `/postal/dispatch/${postId}`;

                                                    $.ajax({
                                                        url: url,
                                                        type: 'POST',
                                                        data: {
                                                            _token: '{{ csrf_token() }}'
                                                        },
                                                        success: function (response) {
                                                            if (response.success) {
                                                                const newStatus = response.status;
                                                                
                                                                // Update button text and data attribute
                                                                button.data('status', newStatus);
                                                                button.text(newStatus === 'Dispatched' ? 'Undo' : 'Dispatch');
                                                                
                                                                // Update status column
                                                                button.closest('tr').find('.postal-status').text(newStatus);

                                                                // Update dispatched_to column
                                                                button.closest('tr').find('.postal-dispatched-to').text(response.dispatched_to);

                                                                // Update dispatched_by column
                                                                button.closest('tr').find('.postal-dispatched-by').text(response.dispatched_by);
                                                            } else {
                                                                alert('Failed to update status.');
                                                            }
                                                        },
                                                        error: function () {
                                                            alert('An error occurred. Please try again.');
                                                        }
                                                    });
                                                });
                                            </script>
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
