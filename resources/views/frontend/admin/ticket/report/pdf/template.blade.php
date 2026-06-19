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
            <img src="https://officesuite.vinayakamission.edu.in/assets/img/vm/logo.jpg" alt="Logo" style="width:100%;max-width:420px;height:auto;display:block;margin:0 auto;border-radius:8px;">
          </td>
        </tr>
      </table>
      <table style="width:85%;border-collapse:collapse;margin:0 auto 15px;">
        <tr>
          <td style="padding:5px;border-top:1px solid #ddd; border-bottom:1px solid #ddd; background-color:#fff;text-align: center;">
            <h1 style="margin: 15px auto;">Ticketing Report</h1>
            <p style="margin: 5px auto;">By - {{ Auth::user()->name }}, {{ Auth::user()->department }} - {{ Auth::user()->role }}</p>
            <p style="margin: 5px auto;">{{ Auth::user()->department }} Department</p>
            <p style="font-style: italic; margin-top: 10px;margin-bottom: 20px;">{{ \Carbon\Carbon::parse($from)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}</p>
          </td>
        </tr>
      </table>
      <table style="width:95%;border-collapse:collapse;margin:0 auto">
        <tr>
            <td colspan="5" style="text-align: center; margin: 0;"><h2 style="margin: 10px auto">Tickets Received</h2></td>
        </tr>
        <tr>
            <td style="width:20% ;padding: 10px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $open_tickets }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #d1dbff; color: #000; margin:0; border-radius: 0 0 17px 17px">Open</p>
                </div>
            </td>
            <td style="width:20% ;padding: 10px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $tickets_on_hold }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #ff9191; color: #000; margin:0; border-radius: 0 0 17px 17px">On Hold</p>
                </div>
            </td>
            <td style="width:20% ;padding: 10px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $tickets_in_progress }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #ffe17a; color: #000; margin:0; border-radius: 0 0 17px 17px">In Progreess</p>
                </div>
            </td>
            <td style="width:20% ;padding: 10px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $completed_tickets }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #484948; color: #fff; margin:0; border-radius: 0 0 17px 17px">Completed</p>
                </div>
            </td>
            <td style="width:20% ;padding: 10px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $closed_tickets }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #9be78b; color: #000; margin:0; border-radius: 0 0 17px 17px">Closed</p>
                </div>
            </td>
        </tr>
      </table>
      <table style="width:95%;border-collapse:collapse;margin:0 auto 20px;">
        <tr>
            <td style="padding: 20px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $open_tickets + $closed_tickets + $completed_tickets + $tickets_on_hold + $tickets_in_progress }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #f1d1ff; color: #000; margin:0; border-radius: 0 0 17px 17px">Total Tickets</p>
                </div>
            </td>
        </tr>
      </table>
      <table style="width:90%;border-collapse:separate;margin:20px auto;">
        <thead>
          <tr>
            <th colspan="2" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#fff;font-weight:bold; border-radius: 18px 18px 0 0;">Tickets by Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width:30%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <ul style="text-align: left; font-size:14px">
                <li>Open - {{ $open_tickets }}</li>
                <li>Hold - {{ $tickets_on_hold }}</li>
                <li>In Progress - {{ $tickets_in_progress }}</li>
                <li>Completed - {{ $completed_tickets }}</li>
                <li>Closed - {{ $closed_tickets }}</li>
                <li>Total - {{ $completed_tickets + $open_tickets + $closed_tickets + $tickets_on_hold + $tickets_in_progress }}</li>
              </ul>
              
            </td>
            <td style="width:70%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <p></p>
              <img src="{{ $statusChartImg }}" alt="Chart 1" style="width:100%;max-width:450px;height:auto;display:block;margin:0 auto;">
            </td>
          </tr>
        </tbody>
      </table>
      
    </div>
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
    
      <table style="width:90%;border-collapse:separate;margin:0 auto;">
        <thead>
          <tr>
            <th colspan="2" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#fff;font-weight:bold; border-radius: 18px 18px 0 0;">Tickets by Department</th>
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
                    @if($data['pending'] > 0)
                    <span style="color: red">(Pending - {{ $data['pending'] }})</span>
                    @endif
                  @endif
                @endforeach
              </ul>
              
            </td>
          </tr>
        </tbody>
      </table>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <thead>
          <tr>
            <th colspan="6" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Ticket Details</th>
          </tr>
          <tr>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">S.No</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Title</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">From</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Created</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Due</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Status</th>
          </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
          @foreach($tickets as $ticket)
          <tr>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $i++ }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->title }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->ticket_from }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</td>
            @if($ticket->status == 'Closed')
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">-</td>
            @else
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</td>
            @endif
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->status }}</td>
          </tr>
          @endforeach
          <!-- Add more rows as needed -->
        </tbody>
      </table>
    </div>
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
      <table style="width:95%;border-collapse:collapse;margin:0 auto 20px;">
        <tr>
            <td style="text-align: center; margin: 0;"><h2 style="margin: 10px auto">Tickets Raised</h2></td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center;">
                <div style="border:1px solid #9f9b9b; border-radius: 18px;">
                    <h2>{{ $tickets_raised }}</h2>
                    <p style="font-size: 13px; padding: 10px; background-color: #f1d1ff; color: #000; margin:0; border-radius: 0 0 17px 17px">Tickets Raised</p>
                </div>
            </td>
        </tr>
      </table>
      <table style="width:90%;border-collapse:separate;margin:20px auto;">
        <thead>
          <tr>
            <th colspan="2" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#fff;font-weight:bold; border-radius: 18px 18px 0 0;">Tickets Raised</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width:60%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <p></p>
              <img src="{{ $ticketRaisedImg }}" alt="Chart 1" style="width:100%;max-width:450px;height:auto;display:block;margin:0 auto;">
            </td>
            <td style="width:30%;padding:12px;border:1px solid #ddd;background-color:#fff;text-align: center;border-radius:0 0 18px 18px;">
              <ul style="text-align: left; font-size:14px">
                @foreach($ticketData as $data)
                  @if($data['count'] != 0)
                    <li>{{ $data['name'] }} - {{ $data['count'] }}</li>
                    @if($data['pending'] > 0)
                    <span style="color: red">(Pending - {{ $data['pending'] }})</span>
                    @endif
                  @endif
                @endforeach
              </ul>
              
            </td>
          </tr>
        </tbody>
      </table>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <thead>
          <tr>
            <th colspan="6" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Tickets Raised</th>
          </tr>
          <tr>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">S.No</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Title</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">To</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Created</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Due</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Status</th>
          </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
          @foreach($raised_tickets as $ticket)
          <tr>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $i++ }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->title }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->ticket_to }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</td>
            @if($ticket->status == 'Closed')
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">-</td>
            @else
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ floor(\Carbon\Carbon::parse($ticket->created_at)->diffInDays(\Carbon\Carbon::now(), false)) . ' days'; }}</td>
            @endif
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->status }}</td>
          </tr>
          @endforeach
          <!-- Add more rows as needed -->
        </tbody>
      </table>
    </div>
  </body>
</html>