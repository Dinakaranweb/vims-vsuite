@extends('frontend.frontend_master')
<style>
  .tags-container {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 10px;
  }

  .tag {
      background-color: #007bff;
      color: white;
      border-radius: 3px;
      padding: 5px 10px;
      margin: 5px;
  }

  .tag .remove-tag {
      margin-left: 5px;
      cursor: pointer;
      color: white;
  }
</style>

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
            <h1>{!! $post->subject !!}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin_dept_post') }}">Post</a></div>
              <div class="breadcrumb-item">View</div>
            </div>
          </div>

          <div class="section-body">
            <!-- <h2 class="section-title">{!! $post->subject !!}</h2> -->
            <!-- <p class="section-lead">WYSIWYG editor and code editor.</p> -->


            <div class="row">
              <div class="col-lg-8 col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Scanned Copy</h4>
                  </div>
                  @if($post->scanned_copy == null)
                    <div class="card-body">
                        Scanned Copy is not available yet
                    </div>
                  @else
                    <div class="card-body">
                        @php
                            $scanned_copy = 'https://vmrfdu.edu.in/img/VMC-header.jpg';
                        @endphp
                        <iframe src="{{ Storage::url($post->scanned_copy) }}" frameborder="0" height="980px" width="100%"></iframe>
                    </div>
                  @endif
                  
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
                            @if($post->registrar_id)
                            <p><b>Registrar ID :</b> {{ $post->registrar_id }}</p>
                            @endif
                            <p><b>Sent by :</b> {{ $post->sent_by }}</p>
                            <p><b>From :</b> {!! strip_tags($post->post_from_address, '<span><b><i><u>') !!}</p>
                            <p><b>Forwarded to :</b> {!! $post->forward_to !!}</p>
                            <p><b>Original at :</b> {!! $post->original_at !!}</p>
                            @if($post->category)
                            <p><b>Category :</b> {{ $post->category }}</p>
                            @endif
                            <p><b>Status :</b> {{ $post->status }}</p>
                            @if($post->remarks)
                            <p><b>Remarks :</b> {{ $post->remarks }}</p>
                            @endif
                            <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($post->created_at)) }}</p>
                            @if($post->due_date)
                            <p style="color:red"><b>Due Date :</b> {{ date('d/m/Y', strtotime($post->due_date)) }}</p>
                            @elseif($post->status != 'Closed')
                            <!-- Add Due Date Section -->
                            <div class="form-group">
                              <label for="due_date"><b>Due Date:</b></label>
                              <input type="date" id="due_date" name="due_date" class="form-control" value="{{ $post->due_date }}">
                              <button type="button" class="btn btn-primary mt-2" onclick="saveDueDate()">Save Due Date</button>
                            </div>
                            @endif
                            <div class="buttons">
                              @if($post->status != 'Closed')
                              <a href="{{ route('reply-post' ,['post_id' => $post->id]) }}" class="btn btn-primary">Reply</a>
                              <a id="modal-upload" class="btn btn-info" style="color:#fff">Upload</a>
                              <a id="modal-categorize" class="btn btn-dark" style="color:#fff">File</a> 
                              <a href="{{ route('postal_edit_entry' ,['postal_id' => $post->id]) }}" class="btn btn-danger">Forward</a>
                              <button type="button" class="btn btn-warning" id="modal-post-close">Status</button>
                              @endif
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>

                <form class="modal-part" id="modal-post-close-part">
                    @csrf
                    <div class="form-group">
                        <label for="status-select"><b>Status</b></label>
                        <select name="status" id="status-select" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <div class="input-group">
                            <div class="col-sm-12 col-md-12">
                                <textarea name="remarks" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                    </div>
                </form>

                <script>
                  function saveDueDate() {
                    const dueDate = document.getElementById('due_date').value;

                    if (!dueDate) {
                      alert('Please select a due date.');
                      return;
                    }

                    // Send an AJAX request to save the due date
                    fetch('{{ route("save_due_date", ["post_id" => $post->id]) }}', {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify({ due_date: dueDate })
                    })
                    .then(response => response.json())
                    .then(data => {
                      if (data.success) {
                        alert('Due date saved successfully.');
                      } else {
                        alert('Failed to save due date.');
                      }
                    })
                    .catch(error => {
                      console.error('Error:', error);
                      alert('An error occurred while saving the due date.');
                    });
                  }
                </script>
                @if($post->is_responded)
                @foreach($rps as $rp)
                  <div class="row">
                    <div class="card col-12">
                      <div class="card-header">
                        <h4>Reply Post</h4>
                      </div>
                      <div class="card-body">
                      <div class="collapse show" id="mycard-collapse1">
                            <div class="card-body">
                              <p><b>Post ID :</b> {{ $rp->post_id }}</p>
                              <!--<p><b>Post :</b> {{ Storage::url($rp->scanned_copy) }}</p>-->
                              <iframe src="{{ Storage::url($rp->scanned_copy) }}" frameborder="0" height="200px" width="100%"></iframe>
                              
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                @endforeach
                @endif
                <div class="row">
                  <div class="card col-12">
                    <div class="card-header">
                        <h4>Postal Log</h4>
                        <div class="card-header-action">
                        <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                        </div>
                    </div>
                    <div class="collapse show" id="mycard-collapse">
                        <div class="card-body">
                        <div class="col-12">
                            <div class="activities">
                            @foreach($logs as $entry)
                                <div class="activity">
                                  <div class="activity-icon bg-primary text-white shadow-primary">
                                      <i class="fas fa-comment-alt" style="margin-top:19px"></i>
                                  </div>
                                  <div class="activity-detail">
                                      <div class="mb-2">
                                      <span class="text-job text-primary">{{ $entry->created_at }}</span>
                                      <span class="bullet"></span>
                                      </div>
                                      <p>{!! $entry->description !!}</p>
                                  </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>
        <form class="modal-part" id="modal-upload-part" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="fileInput">Scanned Copy</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input" id="fileInput">
                        <label class="custom-file-label" for="fileInput">Choose file</label>
                    </div>
                </div>
                <!-- Hidden input to hold the post_id -->
                <input type="hidden" name="post_id" value="{{ $post->id }}">
            </div>
        </form>
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
        <form class="modal-part" id="modal-categorize-part">
            @csrf
            <div class="form-group">
                <label>Categorize</label>
                <div class="input-group">
                    <div class="col-sm-12 col-md-12">
                        <div id="tags-container" class="tags-container">
                            <!-- Tags will be displayed here -->
                        </div>
                        <input type="text" id="tag-input" class="form-control" placeholder="Add a tag and press Enter or comma">
                        <div id="tag-suggestions" class="list-group" style="max-height:150px;overflow-y:auto;margin-top:5px;">
                            <button type="button" class="list-group-item list-group-item-action">ATTEMPT CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">ATTESTATION OF CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">ATTESTATION OF SYLLABUS WES/HCPC/CGFNS FORM</button>
                            <button type="button" class="list-group-item list-group-item-action">BONAFIDE CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">CORRECTION OF DEGREE CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">CORRECTION OF MARKSHEET</button>
                            <button type="button" class="list-group-item list-group-item-action">COURSE COMPLETION CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">DEGREE / CONVOCATION CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">DUPLICATE MARKSHEET</button>
                            <button type="button" class="list-group-item list-group-item-action">DUPLICATE OF DEGREE CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">EXAMINATION SCHEDULE / TIME TABLE CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">GENUINITY / VERIFICATION CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">INTERNATIONAL COURIER CHARGES</button>
                            <button type="button" class="list-group-item list-group-item-action">LAST DATE OF EXAM / DATE OF RESULT DECLARATION</button>
                            <button type="button" class="list-group-item list-group-item-action">MEDIUM OF INSTRUCTION CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">MIGRATION CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">TATKAL DEGREE / CONVOCATION CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">TRANSCRIPT CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">TRANSFER CERTIFICATE</button>
                            <button type="button" class="list-group-item list-group-item-action">SEARCH / LATE FEE FOR DEGREE CERTIFICATE</button>
                        </div>
                        <input type="hidden" id="tags" name="tags" value="">
                    </div>
                </div>
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <input type="hidden" name="status" value="Filed">
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tagInput = document.getElementById('tag-input');
                const tagsContainer = document.getElementById('tags-container');
                const tagsInput = document.getElementById('tags'); // Hidden input for tags
                let tags = [];

                tagInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ',') {
                        event.preventDefault(); // Prevent form submission
                        const tagValue = tagInput.value.trim();
                        if (tagValue && !tags.includes(tagValue)) {
                            tags.push(tagValue);
                            renderTags();
                            tagInput.value = ''; // Clear input
                        }
                    }
                });

                document.querySelectorAll('#tag-suggestions button').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const tagValue = btn.textContent.trim();
                        if (tagValue && !tags.includes(tagValue)) {
                            tags.push(tagValue);
                            renderTags();
                        }
                    });
                });

                function renderTags() {
                    tagsContainer.innerHTML = ''; // Clear existing tags
                    tags.forEach((tag, index) => {
                        const tagElement = document.createElement('div');
                        tagElement.className = 'tag';
                        tagElement.textContent = tag;

                        const removeTag = document.createElement('span');
                        removeTag.className = 'remove-tag';
                        removeTag.textContent = 'x';
                        removeTag.onclick = function() {
                            tags.splice(index, 1); // Remove tag from array
                            renderTags(); // Re-render tags
                        };

                        tagElement.appendChild(removeTag);
                        tagsContainer.appendChild(tagElement);
                    });

                    // Update the hidden input with the tags
                    tagsInput.value = tags.join(','); // Join tags into a comma-separated string
                }

                // Handle form submission
                document.getElementById('modal-categorize-part').addEventListener('submit', function(event) {
                    // Ensure tags are updated before submission
                    tagsInput.value = tags.join(','); // Update the hidden input with the tags
                });
            });
        </script>
      </div>
        @include('frontend.body.footer')
        </div>
  </div>
@endsection
@include('frontend.postal.script')