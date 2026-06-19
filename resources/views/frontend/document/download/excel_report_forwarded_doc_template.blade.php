<thead>
    <tr>
        <th>S.No</th>
        <th>DOC ID</th>
        <th>Title</th>
        <th>Priority</th>
        <th>From</th>
        <th>Status</th>
        <th>Approval Progress</th>
        <th>Forwarded at</th>
    </tr>
</thead>
<tbody>
    @foreach($docs as $doc)
        @php
            $document = App\Models\DocumentApproval::find($doc->doc_id);
            $approvals = $approvalLogs[$doc->id] ?? collect();
            
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
            $flagRegistrar = $approvals->firstWhere('status', 'Approved by Registrar');
            $flagProVC = $approvals->firstWhere('status', 'Approved by Pro VC');
            $flagVC = $approvals->firstWhere(fn($log) => in_array($log->status, ['Approved by VC', 'VC Approved in Principle']));
        @endphp
        
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $document->doc_id }}</td>
            <td style="text-align: left;">
                <a href="{{ url('/view/document/'.$document->id) }}" style="color: #1e1e1e">
                    {{ \Illuminate\Support\Str::limit($document->title, 50) }}
                </a>
            </td>
            <td>
                @if($doc->priority == 'High')
                    <span class="badge badge-danger">{{ $doc->priority }}</span>
                @elseif($doc->priority == 'Medium')
                    <span class="badge badge-warning">{{ $doc->priority }}</span>
                @else
                    <span class="badge badge-info">{{ $doc->priority }}</span>
                @endif
            </td>
            <td>{{ $document->from }}</td>
            <td>
                @if($document->status == 'Closed')
                    <span class="badge badge-secondary">Closed</span>
                @elseif($document->status == 'Completed')
                    <span class="badge badge-success">Completed</span>
                @elseif($document->status == 'Rejected')
                    <span class="badge badge-danger">Rejected</span>
                @else
                    <span class="badge badge-primary">{{ $document->status }}</span>
                @endif
            </td>
            <td>
                @if(!empty($approvalSequence))
                    <div style="min-width: 200px;">
                        <div class="approval-status-container" style="display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;">
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
                                <div style="text-align: center; min-width: 60px;">
                                    <div style="font-size: 18px; color: {{ $isCompleted ? '#28a745' : ($isCurrent ? '#ffc107' : '#ccc') }};">
                                        {{ $iconSymbol }}
                                    </div>
                                    <span style="font-size: 10px; {{ $isCompleted ? 'color:#28a745;font-weight:bold' : ($isCurrent ? 'color:#ffc107;font-weight:bold' : 'color:#999') }}" title="{{ $approver }}">
                                        {{ $shortName }}
                                    </span>
                                </div>
                                @if(!$loop->last)
                                    <span style="color: #ccc;">→</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="progress" style="height: 4px; width: 100%; margin-top: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercent }}%;"></div>
                        </div>
                        <small class="text-muted">{{ $completionPercent }}% Complete</small>
                    </div>
                @else
                    <!-- Fallback for old documents -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                        <div style="text-align: center;">
                            <span style="font-size: 18px; color: {{ $flagRegistrar ? '#28a745' : '#ccc' }};">
                                {!! $flagRegistrar ? '✓' : '○' !!}
                            </span><br>
                            <span style="font-size: 11px;">Registrar</span>
                        </div>
                        <span style="color: #ccc;">→</span>
                        <div style="text-align: center;">
                            <span style="font-size: 18px; color: {{ $flagProVC ? '#28a745' : '#ccc' }};">
                                {!! $flagProVC ? '✓' : '○' !!}
                            </span><br>
                            <span style="font-size: 11px;">Pro-VC</span>
                        </div>
                        <span style="color: #ccc;">→</span>
                        <div style="text-align: center;">
                            <span style="font-size: 18px; color: {{ $flagVC ? '#28a745' : '#ccc' }};">
                                {!! $flagVC ? '✓' : '○' !!}
                            </span><br>
                            <span style="font-size: 11px;">VC</span>
                        </div>
                    </div>
                @endif
            </td>
            <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d-m-Y') }}</td>
        </tr>
    @endforeach
</tbody>