<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateITAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'ITAdmin') {
            $notification = ['message' => 'Permission Denied', 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        return $next($request);
    }
}
