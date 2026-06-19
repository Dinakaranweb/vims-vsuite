@extends('frontend.frontend_master')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
              <h1>Reports</h1>
              <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Report</div>
              </div>
            </div>

            <div class="section-body">
              <h2 class="section-title">Report</h2>
              <p class="section-lead">Select the duration below for specific reports</p>

              <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                  <div class="card">
                    <div class="card-body">
                      <form method="POST" action="{{ route('specific_report') }}" class="needs-validation" novalidate="">
                      @csrf  
                        <div class="col-lg-5 col-md-5 form-group" style="display: inline-block;">
                          <label for="from">From</label>
                          <input id="from" type="text" name="from" class="form-control datepicker">
                        </div>
                        <div class="col-lg-5 col-md-5 form-group" style="display: inline-block;">
                          <label for="to">To</label>
                          <input id="to" type="text" name="to" class="form-control datepicker">
                        </div>
                        <div class="col-lg-2 col-md-2 form-group row" style="display: inline-block; align-content: right;">
                            <button class="btn btn-primary">Create</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                  <div class="card">
                      <div class="card-header bg-primary text-white" style="border-bottom-color: #2a6d98; display: flex; justify-content: center;">
                        <h5 class="mb-0">Post Received</h5>
                      </div>
                    </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-header">
                          <h4>Post Received</h4>
                        </div>
                        <div class="card-body">
                          <canvas id="myChart10"></canvas>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-body">
                          <ul class="list-group">
                            @foreach($departmentData as $department)
                              @if ($department['count'] != 0)
                                <li class="list-group-item" style="font-weight: 600">
                                    {{ $department['name'] }} - {{ $department['count'] }}
                                    
                                </li>
                              @endif
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                  <div class="card">
                      <div class="card-header bg-primary text-white" style="border-bottom-color: #2a6d98; display: flex; justify-content: center;">
                        <h5 class="mb-0">Post Sent</h5>
                      </div>
                    </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-header">
                          <h4>Post Sent</h4>
                        </div>
                        <div class="card-body">
                          <canvas id="myChart11"></canvas>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-body">
                          <ul class="list-group">
                            @foreach($opData as $department)
                              @if ($department['count'] != 0)
                                <li class="list-group-item" style="font-weight: 600">
                                    {{ $department['name'] }} - {{ $department['count'] }}
                                    
                                </li>
                              @endif
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
    
            <script>
                // Prepare data arrays
                const departmentNames = [];
                const departmentCounts = [];

                const opNames = [];
                const opCounts = [];

                @foreach($departmentData as $department)
                    @if ($department['count'] != 0)
                        departmentNames.push('{{ $department['name'] }}');
                        departmentCounts.push({{ $department['count'] }});
                    @endif
                @endforeach

                @foreach($opData as $department)
                    @if ($department['count'] != 0)
                        opNames.push('{{ $department['name'] }}');
                        opCounts.push({{ $department['count'] }});
                    @endif
                @endforeach

                console.log('Name: ', departmentNames);
                console.log('Count: ', departmentCounts);

                // Chart.js code
                const ctx = document.getElementById('myChart10').getContext('2d');
                const myChart = new Chart(ctx, {
                    type: 'bar', // You can change this to 'line', 'pie', etc.
                    data: {
                        labels: departmentNames,
                        datasets: [{
                            label: 'Department Counts',
                            data: departmentCounts,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                const ctx2 = document.getElementById('myChart11').getContext('2d');
                const myChart2 = new Chart(ctx2, {
                    type: 'bar', // You can change this to 'line', 'pie', etc.
                    data: {
                        labels: opNames,
                        datasets: [{
                            label: 'OP Counts',
                            data: opCounts,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                function submitChart() {
                    const chartImage = myChart.toBase64Image(); // Department chart image
                    const opImage = myChart2.toBase64Image(); // OP chart image
                    document.getElementById('departments-chart-img').value = chartImage; // Set department chart image
                    document.getElementById('op-chart-img').value = opImage; // Set OP chart image
                    document.getElementById('chart-form').submit(); // Submit the form
                }
            </script>
            
            </div>
            
          </section>
        <!-- <a href="{{ url('/chart-template') }}"><button class="btn btn-primary">Download</button></a> -->

        <form id="chart-form" method="POST" action="{{ route('postal_report_download') }}" onsubmit="event.preventDefault(); submitChart();">
            @csrf
            <input type="hidden" id="departments-chart-img" name="departments_chart_img">
            <input type="hidden" id="op-chart-img" name="op_chart_img">
            <input type="submit" class="btn btn-primary" value="Download" onclick="submitChart()">
        </form>

        </div>

        @include('frontend.body.footer')

        </div>
  </div>
@endsection