<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Notification;

class NotificationController extends Controller
{
    
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read'], 200);
    }

    public function notificationRedirect($task_type, $task_it, $notification_id){
        
        $notification = Notification::findOrFail($notification_id);

        if(Auth::user()->role == 'Staff'){
            $role = 'staff';
        }else{
            $role = 'admin';
        }

        if(Auth::user()->role == 'SuperAdmin'){
            $role = 'superadmin';
        }
        
        //dd($role);

        $link = url($role.'/view/'.$notification->task_type.'/'.$notification->task_id);

        if($notification->task_type == 'postal'){
            $link = url('post/view/'.$notification->task_id);
        }

        if($notification->task_type == 'document'){
            $link = url('view/document/'.$notification->task_id);
        }

        Notification::find($notification_id)->update([
            'is_read' => true,
            'updated_at' => now()
        ]);

        return redirect()->to($link);
    }

    public function notificationEntry($to, $task_type, $task_id, $message){

        Notification::create([
            'to' => $to,
            'task_type' => $task_type,
            'task_id' => $task_id,
            'message' => $message,
            'created_at' => now()
        ]);

    }

    public function notificationMarkAllRead(){

        if(Auth::user()->role == 'Staff'){
            $notifications = Notification::where('to', Auth::id())->get();    
        }else{
            $notifications = Notification::where('to', Auth::id())->orWhere('to', Auth::user()->department)->get();
        }

        foreach($notifications as $notification){

            Notification::where('id', $notification->id)->update([
                'is_read' => 1,
                'updated_at' => now()
            ]);

        }

        $alert = array(
            'message' => 'Marked Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);
    }

    public function NotificationsMark($notification_id){

        $notification = Notification::findOrFail($notification_id);

        if($notification->is_read == True){
            $read = False;
        }else{
            $read = True;
        }

        Notification::where('id', $notification_id)->update([
            'is_read' => $read,
            'updated_at' => now()
        ]);

        $alert = array(
            'message' => 'Marked Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($alert);
    }

    public function Notifications(){

        if(Auth::user()->role == 'Staff'){
            
            $activeMenu = "notifications";
            $activeDropdown = "";

            $notifications = Notification::where('to', Auth::id())->latest()->paginate(10);

            //dd($notifications);

            return view('frontend.staff.notifications.index', compact('activeMenu', 'activeDropdown', 'notifications'));

        }elseif(Auth::user()->role == 'SuperAdmin'){
            
            $activeMenu = "notifications";
            $activeDropdown = "";

            $notifications = Notification::where('to', Auth::id())
                                            ->orWhere('to', Auth::user()->department)
                                            ->latest()
                                            ->paginate(10);

            return view('frontend.superadmin.notifications.index', compact('activeMenu', 'activeDropdown', 'notifications'));

        }

        else{

            $activeMenu = "notifications";
            $activeDropdown = "";

            $notifications = Notification::where('to', Auth::id())
                                            ->orWhere('to', Auth::user()->department)
                                            ->latest()
                                            ->paginate(10);

            //dd($notifications);

            return view('frontend.admin.notifications.index', compact('activeMenu', 'activeDropdown', 'notifications'));
        }

    }
}
