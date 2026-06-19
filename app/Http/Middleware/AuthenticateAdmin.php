<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !in_array(auth()->user()->role, ['SuperAdmin', 'ITAdmin'])) {
            //return redirect()->route('login'); // Redirect to home if not VC

            $notification = array(
                'message' => 'Permission Denied',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }

        return $next($request);
    }
}
