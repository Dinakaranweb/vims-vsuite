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
            <p style="margin: 5px auto;">By - {{ Auth::user()->department }}</p>
            <p style="font-style: italic; margin-top: 10px;margin-bottom: 20px;">{{ \Carbon\Carbon::parse($from)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}</p>
          </td>
        </tr>
      </table>
      <table style="width:95%;border-collapse:collapse;margin:0 auto">
        <tr>
            <td colspan="5" style="text-align: center; margin: 0;"><h2 style="margin: 10px auto">Tickets Summary</h2></td>
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
                    <h2>{{ $open_tickets + $completed_tickets + $closed_tickets + $tickets_on_hold + $tickets_in_progress }}</h2>
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
                <li>Total - {{ $open_tickets + $completed_tickets + $closed_tickets + $tickets_on_hold + $tickets_in_progress }}</li>
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
    @if($reportData != Null)
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
    
      
      <!--<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <thead>
            <tr>
                <th colspan="{{ count($departments) + 2 }}" style="font-size:15px ; padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Tickets Received</th>
            </tr>
            <tr>
                <th style="min-width: 25px; font-size:11px ; padding:8px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;"></th>
                @foreach ($departments as $department)
                    <th style="min-width: 25px; font-size:11px; padding:8px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">{{ $department->dept_label }}</th>
                @endforeach
                <th style="min-width: 25px; font-size:11px; padding:8px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Total Raised</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($departments as $fromDepartment)
              <tr>
                  <th style="padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#f5f5f5;">{{ $fromDepartment->dept_label }}</th>
                  @php
                      $rowRTotal = 0; // Initialize row R-total
                      $rowPTotal = 0; // Initialize row P-total
                  @endphp
                  @foreach ($departments as $toDepartment)
                      @php
                          $found = false;
                          $count = 0;
                          $pending = 0;
                          foreach ($reportData as $data) {
                              if ($data['name'] == $fromDepartment->dept_label && $data['to'] == $toDepartment->dept_label) {
                                  $count = $data['count'];
                                  $pending = $data['pending'];
                                  $found = true;
                                  break;
                              }
                          }
                          if ($found) {
                              echo "<td style='padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#fff;'>R - $count <br/><span style='color:red'>P - $pending</span></td>";
                              $rowRTotal += $count; // Add to row R-total
                              $rowPTotal += $pending; // Add to row P-total
                          } else {
                              echo "<td style='padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#fff;'>-</td>";
                          }
                      @endphp
                  @endforeach
                  <td style="padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#fff;">R - {{ $rowRTotal }} <br/><span style='color:red'>P - {{ $rowPTotal }}</span></td> ->
              </tr>
          @endforeach
          <tr>
              <th style="padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#f5f5f5;">Total Received</th> 
              @php
                  $grandRTotal = 0; // Initialize grand R-total
                  $grandPTotal = 0; // Initialize grand P-total
              @endphp
              @foreach ($departments as $department)
                  @php
                      $colRTotal = 0; // Initialize column R-total
                      $colPTotal = 0; // Initialize column P-total
                      foreach ($reportData as $data) {
                          if ($data['to'] == $department->dept_label) {
                              $colRTotal += $data['count'];
                              $colPTotal += $data['pending'];
                          }
                      }
                      $grandRTotal += $colRTotal; // Add to grand R-total
                      $grandPTotal += $colPTotal; // Add to grand P-total
                  @endphp
                  <td style="padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#fff;">R - {{ $colRTotal }} <br/><span style='color:red'>P - {{ $colPTotal }}</span></td> 
              @endforeach
              <td style="padding:8px;text-align:center; font-size:11px ;border:1px solid #ddd;background-color:#fff;">R - {{ $grandRTotal }} <br/><span style='color:red'>P - {{ $grandPTotal }}</span></td> 
          </tr>
        </tbody>
      </table> -->
    </div>
    @endif
    <div class="container" style="width:95%;max-width:1000px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:8px; page-break-inside: avoid;">
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <thead>
          <tr>
            <th colspan="7" style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Ticket Details</th>
          </tr>
          <tr>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">S.No</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">Title</th>
            <th style="padding:12px;text-align:center;border:1px solid #ddd;background-color:#f5f5f5;font-weight:bold;">From</th>
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
          @foreach($tickets as $ticket)
          <tr>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $i++ }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->title }}</td>
            <td style="padding:12px;text-align:center; font-size:13px ;border:1px solid #ddd;background-color:#fff;">{{ $ticket->ticket_from }}</td>
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