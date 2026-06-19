@php
                
    $departments = App\Models\Department::orderBy('dept_name', 'asc')->get();  

@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Search Documents</h4>
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#searchCard" aria-expanded="false" aria-controls="searchCard">
                    Toggle Search
                </button>
            </div>
            <div id="searchCard" class="collapse">
                <div class="card-body">
                    <form method="GET" action="{{ route('sa_search_documents') }}" class="form-inline" style="margin-top: 10px;">
                        <input type="hidden" name="type" value="total">
                        <div class="form-group mr-2">
                            <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
                        </div>
                        <div class="form-group mr-2">
                            <input type="text" name="doc_id" class="form-control" placeholder="Search by Document ID" value="{{ request('doc_id') }}">
                        </div>
                        <div class="form-group mr-2">
                            <select name="section" class="form-control">
                                <option value="">Search by Section</option>
                                @foreach($departments as $section)
                                    <option value="{{ $section->dept_label }}" {{ request('section') == $section->dept_label ? 'selected' : '' }}>{{ $section->dept_label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <input type="date" name="date_from" class="form-control" placeholder="Date From" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group mr-2">
                            <input type="date" name="date_to" class="form-control" placeholder="Date To" value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('summary_new', ['role' => 'VC']) }}" class="btn btn-secondary ml-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: center;">
                <h4>Requests</h4>
            </div>
            <hr style="margin: 0;">
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
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'doc_id', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'doc_id' ? 'desc' : 'asc']) }}">
                                        {!! request('sort_by') == 'doc_id' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                    </a>
                                </th>
                                <th style="width:15%">
                                    Title
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'title' ? 'desc' : 'asc']) }}">
                                        {!! request('sort_by') == 'title' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                    </a>
                                </th>
                                <th style="width:10%">
                                    Priority
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'priority', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'priority' ? 'desc' : 'asc']) }}">
                                        {!! request('sort_by') == 'priority' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                    </a>
                                </th>
                                <th style="width:10%">
                                    From
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'from', 'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'from' ? 'desc' : 'asc']) }}">
                                        {!! request('sort_by') == 'from' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                    </a>
                                </th>
                               <th style="width:15%">
                                    Status
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'payment_status',
                                        'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'payment_status' ? 'desc' : 'asc'
                                    ]) }}">
                                        {!! request('sort_by') == 'payment_status' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                    </a>
                                </th>
                                <th style="width:15%">Current Status</th>
                                <th style="width:15%">
                                    Created
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'created_at',
                                        'sort_dir' => request('sort_dir') == 'asc' && request('sort_by') == 'created_at' ? 'desc' : 'asc'
                                    ]) }}">
                                        {!! request('sort_by') == 'created_at' ? (request('sort_dir') == 'asc' ? '▲' : '▼') : '↕' !!}
                                    </a>
                                </th>
                                <th style="width:10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                @php
                                $i = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                            @endphp
                            @foreach ($documents as $doc)

                                @php

                                    $isLevel1Complete = DB::table('approval_log')->where('doc_id', $doc->id)->whereIn('status', [
                                        'Approved by Registrar',
                                        'Approved by Pro VC',
                                        'Approved by VC',
                                    ])->count() == 3;
                                
                                    $isLevel2 = $isLevel1Complete && $doc->is_purchase == 'Y';

                                    $flagRegistrar = DB::table('approval_log')->where('doc_id', $doc->id)
                                                    ->whereIn('status', ['Approved by Registrar'])->first();
                    
                                    $flagProVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                    ->whereIn('status', ['Approved by Pro VC'])->first();

                                    $flagVC = DB::table('approval_log')->where('doc_id', $doc->id)
                                                    ->whereIn('status', ['Approved by VC', 'VC Approved in Principle'])->first();

                                @endphp

                                @if($doc)
                                    <tr data-id="{{ $doc->id }}">
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $doc->doc_id }}</td>
                                        <td><a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">{{ $doc->title }}</a></td>
                                        <td>{{ $doc->priority }}</td>
                                        <td>{{ $doc->from }}</td>
                                        <td>
                                            <!-- <div style="margin-top: 4px;">{{ $doc->approval_status }}</div> -->
                                            <div style="display: flex; align-items: flex-start; justify-content: center; gap: 12px;">
                                                <div style="text-align: center; width: 60px;">
                                                    <span style="font-size: 22px; color: {{ $flagRegistrar ? '#28a745' : '#ccc' }};">
                                                        {!! $flagRegistrar ? '&#10004;' : '&#10003;' !!}
                                                    </span><br>
                                                    <span style="font-size: 12px;">Registrar</span>
                                                </div>
                                                <div style="text-align: center; width: 60px;">
                                                    <span style="font-size: 22px; color: {{ $flagProVC ? '#28a745' : '#ccc' }};">
                                                        {!! $flagProVC ? '&#10004;' : '&#10003;' !!}
                                                    </span><br>
                                                    <span style="font-size: 12px;">ProVC</span>
                                                </div>
                                                <div style="text-align: center; width: 60px;">
                                                    <span style="font-size: 22px; color: {{ $flagVC ? '#28a745' : '#ccc' }};">
                                                        {!! $flagVC ? '&#10004;' : '&#10003;' !!}
                                                    </span><br>
                                                    <span style="font-size: 12px;">VC</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $doc->approval_status }}</td>
                                        <td>{{ $doc->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-primary btn-action mr-1 toggle-accordion" data-toggle="tooltip" title="View" data-target="#accordion-{{ $doc->id }}">
                                                <i class="fas fa-solid fa-eye"></i>
                                            </a>                                               
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
                                        <td colspan="5">
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
                                @endif
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