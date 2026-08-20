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
            <h1>Departments</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">Departments</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Departments</h2>

            @if(session('message'))
                <div class="alert alert-{{ session('alert-type') == 'success' ? 'success' : 'danger' }}">
                    {{ session('message') }}
                </div>
            @endif

            @if($missingDepartments->isNotEmpty())
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card" style="border: 1px solid #ffc107;">
                        <div class="card-header" style="background: #fff8e1;">
                            <h4><i class="fas fa-exclamation-triangle text-warning"></i> Departments Missing From This List</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                These department names are already used by real users but don't have a department record yet -
                                without one, they won't show up in department search/forward-to pickers elsewhere in the app.
                            </p>
                            <table class="table table-sm">
                                <tbody>
                                    @foreach($missingDepartments as $deptName)
                                        <tr>
                                            <td class="align-middle">{{ $deptName }}</td>
                                            <td class="text-right">
                                                <form method="POST" action="{{ route('quick-add-dept') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="department_name" value="{{ $deptName }}">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Departments</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Dept ID</th>
                                    <th>Department</th>
                                    <th>Label</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($depts as $emp)
                                    <tr data-id="{{ $emp->id }}">
                                        <td>
                                            {{ $i++ }}
                                        </td>
                                        <td>{{ $emp->dept_id }}</td>
                                        <td>{{ $emp->dept_name }}</td>
                                        <td>{{ $emp->dept_label }}</td>
                                        <td>
                                            @if($emp->is_active)
                                            <a href="{{ route('change-dept-status', ['id' => $emp->id]) }}" class="btn btn-icon btn-primary mr-1" data-toggle="tooltip" title="Active"><i class="fas fa-check"></i></a>
                                            @else
                                            <a href="{{ route('change-dept-status', ['id' => $emp->id]) }}" class="btn btn-icon btn-dark mr-1" data-toggle="tooltip" title="Inactive"><i class="fas fa-exclamation-triangle"></i></a>
                                            @endif
                                            <a href="{{ route('edit-dept', ['id' => $emp->id]) }}" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit"><i class="far fa-edit"></i></a>
                                            <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $emp->id }})"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                <script>
                                    function handleConfirmYes(empId) {
                                        // Redirect to the specified route with the correct emp ID
                                        window.location.href = "{{ route('delete-dept', ['id' => ':id']) }}".replace(':id', empId);
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