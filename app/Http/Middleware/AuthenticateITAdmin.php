<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateITAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['ITAdmin', 'SuperAdmin'])) {
            $notification = ['message' => 'Permission Denied', 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        return $next($request);
    }
}
