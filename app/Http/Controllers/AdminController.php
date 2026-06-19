<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Ping;
use App\Models\Department;
use App\Models\TicketLog;
use App\Models\TicketConversation;
use App\Models\TicketForwarding;
use App\Models\Postal;
use App\Models\PostalForwarding;
use App\Models\ReplyPost;
use App\Models\DocumentApproval;
use App\Models\DocumentApprovalForwardings;

class AdminController extends Controller
{
    public function Dashboard() {

        $total_tickets = Ticket::where('is_approved', True)->count();
        $pending_tickets = Ticket::where('is_approved', True)->where('forwarded_to', Auth::user()->department)->where('status', '!=', 'Closed')->count();
        $new_tickets = Ticket::where('is_approved', True)->where('assigned_to', '=', Null)->count();
        $closed_tickets = Ticket::where('is_approved', True)->where('status', '=', 'Closed')->count();
        
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
        
        if(Auth::user()->department == 'Medical Director'){
        
            $total_docs = DocumentApproval::where('status', '!=', 'Draft')->count();
                    
            $closed_docs = DocumentApproval::where('to', Auth::user()->department)->where('status', 'Closed')->count();
                    
            $inProgress_docs = DocumentApproval::where('to', Auth::user()->department)->whereNotIn('status', ['Closed', 'Draft'])->count();
        
            
        }elseif(Auth::user()->department == 'Pro-VC'){
            
            $total_docs = DocumentApproval::where(function($query) {
                        $query->where('to', Auth::user()->department)
                              ->orWhere('to', 'Registrar'); 
                    })->where('status', '!=', 'Draft')->count();
                    
            $closed_docs = DocumentApproval::where(function($query) {
                        $query->where('to', Auth::user()->department)
                              ->orWhere('to', 'Registrar'); 
                    })->where('status', 'Closed')->count();
            
            $inProgress_docs = DocumentApproval::where(function($query) {
                        $query->where('to', Auth::user()->department)
                              ->orWhere('to', 'Registrar'); 
                    })->whereNotIn('status', ['Closed', 'Draft'])->count();
            
        }else{
            
            $total_docs = DocumentApproval::where('status', '!=', 'Draft')->where('to', Auth::user()->department)->count();
            $closed_docs = DocumentApproval::where('status', 'Closed')->where('to', Auth::user()->department)->count();
            $inProgress_docs = DocumentApproval::whereNotIn('status', ['Draft', 'Closed'])->where('to', Auth::user()->department)->count();
            
            
        }
        
        $approved_docs = DocumentApproval::wherein('to', ['VC', 'Registrar'])->whereIn('approval_status', ['Approved by VC', 'Noted by VC', 'VC Discussion for Discussion', 'VC Approved in Principle'])->count();
         
        $forwarded_to_you = DocumentApprovalForwardings::where('forwarded_to', Auth::user()->department)
                            ->whereHas('document', function ($query) {
                                $query->whereNotIn('status', ['Closed', 'Completed']);
                            })
                            ->count();
        
        $pending_docs = DocumentApproval::whereNotIn('status', ['Draft', 'Closed'])
                                            ->where(function ($query) {
                                                $query->where('forwarded_to', Auth::user()->department)
                                                    ->orWhere('to', Auth::user()->department);
                                            })
                                            ->count();
        
        $new_docs = DocumentApproval::where('status', 'Sent to Registrar')->where('to', '!=', 'VC')->count();
        
        if(Auth::user()->department == 'VC'){
            $new_docs = DocumentApproval::where('status', 'Sent to Registrar')->count();
        }
        
        $activeMenu = "dashboard";
        $activeDropdown = "";

        $highPriorityTickets = Ticket::select('id as id', 'ticket_id as task_id', 'title as task_title', 'status as task_status', 'created_at as task_due', \DB::raw('"ticket" as task_type'))
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

        return view('frontend.superadmin.dashboard', compact('activeMenu', 'activeDropdown', 'total_tickets', 'pending_tickets', 'closed_tickets', 'new_tickets', 'highPriorityTasks', 'total_pings', 'pings', 'total_posts', 'pending_posts', 'new_posts', 'total_docs', 'pending_docs', 'new_docs', 'forwarded', 'closed_docs', 'inProgress_docs', 'forwarded_to_you', 'approved_docs'));
    }

    public function superAdminTicketsSummary(){
        
        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_summary";
        
        $open_tickets = Ticket::where('status', 'Open')->where('is_approved', True)->count();
        $closed_tickets = Ticket::where('status', 'Closed')->where('is_approved', True)->count();
        $completed_tickets = Ticket::where('status', 'Completed')->where('is_approved', True)->count();
        $total_tickets = Ticket::where('is_approved', True)->count();
        $tickets_on_hold = Ticket::where('status', 'Hold')->where('is_approved', True)->count();
        $tickets_in_progress = Ticket::where('status', 'In Progress')->where('is_approved', True)->count();

        $tickets = Ticket::where('status', '!=', 'Closed')->where('is_approved', True)->get();

        $assignedTickets = Ticket::where('is_approved', True)
                                    ->where(function ($query) {
                                        $query->where('status', '!=', 'Closed')
                                            ->where('status', '!=', 'Hold');
                                        })
                                    ->count();

        $departments = Department::all();

        $departmentData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('ticket_from', $department->dept_label)
                                    ->where('is_approved', True)
                                    ->count();

            $raisedTicketCount = Ticket::where('ticket_to', $department->dept_label)
                                        ->where('is_approved', True)
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
        
        return view('frontend.superadmin.ticket.sa.summary', compact('activeMenu', 'activeDropdown', 'open_tickets', 'closed_tickets', 'completed_tickets', 'tickets_on_hold', 'total_tickets', 'tickets', 'departmentData', 'ticketData', 'tickets_in_progress', 'assignedTickets'));
    }

    public function superAdminOpenTickets(){

        $tickets = Ticket::where('is_approved', True)
                            ->where('status', 'Open')
                            ->latest()
                            ->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_summary";

        return view('frontend.superadmin.ticket.sa.open-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminTicketsInProgress(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('status', 'In Progress')
                        ->latest()
                        ->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_in_progress_tickets";

        return view('frontend.superadmin.ticket.sa.in-progress-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminCompletedTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('status', 'Completed')
                        ->latest()
                        ->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_summary";

        return view('frontend.superadmin.ticket.sa.in-progress-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminClosedTickets(){

        $tickets = Ticket::where('is_approved', True)->where('status', 'Closed')->latest()->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_summary";

        return view('frontend.superadmin.ticket.sa.closed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminTicketsOnHold(){

        $tickets = Ticket::where('is_approved', True)->where('status', 'Hold')->latest()->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_summary";

        return view('frontend.superadmin.ticket.sa.hold-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminTotalTickets(){

        $tickets = Ticket::where('is_approved', True)->latest()->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_summary";

        return view('frontend.superadmin.ticket.sa.total-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminUnassignedTickets(){

        $tickets = Ticket::where('is_approved', True)->where('assigned_to', Null)->get();

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_unassigned_tickets";

        return view('frontend.superadmin.ticket.sa.unassigned-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminTicketsReport(){

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_reports";

        $open_tickets = Ticket::where('is_approved', True)->where('status', 'Open')->count();
        $completed_tickets = Ticket::where('is_approved', True)->where('status', 'Completed')->count();
        $closed_tickets = Ticket::where('is_approved', True)->where('status', 'Closed')->count();
        $total_tickets = Ticket::where('is_approved', True)->count();
        $tickets_on_hold = Ticket::where('is_approved', True)->where('status', 'Hold')->count();
        $tickets_in_progress = Ticket::where('is_approved', True)->where('status', 'In Progress')->count();

        $tickets = Ticket::where('is_approved', True)->latest()->get();

        $departments = Department::all();

        $ticketData = null;

        $departmentData = [];

        foreach ($departments as $fromDepartment) {

            $ticketCount = Ticket::where('is_approved', True)
                                    ->where('ticket_from', $fromDepartment->dept_label)
                                    ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_from', $fromDepartment->dept_label)
                                        ->where('status', '!=', 'Closed')
                                        ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $fromDepartment->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            foreach ($departments as $toDepartment) {
                // Fetch ticket counts from $fromDepartment to $toDepartment
                $ticketCount = Ticket::where('is_approved', True)
                                     ->where('ticket_from', $fromDepartment->dept_label)
                                     ->where('ticket_to', $toDepartment->dept_label)
                                     ->count();

                $pendingTicketCount = Ticket::where('is_approved', True)
                                            ->where('ticket_from', $fromDepartment->dept_label)
                                            ->where('ticket_to', $toDepartment->dept_label)
                                            ->where('status', '!=', 'Closed')
                                            ->count();

                if($ticketCount!= Null){
                    $reportData[] = [
                        'name' => $fromDepartment->dept_label,
                        'to' => $toDepartment->dept_label,
                        'pending' => $pendingTicketCount,
                        'count' => $ticketCount
                    ];
                }
            }
        }

        //dd($departmentData);

        return view('frontend.superadmin.ticket.sa.report.index', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'reportData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'departments'));

    }

    public function superAdminReportDownload(Request $request){

        $statusChartImg = $request->input('status_chart_img');
        
        $from = "2024-05-01";
        $to = date('Y-m-d');

        $open_tickets = Ticket::where('is_approved', True)->where('status', 'Open')->count();
        $closed_tickets = Ticket::where('is_approved', True)->where('status', 'Closed')->count();
        $completed_tickets = Ticket::where('is_approved', True)->where('status', 'Completed')->count();
        $total_tickets = Ticket::where('is_approved', True)->count();
        $tickets_on_hold = Ticket::where('is_approved', True)->where('status', 'Hold')->count();
        $tickets_in_progress = Ticket::where('is_approved', True)->where('status', 'In Progress')->count();

        $tickets = Ticket::where('is_approved', True)->latest()->get();

        $departments = Department::all();

        $departmentData = [];

        $ticketData = null;

        foreach ($departments as $fromDepartment) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                    ->where('ticket_from', $fromDepartment->dept_label)
                                    ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_from', $fromDepartment->dept_label)
                                        ->where('status', '!=', 'Closed')
                                        ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $fromDepartment->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            foreach ($departments as $toDepartment) {
                // Fetch ticket counts from $fromDepartment to $toDepartment
                $ticketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_from', $fromDepartment->dept_label)
                                        ->where('ticket_to', $toDepartment->dept_label)
                                        ->count();

                $pendingTicketCount = Ticket::where('is_approved', True)
                                            ->where('ticket_from', $fromDepartment->dept_label)
                                            ->where('ticket_to', $toDepartment->dept_label)
                                            ->where('status', '!=', 'Closed')
                                            ->count();

                if($ticketCount!= Null){
                    $reportData[] = [
                        'name' => $fromDepartment->dept_label,
                        'to' => $toDepartment->dept_label,
                        'pending' => $pendingTicketCount,
                        'count' => $ticketCount
                    ];
                }
            }
        }

        // Create the PDF instance with custom options
        $pdf = PDF::loadView('frontend.superadmin.ticket.sa.report.pdf.template', compact('departmentData', 'ticketData', 'departments', 'reportData', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    public function superAdminSpecificTicketsReport(Request $request){

        $activeMenu = "sa_tickets";
        $activeDropdown = "sa_reports";

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->latest()
                            ->get();

        $departments = Department::all();

        $departmentData = [];

        $ticketData = null;
        $reportData = null;

        foreach ($departments as $fromDepartment) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->where('ticket_from', $fromDepartment->dept_label)
                                ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->where('ticket_from', $fromDepartment->dept_label)
                                ->where('status', '!=', 'Closed')
                                ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $fromDepartment->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            foreach ($departments as $toDepartment) {
                // Fetch ticket counts from $fromDepartment to $toDepartment
                $ticketCount = Ticket::where('is_approved', True)
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->where('ticket_from', $fromDepartment->dept_label)
                                    ->where('ticket_to', $toDepartment->dept_label)
                                    ->count();

                $pendingTicketCount = Ticket::where('is_approved', True)
                                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                            ->where('ticket_from', $fromDepartment->dept_label)
                                            ->where('ticket_to', $toDepartment->dept_label)
                                            ->where('status', '!=', 'Closed')
                                            ->count();

                if($ticketCount!= Null){
                    $reportData[] = [
                        'name' => $fromDepartment->dept_label,
                        'to' => $toDepartment->dept_label,
                        'pending' => $pendingTicketCount,
                        'count' => $ticketCount
                    ];
                }
            }

        }

        return view('frontend.superadmin.ticket.sa.report.specific', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'reportData', 'departments', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'from', 'to'));

    }

    public function superAdminSpecificReportDownload(Request $request){

        $statusChartImg = $request->input('status_chart_img');

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                        ->latest()
                        ->get();

        $departments = Department::all();

        $departmentData = [];

        $ticketData = null;

        $reportData = null;

        foreach ($departments as $fromDepartment) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->where('ticket_from', $fromDepartment->dept_label)
                                ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->where('ticket_from', $fromDepartment->dept_label)
                                    ->where('status', '!=', 'Closed')
                                    ->count();
            
            if($ticketCount != Null){
                $departmentData[] = [
                    'name' => $fromDepartment->dept_label,
                    'pending' => $pendingTicketCount,
                    'count' => $ticketCount
                ];
            }

            foreach ($departments as $toDepartment) {
                // Fetch ticket counts from $fromDepartment to $toDepartment
                $ticketCount = Ticket::where('is_approved', True)
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->where('ticket_from', $fromDepartment->dept_label)
                                    ->where('ticket_to', $toDepartment->dept_label)
                                    ->count();

                $pendingTicketCount = Ticket::where('is_approved', True)
                                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                            ->where('ticket_from', $fromDepartment->dept_label)
                                            ->where('ticket_to', $toDepartment->dept_label)
                                            ->where('status', '!=', 'Closed')
                                            ->count();

                if($ticketCount!= Null){
                    $reportData[] = [
                        'name' => $fromDepartment->dept_label,
                        'to' => $toDepartment->dept_label,
                        'pending' => $pendingTicketCount,
                        'count' => $ticketCount
                    ];
                }
            }

        }

        // Create the PDF instance with custom options
        $pdf = PDF::loadView('frontend.superadmin.ticket.sa.report.pdf.template', compact('departmentData', 'ticketData', 'reportData', 'departments', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    public function recievedTicketsSummary(){
        
        $activeMenu = "tickets";
        $activeDropdown = "summary";
        
        $open_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Open')
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Closed')
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'Completed')
                                    ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Hold')
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'In Progress')
                                    ->count();

        $tickets = Ticket::where('is_approved', True)->where('ticket_to', Auth::user()->department)->where('status', '!=', 'Closed')->latest()->get();

        $assignedTickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
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
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
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
        
        return view('frontend.superadmin.ticket.summary', compact('activeMenu', 'activeDropdown', 'open_tickets', 'closed_tickets', 'completed_tickets', 'tickets_on_hold', 'total_tickets', 'tickets', 'departmentData', 'ticketData', 'tickets_in_progress', 'assignedTickets'));
    }
    
    public function adminRecievedOpenTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->where('status', 'Open')
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "open_tickets";

        return view('frontend.superadmin.ticket.open-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminRecievedTicketsInProgress(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->where('status', 'In Progress')
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "in_progress_tickets";

        return view('frontend.superadmin.ticket.in-progress-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function adminRecievedCompletedTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->where('status', 'Completed')
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "completed_tickets";

        return view('frontend.superadmin.ticket.completed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }
    
    public function adminRecievedUnassignedTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->where('assigned_to', Null)
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "unassigned_tickets";

        return view('frontend.superadmin.ticket.unassigned-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }
    
    public function adminRecievedSelfAssignedTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->where('assigned_to', Auth::id())
                        ->orderByRaw("CASE status 
                                        WHEN 'Open' THEN 1 
                                        WHEN 'Hold' THEN 2 
                                        WHEN 'In Progress' THEN 3 
                                        WHEN 'Completed' THEN 4 
                                        WHEN 'Closed' THEN 5 
                                      END, created_at DESC")
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "self_tickets";

        return view('frontend.superadmin.ticket.self-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }
    
    public function adminRecievedClosedTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('status', 'Closed')
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "summary";

        return view('frontend.superadmin.ticket.closed-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }
    
    public function adminRecievedTicketsOnHold(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('status', 'Hold')
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "summary";

        return view('frontend.superadmin.ticket.hold-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }
    
    public function adminRecievedTotalTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "summary";

        return view('frontend.superadmin.ticket.total-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }
    
    public function adminRecievedPendingTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('forwarded_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $activeMenu = "tickets";
        $activeDropdown = "summary";

        return view('frontend.superadmin.ticket.pending-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminCreateTicket(){
        
        $activeMenu = "tickets_raised";
        $activeDropdown = "create_ticket";

        $departments = Department::all();
        
        return view('frontend.superadmin.ticket.create-ticket', compact('activeMenu', 'activeDropdown', 'departments'));
    }
    
    public function superAdminMyTickets(){

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

        $activeMenu = "tickets_raised";
        $activeDropdown = "my_tickets";

        return view('frontend.superadmin.ticket.my-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminDeptTickets(){

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_from', Auth::user()->department)
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

        return view('frontend.superadmin.ticket.dept-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminForwardedTickets(){

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

        return view('frontend.superadmin.ticket.forwarded-tickets', compact('forwards', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminUnapprovedTickets(){

        $tickets = Ticket::where('ticket_from', Auth::user()->department)
                        ->where('is_approved', false)
                        ->get();

        $activeMenu = "tickets_raised";
        $activeDropdown = "unapproved_tickets";

        return view('frontend.superadmin.ticket.unapproved-tickets', compact('tickets', 'activeMenu', 'activeDropdown'));
    }

    public function superAdminDeptTicketsReport(){

        $activeMenu = "reports";
        $activeDropdown = "";

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Open')
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Closed')
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'Completed')
                                    ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Hold')
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'In Progress')
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->latest()
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
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

        return view('frontend.superadmin.ticket.report.index', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'raised_tickets'));

    }

    public function superAdminDeptTicketReportDownload(Request $request){

        $departmentsChartImg = $request->input('departments_chart_img');
        $statusChartImg = $request->input('status_chart_img');
        $ticketRaisedImg = $request->input('ticket_raised_img');
        
        $from = "2024-05-01";
        $to = date('Y-m-d');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Open')
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Closed')
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'Completed')
                                    ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Hold')
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'In Progress')
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                        ->where('ticket_to', Auth::user()->department)
                        ->latest()
                        ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->latest()
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->where('ticket_from', Auth::user()->department)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
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
        $pdf = PDF::loadView('frontend.superadmin.ticket.report.pdf.template', compact('departmentData', 'ticketData', 'departmentsChartImg', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'ticketRaisedImg', 'raised_tickets'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Ticketing_report.pdf');
    }

    public function superAdminSpecificDeptTicketsReport(Request $request){

        $activeMenu = "reports";
        $activeDropdown = "";

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('ticket_to', Auth::user()->department)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->latest()
                            ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->where('ticket_from', Auth::user()->department)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
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

        return view('frontend.superadmin.ticket.report.specific', compact('activeMenu', 'activeDropdown', 'departmentData', 'ticketData', 'ticketCount', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'raised_tickets', 'from', 'to'));

    }

    public function superAdminSpecificDeptTicketReportDownload(Request $request){

        $departmentsChartImg = $request->input('departments_chart_img');
        $statusChartImg = $request->input('status_chart_img');
        $ticketRaisedImg = $request->input('ticket_raised_img');
        $logo = $request->input('logo');

        $from = $request->input('from');
        $to = $request->input('to');

        $open_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Open')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $closed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Closed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $completed_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Completed')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $total_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_on_hold = Ticket::where('is_approved', True)
                                ->where('ticket_to', Auth::user()->department)
                                ->where('status', 'Hold')
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->count();

        $tickets_in_progress = Ticket::where('is_approved', True)
                                    ->where('ticket_to', Auth::user()->department)
                                    ->where('status', 'In Progress')
                                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                    ->count();

        $tickets = Ticket::where('is_approved', True)
                            ->where('ticket_to', Auth::user()->department)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->latest()
                            ->get();

        $tickets_raised = Ticket::where('is_approved', True)
                            ->where('ticket_from', Auth::user()->department)
                            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                            ->count();

        $raised_tickets = Ticket::where('is_approved', True)
                                ->where('ticket_from', Auth::user()->department)
                                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                ->latest()
                                ->get();

        $departments = Department::all();

        $departmentData = [];
        $ticketData = [];

        foreach ($departments as $department) {
            
            // Fetch ticket counts for the current department
            $ticketCount = Ticket::where('is_approved', True)
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->count();

            $pendingTicketCount = Ticket::where('is_approved', True)
                                 ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                 ->where('ticket_from', $department->dept_label)
                                 ->where('ticket_to', Auth::user()->department)
                                 ->where('status', '!=', 'Closed')
                                 ->count();

            $raisedTicketCount = Ticket::where('is_approved', True)
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                                        ->where('ticket_from', Auth::user()->department)
                                        ->where('ticket_to', $department->dept_label)
                                        ->count();

            $pendingRaisedTicketCount = Ticket::where('is_approved', True)
                                        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
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
        $pdf = PDF::loadView('frontend.superadmin.ticket.report.pdf.template', compact('departmentData', 'ticketData','departmentsChartImg', 'statusChartImg', 'from', 'to', 'open_tickets', 'closed_tickets', 'completed_tickets', 'total_tickets', 'tickets_on_hold', 'tickets_in_progress', 'tickets', 'tickets_raised', 'ticketRaisedImg', 'raised_tickets'));

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

    public function superAdminHighPriorityTasks(){

        $activeMenu = "highPriorityTasks";
        $activeDropdown = "";

        $highPriorityTickets = Ticket::select('id as id', 'ticket_id as task_id', 'title as task_title', 'status as task_status', 'due_date as task_due', 'ticket_from as task_from', 'created_at as task_created_at', \DB::raw('"ticket" as task_type'))
                                ->where('is_approved', True)
                                ->where('priority', 'high')
                                ->where('status', '!=', 'Closed')
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

        return view('frontend.superadmin.highPriorityTasks', compact('activeMenu', 'activeDropdown', 'highPriorityTasksPaginated'));

    }

    public function addStaffs(){

        $activeMenu = "staffs";
        $activeDropdown = "add_staffs";

        return view('frontend.superadmin.staffs.add-staffs', compact('activeMenu', 'activeDropdown'));
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
 
        return redirect()->route('super-admin-view-staffs')->with($notification);
 
    }

    public function viewStaffs(){

        $activeMenu = "staffs";
        $activeDropdown = "view_staffs";

        $staffs = User::where('department', Auth::user()->department)->get();

        return view('frontend.superadmin.staffs.view-staffs', compact('activeMenu', 'activeDropdown', 'staffs'));
    }

    public function editStaff($id){

        $activeMenu = "staffs";
        $activeDropdown = "view_staffs";

        $emp = User::findOrFail($id);

        return view('frontend.superadmin.staffs.edit-staff', compact('activeMenu', 'activeDropdown', 'emp'));

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
 
        return redirect()->route('super-admin-view-staffs')->with($notification);

    }

    public function deleteStaff($id){

        $user = User::findOrFail($id);

        if ($user) {
            
            Ticket::where('assigned_to', $id)->update([
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

        return view('frontend.superadmin.staffs.ex-staffs', compact('activeMenu', 'activeDropdown', 'staffs'));

    }

    public function recoverStaff($id){

        $user = User::withTrashed()->find($id);
        $user->restore();

        User::findOrFail($id)->update(['is_active' => true]);

        $alert = array(
            'message' => 'Account Recovered!',
            'alert-type' => 'success'
        );

        return redirect()->route('super-admin-view-staffs')->with($alert);
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
        
        $posts = Postal::where(function($query) {
                            $query->where('staff_name', Null)
                                ->orWhere('sent_to', Auth::user()->department);
                            })
                        ->orWhere('forward_to', 'LIKE', '%' . Auth::user()->department . '%')
                        ->get();
        
        return view('frontend.superadmin.postal.received', compact('activeMenu', 'activeDropdown', 'posts'));
    }

    public function personalPosts(){
        
        $activeMenu = "postal";
        $activeDropdown = "personal_posts";
        
        $postals = ReplyPost::where('reply_by', Auth::id())->get();
        
        return view('frontend.superadmin.postal.myposts', compact('activeMenu', 'activeDropdown', 'postals'));
    }

}
