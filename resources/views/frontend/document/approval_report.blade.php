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

    <div class="main-content">
      <section class="section">

        <div class="section-header">
          <h1>Approval Report</h1>
          <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Approval Report</div>
          </div>
        </div>

        <div class="section-body">

          {{-- ── Filter Card ── --}}
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex align-items-center">
              <i class="fas fa-filter mr-2"></i>
              <h4 class="mb-0">Report Filters</h4>
            </div>
            <div class="card-body">
              <form method="GET" action="{{ route('approval-report') }}" id="filterForm">

                <div class="row">
                  {{-- Role --}}
                  <div class="col-md-3 col-sm-6 mb-3">
                    <label class="font-weight-bold">Approval Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-control" required>
                      <option value="">-- Select Role --</option>
                      @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                          {{ $role }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  {{-- Status --}}
                  <div class="col-md-3 col-sm-6 mb-3">
                    <label class="font-weight-bold">Document Status</label>
                    <select name="status" class="form-control">
                      <option value="">-- All Statuses --</option>
                      @foreach(['Approved','Rejected','Completed','Closed','In Progress','Hold','Pending'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                      @endforeach
                    </select>
                  </div>

                  {{-- From Date --}}
                  <div class="col-md-3 col-sm-6 mb-3">
                    <label class="font-weight-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control"
                           value="{{ request('date_from') }}">
                  </div>

                  {{-- To Date --}}
                  <div class="col-md-3 col-sm-6 mb-3">
                    <label class="font-weight-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control"
                           value="{{ request('date_to') }}">
                  </div>
                </div>

                {{-- Field Selection --}}
                <div class="row mb-3">
                  <div class="col-12">
                    <label class="font-weight-bold d-block mb-2">
                      <i class="fas fa-columns mr-1"></i> Select Columns to Display
                    </label>
                    <div class="border rounded p-3 bg-light">
                      <div class="row">
                        @foreach($allFields as $key => $label)
                          <div class="col-md-3 col-sm-4 col-6 mb-2">
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input field-checkbox"
                                     id="field_{{ $key }}" name="fields[]" value="{{ $key }}"
                                     {{ in_array($key, $selectedFields) ? 'checked' : '' }}>
                              <label class="custom-control-label" for="field_{{ $key }}">
                                {{ $label }}
                              </label>
                            </div>
                          </div>
                        @endforeach
                      </div>
                      <div class="mt-2">
                        <a href="#" id="selectAll" class="btn btn-outline-secondary btn-sm mr-2">
                          <i class="fas fa-check-square mr-1"></i> Select All
                        </a>
                        <a href="#" id="clearAll" class="btn btn-outline-secondary btn-sm">
                          <i class="far fa-square mr-1"></i> Clear All
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-2">
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search mr-1"></i> Generate Report
                  </button>

                  @if(request()->filled('role'))
                  <a href="{{ route('approval-report.pdf') }}?{{ http_build_query(request()->except('_token')) }}"
                     class="btn btn-danger mr-2" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Download PDF
                  </a>
                  <a href="{{ route('approval-report.excel') }}?{{ http_build_query(request()->except('_token')) }}"
                     class="btn btn-success mr-2">
                    <i class="fas fa-file-excel mr-1"></i> Download Excel
                  </a>
                  @endif

                  <a href="{{ route('approval-report') }}" class="btn btn-secondary">
                    <i class="fas fa-redo mr-1"></i> Reset
                  </a>
                </div>

              </form>
            </div>
          </div>

          {{-- ── Results Card ── --}}
          @if(request()->filled('role'))
          <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 class="mb-0">
                <i class="fas fa-table mr-2"></i>
                Results for <strong>{{ request('role') }}</strong>
                <span class="badge badge-primary ml-2">{{ $docs->count() }} record(s)</span>
              </h4>
              <div class="text-muted small">
                @if(request('date_from') || request('date_to'))
                  <i class="fas fa-calendar-alt mr-1"></i>
                  {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d M Y') : 'Start' }}
                  &mdash;
                  {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d M Y') : 'Today' }}
                @endif
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0" id="reportTable">
                  <thead class="thead-light">
                    <tr>
                      <th style="width:45px">#</th>
                      @if(in_array('doc_id', $selectedFields))         <th>Doc ID</th>             @endif
                      @if(in_array('title', $selectedFields))          <th>Title</th>              @endif
                      @if(in_array('subject', $selectedFields))        <th>Subject</th>            @endif
                      @if(in_array('from', $selectedFields))           <th>From Dept</th>          @endif
                      @if(in_array('status', $selectedFields))         <th>Status</th>             @endif
                      @if(in_array('approval_status', $selectedFields))<th>Appr. Status</th>       @endif
                      @if(in_array('priority', $selectedFields))       <th>Priority</th>           @endif
                      @if(in_array('amount', $selectedFields))         <th>Amount</th>             @endif
                      @if(in_array('currency', $selectedFields))       <th>Currency</th>           @endif
                      @if(in_array('is_payment_involved', $selectedFields)) <th>Payment?</th>     @endif
                      @if(in_array('reference', $selectedFields))      <th>Reference</th>          @endif
                      @if(in_array('created_at', $selectedFields))     <th>Created</th>            @endif
                      @if(in_array('current_approver', $selectedFields))<th>Current Approver</th>  @endif
                      @if(in_array('approval_sequence', $selectedFields))<th>Approval Chain</th>   @endif
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($docs as $doc)
                      @php
                        $seq     = json_decode($doc->approval_sequence ?? '[]', true) ?: [];
                        $currIdx = $doc->current_sequence_index ?? 0;
                        $currApprover = $seq[$currIdx] ?? '—';
                      @endphp
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        @if(in_array('doc_id', $selectedFields))
                          <td><a href="{{ url('/view/document/'.$doc->id) }}" target="_blank">{{ $doc->doc_id }}</a></td>
                        @endif
                        @if(in_array('title', $selectedFields))
                          <td>{{ Str::limit($doc->title, 60) }}</td>
                        @endif
                        @if(in_array('subject', $selectedFields))
                          <td>{{ Str::limit($doc->subject, 60) }}</td>
                        @endif
                        @if(in_array('from', $selectedFields))
                          <td>{{ $doc->from }}</td>
                        @endif
                        @if(in_array('status', $selectedFields))
                          <td>
                            @php
                              $sc = ['Completed'=>'success','Approved'=>'success','Closed'=>'secondary',
                                     'Rejected'=>'danger','Hold'=>'warning','In Progress'=>'info','Draft'=>'light'];
                              $cls = $sc[$doc->status] ?? 'primary';
                            @endphp
                            <span class="badge badge-{{ $cls }}">{{ $doc->status }}</span>
                          </td>
                        @endif
                        @if(in_array('approval_status', $selectedFields))
                          <td><span class="badge badge-info">{{ $doc->approval_status ?? '—' }}</span></td>
                        @endif
                        @if(in_array('priority', $selectedFields))
                          <td>
                            @if($doc->priority == 'High')   <span class="badge badge-danger">High</span>
                            @elseif($doc->priority == 'Medium') <span class="badge badge-warning">Medium</span>
                            @else <span class="badge badge-secondary">Low</span>
                            @endif
                          </td>
                        @endif
                        @if(in_array('amount', $selectedFields))
                          <td>{{ $doc->amount ? number_format($doc->amount, 2) : '—' }}</td>
                        @endif
                        @if(in_array('currency', $selectedFields))
                          <td>{{ $doc->currency ?? '—' }}</td>
                        @endif
                        @if(in_array('is_payment_involved', $selectedFields))
                          <td>
                            @if($doc->is_payment_involved == 'Y')
                              <span class="badge badge-warning">Yes</span>
                            @else
                              <span class="badge badge-secondary">No</span>
                            @endif
                          </td>
                        @endif
                        @if(in_array('reference', $selectedFields))
                          <td>{{ $doc->reference ?? '—' }}</td>
                        @endif
                        @if(in_array('created_at', $selectedFields))
                          <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
                        @endif
                        @if(in_array('current_approver', $selectedFields))
                          <td>
                            @if(in_array($doc->status, ['Completed','Closed']))
                              <span class="badge badge-success">Done</span>
                            @else
                              <span class="badge badge-primary">{{ $currApprover }}</span>
                            @endif
                          </td>
                        @endif
                        @if(in_array('approval_sequence', $selectedFields))
                          <td>
                            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;">
                              @foreach($seq as $i => $step)
                                @php
                                  $done = $i < $currIdx || in_array($doc->status,['Completed','Closed']);
                                  $cur  = $i == $currIdx && !in_array($doc->status,['Completed','Closed','Rejected']);
                                @endphp
                                <span class="badge badge-{{ $done ? 'success' : ($cur ? 'warning' : 'light') }}"
                                      style="{{ !$done && !$cur ? 'color:#666' : '' }}"
                                      title="{{ $step }}">
                                  {{ Str::limit($step, 12) }}
                                </span>
                                @if(!$loop->last)<span style="color:#ccc;font-size:10px;">›</span>@endif
                              @endforeach
                            </div>
                          </td>
                        @endif
                      </tr>
                    @empty
                      <tr>
                        <td colspan="20" class="text-center text-muted py-4">
                          <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                          No documents found for the selected filters.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

        </div>
      </section>
    </div>

    @include('frontend.body.footer')
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    // DataTable
    if ($('#reportTable tbody tr').length > 1) {
        $('#reportTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            dom: '<"d-flex justify-content-between align-items-center mb-2"lf>rtip',
            language: { search: '<i class="fas fa-search"></i> ' }
        });
    }

    // Select/Clear all checkboxes
    $('#selectAll').on('click', function (e) {
        e.preventDefault();
        $('.field-checkbox').prop('checked', true);
    });
    $('#clearAll').on('click', function (e) {
        e.preventDefault();
        $('.field-checkbox').prop('checked', false);
    });
});
</script>
@endpush
@endsection
