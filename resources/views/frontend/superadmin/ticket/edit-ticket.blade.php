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
            <h1>Edit Ticket</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin_tickets_summary') }}">Ticket</a></div>
              <div class="breadcrumb-item">Edit</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Edit Ticket</h2>
            <!-- <p class="section-lead">WYSIWYG editor and code editor.</p> -->

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Ticket Generation</h4>
                  </div>
                  <div class="card-body">
                    <form action="{{ route('update_ticket') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Ticket Title</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="title" class="form-control" value="{{ $ticket->title }}" required>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Ticket to</label>
                          <div class="col-sm-12 col-md-7">
                            <select class="form-control selectric" name="ticket_to" required>
                              @foreach ($departments as $department)
                                <option value="{{ $department->dept_label }}" {{ $department->dept_label == $ticket->ticket_to? 'selected' : '' }}>{{ $department->dept_label }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description</label>
                          <div class="col-sm-12 col-md-7">
                            <textarea name="description" class="summernote" required>{{ $ticket->description }}</textarea>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Priority</label>
                          <div class="col-sm-12 col-md-7">
                            <div class="selectgroup selectgroup-pills">
                              <label class="selectgroup-item">
                                  <input type="radio" name="priority" value="Low" class="selectgroup-input" {{ $ticket->priority == 'Low'? 'checked' : '' }} required>
                                  <span class="selectgroup-button">Low</span>
                              </label>
                              <label class="selectgroup-item">
                                  <input type="radio" name="priority" value="Medium" class="selectgroup-input" {{ $ticket->priority == 'Medium'? 'checked' : '' }}>
                                  <span class="selectgroup-button">Medium</span>
                              </label>
                              <label class="selectgroup-item">
                                  <input type="radio" name="priority" value="High" class="selectgroup-input" {{ $ticket->priority == 'High'? 'checked' : '' }}>
                                  <span class="selectgroup-button">High</span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Due Date</label>
                          <div class="col-sm-12 col-md-7">
                            <input type="text" name="due_date" value="{{ $ticket->due_date }}" class="form-control datepicker" required>
                          </div>
                        </div>
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File</label>
                          <div class="col-sm-12 col-md-7">
                            <div class="custom-file">
                              <input type="file" name="file" class="custom-file-input" required>
                              <label class="custom-file-label">Choose file</label>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="id" value="{{ $ticket->id }}">
                        <div class="form-group row mb-4">
                          <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                          <div class="col-sm-12 col-md-7">
                            <button class="btn btn-primary">Update</button>
                          </div>
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