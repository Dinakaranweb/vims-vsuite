<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>DOC ID</th>
            <th>Expenditure ID</th>
            <th>Mode</th>
            <th>Type</th>
            <th>Reference No</th>
            <th>Date</th>
            <th>Paid Amount</th>
            <th>TDS Amount</th>
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
                <td>{{ $payment->paid_amount }}</td>
                <td>{{ $payment->tds_amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
