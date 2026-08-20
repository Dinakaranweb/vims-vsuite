<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Notification;

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

        require_once app_path('Helpers/indianCurrency.php');
    }
}
