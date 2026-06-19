<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role !== 'Staff') {
            
            $notification = array(
                'message' => 'Permission Denied',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification); // Redirect to home if not VC
        }

        return $next($request);
    }
}
