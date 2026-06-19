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
                    <h1>DDE Details</h1>
                </div>

                <div class="section-body">
                    <h2 class="section-title">DDE Details</h2>
                    <p class="section-lead">View and filter DDE details.</p>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('dde_details.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- DDE Details Table -->
                    <div class="card">
                        <div class="card-header">
                            <h4>DDE Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1" style="font-size: 13px; text-align: center;">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Sent by</th>
                                            <th>Reg No</th>
                                            <th>C-Code</th>
                                            <th>Fee Item</th>
                                            <th>Mode</th>
                                            <th>Payment Reference No</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach($ddeDetails as $detail)
                                        @php
                                            $post = App\Models\Postal::where('id', $detail->post_id)->first();
                                        @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $post->sent_by }}</td>
                                                <td>{{ $detail->reg_no }}</td>
                                                <td>{{ $detail->c_code }}</td>
                                                <td>{{ $detail->fee_item }}</td>
                                                <td>{{ $detail->mode }}</td>
                                                <td>{{ $detail->payment_reference_no }}</td>
                                                <td>{{ $detail->payment_date }}</td>
                                                <td>{{ $detail->amount }}</td>
                                                <td>{{ $detail->remarks }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="card">
                        <div class="card-body">
                            <h5>Total Amount Received: <b>{{ number_format($totalAmount, 2) }}</b></h5>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        @include('frontend.body.footer')
    </div>
</div>
@endsection