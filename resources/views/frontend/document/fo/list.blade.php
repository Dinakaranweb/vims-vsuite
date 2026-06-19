@extends('frontend.frontend_master')

@section('content')
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
                        <h1>Students Welfare Finance Dashboard</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                            <div class="breadcrumb-item">Students Welfare</div>
                        </div>
                    </div>

                    <!-- Dashboard Statistics -->
                    @include('frontend.document.fo.card_stats')

                    <!-- Recent Documents -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Recent Documents</h4>
                                    <div class="card-header-action">
                                        <a href="#" class="btn btn-primary">View All <i class="fas fa-chevron-right"></i></a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="table-1" style="text-align: center;">
                                            
                                            <thead>
                                                <tr>
                                                    <th style="width:5%">
                                                        S.No
                                                    </th>
                                                    <th style="width:15%">
                                                        DOC ID
                                                    </th>
                                                    <th style="width:10%">
                                                        EXP ID
                                                    </th>
                                                    <th style="width:20%">
                                                        Title
                                                    </th>
                                                    <th style="width:10%">
                                                        Priority
                                                    </th>
                                                    <th style="width:10%">
                                                        From
                                                    </th>
                                                    <th style="width:20%">
                                                        Payment Status
                                                    </th>
                                                    <th style="width:15%">Forwarded at</th>
                                                    <th style="width:10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                                                @endphp
                                                @foreach ($documents as $doc)

                                                    @php
                                                        $payment = DB::table('payment_processing')->where('doc_id', $doc->id)->first();
                                                    @endphp
                                                    <tr data-id="{{ $doc->id }}">
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $doc->doc_id }}</td>
                                                        <td>{{ isset($payment) ? $payment->expenditure_id : 'Not yet Assigned' }}</td>
                                                        <td><a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">{{ $doc->title }}</a></td>
                                                        <td>{{ $doc->priority }}</td>
                                                        <td>{{ $doc->from }}</td>
                                                        <td>{{ isset($payment) ? $payment->status : 'Not yet Assigned' }}</td>
                                                        <td>{{ isset($payment) ? \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') : \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
                                                        
                                                        <td>
                                                            <a href="javascript:void(0);" class="btn btn-primary btn-action mr-1 toggle-accordion" data-toggle="tooltip" title="View" data-target="#accordion-{{ $doc->id }}">
                                                                <i class="fas fa-solid fa-eye"></i>
                                                            </a>
                                                            @if($doc->status == 'Draft' && $doc->by == Auth::id())
                                                            <a href="{{ route('edit_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-solid fa-pen"></i>
                                                            </a>
                                                            <a class="btn btn-danger btn-action" data-toggle="tooltip" title="Delete" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="handleConfirmYes({{ $doc->id }})"><i class="fas fa-trash"></i></a>
                                                            @endif                                                
                                                        </td>
                                                    </tr>
                                                    <!-- Accordion row -->
                                                    <tr id="accordion-{{ $doc->id }}" class="accordion-content" style="display: none;">
                                                        <td colspan="4" style="border-right: none">
                                                            
                                                            <div class="accordion-details" style="text-align: left">
                                                                <p><b>Document ID :</b> {{ $doc->doc_id }}</p>
                                                                <p><b>Sent by :</b> {{ App\Models\User::find($doc->by)->name }}, {{ App\Models\User::find($doc->by)->department }}</p>
                                                                <p><b>From :</b> {{ $doc->from }}</p>
                                                                <p><b>Subject :</b> {!! strip_tags($doc->subject, '<span><b><i><u>') !!}</p>
                                                                @if($doc->amount)
                                                                    <p><b>Amount :</b> {{ $doc->amount }}</p>
                                                                @endif
                                                                <p><b>Justification :</b> {!! strip_tags($doc->justification, '<span><b><i><u>') !!}</p>
                                                                <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
                                                            </div>
                                                        </td>
                                                        <td colspan="4">
                                                            <div class="approval-flowchart-container">
                                                                <div class="approval-flowchart">
                                                                        @php
                                                                            $approval_logs = DB::table('approval_log')->where('doc_id', $doc->id)->latest()->get();
                                                                        @endphp
                                                                        @foreach($approval_logs as $log)
                                                                    
                                                                            @php
                                                                                $role = \App\Models\User::FindorFail($log->by)->department;
                                                                                if($role == 'Pro-VC'){
                                                                                    $style = 'provc';
                                                                                }elseif($role == 'VC'){
                                                                                    $style = 'vc';
                                                                                }elseif($role == 'Registrar'){
                                                                                    $style = 'registrar';
                                                                                }else{
                                                                                    $style = '';
                                                                                } 
                                                                            @endphp
                                                                    
                                                                            <div class="step">
                                                                                <div class="icon">&#x1F4A1;</div>
                                                                                <div class="{{ $style }}">
                                                                                    <p>{!! $log->status !!}</p>
                                                                                    <hr>
                                                                                    <span class="description">{!! $log->message !!}</span>
                                                                                    <hr>
                                                                                    <p>{{ date('d/m/Y h:i A', strtotime($log->created_at)) }}</p>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            @if (!$loop->last) <!-- Check if it's not the last iteration -->
                                                                                <div class="connector">&#x2190;</div>
                                                                            @endif
                                                                    
                                                                        @endforeach
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {!! $documents->links('frontend.pagination.custom') !!}
                                    </div>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function () {
                                            document.querySelectorAll(".toggle-accordion").forEach(button => {
                                                button.addEventListener("click", function () {
                                                    const targetId = this.getAttribute("data-target");
                                                    const targetRow = document.querySelector(targetId);
                                                    
                                                    // Toggle visibility
                                                    if (targetRow.style.display === "none") {
                                                        targetRow.style.display = "table-row";
                                                    } else {
                                                        targetRow.style.display = "none";
                                                    }
                                                });
                                            });
                                        });
                                    </script>
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

@section('styles')
<style>
    /* Card Styling */
    .card-statistic-1 .card-icon {
        width: 60px;
        height: 60px;
        line-height: 60px;
        text-align: center;
        border-radius: 12px;
        margin: 20px 20px 0;
    }
    
    .card-statistic-1 .card-icon i {
        font-size: 24px;
        color: #fff;
    }
    
    .card-statistic-1 .card-wrap {
        padding: 15px 20px;
    }
    
    .card-statistic-1 .card-body {
        font-size: 28px;
        font-weight: 700;
        color: #34395e;
    }
    
    .card-statistic-1 .card-footer {
        background: transparent;
        border-top: 1px solid #f9f9f9;
        padding: 10px 20px;
    }
    
    /* Payment Breakdown */
    .payment-breakdown {
        padding: 0 10px;
    }
    
    .payment-breakdown .row {
        margin: 0 -5px;
    }
    
    .payment-breakdown .col-6 {
        padding: 0 5px;
    }
    
    /* Staff Tabs Styling */
    .staff-tabs-wrapper {
        position: relative;
    }
    
    .staff-tabs {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        border-bottom: 1px solid #dee2e6;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: -ms-autohiding-scrollbar;
        scrollbar-width: thin;
    }
    
    .staff-tabs::-webkit-scrollbar {
        height: 6px;
    }
    
    .staff-tabs::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .staff-tabs::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .staff-tabs .nav-item {
        flex: 0 0 auto;
        margin-bottom: -1px;
    }
    
    .staff-tabs .nav-link {
        white-space: nowrap;
        padding: 12px 20px;
        border: 1px solid transparent;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        color: #495057;
        background-color: transparent;
        transition: all 0.3s;
    }
    
    .staff-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        background-color: #f8f9fa;
    }
    
    .staff-tabs .nav-link.active {
        color: #2a6d98;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: 600;
    }
    
    .staff-tab-header {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .staff-name {
        font-size: 14px;
        margin-bottom: 5px;
        white-space: nowrap;
    }
    
    .staff-assigned-count {
        font-size: 11px;
    }
    
    .staff-assigned-count .badge {
        font-size: 11px;
        padding: 3px 8px;
    }
    
    /* Staff Content */
    .staff-details {
        padding: 20px 0;
    }
    
    .card-sm {
        height: 100%;
        transition: transform 0.2s;
        border: 1px solid #e9ecef;
    }
    
    .card-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .payment-type-card {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
    }
    
    .payment-type-card:hover {
        background: #e9ecef;
        border-color: #dee2e6;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .staff-tabs .nav-link {
            padding: 10px 15px;
            font-size: 13px;
        }
        
        .staff-name {
            font-size: 13px;
        }
        
        .staff-assigned-count .badge {
            font-size: 10px;
            padding: 2px 6px;
        }
        
        .card-sm .card-body {
            padding: 15px 10px;
        }
        
        .card-statistic-1 .card-icon {
            width: 50px;
            height: 50px;
            line-height: 50px;
            margin: 15px 15px 0;
        }
        
        .card-statistic-1 .card-icon i {
            font-size: 20px;
        }
        
        .card-statistic-1 .card-body {
            font-size: 24px;
        }
    }
    
    @media (max-width: 576px) {
        .staff-tabs .nav-link {
            padding: 8px 12px;
        }
        
        .payment-breakdown .text-small {
            font-size: 12px;
        }
        
        .payment-breakdown .text-xs {
            font-size: 10px;
        }
        
        .card-statistic-1 .card-wrap {
            padding: 10px 15px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Staff tabs scroll to active on mobile
        if ($(window).width() < 768) {
            $('.staff-tabs .nav-link.active').each(function() {
                var container = $('.staff-tabs')[0];
                var activeTab = this;
                container.scrollLeft = activeTab.offsetLeft - 50;
            });
        }
        
        // Smooth scroll for staff tabs
        $('.staff-tabs .nav-link').on('click', function(e) {
            if ($(window).width() < 768) {
                var $this = $(this);
                setTimeout(function() {
                    $('.staff-tabs')[0].scrollLeft = $this[0].offsetLeft - 50;
                }, 100);
            }
        });
    });
</script>
@endsection