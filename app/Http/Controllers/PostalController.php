<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

use App\Models\Department;
use App\Models\Postal;
use App\Models\PostalForwarding;
use App\Models\ReplyPost;
use App\Models\User;
use App\Models\DdeDetails;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

use Carbon\Carbon;
use Mail;

use App\Http\Controllers\NotificationController;
use App\Jobs\SendTicketNotificationMail;

class PostalController extends Controller
{
    public function searchStaff(Request $request)
    {
        $query = $request->input('query');
        
        // Search departments based on the input query
        $staff = User::where('name', 'LIKE', "%{$query}%")->select('id', 'name', 'department')->get();
        
        return response()->json($staff);
    }
    
    public function createPost(){

        $activeMenu = "postal";
        $activeDropdown = "create_post";

        $staff_name = User::select('id', 'name', 'department')->get();

        $departments = Department::select('dept_label')->get();

        return view('frontend.postal.add', compact('activeMenu', 'activeDropdown', 'departments'));
    }

    public function reEditPost($postal_id){

        $activeMenu = "postal";
        $activeDropdown = "create_post";

        $staff_name = User::select('id', 'name', 'department')->get();

        $departments = Department::select('dept_label')->get();

        $post = Postal::findOrFail($postal_id);

        return view('frontend.postal.re-edit', compact('activeMenu', 'activeDropdown', 'departments', 'post'));
    }
    
    public function createOutPost(){

        $activeMenu = "postal";
        $activeDropdown = "out_post";

        $staff_name = User::select('id', 'name', 'department')->get();

        $departments = Department::select('dept_label')->get();

        return view('frontend.postal.outpost', compact('activeMenu', 'activeDropdown', 'departments'));
    }
    
    public function createInPost(){

        $activeMenu = "postal";
        $activeDropdown = "in_post";

        $staff_name = User::select('id', 'name', 'department')->get();

        $departments = Department::select('dept_label')->get();

        return view('frontend.postal.inpost', compact('activeMenu', 'activeDropdown', 'departments'));
    }

    public function replyPost($post_id){

        $activeMenu = "postal";
        $activeDropdown = "create_post";

        $post = Postal::FindorFail($post_id);

        $staff_name = User::select('id', 'name', 'department')->get();

        $departments = Department::select('dept_label')->get();

        return view('frontend.postal.reply', compact('activeMenu', 'activeDropdown', 'departments', 'post'));
    }

    public function replyPostTrackingIDUpdate($rp_id){

        $activeMenu = "postal";
        $activeDropdown = "outgoing";

        $post = ReplyPost::find($rp_id);

        $departments = Department::select('dept_label')->get();

        return view('frontend.postal.rp.edit', compact('activeMenu', 'activeDropdown', 'departments', 'post'));
    }

    public function editPost($post_id){

        $activeMenu = "postal";
        $activeDropdown = "";

        $staff_name = User::select('id', 'name', 'department')->get();

        $post = Postal::find($post_id);

        $departments = Department::select('dept_label')->get();

        $latestRegistrarNo = Postal::whereNotNull('registrar_id')
                                ->orderBy('registrar_id', 'desc')
                                ->value('registrar_id');

                            if ($latestRegistrarNo) {
                                $latestRegistrarNo = (int) str_replace('Reg-PO-', '', $latestRegistrarNo);
                            } else {
                                $latestRegistrarNo = 0;
                            }

        return view('frontend.postal.edit', compact('activeMenu', 'activeDropdown', 'departments', 'post', 'latestRegistrarNo'));
    }

    public function saveDueDate(Request $request, $post_id)
    {
        $request->validate([
            'due_date' => 'required|date',
        ]);

        $post = Postal::findOrFail($post_id);
        $post->due_date = $request->input('due_date');
        $post->save();

        return response()->json(['success' => true]);
    }

    public function closePost(Request $request, $post_id)
    {
        $post = Postal::findOrFail($post_id);

        $post->update([
            'status' => 'Closed',
            'closed_by' => $request->input('closed_by'),
        ]);

        return response()->json(['success' => true]);
    }
    
    public function closePostalStatus(Request $request){

        $post_id = $request->input('post_id');
        $action = $request->input('status');

        $user = User::FindorFail(Auth::id());

        $post = Postal::findOrFail($post_id);

        if(true){

            $post->update([
                'remarks' => $request->input('remarks'),
                'status' => $action,
                'closed_by' => $user->id,
                'updated_at' => now()
            ]);

            $createdAt = Carbon::parse($post->updated_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Post status updated by <b>" .$user->department. "</b> at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $post->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

        }

        return response()->json(['message' => 'Success', 'status' => 'success']);
 
    }

    public function replyPostAddTrackingID(Request $request){

        $tracking_id = $request->input('tracking_id');
        $vendor = $request->input('vendor');

        //dd($request);

        ReplyPost::FindorFail($request->input('post_id'))->update([
            'tracking_id' => $tracking_id,
            'vendor' => $vendor,
            'updated_at' => now()
        ]);

        //outgoing_postal_entries

        $lastInsertedPost = ReplyPost::FindorFail($request->input('post_id'));

            $createdAt = Carbon::parse($lastInsertedPost->updated_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Tracking ID updated <b>" .Auth::user()->name. "</b> at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($lastInsertedPost->reply_by, 'postal', $lastInsertedPost->id, 'Your Reply post received a tracking ID - <b>'.Auth::user()->name.'</b>');


        $alert = array(
            'message' => 'Track ID Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('outgoing_postal_entries')->with($alert);
    }

    public function storePost(Request $request){

        //dd($request);

        $by = $request->input('by');
        $from_address = $request->input('from_address');
        $to = $request->input('to');
        $to_address = $request->input('to_address');
        $by = $request->input('by');
        $received_date = $request->input('received_date');
        $type = $request->input('type');
        $subject = $request->input('subject');

        // Get the department ID from the sent_to department
        $department = Department::where('dept_label', $to)->first();
        $department_id = $department->dept_id;

        // Get the last post for the department and increment the number
        $lastPost = Postal::latest('id')->first();
        $incremental_id = $lastPost ? intval(substr($lastPost->id, -6)) + 1 : 1;
        $incremental_id = str_pad($incremental_id, 6, '0', STR_PAD_LEFT);

        // Generate the post_id
        $post_id = "PO-{$incremental_id}";

        //dd($post_id);

        $to_type = $request->input('type_to');

        $user = Auth::user();

        $status = "Received";
        
        if($to_type == 'University'){
            
            Postal::insert([
                'post_id' => $post_id,
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'type_to' => $to_type,
                'tracking_id' => $request->input('tracking_id'),
                'original_at' => $to,
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($to, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            $email_to = User::where('department', 'Registrar')->where('role', 'SuperAdmin')->where('is_active', 1)->first();

            $mail_details['content'] = 'You have received a Post - <b>'.$by.'</b>';
            $mail_details['url'] = URL::to('post/view/'.$lastInsertedPost->id);
            
            $mail_details['title'] = 'Post Received';
            
            $subject = 'Post from '.$by.' Received!';

            // Dispatch the job to the queue
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications'); 
        }

        if($to_type == 'Cottage' || $to_type == 'KV'){

            if($to_type == 'Cottage'){
                $name = null;
            }else{
                $name = $request->input('name');
                $staff_name =  $request->input('staff_name');
                $staff_id = $request->input('staff_id');
            }

            //$post_id =  "PO-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            Postal::insert([
                'post_id' => $post_id,
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'type_to' => $to_type,
                'tracking_id' => $request->input('tracking_id'),
                'staff_name'  => $staff_id,
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($to, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            // $email_to = User::where('department', $to)->where('role', 'HOD')->where('is_active', 1)->first();

            // $mail_details['content'] = 'You have received a Post from - <b>'.$by.'</b>';
            // $mail_details['url'] = URL::to('admin/view/post/'.$lastInsertedPost->id);
            
            // $mail_details['title'] = 'Post Received';
            
            // $subject = 'Post from '.$by.' Received!';

            // Dispatch the job to the queue
            // SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject);
        }
        
        if($to_type == 'Staff'){

            //$post_id =  "PO-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $status = "Received";
            $staff_name =  $request->input('staff_name');
            $staff_id = $request->input('staff_id');

            if(!$staff_id){
                $staff_id = $to;
                $email_to = User::where('department', $to)->where('role', 'HOD')->where('is_active', 1)->first();
            }else{
                $email_to = User::find($staff_id);
            }

            Postal::insert([
                'post_id' => $post_id,
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'subject' => $subject,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'type_to' => $to_type,
                'is_approved' => 1,
                'staff_name' => $staff_id,
                'tracking_id' => $request->input('tracking_id'),
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            // Notification to Head

            $notificationController = new notificationController();
            $notificationController->notificationEntry($staff_id, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            //$email_to = User::where('department', 'Registrar')->where('role', 'SuperAdmin')->where('is_active', 1)->first();

            $mail_details['content'] = 'You have received a Post - <b>'.$by.'</b>';
            $mail_details['url'] = URL::to('post/view/'.$lastInsertedPost->id);
            
            $mail_details['title'] = 'Post Received';
            
            $subject = 'Post from '.$by.' Received!';

            // Dispatch the job to the queue
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications'); 
        }
        
        $alert = array(
            'message' => 'Entry Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('received_posts')->with($alert);
    }

    public function storeReEditPost(Request $request){

        $post = Postal::FindorFail($request->input('id'));

        $by = $request->input('by');
        $from_address = $request->input('from_address');
        $to = $request->input('to');
        $to_address = $request->input('to_address');
        $by = $request->input('by');
        $received_date = $request->input('received_date');
        $type = $request->input('type');
        $subject = $request->input('subject');

        $to_type = $request->input('type_to');

        $user = Auth::user();

        $status = "Received";
        
        if($to_type == 'University'){
            
            $post->update([
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'type_to' => $to_type,
                'tracking_id' => $request->input('tracking_id'),
                'original_at' => $to,
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($to, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            // $email_to = User::where('department', $to)->where('role', 'HOD')->where('is_active', 1)->first();

            // $mail_details['content'] = 'You have received a Post from - <b>'.$by.'</b>';
            // $mail_details['url'] = URL::to('admin/view/post/'.$lastInsertedPost->id);
            
            // $mail_details['title'] = 'Post Received';
            
            // $subject = 'Post from '.$by.' Received!';

            // // Dispatch the job to the queue
            // SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject);
        }

        if($to_type == 'Cottage' || $to_type == 'KV'){

            if($to_type == 'Cottage'){
                $name = null;
            }else{
                $name = $request->input('name');
                $staff_name =  $request->input('staff_name');
                $staff_id = $request->input('staff_id');
            }

            $post->update([
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'type_to' => $to_type,
                'tracking_id' => $request->input('tracking_id'),
                'staff_name'  => $staff_id,
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($to, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            // $email_to = User::where('department', $to)->where('role', 'HOD')->where('is_active', 1)->first();

            // $mail_details['content'] = 'You have received a Post from - <b>'.$by.'</b>';
            // $mail_details['url'] = URL::to('admin/view/post/'.$lastInsertedPost->id);
            
            // $mail_details['title'] = 'Post Received';
            
            // $subject = 'Post from '.$by.' Received!';

            // Dispatch the job to the queue
            // SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject);
        }
        
        if($to_type == 'Staff'){

            $status = "Received";
            $staff_name =  $request->input('staff_name');
            $staff_id = $request->input('staff_id');

            if(!$staff_id){
                $staff_id = $to;
                $email_to = User::where('department', $to)->where('role', 'HOD')->where('is_active', 1)->first();
            }else{
                $email_to = User::find($staff_id);
            }

            $post->update([
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'subject' => $subject,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'type_to' => $to_type,
                'is_approved' => 1,
                'staff_name' => $staff_id,
                'tracking_id' => $request->input('tracking_id'),
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            // Notification to Head

            $notificationController = new notificationController();
            $notificationController->notificationEntry($staff_id, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            // $email_to = User::find($request->input('staff_id'));
            
            // $mail_details['content'] = 'You have received a Post from - <b>'.$by.'</b>';
            // $mail_details['url'] = URL::to('admin/view/post/'.$lastInsertedPost->id);
            
            // $mail_details['title'] = 'Post Received';
            
            // $subject = 'Post from '.$by.' Received!';

            // SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject);
        }
        
        $alert = array(
            'message' => 'Entry Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('received_posts')->with($alert);
    }
    
    public function storeInPost(Request $request){
        
        //dd($request);

        $by = $request->input('by');
        $from_address = $request->input('from_address');
        $to = $request->input('to');
        $by = $request->input('by');
        $received_date = $request->input('received_date');
        $type = $request->input('type');
        $subject = $request->input('subject');
        
        $student_name = $request->input('student_name');
        $student_reg_no = $request->input('student_reg_no');
        
        //dd($request);
        
        if($type == 'Hand Delivered'){
            $to_address = 'Hand Delivered';
        }else{
            $to_address = Auth::user()->email;
        }

        // Get the last post for the department and increment the number
        $lastPost = Postal::latest('id')->first();
        $incremental_id = $lastPost ? intval(substr($lastPost->id, -6)) + 1 : 1;
        $incremental_id = str_pad($incremental_id, 6, '0', STR_PAD_LEFT);

        // Generate the post_id
        $post_id = "PO-{$incremental_id}";

        $user = Auth::user();

        $status = "Collected";
        
        if(true){

            $post_id =  $post_id;
            $status = "Collected";
            $staff_name =  $request->input('staff_name');
            $staff_id = $request->input('staff_id');

            if(!$staff_id){
                $staff_id = $to;
                $email_to = User::where('department', $to)->where('role', 'HOD')->where('is_active', 1)->first();
            }else{
                $email_to = User::find($staff_id);
            }

            Postal::insert([
                'post_id' => $post_id,
                'post_from_address' => $from_address,
                'post_to_address' => $to_address,
                'subject' => $subject,
                'sent_by' => $by,
                'sent_to' => $to,
                'status' => $status,
                'received_date' => $received_date,
                'type' => $type,
                'is_approved' => 1,
                'staff_name' => $staff_id,
                'tracking_id' => 'NA',
                'created_at' => now()
            ]);

            $lastInsertedPost = Postal::latest()->first();
            
            //dd($lastInsertedPost);
            
            if($student_name && $student_reg_no){
                
                if($request->has('is_fee_paid')){
                    $paid = true;
                }else{
                    $paid = false;
                }
                
                DdeDetails::create([
                    'post_id' => $lastInsertedPost->id,
                    'reg_no' => $student_reg_no,
                    'student_name' => $student_name,
                    'is_fee_paid' => $paid,
                ]);
                
            }

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Received a post from <b>" .$by. "</b> to ".$to. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            // Notification to Head

            $notificationController = new notificationController();
            $notificationController->notificationEntry($staff_id, 'postal', $lastInsertedPost->id, 'You recieved a post from - <b>'.$by.'</b>');

            //$email_to = User::where('department', 'Registrar')->where('role', 'SuperAdmin')->where('is_active', 1)->first();

            $mail_details['content'] = 'You have received a Post - <b>'.$by.'</b>';
            $mail_details['url'] = URL::to('post/view/'.$lastInsertedPost->id);
            
            $mail_details['title'] = 'Post Received';
            
            $subject = 'Post from '.$by.' Received!';

            // Dispatch the job to the queue
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications'); 
        }
        
        $alert = array(
            'message' => 'Entry Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('collected_posts')->with($alert);
    }

    public function postalReplyEntry(Request $request){

        $user = Auth::user();

        $post = Postal::FindorFail($request->input('post_pid'));

        $post->update([
            'is_responded' => true,
        ]);

        $status = "Recorded";

        if ($request->file('file')) {
            
            // Determine the folder structure based on the current year and month
            $year = date('Y');
            $month = date('m');
            $folderPath = "reply/posts/{$year}/{$month}/{$post->sent_to}";

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
        
        ReplyPost::insert([
            'post_id' => $request->input('post_id'),
            'post_pid' => $request->input('post_pid'),
            'reply_from_address' => $request->input('from_address'),
            'reply_to_address' => $request->input('to_address'),
            'subject' => $request->input('subject'),
            'reply_by' => $user->id,
            'reply_from' => $user->department,
            'reply_to' => $request->input('to'),
            'status' => $status,
            'reply_type' => $request->input('type'),
            'scanned_copy' => $filePath,
            'created_at' => now()
        ]);

        $lastInsertedPost = ReplyPost::latest()->first();

        $createdAt = Carbon::parse($lastInsertedPost->created_at);

        $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

        $log_description = "Post replied by <b>" .$user->name. " from " .$user->department. "</b> to at ".$formattedCreatedAt;

        DB::table('postal_logs')->insert([
            'post_id' => $lastInsertedPost->post_pid,
            'description' => $log_description,
            'created_at' => now()
        ]);

        /*Notification to Head*/

        $postal_head = User::where('department', 'Postal')->where('role', 'HOD')->first();

        $notificationController = new notificationController();
        $notificationController->notificationEntry($postal_head->id, 'postal', $lastInsertedPost->post_pid, 'Reply Post created by <b>'.$user->name.' from '.$user->department.'</b>');

        $email_to = $postal_head;

        $mail_details['content'] = 'Out Post created by <b>'.$user->name.' from '.$user->department.'</b>';
        $mail_details['url'] = URL::to('post/view/'.$lastInsertedPost->id);
        
        $mail_details['title'] = 'Out Post Created';
        
        $subject = 'Out Post Created by '.$user->name;

        // Dispatch the job to the queue
        SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications'); 

        $alert = array(
            'message' => 'Entry Added',
            'alert-type' => 'success'
        );
        if($user->role == 'HOD'){
            return redirect()->route('admin_personal_post')->with($alert);    
        }elseif($user->role == 'SuperAdmin'){
            return redirect()->route('superadmin_personal_post')->with($alert);            
        }else{
            return redirect()->back()->with($alert);
        }
        
    }
    
    public function storeOutPost(Request $request){
        
        //dd($request);

        $user = Auth::user();

        $status = "Recorded";
        
        $post_id =  "PO-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        if ($request->file('file')) {
            
            // Determine the folder structure based on the current year and month
            $year = date('Y');
            $month = date('m');
            $folderPath = "out/posts/{$year}/{$month}/{$user->department}";

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
        
            ReplyPost::insert([
                'post_id' => $post_id,
                'reply_from_address' => $request->input('from_address'),
                'reply_to_address' => $request->input('to_address'),
                'subject' => $request->input('subject'),
                'reply_by' => $user->id,
                'reply_from' => $user->department,
                'reply_to' => $request->input('to'),
                'status' => $status,
                'reply_type' => $request->input('type'),
                'scanned_copy' => $filePath,
                'created_at' => now()
            ]);

            $lastInsertedPost = ReplyPost::latest()->first();

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Out Post Created by <b>" .$user->name. " from " .$user->department. "</b> to at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $postal_head = User::where('department', 'Postal')->where('role', 'HOD')->first();

            $notificationController = new notificationController();
            $notificationController->notificationEntry($postal_head->id, 'postal', $lastInsertedPost->id, 'Out Post created by <b>'.$user->name.' from '.$user->department.'</b>');

            $email_to = $postal_head;

        $mail_details['content'] = 'Out Post created by <b>'.$user->name.' from '.$user->department.'</b>';
        $mail_details['url'] = URL::to('post/view/'.$lastInsertedPost->id);
        
        $mail_details['title'] = 'Out Post Created';
        
        $subject = 'Out Post Created by '.$user->name;

        // Dispatch the job to the queue
        SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications'); 

        $alert = array(
            'message' => 'Entry Added',
            'alert-type' => 'success'
        );

        if($user->role == 'HOD'){
            return redirect()->route('admin_personal_post')->with($alert);    
        }elseif($user->role == 'SuperAdmin'){
            return redirect()->route('superadmin_personal_post')->with($alert);            
        }else{
            return redirect()->back()->with($alert);
        }
        
    }

    public function updatePost(Request $request){
        
        //dd($request);

        $status = $request->input('status');
        
        $subject = $request->input('subject');

        $to_type = $request->input('type_to');

        $reg_id = $request->input('registrar_entry_no');

        //dd($request);
        
        if($request->has('original_at')){
            $status = 'Dispatched';
        }else{
            $status = 'Collected';
        }

        foreach (explode(', ', $request->input('forward_to')) as $forwaded_to){
            
            DB::table('postal_forwardings')->insert([
                'post_id' => $request->input('post_id'),
                'forwarded_to' => $forwaded_to,
                'forwarded_by' => Auth::id(),
                'dispatched_by' => Auth::id(),
                'is_read' => False,
                'status' => $status,
                'created_at' => now()
            ]);

            $email_to = User::where('department', $forwaded_to)->where('role', 'HOD')->where('is_active', 1)->first();

            if($forwaded_to == 'Registrar' || $forwaded_to == 'VC' || $forwaded_to == 'Pro-VC'){
                $email_to = User::where('department', $forwaded_to)->where('role', 'SuperAdmin')->where('is_active', 1)->first();
            }

            $mail_details['content'] = Auth::user()->name.' forwarded you a Post';
            $mail_details['url'] = URL::to('post/view/'.$request->input('post_id'));
            
            $mail_details['title'] = 'Post Received';
            
            $subject = 'Post forwarded by <b>'.Auth::user()->name.'</b>';

            // Dispatch the job to the queue
            SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications'); 
        }

        //dd('Stop');

        $user = Auth::user();

        $post = Postal::FindorFail($request->input('post_id'));

        if($request->has('original_at')){
            $to = $request->input('forward_to');
        }else{
            $to = $post->sent_to;
        }

        //dd($to);
        
        if(!$to_type){
            
            Postal::FindorFail($request->input('post_id'))->update([
                'registrar_id' => $reg_id,
                'subject' => $subject,
                'status' => 'Forwarded',
                'forward_to' => $request->input('forward_to'),
                'forwarded_by' => Auth::id(),
                'original_at' => $to,
                'updated_at' => now()
            ]);

            $lastInsertedPost = Postal::findOrFail($request->input('post_id'));

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Post Forwarded to <b>" .$to. "</b> by <b>" .Auth::user()->name. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($to, 'postal', $lastInsertedPost->id, $log_description);

        }elseif($to_type == 'DDE'){
            
            Postal::FindorFail($request->input('post_id'))->update([
                'registrar_id' => $reg_id,
                'subject' => $subject,
                'status' => 'Forwarded',
                'dde_payment_mode' => $request->input('payment_mode'),
                'dde_paid_amount' => $request->input('dde_amount'),
                'dde_dd_number' => $request->input('dde_number'),
                'forward_to' => $request->input('forward_to'),
                'forwarded_by' => Auth::id(),
                'original_at' => $to,
                'updated_at' => now()
            ]);

            $lastInsertedPost = Postal::findOrFail($request->input('post_id'));

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Post Forwarded to <b>" .$to. "</b> by <b>" .Auth::user()->name. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($to, 'postal', $lastInsertedPost->id, $log_description);

        }elseif($to_type == 'Staff'){

            $post_id =  "PO-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $dispatched_by = Auth::user()->name;
            $status = "Received";
            $staff_name =  $request->input('staff_name');
            $staff_id = $request->input('staff_id');

            Postal::FindorFail($request->input('post_id'))->update([
                'registrar_id' => $reg_id,
                'subject' => $subject,
                'status' => 'Forwarded',
                'staff_name' => $staff_id,
                'forward_to' => $request->input('forward_to'),
                'forwarded_by' => Auth::id(),
                'original_at' => $to,
                'updated_at' => now()
            ]);

            $lastInsertedPost = Postal::findOrFail($request->input('post_id'));

            $createdAt = Carbon::parse($lastInsertedPost->created_at);

            $formattedCreatedAt = $createdAt->format('M d, Y g:ia');

            $log_description = "Post Forwarded to <b>" .$to. "</b> by <b>" .Auth::user()->name. " at ".$formattedCreatedAt;

            DB::table('postal_logs')->insert([
                'post_id' => $lastInsertedPost->id,
                'description' => $log_description,
                'created_at' => now()
            ]);

            /*Notification to Head*/

            $notificationController = new notificationController();
            $notificationController->notificationEntry($staff_id, 'postal', $lastInsertedPost->id, $log_description);
        }

        $alert = array(
            'message' => 'Entry Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('view-post' ,['postal_id' => $post->id])->with($alert);
    }

    public function sentForwardedPosts(){

        $activeMenu = "postal_forward";
        $activeDropdown = "sent_forwarded_posts";

        $postals = PostalForwarding::where('forwarded_by', Auth::id())
                                    ->where('dispatched_by', '!=', null)
                                    ->latest()->get();

        return view('frontend.admin.postal.forward.sent_forwarded', compact('postals', 'activeMenu', 'activeDropdown'));
    }

    public function postalEntries(){

        $postals = Postal::latest()->get();

        if(Auth::user()->department == 'Registrar Office'){
            
            $postals = Postal::where('status', 'Sent to Registrar')
                                ->orWhere('status', 'Forwarded')
                                ->latest()->get();
        }

        $activeMenu = "postal";
        $activeDropdown = "entries";

        return view('frontend.postal.entries', compact('postals', 'activeMenu', 'activeDropdown'));
    }

    public function forwardDispatchedPosts(){
        
        $activeMenu = "postal_forward";
        $activeDropdown = "to_be_dispatched_posts";
        
        $postals = PostalForwarding::Where('forwarded_by', Auth::id())
                        ->Where('status', 'Received')
                        ->latest()
                        ->get();
        
        return view('frontend.admin.postal.forward.to_be_dispatched', compact('activeMenu', 'activeDropdown', 'postals'));
    }

    public function forwardDeptPosts(){
        
        $activeMenu = "postal_forward";
        $activeDropdown = "to_be_collected_posts";

        $postals = PostalForwarding::Where('forwarded_to', Auth::user()->department)
                        ->Where('status', 'Dispatched')
                        ->latest()
                        ->get();
        
        return view('frontend.admin.postal.forward.to_be_collected', compact('activeMenu', 'activeDropdown', 'postals'));
    }

    public function receivedPosts(){

        $postals = Postal::where('dispatched_by', null)->orWhere('status', 'Dispatched')->latest()->get();

        $activeMenu = "postal";
        $activeDropdown = "received_posts";

        return view('frontend.postal.receivedPosts', compact('postals', 'activeMenu', 'activeDropdown'));
    }

    public function deliveredPosts(){

        $postals = Postal::where('collected_by', '!=', null)->latest()->get();

        $activeMenu = "postal";
        $activeDropdown = "delivered_posts";

        return view('frontend.postal.deliveredPosts', compact('postals', 'activeMenu', 'activeDropdown'));
    }

    public function dispatchPostal(Request $request, $id)
    {
        $postal = Postal::findOrFail($id);
        $postal->status = 'Dispatched';
        $postal->dispatched_by = Auth::id();
        $postal->dispatched_to = $postal->sent_to;
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Dispatched',
            'dispatched_to' => $postal->dispatched_to,
            'dispatched_by' => Auth::user()->name // Assuming Auth::user()->name is available
        ]);
    }

    public function collectPostal(Request $request, $id)
    {
        $postal = Postal::findOrFail($id);
        $postal->status = 'Collected';
        $postal->delivered_by = $postal->dispatched_by;
        $postal->collected_by = Auth::id();
        $postal->updated_at = now();
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Collected',
            'collected_by' => User::FindorFail($postal->collected_by)->name, 
            'dispatched_to' => $postal->dispatched_to,
            'dispatched_by' => User::FindorFail($postal->dispatched_by)->name // Assuming Auth::user()->name is available
        ]);
    }

    public function undoDispatch(Request $request, $id)
    {
        $postal = Postal::findOrFail($id);
        $postal->status = 'Received'; // Revert to the original status
        $postal->dispatched_by = null;
        $postal->dispatched_to = null;
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Received',
            'dispatched_to' => '',
            'dispatched_by' => ''
        ]);
    }

    public function undoCollect(Request $request, $id)
    {
        $postal = Postal::findOrFail($id);
        $postal->status = 'Dispatched'; // Revert to the original status
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Dispatched',
            'dispatched_to' => $postal->dispatched_to,
            'dispatched_by' => ''
        ]);
    }

    public function outgoingPostalEntries(){

        $postals = ReplyPost::latest()->get();

        $activeMenu = "postal";
        $activeDropdown = "outgoing";

        return view('frontend.postal.out_entries', compact('postals', 'activeMenu', 'activeDropdown'));
    }

    public function changePostalStatus($postal_status, $postal_id){

        $post = Postal::findOrFail($postal_id);
        
        if($postal_status == 'Forwarded'){

            if($post->scanned_copy){

                Postal::where('id', '=', $postal_id)->update([
                    'status' => 'Collected',
                    'forwarded_by' => Auth::id(),
                    'updated_at' => now()
                ]);
        
                if(Auth::user()->role == 'SuperAdmin'){
        
                    $by = Auth::user()->department;
                }else{
        
                    $by = Auth::user()->name.', '.Auth::user()->designation;
                }
        
                    $now = Carbon::now()->format('M d, Y g:ia');
                
                    $description = "Post from ".$post->sent_by." is " .$postal_status. " by " .$by. " at " .$now;
        
                    DB::table('postal_logs')->insert([
                        'post_id' => $postal_id,
                        'description' => $description,
                        'created_at' => now()
                    ]);

                    /* Notification to HOD */
        
                    $notificationController = new notificationController();
        
                    $forwardTo = $post->forward_to;
                    
                    // Split the string into an array
                    $forwardedTo = explode(', ', $forwardTo);

                    // Trim whitespace from each element in the array to ensure clean data
                    $forwardedTo = array_map('trim', $forwardedTo);

                    $description = "Post from ".$post->sent_by." is " .$postal_status. " to you by " .$by. " at " .$now;

                    foreach($forwardedTo as $department){

                        $notificationController->notificationEntry($department, 'post', $postal_id, $description);
                    }
                    
                    $alert = array(
                        'message' => 'Post Forwarded',
                        'alert-type' => 'success'
                    );  
        
        
                return redirect()->back()->with($alert);
                
            }else{

                $alert = array(
                    'message' => 'scanned copy has to be uploaded to forward',
                    'alert-type' => 'error'
                );  

                return redirect()->back()->with($alert);
            }

        }
        
        if($postal_status == 'Sent to Registrar'){
            
            $sent_to = 'Registrar';

        }else{

            $sent_to = $post->sent_to. ' section';
        }

        Postal::where('id', '=', $postal_id)->update([
            'status' => $postal_status,
            'dispatched_to' => $sent_to,
            'delivered_by' => Auth::id(),
            'dispatched_by' => Auth::id(),
            'updated_at' => now()
        ]);

        if(Auth::user()->role == 'SuperAdmin'){

            $by = Auth::user()->department;
        }else{

            $by = Auth::user()->name.', '.Auth::user()->designation;
        }

            $now = Carbon::now()->format('M d, Y g:ia');
        
            $description = "Post from ".$post->sent_by." is " .$postal_status. " by " .$by. " at " .$now;

            DB::table('postal_logs')->insert([
                'post_id' => $postal_id,
                'description' => $description,
                'created_at' => now()
            ]);

            /* Notification to HOD */

            $notificationController = new notificationController();

            if($post->staff_name){
                $notificationController->notificationEntry($post->staff_name, 'post', $postal_id, $description);
            }else{
                $notificationController->notificationEntry($post->sent_to, 'post', $postal_id, $description);
            }

            $alert = array(
                'message' => 'Status Updated',
                'alert-type' => 'success'
            );  


        return redirect()->back()->with($alert);

    }

    public function changeRPStatus($postal_status, $postal_id){

        $post = ReplyPost::findOrFail($postal_id);
        
        ReplyPost::where('id', '=', $postal_id)->update([
            'status' => $postal_status,
            'updated_at' => now()
        ]);

        $now = Carbon::now()->format('M d, Y g:ia');
    
        $description = "Reply Post is " .$postal_status. " by " .Auth::user()->name. " at " .$now;

        if($post->post_pid){
            $post_id = $post->post_pid;
        }else{
            $post_id = $post->id;
        }

        DB::table('postal_logs')->insert([
            'post_id' => $post_id,
            'description' => $description,
            'created_at' => now()
        ]);

        /* Notification to HOD */

        $notificationController = new notificationController();
        $notificationController->notificationEntry($post->reply_by, 'post', $post_id, $description);

        $email_to = User::FindorFail($post->reply_by);

        $mail_details['content'] = "Your Out Post is " .$postal_status. " by " .Auth::user()->name. " at " .$now;
        $mail_details['url'] = URL::to('admin/personal/post/');
        
        $mail_details['title'] = 'Out Post - ' .$postal_status;
        
        $subject = 'Your Out Post is ' .$postal_status;

        // Dispatch the job to the queue
        SendTicketNotificationMail::dispatch($email_to, $mail_details, $subject, 'frontend.email.post_notifications');        

        $alert = array(
            'message' => 'Status Updated',
            'alert-type' => 'success'
        );  


        return redirect()->back()->with($alert);

    }

    public function viewPost($postal_id){

        $activeMenu = "postal";
        $activeDropdown = "posts";

        $post = Postal::FindorFail($postal_id);
        $logs = DB::table('postal_logs')->where('post_id', '=', $post->id)->get();
        $ddeDetails = DdeDetails::where('post_id', $post->id)->first();
        
        $canView = false;
        
        if($post->forward_to){
            
            //dd('stop');
            
            $forwardedTo = explode(', ', $post->forward_to);
            
            $canView = false;
            
            if (in_array(Auth::user()->department, $forwardedTo) || $post->sent_to == Auth::user()->department) {
                $canView = true;
            }
            
        }else{
            
            if($post->sent_to == Auth::user()->department){
                $canView = true;
            }
        }
        
        if(Auth::user()->role == 'SuperAdmin'){
            $canView = true;
        }

        $rps = null;

        if($post->is_responded){
            $rps = ReplyPost::where('post_pid', $post->id)->latest()->get();
        }

        Postal::FindorFail($postal_id)->update([
            'is_read' => true
        ]);
        
        if(Auth::user()->role != 'SuperAdmin' && $canView == false){
            
            $alert = array(
                'message' => 'Access Denied',
                'alert-type' => 'error'
            );  
    
    
            return redirect()->back()->with($alert);
            
        }

        //return view('frontend.postal.view', compact('post'));
        return view('frontend.admin.postal.view_post', compact('activeMenu', 'activeDropdown', 'post', 'logs', 'rps', 'ddeDetails'));

    }

    public function viewForwardPost($postal_id){

        //dd('check');

        $activeMenu = "postal";
        $activeDropdown = "posts";

        $post = Postal::FindorFail($postal_id);
        $logs = DB::table('postal_logs')->where('post_id', '=', $post->id)->get();
        $ddeDetails = DdeDetails::where('post_id', $post->id)->first();

        //dd($post);
        
        $canView = false;
        
        if($post->forward_to){
            
            //dd('stop');
            
            $forwardedTo = explode(', ', $post->forward_to);
            
            //dd($forwardedTo);
            
            $canView = false;
            
            if (in_array(Auth::user()->department, $forwardedTo) || $post->sent_to == Auth::user()->department) {
                $canView = true;
            }
            
        }else{
            
            if($post->sent_to == Auth::user()->department){
                $canView = true;
            }
        }
        
        if(Auth::user()->role == 'SuperAdmin'){
            $canView = true;
        }

        $rps = null;

        if($post->is_responded){
            $rps = ReplyPost::where('post_pid', $post->id)->latest()->get();
        }

        PostalForwarding::where('post_id', $postal_id)->where('forwarded_to', Auth::user()->department)->update([
            'is_read' => true
        ]);
        
        if(Auth::user()->role != 'SuperAdmin' && $canView == false){
            
            $alert = array(
                'message' => 'Access Denied',
                'alert-type' => 'error'
            );  
    
    
            return redirect()->back()->with($alert);
            
        }

        //return view('frontend.postal.view', compact('post'));
        return view('frontend.admin.postal.view_post', compact('activeMenu', 'activeDropdown', 'post', 'logs', 'rps', 'ddeDetails'));

    }

    public function uploadPost(Request $request){

        $post_id = $request->post_id;

        $request->validate([
            'file' => 'file|max:10240', // Example: max size 10MB
        ]);
        
        $post = Postal::FindorFail($post_id);

        if ($request->hasFile('file')){
            Log::info('Some log message', [
                'request_data' => $request->all(),
            ]);
        }

        if ($request->file('file')) {
            // Determine the folder structure based on the current year and month
            $year = date('Y');
            $month = date('m');
            $folderPath = "posts/{$year}/{$month}/{$post->sent_to}";

            Log::info('Some log message', [
                'request_data' => $request->all(),
                'folder_path' => $folderPath,
            ]);

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

        $post->update([
            'scanned_copy' => $filePath,
            'is_read' => false,
            'updated_at' => now()
        ]);

        $updatedAt = Carbon::parse($post->updated_at);
        $formattedUpdatedAt = $updatedAt->format('M d, Y g:ia');

        if (Auth::user()->role == 'SuperAdmin') {
            $updated_by = Auth::user()->name;
        } else {
            $updated_by = Auth::user()->name . ', ' . Auth::user()->designation;
        }

        $log_description = "Scanned Copy uploaded by " . $updated_by . " at " . $formattedUpdatedAt;

        DB::table('postal_logs')->insert([
            'post_id' => $post->id,
            'description' => $log_description,
            'created_at' => now()
        ]);

        $task_id = $post->id;

        /* Notification to Self */
        $notificationController = new notificationController();
        $notificationController->notificationEntry(Auth::id(), 'post', $task_id, $log_description);

        /* Notification to owner */
        if($post->staff_name){
            $notificationController->notificationEntry($post->staff_name, 'post', $task_id, $log_description);
        }else{
            $notificationController->notificationEntry($post->sent_to, 'post', $task_id, $log_description);
        }

        return response()->json(['message' => 'Success', 'status' => 'success']);
        
    }

    public function filePost(Request $request){

        $post_id = $request->input('post_id');

        $post = Postal::FindorFail($post_id);

        //Log::info('Done');

        $post->update([
            'category' => $request->input('tags'),
            'status' => 'Filed',
            'updated_at' => now()
        ]);

        $updatedAt = Carbon::parse($post->updated_at);
        $formattedUpdatedAt = $updatedAt->format('M d, Y g:ia');

        if (Auth::user()->role == 'SuperAdmin') {
            $updated_by = Auth::user()->name;
        } else {
            $updated_by = Auth::user()->name . ', ' . Auth::user()->designation;
        }

        $log_description = "Post Filed by " . $updated_by . " at " . $formattedUpdatedAt;

        DB::table('postal_logs')->insert([
            'post_id' => $post->id,
            'description' => $log_description,
            'created_at' => now()
        ]);

        $task_id = $post->id;

        /* Notification to Self */
        $notificationController = new notificationController();
        $notificationController->notificationEntry(Auth::id(), 'post', $task_id, $log_description);

        return response()->json(['message' => 'Success', 'status' => 'success']);
        
    }

    public function DeletePost($post_id){

        $post = Postal::findOrFail($post_id);

        if ($post->status == 'Received') {

            if ($post->file) {
                Storage::disk('public')->delete($post->file);
            }
            
            $post->delete();

            $alert = array(
                'message' => 'Post Deleted',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);
            
        } else {
            
            $alert = array(
                'message' => 'Delivered posts cannot be deleted!',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);

        }

    }

    public function reportIncoming(){

        $activeMenu = "postal";
        $activeDropdown = "incoming_report";

        $departments = Department::where('is_active', True)->get();

        $departmentData = [];
        $opData = [];
        
        foreach ($departments as $department) {
            // Fetch ticket counts for the current department
            $postCount = Postal::where('sent_to', $department->dept_label)
                                 ->count();

            $opCount = ReplyPost::where('reply_from', $department->dept_label)
                                    ->count();
            
            if($postCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'count' => $postCount
                ];
            }

            if($opCount != NULL){
                $opData[] = [
                    'name' => $department->dept_label,
                    'count' => $opCount
                ];
            }
        }

        return view('frontend.postal.report.index', compact('activeMenu', 'activeDropdown', 'departmentData', 'opData'));
    }

    public function incomingDownload(Request $request){

        $departmentsChartImg = $request->input('departments_chart_img');
        $opImg = $request->input('op_chart_img');
        //op_chart_img

        //dd($opImg);

        $departments = Department::where('is_active', True)->get();

        $departmentData = [];
        $opData = [];

        // Get posts created today
        $posts = Postal::latest()->get();
        $rps = ReplyPost::latest()->get();
        
        foreach ($departments as $department) {
            // Fetch ticket counts for the current department
            $postCount = Postal::where('sent_to', $department->dept_label)
                                 ->count();

            $opCount = ReplyPost::where('reply_from', $department->dept_label)
                                    ->count();
            
            if($postCount != Null){
                $departmentData[] = [
                    'name' => $department->dept_label,
                    'count' => $postCount
                ];
            }

            if($opCount != NULL){
                $opData[] = [
                    'name' => $department->dept_label,
                    'count' => $opCount
                ];
            }
        }

        // Create the PDF instance with custom options
        $pdf = PDF::loadView('frontend.postal.report.download.template', compact('departmentData', 'posts', 'departmentsChartImg', 'opData', 'rps', 'opImg'));

        //$pdf->setPaper('a4', 'portrait')->setMargins(10, 10, 10, 10);

        // Download the PDF
        return $pdf->download('Postal_incoming_report.pdf');
    }

    public function saveDdeDetails(Request $request, $post_id)
    {
        $request->validate([
            'c_code' => 'nullable|string|max:255',
            'reg_no' => 'nullable|string|max:255',
            'fee_item' => 'nullable|string|max:255',
            'mode' => 'nullable|string|max:255',
            'payment_reference_no' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'micr_code' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'receipt_no' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
        ]);
        
        if($request->has('is_fee_paid')){
            $paid = true;
        }else{
            $paid = false;
        }

        // Save the DDE details
        DdeDetails::create([
            'post_id' => $post_id,
            'c_code' => $request->input('c_code'),
            'reg_no' => $request->input('reg_no'),
            'student_name' => $request->input('student_name'),
            'is_fee_paid' => $paid,
            'fee_item' => $request->input('fee_item'),
            'mode' => $request->input('mode'),
            'payment_reference_no' => $request->input('payment_reference_no'),
            'payment_date' => $request->input('payment_date'),
            'micr_code' => $request->input('micr_code'),
            'bank_name' => $request->input('bank_name'),
            'branch' => $request->input('branch'),
            'amount' => $request->input('amount'),
            'remarks' => $request->input('remarks'),
            'receipt_no' => $request->input('receipt_no'),
            'received_date' => $request->input('received_date'),
        ]);

        return redirect()->back()->with('success', 'DDE details saved successfully.');
    }
    
    public function updateDDEDetails(Request $request, $post_id, $dde_id)
    {
        $dde = DdeDetails::findOrFail($dde_id);
        $dde->reg_no = $request->reg_no;
        $dde->student_name = $request->student_name;
        $dde->c_code = $request->c_code;
        $dde->fee_item = $request->fee_item;
        $dde->mode = $request->mode;
        $dde->payment_reference_no = $request->payment_reference_no;
        $dde->payment_date = $request->payment_date;
        $dde->micr_code = $request->micr_code;
        $dde->is_fee_paid = $request->has('is_fee_paid');
        $dde->bank_name = $request->bank_name;
        $dde->branch = $request->branch;
        $dde->amount = $request->amount;
        $dde->remarks = $request->remarks;
        $dde->receipt_no = $request->receipt_no;
        $dde->received_date = $request->received_date;
        $dde->save();

        return redirect()->back()->with('success', 'DDE Details updated successfully.');
    }
}
