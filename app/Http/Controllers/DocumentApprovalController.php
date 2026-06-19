<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\DocumentApproval;
use App\Models\DocumentApprovalForwardings;
use App\Models\PaymentDetails;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ForwardedDocumentsExport;
use App\Exports\PaymentDetailsExport;
use App\Exports\ApprovalReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;
use App\Jobs\SendTicketNotificationMail;

class DocumentApprovalController extends Controller
{
    public function create($ticket_id = null){
        $activeMenu = "document";
        $activeDropdown = "create_doc";
        return view('frontend.document.create', compact('activeMenu', 'activeDropdown', 'ticket_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = User::find(Auth::id());
        $to = $request->input('to');
        $type = $request->has('request_type') ? 'Y' : 'N';
        $isDraft = $request->input('action') === 'draft';
        $isPaymentInvolved = $request->has('is_payment_involved') ? 'Y' : 'N';
        $amount = $request->input('amount', 0);

        // Determine approval path based on payment and amount
        $approvalPath = $this->determineApprovalPath($user, $to, $isPaymentInvolved, $amount, $type);

        if ($isDraft) {
            $newDocId = 'Draft';
            $status = 'Draft';
        } else {
            $maxDocNumber = DocumentApproval::where('doc_id', '!=', 'Draft')
                ->selectRaw("MAX(CAST(SUBSTRING(doc_id, 9) AS UNSIGNED)) as max_number")
                ->value('max_number');
            $lastNumber = $maxDocNumber ? intval($maxDocNumber) : 0;
            $newDocId = 'REG-DOC-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $status = 'Sent to ' . $approvalPath['current_approver'] . ' by ' . $user->name . ', ' . $user->department;
        }

        $documentApproval = DocumentApproval::create([
            'doc_id' => $newDocId,
            'title' => $request->input('title'),
            'from' => $user->department,
            'by' => $user->id,
            'to' => $approvalPath['current_approver'],
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'currency' => $request->input('currency'),
            'justification' => $request->input('justification'),
            'attachment' => true,
            'status' => $status,
            'amount' => $request->input('amount'),
            'is_purchase' => $type,
            'is_payment_involved' => $isPaymentInvolved,
            'priority' => $request->input('priority'),
            'forwarded_to' => $approvalPath['current_approver'],
            'ticket_id' => $request->input('ticket_id'),
            'created_at' => now(),
            'payment_mode' => $request->input('payment_mode'),
            'cash_in_favour' => $request->input('cash_in_favour'),
            'account_holder' => $request->input('account_holder'),
            'account_number' => $request->input('account_number'),
            'ifsc_code' => $request->input('ifsc_code'),
            'account_branch' => $request->input('account_branch'),
            'upi_id' => $request->input('upi_id'),
            'approval_sequence' => json_encode($approvalPath['sequence']),
            'current_sequence_index' => 0,
        ]);

        $this->handleFileUploads($request, $documentApproval->id, $user);
        $this->logDocumentCreation($documentApproval, $user, $status, $approvalPath['current_approver']);
        $this->sendNotifications($documentApproval, $user, $approvalPath['current_approver'], $status);

        if ($request->input('ticket_id')) {
            $this->handleTicketConversion($request, $documentApproval, $newDocId);
        }

        $alert = [
            'message' => $status == 'Draft' ? 'Document Draft Created Successfully' : 'Document Submitted Successfully',
            'alert-type' => 'success'
        ];

        if ($status == 'Draft') {
            return redirect()->route('draft_documents')->with($alert);
        }
        
        if(Auth::user()->role == 'SuperAdmin'){
            return redirect()->route('my_documents')->with($alert);
        }
        return redirect()->route('new_documents')->with($alert);
    }

    /**
     * Determine the approval path based on HOD's division, payment involvement, and amount
     * 
     * @param User $user - The user creating the document (HOD)
     * @param string $initialTo - Initial department/HOD selected (Medical Director or General Manager)
     * @param string $isPaymentInvolved - 'Y' or 'N'
     * @param float $amount - Document amount
     * @param string $isPurchase - 'Y' or 'N'
     * @return array - ['sequence' => [...], 'current_approver' => '...']
     */
    private function determineApprovalPath($user, $initialTo, $isPaymentInvolved, $amount, $isPurchase = 'N')
    {
        $sequence = [];
        
        // Step 1: Add the selected first approver
        $sequence[] = $initialTo;
        
        // Step 2: Add Medical Director and General Manager based on HOD's division
        $isClinical = ($user->division ?? '') === 'Clinical';
        
        if ($isClinical) {
            // Clinical department: Medical Director first, then General Manager
            if ($initialTo !== 'Medical Director') {
                $sequence[] = 'Medical Director';
            }
            if ($initialTo !== 'General Manager') {
                $sequence[] = 'General Manager';
            }
        } else {
            // Non-Clinical department: General Manager first, then Medical Director
            if ($initialTo !== 'General Manager') {
                $sequence[] = 'General Manager';
            }
            if ($initialTo !== 'Medical Director') {
                $sequence[] = 'Medical Director';
            }
        }
        
        // Remove duplicates (in case user selected the same as next approver)
        $sequence = array_values(array_unique($sequence));
        
        // Step 3: If payment is involved, add payment-related approvals
        if ($isPaymentInvolved === 'Y') {
            if ($amount > 200000) {
                // High value (>2 Lakhs) — STB Office → Chairman → PA to Chairman (selects Finance Head)
                $sequence[] = 'STB Office';
                $sequence[] = 'Chairman';
                if ($isPurchase === 'Y') {
                    $sequence[] = 'Purchase Head Chennai';
                }
                $sequence[] = 'PA to Chairman';

            } else {
                // ≤2 Lakhs — go directly to Finance Head Salem (no PA step)
                // If purchase, route through Purchase Head first
                if ($isPurchase === 'Y') {
                    $sequence[] = 'Purchase Head';
                }
                $sequence[] = 'Finance Head Salem';
            }
        }
        
        return [
            'sequence' => $sequence,
            'current_approver' => $sequence[0]
        ];
    }

    /**
     * Update the approval sequence to select specific Finance Head
     * Called by PA to Chairman or PA to GM
     */
    public function updateFinanceHeadSelection(Request $request)
    {
        $request->validate([
            'doc_id' => 'required|exists:document_approvals,id',
            'finance_head' => 'required|string|in:Finance Head Karaikal,Finance Head Salem,Finance Head Chennai,Finance Head Pondy',
        ]);
        
        $doc = DocumentApproval::findOrFail($request->doc_id);
        $user = Auth::user();
        
        // Check if user has permission (PA to Chairman, PA to GM, Chairman, or SuperAdmin)
        $authorizedRoles = ['PA to Chairman', 'PA to GM', 'Chairman', 'SuperAdmin'];
        if (!in_array($user->department, $authorizedRoles) && $user->role != 'SuperAdmin') {
            return response()->json([
                'message' => 'You are not authorized to select Finance Head',
                'status' => 'error'
            ]);
        }
        
        $approvalSequence = json_decode($doc->approval_sequence, true);
        $currentIndex = $doc->current_sequence_index;
        $currentApprover = $approvalSequence[$currentIndex] ?? null;
        
        // Verify current approver is PA to Chairman, PA to GM, or Chairman
        if (!in_array($currentApprover, ['PA to Chairman', 'PA to GM', 'Chairman'])) {
            return response()->json([
                'message' => 'Finance Head can only be selected at the appropriate step',
                'status' => 'error'
            ]);
        }
        
        // Replace the placeholder with the selected Finance Head
        foreach ($approvalSequence as $index => $step) {
            if ($step === 'PA to Chairman' || $step === 'PA to GM') {
                $approvalSequence[$index] = $request->finance_head;
                break;
            }
        }
        
        // Update the document
        $doc->update([
            'approval_sequence' => json_encode($approvalSequence),
            'forwarded_to' => $request->finance_head,
            'to' => $request->finance_head,
            'status' => "Sent to " . $request->finance_head,
            'updated_at' => now()
        ]);
        
        // Log the action
        $remarks = $request->input('message') ?: null;
        $log_description = "Finance Head selected by <b>" . $user->department . "</b>: <b>" . $request->finance_head . "</b>"
            . ($remarks ? " — " . e($remarks) : "");
        DB::table('document_logs')->insert([
            'doc_id' => $doc->id,
            'description' => $log_description,
            'created_at' => now()
        ]);

        $log_message = $request->finance_head . " selected for payment processing"
            . ($remarks ? ". Instruction: " . $remarks : "");
        DB::table('approval_log')->insert([
            'doc_id' => $doc->id,
            'by' => Auth::id(),
            'status' => "Forwarded to " . $request->finance_head . " by " . $user->department,
            'message' => $log_message,
            'created_at' => now()
        ]);
        
        // Notify the selected Finance Head
        $this->sendNotificationToDepartment($request->finance_head, $doc, "A document has been forwarded to you for payment processing");
        
        // Notify document creator
        $this->sendNotificationToUser($doc->by, $doc, "Finance Head (" . $request->finance_head . ") has been assigned to your document");
        
        return response()->json([
            'message' => 'Finance Head selected successfully',
            'status' => 'success',
            'finance_head' => $request->finance_head
        ]);
    }

    /**
     * Get list of available Finance Heads
     */
    public function getFinanceHeads()
    {
        $financeHeads = [
            'Finance Head Karaikal',
            'Finance Head Salem', 
            'Finance Head Chennai',
            'Finance Head Pondy'
        ];
        
        return response()->json([
            'finance_heads' => $financeHeads
        ]);
    }

    /**
     * Get document details for Finance Head selection view
     */
    public function getDocumentForFinanceSelection($doc_id)
    {
        $doc = DocumentApproval::findOrFail($doc_id);
        $user = Auth::user();
        
        // Check if current user is authorized (PA to Chairman or PA to GM)
        $authorizedRoles = ['PA to Chairman', 'PA to GM', 'SuperAdmin'];
        if (!in_array($user->department, $authorizedRoles) && $user->role != 'SuperAdmin') {
            abort(403, 'You are not authorized to select Finance Head for this document');
        }
        
        $approvalSequence = json_decode($doc->approval_sequence, true);
        $currentIndex = $doc->current_sequence_index;
        $currentApprover = $approvalSequence[$currentIndex] ?? null;
        
        // Verify current approver is PA to Chairman or PA to GM
        if (!in_array($currentApprover, ['PA to Chairman', 'PA to GM'])) {
            abort(403, 'Finance Head selection is not available at this stage');
        }
        
        $financeHeads = [
            'Finance Head Karaikal',
            'Finance Head Salem',
            'Finance Head Chennai',
            'Finance Head Pondy'
        ];
        
        $activeMenu = "document";
        $activeDropdown = "forwarded_doc";
        
        return view('frontend.document.select_finance_head', compact('doc', 'financeHeads', 'activeMenu', 'activeDropdown'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string',
            'description' => 'required|string',
        ]);

        $document = DocumentApproval::findOrFail($id);
        $user = User::find(Auth::id());
        $to = $request->input('to');
        $type = $request->has('request_type') ? 'Y' : 'N';
        $isDraft = $request->input('action') === 'draft';
        $isPaymentInvolved = $request->has('is_payment_involved') ? 'Y' : 'N';
        $amount = $request->input('amount', 0);

        // Determine if we need to generate a new doc_id
        $newDocId = $document->doc_id;
        if (!$isDraft && $document->doc_id === 'Draft') {
            $maxDocNumber = DocumentApproval::where('doc_id', '!=', 'Draft')
                ->selectRaw("MAX(CAST(SUBSTRING(doc_id, 9) AS UNSIGNED)) as max_number")
                ->value('max_number');
            $lastNumber = $maxDocNumber ? intval($maxDocNumber) : 0;
            $newDocId = 'REG-DOC-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        // Determine approval path
        $approvalPath = $this->determineApprovalPath($user, $to, $isPaymentInvolved, $amount, $type);

        $status = $isDraft ? 'Draft' : 'Sent to ' . $approvalPath['current_approver'];

        // Update document details
        $document->update([
            'doc_id' => $newDocId,
            'title' => $request->input('title'),
            'from' => $user->department,
            'by' => $user->id,
            'to' => $approvalPath['current_approver'],
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'justification' => $request->input('justification'),
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
            'is_purchase' => $type,
            'is_payment_involved' => $isPaymentInvolved,
            'priority' => $request->input('priority'),
            'status' => $status,
            'forwarded_to' => $approvalPath['current_approver'],
            'payment_mode' => $request->input('payment_mode'),
            'cash_in_favour' => $request->input('cash_in_favour'),
            'account_holder' => $request->input('account_holder'),
            'account_number' => $request->input('account_number'),
            'ifsc_code' => $request->input('ifsc_code'),
            'account_branch' => $request->input('account_branch'),
            'upi_id' => $request->input('upi_id'),
            'approval_sequence' => json_encode($approvalPath['sequence']),
            'current_sequence_index' => 0,
            'updated_at' => now()
        ]);

        // Log the update
        $formattedUpdatedAt = now()->format('M d, Y g:ia');
        $log_description = "Document Updated by <b>" . $user->name . " , " . $user->department . "</b> at " . $formattedUpdatedAt;

        DB::table('document_logs')->insert([
            'doc_id' => $document->id,
            'description' => $log_description,
            'created_at' => now(),
        ]);

        if($status == 'Draft'){
            $log_message = "Draft Updated by ".$user->name." at ".$formattedUpdatedAt;
        } else {
            $log_message = "Sent to ".$approvalPath['current_approver']." for approval at ".$formattedUpdatedAt;
        }

        DB::table('approval_log')->insert([
            'doc_id' => $document->id,
            'by' => Auth::id(),
            'status' => $status,
            'message' => $log_message,
            'created_at' => now()
        ]);

        // Handle file uploads
        $this->handleFileUploads($request, $document->id, $user);

        // Send notifications
        $notificationController = new notificationController();
        
        if($status == 'Draft'){
            $notificationController->notificationEntry($user->id, 'document', $document->id, '<b>Your Draft Document was updated'); 
            $alert = [
                'message' => 'Document Updated Successfully',
                'alert-type' => 'success',
            ]; 
            return redirect()->route('draft_documents')->with($alert);
        } else {
            $this->sendNotificationToDepartment($approvalPath['current_approver'], $document, '<b>' . $user->name . '</b> from <b>' . $user->department . ' Sent a Document for approval');
            $alert = [
                'message' => 'Document Submitted Successfully',
                'alert-type' => 'success',
            ];
            return redirect()->route('new_documents')->with($alert);
        }
    }

    public function edit($doc_id){
        $activeMenu = "document";
        $activeDropdown = "create_doc";
        $doc = DocumentApproval::findOrFail($doc_id);
        $annexures = DB::table('document_annexures')->where('doc_id', $doc_id)->get();
        return view('frontend.document.edit', compact('activeMenu', 'activeDropdown', 'doc', 'annexures'));
    }

public function changeDocumentStatus(Request $request)
{
    $doc_id = $request->input('doc_id');
    $action = $request->input('status');
    $user = User::findOrFail(Auth::id());
    $doc = DocumentApproval::findOrFail($doc_id);
    
    $approvalSequence = json_decode($doc->approval_sequence, true);
    $currentIndex = $doc->current_sequence_index;
    
    // Handle different approval actions
    switch ($action) {
        case 'Approved':
            $response = $this->handleApproval($doc, $user, $approvalSequence, $currentIndex, $request);
            break;
        case 'Approved in Principle':
            $response = $this->handleApprovalInPrinciple($doc, $user, $approvalSequence, $currentIndex, $request);
            break;
        case 'Rejected':
            $response = $this->handleRejection($doc, $user, $request);
            break;
        case 'Hold':
            $response = $this->handleHold($doc, $user, $request);
            break;
        case 'Close':
            $response = $this->handleClose($doc, $user, $request);
            break;
        case 'Forward':
            $response = $this->handleForward($doc, $user, $request);
            break;
        case 'Commented':
            $response = $this->handleComment($doc, $user, $request);
            break;
        case 'Noted':
            $response = $this->handleNoted($doc, $user, $request);
            break;
        case 'Pending':
            $response = $this->handlePending($doc, $user, $request);
            break;
        case 'Discuss':
            $response = $this->handleDiscuss($doc, $user, $request);
            break;
        case 'Payment In Progress':
            $response = $this->handlePaymentInProgress($doc, $user, $request);
            break;
        case 'Completed':
            $response = $this->handleCompleted($doc, $user, $request);
            break;
        case 'Paid':
            $response = $this->handlePaymentDetails($doc, $user, $request);
            break;
        case 'Sanction':
            $response = $this->handleFinanceSanction($doc, $user, $request);
            break;
        case 'Create Purchase Order':
            $response = $this->handlePurchaseOrderCreation($doc, $user, $request);
            break;
        case 'Create Work Order':
            $response = $this->handleWorkOrderCreation($doc, $user, $request);
            break;
        case 'Chairman Approve':
            $response = $this->handleChairmanApproval($doc, $user, $approvalSequence, $currentIndex, $request);
            break;
        case 'Finance Head Forward':
            $response = $this->handleFinanceHeadForward($doc, $user, $request);
            break;
        case 'Select Finance Head':
            // Internal routing step — no broad participant notification needed
            return $this->updateFinanceHeadSelection($request);
        case 'Create Finance Details':
            $response = $this->handleFinanceDetails($doc, $user, $request);
            break;
        case 'Acknowledge':
            $response = $this->handleSTBAcknowledge($doc, $user, $request);
            break;
        default:
            return response()->json(['message' => 'Invalid action', 'status' => 'error']);
    }

    $responseData = json_decode($response->getContent(), true);
    if (($responseData['status'] ?? '') === 'success') {
        $this->notifyAllParticipants($doc->fresh(), $user, $action);
    }

    return $response;
}
    private function handleApproval($doc, $user, $approvalSequence, $currentIndex, $request)
    {
        $currentApprover = $approvalSequence[$currentIndex];
        $nextIndex = $currentIndex + 1;
        
        $approval_status = "Approved by " . $user->department;
        $log_description = "Document Approved by <b>" . $user->department . "</b>";
        
        // For STB Office and Chairman, check if any approver from that department can approve
        $isSTBOrChairman = in_array($currentApprover, ['STB Office', 'Chairman']);
        
        if ($isSTBOrChairman) {
            // Check if this department has already approved (for parallel approvers)
            $alreadyApproved = DB::table('approval_log')
                ->where('doc_id', $doc->id)
                ->where('status', 'like', "Approved by $currentApprover%")
                ->exists();
                
            if ($alreadyApproved) {
                return response()->json([
                    'message' => 'This document has already been approved by ' . $currentApprover,
                    'status' => 'error'
                ]);
            }
        }
        
        // Check if this is the last approval
        if ($nextIndex >= count($approvalSequence)) {
            // Final approval - mark as completed
            $doc->update([
                'approval_status' => $approval_status,
                'status' => 'Completed',
                'forwarded_to' => $doc->from,
                'current_sequence_index' => $nextIndex,
                'recommended_amount' => $request->input('recommended_amount', $doc->recommended_amount),
                'sanctioned_amount' => $request->input('sanctioned_amount', $doc->sanctioned_amount),
                'updated_at' => now()
            ]);
            
            $log_message = "Document fully approved and completed";
            
            // Notify document creator that process is complete
            $this->sendNotificationToUser($doc->by, $doc, "Your document has been fully approved and completed");
            
        } else {
            // Forward to next approver
            $nextApprover = $approvalSequence[$nextIndex];
            
            $doc->update([
                'approval_status' => $approval_status,
                'status' => "Sent to " . $nextApprover,
                'forwarded_to' => $nextApprover,
                'to' => $nextApprover,
                'current_sequence_index' => $nextIndex,
                'recommended_amount' => $request->input('recommended_amount', $doc->recommended_amount),
                'sanctioned_amount' => $request->input('sanctioned_amount', $doc->sanctioned_amount),
                'updated_at' => now()
            ]);
            
            $log_message = "Document forwarded to " . $nextApprover;
            
            // Add to forwarding table if not already forwarded
            $alreadyForwarded = DB::table('document_approval_forwardings')
                ->where('doc_id', $doc->id)
                ->where('forwarded_to', $nextApprover)
                ->exists();
                
            if (!$alreadyForwarded) {
                DB::table('document_approval_forwardings')->insert([
                    'doc_id' => $doc->id,
                    'forwarded_by' => Auth::id(),
                    'forwarded_to' => $nextApprover,
                    'created_at' => now()
                ]);
            }
            
            // Check if next approver is PA to Chairman or PA to GM (Finance Head selection step)
            if (in_array($nextApprover, ['PA to Chairman', 'PA to GM'])) {
                // Notify the PA to select Finance Head
                $this->sendNotificationToDepartment($nextApprover, $doc, "Please select the appropriate Finance Head for this document");
            } elseif (in_array($nextApprover, ['STB Office', 'Chairman'])) {
                $this->sendNotificationToDepartment($nextApprover, $doc, "A document has been forwarded to your department for approval");
            } else {
                $this->sendNotificationToDepartment($nextApprover, $doc, "A document has been forwarded to you for approval");
            }
        }
        
        $this->logApprovalAction($doc, $approval_status, $log_message, $request->input('message', $log_description));
        
        // Notify document owner
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been " . $approval_status);
        
        return response()->json(['message' => 'Success', 'status' => 'success']);
    }

    private function handleApprovalInPrinciple($doc, $user, $approvalSequence, $currentIndex, $request)
    {
        $nextIndex = $currentIndex + 1;
        $approval_status = $user->department . " Approved in Principle";
        
        if ($nextIndex >= count($approvalSequence)) {
            $doc->update([
                'approval_status' => $approval_status,
                'status' => 'Approved in Principle',
                'current_sequence_index' => $nextIndex,
                'updated_at' => now()
            ]);
        } else {
            $nextApprover = $approvalSequence[$nextIndex];
            $doc->update([
                'approval_status' => $approval_status,
                'status' => "Sent to " . $nextApprover . " (Approved in Principle)",
                'forwarded_to' => $nextApprover,
                'to' => $nextApprover,
                'current_sequence_index' => $nextIndex,
                'updated_at' => now()
            ]);
            
            $this->sendNotificationToDepartment($nextApprover, $doc, "A document has been forwarded to you (Approved in Principle)");
        }
        
        $log_message = "Document Approved in Principle by " . $user->department;
        $this->logApprovalAction($doc, $approval_status, $log_message, $request->input('message', $log_message));
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been " . $approval_status);
        
        return response()->json(['message' => 'Success', 'status' => 'success']);
    }

    private function handleChairmanApproval($doc, $user, $approvalSequence, $currentIndex, $request)
    {
        if ($doc->department != 'Chairman' && $user->department != 'Chairman' && $user->role != 'SuperAdmin') {
            return response()->json(['message' => 'Only Chairman can use this action', 'status' => 'error']);
        }

        $alreadyApproved = DB::table('approval_log')
            ->where('doc_id', $doc->id)
            ->where('status', 'like', 'Approved by Chairman%')
            ->exists();

        if ($alreadyApproved) {
            return response()->json(['message' => 'This document has already been approved by Chairman', 'status' => 'error']);
        }

        $financeHead = $request->input('finance_head');
        $validFinanceHeads = ['Finance Head Salem', 'Finance Head Chennai', 'Finance Head Karaikal', 'Finance Head Pondy'];

        if ($financeHead && !in_array($financeHead, $validFinanceHeads)) {
            return response()->json(['message' => 'Invalid Finance Head selected', 'status' => 'error']);
        }

        // If Chairman directly selects a Finance Head, replace the PA step in the sequence
        if ($financeHead) {
            foreach ($approvalSequence as $idx => $step) {
                if ($idx > $currentIndex && in_array($step, ['PA to Chairman', 'PA to GM'])) {
                    $approvalSequence[$idx] = $financeHead;
                    break;
                }
            }
            $doc->update(['approval_sequence' => json_encode($approvalSequence)]);
        }

        $approval_status = "Approved by Chairman";
        $nextIndex = $currentIndex + 1;

        if ($nextIndex >= count($approvalSequence)) {
            $doc->update([
                'approval_status'        => $approval_status,
                'status'                 => 'Completed',
                'forwarded_to'           => $doc->from,
                'current_sequence_index' => $nextIndex,
                'recommended_amount'     => $request->input('recommended_amount', $doc->recommended_amount),
                'sanctioned_amount'      => $request->input('sanctioned_amount', $doc->sanctioned_amount),
                'updated_at'             => now(),
            ]);
            $this->sendNotificationToUser($doc->by, $doc, "Your document has been fully approved and completed by Chairman");
        } else {
            $nextApprover = $approvalSequence[$nextIndex];
            $doc->update([
                'approval_status'        => $approval_status,
                'status'                 => 'Sent to ' . $nextApprover,
                'forwarded_to'           => $nextApprover,
                'to'                     => $nextApprover,
                'current_sequence_index' => $nextIndex,
                'recommended_amount'     => $request->input('recommended_amount', $doc->recommended_amount),
                'sanctioned_amount'      => $request->input('sanctioned_amount', $doc->sanctioned_amount),
                'updated_at'             => now(),
            ]);

            DB::table('document_approval_forwardings')->insertOrIgnore([
                'doc_id'       => $doc->id,
                'forwarded_by' => Auth::id(),
                'forwarded_to' => $nextApprover,
                'created_at'   => now(),
            ]);

            $notificationMsg = $financeHead
                ? "Chairman has approved and routed the document directly to you for payment processing"
                : "A document has been forwarded to you for approval by Chairman";
            $this->sendNotificationToDepartment($nextApprover, $doc, $notificationMsg);
            $this->sendNotificationToUser($doc->by, $doc, "Your document has been approved by Chairman and forwarded to " . $nextApprover);
        }

        $logMsg = $request->input('message', 'Approved by Chairman');
        if ($financeHead) {
            $logMsg .= " [Finance Head assigned directly: {$financeHead}]";
        }
        $log_description = "Document Approved by <b>Chairman</b>"
            . ($financeHead ? " — Finance Head assigned: <b>{$financeHead}</b>" : "");
        $this->logApprovalAction($doc, $approval_status, $log_description, $logMsg);

        $successMsg = $financeHead
            ? "Approved and routed directly to {$financeHead}"
            : "Document approved successfully";

        return response()->json(['message' => $successMsg, 'status' => 'success']);
    }

    private function handleRejection($doc, $user, $request)
    {
        $approval_status = "Rejected by " . $user->department;
        $reason = $request->input('message', 'No reason provided');
        
        $doc->update([
            'approval_status' => $approval_status,
            'status' => 'Rejected',
            'updated_at' => now()
        ]);
        
        $log_description = "Document Rejected by <b>" . $user->department . "</b>. Reason: " . $reason;
        $this->logApprovalAction($doc, $approval_status, $log_description, $reason);
        
        // Notify document creator about rejection
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been rejected by " . $user->department . ". Reason: " . $reason);
        
        return response()->json(['message' => 'Document Rejected', 'status' => 'success']);
    }

    private function handleHold($doc, $user, $request)
    {
        $approval_status = "Hold by " . $user->department;
        $reason = $request->input('message', 'No reason provided');
        
        $doc->update([
            'approval_status' => $approval_status,
            'status' => 'Hold',
            'updated_at' => now()
        ]);
        
        $log_description = "Document put on Hold by <b>" . $user->department . "</b>. Reason: " . $reason;
        $this->logApprovalAction($doc, $approval_status, $log_description, $reason);
        
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been put on hold by " . $user->department);
        
        return response()->json(['message' => 'Document on Hold', 'status' => 'success']);
    }

    private function handleClose($doc, $user, $request)
    {
        // Only the document creator or SuperAdmin can close
        if ($doc->by != Auth::id() && Auth::user()->role != 'SuperAdmin') {
            return response()->json(['message' => 'Only the document creator or admin can close this document', 'status' => 'error']);
        }

        $doc->update([
            'approval_status' => 'Closed by ' . $user->name,
            'status' => 'Closed',
            'updated_at' => now()
        ]);

        $log_description = "Document Closed by <b>" . $user->name . "</b>";
        $this->logApprovalAction($doc, 'Closed', $log_description, $request->input('message', $log_description));

        return response()->json(['message' => 'Document Closed', 'status' => 'success']);
    }

    private function getDocumentParticipants($doc)
    {
        $logUserIds = DB::table('approval_log')
            ->where('doc_id', $doc->id)
            ->pluck('by')
            ->filter()
            ->unique();

        $forwardingUserIds = DB::table('document_approval_forwardings')
            ->where('doc_id', $doc->id)
            ->pluck('forwarded_by')
            ->filter()
            ->unique();

        $approvalSequence = json_decode($doc->approval_sequence, true) ?? [];
        $sequenceUserIds = User::whereIn('department', $approvalSequence)
            ->whereIn('role', ['HOD', 'SuperAdmin'])
            ->where('is_active', 1)
            ->pluck('id');

        return collect([$doc->by])
            ->merge($logUserIds)
            ->merge($forwardingUserIds)
            ->merge($sequenceUserIds)
            ->filter()
            ->unique()
            ->values();
    }

    private function notifyAllParticipants($doc, $actor, $action)
    {
        $participantIds = $this->getDocumentParticipants($doc);
        $viewUrl = URL::to('view/document/' . $doc->id);
        $downloadUrl = route('download_document', $doc->id);

        $notificationMessage = 'Document <b>' . $doc->doc_id . '</b> has been <b>' . $action . '</b> by ' . $actor->name
            . '. You may view, comment, or download the document.';

        foreach ($participantIds as $userId) {
            $notificationController = new NotificationController();
            $notificationController->notificationEntry($userId, 'document', $doc->id, $notificationMessage);

            $participantUser = User::find($userId);
            if ($participantUser) {
                $mail_details = [
                    'content' => $notificationMessage
                        . '<br><br>ID - <b><i>' . $doc->doc_id . '</i></b><br>Titled - <b><i>' . $doc->title . '</i></b>'
                        . '<br><br><a href="' . $downloadUrl . '" style="color:#056b0d;font-weight:bold;">Download Document (PDF)</a>',
                    'url'     => $viewUrl,
                    'title'   => 'Document Update: ' . $action,
                ];

                SendTicketNotificationMail::dispatch(
                    $participantUser,
                    $mail_details,
                    'Document Update (' . $action . ') - ' . $doc->doc_id . ': ' . $doc->title,
                    'frontend.email.document_notifications'
                );
            }
        }
    }

    private function handleForward($doc, $user, $request)
    {
        $request->validate([
            'forward_to' => 'required|string',
            'message' => 'required|string',
        ]);

        $forwardTo = $request->input('forward_to');
        $approval_status = "Forwarded to " . $forwardTo . " by " . $user->department;
        
        $doc->update([
            'approval_status' => $approval_status,
            'status' => "Sent to " . $forwardTo,
            'forwarded_to' => $forwardTo,
            'to' => $forwardTo,
            'updated_at' => now()
        ]);
        
        // Add to forwarding table
        DB::table('document_approval_forwardings')->insert([
            'doc_id' => $doc->id,
            'forwarded_by' => Auth::id(),
            'forwarded_to' => $forwardTo,
            'created_at' => now()
        ]);
        
        $log_description = "Document forwarded to " . $forwardTo . " by " . $user->department;
        $this->logApprovalAction($doc, $approval_status, $log_description, $request->input('message'));
        
        // Notify the forwarded department
        $this->sendNotificationToDepartment($forwardTo, $doc, "A document has been forwarded to you by " . $user->department);
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been forwarded to " . $forwardTo);
        
        return response()->json(['message' => 'Document Forwarded', 'status' => 'success']);
    }

    private function handleComment($doc, $user, $request)
    {
        $approval_status = "Commented by " . $user->department;
        
        $doc->update([
            'approval_status' => $approval_status,
            'updated_at' => now()
        ]);
        
        $log_description = "Comment added by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, $approval_status, $log_description, $request->input('message'));
        
        // Handle file upload in comment
        if ($request->file('file')) {
            $year = date('Y');
            $month = date('m');
            $folderPath = "uploads/{$year}/{$month}";
            
            $originalFileName = $request->file('file')->getClientOriginalName();
            $uniqueName = pathinfo($originalFileName, PATHINFO_FILENAME) . '-' . uniqid() . '.' . $request->file('file')->getClientOriginalExtension();
            $filePath = "{$folderPath}/{$uniqueName}";
            
            $request->file('file')->storeAs($folderPath, $uniqueName, 'public');
            
            DB::table('document_annexures')->insert([
                'doc_id' => $doc->id,
                'annexure' => $filePath,
                'created_at' => now()
            ]);
        }
        
        $this->sendNotificationToUser($doc->by, $doc, "A comment has been added to your document by " . $user->department);
        
        return response()->json(['message' => 'Comment Added', 'status' => 'success']);
    }

    private function handleNoted($doc, $user, $request)
    {
        $approval_status = "Noted by " . $user->department;
        
        $doc->update([
            'approval_status' => $approval_status,
            'status' => 'Noted',
            'updated_at' => now()
        ]);
        
        $log_description = "Document marked as Noted by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, $approval_status, $log_description, $request->input('message', $log_description));
        
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been marked as Noted by " . $user->department);
        
        return response()->json(['message' => 'Document Noted', 'status' => 'success']);
    }

    private function handlePending($doc, $user, $request)
    {
        $approval_status = "Pending by " . $user->department;
        
        $doc->update([
            'approval_status' => $approval_status,
            'status' => 'Pending',
            'updated_at' => now()
        ]);
        
        $log_description = "Document marked as Pending by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, $approval_status, $log_description, $request->input('message', $log_description));
        
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been marked as Pending by " . $user->department);
        
        return response()->json(['message' => 'Document Pending', 'status' => 'success']);
    }

    private function handleDiscuss($doc, $user, $request)
    {
        $approval_status = $user->department . " Called for Discussion";
        
        $doc->update([
            'approval_status' => $approval_status,
            'status' => 'Discussion',
            'updated_at' => now()
        ]);
        
        $log_description = "Document called for discussion by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, $approval_status, $log_description, $request->input('message', $log_description));
        
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been called for discussion by " . $user->department);
        
        return response()->json(['message' => 'Document Called for Discussion', 'status' => 'success']);
    }

    private function handlePaymentInProgress($doc, $user, $request)
    {
        $assigned_to = $request->input('assigned_to');
        $status = "Payment In Progress";
        
        $exists = DB::table('payment_processing')->where('doc_id', $doc->id)->first();
        
        if (!$exists) {
            DB::table('payment_processing')->insert([
                'doc_id' => $doc->id,
                'assigned_to' => $assigned_to,
                'status' => $status,
                'expenditure_id' => $request->input('expenditure_id'),
                'remarks' => $request->input('remarks'),
                'created_at' => now(),
            ]);
        } else {
            DB::table('payment_processing')->where('doc_id', $doc->id)->update([
                'assigned_to' => $assigned_to,
                'status' => $status,
                'remarks' => $request->input('remarks'),
                'updated_at' => now(),
            ]);
        }
        
        $this->sendNotificationToUser($assigned_to, $doc, "A document has been assigned to you for payment processing");
        
        return response()->json(['message' => 'Payment Processing Started', 'status' => 'success']);
    }

    private function handleCompleted($doc, $user, $request)
    {
        // Only the document creator can manually complete the document
        if ($doc->by != $user->id) {
            return response()->json(['message' => 'Only the document creator can complete the document process.', 'status' => 'error']);
        }

        // All approvals must be done before creator can complete
        $approvalSequence = json_decode($doc->approval_sequence, true) ?? [];
        if (!empty($approvalSequence) && ($doc->current_sequence_index < count($approvalSequence))) {
            return response()->json(['message' => 'The approval process is not yet complete. Please wait for all approvers to finish.', 'status' => 'error']);
        }

        $approval_status = "Completed by " . $user->department;

        $doc->update([
            'approval_status' => $approval_status,
            'status' => 'Completed',
            'updated_at' => now()
        ]);

        $log_description = "Document process completed by <b>" . $user->name . "</b> (" . $user->department . ")";
        $this->logApprovalAction($doc, $approval_status, $log_description, $request->input('message', $log_description));

        $this->sendNotificationToUser($doc->by, $doc, "Your document has been marked as Completed");

        return response()->json(['message' => 'Document process completed successfully.', 'status' => 'success']);
    }

    // Helper methods for notifications and logging
    private function sendNotificationToDepartment($department, $doc, $message)
    {
        $users = User::where('department', $department)
            ->whereIn('role', ['HOD', 'SuperAdmin'])
            ->where('is_active', 1)
            ->get();
            
        foreach ($users as $user) {
            $this->sendNotificationToUser($user->id, $doc, $message);
        }
    }
    
    private function sendNotificationToUser($userId, $doc, $message)
    {
        $notificationController = new NotificationController();
        $notificationController->notificationEntry($userId, 'document', $doc->id, $message);
        
        $user = User::find($userId);
        if ($user) {
            $mail_details = [
                'content' => $message . '<br><br>ID - <b><i>' . $doc->doc_id . '</i></b><br>Titled - <b><i>' . $doc->title . '</i></b>',
                'url' => URL::to('view/document/' . $doc->id),
                'title' => 'Document Status Update'
            ];
            
            SendTicketNotificationMail::dispatch($user, $mail_details, 'Document Update - ' . $doc->doc_id, 'frontend.email.document_notifications');
        }
    }
    
    private function logApprovalAction($doc, $status, $description, $message)
    {
        DB::table('document_logs')->insert([
            'doc_id' => $doc->id,
            'description' => $description,
            'created_at' => now()
        ]);

        DB::table('approval_log')->insert([
            'doc_id' => $doc->id,
            'status' => $status,
            'message' => $message ?: $description,
            'by' => Auth::id(),
            'created_at' => now()
        ]);
    }
    
    private function handleFileUploads($request, $docId, $user)
    {
        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                if ($file) {
                    $year = date('Y');
                    $month = date('m');
                    $folderPath = "Documents/{$year}/{$month}/{$user->department}/{$user->name}";
                    
                    do {
                        $originalFileName = $file->getClientOriginalName();
                        $originalFileName = pathinfo($originalFileName, PATHINFO_FILENAME);
                        $uniqueName = $originalFileName . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $filePath = "{$folderPath}/{$uniqueName}";
                        $filePath = trim($filePath);
                    } while (Storage::disk('public')->exists($filePath));
                    
                    $file->storeAs($folderPath, $uniqueName, 'public');
                    
                    DB::table('document_annexures')->insert([
                        'doc_id' => $docId,
                        'annexure' => $filePath,
                        'created_at' => now()
                    ]);
                }
            }
        }
    }
    
    private function logDocumentCreation($document, $user, $status, $forwardedTo)
    {
        $createdAt = Carbon::parse($document->created_at);
        $formattedCreatedAt = $createdAt->format('M d, Y g:ia');
        
        $log_description = "Document Created by <b>" . $user->name . " , " . $user->department . "</b> at " . $formattedCreatedAt;
        
        DB::table('document_logs')->insert([
            'doc_id' => $document->id,
            'description' => $log_description,
            'created_at' => now()
        ]);
        
        if ($status != 'Draft') {
            $log_message = "Sent to " . $forwardedTo . " for approval at " . $formattedCreatedAt;
            DB::table('document_logs')->insert([
                'doc_id' => $document->id,
                'description' => $log_message,
                'created_at' => now()
            ]);
        }
    }
    
    private function sendNotifications($document, $user, $forwardedTo, $status)
    {
        $notificationController = new NotificationController();
        
        if ($status == 'Draft') {
            $notificationController->notificationEntry($user->id, 'document', $document->id, 'Your Document was created as Draft');
        } else {
            // Notify the first approver
            $this->sendNotificationToDepartment($forwardedTo, $document, '<b>' . $user->name . '</b> from <b>' . $user->department . ' sent a Document for approval');
        }
    }
    
    private function handleTicketConversion($request, $document, $newDocId)
    {
        $ticket = Ticket::find($request->input('ticket_id'));
        if ($ticket) {
            $ticket->update([
                'doc_id' => $document->id,
                'updated_at' => now()
            ]);
            
            $notificationController = new NotificationController();
            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket->id, 
                'Your Ticket - <b>' . $ticket->title . '</b> has been converted as Request');
        }
    }
    
    // View document with approval sequence
    public function viewDocument($doc_id)
    {
        $activeMenu = "document";
        $activeDropdown = "my_doc";
        
        try {
            if(Auth::id() == 139){
                $doc = DocumentApproval::withTrashed()->findOrFail($doc_id);
            } else {
                $doc = DocumentApproval::findOrFail($doc_id);
            }
        } catch (\Exception $e) {
            abort(404, 'Document not found');
        }
        
        // CHECK ACCESS RIGHTS
        $user = Auth::user();
        $hasAccess = false;
        
        // 1. Document creator has access
        if ($doc->by == $user->id) {
            $hasAccess = true;
            $activeDropdown = "my_doc";
        }
        
        // 2. Current approver (forwarded_to or to field) has access
        if ($doc->forwarded_to == $user->department || $doc->to == $user->department) {
            $hasAccess = true;
            $activeDropdown = "forwarded_doc";
        }
        
        // 3. Check if document was ever forwarded to this user's department
        $wasForwarded = DB::table('document_approval_forwardings')
            ->where('doc_id', $doc->id)
            ->where('forwarded_to', $user->department)
            ->exists();
        
        if ($wasForwarded) {
            $hasAccess = true;
            $activeDropdown = "forwarded_doc";
        }
        
        // 4. Check approval log for any action by this user's department
        $hasAction = DB::table('approval_log')
            ->where('doc_id', $doc->id)
            ->leftJoin('users', 'approval_log.by', '=', 'users.id')
            ->where('users.department', $user->department)
            ->exists();
        
        if ($hasAction) {
            $hasAccess = true;
        }
        
        // 5. SuperAdmin has access to all
        if ($user->role == 'SuperAdmin') {
            $hasAccess = true;
        }
        
        // 6. Check if user is in the approval sequence
        $approvalSequence = json_decode($doc->approval_sequence, true) ?? [];
        if (in_array($user->department, $approvalSequence)) {
            $hasAccess = true;
            $currentIndex = $doc->current_sequence_index ?? 0;
            if (isset($approvalSequence[$currentIndex]) && $approvalSequence[$currentIndex] == $user->department) {
                $activeDropdown = "forwarded_doc";
            }
        }
        
        // DENY ACCESS if none of the above conditions are met
        if (!$hasAccess) {
            abort(403, 'You do not have permission to view this document. This document has not been forwarded to your department.');
        }
        
        // SAFE: Get creator info
        $creator = User::find($doc->by);
        $creatorName = optional($creator)->name ?? 'Unknown User (Deleted)';
        $creatorDept = optional($creator)->department ?? 'Unknown Department';
        
        $currentIndex = $doc->current_sequence_index ?? 0;
        $nextApprover = isset($approvalSequence[$currentIndex]) ? $approvalSequence[$currentIndex] : null;
        $isComplete = $currentIndex >= count($approvalSequence);
        
        $annexures = DB::table('document_annexures')->where('doc_id', $doc_id)->get();
        $document_logs = DB::table('document_logs')->where('doc_id', $doc_id)->get();
        
        // Get approval logs with user details
        $approval_logs = DB::table('approval_log')
            ->where('doc_id', $doc_id)
            ->leftJoin('users', 'approval_log.by', '=', 'users.id')
            ->select('approval_log.*', 'users.name as user_name', 'users.department as user_department', 'users.role as user_role')
            ->orderBy('approval_log.created_at', 'desc')
            ->get();
        
        $pay = DB::table('payment_details')->where('doc_id', $doc_id)->get();
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        // Check if current approver is PA to Chairman or PA to GM (Finance Head selection needed)
        $needsFinanceSelection = in_array($nextApprover, ['PA to Chairman', 'PA to GM']);
        
        // Get available Finance Heads
        $financeHeads = [
            'Finance Head Karaikal',
            'Finance Head Salem',
            'Finance Head Chennai',
            'Finance Head Pondy'
        ];
        
        // Get current year for expenditure ID generation
        $currentYear = Carbon::now()->year;
        $yearShort = Carbon::now()->format('y');
        $count = DB::table('payment_processing')->whereYear('created_at', $currentYear)->count();
        
        if ($currentYear == 2025) {
            $increment = $count + 1201;
        } else {
            $increment = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        }
        $expenditure_id = "Ex{$yearShort}.{$increment}";
        
        return view('frontend.document.view_doc', compact(
            'activeMenu', 'activeDropdown', 'doc', 'annexures', 
            'document_logs', 'approval_logs', 'departments', 'pay', 
            'expenditure_id', 'approvalSequence', 'currentIndex', 
            'nextApprover', 'isComplete', 'creatorName', 'creatorDept',
            'needsFinanceSelection', 'financeHeads'
        ));
    }
    
    // Get received documents for current user's department
    public function receivedDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "received_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'from', 'priority', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = DocumentApproval::where('to', Auth::user()->department)
            ->whereNotIn('status', ['Draft']);
        
        $docs = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());
        
        return view('frontend.document.received_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }
    
    // My documents (created by current user)
    public function myDocuments()
    {
        $activeMenu = "document";
        $activeDropdown = "my_doc";
        
        $sort_by = request('sort_by', 'created_at');
        $sort_dir = request('sort_dir', 'desc');
        
        $allowedSorts = ['priority', 'doc_id', 'title'];
        if (!in_array($sort_by, $allowedSorts)) {
            $sort_by = 'created_at';
        }
        
        $docs = DocumentApproval::where('by', Auth::id())
            ->orderBy($sort_by, $sort_dir)
            ->paginate(10)
            ->appends(request()->query());
        
        return view('frontend.document.my_doc', compact('activeMenu', 'activeDropdown', 'docs'));
    }
    
    // Draft documents
    public function draftDocuments()
    {
        $activeMenu = "document";
        $activeDropdown = "draft_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $docs = DocumentApproval::where('from', Auth::user()->department)
            ->whereIn('status', ['Draft', 'Retracted'])
            ->latest()
            ->get();
        
        return view('frontend.document.draft_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }
    
    // New documents (submitted by current user)
    public function newDocuments()
    {
        $activeMenu = "document";
        $activeDropdown = "new_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $docs = DocumentApproval::where('from', Auth::user()->department)
            ->where('approval_status', null)
            ->whereNotIn('status', ['Draft', 'Retracted', 'Closed', 'Completed'])
            ->latest()
            ->get();
        
        if(Auth::user()->role == 'SuperAdmin'){
            $docs = DocumentApproval::where('status', 'Sent to Registrar')->latest()->get();
        }
        
        return view('frontend.document.new_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }
    
    // In-progress documents
    public function inProgressDocuments(Request $request)
    {
        $activeMenu = "document";
        $activeDropdown = "inProgress_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = DocumentApproval::query();
        
        if (Auth::user()->designation == 'Registrar') {
            $query->where('to', Auth::user()->department)->whereNotIn('status', ['Draft', 'Closed']);
        } elseif (in_array(Auth::user()->designation, ['VC', 'Pro-VC'])) {
            $query->where(function ($q) {
                $q->where('to', Auth::user()->department)->orWhere('to', 'Registrar');
            })->whereNotIn('status', ['Draft', 'Closed']);
        } else {
            $query->where('from', Auth::user()->department)->whereNotIn('status', ['Draft', 'Closed']);
        }
        
        $docs = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());
        
        return view('frontend.document.inProgress_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }
    
    // Closed documents
    public function closedDocuments(Request $request)
    {
        $activeMenu = "document";
        $activeDropdown = "closed_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = DocumentApproval::query()->where('status', 'Closed');
        
        if (Auth::user()->designation == 'Registrar') {
            $query->where('to', Auth::user()->department);
        } elseif (in_array(Auth::user()->designation, ['VC', 'Pro-VC', 'Chancellor'])) {
            $query->where(function ($q) {
                $q->where('to', Auth::user()->department)->orWhere('to', 'Registrar');
            });
        } else {
            $query->where('from', Auth::user()->department);
        }
        
        $docs = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());
        
        return view('frontend.document.closed_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }
    
    // Remove annexure
    public function removeAnnexure($id)
    {
        try {
            $attachment = DB::table('document_annexures')->where('id', $id)->first();
            Storage::disk('public')->delete($attachment->annexure);
            DB::table('document_annexures')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'File removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove the file.']);
        }
    }
    
    // Download document as PDF
    public function downloadDocument($doc_id)
    {
        ini_set('memory_limit', '512M');
        
        $doc = DocumentApproval::findOrFail($doc_id);
        $user = User::findOrFail($doc->by);
        $annexures = DB::table('document_annexures')->where('doc_id', $doc_id)->get();
        $document_logs = DB::table('document_logs')->where('doc_id', $doc_id)->get();
        $approval_logs = DB::table('approval_log')->where('doc_id', $doc_id)->latest()->get();
        $pay = DB::table('payment_details')->where('doc_id', $doc_id)->get();
        
        $pdf = PDF::loadView('frontend.document.download.template', compact('doc', 'annexures', 'document_logs', 'approval_logs', 'user', 'pay'));
        
        return $pdf->download('Document - ' . $doc->doc_id . '.pdf');
    }
    
    // Edit payment details
    public function editPaymentDetails($id)
    {
        $activeMenu = "payment_details";
        $activeDropdown = "";
        $paymentDetails = PaymentDetails::findOrFail($id);
        return view('frontend.document.edit_payment_details', compact('activeMenu', 'activeDropdown', 'paymentDetails'));
    }
    
    // Update payment details
    public function updatePaymentDetails(Request $request, $id)
    {
        $payment = DB::table('payment_details')->where('id', $id)->first();
        
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment details not found.');
        }
        
        DB::table('payment_details')->where('id', $id)->update([
            'mode' => $request->input('mode'),
            'payment_reference_no' => $request->input('payment_reference_no'),
            'payment_date' => $request->input('payment_date'),
            'paid_amount' => $request->input('paid_amount'),
            'tds_amount' => $request->input('tds_amount'),
            'expenditure_id' => $request->input('expenditure_id'),
            'expenditure_category' => $request->input('expenditure_category') === 'other' 
                ? $request->input('custom_expenditure_category')
                : $request->input('expenditure_category'),
            'payment_type' => $request->input('payment_type'),
            'remarks' => $request->input('remarks'),
            'cheque_issue_date' => $request->input('cheque_issue_date'),
            'cheque_cleared_date' => $request->input('cheque_cleared_date'),
            'bill_amount' => $request->input('bill_amount'),
            'bill_submission_date' => $request->input('bill_submission_date'),
            'refund_amount' => $request->input('refund_amount'),
            'refund_date' => $request->input('refund_date'),
            'updated_at' => now()
        ]);
        
        return redirect('/view/document/' . $payment->doc_id)->with('success', 'Payment details updated successfully.');
    }
    
    // Delete payment details
    public function deletePaymentDetails($id)
    {
        $paymentDetails = PaymentDetails::findOrFail($id);
        $doc_id = $paymentDetails->doc_id;
        $paymentDetails->delete();
        
        return redirect('/view/document/' . $doc_id)->with('success', 'Payment deleted successfully.');
    }
    
    // Change finance status
    public function changeFinanceStatus($status, $finance_id)
    {
        DB::table('payment_processing')->where('id', $finance_id)->update([
            'status' => $status,
            'updated_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Status updated successfully');
    }
    
    // Search departments (for AJAX)
    public function searchDepartments(Request $request)
    {
        $query = $request->get('query');
        $departments = Department::where('dept_label', 'like', "%{$query}%")
            ->orWhere('dept_name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['dept_label', 'dept_name']);
        
        return response()->json($departments);
    }
    
    // Search staff (for AJAX)
    public function searchStaff(Request $request)
    {
        $query = $request->get('query');
        $staff = User::where('name', 'like', "%{$query}%")
            ->orWhere('department', 'like', "%{$query}%")
            ->where('is_active', 1)
            ->limit(10)
            ->get(['id', 'name', 'department']);
        
        return response()->json($staff);
    }
    
    public function pendingDocuments(){
        $activeMenu = "document";
        $activeDropdown = "pending_doc";
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $docs = DocumentApproval::whereNotIn('status', ['Draft', 'Closed'])
            ->where(function ($query) {
                $query->where('forwarded_to', Auth::user()->department)
                    ->orWhere('to', Auth::user()->department);
            })->paginate(5);
        
        return view('frontend.document.pending_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }

    /**
     * Get approved documents
     */
    public function approvedDocuments(Request $request)
    {
        $activeMenu = "document";
        $activeDropdown = "approved_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at', 'from'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = DocumentApproval::query();
        
        // Role-based filtering
        if (Auth::user()->designation == 'Registrar') {
            $query->where('to', Auth::user()->department)
                  ->whereIn('status', ['Approved', 'Closed']);
        } elseif (in_array(Auth::user()->designation, ['VC', 'Pro-VC'])) {
            $query->where(function($q) {
                    $q->where('to', Auth::user()->department)
                      ->orWhere('to', 'Registrar');
                })
                ->whereIn('status', ['Approved', 'Closed']);
        } else {
            $query->where('from', Auth::user()->department)
                  ->whereIn('status', ['Approved', 'Closed']);
        }
        
        // Apply search filters if any
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $docId = str_pad($request->doc_id, 4, '0', STR_PAD_LEFT);
            $query->where('doc_id', 'like', 'REG-DOC-' . $docId);
        }
        
        if ($request->filled('section')) {
            $query->where('from', $request->section);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $docs = $query->orderBy($sortBy, $sortDir)
                      ->paginate(10)
                      ->appends($request->query());
        
        return view('frontend.document.approved_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }

    /**
     * Get total documents (all documents across the system)
     */
    public function totalDocuments(Request $request)
    {
        $activeMenu = "document";
        $activeDropdown = "total_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at', 'from', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = DocumentApproval::query();
        
        // Role-based filtering
        if (Auth::user()->role == 'SuperAdmin') {
            $query->whereNotIn('status', ['Draft']);
        } elseif (Auth::user()->designation == 'Registrar') {
            $query->where('to', Auth::user()->department)
                  ->whereNotIn('status', ['Draft']);
        } elseif (in_array(Auth::user()->designation, ['VC', 'Pro-VC', 'Chancellor'])) {
            $query->where(function($q) {
                    $q->where('to', Auth::user()->department)
                      ->orWhere('to', 'Registrar');
                })
                ->whereNotIn('status', ['Draft']);
        } else {
            $query->where('from', Auth::user()->department)
                  ->whereNotIn('status', ['Draft']);
        }
        
        // Apply search filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $docId = str_pad($request->doc_id, 4, '0', STR_PAD_LEFT);
            $query->where('doc_id', 'like', 'REG-DOC-' . $docId);
        }
        
        if ($request->filled('section')) {
            $query->where('from', $request->section);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $docs = $query->orderBy($sortBy, $sortDir)
                      ->paginate(10)
                      ->appends($request->query());
        
        return view('frontend.document.total_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }

    public function searchSADocuments(Request $request)
    {
        $activeMenu = "document";
        $type = $request->query('type', 'approved');
        $activeDropdown = "{$type}_doc";

        if($type == 'report-doc'){
            $activeMenu = "report_doc";
            $activeDropdown = "";
        }

        $departments = Department::orderBy('dept_name', 'asc')->get();

        $approvalStatusData = $this->getAllApprovalStatusData();
        $yetToApproveIds = $this->getYetToApproveIds();
        $statusCounts = $this->getConsolidatedApprovalStatusCounts($approvalStatusData, $yetToApproveIds);

        if(Auth::id() == 139){
            $query = DocumentApproval::withTrashed();
        } else {
            $query = DocumentApproval::query();
        }

        if ($type === 'approved') {
            $query->where('status', 'Approved');
        } elseif ($type === 'rejected') {
            $query->where('status', 'Rejected');
        } elseif ($type === 'closed') {
            $query->where('status', 'Closed');
        } elseif ($type === 'received') {
            $query->where('status', 'Sent to Registrar');
        } elseif ($type === 'inProgress'){
            $query->whereNotIn('status', ['Closed', 'Rejected', 'Approved', 'Sent to Registrar']); 
        }

        // Apply search filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $docId = str_pad($request->doc_id, 4, '0', STR_PAD_LEFT);
            $query->where('doc_id', 'like', 'REG-DOC-' . $docId);
        }

        if ($request->filled('section')) {
            $query->where('from', $request->section);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $docs = $query->latest()->paginate(10)->appends($request->query());

        return view('frontend.document.vc.search', array_merge(
            [
                'documents' => $docs,
                'activeMenu' => $activeMenu,
                'activeDropdown' => $activeDropdown,
                'yetToApproveCount' => $statusCounts['newCount'],
                'departments' => $departments
            ],
            $statusCounts
        ));
    }
    
    public function rejectedDocuments(Request $request)
    {
        $activeMenu = "document";
        $activeDropdown = "rejected_doc";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at', 'from'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = DocumentApproval::where('status', 'Rejected');
        
        if (Auth::user()->designation == 'Registrar') {
            $query->where('to', Auth::user()->department);
        } elseif (in_array(Auth::user()->designation, ['VC', 'Pro-VC'])) {
            $query->where(function($q) {
                $q->where('to', Auth::user()->department)
                  ->orWhere('to', 'Registrar');
            });
        } else {
            $query->where('from', Auth::user()->department);
        }
        
        // Apply search filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $docId = str_pad($request->doc_id, 4, '0', STR_PAD_LEFT);
            $query->where('doc_id', 'like', 'REG-DOC-' . $docId);
        }
        
        if ($request->filled('section')) {
            $query->where('from', $request->section);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $docs = $query->orderBy($sortBy, $sortDir)
                      ->paginate(10)
                      ->appends($request->query());
        
        return view('frontend.document.rejected_doc', compact('activeMenu', 'activeDropdown', 'docs', 'departments'));
    }
    
    public function searchForwardedDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "forwarded_doc";
        
        $query = DocumentApprovalForwardings::query()
            ->join('document_approvals', 'document_approvals.id', '=', 'document_approval_forwardings.doc_id')
            ->where('document_approval_forwardings.forwarded_to', Auth::user()->department);
        
        if ($request->filled('title')) {
            $query->where('document_approvals.title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        
        if ($request->filled('approval_status')) {
            $query->where('document_approvals.approval_status', $request->approval_status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('document_approval_forwardings.created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('document_approval_forwardings.created_at', '<=', $request->date_to);
        }
        
        $docs = $query->select(
                'document_approval_forwardings.*',
                'document_approvals.title',
                'document_approvals.status',
                'document_approval_forwardings.created_at',
                'document_approvals.approval_status',
                'document_approvals.priority'
            )->paginate(10)
            ->appends($request->query());
        
        return view('frontend.document.forwarded_doc', compact('docs', 'activeMenu', 'activeDropdown'));
    }

    /**
     * Search documents for my documents page
     */
    public function searchDocuments(Request $request)
    {
        $activeMenu = "document";
        $activeDropdown = "my_doc";
        
        $query = DocumentApproval::where('by', Auth::id());
        
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $query->where('doc_id', 'like', $request->doc_id);
        }
        
        if ($request->filled('approval_status')) {
            $query->where('status', $request->approval_status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $docs = $query->latest()->paginate(10)->appends($request->query());
        
        return view('frontend.document.my_doc', compact('docs', 'activeMenu', 'activeDropdown'));
    }

    /**
     * Get all payment details
     */
    public function showAllPayments(Request $request)
    {
        $activeMenu = "payment_details";
        $activeDropdown = "";
        
        $sortBy = $request->get('sort_by', 'payment_details.created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = [
            'payment_details.created_at',
            'payment_details.payment_reference_no',
            'payment_details.mode',
            'payment_details.paid_amount',
            'payment_details.expenditure_id',
            'document_approvals.doc_id',
            'payment_details.payment_date',
            'payment_details.payment_type',
        ];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'payment_details.created_at';
        }
        
        $query = DB::table('payment_details')
            ->join('document_approvals', 'payment_details.doc_id', '=', 'document_approvals.id')
            ->select(
                'payment_details.*',
                'document_approvals.doc_id as document_number',
                'document_approvals.from as forwarded_from',
                'document_approvals.subject',
                'document_approvals.currency as currency',
                'document_approvals.id as document_primary_id'
            );
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        
        if ($request->filled('from')) {
            $query->where('document_approvals.from', 'like', '%' . $request->from . '%');
        }
        
        if ($request->filled('mode')) {
            $query->where('payment_details.mode', 'like', '%' . $request->mode . '%');
        }
        
        if ($request->filled('expenditure_id')) {
            $query->where('payment_details.expenditure_id', 'like', '%' . $request->expenditure_id . '%');
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_details.payment_type', 'like', '%' . $request->payment_type . '%');
        }
        
        if ($request->filled('paid_amount')) {
            $query->where('payment_details.paid_amount', 'like', '%' . $request->paid_amount . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_details.payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_details.payment_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy($sortBy, $sortDir)
                          ->paginate(10)
                          ->appends($request->query());
        
        return view('frontend.document.payment_details', compact('payments', 'activeMenu', 'activeDropdown'));
    }

    /**
     * Delete document
     */
    public function deleteDocument(Request $request)
    {
        $doc = DocumentApproval::findOrFail($request->input('doc_id'));
        
        if (Auth::id() == 139) {
            $doc->update([
                'approval_status' => 'Deleted by Admin',
                'status' => 'Deleted by Admin',
                'updated_at' => now()
            ]);
            
            $createdAt = Carbon::parse($doc->updated_at);
            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');
            
            $log_description = "Document deleted by admin";
            
            DB::table('document_logs')->insert([
                'doc_id' => $doc->id,
                'description' => $log_description,
                'created_at' => now()
            ]);
            
            $message = $request->input('message');
            $clean_message = trim(strip_tags($message));
            $log_message = $clean_message === '' ? $log_description : $message;
            
            DB::table('approval_log')->insert([
                'doc_id' => $doc->id,
                'status' => $log_description,
                'message' => $log_message,
                'by' => Auth::id(),
                'created_at' => now()
            ]);
            
            $notificationController = new notificationController();
            $notificationController->notificationEntry($doc->by, 'document', $doc->id, 'Your document titled <b>' . $doc->title . '</b> was <b> deleted by admin</b>');
            
            $email_to = User::find($doc->by);
            $mail_details['content'] = 'Your Request was deleted at ' . now()->format('M d, Y g:ia') . '<br><br>ID - <b><i>' . $doc->doc_id . '</i></b><br>Titled - <b><i>' . $doc->title . '</i></b>';
            $mail_details['url'] = URL::to('view/document/' . $doc->id);
            $mail_details['title'] = 'Request deleted';
            $subject = 'Status Update - ' . $doc->doc_id . ': ' . $doc->title;
            
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.document_notifications');
            
            $doc->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document deleted successfully.',
                'redirect' => route('deleted_documents')
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Denied: Contact ICT'
            ]);
        }
    }

    /**
     * Get deleted documents
     */
    public function deletedDocuments()
    {
        $activeMenu = "deleted_doc";
        $activeDropdown = "";
        
        $departments = Department::where('is_active', 1)->get();
        
        $sort_by = request('sort_by', 'created_at');
        $sort_dir = request('sort_dir', 'desc');
        
        $allowedSorts = ['priority', 'doc_id', 'title'];
        if (!in_array($sort_by, $allowedSorts)) {
            $sort_by = 'created_at';
        }
        
        $documents = DocumentApproval::onlyTrashed()
            ->orderBy($sort_by, $sort_dir)
            ->paginate(10)
            ->appends(request()->query());
        
        return view('frontend.document.deleted_doc', compact('activeMenu', 'activeDropdown', 'documents', 'departments'));
    }

    /**
     * Get report documents
     */
    public function reportDoc()
    {
        $activeMenu = "report-doc";
        $activeDropdown = "";
        
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $docs = DocumentApproval::where('status', '!=', 'Draft')->latest()->get();
        
        return view('frontend.document.report', compact('docs', 'activeMenu', 'activeDropdown', 'departments'));
    }

    /**
     * Download report as PDF
     */
    public function downloadReport()
    {
        $departments = Department::orderBy('dept_name', 'asc')->get();
        
        $docs = DocumentApproval::where('from', Auth::user()->department)
            ->whereNotIn('status', ['Draft', 'Closed'])
            ->latest()
            ->get();
        
        $approvalLogs = DB::table('approval_log')
            ->whereIn('doc_id', $docs->pluck('id'))
            ->get()
            ->groupBy('doc_id');
        
        $pdf = PDF::loadView('frontend.document.download.report_template', compact('docs', 'approvalLogs'));
        
        return $pdf->download('Document_Report.pdf');
    }

    /**
     * Download forwarded document report as PDF
     */
    public function downloadForwardedDocReport(Request $request)
    {
        $query = DocumentApprovalForwardings::query()
            ->join('document_approvals', 'document_approvals.id', '=', 'document_approval_forwardings.doc_id')
            ->where('document_approval_forwardings.forwarded_to', Auth::user()->department);
        
        if ($request->filled('title')) {
            $query->where('document_approvals.title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        
        if ($request->filled('approval_status')) {
            $query->where('document_approvals.approval_status', $request->approval_status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('document_approval_forwardings.created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('document_approval_forwardings.created_at', '<=', $request->date_to);
        }
        
        $docs = $query->select(
                'document_approval_forwardings.*',
                'document_approvals.title',
                'document_approvals.priority',
                'document_approvals.doc_id as document_doc_id',
                'document_approval_forwardings.created_at as document_created_at',
                'document_approvals.from as document_from'
            )
            ->get();
        
        $approvalLogs = DB::table('approval_log')
            ->whereIn('doc_id', $docs->pluck('id'))
            ->get()
            ->groupBy('doc_id');
        
        $pdf = PDF::loadView('frontend.document.download.report_forwarded_doc_template', compact('docs', 'approvalLogs'));
        
        return $pdf->download('Forwarded_Document_Report.pdf');
    }

    /**
     * Download forwarded document report as Excel
     */
    public function downloadForwardedDocExcel(Request $request)
    {
        $query = DocumentApprovalForwardings::query()
            ->join('document_approvals', 'document_approvals.id', '=', 'document_approval_forwardings.doc_id')
            ->where('document_approval_forwardings.forwarded_to', Auth::user()->department);
        
        if ($request->filled('title')) {
            $query->where('document_approvals.title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        
        if ($request->filled('approval_status')) {
            $query->where('document_approvals.approval_status', $request->approval_status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('document_approval_forwardings.created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('document_approval_forwardings.created_at', '<=', $request->date_to);
        }
        
        $docs = $query->select(
                'document_approval_forwardings.*',
                'document_approvals.title',
                'document_approvals.priority',
                'document_approvals.doc_id as document_doc_id',
                'document_approval_forwardings.created_at as document_created_at',
                'document_approvals.from as document_from'
            )
            ->get();
        
        $approvalLogs = DB::table('approval_log')
            ->whereIn('doc_id', $docs->pluck('id'))
            ->get()
            ->groupBy('doc_id');
        
        return Excel::download(new ForwardedDocumentsExport($docs, $approvalLogs), 'Forwarded_Document_Report.xlsx');
    }

    /**
     * Download payment details as PDF
     */
    public function downloadPaymentDetailsPDF(Request $request)
    {
        $query = DB::table('payment_details')
            ->join('document_approvals', 'payment_details.doc_id', '=', 'document_approvals.id')
            ->select(
                'payment_details.*',
                'document_approvals.doc_id as document_number',
                'document_approvals.from as forwarded_from',
                'document_approvals.subject',
                'document_approvals.id as document_primary_id'
            );
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        if ($request->filled('from')) {
            $query->where('document_approvals.from', 'like', '%' . $request->from . '%');
        }
        if ($request->filled('mode')) {
            $query->where('payment_details.mode', 'like', '%' . $request->mode . '%');
        }
        if ($request->filled('expenditure_id')) {
            $query->where('payment_details.expenditure_id', 'like', '%' . $request->expenditure_id . '%');
        }
        if ($request->filled('payment_type')) {
            $query->where('payment_details.payment_type', 'like', '%' . $request->payment_type . '%');
        }
        if ($request->filled('reference_number')) {
            $query->where('payment_details.payment_reference_no', 'like', '%' . $request->reference_number . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('payment_details.payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_details.payment_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy('payment_details.created_at', 'desc')->get();
        
        $pdf = PDF::loadView('frontend.document.download.report_payment_details_template', ['payments' => $payments]);
        return $pdf->download('payment_details.pdf');
    }

    /**
     * Download payment details as Excel
     */
    public function downloadPaymentDetailsExcel(Request $request)
    {
        $query = DB::table('payment_details')
            ->join('document_approvals', 'payment_details.doc_id', '=', 'document_approvals.id')
            ->select(
                'payment_details.*',
                'document_approvals.doc_id as document_number',
                'document_approvals.from as forwarded_from',
                'document_approvals.subject',
                'document_approvals.id as document_primary_id'
            );
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        if ($request->filled('from')) {
            $query->where('document_approvals.from', 'like', '%' . $request->from . '%');
        }
        if ($request->filled('mode')) {
            $query->where('payment_details.mode', 'like', '%' . $request->mode . '%');
        }
        if ($request->filled('expenditure_id')) {
            $query->where('payment_details.expenditure_id', 'like', '%' . $request->expenditure_id . '%');
        }
        if ($request->filled('payment_type')) {
            $query->where('payment_details.payment_type', 'like', '%' . $request->payment_type . '%');
        }
        if ($request->filled('reference_number')) {
            $query->where('payment_details.payment_reference_no', 'like', '%' . $request->reference_number . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('payment_details.payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_details.payment_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy('payment_details.created_at', 'desc')->get();
        
        return Excel::download(new PaymentDetailsExport($payments), 'payment_details.xlsx');
    }

    /**
     * Get completed documents (documents that are fully approved and completed)
     */
    public function completedDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "completed_doc";

        $departments = Department::orderBy('dept_name', 'asc')->get();

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at', 'from', 'expenditure_id'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        // Subquery to get latest expenditure_id for each document
        $latestExpenditureSub = DB::table('payment_details')
            ->select('doc_id', DB::raw('MAX(expenditure_id) as latest_expenditure_id'))
            ->groupBy('doc_id');

        $sortColumnsMap = [
            'doc_id'         => 'document_approvals.doc_id',
            'title'          => 'document_approvals.title',
            'priority'       => 'document_approvals.priority',
            'created_at'     => 'document_approvals.created_at',
            'from'           => 'document_approvals.from',
            'expenditure_id' => 'latest_payments.latest_expenditure_id',
        ];

        // Query directly from document_approvals so ALL completed/closed docs are listed
        $query = DocumentApproval::query()
            ->leftJoinSub($latestExpenditureSub, 'latest_payments', function ($join) {
                $join->on('document_approvals.id', '=', 'latest_payments.doc_id');
            })
            ->whereIn('document_approvals.status', ['Closed', 'Completed']);

        // HOD sees only documents originating from their own department
        if (Auth::user()->role !== 'SuperAdmin') {
            $query->where('document_approvals.from', Auth::user()->department);
        }

        // Apply search filters
        if ($request->filled('title')) {
            $query->where('document_approvals.title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('doc_id')) {
            $docId = str_pad($request->doc_id, 4, '0', STR_PAD_LEFT);
            $query->where('document_approvals.doc_id', 'like', 'REG-DOC-' . $docId);
        }

        if ($request->filled('status') && in_array($request->status, ['Completed', 'Closed'])) {
            $query->where('document_approvals.status', $request->status);
        }

        if ($request->filled('section')) {
            $query->where('document_approvals.from', $request->section);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('document_approvals.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('document_approvals.created_at', '<=', $request->date_to);
        }

        $docs = $query->orderBy($sortColumnsMap[$sortBy] ?? 'document_approvals.created_at', $sortDir)
            ->select('document_approvals.*', 'latest_payments.latest_expenditure_id')
            ->distinct()
            ->paginate(10)
            ->appends($request->query());

        $docIds = $docs->pluck('id')->toArray();

        $payments = DB::table('payment_details')
            ->whereIn('doc_id', $docIds)
            ->select('doc_id', 'expenditure_id')
            ->get()
            ->groupBy('doc_id');

        return view('frontend.document.completed_doc', compact('activeMenu', 'activeDropdown', 'docs', 'payments', 'departments'));
    }

    /**
     * Get users who can approve for STB Office (multiple approvers)
     */
    private function getSTBOfficeApprovers()
    {
        return User::where('department', 'STB Office')
            ->where('role', 'SuperAdmin')
            ->where('is_active', 1)
            ->get();
    }

    /**
     * Get users who can approve for Chairman (multiple approvers)
     */
    private function getChairmanApprovers()
    {
        return User::where('department', 'Chairman')
            ->where('role', 'SuperAdmin')
            ->where('is_active', 1)
            ->get();
    }

    public function forwardedDocuments(Request $request) 
    {
        $activeMenu = "document_received";
        $activeDropdown = "forwarded_doc";
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['doc_id', 'title', 'priority', 'created_at', 'from'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $docsQuery = DocumentApprovalForwardings::query()
            ->select('document_approval_forwardings.*')
            ->distinct()
            ->join('document_approvals', 'document_approvals.id', '=', 'document_approval_forwardings.doc_id')
            ->where('document_approval_forwardings.forwarded_to', Auth::user()->department)
            ->whereNotIn('document_approvals.status', ['Closed', 'Completed', 'Draft'])
            ->select(
                'document_approval_forwardings.*',
                'document_approvals.title',
                'document_approvals.priority',
                'document_approvals.doc_id as document_doc_id',
                'document_approval_forwardings.created_at as document_created_at',
                'document_approvals.from as document_from'
            );
        
        if ($sortBy === 'created_at') {
            $docsQuery->orderBy("document_approval_forwardings.created_at", $sortDir);
        } else {
            $docsQuery->orderBy("document_approvals.$sortBy", $sortDir);
        }
        
        $docs = $docsQuery->paginate(10)->appends(request()->query());
        
        return view('frontend.document.forwarded_doc', compact('activeMenu', 'activeDropdown', 'docs'));
    }

    /**
     * Handle Payment Details addition by Finance Head
     */
    private function handlePaymentDetails($doc, $user, $request)
    {
        $request->validate([
            'mode' => 'required|string',
            'payment_reference_no' => 'required|string',
            'payment_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'expenditure_id' => 'required|string',
            'expenditure_category' => 'required|string',
            'payment_type' => 'required|string',
            'remarks' => 'nullable|string',
            'tds_amount' => 'nullable|numeric|min:0',
            'cheque_issue_date' => 'nullable|date',
            'cheque_cleared_date' => 'nullable|date',
        ]);
        
        // Handle custom expenditure category
        $expenditureCategory = $request->input('expenditure_category');
        if ($expenditureCategory === 'other') {
            $expenditureCategory = $request->input('custom_expenditure_category');
            if (empty($expenditureCategory)) {
                return response()->json([
                    'message' => 'Please specify the custom expenditure category',
                    'status' => 'error'
                ]);
            }
        }
        
        DB::table('payment_details')->insert([
            'doc_id' => $doc->id,
            'mode' => $request->input('mode'),
            'payment_reference_no' => $request->input('payment_reference_no'),
            'payment_date' => $request->input('payment_date'),
            'paid_amount' => $request->input('paid_amount'),
            'expenditure_id' => $request->input('expenditure_id'),
            'expenditure_category' => $expenditureCategory,
            'payment_type' => $request->input('payment_type'),
            'remarks' => $request->input('remarks'),
            'tds_amount' => $request->input('tds_amount', 0),
            'cheque_issue_date' => $request->input('cheque_issue_date'),
            'cheque_cleared_date' => $request->input('cheque_cleared_date'),
            'created_at' => now(),
        ]);
        
        // Update document status
        $doc->update([
            'status' => 'Payment Completed',
            'approval_status' => 'Payment processed by ' . $user->department,
            'updated_at' => now()
        ]);
        
        $log_description = "Payment details added by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, "Payment Added", $log_description, $request->input('remarks', $log_description));
        
        $this->sendNotificationToUser($doc->by, $doc, "Payment has been processed for your document");
        
        return response()->json([
            'message' => 'Payment details added successfully',
            'status' => 'success'
        ]);
    }

    /**
     * Handle Purchase Order creation by Purchase Head
     */
    private function handlePurchaseOrderCreation($doc, $user, $request)
    {
        $request->validate([
            'purchase_order' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'message' => 'nullable|string',
        ]);

        if (!$request->hasFile('purchase_order')) {
            return response()->json([
                'message' => 'Failed to upload Purchase Order file.',
                'status' => 'error'
            ]);
        }

        $file = $request->file('purchase_order');
        $folderPath = 'PurchaseOrders/' . date('Y') . '/' . date('m');
        $uniqueName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = "{$folderPath}/{$uniqueName}";
        $file->storeAs($folderPath, $uniqueName, 'public');

        DB::table('document_annexures')->insert([
            'doc_id' => $doc->id,
            'annexure' => $filePath,
            'created_at' => now()
        ]);

        // Advance workflow to next approver in the sequence
        $approvalSequence = json_decode($doc->approval_sequence, true);
        $currentIndex = $doc->current_sequence_index;
        $nextIndex = $currentIndex + 1;

        if ($nextIndex < count($approvalSequence)) {
            $nextApprover = $approvalSequence[$nextIndex];
            $doc->update([
                'purchase_order'         => $filePath,
                'status'                 => 'Sent to ' . $nextApprover,
                'forwarded_to'           => $nextApprover,
                'to'                     => $nextApprover,
                'current_sequence_index' => $nextIndex,
                'updated_at'             => now()
            ]);

            DB::table('document_approval_forwardings')->insert([
                'doc_id'       => $doc->id,
                'forwarded_by' => Auth::id(),
                'forwarded_to' => $nextApprover,
                'created_at'   => now()
            ]);

            $this->sendNotificationToDepartment($nextApprover, $doc, "Purchase Order has been created. Please review and proceed.");
        } else {
            $doc->update([
                'purchase_order'         => $filePath,
                'status'                 => 'Purchase Order Created',
                'current_sequence_index' => $nextIndex,
                'updated_at'             => now()
            ]);
        }

        $log_description = "Purchase Order raised by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, "Purchase Order Created", $log_description, $request->input('message') ?: $log_description);
        $this->sendNotificationToUser($doc->by, $doc, "Purchase Order has been created for your document by " . $user->department);

        return response()->json([
            'message' => 'Purchase Order created successfully and forwarded to the next approver.',
            'status'  => 'success'
        ]);
    }

    private function handleWorkOrderCreation($doc, $user, $request)
    {
        $request->validate([
            'work_order' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'message'    => 'nullable|string',
        ]);

        if (!$request->hasFile('work_order')) {
            return response()->json([
                'message' => 'Failed to upload Work Order file.',
                'status'  => 'error'
            ]);
        }

        $file = $request->file('work_order');
        $folderPath = 'WorkOrders/' . date('Y') . '/' . date('m');
        $uniqueName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = "{$folderPath}/{$uniqueName}";
        $file->storeAs($folderPath, $uniqueName, 'public');

        DB::table('document_annexures')->insert([
            'doc_id' => $doc->id,
            'annexure' => $filePath,
            'created_at' => now()
        ]);

        // Advance workflow to next approver in the sequence
        $approvalSequence = json_decode($doc->approval_sequence, true);
        $currentIndex = $doc->current_sequence_index;
        $nextIndex = $currentIndex + 1;

        if ($nextIndex < count($approvalSequence)) {
            $nextApprover = $approvalSequence[$nextIndex];
            $doc->update([
                'work_order'             => $filePath,
                'status'                 => 'Sent to ' . $nextApprover,
                'forwarded_to'           => $nextApprover,
                'to'                     => $nextApprover,
                'current_sequence_index' => $nextIndex,
                'updated_at'             => now()
            ]);

            DB::table('document_approval_forwardings')->insert([
                'doc_id'       => $doc->id,
                'forwarded_by' => Auth::id(),
                'forwarded_to' => $nextApprover,
                'created_at'   => now()
            ]);

            $this->sendNotificationToDepartment($nextApprover, $doc, "Work Order has been created. Please review and proceed.");
        } else {
            $doc->update([
                'work_order'             => $filePath,
                'status'                 => 'Work Order Created',
                'current_sequence_index' => $nextIndex,
                'updated_at'             => now()
            ]);
        }

        $log_description = "Work Order raised by <b>" . $user->department . "</b>";
        $this->logApprovalAction($doc, "Work Order Created", $log_description, $request->input('message') ?: $log_description);
        $this->sendNotificationToUser($doc->by, $doc, "Work Order has been created for your document by " . $user->department);

        return response()->json([
            'message' => 'Work Order created successfully and forwarded to the next approver.',
            'status'  => 'success'
        ]);
    }

    /**
     * Handle Finance Head sanction and payment
     */
    private function handleFinanceSanction($doc, $user, $request)
    {
        $request->validate([
            'sanctioned_amount' => 'required|numeric|min:0',
            'recommended_amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string',
        ]);
        
        $doc->update([
            'sanctioned_amount' => $request->input('sanctioned_amount'),
            'recommended_amount' => $request->input('recommended_amount', $doc->recommended_amount),
            'approval_status' => "Sanctioned by " . $user->department,
            'updated_at' => now()
        ]);
        
        $log_description = "Amount sanctioned by <b>" . $user->department . "</b>: ₹" . number_format($request->input('sanctioned_amount'), 2);
        $this->logApprovalAction($doc, "Amount Sanctioned", $log_description, $request->input('message', $log_description));
        
        $this->sendNotificationToUser($doc->by, $doc, "Your requested amount has been sanctioned by Finance Department");

        return response()->json([
            'message' => 'Amount sanctioned successfully.',
            'status' => 'success'
        ]);
    }

    /**
     * Handle Finance Head payment credit details submission
     */
    private function handleFinanceDetails($doc, $user, $request)
    {
        $request->validate([
            'payment_type'         => 'required|in:Full Payment,Partial Payment',
            'paid_amount'          => 'required|numeric|min:0',
            'payment_date'         => 'required|date',
            'mode'                 => 'required|string',
            'payment_reference_no' => 'required|string|max:255',
            'remarks'              => 'nullable|string',
        ]);

        $paymentType = $request->input('payment_type');

        DB::table('payment_details')->insert([
            'doc_id'               => $doc->id,
            'mode'                 => $request->input('mode'),
            'payment_reference_no' => $request->input('payment_reference_no'),
            'payment_date'         => $request->input('payment_date'),
            'paid_amount'          => $request->input('paid_amount'),
            'expenditure_id'       => $doc->doc_id,
            'expenditure_category' => 'Purchase',
            'payment_type'         => $paymentType,
            'remarks'              => $request->input('remarks'),
            'tds_amount'           => 0,
            'created_at'           => now(),
        ]);

        $log_description = "Finance details submitted by <b>" . $user->department . "</b> — "
            . $paymentType . " | " . $request->input('mode')
            . " ₹" . number_format($request->input('paid_amount'), 2)
            . " (Ref: " . $request->input('payment_reference_no') . ")";

        if ($paymentType === 'Full Payment') {
            // Full payment — advance workflow or mark completed
            $approvalSequence = json_decode($doc->approval_sequence, true);
            $currentIndex     = $doc->current_sequence_index;
            $nextIndex        = $currentIndex + 1;

            if ($nextIndex < count($approvalSequence)) {
                $nextApprover = $approvalSequence[$nextIndex];
                $doc->update([
                    'status'                 => 'Sent to ' . $nextApprover,
                    'forwarded_to'           => $nextApprover,
                    'to'                     => $nextApprover,
                    'current_sequence_index' => $nextIndex,
                    'approval_status'        => 'Full Payment processed by ' . $user->department,
                    'updated_at'             => now(),
                ]);
                $this->sendNotificationToDepartment($nextApprover, $doc, "Full payment submitted. Please review and proceed.");
            } else {
                $doc->update([
                    'status'                 => 'Payment Completed',
                    'approval_status'        => 'Full Payment processed by ' . $user->department,
                    'current_sequence_index' => $nextIndex,
                    'updated_at'             => now(),
                ]);
            }

            $this->logApprovalAction($doc, "Full Payment Added by " . $user->department, $log_description,
                $request->input('remarks') ?: $log_description);

            $this->sendNotificationToUser($doc->by, $doc,
                "Full payment has been recorded for your document by " . $user->department
                . ". Amount: ₹" . number_format($request->input('paid_amount'), 2));

            return response()->json([
                'message' => 'Full payment submitted successfully.',
                'status'  => 'success',
            ]);
        }

        // Partial payment — record only, do not advance workflow
        $doc->update([
            'approval_status' => 'Partial Payment by ' . $user->department,
            'updated_at'      => now(),
        ]);

        $this->logApprovalAction($doc, "Partial Payment by " . $user->department, $log_description,
            $request->input('remarks') ?: $log_description);

        $this->sendNotificationToUser($doc->by, $doc,
            "A partial payment has been recorded for your document by " . $user->department
            . ". Amount: ₹" . number_format($request->input('paid_amount'), 2));

        return response()->json([
            'message' => 'Partial payment recorded. You can continue adding payments until Full Payment is submitted.',
            'status'  => 'success',
        ]);
    }

    /**
 * Handle STB Office Acknowledgment
 * 
 * @param DocumentApproval $doc
 * @param User $user
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
private function handleFinanceHeadForward($doc, $user, $request)
{
    $validFinanceHeads = [
        'Finance Head Salem',
        'Finance Head Chennai',
        'Finance Head Karaikal',
        'Finance Head Pondy',
    ];

    $request->validate([
        'forward_to' => 'required|string|in:' . implode(',', $validFinanceHeads),
        'message'    => 'nullable|string',
    ]);

    // Only an active Finance Head at this document can forward
    if (!in_array($user->department, $validFinanceHeads)) {
        return response()->json(['message' => 'Only a Finance Head can forward to another Finance location', 'status' => 'error']);
    }

    $forwardTo = $request->input('forward_to');

    if ($forwardTo === $user->department) {
        return response()->json(['message' => 'Cannot forward to your own location', 'status' => 'error']);
    }

    // Replace the current Finance Head entry in the approval sequence
    $approvalSequence = json_decode($doc->approval_sequence, true) ?? [];
    foreach ($approvalSequence as $idx => $step) {
        if ($step === $user->department) {
            $approvalSequence[$idx] = $forwardTo;
            break;
        }
    }

    $doc->update([
        'approval_sequence' => json_encode($approvalSequence),
        'forwarded_to'      => $forwardTo,
        'to'                => $forwardTo,
        'status'            => 'Sent to ' . $forwardTo,
        'approval_status'   => 'Forwarded to ' . $forwardTo . ' by ' . $user->department,
        'updated_at'        => now(),
    ]);

    DB::table('document_approval_forwardings')->insert([
        'doc_id'       => $doc->id,
        'forwarded_by' => Auth::id(),
        'forwarded_to' => $forwardTo,
        'created_at'   => now(),
    ]);

    $message = $request->input('message', '');
    $log_description = "Document forwarded to <b>{$forwardTo}</b> by <b>{$user->department}</b>"
        . ($message ? " — " . e($message) : "");
    $this->logApprovalAction($doc, "Forwarded to {$forwardTo} by {$user->department}", $log_description,
        $message ?: $log_description);

    $this->sendNotificationToDepartment($forwardTo, $doc, "{$user->department} has forwarded this document to you for payment processing");
    $this->sendNotificationToUser($doc->by, $doc, "Your document has been forwarded from {$user->department} to {$forwardTo}");

    return response()->json([
        'message' => "Document forwarded to {$forwardTo} successfully",
        'status'  => 'success',
    ]);
}

private function handleSTBAcknowledge($doc, $user, $request)
{
    $approvalSequence = json_decode($doc->approval_sequence, true);
    $currentIndex = $doc->current_sequence_index;
    $currentApprover = $approvalSequence[$currentIndex];
    $nextIndex = $currentIndex + 1;
    
    // Check if current approver is STB Office
    if ($currentApprover != 'STB Office') {
        return response()->json([
            'message' => 'STB Office acknowledgment is not required at this stage',
            'status' => 'error'
        ]);
    }
    
    $acknowledgment_status = "Acknowledged by " . $user->department;
    $log_description = "Document Acknowledged by <b>" . $user->department . "</b>";
    $remarks = $request->input('message', 'Document acknowledged and forwarded');
    
    // Check if this is the last approval
    if ($nextIndex >= count($approvalSequence)) {
        // Final acknowledgment - mark as completed
        $doc->update([
            'approval_status' => $acknowledgment_status,
            'status' => 'Completed',
            'forwarded_to' => $doc->from,
            'current_sequence_index' => $nextIndex,
            'updated_at' => now()
        ]);
        
        $log_message = "Document acknowledged and completed";
        $this->sendNotificationToUser($doc->by, $doc, "Your document has been acknowledged and completed by STB Office");
        
    } else {
        // Forward to next approver (Chairman)
        $nextApprover = $approvalSequence[$nextIndex];
        
        $doc->update([
            'approval_status' => $acknowledgment_status,
            'status' => "Sent to " . $nextApprover,
            'forwarded_to' => $nextApprover,
            'to' => $nextApprover,
            'current_sequence_index' => $nextIndex,
            'updated_at' => now()
        ]);
        
        $log_message = "Document acknowledged by STB Office and forwarded to " . $nextApprover;
        
        // Add to forwarding table
        DB::table('document_approval_forwardings')->insert([
            'doc_id' => $doc->id,
            'forwarded_by' => Auth::id(),
            'forwarded_to' => $nextApprover,
            'created_at' => now()
        ]);
        
        // Notify next approver (Chairman)
        if (in_array($nextApprover, ['Chairman'])) {
            $this->sendNotificationToDepartment($nextApprover, $doc, "A document has been forwarded to you after STB Office acknowledgment");
        } else {
            $this->sendNotificationToDepartment($nextApprover, $doc, "A document has been forwarded to you for approval");
        }
    }
    
    // Log the action with remarks
    $this->logApprovalAction($doc, $acknowledgment_status, $log_message, $remarks);
    
    // Notify document owner
    $this->sendNotificationToUser($doc->by, $doc, "Your document has been acknowledged by STB Office" . (isset($nextApprover) ? " and forwarded to " . $nextApprover : ""));
    
    return response()->json([
        'message' => 'Document acknowledged and forwarded successfully',
        'status' => 'success'
    ]);
}

    /**
     * GET /document/approval-flow/download
     * Download the approval flowchart as a PDF.
     */
    public function downloadFlowchart()
    {
        $pdf = Pdf::loadView('frontend.document.approval_flowchart_download');
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Approval-Flow-Guide-' . now()->format('Ymd') . '.pdf');
    }

    // ---------------------------------------------------------------
    // Approval Report (Chairman / GM / Medical Director)
    // ---------------------------------------------------------------

    /** Available selectable fields */
    private function approvalReportFields(): array
    {
        return [
            'doc_id'              => 'Document ID',
            'title'               => 'Title',
            'subject'             => 'Subject',
            'from'                => 'From Department',
            'status'              => 'Status',
            'approval_status'     => 'Approval Status',
            'priority'            => 'Priority',
            'amount'              => 'Amount',
            'currency'            => 'Currency',
            'is_payment_involved' => 'Payment Involved',
            'reference'           => 'Reference',
            'created_at'          => 'Created Date',
            'current_approver'    => 'Current Approver',
            'approval_sequence'   => 'Approval Chain',
        ];
    }

    private function buildApprovalReportQuery(Request $request)
    {
        $role   = $request->get('role', '');
        $status = $request->get('status');
        $from   = $request->get('date_from');
        $to     = $request->get('date_to');

        $query = DocumentApproval::where('status', '!=', 'Draft');

        if ($role) {
            $query->where('approval_sequence', 'like', '%' . $role . '%');
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query->latest();
    }

    public function approvalReport(Request $request)
    {
        $activeMenu    = 'approval-report';
        $activeDropdown = '';

        $roles        = ['Chairman', 'General Manager', 'General Manager - Admin', 'Medical Director'];
        $allFields    = $this->approvalReportFields();
        $defaultFields = ['doc_id', 'title', 'from', 'status', 'priority', 'created_at'];

        $docs         = collect();
        $selectedFields = $request->filled('fields') ? $request->get('fields') : $defaultFields;

        if ($request->filled('role')) {
            $docs = $this->buildApprovalReportQuery($request)->get();
        }

        return view('frontend.document.approval_report', compact(
            'activeMenu', 'activeDropdown', 'roles', 'allFields',
            'selectedFields', 'docs'
        ));
    }

    public function downloadApprovalReportPDF(Request $request)
    {
        $role           = $request->get('role', 'Chairman');
        $selectedFields = $request->filled('fields') ? $request->get('fields') : array_keys($this->approvalReportFields());
        $allFields      = $this->approvalReportFields();
        $docs           = $this->buildApprovalReportQuery($request)->get();

        $pdf = Pdf::loadView('frontend.document.download.approval_report_pdf',
            compact('docs', 'role', 'selectedFields', 'allFields'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Approval_Report_' . str_replace([' ', '/'], '_', $role) . '_' . now()->format('Ymd') . '.pdf');
    }

    public function downloadApprovalReportExcel(Request $request)
    {
        $role           = $request->get('role', 'Chairman');
        $selectedFields = $request->filled('fields') ? $request->get('fields') : array_keys($this->approvalReportFields());
        $allFields      = $this->approvalReportFields();
        $docs           = $this->buildApprovalReportQuery($request)->get();

        return Excel::download(
            new ApprovalReportExport($docs, $role, $selectedFields, $allFields),
            'Approval_Report_' . str_replace([' ', '/'], '_', $role) . '_' . now()->format('Ymd') . '.xlsx'
        );
    }
}