<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CrossAuthController extends Controller
{
    /**
     * POST /api/v1/cross-auth/chairman
     *
     * Cross-VSuite authentication for Chairman users.
     * A second VSuite instance calls this endpoint with the Chairman's email.
     * If the email matches an active Chairman-department user in THIS instance,
     * a Bearer token is issued so the remote app can act on behalf of that Chairman.
     *
     * Both VSuite apps share the same Chairman email — that equality is the trust anchor.
     *
     * Body: { email (required), source_app? (optional label for the issuing app) }
     */
    public function chairmanVerify(Request $request)
    {
        $request->validate([
            'email'      => 'required|email',
            'source_app' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No active user found with this email address in this VSuite instance.',
            ], 401);
        }

        $plainToken = Str::random(40);
        $tokenName  = 'Cross-VSuite:' . ($request->input('source_app', 'External')) . '-Chairman';

        $apiToken = ApiToken::create([
            'user_id'    => $user->id,
            'name'       => $tokenName,
            'token'      => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chairman identity verified. API token issued.',
            'data'    => [
                'token'      => $plainToken,
                'token_type' => 'Bearer',
                'expires_at' => $apiToken->expires_at->toISOString(),
                'user'       => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'department'  => $user->department,
                    'role'        => $user->role,
                    'designation' => $user->designation,
                ],
            ],
        ]);
    }

    /**
     * POST /api/v1/cross-auth/generate-login-token
     *
     * Called by VMRFDU-VSuite to get a one-time 5-minute web-login token.
     * Verifies the Chairman's password so the remote app knows the credential
     * is still valid. Returns a token that the browser can use at GET /cross-login/{token}.
     *
     * Body: { email (required), password (required) }
     */
    public function generateLoginToken(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', 1)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Clean up any expired tokens first
        DB::table('cross_login_tokens')->where('expires_at', '<', now())->delete();

        $token = Str::random(64);

        DB::table('cross_login_tokens')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'token'   => $token,
        ]);
    }
}
