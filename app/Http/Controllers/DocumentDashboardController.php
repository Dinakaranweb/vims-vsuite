<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\DocumentApproval;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentDashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $viewType = $this->resolveViewType($user);
        $data     = $this->fetchData($user, $viewType);

        $sidebarType = match ($user->role) {
            'SuperAdmin', 'ITAdmin' => 'superadmin',
            'HOD'                   => 'admin',
            default                 => 'staff',
        };

        return view('frontend.document.dashboard', array_merge($data, [
            'viewType'    => $viewType,
            'sidebarType' => $sidebarType,
            'authUser'    => $user,
            'activeMenu'     => 'dashboard',
            'activeDropdown' => '',
        ]));
    }

    // ── Role detection ────────────────────────────────────────────────────────

    private function resolveViewType($user): string
    {
        $dept = $user->department ?? '';
        $role = $user->role ?? '';

        if ($role === 'ITAdmin')                      return 'itadmin';
        if ($dept === 'Chairman')                    return 'chairman';
        if (str_starts_with($dept, 'Finance Head'))  return 'finance';
        if (str_starts_with($dept, 'Purchase Head')) return 'purchase';
        if ($dept === 'STB Office')                  return 'stb';
        if ($dept === 'Medical Director')            return 'medical_director';
        if (str_contains($dept, 'General Manager'))  return 'general_manager';
        if ($role === 'HOD')                         return 'hod';
        return 'staff';
    }

    private function fetchData($user, string $viewType): array
    {
        return match ($viewType) {
            'itadmin'          => $this->itadminData(),
            'hod'              => $this->hodData($user->department),
            'medical_director',
            'general_manager'  => $this->approverData($user->department),
            'stb'              => $this->stbData($user->department),
            'chairman'         => $this->chairmanData(),
            'finance'          => $this->financeData($user->department),
            'purchase'         => $this->purchaseData($user->department),
            default            => $this->staffData($user->id),
        };
    }

    // ── ITAdmin: system-wide API + document overview ──────────────────────────

    private function itadminData(): array
    {
        $totalTokens   = ApiToken::count();
        $activeTokens  = ApiToken::where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();
        $expiringSoon  = ApiToken::whereBetween('expires_at', [now(), now()->addDays(7)])->count();
        $totalUsers    = User::where('is_active', 1)->count();
        $totalDocs     = DocumentApproval::where('status', '!=', 'Draft')->count();
        $pendingDocs   = DocumentApproval::whereNotIn('status', ['Draft', 'Completed', 'Closed', 'Rejected'])->count();

        $monthly = DocumentApproval::where('status', '!=', 'Draft')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = collect([
            'Active Tokens'  => $activeTokens,
            'Expiring Soon'  => $expiringSoon,
            'Expired'        => $totalTokens - $activeTokens,
            'Pending Docs'   => $pendingDocs,
        ]);

        $recentDocs = ApiToken::with('user')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($t) => (object) [
                'id'         => $t->id,
                'doc_id'     => $t->name,
                'title'      => 'Token for: ' . ($t->user->name ?? 'Unknown User'),
                'subject'    => $t->name,
                'from'       => $t->user->department ?? '—',
                'status'     => $t->isExpired() ? 'Expired' : 'Active',
                'priority'   => 'normal',
                'amount'     => null,
                'created_at' => $t->created_at,
            ]);

        $total = $totalTokens;

        return compact('totalTokens', 'activeTokens', 'expiringSoon', 'totalUsers', 'totalDocs', 'pendingDocs', 'total', 'monthly', 'breakdown', 'recentDocs');
    }

    // ── HOD: document creator view ────────────────────────────────────────────

    private function hodData(string $dept): array
    {
        $base = fn () => DocumentApproval::where('from', $dept);

        $total      = $base()->where('status', '!=', 'Draft')->count();
        $draft      = $base()->where('status', 'Draft')->count();
        $inProgress = $base()->whereNotIn('status', ['Draft', 'Completed', 'Closed', 'Rejected'])->count();
        $completed  = $base()->whereIn('status', ['Completed', 'Closed'])->count();
        $rejected   = $base()->where('status', 'Rejected')->count();

        $monthly = $base()
            ->where('status', '!=', 'Draft')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = $base()
            ->where('status', '!=', 'Draft')
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->status => (int) $r->cnt]);

        $recentDocs = $base()
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected', 'Draft'])
            ->latest()->limit(8)->get();

        return compact('total', 'draft', 'inProgress', 'completed', 'rejected', 'monthly', 'breakdown', 'recentDocs');
    }

    // ── Approver: Medical Director / General Manager ──────────────────────────

    private function approverData(string $dept): array
    {
        $pending  = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->count();

        $approved = DB::table('approval_log')
            ->where('status', 'like', "Approved by {$dept}%")
            ->distinct('doc_id')->count('doc_id');

        $rejected = DB::table('approval_log')
            ->where('status', 'like', "Rejected by {$dept}%")
            ->distinct('doc_id')->count('doc_id');

        $held = DB::table('approval_log')
            ->where('status', 'like', "Held by {$dept}%")
            ->distinct('doc_id')->count('doc_id');

        $total = $approved + $rejected + $held + $pending;

        $monthly = DB::table('approval_log')
            ->where(function ($q) use ($dept) {
                $q->where('status', 'like', "Approved by {$dept}%")
                  ->orWhere('status', 'like', "Noted by {$dept}%");
            })
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = collect([
            'Approved' => $approved,
            'Rejected' => $rejected,
            'On Hold'  => $held,
            'Pending'  => $pending,
        ]);

        $recentDocs = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->latest()->limit(8)->get();

        return compact('pending', 'approved', 'rejected', 'held', 'total', 'monthly', 'breakdown', 'recentDocs');
    }

    // ── STB Office ────────────────────────────────────────────────────────────

    private function stbData(string $dept): array
    {
        $pending = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->count();

        $acknowledged = DB::table('approval_log')
            ->where('status', 'like', "%{$dept}%")
            ->distinct('doc_id')->count('doc_id');

        $total = $pending + $acknowledged;

        $monthly = DB::table('approval_log')
            ->where('status', 'like', "%{$dept}%")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = collect([
            'Pending'      => $pending,
            'Acknowledged' => $acknowledged,
        ]);

        $recentDocs = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->latest()->limit(8)->get();

        return compact('pending', 'acknowledged', 'total', 'monthly', 'breakdown', 'recentDocs');
    }

    // ── Chairman ──────────────────────────────────────────────────────────────

    private function chairmanData(): array
    {
        $pending = DocumentApproval::where('forwarded_to', 'Chairman')
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->count();

        $approved = DB::table('approval_log')
            ->where('status', 'like', 'Approved by Chairman%')
            ->distinct('doc_id')->count('doc_id');

        $rejected = DB::table('approval_log')
            ->where('status', 'like', 'Rejected by Chairman%')
            ->distinct('doc_id')->count('doc_id');

        $totalSanctioned = DocumentApproval::whereNotNull('sanctioned_amount')->sum('sanctioned_amount');

        $total = $approved + $rejected + $pending;

        $monthly = DB::table('approval_log')
            ->where('status', 'like', 'Approved by Chairman%')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $monthlySanctioned = DocumentApproval::whereNotNull('sanctioned_amount')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, SUM(sanctioned_amount) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = collect([
            'Approved' => $approved,
            'Rejected' => $rejected,
            'Pending'  => $pending,
        ]);

        $recentDocs = DocumentApproval::where('forwarded_to', 'Chairman')
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->latest()->limit(8)->get();

        return compact('pending', 'approved', 'rejected', 'totalSanctioned', 'total', 'monthly', 'monthlySanctioned', 'breakdown', 'recentDocs');
    }

    // ── Finance Head ──────────────────────────────────────────────────────────

    private function financeData(string $dept): array
    {
        $pending = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->count();

        $docIds = DocumentApproval::where('forwarded_to', $dept)
            ->orWhere('to', $dept)
            ->pluck('id');

        $fullPayments    = DB::table('payment_details')->whereIn('doc_id', $docIds)->where('payment_type', 'Full Payment')->count();
        $partialPayments = DB::table('payment_details')->whereIn('doc_id', $docIds)->where('payment_type', 'Partial Payment')->count();
        $totalAmount     = DB::table('payment_details')->whereIn('doc_id', $docIds)->sum('paid_amount');

        $total = $fullPayments + $partialPayments + $pending;

        $monthly = DB::table('payment_details')
            ->whereIn('doc_id', $docIds)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, SUM(paid_amount) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = collect([
            'Pending'      => $pending,
            'Full Payment' => $fullPayments,
            'Advance'      => $partialPayments,
        ]);

        $recentDocs = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->latest()->limit(8)->get();

        return compact('pending', 'fullPayments', 'partialPayments', 'totalAmount', 'total', 'monthly', 'breakdown', 'recentDocs');
    }

    // ── Purchase Head ─────────────────────────────────────────────────────────

    private function purchaseData(string $dept): array
    {
        $pending = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->count();

        $approved = DB::table('approval_log')
            ->where('status', 'like', "Approved by {$dept}%")
            ->distinct('doc_id')->count('doc_id');

        $rejected = DB::table('approval_log')
            ->where('status', 'like', "Rejected by {$dept}%")
            ->distinct('doc_id')->count('doc_id');

        $total = $approved + $rejected + $pending;

        $monthly = DB::table('approval_log')
            ->where('status', 'like', "Approved by {$dept}%")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = collect([
            'Approved' => $approved,
            'Rejected' => $rejected,
            'Pending'  => $pending,
        ]);

        $recentDocs = DocumentApproval::where('forwarded_to', $dept)
            ->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])
            ->latest()->limit(8)->get();

        return compact('pending', 'approved', 'rejected', 'total', 'monthly', 'breakdown', 'recentDocs');
    }

    // ── Staff / User ──────────────────────────────────────────────────────────

    private function staffData(int $userId): array
    {
        $base = fn () => DocumentApproval::where('by', $userId)->where('status', '!=', 'Draft');

        $total      = $base()->count();
        $draft      = DocumentApproval::where('by', $userId)->where('status', 'Draft')->count();
        $inProgress = $base()->whereNotIn('status', ['Completed', 'Closed', 'Rejected'])->count();
        $completed  = $base()->whereIn('status', ['Completed', 'Closed'])->count();
        $rejected   = $base()->where('status', 'Rejected')->count();

        $monthly = $base()
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%b') as month, DATE_FORMAT(created_at,'%Y-%m') as skey, COUNT(*) as value")
            ->groupBy('month', 'skey')
            ->orderBy('skey')
            ->get();

        $breakdown = $base()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->status => (int) $r->cnt]);

        $recentDocs = $base()->latest()->limit(8)->get();

        return compact('total', 'draft', 'inProgress', 'completed', 'rejected', 'monthly', 'breakdown', 'recentDocs');
    }
}
