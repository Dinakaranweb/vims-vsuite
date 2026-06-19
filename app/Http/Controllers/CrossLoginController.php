<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrossLoginController extends Controller
{
    /**
     * GET /cross-login/{token}
     *
     * One-time auto-login for a Chairman redirected from VMRFDU-VSuite.
     * The token was issued by POST /api/v1/cross-auth/generate-login-token
     * and is valid for 5 minutes, single-use.
     */
    public function login(string $token)
    {
        $record = DB::table('cross_login_tokens')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect('/')
                ->with('error', 'This login link has expired or is invalid. Please try again from VMRFDU-VSuite.');
        }

        // One-time use — delete immediately before logging in
        DB::table('cross_login_tokens')->where('token', $token)->delete();

        $user = User::where('email', $record->email)
            ->where('is_active', 1)
            ->first();

        if (!$user) {
            return redirect('/')
                ->with('error', 'User account not found or is inactive.');
        }

        Auth::login($user, false);
        request()->session()->regenerate();

        return redirect('/chairman/dashboard');
    }
}
