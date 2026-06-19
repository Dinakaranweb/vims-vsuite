<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <style>
    @page {
      size: A4;
      margin: 15mm;
    }

    body {
      font-family: "DejaVu Sans", Arial, sans-serif;
      font-size: 14px;
      margin: 0;
      padding: 0;
      background-color: #fff;
      color: #333;
      line-height: 1.3;
    }

    * {
      box-sizing: border-box;
    }

    p, div, td, th, span {
      font-size: 14px !important;
      line-height: 1.3;
      word-break: break-word;
      white-space: normal;
    }

    a {
      word-break: break-all;
      color: #2a6d98;
      text-decoration: none;
    }

    .container {
      width: 95%;
      max-width: 1000px;
      margin: 0 auto 20px auto;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      /* Removed page-break-after: always */
    }
    
    .section {
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 18px;
      padding: 15px;
      background-color: #fff;
      /* More conservative page break handling */
      page-break-inside: auto;
      page-break-after: auto;
      page-break-before: avoid;
    }

    /* Prevent sections from breaking too early */
    .section h3 {
      text-align: center;
      margin-top: 0;
      margin-bottom: 10px;
      font-size: 16px;
      page-break-after: avoid;
    }

    .header {
      text-align: center;
      margin-bottom: 15px;
    }

    .header img {
      width: 300px;
      height: auto;
      display: block;
      margin: 0 auto;
      border-radius: 8px;
    }

    .title-block {
      text-align: center;
      margin-bottom: 15px;
    }

    .title-block h3 {
      margin-bottom: 8px;
      font-size: 16px;
    }

    .box {
      border: 2px solid #2a6d98;
      border-radius: 8px;
      padding: 12px;
      margin: 15px 0;
      background-color: #f8fafc;
    }

    .log-entry {
      text-align: center;
      padding: 12px;
      border-radius: 12px;
      border: 1px solid #ddd;
      margin-bottom: 8px;
      /* Allow log entries to break if necessary */
      page-break-inside: auto;
    }

    /* Keep log entries together when they fit, but allow breaking */
    .log-entry:not(:has(img)) {
      page-break-inside: avoid;
    }

    /* Allow breaking if log entry contains large images */
    .log-entry:has(img) {
      page-break-inside: auto;
    }

    .log-status {
      text-align: center;
      margin-bottom: 6px;
    }

    .log-message {
      text-align: left !important;
      margin: 6px 0;
    }

    .log-message * {
      text-align: left !important;
    }

    .log-message p {
      text-align: left !important;
      margin-bottom: 6px;
    }

    .log-message div {
      text-align: left !important;
    }

    .log-timestamp {
      text-align: center;
      margin-top: 6px;
    }

    p[style*="font-size"] {
      font-size: 13px !important;
    }

    /* ===== PDF Safe Wrapping for Description ===== */
    .description-content * {
      white-space: normal !important;
      word-break: break-word !important;
      overflow-wrap: break-word !important;
      text-align: left !important;
    }

    .description-content a {
      word-break: break-all !important;
    }
    
    /* Reduce gaps inside description for PDF */
    .description-content p {
      margin-top: 0 !important;
      margin-bottom: 4px !important;
      line-height: 1.2 !important;
      text-align: left !important;
    }
    
    .description-content br + br {
      display: none; /* Remove double line breaks */
    }

    .description-content pre {
      white-space: pre-wrap !important;
      word-wrap: break-word !important;
      text-align: left !important;
    }

    .description-content table {
      table-layout: fixed !important;
      width: 100% !important;
    }

    .description-content td,
    .description-content th {
      word-break: break-word !important;
      text-align: left !important;
    }

    /* Auto-resize images in description */
    .description-content img {
      max-width: 100% !important;
      height: auto !important;
      display: block;
      max-height: 200px !important; /* Limit image height */
    }

    /* Limit images in log messages */
    .log-message img {
      max-width: 100% !important;
      max-height: 350px !important;
      height: auto !important;
      display: block;
      margin: 4px auto;
    }

    /* Remove text-align center from any element containing Summernote content */
    [style*="text-align:center"],
    [style*="text-align: center"] {
      text-align: left !important;
    }

    /* Better page break control for specific sections */
    .approval-log-section {
      page-break-before: auto;
    }

    /* Force sections to stay together when they're small */
    .section:has(> :nth-child(1):last-child) {
      page-break-inside: avoid;
    }

    /* Allow breaking for sections with multiple items */
    .section:has(> *:nth-child(3)) {
      page-break-inside: auto;
    }
    
  </style>
</head>
<body>

  <div class="container">

    <div class="header">
      <img src="https://officesuite.vinayakamission.edu.in/assets/img/vm/logo.jpg" alt="VMC Logo">
    </div>

    <div class="title-block">
      <h3>{{ $doc->title }}</h3>
      <p>By - {{ $user->name }}, {{ $user->department }} - {{ $user->designation }}</p>
      <p style="font-style: italic;">{{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
      <p><b>Report as on:</b> {{ now()->format('d-m-Y h:i a') }}</p>
    </div>

    <div class="section">
      <h3>Document Details</h3>
      <p><b>Document ID:</b> {{ $doc->doc_id }}</p>
      <p><b>Sent by:</b> {{ App\Models\User::find($doc->by)->name }}, {{ App\Models\User::find($doc->by)->department }}</p>
      <p><b>Subject:</b> {!! $doc->subject !!}</p>

      @if($doc->amount)
        <div class="box">
          
          <p><b>Requested Amount:</b> {{ isset($doc->currency) ? $doc->currency : '₹' }} 
                @if(is_numeric($doc->amount))
                    {{ indianCurrencyFormat($doc->amount) }}
                @else
                    {{ $doc->amount }}
                @endif
            /-</p>
            
            <p><b>Recommended Amount:</b> {{ isset($doc->currency) ? $doc->currency : '₹' }} 
                @if(is_numeric($doc->recommended_amount))
                    {{ indianCurrencyFormat($doc->recommended_amount) }}
                @else
                    {{ $doc->recommended_amount }}
                @endif
            /-</p>
            
            <p><b>Sanctioned Amount:</b> {{ isset($doc->currency) ? $doc->currency : '₹' }} 
                @if(is_numeric($doc->sanctioned_amount))
                    {{ indianCurrencyFormat($doc->sanctioned_amount) }}
                @else
                    {{ $doc->sanctioned_amount }}
                @endif
            /-</p>
            
            @php 
                $totalPaid = is_numeric($pay->sum('paid_amount', 2)) ? $pay->sum('paid_amount', 2) : 0; 
            @endphp
            <p><b>Paid Amount:</b> {{ isset($doc->currency) ? $doc->currency : '₹' }} {{ indianCurrencyFormat($totalPaid) }}/-</p>
          

          @if($doc->payment_mode)
            <p><b>Payment Mode:</b>
              @switch($doc->payment_mode)
                @case('cash') Cash @break
                @case('cheque') Cheque @break
                @case('bank') NEFT/RTGS @break
                @case('upi') UPI @break
                @default {{ ucfirst($doc->payment_mode) }}
              @endswitch
            </p>

            <p><b>Payment Details:</b><br>
              @if($doc->payment_mode == 'cheque')
                In favour of: {{ $doc->cash_in_favour ?? '-' }}
              @elseif($doc->payment_mode == 'bank')
                Name: {{ $doc->account_holder ?? '-' }}<br>
                Account Number: {{ $doc->account_number ?? '-' }}<br>
                IFSC: {{ $doc->ifsc_code ?? '-' }}<br>
                Branch: {{ $doc->account_branch ?? '-' }}
              @elseif($doc->payment_mode == 'upi')
                UPI ID: {{ $doc->upi_id ?? '-' }}
              @else
                NA
              @endif
            </p>
          @endif
        </div>
      @endif

      @if($annexures)
        @php $no = 1; @endphp
        @foreach($annexures as $attachment)
          @php $link = 'https://officesuite.vinayakamission.edu.in' . Storage::url($attachment->annexure); @endphp
          <p><b>Annexure {{ $no++ }}:</b> <a href="{{ $link }}" target="_blank">{{ basename($attachment->annexure) }}</a></p>
        @endforeach
      @endif

      <p><b>Date:</b> {{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</p>
    </div>

    <div class="section">
      <h3>Document Description</h3>
      <p><b>Description:</b></p>
      <div class="description-content">
            {!! preg_replace([
                '/style="[^"]*font-size[^"]*"/i', // remove font-size styles
                '/(&nbsp;|\s){2,}/',              // collapse multiple spaces & non-breaking spaces
                '/<br\s*\/?>\s*<br\s*\/?>/i'      // remove double <br>
            ], [
                '',
                ' ',
                '<br>'
            ], $doc->description) !!}
        </div>
    </div>

    <div class="section">
      <h3>Approval Status</h3>
      <p><b>Document Status:</b> {{ $doc->status }}</p>

      @php
        $vc_approval = DB::table('approval_log')->where('doc_id', $doc->id)->where('status', 'Approved by VC')->first();
        $provc_approval = DB::table('approval_log')->where('doc_id', $doc->id)->where('status', 'Approved by Pro VC')->first();
        $registrar_approval = DB::table('approval_log')->where('doc_id', $doc->id)->where('status', 'Approved by Registrar')->first();
      @endphp

      @if($registrar_approval)
        <p style="color: #19b91f;"><b>Registrar Approval:</b> {{ date('d/m/Y h:i:s A', strtotime($registrar_approval->created_at)) }}</p>
      @endif
      @if($provc_approval)
        <p style="color: green;"><b>Pro VC Approval:</b> {{ date('d/m/Y h:i:s A', strtotime($provc_approval->created_at)) }}</p>
      @endif
      @if($vc_approval)
        <p style="color: red;"><b>VC Approval:</b> {{ date('d/m/Y h:i:s A', strtotime($vc_approval->created_at)) }}</p>
      @endif
    </div>

    @foreach($pay as $paymentDetails)
      <div class="section">
        <h3>Payment Details</h3>
        <p><b>Payment Mode:</b> {{ $paymentDetails->mode ?? '-' }}</p>
        <p><b>Payment Type:</b> {{ $paymentDetails->payment_type ?? '-' }}</p>
        <p><b>Reference No:</b> {{ $paymentDetails->payment_reference_no ?? '-' }}</p>
        <p><b>Payment Date:</b> {{ $paymentDetails->payment_date ? date('d/m/Y', strtotime($paymentDetails->payment_date)) : '-' }}</p>
        <p><b>Paid Amount:</b> {{ isset($doc->currency) ? $doc->currency : '₹' }} {{ indianCurrencyFormat($paymentDetails->paid_amount ?? 0) }}</p>
        <p><b>Expenditure ID:</b> {{ $paymentDetails->expenditure_id ?? '-' }}</p>
        <p><b>Remark:</b> {{ $paymentDetails->remarks ?? '-' }}</p>
      </div>
    @endforeach

    <div class="section">
      <h3>Approval Log</h3>
      @foreach ($approval_logs as $log)
        <div class="log-entry">
          <div class="log-status">
            <p><b>{{ $log->status }}</b></p>
          </div>
          <div class="log-message">
            {!! preg_replace([
                '/style="[^"]*text-align[^"]*"/i', // remove text-align styles
                '/(&nbsp;|\s){2,}/',               // collapse multiple spaces & non-breaking spaces
                '/<br\s*\/?>\s*<br\s*\/?>/i'       // remove double <br>
            ], [
                '',
                ' ',
                '<br>'
            ], $log->message) !!}
          </div>
          <div class="log-timestamp">
            <p>{{ date('d/m/Y h:i A', strtotime($log->created_at)) }}</p>
          </div>
        </div>
      @endforeach
    </div>

  </div>

</body>
</html>