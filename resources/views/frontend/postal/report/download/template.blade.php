<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Report</title>
    <style type="text/css">
      @page {
        size: A4;
        margin: 10mm;
      }
    </style>
  </head>
  <body style="box-sizing:border-box;font-family:'Open Sans', sans-serif;margin:0;padding:0;background-color:#fff;color:#333;">
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
      <table style="width:100%;border-collapse:collapse;margin-bottom:15px;">
        <tr>
          <td style="padding:12px; background-color:#fff;text-align: center;">
            <img src="https://vmrfdu.edu.in/img/VMC-header.jpg" alt="Logo" style="width:100%;max-width:420px;height:auto;display:block;margin:0 auto;border-radius:8px;">
          </td>
        </tr>
      </table>
      <table style="width:85%;border-collapse:collapse;margin:0 auto 15px;">
        <tr>
          <td style="padding:5px;border-top:1px solid #ddd; border-bottom:1px solid #ddd; background-color:#fff;text-align: center;">
            <h1 style="margin: 15px auto;">Postals Received Today</h1>
            <p style="margin: 5px auto;">By - {{ Auth::user()->name }}, {{ Auth::user()->department }} - {{ Auth::user()->role }}</p>
            <p style="font-style: italic; margin-top: 10px;margin-bottom: 20px;">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
          </td>
        </tr>
      </table>
      <table style="width:90%;border-collapse:separate;margin:0 auto;">
        <thead>
          <tr>
            <th colspan="2" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#fff;font-weight:bold; border-radius: 18px 18px 0 0;">Post Received</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width:70%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <p></p>
              <img src="{{ $departmentsChartImg }}" alt="Chart 1" style="width:100%;max-width:450px;height:auto;display:block;margin:0 auto;">
            </td>
            <td style="width:30%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <ul style="text-align: left; font-size:14px">
                @foreach($departmentData  as $data)
                  @if($data['count'] != 0)
                    <li>{{ $data['name'] }} - {{ $data['count'] }}</li> 
                  @endif
                @endforeach
              </ul>
              
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
    
      <table style="width:100%;border-collapse:collapse;table-layout:fixed;margin-bottom:20px;">
        <thead>
          <tr>
            <th colspan="3" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Post Details</th>
          </tr>
          <tr>
            <th style="width:10%;padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">S.No</th>
            <th style="width:45%;padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;word-wrap:break-word;white-space:normal;">From</th>
            <th style="width:45%;padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;word-wrap:break-word;white-space:normal;">To</th>
          </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
          @foreach($posts as $post)
          <tr>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $i++ }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">
                {{ $post->post_id }}<br>
                <b>{{ $post->sent_by }}</b><br>
                {!! $post->post_from_address !!}<br>
                @if($post->dde_paid_amount)
                Amount Paid : {{ $post->dde_paid_amount }}<br>
                Payment Mode : {{ $post->dde_payment_mode }}<br>
                @endif
            </td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">
                @php
                    if($post->staff_name){
                        $to = App\Models\User::find($post->staff_name);
                        $to = $to->name;
                    }else{
                        $to = "";
                    }
                @endphp
                <b>{{ $to }}<br>
                {{ $post->sent_to }}<br></b>
                Status - {{ $post->status }}<br>
                @php
                    if($post->delivered_by){
                        $del = App\Models\User::find($post->delivered_by);
                        $del = $del->name;
                    }else{
                        $del = "Not Yet Delivered";
                    }
                @endphp
                Delivered by - {{ $del }}
            </td>
          </tr>
          @endforeach
          <!-- Add more rows as needed -->
        </tbody>
      </table>
    </div>
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
      <table style="width:85%;border-collapse:collapse;margin:0 auto 15px;">
        <tr>
          <td style="padding:5px;border-top:1px solid #ddd; border-bottom:1px solid #ddd; background-color:#fff;text-align: center;">
            <h1 style="margin: 15px auto;">Postals Sent Today</h1>
            <p style="margin: 5px auto;">By - {{ Auth::user()->name }}, {{ Auth::user()->department }} - {{ Auth::user()->role }}</p>
            <p style="font-style: italic; margin-top: 10px;margin-bottom: 20px;">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
          </td>
        </tr>
      </table>
      <table style="width:90%;border-collapse:separate;margin:0 auto;">
        <thead>
          <tr>
            <th colspan="2" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#fff;font-weight:bold; border-radius: 18px 18px 0 0;">Post Sent</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width:70%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <p></p>
              <img src="{{ $opImg }}" alt="Chart 1" style="width:100%;max-width:450px;height:auto;display:block;margin:0 auto;">
            </td>
            <td style="width:30%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <ul style="text-align: left; font-size:14px">
                @foreach($opData  as $data)
                  @if($data['count'] != 0)
                    <li>{{ $data['name'] }} - {{ $data['count'] }}</li> 
                  @endif
                @endforeach
              </ul>
              
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
    
      <table style="width:100%;border-collapse:collapse;table-layout:fixed;margin-bottom:20px;">
        <thead>
          <tr>
            <th colspan="3" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Post Details</th>
          </tr>
          <tr>
            <th style="width:10%;:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">S.No</th>
            <th style="width:45%;padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;word-wrap:break-word;white-space:normal;">From</th>
            <th style="width:45%;padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;word-wrap:break-word;white-space:normal;">To</th>
          </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
          @foreach($rps as $post)
          <tr>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $i++ }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">
                ID: {{ $post->post_id }}<br>
                <b>{{ App\Models\User::find($post->reply_by)->name }}</b><br>
                {{ $post->reply_from }}<br>
                {!! $post->reply_from_address !!}<br>
            </td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">
                @php
                    if($post->staff_name){
                        $to = App\Models\User::find($post->staff_name);
                        $to = $to->name;
                    }else{
                        $to = "";
                    }
                @endphp
                <b>{{ $post->reply_to }}<br>
                {!! $post->reply_to_address !!}<br></b>
                Status - {{ $post->status }}<br>
                Vendor - {{ $post->vendor }}<br>
                Tracking ID - {{ $post->tracking_id }}
            </td>
          </tr>
          @endforeach
          <!-- Add more rows as needed -->
        </tbody>
      </table>
    </div>
  </body>
</html>