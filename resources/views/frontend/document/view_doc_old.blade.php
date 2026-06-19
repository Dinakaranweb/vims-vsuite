@extends('frontend.frontend_master')

@section('content')

<style>
    .approval-flowchart-container {
        overflow-x: auto;
        padding: 10px;
    }

    .approval-flowchart {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    .step {
        text-align: center;
        min-width: 250px;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        background-color: #f3f3f3;
        position: relative;
    }

    .step .vc {
        color: red;
    }

    .step .provc {
        color: green;
    }

    .step .registrar {
        color: #19b91f;
    }

    .icon {
        font-size: 24px;
        color: #ff7a00;
    }

    p {
        font-weight: bold;
        margin: 5px 0;
    }

    .connector {
        font-size: 24px;
        color: #ff7a00;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .approval-flowchart {
            gap: 10px;
        }
    }

    /* Scrollable description with manual scroll */
    .step .description {
        max-height: 150px;
        overflow-y: auto;
        display: block;
        padding: 5px;
        word-break: break-word;
        text-align: center;
        line-height: 1.5;
        scroll-behavior: smooth;
    }

    /* Modern clean scrollbar styling */
    .step .description::-webkit-scrollbar {
        width: 6px;
    }

    .step .description::-webkit-scrollbar-track {
        background: transparent;
    }

    .step .description::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .step .description::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Firefox scrollbar */
    .step .description {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }
</style>

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
                <h1>{!! $doc->subject !!}</h1>
                <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin_dept_post') }}">Document</a></div>
                <div class="breadcrumb-item">View</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">{!! $doc->subject !!}</h2>
                <!-- <p class="section-lead">WYSIWYG editor and code editor.</p> -->
                @php
                    $totalPaid = $pay->sum('paid_amount', 2);
                    $total_tds = $pay->sum('tds_amount', 2);
                @endphp
                <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                    <div class="card-header">
                        <h4>Document Details</h4>
                    </div>
                        <div class="card-body" style="overflow-x:auto;">
                            @if($doc->ticket_id)
                                @php
                                    $ticket = App\Models\Ticket::find($doc->ticket_id);
                                @endphp
                                @if(Auth::user()->role == 'SuperAdmin')
                                    <p><b>Ticket ID :</b> <a href="{{ url('superadmin/view/ticket/'.$ticket->id) }}" target="_blank">{{ $ticket->ticket_id }}</a></p>
                                @else
                                    <p><b>Ticket ID :</b> <a href="{{ url('admin/view/ticket/'.$ticket->id) }}" target="_blank">{{ $ticket->ticket_id }}</a></p>
                                @endif
                                <p><b>Ticket Title :</b> {{ $ticket->title }}</p>
                            @endif
                            <p><b>Document ID :</b> {{ $doc->doc_id }}</p>
                            <p><b>Sent by :</b> {{ App\Models\User::find($doc->by)->name }}, {{ App\Models\User::find($doc->by)->department }}</p>
                            <p><b>From :</b> {{ $doc->from }}</p>
                            <p><b>Subject :</b> {!! strip_tags($doc->subject, '<span><b><i><u>') !!}</p>
                            <p><b>Priority :</b> {{ $doc->priority }}</p>
                            <p><b>Purchase Request :</b> {{ $doc->is_purchase }}</p>
                            @if($doc->amount)
                                <div style="border:2px solid #2a6d98; border-radius:8px; padding:16px; margin-bottom:16px; background:#f8fafc;">
                                    <p><b>Requested Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.'  }} {{ ($doc->amount) }}/-</p>
                                    <p><b>Recommended Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ ($doc->recommended_amount) }}/-</p>
                                    <p><b>Sanctioned Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ ($doc->sanctioned_amount) }}/-</p>
                                    
                                    <p><b>Paid Amount : </b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($totalPaid) }}/-</p>
                                    @if($doc->payment_mode)
                                        <p><b>Payment Mode :</b>
                                            @if($doc->payment_mode == 'cash')
                                                Cash
                                            @elseif($doc->payment_mode == 'annexure')
                                                Refer Annexure
                                            @elseif($doc->payment_mode == 'cheque')
                                                Cheque
                                            @elseif($doc->payment_mode == 'bank')
                                                NEFT/RTGS
                                            @elseif($doc->payment_mode == 'upi')
                                                UPI
                                            @else
                                                {{ ucfirst($doc->payment_mode) }}
                                            @endif
                                        </p>
                                        <p><b>Payment Details :</b>
                                            @if($doc->payment_mode == 'cash')
                                                NA
                                            @elseif($doc->payment_mode == 'cheque')
                                                In favour of: {{ $doc->cash_in_favour ?? '-' }}
                                            @elseif($doc->payment_mode == 'bank')
                                                <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Name:</b> {{ $doc->account_holder ?? '-' }}
                                                <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Account Number:</b> {{ $doc->account_number ?? '-' }}
                                                <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>IFSC:</b> {{ $doc->ifsc_code ?? '-' }}
                                                <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Branch:</b> {{ $doc->account_branch ?? '-' }}
                                            @elseif($doc->payment_mode == 'upi')
                                                UPI ID: {{ $doc->upi_id ?? '-' }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endif
                            <p><b>Description :</b> {!! $doc->description !!}</p>
                            @if($doc->justification)
                                <p><b>Justification :</b> {!! $doc->justification !!}</p>
                            @endif
                            @if($doc->purchase_committee_report)
                                @php
                                    $link = 'https://officesuite.vinayakamission.edu.in'.Storage::url($doc->purchase_committee_report);
                                @endphp
                                <p><b>Purchase Committee Report :</b> <a href="{{ $link }}" target="__blank">{{ basename($doc->purchase_committee_report) }}</a></p>
                            @endif

                            @if($doc->purchase_order)
                                @php
                                    $link = 'https://officesuite.vinayakamission.edu.in'.Storage::url($doc->purchase_order);
                                @endphp
                                <p><b>Purchase Order :</b> <a href="{{ $link }}" target="__blank">{{ basename($doc->purchase_order) }}</a></p>
                            @endif

                            <p><b>Date :</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
                        </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h4>Approval Status</h4>
                    </div>
                        <div class="card-body">
                            <div class="approval-flowchart-container">
                                <div class="approval-flowchart">

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
                        </div>
                    </div>
                    
                    @if($doc->purchase_order)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Purchase Order</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-2">
                                    <button onclick="zoomOut('purchase_order')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                    <button onclick="zoomIn('purchase_order')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                </div>
                                <div id="viewer-purchase_order" class="pdf-viewer" data-url="{{ Storage::url($doc->purchase_order) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                            </div>
                        </div>
                    @endif
                    
                    @if($doc->purchase_committee_report)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Purchase Committee Report</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-2">
                                    <button onclick="zoomOut('purchase_committee_report')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                    <button onclick="zoomIn('purchase_committee_report')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                </div>
                                <div id="viewer-purchase_committee_report" class="pdf-viewer" data-url="{{ Storage::url($doc->purchase_committee_report) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                            </div>
                        </div>
                    @endif
                    
                    @php
                        $no = 1;
                    @endphp
                    @foreach($annexures as $attachment)
                        <div class="card">
                            @php
                                $link = 'https://officesuite.vinayakamission.edu.in'.Storage::url($attachment->annexure);
                                $viewerId = 'viewer-annexure-' . $no; // Make the id unique
                            @endphp
                            <div class="card-header">
                                <h4>{{ $no }} - <a href="{{ $link }}" target="__blank">{{ basename($attachment->annexure) }}</a></h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-2">
                                    <button onclick="zoomOut('annexure-{{ $no }}')" class="btn btn-secondary btn-sm">- Zoom Out</button>
                                    <button onclick="zoomIn('annexure-{{ $no }}')" class="btn btn-secondary btn-sm">+ Zoom In</button>
                                </div>
                                <div id="viewer-annexure-{{ $no }}" class="pdf-viewer" data-url="{{ Storage::url($attachment->annexure) }}" style="max-height: 720px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
                            </div>
                        </div>
                        @php $no++; @endphp
                    @endforeach
                </div>
                <div class="col-lg-4 col-md-12">
                    
                    <div class="row">
                        <div class="card col-12">
                            <div class="card-header">
                                <h4>Action</h4>
                                <div class="card-header-action">
                                <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                                </div>
                            </div>
                            <!-- <div class="collapse show" id="mycard-collapse">
                                <div class="card-body">
                                    @if($doc->status == 'Closed')
                                        <span style="color:red"><h5>Document was Closed</h5></span>  
                                        <br><br>
                                        <a href="#" class="btn btn-outline-primary" id="modal-comment">Comment</a>
                                    @else
                                        <div class="buttons">
                                        @php
                                            $user = App\Models\User::findOrFail(Auth::id());
                                            $is_forwarded = App\Models\DocumentApprovalForwardings::where('doc_id', $doc->id)->where('forwarded_to', Auth::user()->department)->get();
                                        @endphp

                                        @php
                                            $isLevel1Complete = DB::table('approval_log')->where('doc_id', $doc->id)->whereIn('status', [
                                                'Approved by Registrar',
                                                'Approved by Pro VC',
                                                'Approved by VC',
                                            ])->count() == 3;
                                        
                                            $isLevel2 = $isLevel1Complete && $doc->is_purchase == 'Y';
                                        @endphp
                                        
                                        @if(($doc->forwarded_to == Auth::user()->department || $is_forwarded ) && $doc->status != 'Requested')
                                            @if($user->role == 'SuperAdmin')
                                                @if($user->department == 'Registrar')
                                                    @php
                                                        // Check Registrar approval (Level 1 or Level 2 based)
                                                        $registrarApproved = DB::table('approval_log')
                                                            ->where('doc_id', $doc->id)
                                                            ->whereIn('status', $isLevel2 
                                                                ? ['Level 2 - Approved by Registrar'] 
                                                                : ['Approved by Registrar'])
                                                            ->exists();
                                                
                                                        // Check VC approval (only Level 2 or final approvals count for closing)
                                                        $vcFinalApproved = DB::table('approval_log')
                                                            ->where('doc_id', $doc->id)
                                                            ->whereIn('status', [
                                                                'Level 2 - Approved by VC',
                                                                'Level 2 - VC Approved in Principle',
                                                                'Approved by VC',
                                                                'VC Approved in Principle'
                                                            ])
                                                            ->exists();
                                                    @endphp
                                                
                                                    {{-- Show Registrar's action buttons only if Registrar has not yet approved --}}
                                                    @if(!$registrarApproved)
                                                        <a class="btn btn-success" id="modal-approve">Approve</a>
                                                        <a href="#" class="btn btn-dark" id="modal-hold">Hold</a>
                                                        <a href="#" class="btn btn-danger" id="modal-reject">Reject</a>
                                                        <a href="#" class="btn btn-warning" id="modal-pending">Pending</a>
                                                        <a href="#" class="btn btn-light" id="modal-discuss">Discuss</a>
                                                        <a href="#" class="btn btn-info" id="modal-noted">Noted</a>
                                                    @endif
                                                
                                                    {{-- Show "Close" button only after both Registrar and VC have approved --}}
                                                    @if(($doc->status == 'Approved' && $doc->is_purchase == 'N') || $doc->status == 'Final Approval Completed')
                                                        <a href="#" class="btn btn-dark" id="modal-close">Close</a>
                                                    @endif
                                                @endif

                                        
                                                @if($user->department == 'Pro-VC')
                                                    @php
                                                        $flagRegistrar = DB::table('approval_log')->where('doc_id', $doc->id)
                                                                        ->whereIn('status', $isLevel2 ? ['Level 2 - Approved by Registrar'] : ['Approved by Registrar'])->first();
                                        
                                                        $flagProVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                                        ->whereIn('status', $isLevel2 ? ['Level 2 - Approved by Pro VC'] : ['Approved by Pro VC'])->first();
                                        
                                                        $flagVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                                    ->whereIn('status', $isLevel2 ? ['Level 2 - Approved by VC', 'Level 2 - VC Approved in Principle'] : ['Approved by VC', 'VC Approved in Principle'])->first();
                                                    @endphp
                                        
                                                    @if(($flagRegistrar && !$flagProVC) || (Auth::user()->department == $doc->to && $doc->status != 'Requested'))
                                                        <a class="btn btn-success" id="modal-approve">Approve</a>
                                                        <a href="#" class="btn btn-dark" id="modal-hold">Hold</a>
                                                        <a href="#" class="btn btn-danger" id="modal-reject">Reject</a>
                                                        <a href="#" class="btn btn-warning" id="modal-pending">Pending</a>
                                                        <a href="#" class="btn btn-light" id="modal-discuss">Discuss</a>
                                                        <a href="#" class="btn btn-info" id="modal-noted">Noted</a>
                                                    @elseif($flagProVC && !$flagVC && $doc->approval_status != 'Closed by Registrar')
                                                        <a href="#" class="btn btn-outline-danger" id="modal-revoke">Revoke</a>
                                                    @endif
                                                @endif
                                        
                                                @if($user->department == 'VC')
                                                    @php
                                                        $flagProVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                                        ->whereIn('status', $isLevel2 ? ['Level 2 - Approved by Pro VC'] : ['Approved by Pro VC'])->first();
                                        
                                                        $flagVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                                    ->whereIn('status', $isLevel2 ? ['Level 2 - Approved by VC', 'Level 2 - VC Approved in Principle'] : ['Approved by VC', 'VC Approved in Principle'])->first();
                                                    @endphp
                                        
                                                    @if(($flagProVC && !$flagVC) || (Auth::user()->department == $doc->to))
                                                        <a class="btn btn-success" id="modal-approve">Approve</a>
                                                        <a class="btn btn-primary" id="modal-approve-in-principle" style="color:#fff">Approve in Principle</a>
                                                        <a href="#" class="btn btn-dark" id="modal-hold">Hold</a>
                                                        <a href="#" class="btn btn-danger" id="modal-reject">Reject</a>
                                                        <a href="#" class="btn btn-warning" id="modal-pending">Pending</a>
                                                        <a href="#" class="btn btn-light" id="modal-discuss">Discuss</a>
                                                        <a href="#" class="btn btn-info" id="modal-noted">Noted</a>
                                                    @elseif($flagVC && $doc->approval_status != 'Closed by Registrar')
                                                        <a href="#" class="btn btn-outline-danger" id="modal-revoke">Revoke</a>
                                                    @endif
                                                @endif
                                            @elseif($user->id != $doc->by)
                                                <a class="btn btn-success" id="modal-approve">Approve</a>
                                                <a href="#" class="btn btn-dark" id="modal-hold">Hold</a>
                                                <a href="#" class="btn btn-danger" id="modal-reject">Reject</a>
                                                <a href="#" class="btn btn-warning" id="modal-pending">Pending</a>
                                                <a href="#" class="btn btn-light" id="modal-discuss">Discuss</a>
                                                <a href="#" class="btn btn-info" id="modal-noted">Noted</a>
                                            @elseif($user->id == $doc->by)
                                                @if(($doc->status == 'Approved' && $doc->is_purchase == 'N') || $doc->status == 'Final Approval Completed')
                                                    <a href="#" class="btn btn-dark" id="modal-close">Close</a>
                                                @endif
                                            @endif
                                        
                                            <br><br><br>
                                            @if($doc->status != 'Requested')
                                                <a href="#" class="btn btn-outline-dark" id="modal-forward-doc">Forward</a>
                                            @endif
                                        @else
                                            @if($doc->requested_by != Auth::id() && $doc->status != 'Requested')
                                                <a href="#" class="btn btn-outline-dark" id="modal-request-doc">RFA (Req For Action)</a>
                                            @endif
                                        @endif
                                        
                                        {{-- ✅ New Buttons for Document Requests --}}
                                        @if($doc->status == 'Requested' || ($doc->status == 'Commented' && $doc->requested_by != Null))
                                            @if($doc->forwarded_to == Auth::user()->department)
                                                <a href="#" class="btn btn-outline-success" id="modal-send-doc">Send</a>
                                            @endif
                                        
                                            @if($doc->requested_by == Auth::id())
                                                <a href="#" class="btn btn-outline-danger" id="modal-cancel-request">Cancel RFA</a>
                                            @endif
                                        @endif


                                        <a href="#" class="btn btn-outline-primary" id="modal-comment">Comment</a>
                                    </div>
                                    @endif
                                </div>
                            </div> -->
                            
                            @php
                                $process_payment = DB::table('payment_processing')->where('doc_id', $doc->id)->first();
                            @endphp
                            
                            <div class="collapse show" id="mycard-collapse">
                                <div class="card-body">
                                    @if($doc->status != 'Draft')
                                        @if($doc->status == 'Closed')
                                            <span style="color:red"><h5>Document was Closed</h5></span>
                                            <div class="buttons">
                                                @php
                                                    $user = App\Models\User::findOrFail(Auth::id());
                                                @endphp
                                                <a href="#" class="btn btn-outline-primary" id="modal-comment">Comment</a>
                                                <a href="{{ route('download_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning">Download</a>
                                            </div>
                                        @else
                                            <div class="buttons">
                                                @php
                                                    $user = App\Models\User::findOrFail(Auth::id());
                                                    $status_check = DB::table('approval_log')->where('doc_id', $doc->id)->where('status', '!=', 'Draft')->count();
                                                @endphp
                                                
                                                @if($doc->by == $user->id && ($status_check == 1))
                                                    <a class="btn btn-primary" id="modal-retract" style="color:#fff">Retract</a> <br><br><br>
                                                @endif
    
                                                @if($user->role == 'SuperAdmin')
                                                    
                                                    <a class="btn btn-success" id="modal-approve">Approve</a>
                                                    @if($user->designation == 'VC')
                                                        <a class="btn btn-primary" id="modal-approve-in-principle" style="color:#fff">Approve in Principle</a>
                                                    @endif
                                                    <a href="#" class="btn btn-dark" id="modal-hold">Hold</a>
                                                    <a href="#" class="btn btn-danger" id="modal-reject">Reject</a>
                                                    <a href="#" class="btn btn-warning" id="modal-pending">Pending</a>
                                                    <a href="#" class="btn btn-light" id="modal-discuss">Discuss</a>
                                                    <a href="#" class="btn btn-info" id="modal-noted">Noted</a>
                                                @else
                                                    <a href="#" class="btn btn-danger" id="modal-hold">Hold</a>
                                                    <a href="#" class="btn btn-light" id="modal-discuss">Discuss</a>
                                                    <a href="#" class="btn btn-info" id="modal-noted">Noted</a>
                                                    @if($user->id == $doc->by && ($doc->status == 'Completed' || $user->department == 'HR' ))
                                                        <a href="#" class="btn btn-dark" id="modal-close">Close</a>
                                                    @else
                                                        <a href="#" class="btn btn-dark" id="modal-completed">Completed</a>
                                                    @endif
                                                    @if($user->department == 'Students Welfare')
                                                        
                                                        @if($process_payment)
                                                        
                                                            <a href="#" class="btn btn-primary" id="modal-pay">Payment Details</a>
                                                        
                                                        @elseif($user->role == 'HOD')
                                                            <a href="#" 
                                                                class="btn" 
                                                                id="modal-process-payment"
                                                                style="background: linear-gradient(135deg, #ff7e5f, #feb47b); 
                                                                        color: white; 
                                                                        padding: 10px 20px; 
                                                                        border-radius: 8px; 
                                                                        text-decoration: none;">
                                                                Proceed for Payment
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            
                                                <br><br><br>
                                                <a href="#" class="btn btn-outline-dark" id="modal-forward-doc">Forward</a>
                                                <a href="#" class="btn btn-outline-primary" id="modal-comment">Comment</a>
                                                <a href="{{ route('download_document', ['doc_id' => $doc->id]) }}" class="btn btn-warning">Download</a>                                            
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        

                        @if($process_payment && $user->department() == 'Students Welfare')
                            
                            <div class="col-12 mb-4">
                                <div class="card shadow" style="border: 2px solid #ff7e5f; border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #ff7e5f, #feb47b); border-radius: 8px 8px 0 0;">
                                        <h4 class="mb-0">Payment Processing</h4>
                                    </div>
                                    <div class="card-body" style="font-size: 14px; line-height: 2.0;">
                                        <div class="row mb-2">
                                            <div class="col-md-5 font-weight-bold">Exp ID</div>
                                            <div class="col-md-7">: {{ $process_payment->expenditure_id }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-5 font-weight-bold">Assigned to</div>
                                            <div class="col-md-7">: {{ App\Models\User::find($process_payment->assigned_to)->name }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-5 font-weight-bold">Assigned on</div>
                                            <div class="col-md-7">: {{ date('d/m/Y h:i A', strtotime($process_payment->updated_at ?? $process_payment->created_at)) }}</div>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-5 font-weight-bold">Status</div>
                                            <div class="col-md-7 d-flex align-items-center">
                                                : 
                                                <div class="btn-group ml-2">
                                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ $process_payment->status }}
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('change.finance.status', ['status' => 'In Progress', 'finance_id' => $process_payment->id]) }}">In Progress</a>
                                                        <a class="dropdown-item" href="{{ route('change.finance.status', ['status' => 'Hold', 'finance_id' => $process_payment->id]) }}">Hold</a>
                                                        <a class="dropdown-item" href="{{ route('change.finance.status', ['status' => 'Completed', 'finance_id' => $process_payment->id]) }}">Completed</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($totalPaid)
                            
                            <div class="card col-12" style="background-color: #2a6d98; color: #fff;">
                                <div class="card-header" style="background-color: #2a6d98; color: #fff; border-bottom: 1px solid #fff;">
                                    <h4>Total Paid Amount</h4>
                                </div>
                                <div class="card-body">
                                    <h6 style="color: #fff;"><b>Total Paid:</b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($totalPaid, 2) }}</h6>
                                    <h6 style="color: #fff;"><b>Total TDS:</b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format($total_tds, 2) }}</h6>
                                    <hr style="border-bottom: 1px solid #fff;">
                                    <h6 style="color: #fff;"><b>Grand Total:</b> {{ isset($doc->currency) ? $doc->currency : 'Rs.' }} {{ number_format(($total_tds + $totalPaid), 2) }}</h6>
                                </div>
                            </div>
                        
                        @endif
                        
                        @foreach($pay as $paymentDetails)
                            <div class="card col-12"><br>
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="card-header">
                                    <h4>Payment Details</h4>
                                    @if(Auth::user()->department == 'Students Welfare')
                                    <a href="{{ route('edit-payment-details', ['id' => $paymentDetails->id]) }}" class="btn btn-sm btn-primary edit-payment-btn">Edit</a>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <p><b>Payment Mode:</b> {{ $paymentDetails->mode ?? '-' }}</p>
                                    <p><b>Payment Type:</b> {{ $paymentDetails->payment_type ?? '-' }}</p>
                                    <p><b>Reference No:</b> {{ $paymentDetails->payment_reference_no ?? '-' }}</p>
                                    <p><b>Payment Date:</b> {{ $paymentDetails->payment_date ? date('d/m/Y', strtotime($paymentDetails->payment_date)) : '-' }}</p>
                                    <p><b>Paid Amount:</b> {{ $doc->currency ?? 'Rs.' }} {{ isset($paymentDetails->paid_amount) ? number_format($paymentDetails->paid_amount) : '-' }}</p>
                                    <p><b>Expenditure ID:</b> {{ $paymentDetails->expenditure_id ?? '-' }}</p>
                                    <p><b>Remark:</b> {{ $paymentDetails->remarks ?? '-' }}</p>
                                </div>
                            </div>
                        @endforeach

                    <div class="card col-12">
                        <div class="card-header">
                            <h4>Document Log</h4>
                            <div class="card-header-action">
                            <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#"><i class="fas fa-minus"></i></a>
                            </div>
                        </div>
                        <div class="collapse show" id="mycard-collapse">
                            <div class="card-body">
                            <div class="col-12">
                                <div class="activities">
                                @foreach($document_logs as $entry)
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
            <form class="modal-part" id="modal-approve-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    @if($doc->amount)
                        <div class="form-group row align-items-center">
                            <label class="col-md-6 text-md-right text-left">Requested amount<span style="color:Red"> *</span></label>
                            <div class="col-lg-5 col-md-6">
                                <input type="text" name="amount" value="{{ $doc->amount }}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-md-6 text-md-right text-left">Recommended amount<span style="color:Red"> *</span></label>
                            <div class="col-lg-5 col-md-6">
                                <input type="text" name="recommended_amount" value="{{ $doc->recommended_amount ? $doc->recommended_amount : $doc->amount }}" class="form-control" {{ $doc->recommended_amount ? 'readonly' : '' }}>
                            </div>
                        </div>
                        @if($doc->recommended_amount)
                            <div class="form-group row align-items-center">
                                <label class="col-md-6 text-md-right text-left">Sanctioned amount<span style="color:Red"> *</span></label>
                                <div class="col-lg-5 col-md-6">
                                    <input type="text" name="sanctioned_amount" value="{{ $doc->sanctioned_amount ? $doc->sanctioned_amount : $doc->recommended_amount }}" class="form-control">
                                </div>
                            </div>
                        @endif
                    @endif
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Approved">
                </div>
            </form>
            <form class="modal-part" id="modal-approve-in-principle-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Approved in Principle">
                </div>
            </form>
            <form class="modal-part" id="modal-retract-part">
                @csrf
                <div class="form-group">
                    <label>Reason</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <p style = "color:red">Retract option is available only once!</p>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Retract">
                </div>
            </form>
            <form class="modal-part" id="modal-hold-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Hold">
                </div>
            </form>
            <form class="modal-part" id="modal-close-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Close">
                </div>
            </form>
            <form class="modal-part" id="modal-completed-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Completed">
                </div>
            </form>
            <form class="modal-part" id="modal-pay-part">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select name="mode" class="form-control" required>
                                <option value="">Select Payment Mode</option>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="NEFT/RTGS">NEFT/RTGS</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Reference No</label>
                            <input type="text" name="payment_reference_no" class="form-control" placeholder="Reference Number">
                        </div>
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Type</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="full_payment" value="Full Payment" required>
                                <label class="form-check-label" for="full_payment">Full Payment</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="partial_payment" value="Partial Payment">
                                <label class="form-check-label" for="partial_payment">Partial Payment</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Expenditure ID</label>
                            <input type="text" name="expenditure_id" id="expenditure_id" class="form-control" value="{{ $process_payment ? $process_payment->expenditure_id : '' }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" placeholder="Amount Paid">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_tds_applicable" id="is_tds_applicable">
                                <label class="form-check-label" for="is_tds_applicable">TDS Applicable</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group tds-amount-group" style="display: none;">
                            <label>TDS Amount</label>
                            <input type="number" step="0.01" name="tds_amount" class="form-control" placeholder="Enter TDS amount">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" placeholder="Any additional remarks"></textarea>
                        </div>
                    </div> 
                </div>
                <div class="form-group">
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Paid">
                </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const tdsCheckbox = document.getElementById('is_tds_applicable');
                    const tdsAmountGroup = document.querySelector('.tds-amount-group');
                    const fullPaymentRadio = document.getElementById('full_payment');
                    const partialPaymentRadio = document.getElementById('partial_payment');
                    const expenditureIdInput = document.getElementById('expenditure_id');
                    
                    // TDS functionality
                    tdsCheckbox.addEventListener('change', function() {
                        if(this.checked) {
                            tdsAmountGroup.style.display = 'block';
                        } else {
                            tdsAmountGroup.style.display = 'none';
                            document.querySelector('input[name="tds_amount"]').value = '';
                        }
                    });
                    
                    // Expenditure ID editability based on payment type
                    fullPaymentRadio.addEventListener('change', function() {
                        if(this.checked) {
                            expenditureIdInput.readOnly = true;
                        }
                    });
                    
                    partialPaymentRadio.addEventListener('change', function() {
                        if(this.checked) {
                            expenditureIdInput.readOnly = false;
                        }
                    });
                    
                    // Also handle the case when page loads with a pre-selected value
                    if (fullPaymentRadio.checked) {
                        expenditureIdInput.readOnly = true;
                    } else if (partialPaymentRadio.checked) {
                        expenditureIdInput.readOnly = false;
                    }
                });
            </script>
            <form class="modal-part" id="modal-process-payment-part">
                @csrf
                
                @php
                    $staff = App\Models\User::where('department', 'Students Welfare')->get();
                @endphp
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Expenditure ID</label>
                            <input type="text" name="expenditure_id" class="form-control" value="{{ $process_payment ? $process_payment->expenditure_id : $expenditure_id }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Assign to</label>
                            <select name="assigned_to" class="form-control" required>
                                <option value="">Select the staff to assign</option>
                                @foreach($staff as $staff_member)
                                    <option value="{{ $staff_member->id }}">{{ $staff_member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div> 
                </div>
                <div class="form-group">
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Payment In Progress">
                </div>
            </form>
            <form class="modal-part" id="modal-reject-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Rejected">
                </div>
            </form>

            <form class="modal-part" id="modal-revoke-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Revoked">
                </div>
            </form>

            <form class="modal-part" id="modal-pending-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Pending">
                </div>
            </form>
            <form class="modal-part" id="modal-discuss-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Discuss">
                </div>
            </form>
            <form class="modal-part" id="modal-noted-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Noted">
                </div>
            </form>
            <form class="modal-part" id="modal-forward-doc-part">
                @csrf
                <div class="form-group">
                    
                    <div class="input-group mb-4">
                        <label>Forward To</label>
                        <div class="col-sm-12 col-md-12">
                        
                        @php
                            $forwardedTo = explode(', ', $doc->forwarded_to);
                            $forwardedTo = array_map('trim', $forwardedTo);
                        @endphp

                        @if($doc->forwarded_to)
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
                                <input type="text" id="forward_to_search" name="forward_to" value="{{ $doc->forwarded_to }}" class="form-control" placeholder="Search and select departments" autocomplete="off" data-toggle="dropdown">
                                <div id="forward-suggestions" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div><br>
                    <div class="form-group mt-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="send_original" name="send_original" value="1">
                            <label class="custom-control-label" for="send_original">Send Original</label>
                        </div>
                    </div><br>
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Forward">
                </div>
            </form>
            <form class="modal-part" id="modal-re-submit-part">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Re-Submit">
                </div>
            </form>
            <form class="modal-part" id="modal-comment-part" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12 col-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Commented">
                </div>
                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File</label>
                    <div class="col-sm-12 col-md-7">
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input">
                        <label class="custom-file-label">Choose file</label>
                    </div>
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
                @if(Auth::user()->department == 'Purchase')
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File Type</label>
                        <div class="col-sm-12 col-md-7">
                        <select class="form-control selectric" name="file_type" required>  
                            <option>Select</option>
                            <option value="Purchase Committee Report">Purchase Committee Report</option>
                            <option value="Purchase Order">Purchase Order</option>
                            <option value="Annexure">Annexure</option>
                        </select>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="file_type" value="Annexure">
                @endif
            </form>
            <form class="modal-part" id="modal-request-doc-part" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12 col-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Requested">
                </div>
            </form>
            <form class="modal-part" id="modal-send-doc-part" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12 col-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Send">
                </div>
            </form>
            <form class="modal-part" id="modal-cancel-request-part" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Message</label>
                    <div class="input-group">
                        <div class="col-sm-12 col-md-12 col-12">
                            <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Cancel RFA">
                </div>
            </form>
        </div>
        @include('frontend.body.footer')
        <!-- </div> -->
    </div>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

        const pdfDocs = {};

        document.querySelectorAll('.pdf-viewer').forEach(div => {
            const id = div.id.replace('viewer-', '');
            const url = div.dataset.url;
            pdfDocs[id] = { url, containerId: div.id, scale: 1.0, pdf: null };

            pdfjsLib.getDocument(url).promise.then(pdf => {
                pdfDocs[id].pdf = pdf;
                renderAllPages(id);
            });
        });

        function renderAllPages(id) {
            const { pdf, containerId, scale } = pdfDocs[id];
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            for (let i = 1; i <= pdf.numPages; i++) {
                renderPage(id, i);
            }
        }

        function renderPage(id, pageNum) {
            const { pdf, containerId, scale } = pdfDocs[id];
            const container = document.getElementById(containerId);

            pdf.getPage(pageNum).then(page => {
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                const dpr = window.devicePixelRatio || 1;
                const desiredWidth = container.clientWidth;
                const adjustedScale = (desiredWidth / unscaledViewport.width) * scale;

                const viewport = page.getViewport({ scale: adjustedScale });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.width = viewport.width * dpr;
                canvas.height = viewport.height * dpr;
                canvas.style.width = `${viewport.width}px`;
                canvas.style.height = `${viewport.height}px`;

                context.setTransform(dpr, 0, 0, dpr, 0, 0);
                page.render({ canvasContext: context, viewport });

                canvas.style.marginBottom = "20px";
                container.appendChild(canvas);
            });
        }

        function zoomIn(id) {
            pdfDocs[id].scale += 0.2;
            renderAllPages(id);
        }

        function zoomOut(id) {
            pdfDocs[id].scale = Math.max(0.4, pdfDocs[id].scale - 0.2);
            renderAllPages(id);
        }
    </script>
  
@endsection
@include('frontend.postal.script')