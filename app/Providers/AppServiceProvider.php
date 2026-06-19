<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Notification;
use App\Models\Ticket;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('frontend.admin.body.header', function ($view) {
            $notifications = Notification::where('to', auth()->id())
                ->orWhere('to', Auth::user()->department)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $unread = Notification::where(function($query) {
                $query->where('to', auth()->id())
                      ->orWhere('to', Auth::user()->department);
                })
                ->where('is_read', false)
                ->count();

            //dd($unread);

            $view->with('notifications', $notifications)
                 ->with('unread', $unread);
        });

        View::composer('frontend.superadmin.body.header', function ($view) {
            $notifications = Notification::where('to', auth()->id())
                ->orWhere('to', Auth::user()->department)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $unread = Notification::where(function($query) {
                $query->where('to', auth()->id())
                      ->orWhere('to', Auth::user()->department);
                })
                ->where('is_read', false)
                ->count();

            //dd($unread);

            $view->with('notifications', $notifications)
                 ->with('unread', $unread);
        });

        View::composer('frontend.staff.body.header', function ($view) {
            $notifications = Notification::where('to', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $unread = Notification::where(function($query) {
                $query->where('to', auth()->id());
                })
                ->where('is_read', false)
                ->count();

            //dd($unread);

            $view->with('notifications', $notifications)
                 ->with('unread', $unread);
        });

        View::composer('frontend.admin.body.sidebar', function ($view) {
            
            $unassigned_tickets_count = Ticket::where('is_approved', True)
                                                ->where('assigned_to', Null)
                                                ->Where('ticket_to', Auth::user()->department)
                                                ->count();

            $my_tickets_count = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->Where('status', '!=', 'Closed')
                                        ->count();

            $open_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('status', 'Open')
                                        ->count();

            $in_progress_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('status', 'In Progress')
                                        ->count();

            $hold_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('status', 'Hold')
                                        ->count();

            $completed_tickets_count = Ticket::where('is_approved', True)
                                            ->Where('ticket_to', Auth::user()->department)
                                            ->where('status', 'Completed')
                                            ->count();

            $unapproved_tickets_count = Ticket::Where('ticket_from', Auth::user()->department)
                                                ->where('is_approved', False)
                                                ->count();

            $dept_staff_tickets_count = Ticket::where('is_approved', True)
                                                ->Where('ticket_from', Auth::user()->department)
                                                ->where('status', '!=', 'Closed')
                                                ->count();

            $self_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('assigned_to', Auth::id())
                                        ->where('status', '!=', 'Closed')
                                        ->count();

            $forwardedToItCount = Ticket::join('ticket_forwardings', 'tickets.id', '=', 'ticket_forwardings.ticket_id')
                                        ->where('ticket_forwardings.forwarded_to', Auth::user()->department)
                                        ->where('tickets.status', '!=', 'Closed')
                                        ->count();


            $view->with([
                        'unassigned_tickets_count' => $unassigned_tickets_count,
                        'my_tickets_count' => $my_tickets_count,
                        'open_tickets_count' => $open_tickets_count,
                        'in_progress_tickets_count' => $in_progress_tickets_count,
                        'hold_tickets_count' => $hold_tickets_count,
                        'completed_tickets_count' => $completed_tickets_count,
                        'unapproved_tickets_count' => $unapproved_tickets_count,
                        'dept_staff_tickets_count' => $dept_staff_tickets_count,
                        'self_tickets_count' => $self_tickets_count,
                        'forwarded_tickets_count' => $forwardedToItCount
                    ]);
        });

        View::composer('frontend.staff.body.sidebar', function ($view) {
            
            $my_tickets_count = Ticket::where('is_approved', True)
                                        ->where('ticket_by', Auth::id())
                                        ->Where('status', '!=', 'Closed')
                                        ->count();

            $open_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('assigned_to', Auth::id())
                                        ->where('status', 'Open')
                                        ->count();

            $in_progress_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('assigned_to', Auth::id())
                                        ->where('status', 'In Progress')
                                        ->count();

            $hold_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('assigned_to', Auth::id())
                                        ->where('status', 'Hold')
                                        ->count();

            $completed_tickets_count = Ticket::where('is_approved', True)
                                            ->Where('assigned_to', Auth::id())
                                            ->where('status', 'Completed')
                                            ->count();

            $unapproved_tickets_count = Ticket::Where('ticket_by', Auth::id())
                                                ->where('is_approved', False)
                                                ->count();

            $forwardedToItCount = Ticket::join('ticket_forwardings', 'tickets.id', '=', 'ticket_forwardings.ticket_id')
                                        ->where('ticket_forwardings.assigned_to', Auth::id())
                                        ->where('tickets.status', '!=', 'Closed')
                                        ->count();


            $view->with([
                        'my_tickets_count' => $my_tickets_count,
                        'open_tickets_count' => $open_tickets_count,
                        'in_progress_tickets_count' => $in_progress_tickets_count,
                        'hold_tickets_count' => $hold_tickets_count,
                        'completed_tickets_count' => $completed_tickets_count,
                        'unapproved_tickets_count' => $unapproved_tickets_count,
                        'forwarded_tickets_count' => $forwardedToItCount
                    ]);
        });

        View::composer('frontend.superadmin.body.sidebar', function ($view) {
            
            $unassigned_tickets_count = Ticket::Where('is_approved', True)
                                                ->where('assigned_to', Null)
                                                ->count();

            $unassigned_received_tickets_count = Ticket::Where('is_approved', True)
                                                        ->Where('ticket_to', Auth::user()->department)
                                                        ->where('assigned_to', Null)
                                                        ->count();

            $open_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('status', 'Open')
                                        ->count();

            $in_progress_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('status', 'In Progress')
                                        ->count();

            $hold_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('status', 'Hold')
                                        ->count();

            $completed_tickets_count = Ticket::where('is_approved', True)
                                            ->Where('ticket_to', Auth::user()->department)
                                            ->where('status', 'Completed')
                                            ->count();

            $unapproved_tickets_count = Ticket::Where('ticket_from', Auth::user()->department)
                                                ->where('is_approved', False)
                                                ->count();

            $dept_staff_tickets_count = Ticket::where('is_approved', True)
                                                ->Where('ticket_from', Auth::user()->department)
                                                ->where('status', '!=', 'Closed')
                                                ->count();

            $self_tickets_count = Ticket::where('is_approved', True)
                                        ->Where('ticket_to', Auth::user()->department)
                                        ->where('assigned_to', Auth::id())
                                        ->where('status', '!=', 'Closed')
                                        ->count();

            $my_tickets_count = Ticket::where('ticket_by', Auth::id())
                                        ->Where('status', '!=', 'Closed')
                                        ->count();

            $forwardedToItCount = Ticket::join('ticket_forwardings', 'tickets.id', '=', 'ticket_forwardings.ticket_id')
                                        ->where('ticket_forwardings.forwarded_to', Auth::user()->department)
                                        ->where('tickets.status', '!=', 'Closed')
                                        ->count();

            $view->with([
                        'unassigned_tickets_count' => $unassigned_tickets_count,
                        'unassigned_received_tickets_count' => $unassigned_received_tickets_count,
                        'my_tickets_count' => $my_tickets_count,
                        'open_tickets_count' => $open_tickets_count,
                        'in_progress_tickets_count' => $in_progress_tickets_count,
                        'hold_tickets_count' => $hold_tickets_count,
                        'completed_tickets_count' => $completed_tickets_count,
                        'unapproved_tickets_count' => $unapproved_tickets_count,
                        'dept_staff_tickets_count' => $dept_staff_tickets_count,
                        'self_tickets_count' => $self_tickets_count,
                        'forwarded_tickets_count' => $forwardedToItCount
                    ]);
        });
        
        require_once app_path('Helpers/indianCurrency.php');
    }
}
