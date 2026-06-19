<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\Department;
use App\Models\TicketConversation;

use App\Http\Controllers\NotificationController;

class TicketController extends Controller
{

    public function storeTicket(Request $request){

        $title = $request->input('title');
        $ticket_to = $request->input('ticket_to');
        $description = $request->input('description');
        $priority = $request->input('priority');
        $due_date = $request->input('due_date');

        $user = Auth::user();

        $ticket_id = "TK-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $ticket_from = $user->department;
        $assigned_to = Null;
        $ticket_by = $user->id;
        $status = "Open";

        if ($request->file('file')) {
            // Determine the folder structure based on the current year and month
            $year = date('Y');
            $month = date('m');
            $folderPath = "uploads/{$year}/{$month}";

            // Generate a unique name for the file and ensure it is unique
            do {
                $uniqueName = uniqid() . '.' . $request->file('file')->getClientOriginalExtension();
                $filePath = "{$folderPath}/{$uniqueName}";
            } while (Storage::disk('public')->exists($filePath));

            // Store the file
            $request->file('file')->storeAs($folderPath, $uniqueName, 'public');
        } else {
            $filePath = null;
        }

        DB::table('tickets')->insert([
            'ticket_id' => $ticket_id,
            'title' => $title,
            'ticket_to' => $ticket_to,
            'assigned_to' => $assigned_to,
            'ticket_from' => $ticket_from,
            'ticket_by' => $ticket_by,
            'description' => $description,
            'file' => $filePath,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $due_date,
            'is_approved' => True,
            'created_at' => now()
        ]);

        $lastInsertedTicket = Ticket::latest()->first();

        $createdAt = Carbon::parse($lastInsertedTicket->created_at);

        $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

        if(User::find($lastInsertedTicket->ticket_by)->role == 'SuperAdmin'){

            $created_by = User::find($lastInsertedTicket->ticket_by)->department;

        }else{

            $created_by = User::find($lastInsertedTicket->ticket_by)->name.', '.User::find($lastInsertedTicket->ticket_by)->designation;
        }

        $log_description = "Ticket created by <b>" .$created_by. "</b> at ".$formattedCreatedAt;

        DB::table('ticket_logs')->insert([
            'ticket_id' => $lastInsertedTicket->id,
            'title' => "Created",
            'description' => $log_description,
            'created_at' => now()
        ]);

        /*Notification to self*/

        $to = Auth::id();
        $task_id = $lastInsertedTicket->id;
        
        $notificationController = new notificationController();
        $notificationController->notificationEntry($to, 'ticket', $task_id, 'Your Ticket - <b>'.$title.'</b> has been created');

        /*Notification to Head*/

        if(Auth::user()->role == 'Staff'){

            $notificationController = new notificationController();
            $notificationController->notificationEntry($lastInsertedTicket->ticket_from, 'ticket', $task_id, 'Ticket - <b>'.$title.'</b> has been created by <b>'.User::find($lastInsertedTicket->ticket_by)->name.'</b> to <b>'.$lastInsertedTicket->ticket_to.'</b>');

        }

        $notificationController = new notificationController();
        $notificationController->notificationEntry($ticket_to, 'ticket', $task_id, 'Ticket - <b>'.$title.'</b> from <b>'.$ticket_from.'</b> Received');

        $alert = array(
            'message' => 'Ticket Created',
            'alert-type' => 'success'
        );

        if(User::findOrFail($lastInsertedTicket->ticket_by)->role == 'Staff'){
            
            return redirect()->route('staff_unapproved_tickets')->with($alert);
        
        }elseif(User::findOrFail($lastInsertedTicket->ticket_by)->role == 'SuperAdmin'){

            return redirect()->route('super_admin_my_tickets')->with($alert);

        }
        elseif(User::findOrFail($lastInsertedTicket->ticket_by)->role == 'HOD'){
            return redirect()->route('admin_my_tickets')->with($alert);
        }
    }

    public function editTicket($id){

        $activeMenu = "tickets_raised";
        $activeDropdown = "create_ticket";

        $departments = Department::all();

        $ticket = Ticket::findOrFail($id);

        if(Auth::user()->role == 'Staff'){

            return view('frontend.staff.ticket.edit-ticket', compact('activeMenu', 'activeDropdown', 'departments', 'ticket'));

        }elseif(Auth::user()->role == 'SuperAdmin'){

            return view('frontend.superadmin.ticket.edit-ticket', compact('activeMenu', 'activeDropdown', 'departments', 'ticket'));

        }elseif(Auth::user()->role == 'HOD'){

            return view('frontend.admin.ticket.edit-ticket', compact('activeMenu', 'activeDropdown', 'departments', 'ticket'));

        }

    }

    public function updateTicket(Request $request)
    {
        $title = $request->input('title');
        $ticket_to = $request->input('ticket_to');
        $description = $request->input('description');
        $priority = $request->input('priority');
        $due_date = $request->input('due_date');
        $id = $request->input('id');

        $ticket = Ticket::findOrFail($id);

        if ($ticket->is_approved) {
            $alert = array(
                'message' => 'Approved tickets cannot be edited',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);
        }

        if ($request->hasFile('file')) {
            
            if ($ticket->file) {
                if (Storage::disk('public')->exists($ticket->file)) {
                    Storage::disk('public')->delete($ticket->file);
                } else {
                    \Log::warning("File not found: " . $ticket->file);
                }
            }

            
            $year = date('Y');
            $month = date('m');
            $folderPath = "uploads/{$year}/{$month}";

            
            do {
                $uniqueName = uniqid() . '.' . $request->file('file')->getClientOriginalExtension();
                $filePath = "{$folderPath}/{$uniqueName}";
            } while (Storage::disk('public')->exists($filePath));

            
            $request->file('file')->storeAs($folderPath, $uniqueName, 'public');
        
        } else {
            
            $filePath = $ticket->file;
        }

        $ticket->update([
            'title' => $title,
            'ticket_to' => $ticket_to,
            'description' => $description,
            'file' => $filePath,
            'priority' => $priority,
            'due_date' => $due_date,
            'updated_at' => now()
        ]);

        $updatedAt = Carbon::parse($ticket->updated_at);
        $formattedUpdatedAt = $updatedAt->format('M d, Y g:ia');

        if (Auth::user()->role == 'SuperAdmin') {
            $updated_by = Auth::user()->name;
        } else {
            $updated_by = Auth::user()->name . ', ' . Auth::user()->designation;
        }

        $log_description = "Ticket edited by " . $updated_by . " at " . $formattedUpdatedAt;

        DB::table('ticket_logs')->insert([
            'ticket_id' => $ticket->id,
            'title' => "Edited",
            'description' => $log_description,
            'created_at' => now()
        ]);

        $to = Auth::id();
        $task_id = $ticket->id;

        /* Notification to Self */
        $notificationController = new notificationController();
        $notificationController->notificationEntry($to, 'ticket', $task_id, 'Ticket - <b>' . $title . '</b> has been edited');

        /* Notification to owner */
        if ($ticket->ticket_by != Auth::id()) {
            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $task_id, 'Your Ticket - <b>' . $title . '</b> has been edited');
        }

        $alert = array(
            'message' => 'Ticket Updated',
            'alert-type' => 'success'
        );

        $userRole = Auth::user()->role;

        if ($userRole == 'Staff') {
            return redirect()->route('staff_unapproved_tickets')->with($alert);
        } elseif ($userRole == 'SuperAdmin') {
            return redirect()->route('super_admin_unapproved_tickets')->with($alert);
        } else {
            return redirect()->route('admin_unapproved_tickets')->with($alert);
        }
    }



    public function adminApproveTicket($id){

        $ticket = Ticket::findOrFail($id);

        if($ticket->ticket_from == Auth::user()->department){

            Ticket::findOrFail($id)->update([
                'is_approved' => True,
                'updated_at' => now()
            ]);

            /*Log Entry*/

            $updatedAt = Carbon::parse($ticket->updated_at);

            $formattedUpdatedAt = $updatedAt->format('M d, Y g:ia');

            $updated_by = Auth::user()->name.', '.$ticket->ticket_from;

            $log_description = "Ticket approved by " .$updated_by. "Head at ".$formattedUpdatedAt;

            DB::table('ticket_logs')->insert([
                'ticket_id' => $ticket->id,
                'title' => "Approved",
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification Entries*/

            /* Self */

            $notificationController = new notificationController();
            $notificationController->notificationEntry(Auth::id(), 'ticket', $ticket->id, 'Ticket - <b>'.$ticket->title.'</b> has been approved');

            /* Ticket Owner */

            if($ticket->ticket_by != Auth::id()){

                $notificationController = new notificationController();
                $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket->id, 'Your Ticket - <b>'.$ticket->title.'</b> has been approved');

            }

            /* Ticket to */

            $notificationController = new notificationController();
            $notificationController->notificationEntry($ticket->ticket_to, 'ticket', $ticket->id, 'Ticket - <b>'.$ticket->title.'</b> from <b>'.$ticket->ticket_from.'</b> Received');

            $alert = array(
                'message' => 'Ticket approved',
                'alert-type' => 'success'
            );
 
            return redirect()->back()->with($alert);


        }else{
            $alert = array(
                'message' => 'Permission denied',
                'alert-type' => 'error'
            );
 
            return redirect()->back()->with($alert);
        }

    }


    public function showLog($ticketId)
    {
        $activityLog = TicketLog::where('ticket_id', $ticketId)->get(); // Replace with your actual query

        return response()->json($activityLog);
    }

    public function viewTicket($ticketId){

        $ticket = Ticket::find($ticketId);

        $departments = Department::all();

        $activeMenu = "";
        $activeDropdown = "";

        if($ticket){

            $log = TicketLog::where('ticket_id', '=', $ticket->id)->get();
            $conversation = TicketConversation::where('ticket_id', '=', $ticket->id)->get();

            if(Auth::user()->role == 'HOD'){
                return view('frontend.admin.ticket.view-ticket', compact('ticket', 'activeMenu', 'activeDropdown', 'log', 'conversation', 'departments'));
            }else{
                return view('frontend.superadmin.ticket.view-ticket', compact('ticket', 'activeMenu', 'activeDropdown', 'log', 'conversation', 'departments'));
            }

        }else{

            $alert = array(
            'message' => 'Ticket Not Found',
            'alert-type' => 'error'
        );

            return redirect()->back()->with($alert);
        }

    }

    public function logEntry($ticketId, $log_description){

        $lastInsertedTicket = TicketConversation::latest()->first();

        $createdAt = Carbon::parse($lastInsertedTicket->created_at);

        $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

        switch($log_description){
            case 'Response':
                $description = "Ticket responded by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Assign':
                $description = "Staff Assigned by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Reopen':
                $description = "Ticket Reopened by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Progress':
                $description = "Ticket in Progress by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Closed':
                $description = "Ticket Closed by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Hold':
                $description = "Ticket put on Hold by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Completed':
                $description = "Ticket has been marked Completed by " .$lastInsertedTicket->by. " at ".$formattedCreatedAt;
                break;
            case 'Forward':
                $description = $lastInsertedTicket->description;
                break;
            case 'Forwarded_Assign':
                $description = $lastInsertedTicket->description;
                break;
            case 'Priority':
                $description = $lastInsertedTicket->description;
                break;
        }

        DB::table('ticket_logs')->insert([
            'ticket_id' => $lastInsertedTicket->ticket_id,
            'title' => "Dummy",
            'description' => $description,
            'created_at' => now()
        ]);

    }

    public function respondTicket(Request $request){

        $ticket_id = $request->input('ticket_id');
        $description = $request->input('description');

        $user = Auth::user();

        if ($request->file('file')) {
            
            $year = date('Y');
            $month = date('m');
            $folderPath = "uploads/{$year}/{$month}/responses";

            do {
                $uniqueName = uniqid() . '.' . $request->file('file')->getClientOriginalExtension();
                $filePath = "{$folderPath}/{$uniqueName}";
            } while (Storage::disk('public')->exists($filePath));

            
            $request->file('file')->storeAs($folderPath, $uniqueName, 'public');
        
        } else {
        
            $filePath = null;
        }

        if($user->role == 'SuperAdmin'){
            $by = $user->department;
        }else{
            $by = $user->name. ', '. $user->department. '-'. $user->designation;
        }

        $ticket = Ticket::findOrFail($ticket_id);

        DB::table('ticket_conversations')->insert([
            'ticket_id' => $ticket_id,
            'title' => "Response",
            'by' => $by,
            'description' => $description,
            'file' => $filePath,
            'created_at' => now()
        ]);

        $log_description = "Response";

        $this->logEntry($ticket_id, $log_description);

        $notificationController = new notificationController();

        if(Auth::id() == $ticket->ticket_by){
            $message = "Ticket - <b>".$ticket->title."</b>'s Owner replied to you";
            $notificationController->notificationEntry($ticket->assigned_to, 'ticket', $ticket_id, $message);
        }

        if(Auth::id() == $ticket->assigned_to){
            $message = "Staff responded to your ticket - <b>".$ticket->title."</b>";
            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, $message);    
        }

        $alert = array(
            'message' => 'Ticket Responded',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);

    }

    public function DeleteTicket($ticket_id){

        $ticket = Ticket::findOrFail($ticket_id);

        if ($ticket->assigned_to == Null) {

            if ($ticket->file) {
                Storage::disk('public')->delete($ticket->file);
            }
            
            $ticket->delete();

            $alert = array(
                'message' => 'Ticket Deleted',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);
            
        } else {
            
            $alert = array(
                'message' => 'Assigned tickets cannot be deleted!',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);

        }

    }

    public function assignTicket($emp_id, $ticket_id){
        
        $user = User::find($emp_id);
        $ticket = Ticket::find($ticket_id);

        Ticket::where('id', '=', $ticket_id)->update([
            'assigned_to' => $emp_id,
            'assigned_by' => Auth::id()
        ]);

        $now = Carbon::now()->format('M d, Y g:ia'); // Get the current time
        
        $to = $user->name.', '.$user->designation;

        if(Auth::user()->role == 'SuperAdmin'){

            $by = Auth::user()->department; 
        }else{

            $by = Auth::user()->name.', '.Auth::user()->designation;
        }

        $description = "Ticket has been assigned to " .$to. " by " .$by. " at " .$now;

        DB::table('ticket_conversations')->insert([
            'ticket_id' => $ticket->id,
            'title' => "Assign",
            'by' => $by,
            'description' => $description,
            'created_at' => now()
        ]);

        $log_description = "Assign";

        $ticket = Ticket::findOrFail($ticket_id);

        $this->logEntry($ticket_id, $log_description);

        $notificationController = new notificationController();

        if($ticket->assigned_to == Auth::id()){

            $notificationController->notificationEntry($user->id, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title.'</b> from <b>' .$ticket->ticket_from. '</b> assigned to you');

        }else{

            $notificationController->notificationEntry($user->id, 'ticket', $ticket_id, 'Head assigned you a ticket - <b>'.$ticket->title.'</b> from <b>' .$ticket->ticket_from.'</b>');    
        }

        //$ticket = Ticket::find($ticket_id);

        $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Staff assigned to your ticket - <b>'.$ticket->title.'</b>');


        $alert = array(
            'message' => 'Ticket Assigned',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);
    }

    public function changeTicketStatus($ticket_status, $ticket_id){

        Ticket::where('id', '=', $ticket_id)->update([
            'status' => $ticket_status
        ]);

        if(Auth::user()->role == 'SuperAdmin'){

            $by = Auth::user()->department;
        }else{

            $by = Auth::user()->name.', '.Auth::user()->department.'-'.Auth::user()->designation;
        }

        if($ticket_status == 'Reopen'){

            $now = Carbon::now()->format('M d, Y g:ia');
        
            $description = "Ticket has been Reopened by " .$by. " at " .$now;

            DB::table('ticket_conversations')->insert([
                'ticket_id' => $ticket_id,
                'title' => "Reopened",
                'by' => $by,
                'description' => $description,
                'created_at' => now()
            ]);

            $log_description = "Reopen";

            Ticket::where('id', '=', $ticket_id)->update([
                'status' => "Open",
            ]);

            /* Notification to HOD */

            $ticket = Ticket::findOrFail($ticket_id);

            $notificationController = new notificationController();

            $notificationController->notificationEntry($ticket->assigned_by, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Reopened');

            /* Notification to Self */

            if($ticket->assigned_to != $ticket->assigned_by){
                $notificationController->notificationEntry($ticket->assigned_to, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Reopened');
            }

            /* Notification to Ticket Owner */

            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Your Ticket - <b>'.$ticket->title.'</b> has been Reopened');

            $this->logEntry($ticket_id, $log_description);
            
            $alert = array(
                'message' => 'Ticket Reopened',
                'alert-type' => 'success'
            );  

        }else if($ticket_status == 'In Progress') {

            $now = Carbon::now()->format('M d, Y g:ia');
        
            $description = "Ticket in progress by " .$by. " at " .$now;

            DB::table('ticket_conversations')->insert([
                'ticket_id' => $ticket_id,
                'title' => "In Progress",
                'by' => $by,
                'description' => $description,
                'created_at' => now()
            ]);

            $log_description = "Progress";

            Ticket::where('id', '=', $ticket_id)->update([
                'status' => "In Progress",
            ]);

            $this->logEntry($ticket_id, $log_description);

            /* Notification Entries */

            /* Notification to HOD */

            $ticket = Ticket::findOrFail($ticket_id);

            $notificationController = new notificationController();

            $notificationController->notificationEntry($ticket->assigned_by, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> is In Progress');

            if($ticket->assigned_to != $ticket->assigned_by){

                $notificationController->notificationEntry($ticket->assigned_to, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> is In Progress');
            }

            /* Notification to Ticket Owner */

            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Your Ticket - <b>'.$ticket->title.'</b> is In Progress');

            $alert = array(
                'message' => 'Status Updated',
                'alert-type' => 'success'
            );

        }else if($ticket_status == 'Hold') {

            $now = Carbon::now()->format('M d, Y g:ia');
        
            $description = "Ticket put on Hold by " .$by. " at " .$now;

            DB::table('ticket_conversations')->insert([
                'ticket_id' => $ticket_id,
                'title' => "Hold",
                'by' => $by,
                'description' => $description,
                'created_at' => now()
            ]);

            $log_description = "Hold";

            Ticket::where('id', '=', $ticket_id)->update([
                'status' => "Hold",
            ]);

            $this->logEntry($ticket_id, $log_description);

            /* Notification Entries */

            /* Notification to HOD */

            $ticket = Ticket::findOrFail($ticket_id);

            $notificationController = new notificationController();

            $notificationController->notificationEntry($ticket->assigned_by, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> is Hold');

            /* Notification to Self */

            if($ticket->assigned_to != $ticket->assigned_by){

                $notificationController->notificationEntry($ticket->assigned_to, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> is Hold');
            }

            /* Notification to Ticket Owner */

            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Your Ticket - <b>'.$ticket->title.'</b> is Hold');

            $alert = array(
                'message' => 'Status Updated',
                'alert-type' => 'success'
            );

        }else if($ticket_status == 'Completed'){

            $now = Carbon::now()->format('M d, Y g:ia');
        
            $description = "Ticket has been marked Completed by " .$by. " at " .$now;

            DB::table('ticket_conversations')->insert([
                'ticket_id' => $ticket_id,
                'title' => "Completed",
                'by' => $by,
                'description' => $description,
                'created_at' => now()
            ]);

            
            $log_description = "Completed";

            Ticket::where('id', '=', $ticket_id)->update([
                'status' => "Completed",
                'closed_by' => Auth::id()
            ]);

            $this->logEntry($ticket_id, $log_description);

            /* Notification Entries */

            /* Notification to HOD */

            $ticket = Ticket::findOrFail($ticket_id);

            $notificationController = new notificationController();

            $notificationController->notificationEntry($ticket->assigned_by, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been marked as Completed');

            /* Notification to Staff */

            if($ticket->assigned_to != $ticket->assigned_by){
                $notificationController->notificationEntry($ticket->assigned_to, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been marked as Completed');    
            }
            
            /* Notification to Ticket Owner */

            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Your Ticket - <b>'.$ticket->title.'</b> has been marked as Completed');

            $alert = array(
                'message' => 'Ticket Marked as Completed',
                'alert-type' => 'success'
            );

        }

        return redirect()->back()->with($alert);

    }

    public function changeTicketPriority($ticket_id, $priority){

        Ticket::where('id', '=', $ticket_id)->update([
            'priority' => $priority
        ]);

        if(Auth::user()->role == 'SuperAdmin'){

            $by = Auth::user()->department;
        }else{

            $by = Auth::user()->name.', '.Auth::user()->department.'-'.Auth::user()->designation;
        }


        $now = Carbon::now()->format('M d, Y g:ia');
        
        $description = "Tickets priority changed to <b>" .$priority. "</b> by <b>" .$by. "</b> at " .$now;

        DB::table('ticket_conversations')->insert([
            'ticket_id' => $ticket_id,
            'title' => "Priority",
            'by' => $by,
            'description' => $description,
            'created_at' => now()
        ]);

        $log_description = "Priority";

            /* Notification to Ticket Owner */

        $ticket = Ticket::findOrFail($ticket_id);

        $notificationController = new notificationController();

        $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> priority changed to <b>'.$priority.'</b> by <b>'.$by.'</b>');

            /* Notification to Ticket Owner's HOD */

        if(Auth::user()->department != $ticket->ticket_from){

            $notificationController->notificationEntry($ticket->ticket_from, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> priority changed to <b>'.$priority.'</b> by <b>'.$by.'</b>');
        }

            /* Notification to Ticket Self */

        $notificationController->notificationEntry(Auth::id(), 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> priority changed to <b>'.$priority.'</b>');


        $this->logEntry($ticket_id, $log_description);
            
        $alert = array(
            'message' => 'Priority Changed',
            'alert-type' => 'success'
        );  

        

        return redirect()->back()->with($alert);

    }

    public function closeTicket(Request $request){

        $ticket_id = $request->input('ticket_id');
        $rating = $request->input('rating3');

        if(Auth::user()->role == 'SuperAdmin'){

            $by = Auth::user()->department;
        }else{

            $by = Auth::user()->name.', '.Auth::user()->department.'-'.Auth::user()->designation;
        }

        $ticket_status = "Closed";

        $now = Carbon::now()->format('M d, Y g:ia');
        
        $description = "Ticket Closed by " .$by. " at " .$now;

        DB::table('ticket_conversations')->insert([
            'ticket_id' => $ticket_id,
            'title' => "Closed",
            'by' => $by,
            'description' => $description,
            'created_at' => now()
        ]);

        
        $log_description = "Closed";

        Ticket::where('id', '=', $ticket_id)->update([
            'status' => "Closed",
            'closed_by' => Auth::id(),
            'rating' => $rating,
            'updated_at' => now()
        ]);

        $this->logEntry($ticket_id, $log_description);

        /* Notification Entries */

        /* Notification to HOD */

        $ticket = Ticket::findOrFail($ticket_id);

        $notificationController = new notificationController();

        $notificationController->notificationEntry($ticket->assigned_by, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Closed');

        /* Notification to Staff */
        if($ticket->assigned_to != $ticket->assigned_by){
            $notificationController->notificationEntry($ticket->assigned_to, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Closed');
        }

        /* Notification to Ticket Owner */

        $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Your Ticket - <b>'.$ticket->title.'</b> has been Closed');

        $alert = array(
            'message' => 'Ticket Closed',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);

    }
}
