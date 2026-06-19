<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    /**
     * POST /api/v1/auth/login
     * Authenticate and get a bearer token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Your account is inactive.'], 403);
        }

        // Generate a plain token — we store its hash
        $plainToken = Str::random(40);

        $apiToken = ApiToken::create([
            'user_id'    => $user->id,
            'name'       => $request->input('token_name', 'API Access'),
            'token'      => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'token'      => $plainToken,
                'token_type' => 'Bearer',
                'expires_at' => $apiToken->expires_at->toISOString(),
                'user'       => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'department' => $user->department,
                    'role'       => $user->role,
                    'designation'=> $user->designation,
                ],
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     * Revoke the current token.
     */
    public function logout(Request $request)
    {
        $token = $request->get('_api_token');
        if ($token) {
            $token->delete();
        }

        return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
    }

    /**
     * GET /api/v1/auth/me
     * Get the authenticated user's profile.
     */
    public function me(Request $request)
    {
        $user = $request->get('_api_user');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'emp_id'     => $user->emp_id,
                'department' => $user->department,
                'division'   => $user->division,
                'role'       => $user->role,
                'designation'=> $user->designation,
                'phone'      => $user->phone,
            ],
        ]);
    }
}
