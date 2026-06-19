<table>
  <thead>
    <tr style="background:#1a3c5e;color:#fff;font-weight:bold;">
      <th>#</th>
      @if(in_array('doc_id', $selectedFields))          <th>Document ID</th>        @endif
      @if(in_array('title', $selectedFields))           <th>Title</th>              @endif
      @if(in_array('subject', $selectedFields))         <th>Subject</th>            @endif
      @if(in_array('from', $selectedFields))            <th>From Department</th>    @endif
      @if(in_array('status', $selectedFields))          <th>Status</th>             @endif
      @if(in_array('approval_status', $selectedFields)) <th>Approval Status</th>    @endif
      @if(in_array('priority', $selectedFields))        <th>Priority</th>           @endif
      @if(in_array('amount', $selectedFields))          <th>Amount</th>             @endif
      @if(in_array('currency', $selectedFields))        <th>Currency</th>           @endif
      @if(in_array('is_payment_involved', $selectedFields)) <th>Payment Involved</th> @endif
      @if(in_array('reference', $selectedFields))       <th>Reference</th>          @endif
      @if(in_array('created_at', $selectedFields))      <th>Created Date</th>       @endif
      @if(in_array('current_approver', $selectedFields))<th>Current Approver</th>   @endif
      @if(in_array('approval_sequence', $selectedFields))<th>Approval Chain</th>    @endif
    </tr>
  </thead>
  <tbody>
    @forelse($docs as $doc)
      @php
        $seq     = json_decode($doc->approval_sequence ?? '[]', true) ?: [];
        $currIdx = $doc->current_sequence_index ?? 0;
        $currApp = $seq[$currIdx] ?? '—';
        $done    = in_array($doc->status, ['Completed', 'Closed']);
        $chainStr = implode(' › ', $seq);
      @endphp
      <tr>
        <td>{{ $loop->iteration }}</td>
        @if(in_array('doc_id', $selectedFields))          <td>{{ $doc->doc_id }}</td>                                                     @endif
        @if(in_array('title', $selectedFields))           <td>{{ $doc->title }}</td>                                                      @endif
        @if(in_array('subject', $selectedFields))         <td>{{ $doc->subject }}</td>                                                    @endif
        @if(in_array('from', $selectedFields))            <td>{{ $doc->from }}</td>                                                       @endif
        @if(in_array('status', $selectedFields))          <td>{{ $doc->status }}</td>                                                     @endif
        @if(in_array('approval_status', $selectedFields)) <td>{{ $doc->approval_status ?? '—' }}</td>                                     @endif
        @if(in_array('priority', $selectedFields))        <td>{{ $doc->priority }}</td>                                                   @endif
        @if(in_array('amount', $selectedFields))          <td>{{ $doc->amount ? number_format($doc->amount, 2) : '—' }}</td>              @endif
        @if(in_array('currency', $selectedFields))        <td>{{ $doc->currency ?? '—' }}</td>                                            @endif
        @if(in_array('is_payment_involved', $selectedFields)) <td>{{ $doc->is_payment_involved == 'Y' ? 'Yes' : 'No' }}</td>             @endif
        @if(in_array('reference', $selectedFields))       <td>{{ $doc->reference ?? '—' }}</td>                                          @endif
        @if(in_array('created_at', $selectedFields))      <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>        @endif
        @if(in_array('current_approver', $selectedFields))<td>{{ $done ? 'Completed' : $currApp }}</td>                                   @endif
        @if(in_array('approval_sequence', $selectedFields))<td>{{ $chainStr }}</td>                                                       @endif
      </tr>
    @empty
      <tr>
        <td colspan="20">No records found.</td>
      </tr>
    @endforelse
  </tbody>
</table>
