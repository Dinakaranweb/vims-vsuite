<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['success' => false, 'message' => 'API token missing. Pass Authorization: Bearer <token> header.'], 401);
        }

        $apiToken = ApiToken::where('token', hash('sha256', $bearerToken))->with('user')->first();

        if (!$apiToken) {
            return response()->json(['success' => false, 'message' => 'Invalid API token.'], 401);
        }

        if ($apiToken->isExpired()) {
            return response()->json(['success' => false, 'message' => 'API token has expired.'], 401);
        }

        if (!$apiToken->user || !$apiToken->user->is_active) {
            return response()->json(['success' => false, 'message' => 'User account is inactive.'], 403);
        }

        $apiToken->update(['last_used_at' => now()]);

        $request->merge(['_api_user' => $apiToken->user, '_api_token' => $apiToken]);
        auth()->setUser($apiToken->user);

        return $next($request);
    }
}
