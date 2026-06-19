<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HODController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketForwardingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PostalController;
use App\Http\Controllers\PostalForwardingController;
use App\Http\Controllers\DocumentApprovalController;
use App\Http\Controllers\DdeDetailsController;
use App\Http\Controllers\FoController;
use App\Http\Controllers\ChairmanController;
use App\Http\Controllers\UnifiedDashboardController;
use App\Http\Controllers\DocumentDashboardController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\CrossLoginController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::get('/documentation', function () {
    return view('frontend.documentation');
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

Route::get('/maintenance-down', function () {
    Artisan::call('down', [
        '--secret' => 'Dep-San' // optional: lets you bypass maintenance with /?secret=my-secret-key
    ]);

    return "App is now in maintenance mode.";
});

Route::get('/maintenance-up', function () {
    Artisan::call('up');
    return "App is now live again.";
});

Route::get('/debug-db-info', function() {
    return [
        'connection_name' => DB::connection()->getName(),
        'database_name' => DB::connection()->getDatabaseName(),
        'table_exists' => Schema::hasTable('users'),
        'has_deleted_at' => Schema::hasColumn('users', 'deleted_at'),
        'user_count' => DB::table('users')->count(),
        'test_user' => DB::table('users')->where('email', 'dydirector.it@vmu.edu.in')->first()
    ];
});

Route::get('/forget-password', [ForgetPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::get('/forget-password/{email}', [ForgetPasswordController::class, 'showForgetPasswordFormAgain'])->name('forget.password.get.again');
Route::post('/forget-password/reset-link', [ForgetPasswordController::class, 'submitForgetPasswordFormAgain'])->name('forget.password.again'); 
Route::post('/forget-password', [ForgetPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('/reset-password-form/{token}', [ForgetPasswordController::class, 'showResetPasswordForm'])->name('reset.password');
Route::post('/reset-password-form', [ForgetPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');


Route::post('/auth/login', [AuthController::class, 'login'])->name('auth_login');
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth_logout');

Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('show_register_page');
Route::post('/auth/register', [AuthController::class, 'registerUser'])->name('auth_register_user');

// ── Unified Dashboard + Approval Flowchart — all authenticated users ────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UnifiedDashboardController::class, 'index'])->name('unified_dashboard');
    Route::get('/document/approval-flow', function () {
        return view('frontend.document.approval_flowchart');
    })->name('approval_flowchart');
    Route::get('/document/approval-flow/download', [DocumentApprovalController::class, 'downloadFlowchart'])->name('approval_flowchart_download');
});

Route::middleware(['auth', 'hod'])->group(function (){

    Route::get('/admin/dashboard', [DocumentDashboardController::class, 'index'])->name('admin_dashboard');
    Route::get('/fo/dashboard', [FoController::class, 'Dashboard_FO'])->name('fo_dashboard');
    Route::get('/fo-dashboard-documents/export', [FoController::class, 'exportForwardedDocuments'])
                        ->name('download.fo.dashboard.doc.excel');

    Route::get('/admin/tickets/summary', [HODController::class, 'ticketsSummary'])->name('admin_tickets_summary');
    Route::get('/admin/tickets/open', [HODController::class, 'adminOpenTickets'])->name('admin_open_tickets');
    Route::get('/admin/tickets/in-progress', [HODController::class, 'adminTicketsInProgress'])->name('admin_tickets_in_progress');
    Route::get('/admin/tickets/closed', [HODController::class, 'adminClosedTickets'])->name('admin_closed_tickets');
    Route::get('/admin/tickets/hold', [HODController::class, 'adminTicketsOnHold'])->name('admin_tickets_on_hold');
    Route::get('/admin/tickets/completed', [HODController::class, 'adminTicketsCompleted'])->name('admin_completed_tickets');
    Route::get('/admin/tickets/total', [HODController::class, 'adminTotalTickets'])->name('admin_total_tickets');
    
    Route::get('/admin/tickets/unassigned', [HODController::class, 'adminUnassignedTickets'])->name('admin_unassigned_tickets');
    Route::get('/admin/tickets/self-assigned', [HODController::class, 'adminSelfAssignedTickets'])->name('admin_self_tickets');
    

    Route::get('/admin/tickets/create', [HODController::class, 'createTicket'])->name('admin_create_ticket');
    
    
    Route::get('/admin/tickets/my-tickets', [HODController::class, 'adminMyTickets'])->name('admin_my_tickets');
    
    Route::get('/admin/dept/tickets', [HODController::class, 'adminDeptTickets'])->name('admin_dept_tickets');
    Route::get('/admin/unapproved/tickets', [HODController::class, 'adminUnapprovedTickets'])->name('admin_unapproved_tickets');
    
    Route::get('/admin/view/ticket/{ticketId}', [TicketController::class, 'viewTicket']);
    
    //Route::get('/notification/redirect/{task_type}/{task_id}/{notification_id}', [NotificationController::class, 'notificationRedirect'])->name('notification-redirect');
    //Route::get('/admin/notifications/all', [NotificationController::class, 'adminNotifications'])->name('admin-notifications');

    Route::get('/admin/forwarded/tickets', [HODController::class, 'adminForwardedTickets'])->name('admin-forwarded-report');

    Route::get('/admin/tickets/report', [HODController::class, 'adminTicketsReport'])->name('admin-ticket-report');
    Route::post('/admin/download/report', [HODController::class, 'adminReportDownload'])->name('generate_report');

    Route::post('/admin/specific/tickets/report', [HODController::class, 'adminSpecificTicketsReport'])->name('specific_report');
    Route::post('/admin/download/specific/report', [HODController::class, 'adminSpecificReportDownload'])->name('generate_specific_report');

    Route::get('/admin/tasks/priority/high', [HODController::class, 'adminHighPriorityTasks'])->name('high-priority-tasks');

    /* Staffs Master */

    Route::get('/admin/add/staffs', [HODController::class, 'addStaffs'])->name('add-staffs');
    Route::post('/admin/store/staffs', [HODController::class, 'storeStaffs'])->name('store-staffs');
    Route::get('/admin/view/staffs', [HODController::class, 'viewStaffs'])->name('view-staffs');
    Route::get('/admin/view/ex/staffs', [HODController::class, 'exStaffs'])->name('ex-staffs');
    Route::get('/admin/edit/staff/{id}', [HODController::class, 'editStaff'])->name('edit-staff');
    Route::post('/admin/update/staff', [HODController::class, 'updateStaff'])->name('update-staff');
    Route::get('/admin/delete/staff/{id}', [HODController::class, 'deleteStaff'])->name('delete-staff');
    Route::get('/admin/recover/staff/{id}', [HODController::class, 'recoverStaff'])->name('recover-staff');
    Route::get('/admin/change/account/status/{id}', [HODController::class, 'changeAccountStatus'])->name('change-account-status');

    /* Dept Master */

    Route::get('/admin/add/depts', [DepartmentController::class, 'addDepts'])->name('add-depts');
    Route::post('/admin/store/depts', [DepartmentController::class, 'storeDepts'])->name('store-depts');
    Route::get('/admin/view/depts', [DepartmentController::class, 'viewDepts'])->name('view-depts');
    Route::get('/admin/view/ex/depts', [DepartmentController::class, 'exDepts'])->name('ex-depts');
    Route::get('/admin/edit/depts/{id}', [DepartmentController::class, 'editDept'])->name('edit-dept');
    Route::post('/admin/update/depts', [DepartmentController::class, 'updateDept'])->name('update-dept');
    Route::get('/admin/delete/depts/{id}', [DepartmentController::class, 'deleteDept'])->name('delete-dept');
    Route::get('/admin/recover/depts/{id}', [DepartmentController::class, 'recoverDept'])->name('recover-dept');
    Route::get('/admin/change/account/depts/{id}', [DepartmentController::class, 'changeDeptStatus'])->name('change-dept-status');

    Route::get('/admin/dept/post', [HODController::class, 'deptPosts'])->name('admin_dept_post');
    Route::get('/admin/personal/post', [HODController::class, 'personalPosts'])->name('admin_personal_post');
    /* Document Approval */

});

// Chairman dashboard — accessible to Chairman dept users (SuperAdmin role) + actual SuperAdmins
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/chairman/dashboard', [DocumentDashboardController::class, 'index'])->name('chairman_dashboard');
});

// ITAdmin routes — accessible to ITAdmin + SuperAdmin
Route::middleware(['auth', 'itadmin'])->group(function () {
    Route::get('/itadmin/dashboard', [DocumentDashboardController::class, 'index'])->name('itadmin_dashboard');
    Route::get('/itadmin/api/tokens', [ApiTokenController::class, 'index'])->name('api.tokens.index');
    Route::post('/itadmin/api/tokens', [ApiTokenController::class, 'store'])->name('api.tokens.store');
    Route::delete('/itadmin/api/tokens/{id}', [ApiTokenController::class, 'destroy'])->name('api.tokens.destroy');
});

Route::middleware(['auth', 'admin'])->group(function (){

    Route::get('/superadmin/dashboard', [DocumentDashboardController::class, 'index'])->name('super_admin_dashboard');

    /* Dept Specific */

    Route::get('/superadmin/tickets/recieved/summary', [AdminController::class, 'recievedTicketsSummary'])->name('super_admin_recieved_tickets_summary');
    Route::get('/superadmin/tickets/recieved/open', [AdminController::class, 'adminRecievedOpenTickets'])->name('super_admin_recieved_open_tickets');
    Route::get('/superadmin/tickets/recieved/in-progress', [AdminController::class, 'adminRecievedTicketsInProgress'])->name('super_admin_recieved_tickets_in_progress');
    Route::get('/superadmin/tickets/recieved/completed', [AdminController::class, 'adminRecievedCompletedTickets'])->name('super_admin_recieved_completed_tickets');
    Route::get('/superadmin/tickets/recieved/closed', [AdminController::class, 'adminRecievedClosedTickets'])->name('super_admin_recieved_closed_tickets');
    Route::get('/superadmin/tickets/recieved/hold', [AdminController::class, 'adminRecievedTicketsOnHold'])->name('super_admin_recieved_tickets_on_hold');
    Route::get('/superadmin/tickets/recieved/total', [AdminController::class, 'adminRecievedTotalTickets'])->name('super_admin_recieved_total_tickets');
    
    Route::get('/superadmin/tickets/recieved/unassigned', [AdminController::class, 'adminRecievedUnassignedTickets'])->name('super_admin_recieved_unassigned_tickets');
    Route::get('/superadmin/tickets/self-recieved/assigned', [AdminController::class, 'adminRecievedSelfAssignedTickets'])->name('super_admin_recieved_self_tickets');

    Route::get('/superadmin/tickets/create', [AdminController::class, 'superAdminCreateTicket'])->name('super_admin_create_ticket');

    Route::get('/superadmin/unapproved/tickets', [AdminController::class, 'superAdminUnapprovedTickets'])->name('super_admin_unapproved_tickets');

    Route::get('/superadmin/tickets/my-tickets', [AdminController::class, 'superAdminMyTickets'])->name('super_admin_my_tickets');
    
    Route::get('/superadmin/dept/tickets', [AdminController::class, 'superAdminDeptTickets'])->name('super_admin_dept_tickets');

    Route::get('/superadmin/view/ticket/{ticketId}', [TicketController::class, 'viewTicket']);

    Route::get('/superadmin/forwarded/tickets', [AdminController::class, 'superAdminForwardedTickets'])->name('superadmin-forwarded-ticket');

    Route::get('/superadmin/tickets/report', [AdminController::class, 'superAdminDeptTicketsReport'])->name('super-admin-dept-ticket-report');
    Route::post('/superadmin/download/report', [AdminController::class, 'superAdminDeptTicketReportDownload'])->name('super_admin_generate_dept_ticket_report');

    Route::post('/superadmin/specific/tickets/report', [AdminController::class, 'superAdminSpecificDeptTicketsReport'])->name('super_admin_specific_dept_ticket_report');
    Route::post('/superadmin/download/specific/report', [AdminController::class, 'superAdminSpecificDeptTicketReportDownload'])->name('super_admin_generate_specific_dept_ticket_report');

    Route::get('/superadmin/add/staffs', [AdminController::class, 'addStaffs'])->name('super-admin-add-staffs');
    Route::post('/superadmin/store/staffs', [AdminController::class, 'storeStaffs'])->name('super-admin-store-staffs');
    Route::get('/superadmin/view/staffs', [AdminController::class, 'viewStaffs'])->name('super-admin-view-staffs');
    Route::get('/superadmin/view/ex/staffs', [AdminController::class, 'exStaffs'])->name('super-admin-ex-staffs');
    Route::get('/superadmin/edit/staff/{id}', [AdminController::class, 'editStaff'])->name('super-admin-edit-staff');
    Route::post('/superadmin/update/staff', [AdminController::class, 'updateStaff'])->name('super-admin-update-staff');
    Route::get('/superadmin/delete/staff/{id}', [AdminController::class, 'deleteStaff'])->name('super-admin-delete-staff');
    Route::get('/superadmin/recover/staff/{id}', [AdminController::class, 'recoverStaff'])->name('super-admin-recover-staff');
    Route::get('/superadmin/change/account/status/{id}', [AdminController::class, 'changeAccountStatus'])->name('super-admin-change-account-status');

    /* Report */

    Route::get('/superadmin/sa/tickets/summary', [AdminController::class, 'superAdminTicketsSummary'])->name('super_admin_tickets_summary');
    Route::get('/superadmin/sa/tickets/open', [AdminController::class, 'superAdminOpenTickets'])->name('super_admin_open_tickets');
    Route::get('/superadmin/sa/tickets/closed', [AdminController::class, 'superAdminClosedTickets'])->name('super_admin_closed_tickets');
    Route::get('/superadmin/sa/tickets/hold', [AdminController::class, 'superAdminTicketsOnHold'])->name('super_admin_tickets_on_hold');
    Route::get('/superadmin/sa/tickets/in-progress', [AdminController::class, 'superAdminTicketsInProgress'])->name('super_admin_tickets_in_progress');
    Route::get('/superadmin/sa/tickets/completed', [AdminController::class, 'superAdminCompletedTickets'])->name('super_admin_completed_tickets');
    Route::get('/superadmin/sa/tickets/total', [AdminController::class, 'superAdminTotalTickets'])->name('super_admin_total_tickets');
    
    Route::get('/superadmin/tickets/recieved/pending', [AdminController::class, 'adminRecievedPendingTickets'])->name('super_admin_pending_tickets');
    
    Route::get('/superadmin/sa/tickets/unassigned', [AdminController::class, 'superAdminUnassignedTickets'])->name('super_admin_unassigned_tickets');

    Route::get('/superadmin/sa/tickets/report', [AdminController::class, 'superAdminTicketsReport'])->name('super-admin-ticket-report');
    Route::post('/superadmin/sa/download/report', [AdminController::class, 'superAdminReportDownload'])->name('super_admin_generate_report');

    Route::post('/superadmin/sa/specific/tickets/report', [AdminController::class, 'superAdminSpecificTicketsReport'])->name('super_admin_specific_report');
    Route::post('/superadmin/sa/download/specific/report', [AdminController::class, 'superAdminSpecificReportDownload'])->name('super_admin_generate_specific_report');

    Route::get('/superadmin/tasks/priority/high', [AdminController::class, 'superAdminHighPriorityTasks'])->name('superadmin-high-priority-tasks');

    Route::get('/superadmin/dept/post', [AdminController::class, 'deptPosts'])->name('superadmin_dept_post');
    Route::get('/superadmin/personal/post', [AdminController::class, 'personalPosts'])->name('superadmin_personal_post');

});

Route::middleware(['auth', 'staff'])->group(function (){

    Route::get('/staff/dashboard', [DocumentDashboardController::class, 'index'])->name('staff_dashboard');
    Route::get('/staff/tickets/open', [StaffController::class, 'staffOpenTickets'])->name('staff_open_tickets');
    Route::get('/staff/tickets/hold', [StaffController::class, 'staffHoldTickets'])->name('staff_hold_tickets');
    Route::get('/staff/tickets/closed', [StaffController::class, 'staffClosedTickets'])->name('staff_closed_tickets');
    Route::get('/staff/tickets/completed', [StaffController::class, 'staffCompletedTickets'])->name('staff_completed_tickets');
    Route::get('/staff/tickets/in-progress', [StaffController::class, 'staffTicketsInProgress'])->name('staff_tickets_in_progress');
    Route::get('/staff/tickets/total', [StaffController::class, 'staffTotalTickets'])->name('staff_total_tickets');
    Route::get('/staff/view/ticket/{ticketId}', [StaffController::class, 'staffViewTicket']);
    //Route::get('/change/ticket/status/{ticket_status}/{ticket_id}', [TicketController::class, 'changeTicketStatus'])->name('change.ticket.status');
    
    Route::get('/staff/tickets/create', [staffController::class, 'createTicket'])->name('staff_create_ticket');
    
    Route::get('/staff/unapproved/tickets', [StaffController::class, 'staffUnapprovedTickets'])->name('staff_unapproved_tickets');

    Route::get('/staff/forwarded/tickets', [StaffController::class, 'staffForwardedTickets'])->name('staff-forwarded-report');

    Route::get('/staff/tickets/my-tickets', [staffController::class, 'staffMyTickets'])->name('staff_my_tickets');

    Route::get('/staff/tickets/report', [StaffController::class, 'staffTicketsReport'])->name('staff-ticket-report');
    Route::post('/staff/download/report', [StaffController::class, 'staffReportDownload'])->name('staff_generate_report');

    Route::post('/staff/specific/tickets/report', [StaffController::class, 'staffSpecificTicketsReport'])->name('staff_specific_report');
    Route::post('/staff/download/specific/report', [StaffController::class, 'staffSpecificReportDownload'])->name('staff_generate_specific_report');

    Route::get('/staff/personal/post', [StaffController::class, 'personalPosts'])->name('staff_personal_post');

    Route::get('/staff/documents/payment/new', [StaffController::class, 'paymentNewDocuments'])->name('staffpayment.new');
    Route::get('/staff/documents/payment/in-progress', [StaffController::class, 'paymentInProgressDocuments'])->name('staffpayment.in_progress');
    Route::get('/staff/documents/payment/hold', [StaffController::class, 'paymentHoldDocuments'])->name('staffpayment.hold');
    Route::get('/staff/documents/payment/completed', [StaffController::class, 'paymentCompletedDocuments'])->name('staffpayment.completed');

    Route::get('/staff/payment/advance', [StaffController::class, 'advanceRequests'])->name('staffpayment.advance');
    Route::get('/staff/payment/full', [StaffController::class, 'fullRequests'])->name('staffpayment.full');


});

Route::middleware(['auth', 'postal'])->group(function (){

    Route::get('/postal/add/entry', [PostalController::class, 'createPost'])->name('postal_add_entry');
    Route::get('/postal/re-edit/entry/{postal_id}', [PostalController::class, 'reEditPost'])->name('postal_reEdit_entry');
    Route::post('/postal/store/entry', [PostalController::class, 'storePost'])->name('postal_store_entry');
    Route::post('/postal/store/re-edit/entry', [PostalController::class, 'storeReEditPost'])->name('postal_store_re-edit_entry');

    Route::get('/postal/view/received', [PostalController::class, 'receivedPosts'])->name('received_posts');
    
    Route::post('/postal/dispatch/{id}', [PostalController::class, 'dispatchPostal'])->name('postal.dispatch');
    Route::post('/postal/undo/{id}', [PostalController::class, 'undoDispatch'])->name('postal.undo');

    Route::get('/postal/view/delivered', [PostalController::class, 'deliveredPosts'])->name('delivered_posts');

    Route::get('/replyPost/add/tracking_id/{rp_id}', [PostalController::class, 'replyPostTrackingIDUpdate'])->name('outPost_trackingID');
    Route::post('/replyPost/add/tracking_id', [PostalController::class, 'replyPostAddTrackingID'])->name('rp_add_track_id');

    Route::get('/postal/view/entry', [PostalController::class, 'postalEntries'])->name('postal_entries');
    Route::get('/postal/outgoing/entries', [PostalController::class, 'outgoingPostalEntries'])->name('outgoing_postal_entries');

    Route::get('/change/rp/status/{postal_status}/{postal_id}', [PostalController::class, 'changeRPStatus'])->name('change.rp.status');

    Route::get('/postal/view/report', [PostalController::class, 'reportIncoming'])->name('postal_report');

    Route::any('/postal/download', [PostalController::class, 'incomingDownload'])->name('postal_report_download');

});

Route::middleware('auth')->group(function () {

    Route::get('/search-departments', [DepartmentController::class, 'searchDepartments'])->name('search.departments');
    Route::get('/search-staff', [PostalController::class, 'searchStaff'])->name('search.staff');

    Route::post('/upload/post', [PostalController::class, 'uploadPost'])->name('postal_upload_entry');
    
    /*Post In Entry*/
    Route::get('/postal/in/entry', [PostalController::class, 'createInPost'])->name('postal_add_in_entry');
    Route::post('/postal/store/in/entry', [PostalController::class, 'storeInPost'])->name('postal_store_in_entry');

    Route::post('/categorize/post/file', [PostalController::class, 'filePost'])->name('postal_file_entry');

    Route::get('/postal/edit/entry/{postal_id}', [PostalController::class, 'editPost'])->name('postal_edit_entry');
    Route::post('/postal/update/entry', [PostalController::class, 'updatePost'])->name('postal_update_entry');

    Route::post('/tickets/store', [TicketController::class, 'storeTicket'])->name('store_ticket');
    Route::get('/edit/ticket/{id}', [TicketController::class, 'editTicket']);
    Route::post('/tickets/update', [TicketController::class, 'updateTicket'])->name('update_ticket');

    Route::get('/approve/ticket/{id}', [TicketController::class, 'adminApproveTicket']);

    Route::post('/forward/ticket', [TicketForwardingController::class, 'forwardTicket']);
    Route::get('/assign/forwarded/ticket/{emp_id}/{forwarded_id}', [TicketForwardingController::class, 'assignForwardedTicket']);
    
    Route::get('/change/priority/ticket/{ticket_id}/{priority}', [TicketController::class, 'changeTicketPriority']);
    
    Route::get('/notification/redirect/{task_type}/{task_id}/{notification_id}', [NotificationController::class, 'notificationRedirect'])->name('notification-redirect');

    Route::get('/change/ticket/status/{ticket_status}/{ticket_id}', [TicketController::class, 'changeTicketStatus'])->name('change.ticket.status');

    Route::get('/activity-log/{ticketId}', [TicketController::class, 'showLog']);

    Route::get('/assign/ticket/{emp_id}/{ticket_id}', [TicketController::class, 'assignTicket']);
    Route::post('/close/ticket/', [TicketController::class, 'closeTicket'])->name('close-ticket');    
    
    Route::post('/view/tickets/reply', [TicketController::class, 'respondTicket'])->name('ticket-respond');
    
    // Route::get('/tickets/create', [TicketController::class, 'createTicket'])->name('create_ticket');
    // Route::get('/tickets/my-tickets', [HodController::class, 'adminMyTickets'])->name('my_tickets');
    
    Route::get('/ping/alert/{task_type}/{task_id}/{ping_to}', [PingController::class, 'PingAlert'])->name('ping-alert');

    Route::get('/delete/ticket/{ticket_id}', [TicketController::class, 'DeleteTicket'])->name('delete_ticket');
    Route::get('notification/mark/read/all', [NotificationController::class, 'notificationMarkAllRead'])->name('notification-mark-all_read');
    Route::get('/system/notifications/all', [NotificationController::class, 'Notifications'])->name('notifications');

    Route::get('/notification/mark/{notification_id}', [NotificationController::class, 'NotificationsMark'])->name('notifications-mark');

    Route::get('/system/pings/all', [PingController::class, 'Pings'])->name('pings');

    Route::get('/das/user/profile', [UserController::class, 'Profile'])->name('user-profile');
    Route::post('/das/update/profile', [UserController::class, 'updateProfile'])->name('update-profile');
    
    Route::get('/das/user/manual', [UserController::class, 'Manual'])->name('user-manual');
    Route::get('/das/signature/demo', [UserController::class, 'signatureDemo'])->name('signature-demo');

    Route::any('/user/download/ticket/{ticketID}', [HODController::class, 'adminTicketDownload'])->name('download-ticket');

    Route::get('/post/view/{postal_id}', [PostalController::class, 'viewPost'])->name('view-post');
    Route::get('/forward/post/view/{postal_id}', [PostalController::class, 'viewForwardPost'])->name('view-forward-post');
    Route::get('/delete/post/{post_id}', [PostalController::class, 'DeletePost'])->name('delete_post');

    Route::get('/postal/reply/{post_id}', [PostalController::class, 'replyPost'])->name('reply-post');
    Route::post('/postal/reply/entry', [PostalController::class, 'postalReplyEntry'])->name('postal_reply_entry');

    Route::post('/forward/postal/dispatch/{id}', [PostalForwardingController::class, 'dispatchPostal'])->name('forward.postal.dispatch');
    Route::post('/forward/postal/undo/{id}', [PostalForwardingController::class, 'undoDispatch'])->name('forward.postal.undo');
    
    Route::get('/view/document/{doc_id}', [DocumentApprovalController::class, 'viewDocument']);

    Route::post('/change/document/status', [DocumentApprovalController::class, 'changeDocumentStatus']);

    Route::get('/change/postal/status/{postal_status}/{postal_id}', [PostalController::class, 'changePostalStatus'])->name('change.postal.status');

    Route::post('/close/postal/status', [PostalController::class, 'closePostalStatus'])->name('close.postal.status');

    Route::get('/admin/dispatched/post', [HODController::class, 'dispatchedPosts'])->name('dispatched_posts');
    Route::get('/admin/collected/post', [HODController::class, 'deptPosts'])->name('collected_posts');
    Route::get('/admin/collected/post/dated', [HODController::class, 'datedDeptPosts'])->name('dept-posts');
    Route::get('/admin/collected/forwardedpost/dated', [HODController::class, 'datedForwardedPosts'])->name('forwarded-posts');
    Route::get('/admin/search/posts', [HODController::class, 'searchPosts'])->name('search-posts');

    Route::get('/forward/dispatched/post', [PostalController::class, 'forwardDispatchedPosts'])->name('to_be_dispatched_posts');
    Route::get('/forward/collected/post', [PostalController::class, 'forwardDeptPosts'])->name('to_be_collected_posts');
    
    Route::post('/postal/collect/{id}', [PostalController::class, 'collectPostal'])->name('postal.collect');
    Route::post('/postal/undo/collect/{id}', [PostalController::class, 'undoCollect'])->name('postal.undo.collect');

    Route::post('/forward/postal/collect/{id}', [PostalForwardingController::class, 'collectPostal'])->name('forward.postal.collect');
    Route::post('/forward/postal/undo/collect/{id}', [PostalForwardingController::class, 'undoCollect'])->name('forward.postal.undo.collect');

    Route::get('/admin/download/document/{doc_id}', [DocumentApprovalController::class, 'downloadDocument'])->name('download_document');
    Route::get('/admin/download/report/', [DocumentApprovalController::class, 'downloadReport'])->name('download_report_doc');
    Route::get('/admin/download/forwardedDoc/report/', [DocumentApprovalController::class, 'downloadForwardedDocReport'])->name('download_report_forwarded_doc');

    Route::get('/forwarded/post', [HODController::class, 'forwardedPosts'])->name('admin_forwarded_post');
    Route::get('/sent/forwarded/post', [PostalController::class, 'sentForwardedPosts'])->name('admin_sent_forwarded_post');

    Route::post('/postal/save-due-date/{post_id}', [PostalController::class, 'saveDueDate'])->name('save_due_date');
    Route::post('/postal/close-post/{post_id}', [PostalController::class, 'closePost'])->name('close_post');
    Route::post('/postal/save-dde-details/{post_id}', [PostalController::class, 'saveDdeDetails'])->name('save_dde_details');
    Route::put('/admin/postal/dde-details/update/{post_id}/{dde_id}', [PostalController::class, 'updateDDEDetails'])->name('update_dde_details');
    Route::get('/dde-details', [DdeDetailsController::class, 'index'])->name('dde_details.index');
    
    /* Out Entry */
    Route::get('/postal/out/entry', [PostalController::class, 'createOutPost'])->name('postal_add_out_entry');
    Route::post('/postal/store/out/entry', [PostalController::class, 'storeOutPost'])->name('postal_store_out_entry');
    
    /* Document Approval */
    Route::get('/superadmin/documents/received', [DocumentApprovalController::class, 'receivedDocuments'])->name('received_documents');
    Route::get('/superadmin/documents/all', [DocumentApprovalController::class, 'totalDocuments'])->name('total_documents');
    Route::get('/superadmin/documents/drafts', [DocumentApprovalController::class, 'draftDocuments'])->name('draft_documents');
    Route::get('/superadmin/documents/new', [DocumentApprovalController::class, 'newDocuments'])->name('new_documents');
    Route::get('/superadmin/documents/inProgress', [DocumentApprovalController::class, 'inProgressDocuments'])->name('inProgress_documents');
    Route::get('/superadmin/documents/pending', [DocumentApprovalController::class, 'pendingDocuments'])->name('pending_documents');
    Route::get('/superadmin/documents/rejected', [DocumentApprovalController::class, 'rejectedDocuments'])->name('rejected_documents');
    Route::get('/superadmin/documents/closed', [DocumentApprovalController::class, 'closedDocuments'])->name('closed_documents');
    Route::get('/superadmin/documents/approved', [DocumentApprovalController::class, 'approvedDocuments'])->name('approved_documents');
    Route::post('/delete/document/', [DocumentApprovalController::class, 'DeleteDocument'])->name('delete_document');
    Route::get('/deleted/document/admin/deep', [DocumentApprovalController::class, 'deletedDocuments'])->name('deleted_documents');
    
    Route::get('/admin/document/create/{ticket_id?}', [DocumentApprovalController::class, 'create'])->name('create_document');
    Route::get('/admin/document/edit/{doc_id}', [DocumentApprovalController::class, 'edit'])->name('edit_document');
    Route::post('/admin/document/send', [DocumentApprovalController::class, 'store'])->name('add_document_for_approval');
    Route::post('/admin/document/update/{id}', [DocumentApprovalController::class, 'update'])->name('document.update');
    Route::delete('/remove-annexure/{id}', [DocumentApprovalController::class, 'removeAnnexure'])->name('remove.annexure');

    Route::get('/admin/myDocuments/view', [DocumentApprovalController::class, 'myDocuments'])->name('my_documents');
    Route::get('/search/documents', [DocumentApprovalController::class, 'searchDocuments'])->name('search_documents');
    Route::get('/search/forwarded-documents', [DocumentApprovalController::class, 'searchForwardedDocuments'])->name('search_forwarded_documents');
    Route::get('/admin/forwardedDocuments/view', [DocumentApprovalController::class, 'forwardedDocuments'])->name('forwarded_documents');
    Route::get('/admin/completedDocuments/view', [DocumentApprovalController::class, 'completedDocuments'])->name('completed_documents');

    Route::get('/admin/documents/report', [DocumentApprovalController::class, 'reportDoc'])->name('report-doc');
    Route::get('/admin/documents/approval-report', [DocumentApprovalController::class, 'approvalReport'])->name('approval-report');
    Route::get('/admin/documents/approval-report/pdf', [DocumentApprovalController::class, 'downloadApprovalReportPDF'])->name('approval-report.pdf');
    Route::get('/admin/documents/approval-report/excel', [DocumentApprovalController::class, 'downloadApprovalReportExcel'])->name('approval-report.excel');
    Route::get('/superadmin/search/documents', [DocumentApprovalController::class, 'searchSADocuments'])->name('sa_search_documents');
    
    Route::get('/admin/financeOfficer/payment-details', [DocumentApprovalController::class, 'showAllPayments'])->name('payment.details');
    
    Route::get('/admin/financeOfficer/edit/payment-details/{id}', [DocumentApprovalController::class, 'editPaymentDetails'])->name('edit-payment-details');
    Route::put('/update-payment-details/{id}', [DocumentApprovalController::class, 'updatePaymentDetails'])->name('update-payment-details');
    
    Route::get('/admin/financeOfficer/delete/payment-details/{id}', [DocumentApprovalController::class, 'deletePaymentDetails'])->name('delete-payment-details');

    Route::get('/download/forwarded-doc-excel', [DocumentApprovalController::class, 'downloadForwardedDocExcel'])->name('download.forwarded.doc.excel');

    Route::get('payment-details/download-pdf', [DocumentApprovalController::class, 'downloadPaymentDetailsPDF'])->name('download_payment_details_pdf');
    Route::get('payment-details/download-excel', [DocumentApprovalController::class, 'downloadPaymentDetailsExcel'])->name('download_payment_details_excel');

    Route::get('/change/request/status/{status}/{finance_id}', [DocumentApprovalController::class, 'changeFinanceStatus'])->name('change.finance.status');

    Route::get('/documents/payment/new', [DocumentApprovalController::class, 'paymentNewDocuments'])->name('payment.new');
    Route::get('/documents/payment/in-progress', [DocumentApprovalController::class, 'paymentInProgressDocuments'])->name('payment.in_progress');
    Route::get('/documents/payment/hold', [DocumentApprovalController::class, 'paymentHoldDocuments'])->name('payment.hold');
    Route::get('/documents/payment/completed', [DocumentApprovalController::class, 'paymentCompletedDocuments'])->name('payment.completed');
    Route::get('/documents/payment/advance', [DocumentApprovalController::class, 'advancePaymentDocuments'])->name('payment.advance');
    Route::get('/documents/payment/full', [DocumentApprovalController::class, 'fullPaymentDocuments'])->name('payment.full');

    Route::get('/superadmin/documents/summary/yet-to-approve/{role}', [DocumentApprovalController::class, 'approvalSummary'])->name('summary_documents');
    Route::get('/superadmin/documents/summary/approved/{role}', [DocumentApprovalController::class, 'summaryApproved'])->name('summary_approved');
    Route::get('/superadmin/documents/summary/approved-in-principle/{role}', [DocumentApprovalController::class, 'summaryApprovedInPrinciple'])->name('summary_approved_in_principle');
    Route::get('/superadmin/documents/summary/hold/{role}', [DocumentApprovalController::class, 'summaryHold'])->name('summary_hold');
    Route::get('/superadmin/documents/summary/rejected/{role}', [DocumentApprovalController::class, 'summaryRejected'])->name('summary_rejected');
    Route::get('/superadmin/documents/summary/pending/{role}', [DocumentApprovalController::class, 'summaryPending'])->name('summary_pending');
    Route::get('/superadmin/documents/summary/discussion/{role}', [DocumentApprovalController::class, 'summaryDiscussion'])->name('summary_discussion');
    Route::get('/superadmin/documents/summary/nat/{role}', [DocumentApprovalController::class, 'summaryNoActionTaken'])->name('summary_nat');
    Route::get('/superadmin/documents/summary/new/{role}', [DocumentApprovalController::class, 'summaryNew'])->name('summary_new');
    Route::get('/superadmin/documents/summary/commented/{role}', [DocumentApprovalController::class, 'summaryCommentedByVc'])->name('summary_commented');
    Route::get('/superadmin/documents/summary/forwarded/{role}', [DocumentApprovalController::class, 'summaryForwardedByVc'])->name('summary_forwarded');


    Route::get('/fo/staff/{staff}/report',[FOController::class, 'staffDetailedReport'])
        ->name('fo.staff.report');
    Route::post('/change/document/status', [DocumentApprovalController::class, 'changeDocumentStatus'])->name('change.document.status');


});

Route::middleware('auth')->prefix('fo/documents')->name('fo.documents.')->group(function () {

    Route::get('/new', [FoController::class, 'new'])
        ->name('new');

    Route::get('/assigned', [FoController::class, 'assigned'])
        ->name('assigned');

    Route::get('/in-progress', [FoController::class, 'inProgress'])
        ->name('inProgress');

    Route::get('/completed', [FoController::class, 'completed'])
        ->name('completed');

    Route::get('/total', [FoController::class, 'total'])
        ->name('total');

    Route::get('/payment/full', [FoController::class, 'fullPayment'])
        ->name('fullPayment');

    Route::get('/payment/advance', [FoController::class, 'advancePayment'])
        ->name('advancePayment');

    Route::get('/staff/{staff}', [FoController::class, 'staffWise'])
        ->name('staff');

    

});

Route::middleware('auth')->prefix('fo/staff/{staff}')->group(function () {

    Route::get('/assigned', [FOController::class, 'staffAssignedDocs'])
        ->name('fo.staff.assigned');

    Route::get('/in-progress', [FOController::class, 'staffInProgressDocs'])
        ->name('fo.staff.inProgress');

    Route::get('/completed', [FOController::class, 'staffCompletedDocs'])
        ->name('fo.staff.completed');

    Route::get('/hold', [FOController::class, 'staffHoldDocs'])
        ->name('fo.staff.hold');

    Route::get('/payment/full', [FOController::class, 'staffFullPaymentDocs'])
        ->name('fo.staff.fullPayment');

    Route::get('/payment/advance', [FOController::class, 'staffAdvancePaymentDocs'])
        ->name('fo.staff.advancePayment');

});


// ── Cross-login — one-time token issued by VMRFDU-VSuite ─────────────────
Route::get('/cross-login/{token}', [CrossLoginController::class, 'login'])->name('cross.login');

require __DIR__.'/auth.php';
