<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;


use App\Models\Ticket;
use App\Models\Ping;
use App\Models\Department;
use App\Models\TicketLog;
use App\Models\TicketConversation;
use App\Models\TicketForwarding;

use App\Models\Postal;

class StaffController extends Controller
{
    public function showStaffDashboard(){
        
        $activeMenu = "Dashboard";
        $activeDropdown = "";

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Open')
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Closed')
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Hold')
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'In Progress')
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Completed')
                                ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', Auth::id())
                            ->where('status', '!=', 'Closed')
                            ->latest()
                            ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];
        
        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

        }

        
        $pings = Ping::join('tickets', function($join) {
                $join->on('pings.task_id', '=', 'tickets.id')
                     ->where(function($query) {
                         $query->where('pings.ping_to', '=', Auth::id())
                               ->where('tickets.status', '!=', 'closed'); 
                     })
                     ->where(function($query) {
                         $query->where('tickets.assigned_to', '=', Auth::id())
                               ->orWhere('tickets.ticket_by', '=', Auth::id());
                     });
            })
            ->select('pings.*')
            ->get();

        $total_pings = 0;

        foreach($pings as $ping){
            $total_pings = $ping->ping_count + $total_pings;
        }

        $pings = Ping::join('tickets', function($join) {
                $join->on('pings.task_id', '=', 'tickets.id')
                     ->where(function($query) {
                         $query->where('pings.ping_to', '=', Auth::id())
                               ->where('tickets.status', '!=', 'closed'); 
                     })
                     ->where(function($query) {
                         $query->where('tickets.assigned_to', '=', Auth::id())
                               ->orWhere('tickets.ticket_by', '=', Auth::id());
                     });
            })
            ->select('pings.*')
            ->orderBy('pings.ping_count', 'desc') // Order by ping_count in descending order
            ->limit(5) // Limit the result set to 5 records
            ->get();

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        $documents = DB::table('payment_processing')->where('assigned_to', Auth::id())->paginate(10);
        
        return view('frontend.staff.dashboard', compact('activeMenu', 'activeDropdown', 'open_tickets', 'closed_tickets', 'completed_tickets', 'tickets_in_progress', 'tickets_on_hold', 'total_tickets', 'tickets', 'pings', 'total_pings', 'departmentData', 'ticketData', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));
    }

    public function paymentInProgressDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "inprogress_doc";

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        $documents = DB::table('payment_processing')->where('assigned_to', Auth::id())->where('status', ['In Progress', 'Payment Done'])->paginate(10);

        return view('frontend.staff.document.payments_in_progress', compact('activeMenu', 'activeDropdown', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));
    }

    public function paymentNewDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "inprogress_doc";

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        $documents = DB::table('payment_processing')->where('assigned_to', Auth::id())->where('status', 'Payment In Progress')->paginate(10);

        return view('frontend.staff.document.payments_new', compact('activeMenu', 'activeDropdown', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));
    }

    public function paymentHoldDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "hold_doc";

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        $documents = DB::table('payment_processing')->where('assigned_to', Auth::id())->where('status', 'Hold')->paginate(10);

        return view('frontend.staff.document.payments_hold', compact('activeMenu', 'activeDropdown', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));
    }

    public function paymentCompletedDocuments(Request $request)
    {
        $activeMenu = "document_received";
        $activeDropdown = "completed_doc";

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        $documents = DB::table('payment_processing')->where('assigned_to', Auth::id())->where('status', 'Completed')->paginate(10);

        return view('frontend.staff.document.payments_completed', compact('activeMenu', 'activeDropdown', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));
    }

    public function advanceRequests(Request $request)
    {
        $activeMenu = "staff_dashboard";
        $activeDropdown = "advance_doc";

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // Filter to staff-assigned requests
        $documents = DB::table('payment_processing as pp')
            ->join('document_approvals as a', 'a.id', '=', 'pp.doc_id')
            ->where('pp.assigned_to', Auth::id())

            // Has partial payment(s)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details')
                ->whereRaw('payment_details.doc_id = pp.doc_id')
                ->where('payment_details.payment_type', 'Partial Payment');
            })

            // NO full payment yet
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details')
                ->whereRaw('payment_details.doc_id = pp.doc_id')
                ->where('payment_details.payment_type', 'Full Payment');
            })

            ->select('pp.*', 'a.title', 'a.priority', 'a.from', 'a.doc_id as document_id')
            ->distinct()
            ->orderBy('pp.created_at', $sortDir)
            ->paginate(10);

        return view('frontend.staff.document.payments_advance', compact('activeMenu', 'activeDropdown', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));

    }

    public function fullRequests(Request $request)
    {
        $activeMenu = "staff_dashboard";
        $activeDropdown = "full_doc";

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $total_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->count();
        
        $new_reqs = DB::table('payment_processing')
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Payment In Progress')
                        ->count();

        $inprogress_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->whereIn('status', ['In Progress', 'Payment Done'])
                            ->count();

        $hold_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Hold')
                            ->count();

        $completed_reqs = DB::table('payment_processing')
                            ->where('assigned_to', Auth::id())
                            ->where('status', 'Completed')
                            ->count();

        // ----------------- NEW: Advance (partial-only) -----------------
        // approvals assigned to this user which have Partial Payment(s) and NO Full Payment
        $advance_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Partial Payment');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd2')
                ->whereRaw('pd2.doc_id = pp.doc_id')
                ->where('pd2.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // ----------------- NEW: Full payments -----------------
        // approvals assigned to this user which have at least one Full Payment
        $full_reqs = DB::table('payment_processing as pp')
            ->where('pp.assigned_to', Auth::id())
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details as pd')
                ->whereRaw('pd.doc_id = pp.doc_id')
                ->where('pd.payment_type', 'Full Payment');
            })
            ->distinct()
            ->count('pp.doc_id');

        // Filter requests assigned to this staff member
        $documents = DB::table('payment_processing as pp')
            ->join('document_approvals as a', 'a.id', '=', 'pp.doc_id')
            ->where('pp.assigned_to', Auth::id())

            // Has Full Payment
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                ->from('payment_details')
                ->whereRaw('payment_details.doc_id = pp.doc_id')
                ->where('payment_details.payment_type', 'Full Payment');
            })

            ->select('pp.*', 'a.title', 'a.priority', 'a.from', 'a.doc_id as document_id')
            ->distinct()
            ->orderBy('pp.created_at', $sortDir)
            ->paginate(10);

        return view('frontend.staff.document.payments_full', compact('activeMenu', 'activeDropdown', 'documents', 'inprogress_reqs', 'completed_reqs', 'total_reqs', 'new_reqs', 'hold_reqs', 'advance_reqs', 'full_reqs'));

    }

    public function staffOpenTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Open')
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "open_tickets";

        return view('frontend.staff.ticket.open-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffCompletedTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'Completed')
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "completed_tickets";

        return view('frontend.staff.ticket.completed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffTicketsInProgress(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('assigned_to', Auth::id())
                        ->where('status', 'In Progress')
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "tickets_in_progress";

        return view('frontend.staff.ticket.in-progress-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffViewTicket($ticketId){

        $ticket = Ticket::find($ticketId);

        $activeMenu = "";

        if($ticket){

            if($ticket->assigned_to == Auth::id()){

                $activeMenu = "tickets_received";
                $activeDropdown = "open_tickets"; 

                $log = TicketLog::where('ticket_id', '=', $ticket->id)->get();
                $conversation = TicketConversation::where('ticket_id', '=', $ticket->id)->get();

                return view('frontend.staff.ticket.view-ticket', compact('ticket', 'activeMenu', 'activeDropdown', 'log', 'conversation'));
            
            }elseif($ticket->ticket_by == Auth::id()){

                $activeMenu = "tickets";
                $activeDropdown = "my_tickets";
                
                $log = TicketLog::where('ticket_id', '=', $ticket->id)->get();
                $conversation = TicketConversation::where('ticket_id', '=', $ticket->id)->get();

                return view('frontend.staff.ticket.view-ticket', compact('ticket', 'activeMenu', 'activeDropdown', 'log', 'conversation'));

            }elseif($ticket->is_forwarded){

                $flag = TicketForwarding::where('ticket_id', $ticketId)
                                            ->where('assigned_to', Auth::id())
                                            ->first();

                if($flag){
                    
                    $activeMenu = "forwarded_tickets";
                    $activeDropdown = "";
                    
                    $log = TicketLog::where('ticket_id', '=', $ticket->id)->get();
                    $conversation = TicketConversation::where('ticket_id', '=', $ticket->id)->get();

                    return view('frontend.staff.ticket.view-ticket', compact('ticket', 'activeMenu', 'activeDropdown', 'log', 'conversation'));
                
                }else{

                    $alert = array(
                        'message' => 'Access Denied, Ticket not assigned to you',
                        'alert-type' => 'error'
                    );
                    return redirect()->back()->with($alert);
                }

            }
                
        }else{

            $alert = array(
                'message' => 'Ticket Not Found, Might be deleted by the owner',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);
        }

    }

    public function staffHoldTickets(){

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', '=', Auth::id())
                            ->where('status', 'Hold')
                            ->latest()
                            ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "hold_tickets";

        return view('frontend.staff.ticket.hold-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffClosedTickets(){

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', '=', Auth::id())
                            ->where('status', 'Closed')
                            ->latest()
                            ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "closed_tickets";

        return view('frontend.staff.ticket.closed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffTotalTickets(){

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', '=', Auth::id())
                            ->latest()
                            ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "";

        return view('frontend.staff.ticket.total-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function createTicket(){
        
        $activeMenu = "tickets";
        $activeDropdown = "create_ticket";

        $departments = Department::all();
        
        return view('frontend.staff.ticket.create-ticket', compact('activeMenu', 'activeDropdown', 'departments'));
    }

    public function staffMyTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_by', Auth::id())
                        ->orderByRaw("CASE status 
                                        WHEN 'Open' THEN 1 
                                        WHEN 'Hold' THEN 2 
                                        WHEN 'In Progress' THEN 3 
                                        WHEN 'Completed' THEN 4 
                                        WHEN 'Closed' THEN 5 
                                      END, created_at DESC")
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "my_tickets";

        return view('frontend.staff.ticket.my-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffUnapprovedTickets(){

        $tickets = Ticket::where('ticket_by', Auth::id())
                        ->where('is_approved', false)
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "unapproved_tickets";

        return view('frontend.staff.ticket.unapproved-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function staffForwardedTickets(){

        $forwards = TicketForwarding::join('tickets', 'ticket_forwardings.ticket_id', '=', 'tickets.id')
                                    ->where('ticket_forwardings.assigned_to', Auth::id())
                                    ->select('ticket_forwardings.*', 'tickets.status', 'tickets.created_at')
                                    ->orderByRaw("CASE tickets.status 
                                                    WHEN 'Open' THEN 1 
                                                    WHEN 'Hold' THEN 2 
                                                    WHEN 'In Progress' THEN 3 
                                                    WHEN 'Completed' THEN 4 
                                                    WHEN 'Closed' THEN 5 
                                                  END")
                                    ->orderBy('tickets.created_at', 'DESC')
                                    ->get();

        $activeMenu = "forwarded_tickets";
        $activeDropdown = "";

        return view('frontend.staff.ticket.forwarded-tickets', compact('forwards', 'activeMenu', 'activeDropdown'));
    }

    public function staffTicketsReport(){

        $activeMenu = "reports";
        $activeDropdown = "";

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Open')
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Closed')
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                    ->where('assigned_to', Auth::id())
                                    ->where('status', 'Completed')
                                    ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                    ->where('assigned_to', Auth::id())
                                    ->where('status', 'Hold')
                                    ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                        ->where('assigned_to', Auth::id())
                                        ->where('status', 'In Progress')
                                        ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', Auth::id())
                            ->where('status', '!=', 'Closed')
                            ->latest()
                            ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->latest()
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->where('status', '!=', 'Closed')
                                        ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            if($raisedTicketCount != Null){
                $ticketData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingRaisedTicketCount,
                    'count' => $raisedTicketCount
                ];
            }
        }

        return view('frontend.staff.ticket.report.index', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'raised_tickets'));

    }

    public function staffReportDownload(Request $request){

        $departmentsChartImg = $request->input('departments_chart_img');
        $statusChartImg = $request->input('status_chart_img');
        $ticketRaisedImg = $request->input('ticket_raised_img');
        
        $from = "2024-05-01";
        $to = date('Y-m-d');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Open')
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Closed')
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                    ->where('assigned_to', Auth::id())
                                    ->where('status', 'Completed')
                                    ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Hold')
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('assigned_to', Auth::id())
                                    ->where('status', 'In Progress')
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', Auth::id())
                            ->latest()
                            ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->latest()
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                     ->where('ticket_from', $department->dept_label)
                                     ->where('assigned_to', Auth::id())
                                     ->where('status', '!=', 'Closed')
                                     ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->where('status', '!=', 'Closed')
                                        ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            if($raisedTicketCount != Null){
                $ticketData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingRaisedTicketCount,
                    'count' => $raisedTicketCount
                ];
            }
        }

        // Create the PDF instance with custom options
        $pdf = PDF::loadView('frontend.staff.ticket.report.pdf.template', compact('departmentData', 'ticketData', 'departmentsChartImg', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'ticketRaisedImg', 'raised_tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    public function staffSpecificTicketsReport(Request $request){

        $activeMenu = "reports";
        $activeDropdown = "";

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('assigned_to', Auth::id())
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', Auth::id())
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->latest()
                            ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->latest()
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->where('status', '!=', 'Closed')
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->where('status', '!=', 'Closed')
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            if($raisedTicketCount != Null){
                $ticketData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingRaisedTicketCount,
                    'count' => $raisedTicketCount
                ];
            }
        }

        return view('frontend.staff.ticket.report.specific', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'raised_tickets', 'from', 'to'));

    }

    public function staffSpecificReportDownload(Request $request){
        
        $departmentsChartImg = $request->input('departments_chart_img');
        $statusChartImg = $request->input('status_chart_img');
        $ticketRaisedImg = $request->input('ticket_raised_img');
        $logo = $request->input('logo');

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('assigned_to', Auth::id())
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('assigned_to', Auth::id())
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('assigned_to', Auth::id())
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->latest()
                            ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_by', Auth::id())
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('assigned_to', Auth::id())
                                 ->where('status', '!=', 'Closed')
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->where('ticket_to', $department->dept_label)
                                        ->where('status', '!=', 'Closed')
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            if($raisedTicketCount != Null){
                $ticketData[] = [
                    'name' => $department->dept_label,
                    'pending' => $pendingRaisedTicketCount,
                    'count' => $raisedTicketCount
                ];
            }
        }

        // Create the PDF instance with custom options
        $pdf = PDF::loadView('frontend.staff.ticket.report.pdf.template', compact('departmentData', 'ticketData','departmentsChartImg', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'ticketRaisedImg', 'raised_tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    public function personalPosts(){
        
        $activeMenu = "postal";
        $activeDropdown = "personal_posts";
        
        $posts = Postal::where('staff_name', Auth::id())->get();
        
        return view('frontend.admin.postal.received', compact('activeMenu', 'activeDropdown', 'posts'));
    }
}
