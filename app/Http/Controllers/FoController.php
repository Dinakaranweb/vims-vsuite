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
use App\Models\Postal;
use App\Models\ReplyPost;
use App\Models\User;
use App\Models\DocumentApproval;
use App\Models\DocumentApprovalForwardings;
use App\Models\PaymentDetails;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

use App\Exports\ForwardedDocumentsExport;
use App\Exports\PaymentDetailsExport;
use App\Exports\FoDocumentsExport;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
use Mail;

use App\Http\Controllers\NotificationController;
use App\Jobs\SendTicketNotificationMail;

class FoController extends Controller
{
    
    private function buildOverallStats()
    {
        $dept = 'Students Welfare';

        /* ---------- RELEVANT DOCUMENTS ---------- */
        $approvalIds = DocumentApproval::whereNull('deleted_at')
            ->where(fn ($q) =>
                $q->where('from', $dept)->orWhere('to', $dept)
            )
            ->pluck('id')
            ->toArray();

        $forwardedIds = DocumentApprovalForwardings::where('forwarded_to', $dept)
            ->whereHas('documentApproval', fn ($q) => $q->whereNull('deleted_at'))
            ->pluck('doc_id')
            ->unique()
            ->toArray();

        $allRelevantDocIds = array_values(array_unique(
            array_merge($approvalIds, $forwardedIds)
        ));

        $totalForwardedCount = count($allRelevantDocIds);

        /* ---------- PAYMENT PROCESSING ---------- */
        $paymentProcessing = DB::table('payment_processing')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->select('doc_id', 'assigned_to', 'status')
            ->get();

        $assignedDocIds = $paymentProcessing
            ->pluck('doc_id')
            ->unique()
            ->values()
            ->toArray();

        $assignedCount = count($assignedDocIds);

        /* ---------- NEW (CORRECT LOGIC) ---------- */
        $newDocIds = array_diff($allRelevantDocIds, $assignedDocIds);
        $newCount = count($newDocIds);

        /* ---------- STATUS COUNTS ---------- */
        $paymentStartedCount = $paymentProcessing
            ->whereIn('status', ['In Progress', 'Payment In Progress'])
            ->pluck('doc_id')->unique()->count();

        $paymentCompletedCount = $paymentProcessing
            ->where('status', 'Completed')
            ->pluck('doc_id')->unique()->count();

        $holdCount = $paymentProcessing
            ->where('status', 'Hold')
            ->pluck('doc_id')->unique()->count();

        $paymentStartedDocIds = $paymentProcessing
                                ->whereIn('status', ['In Progress', 'Payment In Progress'])
                                ->pluck('doc_id')
                                ->unique()
                                ->values()
                                ->toArray();

        $paymentCompletedDocIds = $paymentProcessing
                                ->where('status', 'Completed')
                                ->pluck('doc_id')
                                ->unique()
                                ->values()
                                ->toArray();

        $holdDocIds = $paymentProcessing
                                ->where('status', 'Hold')
                                ->pluck('doc_id')
                                ->unique()
                                ->values()
                                ->toArray();

        /* ---------- PAYMENT TYPES ---------- */
        $paymentStats = DB::table('payment_details')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->selectRaw('
                SUM(CASE WHEN has_full_payment = 1 THEN 1 ELSE 0 END) as full_count,
                SUM(CASE WHEN has_full_payment = 0 AND has_partial_payment = 1 THEN 1 ELSE 0 END) as advance_count
            ')
            ->from(function ($q) use ($allRelevantDocIds) {
                $q->from('payment_details')
                ->select('doc_id')
                ->selectRaw('MAX(payment_type = "Full Payment") as has_full_payment')
                ->selectRaw('MAX(payment_type = "Partial Payment") as has_partial_payment')
                ->whereIn('doc_id', $allRelevantDocIds)
                ->groupBy('doc_id');
            }, 'x')
            ->first();

        /* ---------- PAYMENT TYPE DOC IDS ---------- */
        $paymentTypeResults = DB::table('payment_details')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->select('doc_id')
            ->selectRaw('
                MAX(CASE WHEN payment_type = "Full Payment" THEN 1 ELSE 0 END) as has_full,
                MAX(CASE WHEN payment_type = "Partial Payment" THEN 1 ELSE 0 END) as has_partial
            ')
            ->groupBy('doc_id')
            ->get();

        $fullPaymentDocIds = [];
        $advancePaymentDocIds = [];

        foreach ($paymentTypeResults as $row) {
            if ($row->has_full) {
                $fullPaymentDocIds[] = $row->doc_id;
            } elseif ($row->has_partial) {
                $advancePaymentDocIds[] = $row->doc_id;
            }
        }


        return [
            // counts
            'totalForwardedCount'   => $totalForwardedCount,
            'assginedDocsCount'         => $assignedCount,
            'newCount'              => $newCount,
            'paymentStartedCount'   => $paymentStartedCount,
            'paymentCompletedCount' => $paymentCompletedCount,
            'holdCount'             => $holdCount,
            'fullCount'             => $paymentStats->full_count ?? 0,
            'advanceCount'          => $paymentStats->advance_count ?? 0,

            // ids (for listings)
            'allRelevantDocIds'      => $allRelevantDocIds,
            'assignedDocIds'         => $assignedDocIds,
            'newDocIds'              => $newDocIds,
            'paymentStartedDocIds'   => $paymentStartedDocIds,
            'paymentCompletedDocIds' => $paymentCompletedDocIds,
            'holdDocIds'             => $holdDocIds,

            'fullPaymentDocIds'        => $fullPaymentDocIds,     // ✅ ADDED
            'advancePaymentDocIds'     => $advancePaymentDocIds,
        ];
    }

    public function Dashboard_FO(Request $request) {

        $activeMenu = "fo_dashboard";
        $activeDropdown = "";

        // 1. Get all staff members in Students Welfare department
        $staffMembers = User::where('department', 'Students Welfare')
            ->whereIn('role', ['staff', 'HOD'])
            ->get(['id', 'name', 'email']);

        // 2. Get all relevant documents for Students Welfare
        $studentWelfareDept = 'Students Welfare';
        
        // Documents created by or sent to Students Welfare
        $relevantDocsQuery = DocumentApproval::whereNull('deleted_at')
            ->where(function($query) use ($studentWelfareDept) {
                $query->where('from', $studentWelfareDept)
                    ->orWhere('to', $studentWelfareDept);
            });
        
        $relevantDocIdsFromApprovals = $relevantDocsQuery->pluck('id')->toArray();
        
        // Documents forwarded to Students Welfare
        $forwardedDocIds = DocumentApprovalForwardings::where('forwarded_to', $studentWelfareDept)
            ->whereHas('documentApproval', function($q) {
                $q->whereNull('deleted_at');
            })
            ->select('doc_id')
            ->distinct()
            ->pluck('doc_id')
            ->toArray();
        
        // Combine all relevant document IDs
        $allRelevantDocIds = array_unique(array_merge($relevantDocIdsFromApprovals, $forwardedDocIds));
        $totalForwardedCount = count($allRelevantDocIds);

        if ($totalForwardedCount === 0) {
            dd(['message' => 'No documents found', 'department' => $studentWelfareDept]);
        }

        // 3. Get payment processing data
        $paymentProcessingData = DB::table('payment_processing')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->select('doc_id', 'assigned_to', 'status')
            ->get();

        $paymentStartedDocs = $paymentProcessingData->whereIn('status', ['In Progress', 'Payment In Progress'])->pluck('doc_id')->unique()->toArray();
        $paymentStartedCount = count($paymentStartedDocs);

        $paymentCompletedDocs = $paymentProcessingData->where('status', 'Completed')
            ->pluck('doc_id')
            ->unique()
            ->toArray();
        $paymentCompletedCount = count($paymentCompletedDocs);

        $assginedDocs = $paymentProcessingData
            ->pluck('doc_id')
            ->unique()
            ->toArray();
        $assginedDocsCount = count($assginedDocs);

        // 4. Get payment type breakdown by document
        $paymentStats = DB::table('payment_details')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->selectRaw('
                SUM(CASE WHEN has_full_payment = 1 THEN 1 ELSE 0 END) as full_count,
                SUM(CASE WHEN has_full_payment = 0 AND has_partial_payment = 1 THEN 1 ELSE 0 END) as advance_count
            ')
            ->from(function($query) use ($allRelevantDocIds) {
                $query->from('payment_details')
                    ->select('doc_id')
                    ->selectRaw('MAX(CASE WHEN payment_type = "Full Payment" THEN 1 ELSE 0 END) as has_full_payment')
                    ->selectRaw('MAX(CASE WHEN payment_type = "Partial Payment" THEN 1 ELSE 0 END) as has_partial_payment')
                    ->whereIn('doc_id', $allRelevantDocIds)
                    ->groupBy('doc_id');
            }, 'doc_payments')
            ->first();

        $fullCount = $paymentStats->full_count ?? 0;
        $advanceCount = $paymentStats->advance_count ?? 0;

        // 5. New documents
        $newDocIds = array_diff($allRelevantDocIds, $assginedDocs);
        $newCount = count($newDocIds);

        // 6. Staff statistics (need to update for staff-specific payment types)
        $staffStatistics = [];
        $staffPaymentData = $paymentProcessingData->groupBy('assigned_to');
        
        // Get all payment details for staff documents
        $allStaffDocIds = [];
        foreach ($staffPaymentData as $staffId => $payments) {
            $allStaffDocIds = array_merge($allStaffDocIds, $payments->pluck('doc_id')->unique()->toArray());
        }
        $allStaffDocIds = array_unique($allStaffDocIds);
        
        // Get payment status for all staff documents in one query
        $staffDocPaymentStatus = [];
        if (!empty($allStaffDocIds)) {
            $results = DB::table('payment_details')
                ->whereIn('doc_id', $allStaffDocIds)
                ->select('doc_id')
                ->selectRaw('MAX(CASE WHEN payment_type = "Full Payment" THEN 1 ELSE 0 END) as has_full_payment')
                ->selectRaw('MAX(CASE WHEN payment_type = "Partial Payment" THEN 1 ELSE 0 END) as has_partial_payment')
                ->groupBy('doc_id')
                ->get();
            
            foreach ($results as $result) {
                $staffDocPaymentStatus[$result->doc_id] = [
                    'has_full' => $result->has_full_payment,
                    'has_partial' => $result->has_partial_payment
                ];
            }
        }
        
        foreach ($staffMembers as $staff) {
            $staffId = $staff->id;
            $staffPayments = $staffPaymentData[$staffId] ?? collect();
            
            $assignedDocs = $staffPayments->pluck('doc_id')->unique()->toArray();
            $assignedCount = count($assignedDocs);
            
            $paymentStartedDocsForStaff = $staffPayments
                ->where('status', 'In Progress')
                ->pluck('doc_id')
                ->unique()
                ->toArray();
            $paymentStartedCountStaff = count($paymentStartedDocsForStaff);
            
            // Calculate payment types for this staff
            $staffFullCount = 0;
            $staffAdvanceCount = 0;

            foreach ($assignedDocs as $docId) {
                if (!isset($staffDocPaymentStatus[$docId])) {
                    continue;
                }

                if ($staffDocPaymentStatus[$docId]['has_full']) {
                    // At least one final payment → FULL
                    $staffFullCount++;
                } elseif ($staffDocPaymentStatus[$docId]['has_partial']) {
                    // Only advance payments → ADVANCE
                    $staffAdvanceCount++;
                }
            }
            
            $staffPaymentTypes = [];
            if ($staffFullCount > 0) {
                $staffPaymentTypes['Full Payment'] = $staffFullCount;
            }
            if ($staffAdvanceCount > 0) {
                $staffPaymentTypes['Partial Payment'] = $staffAdvanceCount;
            }
            
            $completedDocs = $staffPayments
                ->where('status', 'Completed')
                ->pluck('doc_id')
                ->unique()
                ->toArray();
            $completedCount = count($completedDocs);
            
            $holdDocs = $staffPayments
                ->where('status', 'Hold')
                ->pluck('doc_id')
                ->unique()
                ->toArray();
            $holdCount = count($holdDocs);

            $staffStatistics[$staffId] = [
                'staff_info' => $staff,
                'counts' => [
                    'assigned' => $assignedCount,
                    'payment_started' => $paymentStartedCountStaff,
                    'payment_types' => $staffPaymentTypes,
                    'completed' => $completedCount,
                    'hold' => $holdCount
                ]
            ];
        }

        // 7. Get recent documents
        $sortField = $request->get('sort', 'forwarded_date');
        $sortDirection = $request->get('direction', 'desc');
        
        $validSortFields = ['doc_id', 'title', 'priority', 'from', 'payment_status', 'forwarded_date'];
        
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'forwarded_date';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        // Base query
        $query = DocumentApproval::whereIn('document_approvals.id', $allRelevantDocIds);
        
        // Apply simple search filters first
        if ($request->filled('title')) {
            $query->where('document_approvals.title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        
        if ($request->filled('priority')) {
            $query->where('document_approvals.priority', $request->priority);
        }
        
        if ($request->filled('from')) {
            $query->where('document_approvals.from', 'like', '%' . $request->from . '%');
        }
        
        // Get document IDs after applying basic filters
        $filteredDocIds = $query->pluck('document_approvals.id')->toArray();
        
        if (empty($filteredDocIds)) {
            // Return empty pagination if no documents match basic filters
            $alert = array(
                'message' => 'No Documents found for the given criteria.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($alert);
        }
        
        // Now build the main query with joins
        $mainQuery = DB::table('document_approvals as da')
            ->select(
                'da.id',
                'da.doc_id',
                'da.title',
                'da.priority',
                'da.from',
                'da.created_at as doc_created_at',
                'da.subject',
                'da.justification',
                'da.amount',
                'da.by',
                'da.status as doc_status',
                'pp.expenditure_id',
                'pp.status as payment_status',
                'pp.created_at as payment_created_at',
                'daf.created_at as forwarded_date',
                DB::raw('COALESCE(daf.created_at, da.created_at) as sort_date')
            )
            ->leftJoin('payment_processing as pp', 'da.id', '=', 'pp.doc_id')
            ->leftJoin('document_approval_forwardings as daf', function($join) use ($studentWelfareDept) {
                $join->on('da.id', '=', 'daf.doc_id')
                    ->where('daf.forwarded_to', $studentWelfareDept);
            })
            ->whereIn('da.id', $filteredDocIds);
        
        // Apply date range filtering
        if ($request->filled('date_from')) {
            $mainQuery->where(function($q) use ($request) {
                $q->whereDate('daf.created_at', '>=', $request->date_from)
                ->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('daf.created_at')
                        ->whereDate('da.created_at', '>=', $request->date_from);
                });
            });
        }
        
        if ($request->filled('date_to')) {
            $mainQuery->where(function($q) use ($request) {
                $q->whereDate('daf.created_at', '<=', $request->date_to)
                ->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('daf.created_at')
                        ->whereDate('da.created_at', '<=', $request->date_to);
                });
            });
        }
        
        // Apply expenditure ID search
        if ($request->filled('exp_id')) {
            $mainQuery->where(function($q) use ($request) {
                $q->where('pp.expenditure_id', 'like', '%' . $request->exp_id . '%')
                ->orWhere('da.doc_id', 'like', '%' . $request->exp_id . '%');
            });
        }
        
        // Apply payment status filter
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'Not yet Assigned') {
                $mainQuery->whereNull('pp.status');
            } else {
                $mainQuery->where('pp.status', $request->payment_status);
            }
        }
        
        // Apply sorting
        switch ($sortField) {
            case 'doc_id':
                $mainQuery->orderBy('da.doc_id', $sortDirection);
                break;
            case 'title':
                $mainQuery->orderBy('da.title', $sortDirection);
                break;
            case 'priority':
                $mainQuery->orderBy('da.priority', $sortDirection);
                break;
            case 'from':
                $mainQuery->orderBy('da.from', $sortDirection);
                break;
            case 'payment_status':
                $mainQuery->orderBy('pp.status', $sortDirection);
                break;
            case 'forwarded_date':
            default:
                $mainQuery->orderBy('sort_date', $sortDirection);
                break;
        }
        
        // Paginate results
        $recentForwardedDocs = $mainQuery->paginate(10);
        
        // Convert to DocumentApproval models if needed
        $recentForwardedDocs->getCollection()->transform(function ($item) {
            // Create a DocumentApproval model instance
            $doc = new DocumentApproval();
            $doc->id = $item->id;
            $doc->doc_id = $item->doc_id;
            $doc->title = $item->title;
            $doc->priority = $item->priority;
            $doc->from = $item->from;
            $doc->created_at = $item->doc_created_at;
            $doc->subject = $item->subject;
            $doc->justification = $item->justification;
            $doc->amount = $item->amount;
            $doc->by = $item->by;
            $doc->status = $item->doc_status;
            // Add forwarded_date as an attribute
            $doc->forwarded_date = $item->forwarded_date;
            // Add payment info as attributes
            $doc->payment_status = $item->payment_status;
            $doc->expenditure_id = $item->expenditure_id;
            $doc->payment_created_at = $item->payment_created_at;
            
            return $doc;
        });

        $searchParams = $request->all();

        $otherPaymentCount = max(0, intval($paymentStartedCount) - (intval($advanceCount) + intval($fullCount)));

        return view('frontend.document.fo.dashboard', compact(
            'sortField',
            'sortDirection',
            'activeMenu', 
            'activeDropdown',
            'totalForwardedCount',
            'assginedDocsCount',  // Note: variable name has a typo - should be $assignedDocsCount
            'paymentStartedCount',
            'paymentCompletedCount',
            'newCount',
            'advanceCount',
            'fullCount',
            'staffStatistics',
            'staffMembers',
            'recentForwardedDocs',
            'searchParams'
        ));
    }

    public function new()
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "new";

        $stats = $this->buildOverallStats();

        $documents = DocumentApproval::whereIn('id', $stats['newDocIds'])
            ->orderByDesc('created_at')
            ->paginate(15);


        return view('frontend.document.fo.list', array_merge(
            $stats,
            compact('documents', 'activeMenu', 'activeDropdown')
        ));
    }

    public function assigned()
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "assigned";

        $stats = $this->buildOverallStats();

        $documents = DocumentApproval::whereIn('id', $stats['assignedDocIds'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);


        return view('frontend.document.fo.list', array_merge(
            $stats,
            compact('documents', 'activeMenu', 'activeDropdown')
        ));
    }

    public function completed()
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "completed";

        $stats = $this->buildOverallStats();

        $documents = DocumentApproval::whereIn('id', $stats['paymentCompletedDocIds'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);


        return view('frontend.document.fo.list', array_merge(
            $stats,
            compact('documents', 'activeMenu', 'activeDropdown')
        ));
    }

    public function total()
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "total";

        $stats = $this->buildOverallStats();

        $documents = DocumentApproval::whereIn('id', $stats['allRelevantDocIds'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);


        return view('frontend.document.fo.list', array_merge(
            $stats,
            compact('documents', 'activeMenu', 'activeDropdown')
        ));
    }

    public function fullPayment()
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "fullPayment";

        $stats = $this->buildOverallStats();

        $documents = DocumentApproval::whereIn(
            'id',
            $stats['fullPaymentDocIds']
        )->orderByDesc('created_at')->paginate(15);

        return view('frontend.document.fo.list', array_merge(
            $stats,
            compact('documents', 'activeMenu', 'activeDropdown')
        ));
    }

    public function advancePayment()
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "advancePayment";

        $stats = $this->buildOverallStats();

        $documents = DocumentApproval::whereIn(
            'id',
            $stats['advancePaymentDocIds']
        )->orderByDesc('created_at')->paginate(15);

        return view('frontend.document.fo.list', array_merge(
            $stats,
            compact('documents', 'activeMenu', 'activeDropdown')
        ));
    }

    public function staffDetailedReport($staffId)
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "";

        $dept = 'Students Welfare';

        /* ---------- RELEVANT DOCUMENTS ---------- */
        $approvalIds = DocumentApproval::whereNull('deleted_at')
            ->where(fn ($q) =>
                $q->where('from', $dept)->orWhere('to', $dept)
            )
            ->pluck('id')
            ->toArray();

        $forwardedIds = DocumentApprovalForwardings::where('forwarded_to', $dept)
            ->whereHas('documentApproval', fn ($q) => $q->whereNull('deleted_at'))
            ->pluck('doc_id')
            ->unique()
            ->toArray();

        $allRelevantDocIds = array_unique(array_merge($approvalIds, $forwardedIds));

        /* ---------- STAFF PAYMENT PROCESSING ---------- */
        $staffPayments = DB::table('payment_processing')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->where('assigned_to', $staffId)
            ->select('doc_id', 'status')
            ->get();

        $assignedDocIds = $staffPayments
            ->pluck('doc_id')
            ->unique()
            ->values()
            ->toArray();

        /* ---------- STATUS DOC IDS ---------- */
        $inProgressDocIds = $staffPayments
            ->where('status', 'In Progress')
            ->pluck('doc_id')
            ->unique()
            ->values()
            ->toArray();

        $completedDocIds = $staffPayments
            ->where('status', 'Completed')
            ->pluck('doc_id')
            ->unique()
            ->values()
            ->toArray();

        $holdDocIds = $staffPayments
            ->where('status', 'Hold')
            ->pluck('doc_id')
            ->unique()
            ->values()
            ->toArray();

        /* ---------- PAYMENT TYPES ---------- */
        $paymentTypeResults = DB::table('payment_details')
            ->whereIn('doc_id', $assignedDocIds)
            ->select('doc_id')
            ->selectRaw('
                MAX(CASE WHEN payment_type = "Full Payment" THEN 1 ELSE 0 END) as has_full,
                MAX(CASE WHEN payment_type = "Partial Payment" THEN 1 ELSE 0 END) as has_partial
            ')
            ->groupBy('doc_id')
            ->get();

        $fullPaymentDocIds = [];
        $advancePaymentDocIds = [];

        foreach ($paymentTypeResults as $row) {
            if ($row->has_full) {
                $fullPaymentDocIds[] = $row->doc_id;
            } elseif ($row->has_partial) {
                $advancePaymentDocIds[] = $row->doc_id;
            }
        }

        /* ---------- LOAD DOCUMENTS (OPTIONAL DEFAULT TAB) ---------- */
        $documents = DocumentApproval::whereIn('id', $assignedDocIds)
            ->orderByDesc('created_at')
            ->paginate(15);

        $staff = User::findOrFail($staffId);

        return view('frontend.document.fo.staff-report', compact(
            'staff',

            // counts
            'assignedDocIds',
            'inProgressDocIds',
            'completedDocIds',
            'holdDocIds',
            'fullPaymentDocIds',
            'advancePaymentDocIds',

            // default list
            'documents',
            'activeMenu',
            'activeDropdown'
        ));
    }

    private function getStaffDocBuckets($staffId)
    {
        $dept = 'Students Welfare';

        $approvalIds = DocumentApproval::whereNull('deleted_at')
            ->where(fn ($q) =>
                $q->where('from', $dept)->orWhere('to', $dept)
            )
            ->pluck('id')
            ->toArray();

        $forwardedIds = DocumentApprovalForwardings::where('forwarded_to', $dept)
            ->whereHas('documentApproval', fn ($q) => $q->whereNull('deleted_at'))
            ->pluck('doc_id')
            ->unique()
            ->toArray();

        $allRelevantDocIds = array_unique(array_merge($approvalIds, $forwardedIds));

        $staffPayments = DB::table('payment_processing')
            ->whereIn('doc_id', $allRelevantDocIds)
            ->where('assigned_to', $staffId)
            ->select('doc_id', 'status')
            ->get();

        $assignedDocIds = $staffPayments->pluck('doc_id')->unique()->toArray();

        $inProgressDocIds = $staffPayments
            ->where('status', 'In Progress')
            ->pluck('doc_id')->unique()->toArray();

        $completedDocIds = $staffPayments
            ->where('status', 'Completed')
            ->pluck('doc_id')->unique()->toArray();

        $holdDocIds = $staffPayments
            ->where('status', 'Hold')
            ->pluck('doc_id')->unique()->toArray();

        $paymentTypes = DB::table('payment_details')
            ->whereIn('doc_id', $assignedDocIds)
            ->select('doc_id')
            ->selectRaw('
                MAX(payment_type = "Full Payment") as has_full,
                MAX(payment_type = "Partial Payment") as has_partial
            ')
            ->groupBy('doc_id')
            ->get();

        $fullPaymentDocIds = [];
        $advancePaymentDocIds = [];

        foreach ($paymentTypes as $row) {
            if ($row->has_full) {
                $fullPaymentDocIds[] = $row->doc_id;
            } elseif ($row->has_partial) {
                $advancePaymentDocIds[] = $row->doc_id;
            }
        }

        return compact(
            'assignedDocIds',
            'inProgressDocIds',
            'completedDocIds',
            'holdDocIds',
            'fullPaymentDocIds',
            'advancePaymentDocIds'
        );
    }

    public function staffAssignedDocs($staffId)
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "staffassigned";

        $data = $this->getStaffDocBuckets($staffId);

        $documents = DocumentApproval::whereIn('id', $data['assignedDocIds'])
            ->paginate(15);

        return view('frontend.document.fo.staff-report', array_merge(
            $data,
            [
                'documents' => $documents,
                'activeDropdown' => 'staffassigned',
                'staff' => User::findOrFail($staffId),
                'activeMenu' => $activeMenu
            ]
        ));
    }

    public function staffInProgressDocs($staffId)
    {

        $activeMenu = "fo_dashboard";
        $activeDropdown = "staffInProgress";

        $data = $this->getStaffDocBuckets($staffId);

        $documents = DocumentApproval::whereIn('id', $data['inProgressDocIds'])
            ->paginate(15);

        return view('frontend.document.fo.staff-report', array_merge(
            $data,
            [
                'documents' => $documents,
                'activeDropdown' => 'staffInProgress',
                'staff' => User::findOrFail($staffId),
                'activeMenu' => $activeMenu
            ]
        ));
    }

    public function staffCompletedDocs($staffId)
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "staffCompleted";

        $data = $this->getStaffDocBuckets($staffId);

        $documents = DocumentApproval::whereIn('id', $data['completedDocIds'])
            ->paginate(15);

        return view('frontend.document.fo.staff-report', array_merge(
            $data,
            [
                'documents' => $documents,
                'activeDropdown' => 'staffCompleted',
                'staff' => User::findOrFail($staffId),
                'activeMenu' => $activeMenu
            ]
        ));
    }

    public function staffHoldDocs($staffId)
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "staffHold";

        $data = $this->getStaffDocBuckets($staffId);

        $documents = DocumentApproval::whereIn('id', $data['holdDocIds'])
            ->paginate(15);

        return view('frontend.document.fo.staff-report', array_merge(
            $data,
            [
                'documents' => $documents,
                'activeDropdown' => 'staffHold',
                'staff' => User::findOrFail($staffId),
                'activeMenu' => $activeMenu
            ]
        ));
    }

    public function staffFullPaymentDocs($staffId)
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "staffFullPayment";

        $data = $this->getStaffDocBuckets($staffId);

        $documents = DocumentApproval::whereIn('id', $data['fullPaymentDocIds'])
            ->paginate(15);

        return view('frontend.document.fo.staff-report', array_merge(
            $data,
            [
                'documents' => $documents,
                'activeDropdown' => 'staffFullPayment',
                'staff' => User::findOrFail($staffId),
                'activeMenu' => $activeMenu
            ]
        ));
    }

    public function staffAdvancePaymentDocs($staffId)
    {
        $activeMenu = "fo_dashboard";
        $activeDropdown = "staffAdvancePayment";

        $data = $this->getStaffDocBuckets($staffId);

        $documents = DocumentApproval::whereIn('id', $data['advancePaymentDocIds'])
            ->paginate(15);

        return view('frontend.document.fo.staff-report', array_merge(
            $data,
            [
                'documents' => $documents,
                'activeDropdown' => 'staffAdvancePayment',
                'staff' => User::findOrFail($staffId),
                'activeMenu' => $activeMenu
            ]
        ));
    }

    private function getAllRelevantDocIds()
    {

        $studentWelfareDept = 'Students Welfare';
        
        // Documents created by or sent to Students Welfare
        $relevantDocsQuery = DocumentApproval::whereNull('deleted_at')
            ->where(function($query) use ($studentWelfareDept) {
                $query->where('from', $studentWelfareDept)
                    ->orWhere('to', $studentWelfareDept);
            });
        
        $relevantDocIdsFromApprovals = $relevantDocsQuery->pluck('id')->toArray();
        
        // Documents forwarded to Students Welfare
        $forwardedDocIds = DocumentApprovalForwardings::where('forwarded_to', $studentWelfareDept)
            ->whereHas('documentApproval', function($q) {
                $q->whereNull('deleted_at');
            })
            ->select('doc_id')
            ->distinct()
            ->pluck('doc_id')
            ->toArray();
        
        // Combine all relevant document IDs
        $allRelevantDocIds = array_unique(array_merge($relevantDocIdsFromApprovals, $forwardedDocIds));
        
        // Or if you're getting from somewhere else, use that logic
        // return $allRelevantDocIds; // Your existing variable
        
        return $allRelevantDocIds;
    }

    public function exportForwardedDocuments(Request $request)
    {
        
        $allRelevantDocIds = $this->getAllRelevantDocIds(); // Your existing logic to get document IDs
        $studentWelfareDept = 'Students Welfare'; // Same as your display method
        
        $sortField = $request->get('sort', 'forwarded_date');
        $sortDirection = $request->get('direction', 'desc');
        
        $validSortFields = ['doc_id', 'title', 'priority', 'from', 'payment_status', 'forwarded_date'];
        
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'forwarded_date';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        // Base query
        $query = DocumentApproval::whereIn('document_approvals.id', $allRelevantDocIds);

        // Apply simple search filters first
        if ($request->filled('title')) {
            $query->where('document_approvals.title', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('doc_id')) {
            $query->where('document_approvals.doc_id', 'like', '%' . $request->doc_id . '%');
        }
        
        if ($request->filled('priority')) {
            $query->where('document_approvals.priority', $request->priority);
        }
        
        if ($request->filled('from')) {
            $query->where('document_approvals.from', 'like', '%' . $request->from . '%');
        }
        
        // Get document IDs after applying basic filters
        $filteredDocIds = $query->pluck('document_approvals.id')->toArray();
        
        if (empty($filteredDocIds)) {
            // Return empty Excel file
            $alert = array(
                'message' => 'No Documents found for the given criteria.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($alert);
        }
        
        // Now build the main query with joins
        $mainQuery = DB::table('document_approvals as da')
            ->select(
                'da.id',
                'da.doc_id',
                'da.title',
                'da.priority',
                'da.from',
                'da.amount as budget_amount',
                'da.status as doc_status',
                'da.created_at as doc_created_at',
                'da.approval_status as file_status',
                
                // Payment processing table for payment status
                'pp.status as payment_status',
                
                // Forwarding info
                'daf.created_at as forwarded_date',
                
                // Payment details aggregation
                DB::raw('COALESCE(SUM(pd.paid_amount), 0) as total_paid_amount'),
                DB::raw('COALESCE(SUM(pd.tds_amount), 0) as total_tds_amount'),
                DB::raw('GROUP_CONCAT(DISTINCT pd.expenditure_id) as expenditure_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT pd.expenditure_category) as expenditure_categories'),
                DB::raw('GROUP_CONCAT(DISTINCT pd.mode) as payment_modes'),
                DB::raw('GROUP_CONCAT(DISTINCT pd.payment_reference_no) as reference_numbers'),
                DB::raw('MAX(CASE WHEN pd.mode = "cheque" THEN pd.cheque_cleared_date ELSE pd.payment_date END) as latest_payment_date'),
                DB::raw('GROUP_CONCAT(DISTINCT pd.payment_type) as payment_types'),
                DB::raw('GROUP_CONCAT(DISTINCT pd.remarks SEPARATOR "\n") as payment_remarks'),
                DB::raw('COALESCE(SUM(pd.bill_amount), 0) as total_bill_amount'), // NEW
                DB::raw('COALESCE(SUM(pd.refund_amount), 0) as total_refund_amount'), // NEW
                DB::raw('MAX(pd.bill_submission_date) as latest_bill_submission_date'), // NEW
                DB::raw('MAX(pd.refund_date) as latest_refund_date'), // NEW
                
                // For sorting
                DB::raw('COALESCE(daf.created_at, da.created_at) as sort_date')
            )
            ->leftJoin('payment_details as pd', 'da.id', '=', 'pd.doc_id')
            ->leftJoin('payment_processing as pp', 'da.id', '=', 'pp.doc_id')
            ->leftJoin('document_approval_forwardings as daf', function($join) use ($studentWelfareDept) {
                $join->on('da.id', '=', 'daf.doc_id')
                    ->where('daf.forwarded_to', $studentWelfareDept);
            })
            ->whereIn('da.id', $filteredDocIds)
            ->groupBy(
                'da.id',
                'da.doc_id',
                'da.title',
                'da.priority',
                'da.from',
                'da.amount',
                'da.status',
                'da.created_at',
                'da.approval_status',
                'pp.status',
                'daf.created_at'
            );
        
        // Apply date range filtering
        if ($request->filled('date_from')) {
            $mainQuery->where(function($q) use ($request) {
                $q->whereDate('daf.created_at', '>=', $request->date_from)
                ->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('daf.created_at')
                        ->whereDate('da.created_at', '>=', $request->date_from);
                });
            });
        }
        
        if ($request->filled('date_to')) {
            $mainQuery->where(function($q) use ($request) {
                $q->whereDate('daf.created_at', '<=', $request->date_to)
                ->orWhere(function($subQ) use ($request) {
                    $subQ->whereNull('daf.created_at')
                        ->whereDate('da.created_at', '<=', $request->date_to);
                });
            });
        }
        
        // Apply expenditure ID search
        if ($request->filled('exp_id')) {
            $mainQuery->where(function($q) use ($request) {
                $q->where('pd.expenditure_id', 'like', '%' . $request->exp_id . '%')
                ->orWhere('da.doc_id', 'like', '%' . $request->exp_id . '%');
            });
        }
        
        // Apply payment status filter
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'Not yet Assigned') {
                $mainQuery->whereNull('pp.status');
            } else {
                $mainQuery->where('pp.status', $request->payment_status);
            }
        }
        
        // Apply sorting
        switch ($sortField) {
            case 'doc_id':
                $mainQuery->orderBy('da.doc_id', $sortDirection);
                break;
            case 'title':
                $mainQuery->orderBy('da.title', $sortDirection);
                break;
            case 'priority':
                $mainQuery->orderBy('da.priority', $sortDirection);
                break;
            case 'from':
                $mainQuery->orderBy('da.from', $sortDirection);
                break;
            case 'payment_status':
                $mainQuery->orderBy('pp.status', $sortDirection);
                break;
            case 'forwarded_date':
            default:
                $mainQuery->orderBy('sort_date', $sortDirection);
                break;
        }
        
        // Get ALL results (no pagination limit for export)
        $documents = $mainQuery->get();

        if ($documents->isEmpty()) {
            $alert = array(
                'message' => 'No Documents found for the given criteria.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($alert);
        }
        
        // Generate filename with search criteria
        $filename = $this->generateExportFilename($request);
        
        // Pass the documents directly to export class
        return Excel::download(
            new FoDocumentsExport($documents, $request->all()), 
            $filename
        );
    }

    private function generateExportFilename(Request $request)
    {
        $filename = 'forwarded_documents';
        
        // Add filter info to filename
        if ($request->filled('title')) {
            $filename .= '_title-' . substr($request->title, 0, 10);
        }
        
        if ($request->filled('doc_id')) {
            $filename .= '_doc-' . substr($request->doc_id, 0, 10);
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filename .= '_' . str_replace('-', '', $request->date_from) . '-to-' . str_replace('-', '', $request->date_to);
        } elseif ($request->filled('date_from')) {
            $filename .= '_from-' . str_replace('-', '', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $filename .= '_to-' . str_replace('-', '', $request->date_to);
        }
        
        if ($request->filled('priority')) {
            $filename .= '_' . strtolower($request->priority);
        }
        
        $filename .= '_' . date('Y_m_d_His') . '.xlsx';
        
        // Limit filename length
        if (strlen($filename) > 100) {
            $filename = 'forwarded_documents_' . date('Y_m_d_His') . '.xlsx';
        }
        
        return $filename;
    }

}
