<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::with('user')->latest()->get();

        $stats = [
            'total'   => $tokens->count(),
            'active'  => $tokens->filter(fn ($t) => !$t->isExpired())->count(),
            'expiring'=> $tokens->filter(fn ($t) => !$t->isExpired() && $t->expires_at && $t->expires_at->diffInDays(now()) <= 7)->count(),
            'expired' => $tokens->filter(fn ($t) => $t->isExpired())->count(),
        ];

        $users = User::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'department', 'role']);

        $newToken = session('new_api_token');

        return view('frontend.api.index', compact('tokens', 'stats', 'users', 'newToken'))
            ->with('activeMenu', 'api')
            ->with('activeDropdown', 'api_tokens');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'user_id'     => 'required|exists:users,id',
            'expires_days'=> 'required|integer|min:1|max:365',
        ]);

        $plain = Str::random(48);

        ApiToken::create([
            'user_id'    => $request->user_id,
            'name'       => $request->name,
            'token'      => hash('sha256', $plain),
            'expires_at' => now()->addDays((int) $request->expires_days),
        ]);

        return redirect()->route('api.tokens.index')
            ->with('new_api_token', $plain)
            ->with('success', 'API token created. Copy it now — it will not be shown again.');
    }

    public function destroy(int $id)
    {
        ApiToken::findOrFail($id)->delete();
        return redirect()->route('api.tokens.index')->with('success', 'Token revoked successfully.');
    }
}
