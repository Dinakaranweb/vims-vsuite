<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approval Report – {{ $role }}</title>
  <style>
    @page { size: A4 landscape; margin: 10mm; }
    body  { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; margin:0; padding:0; }
    .header { text-align:center; margin-bottom:10px; }
    .header img { max-height:55px; }
    .header h2 { margin:4px 0; font-size:15px; }
    .header p  { margin:2px 0; font-size:10px; color:#555; }
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    th { background:#1a3c5e; color:#fff; padding:6px 5px; font-size:10px; text-align:center; }
    td { border:1px solid #ccc; padding:5px; font-size:10px; vertical-align:middle; }
    tr:nth-child(even) td { background:#f5f8fc; }
    .badge { display:inline-block; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:bold; }
    .b-success  { background:#28a745; color:#fff; }
    .b-danger   { background:#dc3545; color:#fff; }
    .b-warning  { background:#ffc107; color:#000; }
    .b-info     { background:#17a2b8; color:#fff; }
    .b-secondary{ background:#6c757d; color:#fff; }
    .b-primary  { background:#007bff; color:#fff; }
    .b-light    { background:#e9ecef; color:#444; }
    .chain      { display:flex; align-items:center; flex-wrap:wrap; gap:3px; }
    .chain span { font-size:8px; }
    .footer     { text-align:center; font-size:9px; color:#888; margin-top:14px; border-top:1px solid #ddd; padding-top:6px; }
  </style>
</head>
<body>

  <div class="header">
    <img src="https://officesuite.vinayakamission.edu.in/assets/img/vm/logo.jpg" alt="Logo">
    <h2>Approval Report – {{ $role }}</h2>
    <p>Generated: {{ now()->format('d-m-Y h:i A') }}
      @if(request('date_from') || request('date_to'))
        &nbsp;|&nbsp; Period:
        {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d-m-Y') : 'Start' }}
        to
        {{ request('date_to')   ? \Carbon\Carbon::parse(request('date_to'))->format('d-m-Y')   : 'Today' }}
      @endif
      &nbsp;|&nbsp; Total Records: {{ $docs->count() }}
    </p>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:30px">#</th>
        @if(in_array('doc_id', $selectedFields))          <th>Doc ID</th>           @endif
        @if(in_array('title', $selectedFields))           <th>Title</th>            @endif
        @if(in_array('subject', $selectedFields))         <th>Subject</th>          @endif
        @if(in_array('from', $selectedFields))            <th>From Dept</th>        @endif
        @if(in_array('status', $selectedFields))          <th>Status</th>           @endif
        @if(in_array('approval_status', $selectedFields)) <th>Appr. Status</th>     @endif
        @if(in_array('priority', $selectedFields))        <th>Priority</th>         @endif
        @if(in_array('amount', $selectedFields))          <th>Amount</th>           @endif
        @if(in_array('currency', $selectedFields))        <th>Currency</th>         @endif
        @if(in_array('is_payment_involved', $selectedFields)) <th>Payment?</th>    @endif
        @if(in_array('reference', $selectedFields))       <th>Reference</th>        @endif
        @if(in_array('created_at', $selectedFields))      <th>Created</th>          @endif
        @if(in_array('current_approver', $selectedFields))<th>Current Approver</th> @endif
        @if(in_array('approval_sequence', $selectedFields))<th>Approval Chain</th>  @endif
      </tr>
    </thead>
    <tbody>
      @forelse($docs as $doc)
        @php
          $seq      = json_decode($doc->approval_sequence ?? '[]', true) ?: [];
          $currIdx  = $doc->current_sequence_index ?? 0;
          $currApp  = $seq[$currIdx] ?? '—';
          $done     = in_array($doc->status, ['Completed', 'Closed']);
          $scMap    = ['Completed'=>'b-success','Approved'=>'b-success','Closed'=>'b-secondary',
                       'Rejected'=>'b-danger','Hold'=>'b-warning','In Progress'=>'b-info'];
          $sBadge   = $scMap[$doc->status] ?? 'b-primary';
        @endphp
        <tr>
          <td style="text-align:center">{{ $loop->iteration }}</td>
          @if(in_array('doc_id', $selectedFields))
            <td style="text-align:center">{{ $doc->doc_id }}</td>
          @endif
          @if(in_array('title', $selectedFields))
            <td>{{ \Illuminate\Support\Str::limit($doc->title, 55) }}</td>
          @endif
          @if(in_array('subject', $selectedFields))
            <td>{{ \Illuminate\Support\Str::limit($doc->subject, 55) }}</td>
          @endif
          @if(in_array('from', $selectedFields))
            <td>{{ $doc->from }}</td>
          @endif
          @if(in_array('status', $selectedFields))
            <td style="text-align:center"><span class="badge {{ $sBadge }}">{{ $doc->status }}</span></td>
          @endif
          @if(in_array('approval_status', $selectedFields))
            <td style="text-align:center"><span class="badge b-info">{{ $doc->approval_status ?? '—' }}</span></td>
          @endif
          @if(in_array('priority', $selectedFields))
            <td style="text-align:center">
              @if($doc->priority=='High')     <span class="badge b-danger">High</span>
              @elseif($doc->priority=='Medium')<span class="badge b-warning">Medium</span>
              @else                            <span class="badge b-secondary">Low</span>
              @endif
            </td>
          @endif
          @if(in_array('amount', $selectedFields))
            <td style="text-align:right">{{ $doc->amount ? number_format($doc->amount, 2) : '—' }}</td>
          @endif
          @if(in_array('currency', $selectedFields))
            <td style="text-align:center">{{ $doc->currency ?? '—' }}</td>
          @endif
          @if(in_array('is_payment_involved', $selectedFields))
            <td style="text-align:center">
              <span class="badge {{ $doc->is_payment_involved=='Y' ? 'b-warning' : 'b-secondary' }}">
                {{ $doc->is_payment_involved=='Y' ? 'Yes' : 'No' }}
              </span>
            </td>
          @endif
          @if(in_array('reference', $selectedFields))
            <td>{{ $doc->reference ?? '—' }}</td>
          @endif
          @if(in_array('created_at', $selectedFields))
            <td style="text-align:center">{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
          @endif
          @if(in_array('current_approver', $selectedFields))
            <td style="text-align:center">
              <span class="badge {{ $done ? 'b-success' : 'b-primary' }}">
                {{ $done ? 'Done' : $currApp }}
              </span>
            </td>
          @endif
          @if(in_array('approval_sequence', $selectedFields))
            <td>
              <div class="chain">
                @foreach($seq as $i => $step)
                  @php
                    $isDone = $i < $currIdx || $done;
                    $isCur  = $i == $currIdx && !$done && $doc->status !== 'Rejected';
                  @endphp
                  <span class="badge {{ $isDone ? 'b-success' : ($isCur ? 'b-warning' : 'b-light') }}">
                    {{ \Illuminate\Support\Str::limit($step, 12) }}
                  </span>
                  @if(!$loop->last)<span style="color:#ccc">›</span>@endif
                @endforeach
              </div>
            </td>
          @endif
        </tr>
      @empty
        <tr>
          <td colspan="20" style="text-align:center;padding:20px;color:#888">
            No documents found for the selected filters.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    This is a system-generated report from VIMS-Suite &bull; {{ now()->format('d-m-Y H:i') }}
  </div>

</body>
</html>
