<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forwarded Documents Report</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 10mm;
        }
        body { 
            font-family: 'Open Sans', sans-serif, DejaVu Sans; 
            background: #fff; 
            color: #333; 
            margin: 0; 
            padding: 0;
            font-size: 12px;
        }
        .container { 
            width: 95%; 
            max-width: 1000px; 
            margin: 0 auto; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            page-break-inside: avoid; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            font-size: 11px; 
            vertical-align: middle;
        }
        th { 
            background: #f9f9f9; 
            font-weight: bold; 
            text-align: center;
        }
        td {
            text-align: center;
        }
        td.text-left {
            text-align: left;
        }
        h1, h2 { 
            margin: 10px 0; 
        }
        .page-break { 
            page-break-before: always; 
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-danger { background: #dc3545; color: #fff; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-info { background: #17a2b8; color: #fff; }
        .badge-success { background: #28a745; color: #fff; }
        .badge-secondary { background: #6c757d; color: #fff; }
        .approval-status-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .approval-status-item {
            text-align: center;
            min-width: 55px;
        }
        .approval-status-icon {
            font-size: 14px;
        }
        .approval-status-icon.completed { color: #28a745; }
        .approval-status-icon.pending { color: #ccc; }
        .approval-status-icon.current { color: #ffc107; }
        .approval-status-label {
            font-size: 9px;
            margin-top: 3px;
            display: block;
        }
        .approval-status-label.completed { color: #28a745; font-weight: bold; }
        .approval-status-label.current { color: #ffc107; font-weight: bold; }
        .approval-status-label.pending { color: #999; }
        .approval-arrow {
            color: #ccc;
            font-size: 10px;
            margin: 0 2px;
        }
        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            height: 4px;
            margin-top: 5px;
            width: 100%;
        }
        .progress-bar {
            background-color: #28a745;
            height: 4px;
            width: 0%;
        }
        .text-muted {
            color: #6c757d;
            font-size: 9px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <table style="border: none;">
            <tr>
                <td style="text-align: center; background: #fff; border: none;">
                    <img src="https://officesuite.vinayakamission.edu.in/assets/img/vm/logo.jpg" alt="Logo" style="width:100%;max-width:420px;height:auto;display:block;margin:0 auto;border-radius:8px;">
                </td>
            </tr>
        </table>
        <h1 style="text-align:center;">Forwarded Documents Report</h1>
        <p style="text-align:center;">Report generated on: {{ now()->format('d-m-Y h:i a') }}</p>
    </div>

    <table class="table table-striped" style="width: 100%; font-family: DejaVu Sans;">
        <thead>
            <tr>
                <th style="width:5%">S.No</th>
                <th style="width:10%">DOC ID</th>
                <th style="width:18%">Title</th>
                <th style="width:8%">Priority</th>
                <th style="width:10%">From</th>
                <th style="width:10%">Status</th>
                <th style="width:25%">Approval Progress</th>
                <th style="width:14%">Forwarded at</th>
            </tr>
        </thead>
        <tbody>
        @foreach($docs as $doc)
        
            @php
                $document = App\Models\DocumentApproval::find($doc->doc_id);
                
                // Get approval sequence and current progress
                $approvalSequence = [];
                $currentIndex = 0;
                $completedApprovers = [];
                
                if (!empty($document->approval_sequence)) {
                    try {
                        $approvalSequence = json_decode($document->approval_sequence, true);
                        if (!is_array($approvalSequence)) {
                            $approvalSequence = [];
                        }
                    } catch (\Exception $e) {
                        $approvalSequence = [];
                    }
                }
                
                $currentIndex = $document->current_sequence_index ?? 0;
                
                // Get completed approvals from approval_log
                $completedLogs = DB::table('approval_log')
                    ->where('doc_id', $document->id)
                    ->where(function($query) {
                        $query->where('status', 'like', 'Approved by%')
                              ->orWhere('status', 'like', 'Noted by%')
                              ->orWhere('status', 'like', 'Completed by%')
                              ->orWhere('status', 'like', 'Forwarded to%by%');
                    })
                    ->pluck('status')
                    ->toArray();
                
                // Determine which approvers have completed
                foreach ($completedLogs as $log) {
                    if (preg_match('/Approved by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                        $completedApprovers[] = trim($matches[1]);
                    } 
                    elseif (preg_match('/Noted by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                        $completedApprovers[] = trim($matches[1]);
                    }
                    elseif (preg_match('/Completed by\s+(.+?)(?:\s|$)/', $log, $matches)) {
                        $completedApprovers[] = trim($matches[1]);
                    }
                    elseif (preg_match('/Forwarded to\s+(.+?)\s+by/', $log, $matches)) {
                        $completedApprovers[] = trim($matches[1]);
                    }
                }
                
                $completedApprovers = array_unique($completedApprovers);
                
                // Calculate completion percentage
                $totalSteps = count($approvalSequence);
                $completedSteps = 0;
                foreach ($approvalSequence as $index => $approver) {
                    if (in_array($approver, $completedApprovers) || $index < $currentIndex) {
                        $completedSteps++;
                    }
                }
                $completionPercent = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                
                // Fallback for old documents
                $flagRegistrar = DB::table('approval_log')
                    ->where('doc_id', $document->id)
                    ->whereIn('status', ['Approved by Registrar', 'Noted by Registrar'])->first();
                
                $flagProVC = DB::table('approval_log')
                    ->where('doc_id', $document->id)
                    ->whereIn('status', ['Approved by Pro VC', 'Noted by Pro-VC'])->first();

                $flagVC = DB::table('approval_log')
                    ->where('doc_id', $document->id)
                    ->whereIn('status', ['Approved by VC', 'VC Approved in Principle', 'Noted by VC'])->first();
            @endphp

            <tr style="page-break-inside: avoid;">
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td style="text-align: center;">{{ $document->doc_id }}</td>
                <td style="text-align: left;">
                    {{ \Illuminate\Support\Str::limit($document->title, 60) }}
                </td>
                <td style="text-align: center;">
                    @if($doc->priority == 'High')
                        <span class="badge badge-danger">High</span>
                    @elseif($doc->priority == 'Medium')
                        <span class="badge badge-warning">Medium</span>
                    @else
                        <span class="badge badge-info">Low</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $document->from }}</td>
                <td style="text-align: center;">
                    @if($document->status == 'Closed')
                        <span class="badge badge-secondary">Closed</span>
                    @elseif($document->status == 'Completed')
                        <span class="badge badge-success">Completed</span>
                    @elseif($document->status == 'Rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @else
                        <span class="badge badge-info">{{ $document->status }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if(!empty($approvalSequence))
                        <div>
                            <div class="approval-status-container">
                                @foreach($approvalSequence as $index => $approver)
                                    @php
                                        $isCompleted = in_array($approver, $completedApprovers) || $index < $currentIndex;
                                        $isCurrent = $index == $currentIndex && !$isCompleted && $document->status != 'Completed' && $document->status != 'Closed';
                                        $iconClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                                        $iconSymbol = $isCompleted ? '✓' : ($isCurrent ? '▶' : '○');
                                        $labelClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                                        
                                        $shortName = $approver;
                                        if(strlen($approver) > 12) {
                                            $shortName = substr($approver, 0, 10) . '...';
                                        }
                                    @endphp
                                    <div class="approval-status-item">
                                        <div class="approval-status-icon {{ $iconClass }}">
                                            {{ $iconSymbol }}
                                        </div>
                                        <span class="approval-status-label {{ $labelClass }}" title="{{ $approver }}">
                                            {{ $shortName }}
                                        </span>
                                    </div>
                                    @if(!$loop->last)
                                        <span class="approval-arrow">→</span>
                                    @endif
                                @endforeach
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $completionPercent }}%;"></div>
                            </div>
                            <span class="text-muted">{{ $completionPercent }}% Complete</span>
                            @if(!$isComplete && isset($approvalSequence[$currentIndex]))
                                <div class="text-muted" style="margin-top: 3px;">
                                    Pending: {{ $approvalSequence[$currentIndex] }}
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Fallback for old documents -->
                        <div class="approval-status-container">
                            <div class="approval-status-item">
                                <div class="approval-status-icon {{ $flagRegistrar ? 'completed' : 'pending' }}">
                                    {!! $flagRegistrar ? '✓' : '○' !!}
                                </div>
                                <span class="approval-status-label">Registrar</span>
                            </div>
                            <span class="approval-arrow">→</span>
                            <div class="approval-status-item">
                                <div class="approval-status-icon {{ $flagProVC ? 'completed' : 'pending' }}">
                                    {!! $flagProVC ? '✓' : '○' !!}
                                </div>
                                <span class="approval-status-label">Pro-VC</span>
                            </div>
                            <span class="approval-arrow">→</span>
                            <div class="approval-status-item">
                                <div class="approval-status-icon {{ $flagVC ? 'completed' : 'pending' }}">
                                    {!! $flagVC ? '✓' : '○' !!}
                                </div>
                                <span class="approval-status-label">VC</span>
                            </div>
                        </div>
                    @endif
                </td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is a system-generated report from VIMS Hospital Management System</p>
    </div>
</body>
</html>