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
            <h1>My Staffs</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('super_admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">{{ Auth::user()->department }} Staffs</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">My Staffs</h2>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>{{ Auth::user()->department }} Staffs</h4>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Emp ID</th>
                                    <th>Full Name</th>
                                    <th>User Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Designation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($staffs as $emp)
                                    <tr data-id="{{ $emp->id }}">
                                        <td>
                                            {{ $i++ }}
                                        </td>
                                        <td>{{ $emp->emp_id }}</td>
                                        <td>{{ $emp->name }}</td>
                                        <td>{{ $emp->username }}</td>
                                        <td>{{ $emp->phone }}</td>
                                        <td>{{ $emp->email }}</td>
                                        <td>{{ $emp->designation }}</td>
                                        <td>
                                            @if($emp->is_active)
                                            <a href="{{ route('super-admin-change-account-status', ['id' => $emp->id]) }}" class="btn btn-icon btn-primary mr-1" data-toggle="tooltip" title="Active"><i class="far fa-user"></i></a>
                                            @else
                                            <a href="{{ route('super-admin-change-account-status', ['id' => $emp->id]) }}" class="btn btn-icon btn-dark mr-1" data-toggle="tooltip" title="Inactive"><i class="far fa-user"></i></a>
                                            @endif
                                            <a href="{{ route('super-admin-edit-staff', ['id' => $emp->id]) }}" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit"><i class="far fa-edit"></i></a>
                                            <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $emp->id }})"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                <script>
                                    function handleConfirmYes(empId) {
                                        // Redirect to the specified route with the correct emp ID
                                        window.location.href = "{{ route('super-admin-delete-staff', ['id' => ':id']) }}".replace(':id', empId);
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