<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendTicketNotificationMail;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Services\ApprovalPathResolver;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DocumentApiController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    private function apiUser(Request $request): User
    {
        return $request->get('_api_user');
    }

    private function formatDoc(DocumentApproval $doc): array
    {
        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) ($doc->current_sequence_index ?? 0);
        $isComplete   = $currentIndex >= count($sequence);

        return [
            'id'                     => $doc->id,
            'doc_id'                 => $doc->doc_id,
            'title'                  => $doc->title,
            // subject/description are authored via a rich-text editor on the
            // web — sent raw so the mobile app can render the formatting
            // instead of losing it to strip_tags().
            'subject'                => $doc->subject,
            'description'            => $doc->description,
            'from'                   => $doc->from,
            'to'                     => $doc->to,
            'forwarded_to'           => $doc->forwarded_to,
            'status'                 => $doc->status,
            'approval_status'        => $doc->approval_status,
            'priority'               => $doc->priority,
            'is_payment_involved'    => $doc->is_payment_involved,
            'is_purchase'            => $doc->is_purchase,
            'amount'                 => $doc->amount,
            'currency'               => $doc->currency ?? 'INR',
            'recommended_amount'     => $doc->recommended_amount,
            'sanctioned_amount'      => $doc->sanctioned_amount,
            'approval_sequence'      => $sequence,
            'current_sequence_index' => $currentIndex,
            'current_approver'       => $sequence[$currentIndex] ?? null,
            'is_fully_approved'      => $isComplete,
            'approval_progress_pct'  => count($sequence) > 0 ? round(($currentIndex / count($sequence)) * 100) : 0,
            'created_by'             => optional(User::find($doc->by))->name,
            'created_by_dept'        => optional(User::find($doc->by))->department,
            'created_by_id'          => $doc->by,
            'attachment'             => (bool) $doc->attachment,
            'attachment_url'         => $doc->attachment ? $this->firstAttachmentUrl($doc) : null,
            'created_at'             => $doc->created_at?->toISOString(),
            'updated_at'             => $doc->updated_at?->toISOString(),
        ];
    }

    private function firstAttachmentUrl(DocumentApproval $doc): ?string
    {
        $path = DB::table('document_annexures')->where('doc_id', $doc->id)->value('annexure');
        return $path ? Storage::url($path) : null;
    }

    private function logAction(DocumentApproval $doc, string $status, string $description, string $message): void
    {
        $req = request();
        DB::table('approval_log')->insert([
            'doc_id'             => $doc->id,
            'by'                 => auth()->id(),
            'status'             => $status,
            'message'            => $message,
            'biometric_verified' => (bool) $req->input('biometric_verified', false),
            'platform'           => $req->input('platform'),
            'device_info'        => $req->input('device_info'),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // document_logs has no `by` column (unlike approval_log above) — see
        // create_document_log_table migration.
        DB::table('document_logs')->insert([
            'doc_id'      => $doc->id,
            'description' => $description,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function notifyUser(int $userId, DocumentApproval $doc, string $message): void
    {
        try {
            DB::table('notifications')->insert([
                'user_id'    => $userId,
                'type'       => 'document',
                'data'       => json_encode(['doc_id' => $doc->id, 'message' => $message]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Notifications table may differ — fail silently
        }

        PushNotificationService::sendToUser(
            $userId,
            $doc->doc_id ?? 'Document',
            $message,
            ['type' => 'document', 'id' => $doc->id],
        );

        // Mirrors the web app's sendNotificationToUser() — actions taken from the
        // mobile app previously only reached the recipient via push, which silently
        // dropped anyone without the app installed/notifications enabled.
        $user = User::find($userId);
        if ($user && $user->email) {
            $mail_details = [
                'content' => $message . '<br><br>ID - <b><i>' . $doc->doc_id . '</i></b><br>Titled - <b><i>' . $doc->title . '</i></b>',
                'url'     => URL::to('view/document/' . $doc->id),
                'title'   => 'Document Status Update',
            ];

            SendTicketNotificationMail::dispatch($user, $mail_details, 'Document Update - ' . $doc->doc_id, 'frontend.email.document_notifications');
        }
    }

    /**
     * Notifies every active SuperAdmin/HOD in a department that a document
     * now needs their attention (DB notification + push + email via notifyUser).
     */
    private function notifyDepartment(string $department, DocumentApproval $doc, string $message): void
    {
        $userIds = User::where('department', $department)
            ->where('is_active', true)
            ->whereIn('role', ['SuperAdmin', 'HOD'])
            ->pluck('id');

        foreach ($userIds as $userId) {
            $this->notifyUser($userId, $doc, $message);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Listing endpoints
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/documents
     * List documents visible to the authenticated user.
     *
     * Query params:
     *   status       - Filter by status (e.g. Completed, Rejected, Hold…)
     *   from_dept    - Filter by originating department
     *   date_from    - YYYY-MM-DD
     *   date_to      - YYYY-MM-DD
     *   search       - Search title/doc_id
     *   per_page     - Items per page (default 15, max 100)
     */
    public function index(Request $request)
    {
        $user    = $this->apiUser($request);
        $perPage = min((int) $request->get('per_page', 15), 100);

        $query = DocumentApproval::query()->whereNotIn('status', ['Draft']);

        // SuperAdmin sees everything; others see their dept docs + docs forwarded to them
        if ($user->role !== 'SuperAdmin') {
            $forwardedDocIds = DB::table('document_approval_forwardings')
                ->where('forwarded_to', $user->department)
                ->pluck('doc_id');

            $query->where(function ($q) use ($user, $forwardedDocIds) {
                $q->where('from', $user->department)
                  ->orWhereIn('id', $forwardedDocIds)
                  ->orWhere('by', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_dept')) {
            $query->where('from', $request->from_dept);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('doc_id', 'like', '%' . $request->search . '%');
            });
        }

        $docs = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $docs->map(fn($d) => $this->formatDoc($d)),
            'meta'    => [
                'current_page' => $docs->currentPage(),
                'last_page'    => $docs->lastPage(),
                'per_page'     => $docs->perPage(),
                'total'        => $docs->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/documents/pending
     * Documents currently awaiting the authenticated user's action.
     */
    public function pending(Request $request)
    {
        $user = $this->apiUser($request);

        $docs = DocumentApproval::where('forwarded_to', $user->department)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected', 'Draft', 'Retracted'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($d) => $this->formatDoc($d));

        return response()->json(['success' => true, 'data' => $docs, 'count' => $docs->count()]);
    }

    /**
     * GET /api/v1/documents/my
     * Documents created by the authenticated user.
     */
    public function my(Request $request)
    {
        $user    = $this->apiUser($request);
        $perPage = min((int) $request->get('per_page', 15), 100);

        $docs = DocumentApproval::where('by', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $docs->map(fn($d) => $this->formatDoc($d)),
            'meta'    => [
                'current_page' => $docs->currentPage(),
                'last_page'    => $docs->lastPage(),
                'per_page'     => $docs->perPage(),
                'total'        => $docs->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/documents/completed
     * All completed or closed documents visible to the authenticated user.
     */
    public function completed(Request $request)
    {
        $user    = $this->apiUser($request);
        $perPage = min((int) $request->get('per_page', 15), 100);

        $query = DocumentApproval::whereIn('status', ['Completed', 'Closed']);

        if ($user->role !== 'SuperAdmin') {
            $query->where('from', $user->department);
        }

        $docs = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $docs->map(fn($d) => $this->formatDoc($d)),
            'meta'    => [
                'current_page' => $docs->currentPage(),
                'last_page'    => $docs->lastPage(),
                'per_page'     => $docs->perPage(),
                'total'        => $docs->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/documents/{id}
     * Get full document details including approval log.
     */
    public function show(Request $request, int $id)
    {
        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);

        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        // Access control
        $sequence        = json_decode($doc->approval_sequence, true) ?? [];
        $inSequence      = in_array($user->department, $sequence);
        $isCreator       = $doc->by == $user->id;
        $isForwardedToMe = DB::table('document_approval_forwardings')
            ->where('doc_id', $doc->id)
            ->where('forwarded_to', $user->department)
            ->exists();

        if ($user->role !== 'SuperAdmin' && !$isCreator && !$inSequence && !$isForwardedToMe) {
            return response()->json(['success' => false, 'message' => 'You do not have access to this document.'], 403);
        }

        $approvalLog = DB::table('approval_log')
            ->where('doc_id', $doc->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($log) {
                $by = User::find($log->by);
                return [
                    'action'     => $log->status,
                    'message'    => $log->message ?? '',
                    'by_name'    => optional($by)->name,
                    'by_dept'    => optional($by)->department,
                    'created_at' => $log->created_at,
                ];
            });

        $payments = DB::table('payment_details')
            ->where('doc_id', $doc->id)
            ->get(['mode', 'paid_amount', 'tds_amount', 'payment_date', 'payment_reference_no', 'expenditure_id']);

        $annexures = DB::table('document_annexures')
            ->where('doc_id', $doc->id)
            ->get(['annexure'])
            ->map(fn ($a) => [
                'name' => basename($a->annexure),
                'url'  => Storage::url($a->annexure),
            ]);

        return response()->json([
            'success' => true,
            'data'    => array_merge($this->formatDoc($doc), [
                'approval_log' => $approvalLog,
                'payments'     => $payments,
                'annexures'    => $annexures,
            ]),
        ]);
    }

    /**
     * GET /api/v1/documents/{id}/approval-log
     * Approval history for a document.
     */
    public function approvalLog(Request $request, int $id)
    {
        $doc = DocumentApproval::find($id);
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        $log = DB::table('approval_log')
            ->where('doc_id', $doc->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($entry) {
                $by = User::find($entry->by);
                return [
                    'action'     => $entry->status,
                    'message'    => $entry->message ?? '',
                    'by_name'    => optional($by)->name,
                    'by_dept'    => optional($by)->department,
                    'created_at' => $entry->created_at,
                ];
            });

        return response()->json(['success' => true, 'data' => $log]);
    }

    // ─────────────────────────────────────────────────────────────
    // Action endpoints  (mirror changeDocumentStatus logic)
    // ─────────────────────────────────────────────────────────────

    /**
     * POST /api/v1/documents/{id}/approve
     * Body: { message?, recommended_amount?, sanctioned_amount? }
     */
    public function approve(Request $request, int $id)
    {
        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null)) {
            return response()->json(['success' => false, 'message' => 'It is not your turn to approve this document.'], 403);
        }

        $nextIndex      = $currentIndex + 1;
        $approvalStatus = "Approved by {$user->department}";

        if ($nextIndex >= count($sequence)) {
            // Final approval — auto-complete
            $doc->update([
                'approval_status'        => $approvalStatus,
                'status'                 => 'Completed',
                'forwarded_to'           => $doc->from,
                'current_sequence_index' => $nextIndex,
                'recommended_amount'     => $request->input('recommended_amount', $doc->recommended_amount),
                'sanctioned_amount'      => $request->input('sanctioned_amount', $doc->sanctioned_amount),
                'updated_at'             => now(),
            ]);
            $this->logAction($doc, $approvalStatus, "Document fully approved and completed via API by <b>{$user->name}</b>", $request->input('message', 'Approved'));
            $this->notifyUser($doc->by, $doc, "Your document has been fully approved and completed.");

            return response()->json(['success' => true, 'message' => 'Document approved and marked as Completed.', 'data' => $this->formatDoc($doc->fresh())]);
        }

        $nextApprover = $sequence[$nextIndex];
        $doc->update([
            'approval_status'        => $approvalStatus,
            'status'                 => "Sent to {$nextApprover}",
            'forwarded_to'           => $nextApprover,
            'to'                     => $nextApprover,
            'current_sequence_index' => $nextIndex,
            'recommended_amount'     => $request->input('recommended_amount', $doc->recommended_amount),
            'sanctioned_amount'      => $request->input('sanctioned_amount', $doc->sanctioned_amount),
            'updated_at'             => now(),
        ]);
        $this->logAction($doc, $approvalStatus, "Document approved via API by <b>{$user->name}</b> ({$user->department}). Forwarded to <b>{$nextApprover}</b>.", $request->input('message', 'Approved'));
        $this->notifyUser($doc->by, $doc, "Your document was approved by {$user->department}.");
        $this->notifyDepartment($nextApprover, $doc, "{$doc->doc_id} was forwarded to {$nextApprover} for approval.");

        return response()->json(['success' => true, 'message' => "Approved. Document forwarded to {$nextApprover}.", 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/reject
     * Body: { message }
     */
    public function reject(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null)) {
            return response()->json(['success' => false, 'message' => 'You are not the current approver.'], 403);
        }

        $status = "Rejected by {$user->department}";
        $doc->update(['approval_status' => $status, 'status' => 'Rejected', 'updated_at' => now()]);
        $this->logAction($doc, $status, "Document rejected via API by <b>{$user->name}</b>: {$request->message}", $request->message);
        $this->notifyUser($doc->by, $doc, "Your document was rejected by {$user->department}.");

        return response()->json(['success' => true, 'message' => 'Document rejected.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/hold
     * Body: { message }
     */
    public function hold(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null) && $user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'You are not the current approver.'], 403);
        }

        $status = "Held by {$user->department}";
        $doc->update(['approval_status' => $status, 'status' => 'Hold', 'updated_at' => now()]);
        $this->logAction($doc, $status, "Document put on hold via API by <b>{$user->name}</b>: {$request->message}", $request->message);
        $this->notifyUser($doc->by, $doc, "Your document has been put on hold by {$user->department}.");

        return response()->json(['success' => true, 'message' => 'Document put on hold.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/noted
     * Body: { message? }
     */
    public function noted(Request $request, int $id)
    {
        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null) && $user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'You are not the current approver.'], 403);
        }

        $nextIndex = $currentIndex + 1;
        $status    = "Noted by {$user->department}";

        if ($nextIndex >= count($sequence)) {
            $doc->update(['approval_status' => $status, 'status' => 'Completed', 'forwarded_to' => $doc->from, 'current_sequence_index' => $nextIndex, 'updated_at' => now()]);
        } else {
            $nextApprover = $sequence[$nextIndex];
            $doc->update(['approval_status' => $status, 'status' => "Sent to {$nextApprover}", 'forwarded_to' => $nextApprover, 'to' => $nextApprover, 'current_sequence_index' => $nextIndex, 'updated_at' => now()]);
        }

        $this->logAction($doc, $status, "Document noted via API by <b>{$user->name}</b>.", $request->input('message', 'Noted'));
        $this->notifyUser($doc->by, $doc, "Your document has been noted by {$user->department}.");

        return response()->json(['success' => true, 'message' => 'Document noted.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/acknowledge
     * STB Office-only action — mirrors the web app's "Acknowledge & Forward"
     * button, which replaces the regular approve/reject/hold/etc. bar for
     * that department (see handleSTBAcknowledge() in DocumentApprovalController).
     * Body: { message? }
     */
    public function acknowledge(Request $request, int $id)
    {
        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;
        $currentApprover = $sequence[$currentIndex] ?? null;

        if ($user->department !== 'STB Office' || $currentApprover !== 'STB Office') {
            return response()->json(['success' => false, 'message' => 'STB Office acknowledgment is not required at this stage.'], 403);
        }

        $nextIndex  = $currentIndex + 1;
        $status     = "Acknowledged by {$user->department}";
        $remarks    = $request->input('message', 'Document acknowledged and forwarded');

        if ($nextIndex >= count($sequence)) {
            $doc->update(['approval_status' => $status, 'status' => 'Completed', 'forwarded_to' => $doc->from, 'current_sequence_index' => $nextIndex, 'updated_at' => now()]);
            $logMessage = 'Document acknowledged and completed';
            $this->notifyUser($doc->by, $doc, 'Your document has been acknowledged and completed by STB Office.');
        } else {
            $nextApprover = $sequence[$nextIndex];
            $doc->update(['approval_status' => $status, 'status' => "Sent to {$nextApprover}", 'forwarded_to' => $nextApprover, 'to' => $nextApprover, 'current_sequence_index' => $nextIndex, 'updated_at' => now()]);
            $logMessage = "Document acknowledged by STB Office and forwarded to {$nextApprover}";
            $this->notifyDepartment($nextApprover, $doc, 'A document has been forwarded to you after STB Office acknowledgment.');
            $this->notifyUser($doc->by, $doc, "Your document has been acknowledged by STB Office and forwarded to {$nextApprover}.");
        }

        $this->logAction($doc, $status, "{$logMessage} by <b>{$user->name}</b>.", $remarks);

        return response()->json(['success' => true, 'message' => 'Document acknowledged and forwarded successfully.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/pending
     * Body: { message }
     */
    public function markPending(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null) && $user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'You are not the current approver.'], 403);
        }

        $status = "Pending at {$user->department}";
        $doc->update(['approval_status' => $status, 'status' => 'Pending', 'updated_at' => now()]);
        $this->logAction($doc, $status, "Document marked pending via API by <b>{$user->name}</b>: {$request->message}", $request->message);
        $this->notifyUser($doc->by, $doc, "Your document has been marked as pending by {$user->department}.");

        return response()->json(['success' => true, 'message' => 'Document marked as pending.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/discuss
     * Body: { message }
     */
    public function discuss(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null) && $user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'You are not the current approver.'], 403);
        }

        $status = "Discussion called by {$user->department}";
        $doc->update(['approval_status' => $status, 'status' => 'Discussion', 'updated_at' => now()]);
        $this->logAction($doc, $status, "Document called for discussion via API by <b>{$user->name}</b>: {$request->message}", $request->message);
        $this->notifyUser($doc->by, $doc, "Your document has been called for discussion by {$user->department}.");

        return response()->json(['success' => true, 'message' => 'Document called for discussion.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/forward
     * Body: { forward_to (department name), message? }
     */
    public function forward(Request $request, int $id)
    {
        $request->validate([
            'forward_to' => 'required|string|max:255',
            'message'    => 'nullable|string|max:2000',
        ]);

        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if ($user->department !== ($sequence[$currentIndex] ?? null) && $user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'You are not the current approver.'], 403);
        }

        $forwardTo = $request->forward_to;

        DB::table('document_approval_forwardings')->insert([
            'doc_id'       => $doc->id,
            'forwarded_to' => $forwardTo,
            'forwarded_by' => $user->id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $status = "Forwarded to {$forwardTo} by {$user->department}";
        $doc->update(['approval_status' => $status, 'forwarded_to' => $forwardTo, 'updated_at' => now()]);
        $this->logAction($doc, $status, "Document forwarded via API by <b>{$user->name}</b> to <b>{$forwardTo}</b>.", $request->input('message', 'Forwarded'));

        if ($doc->by != $user->id) {
            $this->notifyUser($doc->by, $doc, "{$user->name} forwarded {$doc->doc_id} to {$forwardTo}.");
        }
        $this->notifyDepartment($forwardTo, $doc, "{$doc->doc_id} was forwarded to {$forwardTo}.");

        return response()->json(['success' => true, 'message' => "Document forwarded to {$forwardTo}.", 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents/{id}/comment
     * Body: { message }
     */
    public function comment(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|max:5000']);

        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        $sequence        = json_decode($doc->approval_sequence, true) ?? [];
        $isCreator       = $doc->by == $user->id;
        $inSequence      = in_array($user->department, $sequence);
        $isForwardedToMe = DB::table('document_approval_forwardings')
            ->where('doc_id', $doc->id)->where('forwarded_to', $user->department)->exists();

        if ($user->role !== 'SuperAdmin' && !$isCreator && !$inSequence && !$isForwardedToMe) {
            return response()->json(['success' => false, 'message' => 'You do not have access to comment on this document.'], 403);
        }

        $status = "Commented by {$user->department}";
        $this->logAction($doc, $status, "Comment added via API by <b>{$user->name}</b>: {$request->message}", $request->message);

        if ($doc->by != $user->id) {
            $this->notifyUser($doc->by, $doc, "{$user->name} commented on {$doc->doc_id}.");
        }

        return response()->json(['success' => true, 'message' => 'Comment added.']);
    }

    /**
     * POST /api/v1/documents/{id}/chairman-approve
     * Chairman-specific approval — optionally direct to a Finance Head.
     * Body: { message?, finance_head?, recommended_amount?, sanctioned_amount? }
     */
    public function chairmanApprove(Request $request, int $id)
    {
        $request->validate([
            'finance_head'       => 'nullable|string|in:Finance Head Salem,Finance Head Chennai,Finance Head Karaikal,Finance Head Pondy',
            'recommended_amount' => 'nullable|numeric|min:0',
            'sanctioned_amount'  => 'nullable|numeric|min:0',
            'message'            => 'nullable|string|max:2000',
        ]);

        $user = $this->apiUser($request);

        if ($user->department !== 'Chairman' && $user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'Only the Chairman can use this endpoint.'], 403);
        }

        $doc = DocumentApproval::find($id);
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if (($sequence[$currentIndex] ?? null) !== 'Chairman') {
            return response()->json(['success' => false, 'message' => 'This document is not currently at the Chairman stage.'], 422);
        }

        $alreadyApproved = DB::table('approval_log')
            ->where('doc_id', $doc->id)
            ->where('status', 'like', 'Approved by Chairman%')
            ->exists();

        if ($alreadyApproved) {
            return response()->json(['success' => false, 'message' => 'This document has already been approved by Chairman.'], 422);
        }

        // Optionally replace the next PA step with a directly chosen Finance Head
        if ($request->filled('finance_head')) {
            foreach ($sequence as $idx => $step) {
                if ($idx > $currentIndex && in_array($step, ['PA to Chairman', 'PA to GM'])) {
                    $sequence[$idx] = $request->finance_head;
                    break;
                }
            }
            $doc->update(['approval_sequence' => json_encode($sequence)]);
        }

        $approvalStatus = 'Approved by Chairman';
        $nextIndex      = $currentIndex + 1;

        if ($nextIndex >= count($sequence)) {
            $doc->update([
                'approval_status'        => $approvalStatus,
                'status'                 => 'Completed',
                'forwarded_to'           => $doc->from,
                'current_sequence_index' => $nextIndex,
                'recommended_amount'     => $request->input('recommended_amount', $doc->recommended_amount),
                'sanctioned_amount'      => $request->input('sanctioned_amount', $doc->sanctioned_amount),
                'updated_at'             => now(),
            ]);
            $this->logAction($doc, $approvalStatus, "Document fully approved and completed via API by Chairman <b>{$user->name}</b>.", $request->input('message', 'Approved by Chairman'));
            $this->notifyUser($doc->by, $doc, "Your document has been fully approved by Chairman and completed.");

            return response()->json([
                'success' => true,
                'message' => 'Document approved by Chairman and marked as Completed.',
                'data'    => $this->formatDoc($doc->fresh()),
            ]);
        }

        $nextApprover = $sequence[$nextIndex];
        $doc->update([
            'approval_status'        => $approvalStatus,
            'status'                 => "Sent to {$nextApprover}",
            'forwarded_to'           => $nextApprover,
            'to'                     => $nextApprover,
            'current_sequence_index' => $nextIndex,
            'recommended_amount'     => $request->input('recommended_amount', $doc->recommended_amount),
            'sanctioned_amount'      => $request->input('sanctioned_amount', $doc->sanctioned_amount),
            'updated_at'             => now(),
        ]);

        DB::table('document_approval_forwardings')->insertOrIgnore([
            'doc_id'       => $doc->id,
            'forwarded_by' => $user->id,
            'forwarded_to' => $nextApprover,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $this->logAction($doc, $approvalStatus, "Document approved via API by Chairman <b>{$user->name}</b>. Forwarded to <b>{$nextApprover}</b>.", $request->input('message', 'Approved by Chairman'));
        $this->notifyUser($doc->by, $doc, "Your document was approved by Chairman. Forwarded to {$nextApprover}.");
        $this->notifyDepartment($nextApprover, $doc, "{$doc->doc_id} was forwarded to {$nextApprover} for approval.");

        return response()->json([
            'success' => true,
            'message' => "Approved by Chairman. Document forwarded to {$nextApprover}.",
            'data'    => $this->formatDoc($doc->fresh()),
        ]);
    }

    /**
     * POST /api/v1/documents/{id}/complete
     * Creator-only action after all approvals are done.
     * Body: { message? }
     */
    public function complete(Request $request, int $id)
    {
        $user = $this->apiUser($request);
        $doc  = DocumentApproval::find($id);
        if (!$doc) return response()->json(['success' => false, 'message' => 'Document not found.'], 404);

        if ($doc->by != $user->id) {
            return response()->json(['success' => false, 'message' => 'Only the document creator can complete the process.'], 403);
        }

        $sequence     = json_decode($doc->approval_sequence, true) ?? [];
        $currentIndex = (int) $doc->current_sequence_index;

        if (!empty($sequence) && $currentIndex < count($sequence)) {
            return response()->json(['success' => false, 'message' => 'Approval process is not yet complete.'], 422);
        }

        $status = "Completed by {$user->department}";
        $doc->update(['approval_status' => $status, 'status' => 'Completed', 'updated_at' => now()]);
        $this->logAction($doc, $status, "Document process completed via API by creator <b>{$user->name}</b>.", $request->input('message', 'Process completed'));

        return response()->json(['success' => true, 'message' => 'Document process completed.', 'data' => $this->formatDoc($doc->fresh())]);
    }

    /**
     * POST /api/v1/documents
     * Create a new document (Staff / HOD / any authenticated user).
     * Multipart form-data when attaching a file.
     *
     * The approval chain is computed server-side from the creator's division,
     * payment involvement, amount, and purchase flag — same rules as the web
     * app (see ApprovalPathResolver) — so the client never selects a
     * department/approver directly.
     *
     * Body: title, subject, description?, priority?, amount?,
     *       is_payment_involved?, is_purchase?, file? (attachment)
     */
    public function store(Request $request)
    {
        $user = $this->apiUser($request);

        $request->validate([
            'title'               => 'required|string|max:255',
            'subject'             => 'required|string',
            'description'         => 'nullable|string',
            'priority'            => 'nullable|in:Low,Normal,High,Urgent',
            'amount'              => 'nullable|numeric|min:0',
            'is_payment_involved' => 'nullable|in:Y,N',
            'is_purchase'         => 'nullable|in:Y,N',
            'file'                => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('file')) {
            $year   = date('Y');
            $month  = date('m');
            $folder = "uploads/{$year}/{$month}";
            $name   = Str::uuid() . '.' . $request->file('file')->getClientOriginalExtension();
            $request->file('file')->storeAs($folder, $name, 'public');
            $attachmentPath = "{$folder}/{$name}";
        }

        $isPaymentInvolved = $request->input('is_payment_involved', 'N');
        $isPurchase        = $request->input('is_purchase', 'N');
        $amount             = (float) $request->input('amount', 0);

        $approvalPath = ApprovalPathResolver::resolve($user, $isPaymentInvolved, $amount, $isPurchase);
        $sequence     = $approvalPath['sequence'];
        $firstApprover = $approvalPath['current_approver'];

        // Same REG-DOC-NNNN series the web app uses — not a separate scheme,
        // so document numbers stay continuous regardless of which client
        // created them.
        $docId = DocumentApproval::nextDocId();

        $doc = DocumentApproval::create([
            'doc_id'                 => $docId,
            'by'                     => $user->id,
            'from'                   => $user->department,
            'to'                     => $firstApprover,
            'forwarded_to'           => $firstApprover,
            'title'                  => $request->title,
            'subject'                => $request->subject,
            'description'            => $request->description ?? '',
            'status'                 => 'Sent to ' . $firstApprover,
            'approval_status'        => 'Pending',
            'approval_sequence'      => json_encode($sequence),
            'current_sequence_index' => 0,
            'priority'               => $request->input('priority', 'Normal'),
            'amount'                 => $amount,
            'is_payment_involved'    => $isPaymentInvolved,
            'is_purchase'            => $isPurchase,
            // `attachment` is a has-attachment flag (boolean column); the real
            // file path is stored in document_annexures, see below — mirrors
            // the web flow's handleFileUploads().
            'attachment'             => $attachmentPath !== null,
        ]);

        if ($attachmentPath !== null) {
            DB::table('document_annexures')->insert([
                'doc_id'     => $doc->id,
                'annexure'   => $attachmentPath,
                'created_at' => now(),
            ]);
        }

        $this->logAction($doc, 'Created', "Document created via mobile API by <b>{$user->name}</b> ({$user->department}).", 'Document created');
        $this->notifyDepartment($firstApprover, $doc, "{$user->name} sent {$docId} to {$firstApprover} for approval.");

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully.',
            'data'    => $this->formatDoc($doc),
        ], 201);
    }

    /**
     * GET /api/v1/notifications
     * Notifications for the authenticated user.
     */
    public function notifications(Request $request)
    {
        $user    = $this->apiUser($request);
        $perPage = min((int) $request->get('per_page', 20), 50);

        $rows = DB::table('notifications')
            ->where(function ($q) use ($user) {
                $q->where('to', $user->id)
                  ->orWhere('to', $user->department);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $data = collect($rows->items())->map(function ($n) {
            $payload = is_string($n->data) ? json_decode($n->data, true) : (array) $n->data;
            return [
                'id'         => $n->id,
                'message'    => $payload['message'] ?? '',
                'doc_id'     => $payload['doc_id'] ?? null,
                'is_read'    => (bool) ($n->is_read ?? false),
                'created_at' => $n->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'current_page' => $rows->currentPage(),
                'last_page'    => $rows->lastPage(),
                'total'        => $rows->total(),
            ],
        ]);
    }

    /**
     * PUT /api/v1/notifications/{id}/read
     * Mark a notification as read.
     */
    public function markNotificationRead(Request $request, int $id)
    {
        $user = $this->apiUser($request);
        DB::table('notifications')
            ->where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('to', $user->id)->orWhere('to', $user->department);
            })
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
    }
}
