<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

use App\Models\Ticket;
use App\Models\Ping;
use App\Models\Department;
use App\Models\TicketLog;
use App\Models\TicketConversation;
use App\Models\TicketForwarding;
use App\Models\User;
use App\Models\Postal;
use App\Models\PostalForwarding;
use App\Models\ReplyPost;
use App\Models\DocumentApproval;
use App\Models\DocumentApprovalForwardings;

class HODController extends Controller
{
    public function Dashboard() {

        $total_tickets = Ticket::where('ticket_to', '=', Auth::user()->department)->where('is_approved', True)->count();
        $pending_tickets = Ticket::where('ticket_to', '=', Auth::user()->department)->where('is_approved', True)->where('status', '!=', 'Closed')->count();
        $new_tickets = Ticket::where('ticket_to', '=', Auth::user()->department)->where('is_approved', True)->where('assigned_to', '=', Null)->count();
        
        $forwarded_tickets = Ticket::where('forwarded_to', '=', Auth::user()->department)->where('is_approved', True)->where('status', '!=', 'Closed')->count();
        
        $total_posts = Postal::where('sent_to', Auth::user()->department)->whereNotIn('status', ['Received', 'Dispatched'])->count();
        $pending_posts = Postal::where('sent_to', Auth::user()->department)->whereNotIn('status', ['Closed', 'Received', 'Dispatched'])->count();
        $forwarded = PostalForwarding::where('forwarded_to', Auth::user()->department)->where('status', 'Collected')->count();
        
        $new_posts = DB::table('postals')
                        ->where('sent_to', Auth::user()->department)
                        ->whereNotNull('collected_by')
                        ->where('is_read', 0)
                        ->count();
        
        $new_posts1 = DB::table('postal_forwardings')
                        ->where('forwarded_to', Auth::user()->department)
                        ->where('status', 'Collected')
                        ->where('is_read', 0)
                        ->count();
                        
        $new_posts = $new_posts + $new_posts1;
        
        
        $total_docs = DocumentApproval::where('from', Auth::user()->department)->count();
        $pending_docs = DocumentApproval::where('from', Auth::user()->department)->where('status', '!=','Closed')->count();
        $new_docs = DocumentApproval::where('from', Auth::user()->department)->where('approval_status', Null)->count();
        $closed_docs = DocumentApproval::where('from', Auth::user()->department)->where('status', 'Closed')->count();
        
        $forwarded_to_you = DocumentApprovalForwardings::where('forwarded_to', Auth::user()->department)
                            ->whereHas('document', function ($query) {
                                $query->whereNotIn('status', ['Closed', 'Completed']);
                            })
                            ->count();
        
        if(Auth::user()->department == 'VC Office'){
            $pending_docs = DocumentApproval::where('forwarded_to', Auth::user()->department)->count();
        }
        
        $activeMenu = "dashboard";
        $activeDropdown = "";

        $highPriorityTickets = Ticket::select('id as id', 'ticket_id as task_id', 'title as task_title', 'status as task_status', 'due_date as task_due', \DB::raw('"ticket" as task_type'))
                                ->where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('priority', 'high')
                                ->where('status', '!=', 'Closed')
                                ->limit(2)
                                ->get();

        // $highPriorityPostals = Postal::select('postal_id as task_id', 'postal_title as task_title', 'status as task_status', 'due_date as task_due', \DB::raw('"postal" as task_type'))
        //                                 ->where('priority', 'high')
        //                                 ->get();

        // $highPriorityDocuments = Document::select('document_id as task_id', 'document_title as task_title', 'status as task_status', 'due_date as task_due', \DB::raw('"document" as task_type'))
        //                                     ->where('priority', 'high')
        //                                     ->get();

        //$highPriorityTasks = $highPriorityTickets->merge($highPriorityPostals)->merge($highPriorityDocuments);
        $highPriorityTasks = $highPriorityTickets;

        $pings = Ping::join('tickets', 'pings.task_id', '=', 'tickets.id')
            ->where(function($query) {
                $query->where('pings.ping_to', Auth::user()->department)
                      ->orWhere('pings.ping_to', Auth::id());
            })
            ->where('tickets.status', '!=', 'closed') // Add this line to exclude closed tickets
            ->orderByRaw('pings.ping_count DESC')
            ->get();

        $total_pings = 0;

        foreach($pings as $ping){
            $total_pings = $ping->ping_count + $total_pings;
        }

        $pings = Ping::join('tickets', 'pings.task_id', '=', 'tickets.id')
            ->where(function($query) {
                $query->where('pings.ping_to', Auth::user()->department)
                      ->orWhere('pings.ping_to', Auth::id());
            })
            ->where('tickets.status', '!=', 'closed')
            ->orderByRaw('pings.ping_count DESC')
            ->limit(5)
            ->get();

        return view('frontend.admin.dashboard', compact('activeMenu', 'activeDropdown', 'total_tickets', 'pending_tickets', 'new_tickets', 'forwarded_tickets', 'highPriorityTasks', 'total_pings', 'pings', 'total_posts', 'pending_posts', 'new_posts', 'total_docs', 'pending_docs', 'new_docs', 'forwarded', 'closed_docs', 'forwarded_to_you'));
    }

    public function ticketsSummary(){
        
        $activeMenu = "tickets_received";
        $activeDropdown = "summary";
        
        $open_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Open')->count();
        $closed_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Closed')->count();
        $completed_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Completed')->count();
        $total_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->count();
        $tickets_on_hold = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Hold')->count();
        $tickets_in_progress = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'In Progress')->count();

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                            ->where('is_approved', True)
                            ->where('status', '!=', 'Closed')
                            ->latest()
                            ->get();

        $assignedTickets = Ticket::where('ticket_to', Auth::user()->department)
                                    ->where('is_approved', True)
                                    ->where(function ($query) {
                                        $query->where('status', '!=', 'Closed')
                                            ->where('status', '!=', 'Hold');
                                    })
                                    ->count();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('ticket_from', $department->dept_label)
                                 ->where('is_approved', True)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $raisedTicketCount = Ticket::where('ticket_from', Auth::user()->department)
                                 ->where('is_approved', True)
                                 ->where('ticket_to', $department->dept_label)
                                 ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'count' => $ticketCount
                ];
            }

            if($raisedTicketCount != Null){
                $ticketData[] = [
                    'name' => $department->dept_label,
                    'count' => $raisedTicketCount
                ];
            }
        }
        
        return view('frontend.admin.ticket.summary', compact('activeMenu', 'activeDropdown', 'open_tickets', 'closed_tickets', 'completed_tickets', 'tickets_on_hold', 'total_tickets', 'tickets', 'departmentData', 'ticketData', 'tickets_in_progress', 'assignedTickets'));
    }

    public function createTicket(){
        
        $activeMenu = "tickets_raised";
        $activeDropdown = "create_ticket";

        $departments = Department::where('is_active', True)->get();
        
        return view('frontend.admin.ticket.create-ticket', compact('activeMenu', 'activeDropdown', 'departments'));
    }

    public function adminOpenTickets(){

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                    ->where('is_approved', True)
                    ->where('status', 'Open')
                    ->latest()
                    ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "open_tickets";

        return view('frontend.admin.ticket.open-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminTicketsInProgress(){

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                    ->where('is_approved', True)
                    ->where('status', 'In Progress')
                    ->latest()
                    ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "in_progress_tickets";

        return view('frontend.admin.ticket.in-progress-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));

    }

    public function adminRecievedCompletedTickets(){

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                    ->where('is_approved', True)
                    ->where('status', 'Completed')
                    ->latest()
                    ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "in_progress_tickets";

        return view('frontend.admin.ticket.completed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));

    }

    public function adminUnassignedTickets(){

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                            ->where('is_approved', True)
                            ->where('assigned_to', Null)
                            ->latest()
                            ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "unassigned_tickets";

        return view('frontend.admin.ticket.unassigned-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminSelfAssignedTickets(){

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                        ->where('is_approved', True)
                        ->where('assigned_to', Auth::id())
                        ->orderByRaw("CASE status 
                                        WHEN 'Open' THEN 1 
                                        WHEN 'Hold' THEN 2 
                                        WHEN 'In Progress' THEN 3 
                                        WHEN 'Completed' THEN 4 
                                        WHEN 'Closed' THEN 5 
                                      END, created_at DESC")
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "self_tickets";

        return view('frontend.admin.ticket.self-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminMyTickets(){

        $tickets = Ticket::where('ticket_by', Auth::id())
                        ->where('is_approved', True)
                        ->orderByRaw("CASE status 
                                        WHEN 'Open' THEN 1 
                                        WHEN 'Hold' THEN 2 
                                        WHEN 'In Progress' THEN 3 
                                        WHEN 'Completed' THEN 4 
                                        WHEN 'Closed' THEN 5 
                                      END, created_at DESC")
                        ->get();

        $activeMenu = "tickets_raised";
        $activeDropdown = "my_tickets";

        return view('frontend.admin.ticket.my-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminDeptTickets(){

        $tickets = Ticket::where('ticket_from', Auth::user()->department)
                        ->where('is_approved', True)
                        ->orderByRaw("CASE status 
                                        WHEN 'Open' THEN 1 
                                        WHEN 'Hold' THEN 2 
                                        WHEN 'In Progress' THEN 3 
                                        WHEN 'Completed' THEN 4 
                                        WHEN 'Closed' THEN 5 
                                      END, created_at DESC")
                        ->get();

        $activeMenu = "tickets_raised";
        $activeDropdown = "staff_tickets";

        return view('frontend.admin.ticket.dept-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminUnapprovedTickets(){

        $tickets = Ticket::where('ticket_from', Auth::user()->department)
                        ->where('is_approved', false)
                        ->latest()
                        ->get();

        $activeMenu = "tickets_raised";
        $activeDropdown = "unapproved_tickets";

        return view('frontend.admin.ticket.unapproved-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminClosedTickets(){

        $tickets = Ticket::where('status', 'Closed')
                        ->where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "summary";

        return view('frontend.admin.ticket.closed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminTicketsOnHold(){

        $tickets = Ticket::where('status', 'Hold')
                        ->where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "hold_tickets";

        return view('frontend.admin.ticket.hold-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminTicketsCompleted(){

        $tickets = Ticket::where('status', 'Completed')
                        ->where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "completed_tickets";

        return view('frontend.admin.ticket.completed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminTotalTickets(){

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                            ->where('is_approved', True)
                            ->latest()->get();

        $activeMenu = "tickets_received";
        $activeDropdown = "summary";

        return view('frontend.admin.ticket.total-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminForwardedTickets(){

        $forwards = TicketForwarding::join('tickets', 'ticket_forwardings.ticket_id', '=', 'tickets.id')
                                    ->where('ticket_forwardings.forwarded_to', Auth::user()->department)
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

        return view('frontend.admin.ticket.forwarded-tickets', compact('forwards', 'activeMenu', 'activeDropdown'));
    }

    public function adminTicketsReport(){

        $activeMenu = "reports";
        $activeDropdown = "";

        $open_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Open')->count();
        $closed_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Closed')->count();
        $completed_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Completed')->count();
        $total_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->count();
        $tickets_on_hold = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Hold')->count();
        $tickets_in_progress = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'In Progress')->count();

        $tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->latest()->get();

        $tickets_raised = Ticket::where('ticket_from', Auth::user()->department)->where('is_approved', True)->count();

        $raised_tickets = Ticket::where('ticket_from', Auth::user()->department)->where('is_approved', True)->latest()->get();

        $departments = Department::where('is_active', True)->get();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('ticket_from', $department->dept_label)
                                 ->where('is_approved', True)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::where('ticket_from', $department->dept_label)
                                 ->where('is_approved', True)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('ticket_from', Auth::user()->department)
                                        ->where('is_approved', True)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('ticket_from', Auth::user()->department)
                                        ->where('is_approved', True)
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

        return view('frontend.admin.ticket.report.index', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'raised_tickets'));

    }

    public function adminReportDownload(Request $request){

        $departmentsChartImg = $request->input('departments_chart_img');
        $statusChartImg = $request->input('status_chart_img');
        $ticketRaisedImg = $request->input('ticket_raised_img');
        
        $from = "2024-05-01";
        $to = date('Y-m-d');

        $open_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Open')->count();
        $closed_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Closed')->count();
        $completed_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Completed')->count();
        $total_tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->count();
        $tickets_on_hold = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'Hold')->count();
        $tickets_in_progress = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->where('status', 'In Progress')->count();

        $tickets = Ticket::where('ticket_to', Auth::user()->department)->where('is_approved', True)->latest()->get();

        $tickets_raised = Ticket::where('ticket_from', Auth::user()->department)->where('is_approved', True)->count();

        $raised_tickets = Ticket::where('ticket_from', Auth::user()->department)->where('is_approved', True)->latest()->get();

        $departments = Department::where('is_active', True)->get();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('ticket_from', $department->dept_label)
                                 ->where('is_approved', True)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::where('ticket_from', $department->dept_label)
                                 ->where('is_approved', True)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('ticket_from', Auth::user()->department)
                                        ->where('is_approved', True)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('ticket_from', Auth::user()->department)
                                        ->where('is_approved', True)
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
        $pdf = PDF::loadView('frontend.admin.ticket.report.pdf.template', compact('departmentData', 'ticketData', 'departmentsChartImg', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'ticketRaisedImg', 'raised_tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    public function adminTicketDownload($ticket_id){

        $ticket = Ticket::FindorFail($ticket_id);

        $conversation = TicketConversation::where('ticket_id', '=', $ticket->id)->get();

        // Create the PDF instance with custom options
        $pdf = PDF::loadView('frontend.admin.ticket.download.pdf', compact('ticket', 'conversation'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticket-'.$ticket_id.'.pdf');
    }

    public function adminSpecificTicketsReport(Request $request){

        $activeMenu = "reports";
        $activeDropdown = "";

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->get();

        $tickets_raised = Ticket::where('ticket_from', Auth::user()->department)
                                ->where('is_approved', True)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->count();

        $raised_tickets = Ticket::where('ticket_from', Auth::user()->department)
                                ->where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->get();

        $departments = Department::where('is_active', True)->get();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
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

        return view('frontend.admin.ticket.report.specific', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'raised_tickets', 'from', 'to'));

    }

    public function adminSpecificReportDownload(Request $request){

        $departmentsChartImg = $request->input('departments_chart_img');
        $statusChartImg = $request->input('status_chart_img');
        $ticketRaisedImg = $request->input('ticket_raised_img');
        $logo = $request->input('logo');

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('ticket_to', Auth::user()->department)
                                ->where('is_approved', True)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->get();

        $tickets_raised = Ticket::where('ticket_from', Auth::user()->department)
                                ->where('is_approved', True)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->count();

        $raised_tickets = Ticket::where('ticket_from', Auth::user()->department)
                                ->where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->get();

        $departments = Department::where('is_active', True)->get();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
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
        $pdf = PDF::loadView('frontend.admin.ticket.report.pdf.template', compact('departmentData', 'ticketData','departmentsChartImg', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'ticketRaisedImg', 'raised_tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : \Illuminate\Support\Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function adminHighPriorityTasks(){

        $activeMenu = "highPriorityTasks";
        $activeDropdown = "";

        $highPriorityTickets = Ticket::select('id as id', 'ticket_id as task_id', 'title as task_title', 'status as task_status', 'due_date as task_due', 'ticket_from as task_from', 'created_at as task_created_at', \DB::raw('"ticket" as task_type'))
                                ->where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('priority', 'high')
                                ->where('status', '!=', 'Closed')
                                ->latest()
                                ->get();

        // $highPriorityPostals = Postal::select('postal_id as task_id', 'postal_title as task_title', 'status as task_status', 'due_date as task_due', \DB::raw('"postal" as task_type'))
        //                                 ->where('priority', 'high')
        //                                 ->get();

        // $highPriorityDocuments = Document::select('document_id as task_id', 'document_title as task_title', 'status as task_status', 'due_date as task_due', \DB::raw('"document" as task_type'))
        //                                     ->where('priority', 'high')
        //                                     ->get();

        //$highPriorityTasks = $highPriorityTickets->merge($highPriorityPostals)->merge($highPriorityDocuments);
        $highPriorityTasks = $highPriorityTickets;

        $perPage = 10; // Number of items per page

        $currentPage = Paginator::resolveCurrentPage(); // Get the current page number
        $highPriorityTasksPaginated = $this->paginate($highPriorityTasks, $perPage, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('frontend.admin.highPriorityTasks', compact('activeMenu', 'activeDropdown', 'highPriorityTasksPaginated'));

    }

    public function addStaffs(){

        $activeMenu = "staffs";
        $activeDropdown = "add_staffs";

        return view('frontend.admin.staffs.add-staffs', compact('activeMenu', 'activeDropdown'));
    }

    public function storeStaffs(Request $request){
        
        $messages = [
            'phone.unique' => 'The phone number has already been taken.',
            'email.unique' => 'The email address has already been taken.',
            'user_name.unique' => 'The username has already been taken.',
            'emp_id.unique' => 'The employee ID has already been taken.',
        ];

        $request->validate([
            'full_name' => 'required',
            'phone' => 'required|unique:users,phone',
            'email' => 'required|unique:users,email',
            'user_name' => 'required|unique:users,username',
            'department' => 'required',
            'role' => 'required',
            'emp_id' => 'required|unique:users,emp_id',
            'designation' => 'required',
        ], $messages);

        $password = 12345678;

        User::create([
            'name' => $request->full_name,
            'username' => $request->user_name,
            'department' => $request->department,
            'role' => $request->role,
            'emp_id' => $request->emp_id,
            'designation' => $request->designation,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($password),
            'created_at' => now()
        ]);

        $notification = array(
            'message' => 'Staff added',
            'alert-type' => 'success'
        );
 
        return redirect()->route('view-staffs')->with($notification);
 
    }

    public function viewStaffs(){

        $activeMenu = "staffs";
        $activeDropdown = "view_staffs";

        $staffs = User::where('department', Auth::user()->department)->get();

        return view('frontend.admin.staffs.view-staffs', compact('activeMenu', 'activeDropdown', 'staffs'));
    }

    public function editStaff($id){

        $activeMenu = "staffs";
        $activeDropdown = "view_staffs";

        $emp = User::findOrFail($id);

        return view('frontend.admin.staffs.edit-staff', compact('activeMenu', 'activeDropdown', 'emp'));

    }

    public function updateStaff(Request $request){

        $messages = [
            'phone.unique' => 'The phone number has already been taken.',
            'email.unique' => 'The email address has already been taken.',
            'user_name.unique' => 'The username has already been taken.',
            'emp_id.unique' => 'The employee ID has already been taken.',
        ];

        $user = User::findOrFail($request->id);

        $validatedData = $request->validate([
            'full_name' => 'required',
            'phone' => [
                'required',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'user_name' => [
                'required',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'department' => 'required',
            'role' => 'required',
            'emp_id' => [
                'required',
                Rule::unique('users', 'emp_id')->ignore($user->id),
            ],
            'designation' => 'required',
        ], $messages);

        $user->update([
            'name' => $request->full_name,
            'username' => $request->user_name,
            'department' => $request->department,
            'role' => $request->role,
            'emp_id' => $request->emp_id,
            'designation' => $request->designation,
            'phone' => $request->phone,
            'email' => $request->email,
            'updated_at' => now()
        ]);

        $notification = array(
            'message' => 'Staff Updated!',
            'alert-type' => 'success'
        );
 
        return redirect()->route('view-staffs')->with($notification);

    }

    public function deleteStaff($id){

        $user = User::findOrFail($id);

        if ($user) {
            
            Ticket::where('assigned_to', $id)
                    ->where('status', '!=', 'closed') 
                    ->update([
                        'assigned_to' => null,
                        'assigned_by' => null,
                        'updated_at' => now()
                    ]);
            
            User::findOrFail($id)->update(['is_active' => false]);

            $user->delete();

            $alert = array(
                'message' => 'Staff Deleted',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);
            
        } else {
            
            $alert = array(
                'message' => 'Error! Try Later',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);

        }

    }

    public function exStaffs(){

        $activeMenu = "staffs";
        $activeDropdown = "ex_staffs";

        $staffs = User::onlyTrashed()->where('department', Auth::user()->department)->get();

        return view('frontend.admin.staffs.ex-staffs', compact('activeMenu', 'activeDropdown', 'staffs'));

    }

    public function recoverStaff($id){

        $user = User::withTrashed()->find($id);
        $user->restore();

        User::findOrFail($id)->update(['is_active' => true]);

        $alert = array(
            'message' => 'Account Recovered!',
            'alert-type' => 'success'
        );

        return redirect()->route('view-staffs')->with($alert);
    }

    public function changeAccountStatus($id){

        $user = User::findOrFail($id);

        if($user->is_active){

            User::findOrFail($id)->update(['is_active' => false]);

            $alert = array(
                'message' => 'Account Deactivated!',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);

        }else{
            
            User::findOrFail($id)->update(['is_active' => true]);

            $alert = array(
                'message' => 'Account Activated!',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);
        }

    }

    public function deptPosts(){
        
        $activeMenu = "postal";
        $activeDropdown = "dept_posts";

        $posts = Postal::where('sent_to', Auth::user()->department)
                    ->whereDate('created_at', now()->toDateString())
                    ->where(function($query) {
                        $query->where('status', 'Collected')
                              ->orWhere('status', 'Forwarded')
                              ->orWhere('status', 'Filed');                              
                    })
                    ->latest()
                    ->get();
                    
        $forwards = PostalForwarding::where('forwarded_to', Auth::user()->department)->whereDate('created_at', now()->toDateString())->where('is_read', 0)->get();
        
        return view('frontend.admin.postal.received', compact('activeMenu', 'activeDropdown', 'posts', 'forwards'));
    }

    public function datedDeptPosts(Request $request){
        $activeMenu = "postal";
        $activeDropdown = "dept_posts";
    
        $date = $request->input('date', now()->toDateString());
        $month = $request->input('month');
        $unread = $request->input('unread');
        $status = $request->input('status');
        $forwards = null;
    
        $query = Postal::where('sent_to', Auth::user()->department)
                   ->where(function ($query) {
                       $query->where('status', 'Collected')
                             ->orWhere('status', 'Forwarded')
                             ->orWhere('status', 'Filed');
                   });

        // Apply filters based on query parameters
        if ($month) {
            $query->whereMonth('created_at', '=', now()->month)
                ->whereYear('created_at', '=', now()->year);
        } elseif ($unread) {
            $query->where('is_read', 0);
            
            $forwards = PostalForwarding::where('forwarded_to', Auth::user()->department)->where('status', 'Collected')->where('is_read', 0)->get();
            
        } elseif($status){
            $query->where('status', '!=', 'Closed');
        
            
        }elseif($request->input('total')){
            $posts = Postal::where('sent_to', Auth::user()->department)->whereNotIn('status', ['Received', 'Dispatched'])->get();;
            return view('frontend.admin.postal.received', compact('activeMenu', 'activeDropdown', 'posts', 'forwards'));
        }
        
        else {
            $query->whereDate('created_at', $date);
        }

        $posts = $query->latest()->get();
        
        return view('frontend.admin.postal.received', compact('activeMenu', 'activeDropdown', 'posts', 'forwards'));
    }

    public function searchPosts(Request $request)
    {
        $activeMenu = "postal";
        $activeDropdown = "search_posts";

        // Check if any search parameters are provided
        if (!$request->filled('subject') && !$request->filled('sent_by') && !$request->filled('post_id') && !$request->filled('received_date') && !$request->filled('category') && !$request->filled('registrar_id') && !$request->filled('status')) {
            $posts = collect(); // Return an empty collection if no search parameters are provided
            return view('frontend.admin.postal.search', compact('activeMenu', 'activeDropdown', 'posts'));
        }

        $query = Postal::query();

        if ($request->filled('subject')) {
            $query->where('subject', 'LIKE', '%' . $request->subject . '%');
        }

        if ($request->filled('sent_by')) {
            $query->where('sent_by', 'LIKE', '%' . $request->sent_by . '%');
        }

        if ($request->filled('post_id')) {
            $query->where('id', $request->post_id);
        }

        if ($request->filled('received_date')) {
            $query->whereDate('created_at', $request->received_date);
        }

        if ($request->filled('category')) {
            $query->where('category', 'LIKE', '%'.$request->category.'%');
        }

        if ($request->filled('registrar_id')) {
            $query->where('registrar_id', $request->registrar_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by sent_to or forwarded_to matching the authenticated user's department
        $posts = $query->where(function ($query) {
            $query->where('sent_to', Auth::user()->department)
                  ->orWhereRaw("FIND_IN_SET(?, forward_to)", [Auth::user()->department]);
        })->latest()->get();

        return view('frontend.admin.postal.search', compact('activeMenu', 'activeDropdown', 'posts'));
    }

    public function forwardedPosts(){
        
        $activeMenu = "postal_forward";
        $activeDropdown = "forwarded_posts";

        $posts = PostalForwarding::Where('forwarded_to', Auth::user()->department)
                        ->Where('status', 'Collected')
                        //->orWhere('forward_to', 'LIKE', '%'.Auth::user()->department.'%')
                        ->latest()
                        ->get();

        //dd($posts);
        
        return view('frontend.admin.postal.forwarded', compact('activeMenu', 'activeDropdown', 'posts'));
    }
    
    public function datedForwardedPosts(Request $request)
    {
        $activeMenu = "postal_forward";
        $activeDropdown = "forwarded_posts";

        // Retrieve filter inputs
        $date = $request->input('date');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Base query for forwarded posts
        $query = PostalForwarding::where('forwarded_to', Auth::user()->department)
                                ->where('status', 'Collected');

        // Apply filters
        if ($date) {
            $query->whereDate('created_at', $date);
        } elseif ($month) {
            $query->whereMonth('created_at', '=', now()->month)
                ->whereYear('created_at', '=', now()->year);
        } elseif ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        $posts = $query->latest()->get();

        return view('frontend.admin.postal.forwarded', compact('activeMenu', 'activeDropdown', 'posts'));
    }

    public function dispatchedPosts(){
        
        $activeMenu = "postal";
        $activeDropdown = "dispatched_posts";
        
        $postals = Postal::Where('sent_to', Auth::user()->department)
                        ->Where('status', 'Dispatched')
                        ->latest()
                        ->get();
        
        return view('frontend.admin.postal.dispatched', compact('activeMenu', 'activeDropdown', 'postals'));
    }

    public function personalPosts(){
        
        $activeMenu = "postal";
        $activeDropdown = "personal_posts";
        
        $postals = ReplyPost::where('reply_by', Auth::id())->get();
        
        return view('frontend.admin.postal.myposts', compact('activeMenu', 'activeDropdown', 'postals'));
    }


}
