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
      .table {
          border-collapse: collapse;
          width: 100%;
      }

      .table th,
        .table td {
          text-align: center;
          padding: 10px;
          font-size: 14px;
      }

      .table-bordered th,
        .table-bordered td {
          border: 1px solid #ddd;
      }
      p{
        font-size: 14px;
      }
      .summernote-content img {
          max-width: 100% !important;
          height: auto !important;
          max-height: 500px !important;
      }
    </style>
  </head>
  <body style="box-sizing:border-box;font-family:'Open Sans', sans-serif;margin:0;padding:0;background-color:#fff;color:#333;">
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
      <table style="width:100%;border-collapse:collapse;margin-bottom:15px;">
        <tr>
          <td style="padding:12px; background-color:#fff;text-align: center;">
          <img src="https://officesuite.vinayakamission.edu.in/assets/img/vm/logo.jpg" alt="Logo" class="logo" style="width:100%;max-width:420px;height:auto;display:block;margin:0 auto;border-radius:8px;">
          </td>
        </tr>
      </table>
      <table style="width:95%;border-collapse:collapse;margin:0 auto 15px;">
        <tr>
            <td colspan="2" style="padding:5px;border-top:1px solid #ddd; border-bottom:1px solid #ddd; background-color:#fff;text-align: center;">
                <h3 style="margin: 15px auto;">{{ $ticket->title }}</h3>
                <h5 style="margin: 15px auto;">VMRF(DU) Ticket ID - {{ $ticket->ticket_id }}</h5>
            </td>
        </tr>
        <tr>
            <td style="width:50%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: left;border-radius:18px;">
                @php
                    $by = App\Models\User::find($ticket->ticket_by);
                @endphp
                <p style="text-align: left;">By - {{ $by->name }}, {{ $by->designation }}</p>
                <p style="text-align: left;">From - {{ $ticket->ticket_from }} Section</p>
                <p style="text-align: left;">Created at - {{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</p>
                <p style="text-align: left;">Priority - {{ $ticket->priority }}</p>

            </td>
            <td style="width:50%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: left;border-radius:18px;">
                @php
                    $to = App\Models\User::find($ticket->assigned_to);
                @endphp
                <p style="text-align: left;">To - {{ $ticket->ticket_to }} Section</p>
                <p style="text-align: left;">Assigned to - {{ $to->name }}, {{ $to->designation }}</p>
                <p style="text-align: left;">Ticket Age - {{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</p>
                <p style="text-align: left;">Status - {{ $ticket->status }}</p>
            </td>
        </tr>
      </table>

      <table style="width:95%;border-collapse:separate;margin:0 auto;">
          <tr>
            <td style="width:100%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: left;border-radius:12px;">
              <p style="font-style:bold; font-size: 18px">{{ $ticket->title }}</p>
              <p style="font-style:bold; font-size: 12px">{{ $by->name }}, {{ $by->designation }} - {{ $ticket->created_at->diffForHumans() }}</p>
              <p>{!! $ticket->description !!}</p>
              @if($ticket->file)
              @php
                    $link = 'https://tickets.vinayakamission.com'.Storage::url($ticket->file);
              @endphp
                  <p>
                      Attachment : <a href="{{ $link }}" target="__blank">{{ basename($ticket->file) }}</a>
                  </p>
                @endif
            </td>
          </tr>
      </table>

      @foreach ($conversation as $convo)
        <table style="width:95%;border-collapse:separate;margin:0 auto;">
            <tr>
              <td style="width:100%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: left;border-radius:12px;">
                <p style="font-style:bold; font-size: 18px">{{ $convo->by }} - <span style="font-style:bold; font-size: 12px">{{ $convo->created_at->diffForHumans() }}</span></p>
                <div class="summernote-content">
                  <p>  {!! $convo->description !!} </p>
                </div>
                @if($convo->file)  
                @php
                    $convo_link = 'https://tickets.vinayakamission.com'.Storage::url($convo->file);
                @endphp
                  <p>
                      Attachment : <a href="{{ $convo_link }}" target="__blank">{{ basename($convo->file) }}</a>
                  </p>
                @endif
              </td>
            </tr>
        </table>
      @endforeach

      @if($ticket->status == 'Closed')
        <table style="width:95%;border-collapse:separate;margin:0 auto;">
          <tr>
            <td style="width:100%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: left;border-radius:12px;">
              <p style="font-style:bold; font-size: 18px">Ticket Closed by {{ App\Models\User::find($ticket->closed_by)->name }} - <span style="font-style:bold; font-size: 12px">{{ $convo->created_at->diffForHumans() }}</span></p>
              <div style="text-align: center; font-family: DejaVu Sans;">
                <p>Performance rating for the ticket</p>
                @for ($i = 1; $i <= 5; $i++)
                  <span style="display: inline-block; font-size: 2rem; color: #ccc; transition: color 0.2s; {{ $i <= $ticket->rating ? 'color: #f00;' : '' }}">&#x2605;</span>
                @endfor
              </div>
            </td>
          </tr>
        </table>
      @endif

    </div>
  </body>
</html>