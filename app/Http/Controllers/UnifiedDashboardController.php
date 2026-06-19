<?php

namespace App\Http\Controllers;

use App\Models\DocumentApproval;
use App\Models\Postal;
use App\Models\PostalForwarding;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnifiedDashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $role   = $user->role;
        $dept   = $user->department;
        $userId = $user->id;

        $isSuperAdmin = $role === 'SuperAdmin';
        $isStaff      = $role === 'Staff';

        // ── Document Approvals ────────────────────────────────────────────
        if ($isSuperAdmin) {
            $docQuery = DocumentApproval::where('status', '!=', 'Draft');
        } elseif ($isStaff) {
            // Staff see payment-processing documents assigned to them
            $assignedDocIds = DB::table('payment_processing')
                ->where('assigned_to', $userId)
                ->pluck('doc_id');
            $docQuery = DocumentApproval::whereIn('id', $assignedDocIds);
        } else {
            $docQuery = DocumentApproval::where('status', '!=', 'Draft')
                ->where(function ($q) use ($dept) {
                    $q->where('from', $dept)
                      ->orWhere('forwarded_to', $dept)
                      ->orWhere('to', $dept);
                });
        }

        $docs_total     = (clone $docQuery)->count();
        $docs_pending   = (clone $docQuery)->whereNotIn('status', ['Completed', 'Closed', 'Rejected', 'Draft'])->count();
        $docs_completed = (clone $docQuery)->whereIn('status', ['Completed', 'Closed'])->count();
        $docs_rejected  = (clone $docQuery)->where('status', 'Rejected')->count();

        $docMonthly = (clone $docQuery)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        // ── Tickets ───────────────────────────────────────────────────────
        if ($isSuperAdmin) {
            $tktQuery = Ticket::where('is_approved', true);
        } elseif ($isStaff) {
            $tktQuery = Ticket::where('is_approved', true)->where('assigned_to', $userId);
        } else {
            $tktQuery = Ticket::where('is_approved', true)->where('ticket_to', $dept);
        }

        $tkt_total      = (clone $tktQuery)->count();
        $tkt_open       = (clone $tktQuery)->where('status', 'Open')->count();
        $tkt_inprogress = (clone $tktQuery)->where('status', 'In Progress')->count();
        $tkt_hold       = (clone $tktQuery)->where('status', 'Hold')->count();
        $tkt_completed  = (clone $tktQuery)->where('status', 'Completed')->count();
        $tkt_closed     = (clone $tktQuery)->where('status', 'Closed')->count();

        $tktMonthly = (clone $tktQuery)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $recentTickets = (clone $tktQuery)
            ->where('status', '!=', 'Closed')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($t) => (object) [
                'id'       => $t->id,
                'ticket_id'=> $t->ticket_id,
                'title'    => $t->title,
                'from'     => $t->ticket_from,
                'priority' => $t->priority,
                'status'   => $t->status,
            ]);

        // ── Postal ────────────────────────────────────────────────────────
        if ($isStaff) {
            $post_total     = 0;
            $post_pending   = 0;
            $post_unread    = 0;
            $post_forwarded = 0;
        } elseif ($isSuperAdmin) {
            $post_total     = Postal::count();
            $post_pending   = Postal::whereNotIn('status', ['Closed', 'Received', 'Dispatched'])->count();
            $post_unread    = DB::table('postals')->where('is_read', 0)->count();
            $post_forwarded = PostalForwarding::where('status', 'Collected')->count();
        } else {
            $unreadDirect   = DB::table('postals')->where('sent_to', $dept)->whereNotNull('collected_by')->where('is_read', 0)->count();
            $unreadForwarded= DB::table('postal_forwardings')->where('forwarded_to', $dept)->where('status', 'Collected')->where('is_read', 0)->count();
            $post_total     = Postal::where('sent_to', $dept)->whereNotIn('status', ['Received', 'Dispatched'])->count();
            $post_pending   = Postal::where('sent_to', $dept)->whereNotIn('status', ['Closed', 'Received', 'Dispatched'])->count();
            $post_unread    = $unreadDirect + $unreadForwarded;
            $post_forwarded = PostalForwarding::where('forwarded_to', $dept)->where('status', 'Collected')->count();
        }

        // ── Route name helpers (role-aware links) ─────────────────────────
        if ($isSuperAdmin) {
            $tktRoutes = [
                'open'       => 'super_admin_open_tickets',
                'inprogress' => 'super_admin_tickets_in_progress',
                'hold'       => 'super_admin_tickets_on_hold',
                'completed'  => 'super_admin_completed_tickets',
                'closed'     => 'super_admin_closed_tickets',
                'total'      => 'super_admin_total_tickets',
            ];
            $docRoute  = 'total_documents';
        } elseif ($isStaff) {
            $tktRoutes = [
                'open'       => 'staff_open_tickets',
                'inprogress' => 'staff_tickets_in_progress',
                'hold'       => 'staff_hold_tickets',
                'completed'  => 'staff_completed_tickets',
                'closed'     => 'staff_closed_tickets',
                'total'      => 'staff_total_tickets',
            ];
            $docRoute  = 'staffpayment.new';
        } else {
            $tktRoutes = [
                'open'       => 'admin_open_tickets',
                'inprogress' => 'admin_tickets_in_progress',
                'hold'       => 'admin_tickets_on_hold',
                'completed'  => 'admin_completed_tickets',
                'closed'     => 'admin_closed_tickets',
                'total'      => 'admin_total_tickets',
            ];
            $docRoute  = 'forwarded_documents';
        }

        return view('frontend.unified.dashboard', compact(
            'docs_total', 'docs_pending', 'docs_completed', 'docs_rejected', 'docMonthly',
            'tkt_total', 'tkt_open', 'tkt_inprogress', 'tkt_hold', 'tkt_completed', 'tkt_closed',
            'tktMonthly', 'recentTickets',
            'post_total', 'post_pending', 'post_unread', 'post_forwarded',
            'tktRoutes', 'docRoute'
        ));
    }
}
