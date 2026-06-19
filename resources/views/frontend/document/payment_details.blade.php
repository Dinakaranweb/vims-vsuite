@extends('frontend.frontend_master')

@section('content')

<style>
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<div id="app">
    <div class="main-wrapper main-wrapper-1">

        @if(Auth::user()->role == 'SuperAdmin')
            @include('frontend.superadmin.body.header')
            @include('frontend.superadmin.body.sidebar')
        @else
            @include('frontend.admin.body.header')
            @include('frontend.admin.body.sidebar')
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Payment Details Report</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item">Payment Details</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">All Payment Details</h2>
                    <p class="section-lead">
                        List of all payment records entered in the system.
                    </p>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                                    <h4>Payment Details</h4>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('download_payment_details_pdf', request()->query()) }}" class="btn btn-success mr-2">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        &nbsp;
                                        <a href="{{ route('download_payment_details_excel', request()->query()) }}" class="btn btn-primary">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </a>
                                    </div>
                                </div>
                                <div class="p-3 border-top">
                                    <form method="GET" action="{{ route('payment.details') }}" class="form-row align-items-end">
                                        <div class="form-group col-md-2">
                                            <input type="text" name="doc_id" class="form-control" placeholder="Document ID" value="{{ request('doc_id') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="text" name="from" class="form-control" placeholder="Forwarded From" value="{{ request('from') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="text" name="mode" class="form-control" placeholder="Mode" value="{{ request('mode') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="text" name="expenditure_id" class="form-control" placeholder="Expenditure ID" value="{{ request('expenditure_id') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="text" name="payment_type" class="form-control" placeholder="Payment Type" value="{{ request('payment_type') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="text" name="paid_amount" class="form-control" placeholder="Paid amount" value="{{ request('paid_amount') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                        </div>
                                        <div class="form-group col-md-2 d-flex">
                                            <button type="submit" class="btn btn-primary mr-2">Search</button>
                                            <a href="{{ route('payment.details') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" style="width: 100%; table-layout: fixed; font-size: 13px;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 7%;">S.No</th>
                                            
                                                    <th style="width: 12.5%;">
                                                        DOC ID
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'document_approvals.doc_id', 'sort_dir' => request('sort_by') == 'document_approvals.doc_id' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'document_approvals.doc_id' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>
                                            
                                                    <th style="width: 12.5%;">
                                                        Expenditure ID
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.expenditure_id', 'sort_dir' => request('sort_by') == 'payment_details.expenditure_id' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.expenditure_id' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>
                                            
                                                    <th style="width: 9%;">
                                                        Mode
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.mode', 'sort_dir' => request('sort_by') == 'payment_details.mode' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.mode' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>

                                                    <th style="width: 12%;">
                                                        Type
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.payment_type', 'sort_dir' => request('sort_by') == 'payment_details.payment_type' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.payment_type' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>
                                            
                                                    <th style="width: 15%;">
                                                        Reference No
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.reference_number', 'sort_dir' => request('sort_by') == 'payment_details.reference_number' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.reference_number' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>
                                            
                                                    <th style="width: 12.5%;">
                                                        Date
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.payment_date', 'sort_dir' => request('sort_by') == 'payment_details.payment_date' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.payment_date' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>
                                            
                                                    <th style="width: 10.5%;">
                                                        Paid Amount
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.amount', 'sort_dir' => request('sort_by') == 'payment_details.amount' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.amount' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>

                                                    <th style="width: 10.5%;">
                                                        TDS Amount
                                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_details.tds_amount', 'sort_dir' => request('sort_by') == 'payment_details.tds_amount' && request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}">
                                                            {!! request('sort_by') == 'payment_details.tds_amount' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                                        </a>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i = ($payments->currentPage() - 1) * $payments->perPage() + 1;
                                                @endphp
                                                @forelse($payments as $payment)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td><a href="{{ url('/view/document/'.$payment->document_primary_id) }}" style="color: #1e1e1e">{{ $payment->document_number }}</a></td>
                                                        <td>{{ $payment->expenditure_id }}</td>
                                                        <td>{{ $payment->mode }}</td>
                                                        <td>{{ $payment->payment_type }}</td>
                                                        <td>{{ $payment->payment_reference_no }}</td>
                                                        <td>{{ $payment->payment_date ? date('d/m/Y', strtotime($payment->payment_date)) : '-' }}</td>
                                                        <td>
                                                            {{ $payment->currency }} {{ indianCurrencyFormat($payment->paid_amount) }}
                                                        </td>
                                                        <td>
                                                            {{ $payment->tds_amount ? $payment->currency . ' ' . indianCurrencyFormat($payment->tds_amount) : '-' }}
                                                        </td>
                                                        
                                                        <!--<td>{{ date('d/m/Y h:i A', strtotime($payment->created_at)) }}</td>-->
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center">No payment details found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        {{-- If you use pagination: --}}
                                        {!! $payments->links('frontend.pagination.custom') !!} 
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