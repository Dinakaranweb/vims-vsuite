<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In Progress Documents Report</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Open Sans', sans-serif; background: #fff; color: #333; margin: 0; padding: 0; }
        .container { width: 95%; max-width: 1000px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; page-break-inside: avoid; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background: #f9f9f9; font-weight: bold; }
        h1, h2 { margin: 10px 0; }
        .approval-status { display: flex; gap: 12px; justify-content: center; }
        .approval-status div { text-align: center; width: 60px; }
        .approval-status span { font-size: 22px; }
        .approval-log { margin-top: 10px; }
        .approval-log-entry { border: 1px solid #eee; border-radius: 8px; padding: 10px; margin-bottom: 8px; }
        .page-break { page-break-before: always; }
    </style>
    
</head>
<body>
    <div class="container">
        <table>
            <tr>
                <td style="text-align: center; background: #fff;">
                    <img src="https://officesuite.vinayakamission.edu.in/assets/img/vm/logo.jpg" alt="Logo" style="width:100%;max-width:420px;height:auto;display:block;margin:0 auto;border-radius:8px;">
                </td>
            </tr>
        </table>
        <h1 style="text-align:center;">Pending Documents</h1>
        <p style="text-align:center;">Report generated on: {{ now()->format('d-m-Y h:i a') }}</p>
    </div>

        <table class="table table-striped" id="table-1" style="text-align: center; width: 100%; font-family: DejaVu Sans;">
            <thead>
                <tr>
                    <th style="width:5%">S.No</th>
                    <th style="width:15%">DOC ID</th>
                    <th style="width:20%">Title</th>
                    <th style="width:10%">From</th>
                    <th style="width:10%">Current</th>
                    <th style="width:25%">Approval Status</th>
                    <th style="width:15%">Created</th>
                </tr>
            </thead>
            <tbody>
            @foreach($docs as $doc)
            
            @php
                $approvals = $approvalLogs[$doc->id] ?? collect();

                $flagRegistrar = $approvals->firstWhere('status', 'Approved by Registrar');
                $flagProVC = $approvals->firstWhere('status', 'Approved by Pro VC');
                $flagVC = $approvals->firstWhere(fn($log) => in_array($log->status, ['Approved by VC', 'VC Approved in Principle']));
            @endphp

                <tr style="page-break-inside: avoid;">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $doc->doc_id }}</td>
                    <td>
                        <a href="{{ url('/view/document/'.$doc->id) }}" style="color: #1e1e1e">
                            {{ $doc->title }}
                        </a>
                    </td>
                    <td>{{ $doc->from }}</td>
                    <td>{{ $doc->forwarded_to }}</td>
                    <td>
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="text-align:center; width:33%;">
                                    <span style="font-size: 28px; color: {{ $flagRegistrar ? '#19b91f' : '#bbb' }};">
                                        &#10004;
                                    </span>
                                </td>
                                <td style="text-align:center; width:33%;">
                                    <span style="font-size: 28px; color: {{ $flagProVC ? '#19b91f' : '#bbb' }};">
                                        &#10004;
                                    </span>
                                </td>
                                <td style="text-align:center; width:33%;">
                                    <span style="font-size: 28px; color: {{ $flagVC ? '#19b91f' : '#bbb' }};">
                                        &#10004;
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center">Registrar</td>
                                <td style="text-align:center">Pro-VC</td>
                                <td style="text-align:center">VC</td>
                            </tr>
                        </table>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
                </tr>
    @endforeach
            </tbody>
        </table>
    

</body>
</html>