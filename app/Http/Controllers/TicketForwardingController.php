<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use Carbon\Carbon;
use Mail;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\Department;
use App\Models\TicketConversation;
use App\Models\TicketForwarding;

use App\Http\Controllers\NotificationController;
use App\Jobs\SendTicketNotificationMail;

class TicketForwardingController extends Controller
{
    public function forwardTicket(Request $request){

        $ticket_id = $request->input('ticket_id');
        $forwarded_to = $request->input('forwarded_to');

        if($forwarded_to == 'Pro-VC & VC'){
            $forwarded_to = 'Pro-VC';
        }
        
        if(Auth::user()->role == 'HOD'){

            $forwarded_by = Auth::user()->department. ' Head';  

        }elseif(Auth::user()->role == 'SuperAdmin'){

            $forwarded_by = Auth::user()->department;

        }else{

            $alert = array(
                'message' => 'Permission Denied',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);
        }

        $flag = TicketForwarding::where('ticket_id', $ticket_id)->where('forwarded_to', $forwarded_to)->get();

        if($flag->count() > 0){
            return response()->json(['message' => 'This ticket has already been forwarded to ' . $forwarded_to. ' by '.$forwarded_by, 'status' => 'error'], 400);
        }else{

            DB::table('tickets')->where('id', $ticket_id)->update([
                'is_forwarded' => True,
                'forwarded_to' => $forwarded_to,
                'updated_at' => now()
            ]);

            DB::table('ticket_forwardings')->insert([
                'ticket_id' => $ticket_id,
                'forwarded_to' => $forwarded_to,
                'forwarded_by' => $forwarded_by,
                'created_at' => now()
            ]);

            $now = Carbon::now()->format('M d, Y g:ia'); // Get the current time

            $description = "Ticket has been forwarded to <b>" .$forwarded_to. "</b> by <b>" .$forwarded_by. "</b> at " .$now;

            DB::table('ticket_conversations')->insert([
                'ticket_id' => $ticket_id,
                'title' => "Forward",
                'by' => $forwarded_by,
                'description' => $description,
                'created_at' => now()
            ]);

            $log_description = "Forward";

            $ticketController = new TicketController();

            $ticketController->logEntry($ticket_id, $log_description);

            $notificationController = new notificationController();

            $ticket = Ticket::find($ticket_id);

            $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket_id, 'Your Ticket - <b>'.$ticket->title.'</b> has been forwarded to <b>'.$forwarded_to.'</b> Section');

            $email_to = User::where('id', $ticket->ticket_by)->where('is_active', 1)->first();

            $mail_details['content'] = 'Your ticket - <b>'.$ticket->title.'</b> has been forwarded to <b>'.$forwarded_to.'</b>';
            
            if($email_to->role == 'Staff'){
                $mail_details['url'] = URL::to('staff/view/ticket/'.$ticket->id);
            }else{
                $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
            }
            
            $mail_details['title'] = 'Ticket Forwarded';
            
            $subject = 'Ticket - '.$ticket->title.' has been forwarded to '.$forwarded_to;
    
            // Dispatch the job to the queue
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject); 

            $notificationController->notificationEntry($forwarded_to, 'ticket', $ticket_id, 'Ticket - <b>'.$ticket->title.'</b> has been forwarded to <b>'.$forwarded_to.'</b> by <b>'.$forwarded_by.'</b>');

            if($forwarded_to == 'VC' || $forwarded_to == 'Pro-VC' || $forwarded_to == 'Registrar'){
                $email_to = User::where('department', $forwarded_to)->where('role', 'SuperAdmin')->where('is_active', 1)->first();
            }else{
                $email_to = User::where('department', $forwarded_to)->where('role', 'HOD')->where('is_active', 1)->first();
            }

            $mail_details['content'] = 'Ticket - <b>'.$ticket->title.'</b> from <b>'.$ticket->ticket_from.'</b> has been forwarded to you';
            
            if($email_to->role == 'SuperAdmin'){
                $mail_details['url'] = URL::to('superadmin/view/ticket/'.$ticket->id);
            }else{
                $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
            }
            
            $mail_details['title'] = 'Ticket Forwarded';
            
            $subject = 'Ticket - '.$ticket->title.' has been forwarded to '.$forwarded_to;
    
            // Dispatch the job to the queue
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject); 

            return response()->json(['message' => 'Ticket forwarded to '.$forwarded_to.' successfully!', 'status' => 'success']);

        } 
    }

    public function assignForwardedTicket($emp_id, $forwarded_id){
        
        $user = User::find($emp_id);
        $forwarded = TicketForwarding::find($forwarded_id);

        $ticket = Ticket::find($forwarded->ticket_id);

        if($emp_id == Auth::id()){

            //dd($emp_id);

            TicketForwarding::where('id', '=', $forwarded_id)->update([
                'assigned_to' => Null,
                'updated_at' => now()
            ]);

        }else{

            TicketForwarding::where('id', '=', $forwarded_id)->update([
                'assigned_to' => $emp_id,
                'updated_at' => now()
            ]);
        }

        $now = Carbon::now()->format('M d, Y g:ia'); // Get the current time
        
        $to = $user->name.', '.$user->designation;

        if(Auth::user()->role == 'SuperAdmin'){

            $by = Auth::user()->department; 
        }else{

            $by = Auth::user()->name.', '.Auth::user()->department.'-'.Auth::user()->designation;
        }

        $description = "Forwarded Ticket has been assigned to " .$to. " by " .$by. " at " .$now;

        DB::table('ticket_conversations')->insert([
            'ticket_id' => $ticket->id,
            'title' => "Assign",
            'by' => $by,
            'description' => $description,
            'created_at' => now()
        ]);

        $log_description = "Forwarded_Assign";

        $ticketController = new TicketController();

        $ticketController->logEntry($ticket->id, $log_description);

        $notificationController = new notificationController();

        
        $notificationController->notificationEntry($user->id, 'ticket', $ticket->id, 'A forwarded ticket - <b>'.$ticket->title.'</b> from <b>' .$ticket->ticket_from.'</b> assigned to you');    
        
        //$ticket = Ticket::find($ticket_id);

        $notificationController->notificationEntry($ticket->ticket_by, 'ticket', $ticket->id, 'Forwarded Staff assigned to your ticket - <b>'.$ticket->title.'</b>');


        $alert = array(
            'message' => 'Ticket Assigned',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);
    }

}
