<?php

namespace App\Http\Controllers;

use App\Models\DocumentApproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChairmanController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->department !== 'Chairman' && $user->role !== 'SuperAdmin') {
            abort(403, 'Access denied. Chairman only.');
        }

        // ── Stat cards ────────────────────────────────────────────────────
        $pendingDocs = DocumentApproval::where('forwarded_to', 'Chairman')
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected', 'Draft', 'Retracted'])
            ->count();

        $approvedDocs = DB::table('approval_log')
            ->where('status', 'like', 'Approved by Chairman%')
            ->distinct('doc_id')
            ->count('doc_id');

        $totalDocs = DocumentApproval::where(function ($q) {
            $q->whereJsonContains('approval_sequence', 'Chairman')
              ->orWhere('forwarded_to', 'Chairman');
        })->count();

        $completedDocs = DocumentApproval::where(function ($q) {
            $q->whereJsonContains('approval_sequence', 'Chairman')
              ->orWhere('forwarded_to', 'Chairman');
        })->whereIn('status', ['Completed', 'Closed'])->count();

        // ── Status distribution (donut) ───────────────────────────────────
        $statusDistribution = DocumentApproval::where(function ($q) {
                $q->whereJsonContains('approval_sequence', 'Chairman')
                  ->orWhere('forwarded_to', 'Chairman');
            })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->status => (int) $r->count]);

        // ── Monthly approvals by Chairman — last 6 months (bar chart) ─────
        $monthlyInflow = DB::table('approval_log')
            ->where('status', 'like', 'Approved by Chairman%')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        // ── Approval trend — last 12 months (line chart) ──────────────────
        $approvalTrend = DB::table('approval_log')
            ->where('status', 'like', 'Approved by Chairman%')
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        // ── Monthly sanctioned amount — last 6 months ────────────────────
        $monthlyAmounts = DocumentApproval::where(function ($q) {
                $q->whereJsonContains('approval_sequence', 'Chairman')
                  ->orWhere('forwarded_to', 'Chairman');
            })
            ->whereNotNull('sanctioned_amount')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, SUM(sanctioned_amount) as total")
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        // ── Recent pending documents (latest 10) ─────────────────────────
        $pendingDocuments = DocumentApproval::where('forwarded_to', 'Chairman')
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected', 'Draft', 'Retracted'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($doc) {
                $creator = User::find($doc->by);
                return (object) [
                    'id'           => $doc->id,
                    'doc_id'       => $doc->doc_id,
                    'title'        => $doc->title,
                    'from'         => $doc->from,
                    'amount'       => $doc->amount,
                    'priority'     => $doc->priority,
                    'status'       => $doc->status,
                    'created_by'   => optional($creator)->name,
                    'created_at'   => $doc->created_at,
                    'days_pending' => $doc->created_at ? (int) now()->diffInDays($doc->created_at) : 0,
                ];
            });

        return view('frontend.document.chairman.dashboard', compact(
            'pendingDocs', 'approvedDocs', 'totalDocs', 'completedDocs',
            'statusDistribution', 'monthlyInflow', 'approvalTrend',
            'pendingDocuments', 'monthlyAmounts'
        ));
    }
}
