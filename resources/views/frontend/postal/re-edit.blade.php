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
            <h1>Edit Post</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin_tickets_summary') }}">Post</a></div>
              <div class="breadcrumb-item">Edit</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Edit Post</h2>
            <!-- <p class="section-lead">WYSIWYG editor and code editor.</p> -->

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Post Generation</h4>
                  </div>
                  <div class="card-body">
                    <form action="{{ route('postal_store_re-edit_entry') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Radio buttons and Staff Name in the same row -->
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Post to</label>
                          <div class="col-sm-12 col-md-3">
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="type_to" value="KV" class="selectgroup-input" {{ $post->type_to == 'KV' ? 'checked' : '' }} onclick="toggleFields()">
                                <span class="selectgroup-button">KVSTAR</span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="type_to" value="Cottage" class="selectgroup-input" {{ $post->type_to == 'Cottage' ? 'checked' : '' }} onclick="toggleFields()">
                                <span class="selectgroup-button">Cottage</span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="type_to" value="University" class="selectgroup-input" {{ $post->type_to == 'University' ? 'checked' : '' }} onclick="toggleFields()">
                                <span class="selectgroup-button">Registrar</span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" name="type_to" value="Staff" class="selectgroup-input" {{ $post->type_to == 'Staff' ? 'checked' : '' }} onclick="toggleFields()">
                                <span class="selectgroup-button">Official</span>
                              </label>
                            </div>
                          </div>
                          
                          <!-- Staff Name Field -->
                        @php
                        if($post->staff_name)
                            $staff = App\Models\User::find($post->staff_name)->name;
                        else
                            $staff = null;
                        @endphp
                          <div id="staff-name-field" class="col-sm-12 col-md-4" style="display: none;">
                            <input type="text" id="staff_name" name="staff_name" value="{{ $staff }}" class="form-control" placeholder="Name" autocomplete="off">
                            <input type="hidden" id="staff_id" name="staff_id" value="{{ $post->staff_name }}">
                            <div id="staff-suggestions" class="dropdown-menu" style="display:none;"></div>
                          </div>
                        
                          
                          <!-- DDE Fields -->
                          <div id="dde-fields" class="col-sm-12 col-md-4" style="display: none;">
                            <input type="text" id="name" name="name" class="form-control mb-2" placeholder="Name" autocomplete="off">
                            <input type="text" id="cottage" name="cottage" class="form-control mb-2" placeholder="Cottage no" autocomplete="off">
                          </div>
                        </div>

                        <!-- JavaScript -->
                        <script>
                          function toggleFields() {
                            const selectedValue = document.querySelector('input[name="type_to"]:checked').value;
                            
                            const staffNameField = document.getElementById('staff-name-field');
                            const ddeFields = document.getElementById('dde-fields');

                            // Display fields based on selection
                              if (selectedValue === 'Staff' || selectedValue === 'KV') {
                                  staffNameField.style.display = 'block';
                                  staffNameField.style.marginLeft = '75px'; // Example margin-left value
                                  ddeFields.style.display = 'none';
                                  ddeFields.style.marginLeft = '0'; // Reset margin-left for ddeFields
                              } else if (selectedValue === 'Cottage') {
                                  ddeFields.style.display = 'block';
                                  ddeFields.style.marginLeft = '75px'; // Example margin-left value
                                  staffNameField.style.display = 'none';
                                  staffNameField.style.marginLeft = '0'; // Reset margin-left for staffNameField
                              } else {
                                  staffNameField.style.display = 'none';
                                  staffNameField.style.marginLeft = '0'; // Reset margin-left
                                  ddeFields.style.display = 'none';
                                  ddeFields.style.marginLeft = '0'; // Reset margin-left
                              }
                          }

                          // Call on page load to set the initial state
                          window.onload = toggleFields;
                        </script>

                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">To</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="post_to" name="to" value="{{ $post->sent_to }}" placeholder="Enter section name" autocomplete="off" required>
                                <div id="dept-suggestions" class="dropdown-menu" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">To address</label>
                          <div class="col-sm-12 col-md-7">
                            <textarea name="to_address" class="summernote-simple" required> 
                              <b>Vinayaka Missions Reseach Foundation</b>, <br>NH 47, Sankari Main Road,<br> Salem, Tamil Nadu 636308
                            </textarea>
                          </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">By</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="by" value="{{ $post->sent_by }}" class="form-control" required>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">From address</label>
                          <div class="col-sm-12 col-md-7">
                            <textarea name="from_address" class="summernote-simple" required>{{ $post->post_from_address }}</textarea>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Received Date</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="received_date" value="{{ $post->received_date }}" class="form-control datepicker" required>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tracking ID</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="tracking_id" value="{{ $post->tracking_id }}" class="form-control" required>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Post Type</label>
                          <div class="col-sm-12 col-md-7">
                            <select class="form-control selectric" name="type" required>
                                <option value="Post" {{ $post->type == 'Post' ? 'selected' : '' }}>Post</option>
                                <option value="Courier" {{ $post->type == 'Courier' ? 'selected' : '' }}>Courier</option>
                                <option value="Hand Delivered" {{ $post->type == 'Hand Delivered' ? 'selected' : '' }}>Hand Delivered</option>
                            </select>
                          </div>
                        </div>
                        <input type="hidden" name="id" value="{{ $post->id }}">
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
            </div>

          </div>
        </section>
      </div>
        @include('frontend.body.footer')
        </div>
  </div>
@endsection
@include('frontend.postal.script')