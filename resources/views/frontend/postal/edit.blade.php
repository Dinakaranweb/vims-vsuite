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
            @if(true)
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
                      <div class="col-lg-8 col-md-12">
                        <div class="card">
                          <div class="card-header">
                            <h4>Post Edit</h4>
                          </div>
                          <div class="card-body">
                            <form action="{{ route('postal_update_entry') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Post to</label>
                                  <div class="col-sm-12 col-md-3">
                                      <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                          <input type="radio" name="type_to" value="DDE" class="selectgroup-input" onclick="toggleFields()">
                                          <span class="selectgroup-button">DDE</span>
                                        </label>
                                        <label class="selectgroup-item">
                                          <input type="radio" name="type_to" value="Staff" class="selectgroup-input" onclick="toggleFields()">
                                          <span class="selectgroup-button">Official</span>
                                        </label>
                                      </div>
                                  </div>
                                  <!-- Staff Name Field -->
                                  <div id="staff-name-field" class="col-sm-12 col-md-4" style="display: none;">
                                    <input type="text" id="staff_name" name="staff_name" class="form-control" placeholder="Name" autocomplete="off">
                                    <input type="hidden" id="staff_id" name="staff_id" value="">
                                    <div id="staff-suggestions" class="dropdown-menu" style="display:none;"></div>
                                  </div>
                                  
                                  <!-- DDE Fields -->
                                  <div id="dde-fields" class="col-sm-12 col-md-4" style="display: none;">
                                    <input type="text" id="payment_mode" name="payment_mode" class="form-control mb-2" placeholder="Payment Mode" autocomplete="off">
                                    <input type="text" id="dde_number" name="dde_number" class="form-control mb-2" placeholder="Reference number" autocomplete="off">
                                    <input type="text" id="dde_amount" name="dde_amount" class="form-control mb-2" placeholder="Amount" autocomplete="off">
                                  </div>
                                </div>
                              

                              <!-- JavaScript -->
                              <script>
                                function toggleFields() {
                                  const selectedValue = document.querySelector('input[name="type_to"]:checked').value;
                                  
                                  const staffNameField = document.getElementById('staff-name-field');
                                  const ddeFields = document.getElementById('dde-fields');

                                  // Display fields based on selection
                                    if (selectedValue === 'Staff') {
                                        staffNameField.style.display = 'block';
                                        ddeFields.style.display = 'none';
                                        ddeFields.style.marginLeft = '0'; // Reset margin-left for ddeFields
                                    } else if (selectedValue === 'DDE') {
                                        ddeFields.style.display = 'block';
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

                                <!-- <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">To</label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control" id="post_to" name="to" placeholder="Enter section name" value="{{ $post->sent_to }}" autocomplete="off" required>
                                        <div id="dept-suggestions" class="dropdown-menu" style="display:none;"></div>
                                    </div>
                                </div> -->
                                
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Registrar No</label>
                                  <div class="col-sm-12 col-md-7 d-flex align-items-center">
                                    <input type="text" id="registrar_entry_no" name="registrar_entry_no" class="form-control" placeholder="Reg-PO-XXXXX" value="{{ $post->registrar_id }}" readonly>
                                    @if($post->registrar_id == null)
                                      <button type="button" id="generateButton" class="btn btn-primary ml-2" onclick="generateRegistrarNo()">Generate</button>
                                      <button type="button" id="removeButton" class="btn btn-secondary ml-2" onclick="removeRegistrarNo()">Remove</button>
                                    @endif
                                    
                                  </div>
                                </div>

                                <script>
                                  let currentNumber = {{ $latestRegistrarNo ?? 0 }}; // Replace with the latest number from the database
                                  const originalNumber = currentNumber;

                                  function generateRegistrarNo() {
                                    const registrarInput = document.getElementById('registrar_entry_no');
                                    const generateButton = document.getElementById('generateButton');

                                    // Check if a number is already generated
                                    if (registrarInput.value) {
                                      return; // Do nothing if a number is already generated
                                    }

                                    currentNumber++;
                                    const formattedNumber = String(currentNumber).padStart(5, '0'); // Pad the number to 5 digits
                                    const registrarNo = `Reg-PO-${formattedNumber}`;
                                    registrarInput.value = registrarNo;

                                    // Disable the generate button to prevent further clicks
                                    generateButton.disabled = true;
                                  }

                                  function removeRegistrarNo() {
                                    const registrarInput = document.getElementById('registrar_entry_no');
                                    const generateButton = document.getElementById('generateButton');

                                    // Clear the input field
                                    registrarInput.value = '';

                                    // Reset the current number to the original value
                                    currentNumber = originalNumber;

                                    // Enable the generate button
                                    generateButton.disabled = false;
                                  }
                                </script>

                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Forward To</label>
                                  <div class="col-sm-12 col-md-7 position-relative">
                                    
                                    @php
                                      $forwardedTo = explode(', ', $post->forward_to);
                                      $forwardedTo = array_map('trim', $forwardedTo);
                                    @endphp

                                    @if($post->forward_to)
                                      <!-- Selected departments tags will display here -->
                                      <div id="selected-departments" class="mb-2">
                                        @foreach($forwardedTo as $section)
                                          <span class="badge badge-danger mr-1">{{ $section }} <span class="remove-tag" data-dept="{{ $section }}" style="cursor: pointer;">&times;</span></span>
                                        @endforeach
                                      </div>
                                    @else
                                      <!-- Selected departments tags will display here -->
                                      <div id="selected-departments" class="mb-2"></div>
                                    @endif

                                    <!-- Searchable input for the dropdown -->
                                    <div class="dropdown">
                                      <input type="text" id="forward_to_search" name="forward_to" value="{{ $post->forward_to }}" class="form-control" placeholder="Search and select departments" autocomplete="off" data-toggle="dropdown">
                                      <div id="forward-suggestions" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                                    </div>
                                  </div>
                                </div>
                                
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Forward with Original</label>
                                    <div class="col-sm-12 col-md-7 d-flex align-items-center">
                                        <div class="form-check">
                                            <input type="checkbox" name="original_at" class="form-check-input" id="originalAtCheckbox">
                                            <label class="form-check-label" for="originalAtCheckbox">Yes</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="form-group row mb-4">
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
                                    <textarea name="from_address" class="summernote-simple" required>{!! $post->post_from_address !!}</textarea>
                                  </div>
                                </div> -->
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Subject</label>
                                  <div class="col-sm-12 col-md-7">
                                    <textarea name="subject" class="summernote-simple" required>{!! $post->subject !!}</textarea>
                                  </div>
                                </div>
                                <!-- <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Received Date</label>
                                  <div class="col-sm-12 col-md-7">
                                    <input type="text" name="received_date" value="{{ $post->received_date }}" class="form-control datepicker" required>
                                  </div>
                                </div>
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Post Type</label>
                                  <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="type" required>
                                        <option value="Post" {{ $post->type == 'Post' ? 'selected' : '' }}>Post</option>
                                        <option value="Courier" {{ $post->type == 'Courier' ? 'selected' : '' }}>Courier</option>
                                        <option value="Speed Post" {{ $post->type == 'Speed Post' ? 'selected' : '' }}>Speed Post</option>
                                    </select>
                                  </div>
                                </div> -->
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                <input type="hidden" name="status" value="{{ $post->status }}">
                                <input type="hidden" name="dispatched_by" value="{{ Auth::user()->name }}">
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
                                    <p><b>Forwarded to :</b> {!! $post->forward_to !!}</p>
                                    <p><b>Original at :</b> {!! $post->original_at !!}</p>
                                    <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($post->created_at)) }}</p>
                                  </div>
                                </div>
                            </div>
                          </div>
                        </div>
                        @if($post->scanned_copy)
                          <div class="row">
                            <div class="card col-12">
                              <div class="card-header">
                                <h4>Scanned Copy</h4>
                              </div>
                              @php
                                  $scanned_copy = 'https://vmrfdu.edu.in/img/VMC-header.jpg';
                              @endphp
                              <div class="card-body">
                                
                              <iframe src="{{ Storage::url($post->scanned_copy) }}" frameborder="0" height="500px" width="100%"></iframe>
                              </div>
                            </div>
                          </div>
                        @endif
                        <!-- <div class="row">
                          <div class="card col-12">
                            <div class="card-header">
                              <h4>Upload</h4>
                            </div>
                            <div class="card-body">
                              <p>Upload the scanned copy of the post to proceed for delivery.</p>
                              <form action="{{ route('postal_upload_entry') }}" method="POST" enctype="multipart/form-data">
                                  @csrf
                                  <div class="form-group">
                                      <label for="fileInput">Scanned Copy</label>
                                      <div class="input-group">
                                          <div class="custom-file">
                                              <input type="file" name="file" class="custom-file-input" id="fileInput">
                                              <label class="custom-file-label" for="fileInput">Choose file</label>
                                          </div>
                                      </div>
                                      <input type="hidden" name="post_id" value="{{ $post->id }}">
                                  </div>
                                  <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                    <div class="col-sm-12 col-md-7">
                                      <button class="btn btn-primary">Upload</button>
                                    </div>
                                  </div>
                              </form>
                            </div>
                            <script>
                                $(document).ready(function () {
                                    $('.custom-file-input').on('change', function (event) {
                                        var inputFile = event.currentTarget;
                                        $(inputFile).parent()
                                            .find('.custom-file-label')
                                            .html(inputFile.files[0].name);
                                    });
                                });
                            </script>
                          </div>
                        </div> -->
                      </div>
                    </div>

                  </div>
                </section>
              </div>
            @else
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
                      <div class="col-lg-12 col-md-12">
                        <div class="card">
                          <div class="card-header">
                            <h4>Post Generation</h4>
                          </div>
                          <div class="card-body">
                            <form action="{{ route('postal_update_entry') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Post to</label>
                                  <div class="col-sm-12 col-md-3">
                                      <div class="selectgroup w-100">
                                          <label class="selectgroup-item">
                                              <input type="radio" name="type_to" value="University" class="selectgroup-input" 
                                                    onclick="toggleStaffName()" 
                                                    {{ $post->staff_name == null ? 'checked' : '' }}>
                                              <span class="selectgroup-button">University</span>
                                          </label>
                                          <label class="selectgroup-item">
                                              <input type="radio" name="type_to" value="Staff" class="selectgroup-input" 
                                                    onclick="toggleStaffName()" 
                                                    {{ $post->staff_name != null ? 'checked' : '' }}>
                                              <span class="selectgroup-button">Staff</span>
                                          </label>
                                      </div>
                                  </div>
                                  <div id="staff-name-field" class="col-sm-12 col-md-4" 
                                      style="display: {{ $post->staff_name != null ? 'block' : 'none' }}; position: relative;">
                                      @php
                                        if($post->staff_name == null){
                                          $staff_name = null;
                                        }else{
                                          $staff_name = App\Models\User::findOrFail($post->staff_name)->name;
                                        }
                                        
                                      @endphp
                                      <input type="text" id="staff_name" name="staff_name" class="form-control" 
                                            placeholder="Staff Name" autocomplete="off" value="{{ $staff_name }}">
                                      <input type="hidden" id="staff_id" name="staff_id" value="{{ $post->staff_name }}">
                                      <div id="staff-suggestions" class="dropdown-menu" style="display:none;"></div>
                                  </div>
                              </div>

                              <script>
                                  function toggleStaffName() {
                                      var staffField = document.getElementById('staff-name-field');
                                      var typeTo = document.querySelector('input[name="type_to"]:checked').value;
                                      if (typeTo === 'Staff') {
                                          staffField.style.display = 'block';
                                      } else {
                                          staffField.style.display = 'none';
                                      }
                                  }

                                  // Ensure the correct field is displayed when the page loads
                                  document.addEventListener('DOMContentLoaded', function() {
                                      toggleStaffName();
                                  });
                              </script>

                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">To</label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control" id="post_to" name="to" placeholder="Enter section name" value="{{ $post->sent_to }}" autocomplete="off" required>
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
                                    <textarea name="from_address" class="summernote-simple" required>{!! $post->post_from_address !!}</textarea>
                                  </div>
                                </div>
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Subject</label>
                                  <div class="col-sm-12 col-md-7">
                                    <input type="text" name="subject" value="{{ $post->subject }}" class="form-control" required>
                                  </div>
                                </div>
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Received Date</label>
                                  <div class="col-sm-12 col-md-7">
                                    <input type="text" name="received_date" value="{{ $post->received_date }}" class="form-control datepicker" required>
                                  </div>
                                </div>
                                <div class="form-group row mb-4">
                                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Post Type</label>
                                  <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="type" required>
                                        <option value="Post" {{ $post->type == 'Post' ? 'selected' : '' }}>Post</option>
                                        <option value="Courier" {{ $post->type == 'Courier' ? 'selected' : '' }}>Courier</option>
                                        <option value="Speed Post" {{ $post->type == 'Speed Post' ? 'selected' : '' }}>Speed Post</option>
                                    </select>
                                  </div>
                                </div>
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                <input type="hidden" name="status" value="Received">
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
            @endif
            @include('frontend.body.footer')
        </div>
    </div>
@endsection
@include('frontend.postal.script')