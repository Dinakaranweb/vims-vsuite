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
            <h1>Ex Staffs</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item">{{ Auth::user()->department }} Staffs</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Ex Staffs</h2>

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
                                    <th>Deleted</th>
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
                                        <td>{{ \Carbon\Carbon::parse($emp->deleted_at)->format('d-m-Y') }}</td>
                                        <td>
                                            <a class="btn btn-info" data-confirm="Are You Sure?|Add this staff back to your department?" data-confirm-yes="handleConfirmYes({{ $emp->id }})" style="color:#fff">Recover</a>
                                        </td>
                                    </tr>
                                @endforeach

                                <script>
                                    function handleConfirmYes(empId) {
                                        // Redirect to the specified route with the correct emp ID
                                        window.location.href = "{{ route('recover-staff', ['id' => ':id']) }}".replace(':id', empId);
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