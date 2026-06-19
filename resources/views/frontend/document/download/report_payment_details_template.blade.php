<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In Progress Documents Report</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Open Sans', sans-serif; background: #fff; color: #333; margin: 0; padding: 0; }
        .container { width: 95%; max-width: 1000px; margin: 0 auto; padding: 20px; border: 1px solid #000; border-radius: 8px; page-break-inside: avoid; }
        table {
            border-collapse: collapse;
            border-radius: 8px;
            width: 100%;
            table-layout: fixed; /* Ensures fixed width columns */
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        th:nth-child(1), td:nth-child(1) { width: 30px; }   /* S.No */
        th:nth-child(2), td:nth-child(2) { width: 70px; }   /* DOC ID */
        th:nth-child(3), td:nth-child(3) { width: 100px; }  /* Expenditure ID */
        th:nth-child(4), td:nth-child(4) { width: 60px; }   /* Mode */
        th:nth-child(5), td:nth-child(5) { width: 60px; }   /* Type */
        th:nth-child(6), td:nth-child(6) { width: 120px; }  /* Reference No */
        th:nth-child(7), td:nth-child(7) { width: 70px; }   /* Date */
        th:nth-child(8), td:nth-child(8) { width: 90px; }   /* Paid Amount */
        th:nth-child(9), td:nth-child(9) { width: 90px; }   /* TDS Amount */
        h1, h2 { margin: 10px 0; }
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
        <h1 style="text-align:center;">Payment Details Report</h1>
        <p style="text-align:center;">Report generated on: {{ now()->format('d-m-Y h:i a') }}</p>
    </div>

        <table class="table table-striped" id="table-1" style="text-align: center; width: 100%; font-family: DejaVu Sans;">
            <thead>
                <tr>
                    <th style="width: 5%;">S.No</th>
                    <th style="width: 10%;">DOC ID</th>
                    <th style="width: 15%;">Expenditure ID</th>
                    <th style="width: 10%;">Mode</th>
                    <th style="width: 10%;">Type</th>
                    <th style="width: 15%;">Reference No</th>
                    <th style="width: 10%;">Date</th>
                    <th style="width: 12.5%;">Paid Amount</th>
                    <th style="width: 12.5%;">TDS Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $key => $payment)
                    @php
                        $document = App\Models\DocumentApproval::find($payment->doc_id);    
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $document->doc_id }}</td>
                        <td>{{ $payment->expenditure_id }}</td>
                        <td>{{ $payment->mode }}</td>
                        <td>{{ $payment->payment_type }}</td>
                        <td>{{ $payment->payment_reference_no }}</td>
                        <td>{{ $payment->payment_date ? date('d/m/Y', strtotime($payment->payment_date)) : '-' }}</td>
                        <td>{{ $document->currency }} {{ $payment->paid_amount }}</td>
                        <td>{{ $document->currency }} {{ $payment->tds_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
</body>
</html>