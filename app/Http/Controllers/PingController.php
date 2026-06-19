<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Ping;

use App\Jobs\SendTicketNotificationMail;

use App\Http\Controllers\NotificationController;

class PingController extends Controller
{
    public function pingAlert($task_type, $task_id, $ping_to){

        switch($task_type){
            case 'ticket':
                $ticket = Ticket::findOrFail($task_id);

                $notificationController = new notificationController();
                
                $ping = Ping::where('task_type', $task_type)->where('task_id', $task_id)->where('ping_to', $ping_to)->first();

                if($ping){

                    Ping::findOrFail($ping->id)->update([
                        'ping_count' => $ping->ping_count + 1,
                        'updated_at' => now()
                    ]);

                }else{

                    Ping::create([
                        'task_type' => $task_type,
                        'task_id' => $task_id,
                        'ping_from' => Auth::id(),
                        'ping_to' => $ping_to,
                        'ping_count' => 1,
                        'created_at' => now()
                    ]);

                }

                $ping_name = $ping_to;

                if (is_numeric($ping_to)) {
                    
                    $ping_name = User::findOrFail($ping_to)->name; 
                    $staff = User::findOrFail($ping_to);

                    /* Notification to */

                    if(Auth::user()->department == $staff->department){

                        $notificationController->notificationEntry($ping_to, 'ticket', $ticket->id, 'Your Head pinged you your ticket - <b>'.$ticket->title);

                        $email_to = User::findOrFail($ping_to);

                        $mail_details['content'] = 'Your Head pinged you your ticket - <b>'.$ticket->title.'</b> from <b>'.$ticket->ticket_from.'</b>';
                        
                        if($email_to->role == 'HOD'){
                            $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
                        }elseif($email_to->role == 'SuperAdmin'){
                            $mail_details['url'] = URL::to('superadmin/view/ticket/'.$ticket->id);
                        }else{
                            $mail_details['url'] = URL::to('staff/view/ticket/'.$ticket->id);
                        }
                        
                        $mail_details['title'] = 'Ticket Pinged';
                        
                        $subject = 'Ticket - '.$ticket->title.' from '.$ticket->ticket_from.' has been pinged!';

                        // Dispatch the job to the queue
                        SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.ticket_notifications');

                    }else{

                        if(Auth::user()->role == 'Staff'){
                            $notificationController->notificationEntry($ping_to, 'ticket', $ticket->id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-' .Auth::user()->role);  

                            $email_to = User::findOrFail($ping_to);

                            $mail_details['content'] = 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-' .Auth::user()->role;
                            
                            if($email_to->role == 'HOD'){
                                $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
                            }elseif($email_to->role == 'SuperAdmin'){
                                $mail_details['url'] = URL::to('superadmin/view/ticket/'.$ticket->id);
                            }else{
                                $mail_details['url'] = URL::to('staff/view/ticket/'.$ticket->id);
                            }
                            
                            $mail_details['title'] = 'Ticket Pinged';
                            
                            $subject = 'Ticket - '.$ticket->title.' from '.$ticket->ticket_from.' has been pinged!';

                            // Dispatch the job to the queue
                            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.ticket_notifications');

                        }else{
                            $notificationController->notificationEntry($ping_to, 'ticket', $ticket->id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-Head');

                            $email_to = User::findOrFail($ping_to);

                            $mail_details['content'] = 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-Head';
                            
                            if($email_to->role == 'HOD'){
                                $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
                            }elseif($email_to->role == 'SuperAdmin'){
                                $mail_details['url'] = URL::to('superadmin/view/ticket/'.$ticket->id);
                            }else{
                                $mail_details['url'] = URL::to('staff/view/ticket/'.$ticket->id);
                            }
                            
                            $mail_details['title'] = 'Ticket Pinged';
                            
                            $subject = 'Ticket - '.$ticket->title.' from '.$ticket->ticket_from.' has been pinged!';

                            // Dispatch the job to the queue
                            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.ticket_notifications');
                        }
                    }

                    /* Notification to self */

                    $notificationController->notificationEntry(Auth::id(), 'ticket', $ticket->id, 'You pinged <b>'.$ping_name.'</b> for the ticket - <b>'.$ticket->title);
                     
                }else{

                    if(Auth::user()->role == 'Staff'){
                        $notificationController->notificationEntry($ping_to, 'ticket', $ticket->id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-' .Auth::user()->role);

                        $email_to = User::where('department', $ping_to)->where('role', 'HOD')->where('is_active', 1)->first();

                        $mail_details['content'] = 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-' .Auth::user()->role;
                        
                        if($email_to->role == 'HOD'){
                            $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
                        }elseif($email_to->role == 'SuperAdmin'){
                            $mail_details['url'] = URL::to('superadmin/view/ticket/'.$ticket->id);
                        }else{
                            $mail_details['url'] = URL::to('staff/view/ticket/'.$ticket->id);
                        }
                        
                        $mail_details['title'] = 'Ticket Pinged';
                        
                        $subject = 'Ticket - '.$ticket->title.' from '.$ticket->ticket_from.' has been pinged!';

                        // Dispatch the job to the queue
                        SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.ticket_notifications');

                    }else{
                        $notificationController->notificationEntry($ping_to, 'ticket', $ticket->id, 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-Head');

                        $email_to = User::where('department', $ping_to)->where('role', 'HOD')->where('is_active', 1)->first();

                        $mail_details['content'] = 'Ticket - <b>'.$ticket->title. '</b> from <b>'.$ticket->ticket_from.'</b> has been Pinged by <b>' .Auth::user()->department. '-Head';
                        
                        if($email_to->role == 'HOD'){
                            $mail_details['url'] = URL::to('admin/view/ticket/'.$ticket->id);
                        }elseif($email_to->role == 'SuperAdmin'){
                            $mail_details['url'] = URL::to('superadmin/view/ticket/'.$ticket->id);
                        }else{
                            $mail_details['url'] = URL::to('staff/view/ticket/'.$ticket->id);
                        }
                        
                        $mail_details['title'] = 'Ticket Pinged';
                        
                        $subject = 'Ticket - '.$ticket->title.' from '.$ticket->ticket_from.' has been pinged!';

                        // Dispatch the job to the queue
                        SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.ticket_notifications');
                    }

                    /* Notification to self */

                    $notificationController->notificationEntry(Auth::id(), 'ticket', $ticket->id, 'You pinged <b>'.$ping_to.'-Head</b> for the ticket - <b>'.$ticket->title);

                }
                
            break;
                
        }

        $alert = array(
            'message' => 'Pinged Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);

    }

    public function getTaskDetails($task_type, $task_id){

        switch($task_type){
            case 'ticket':
                
                $task = Ticket::findOrFail($task_id);
                    
                if(Auth::user()->role == "Staff"){
                    $url = '/staff/view/ticket/'.$task_id;
                }elseif(Auth::user()->role == "HOD"){
                    $url = '/admin/view/ticket/'.$task_id;
                }else{
                    $url = '/superadmin/view/ticket/'.$task_id;
                }


                $task_details = [
                    'task_title' => $task->title,
                    'task_url' => $url,
                    'task_from' => $task->ticket_from,
                    'task_created_at' => $task->created_at,
                ];
                return $task_details;
        }
    }

    public function Pings(){

        if(Auth::user()->role == 'Staff'){
            
            $activeMenu = "pings";
            $activeDropdown = "";

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
            ->get();


            return view('frontend.staff.pings.index', compact('activeMenu', 'activeDropdown', 'pings'));
           

        }elseif(Auth::user()->role == 'SuperAdmin'){
            
            $activeMenu = "pings";
            $activeDropdown = "";

            $pings = Ping::join('tickets', 'pings.task_id', '=', 'tickets.id')
                ->where(function($query) {
                    $query->where('pings.ping_to', Auth::user()->department)
                          ->orWhere('pings.ping_to', Auth::id());
                })
                ->where('tickets.status', '!=', 'closed') // Add this line to exclude closed tickets
                ->orderByRaw('pings.ping_count DESC')
                ->get();

            return view('frontend.superadmin.pings.index', compact('activeMenu', 'activeDropdown', 'pings'));
           

        }else{

            $activeMenu = "pings";
            $activeDropdown = "";

            $pings = Ping::join('tickets', 'pings.task_id', '=', 'tickets.id')
                ->where(function($query) {
                    $query->where('pings.ping_to', Auth::user()->department)
                          ->orWhere('pings.ping_to', Auth::id());
                })
                ->where('tickets.status', '!=', 'closed') // Add this line to exclude closed tickets
                ->orderByRaw('pings.ping_count DESC')
                ->get();

            return view('frontend.admin.pings.index', compact('activeMenu', 'activeDropdown', 'pings'));
        }

    }
}
