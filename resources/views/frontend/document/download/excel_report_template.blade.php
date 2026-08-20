<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Doc ID</th>
            <th>Title</th>
            <th>From</th>
            <th>Current</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach($docs as $doc)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $doc->doc_id }}</td>
                <td>{{ $doc->title }}</td>
                <td>{{ $doc->from }}</td>
                <td>{{ $doc->forwarded_to }}</td>
                <td>{{ $doc->status }}</td>
                <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
