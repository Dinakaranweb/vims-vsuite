<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:512',
            'platform'  => 'sometimes|in:android,ios',
        ]);

        $user = $request->get('_api_user');

        DeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'fcm_token' => $request->fcm_token],
            ['platform' => $request->input('platform', 'android')],
        );

        return response()->json(['success' => true, 'message' => 'Device token registered']);
    }

    public function destroy(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $user = $request->get('_api_user');
        DeviceToken::where('user_id', $user->id)
                   ->where('fcm_token', $request->fcm_token)
                   ->delete();

        return response()->json(['success' => true, 'message' => 'Device token removed']);
    }
}
